<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Kos;
use App\Models\Kamar;
use App\Models\Transaksi;
use Illuminate\Http\Request;

class UserOrderController extends Controller
{
    /**
     * Display user order page with search and order history.
     */
    public function index(Request $request)
    {
        $user = auth()->user();

        // Get user's order history
        $orders = Transaksi::where('id_user', $user->id)
            ->with(['kamar.kos'])
            ->latest()
            ->paginate(10);

        // Count stats
        $pendingCount = Transaksi::where('id_user', $user->id)->where('status', 'pending')->count();
        $verifiedCount = Transaksi::where('id_user', $user->id)->where('status', 'verified')->count();
        $rejectedCount = Transaksi::where('id_user', $user->id)->where('status', 'rejected')->count();

        return view('user.order', [
            'title' => 'Order Kamar',
            'role' => 'user',
            'orders' => $orders,
            'pendingCount' => $pendingCount,
            'verifiedCount' => $verifiedCount,
            'rejectedCount' => $rejectedCount,
        ]);
    }

    /**
     * Search kos with filters: lokasi, harga (predefined range), kategori.
     */
    public function searchKos(Request $request)
    {
        // Filter variables
        $kategori = $request->kategori;
        $lokasi = $request->lokasi;
        $hargaMin = null;
        $hargaMax = null;

        if ($request->filled('harga')) {
            $parts = explode('-', $request->harga);
            if (count($parts) === 2) {
                $hargaMin = (int) $parts[0];
                $hargaMax = (int) $parts[1];
            }
        }

        $query = Kos::query();

        // 1. Filter by lokasi
        if ($lokasi) {
            $query->where(function ($q) use ($lokasi) {
                $q->where('alamat', 'like', '%' . $lokasi . '%')
                    ->orWhere('nama_kos', 'like', '%' . $lokasi . '%');
            });
        }

        // 2. Filter by kategori
        if ($kategori) {
            $query->where('kategori', $kategori);
        }

        // 3. Filter by availability and price (using whereHas)
        $query->whereHas('kamars', function ($q) use ($hargaMin, $hargaMax) {
            $q->where('status', 'tersedia');
            if (!is_null($hargaMin)) {
                $q->where('harga', '>=', $hargaMin);
            }
            if (!is_null($hargaMax)) {
                $q->where('harga', '<=', $hargaMax);
            }
        });

        // 4. Eager load only the matching rooms
        $query->with([
            'kamars' => function ($q) use ($hargaMin, $hargaMax) {
                $q->where('status', 'tersedia');
                if (!is_null($hargaMin)) {
                    $q->where('harga', '>=', $hargaMin);
                }
                if (!is_null($hargaMax)) {
                    $q->where('harga', '<=', $hargaMax);
                }
                $q->with('fasilitas');
            }
        ]);

        $kosList = $query->get();

        if ($kosList->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'Tidak ada kos yang cocok dengan kriteria pencarian.',
            ]);
        }

        $results = $kosList->map(function ($kos) {
            return [
                'id' => $kos->id,
                'kode_kos' => $kos->kode_kos,
                'nama_kos' => $kos->nama_kos,
                'alamat' => $kos->alamat,
                'kategori' => $kos->kategori,
                'foto' => $kos->foto,
                'kamars' => $kos->kamars->values()->map(function ($kamar) {
                    return [
                        'id' => $kamar->id,
                        'nomor_kamar' => $kamar->nomor_kamar,
                        'harga' => $kamar->harga,
                        'foto' => $kamar->foto,
                        'fasilitas' => $kamar->fasilitas->pluck('nama_fasilitas')->toArray(),
                    ];
                }),
                'kamar_count' => $kos->kamars->count(),
            ];
        });

        return response()->json([
            'success' => true,
            'data' => $results->values(),
        ]);
    }

    /**
     * Store a new order (transaksi) from user.
     */
    public function store(Request $request)
    {
        $request->validate([
            'id_kamar' => 'required|exists:kamar,id',
            'kode_kos' => 'required|numeric',
            'catatan' => 'nullable|string|max:500',
        ]);

        $user = auth()->user();

        // Check if user already has an active order for this kamar
        $existingOrder = Transaksi::where('id_user', $user->id)
            ->where('id_kamar', $request->id_kamar)
            ->where('status', 'pending')
            ->first();

        if ($existingOrder) {
            return back()->with('error', 'Anda sudah memiliki order yang menunggu verifikasi untuk kamar ini.');
        }

        // Check if user is already a tenant
        if ($user->isPenyewa()) {
            return back()->with('error', 'Anda sudah menjadi penyewa. Tidak bisa membuat order baru.');
        }

        // Get kamar details for price
        $kamar = Kamar::findOrFail($request->id_kamar);

        // Check if kamar is still available
        if ($kamar->status !== 'tersedia') {
            return back()->with('error', 'Maaf, kamar ini sudah tidak tersedia.');
        }

        Transaksi::create([
            'jumlah_bayar' => $kamar->harga,
            'tanggal_pembayaran' => null,
            'status' => 'pending',
            'id_user' => $user->id,
            'id_kamar' => $kamar->id,
            'kode_kos' => $request->kode_kos,
            'catatan' => $request->catatan,
        ]);

        return redirect()->route('user.order')->with('success', 'Order berhasil dikirim! Menunggu verifikasi admin.');
    }

    /**
     * Cancel a pending order.
     */
    public function cancelOrder(Transaksi $transaksi)
    {
        $user = auth()->user();

        // Ensure the order belongs to the user and is still pending
        if ($transaksi->id_user !== $user->id) {
            return back()->with('error', 'Anda tidak memiliki akses untuk membatalkan pesanan ini.');
        }

        if ($transaksi->status !== 'pending') {
            return back()->with('error', 'Hanya pesanan dengan status pending yang dapat dibatalkan.');
        }

        $transaksi->delete();

        return back()->with('success', 'Pesanan kamar berhasil dibatalkan.');
    }
}
