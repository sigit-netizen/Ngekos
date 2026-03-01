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
        $query = Kos::query()->with([
            'kamars' => function ($q) {
                $q->where('status', 'tersedia')->with('fasilitas');
            }
        ]);

        // Filter by lokasi (partial match on alamat)
        if ($request->filled('lokasi')) {
            $query->where('alamat', 'like', '%' . $request->lokasi . '%');
        }

        // Filter by kategori (putra/putri/campur)
        if ($request->filled('kategori')) {
            $query->where('kategori', $request->kategori);
        }

        // Filter by harga range (predefined: "500000-1000000")
        $hargaMin = null;
        $hargaMax = null;
        if ($request->filled('harga')) {
            $parts = explode('-', $request->harga);
            if (count($parts) === 2) {
                $hargaMin = (int) $parts[0];
                $hargaMax = (int) $parts[1];
            }
        }

        if ($hargaMin || $hargaMax) {
            $query->whereHas('kamars', function ($q) use ($hargaMin, $hargaMax) {
                $q->where('status', 'tersedia');
                if ($hargaMin) {
                    $q->where('harga', '>=', $hargaMin);
                }
                if ($hargaMax) {
                    $q->where('harga', '<=', $hargaMax);
                }
            });
        }

        $kosList = $query->get();

        if ($kosList->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'Tidak ada kos yang cocok dengan filter pencarian.',
            ]);
        }

        $results = $kosList->map(function ($kos) use ($hargaMin, $hargaMax) {
            $kamars = $kos->kamars;

            // Apply price filter on already-loaded kamars
            if ($hargaMin) {
                $kamars = $kamars->where('harga', '>=', $hargaMin);
            }
            if ($hargaMax) {
                $kamars = $kamars->where('harga', '<=', $hargaMax);
            }

            return [
                'id' => $kos->id,
                'kode_kos' => $kos->kode_kos,
                'nama_kos' => $kos->nama_kos,
                'alamat' => $kos->alamat,
                'kategori' => $kos->kategori,
                'kamars' => $kamars->values()->map(function ($kamar) {
                    return [
                        'id' => $kamar->id,
                        'nomor_kamar' => $kamar->nomor_kamar,
                        'harga' => $kamar->harga,
                        'fasilitas' => $kamar->fasilitas->pluck('nama')->toArray(),
                    ];
                }),
                'kamar_count' => $kamars->count(),
            ];
        })->filter(fn($kos) => $kos['kamar_count'] > 0 || !$hargaMin && !$hargaMax);

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
}
