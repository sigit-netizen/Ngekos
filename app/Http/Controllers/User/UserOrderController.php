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
                'no_rekening' => $kos->no_rekening,
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
            'jumlah_bayar' => 'required|numeric|min:0',
            'metode_pembayaran' => 'required|string|in:manual,pymen',
            'batas_bayar' => 'nullable|date_format:Y-m-d\TH:i',
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

        // Get kamar details
        $kamar = Kamar::findOrFail($request->id_kamar);

        // Check if kamar is still available
        if ($kamar->status !== 'tersedia') {
            return back()->with('error', 'Maaf, kamar ini sudah tidak tersedia.');
        }

        // Expiry logic: "jika pyment batas waktunya 3 hari"
        $batasBayar = $request->batas_bayar ? \Carbon\Carbon::parse($request->batas_bayar) : null;
        if ($request->metode_pembayaran === 'pymen') {
            $batasBayar = now()->addDays(3);
        }

        Transaksi::create([
            'jumlah_bayar' => $request->jumlah_bayar,
            'tanggal_pembayaran' => null,
            'status' => 'pending',
            'id_user' => $user->id,
            'id_kamar' => $kamar->id,
            'kode_kos' => $request->kode_kos,
            'catatan' => $request->catatan,
            'metode_pembayaran' => $request->metode_pembayaran,
            'batas_bayar' => $batasBayar,
            'bukti_pembayaran' => null,
        ]);

        return redirect()->route('user.order')->with('success', 'Order berhasil dikirim! Menunggu verifikasi admin.');
    }

    /**
     * Cancel a pending order.
     */
    public function cancelOrder(Transaksi $transaksi)
    {
        if ($transaksi->id_user !== auth()->id()) {
            abort(403);
        }

        if ($transaksi->status !== 'pending') {
            return back()->with('error', 'Hanya order yang berstatus pending yang dapat dibatalkan.');
        }

        $transaksi->delete();

        return back()->with('success', 'Order berhasil dibatalkan.');
    }

    public function uploadProof(Request $request, Transaksi $transaksi)
    {
        if ($transaksi->id_user !== auth()->id()) {
            abort(403);
        }

        if ($transaksi->status !== 'verified') {
            return back()->with('error', 'Silakan unggah bukti setelah pesanan disetujui.');
        }

        // Enforce 24h deadline
        if ($transaksi->batas_bayar && now()->gt($transaksi->batas_bayar)) {
            $transaksi->update(['status' => 'failed']);
            return back()->with('error', 'Waktu maksimal unggah bukti (1x24 jam) telah habis. Pesanan otomatis gagal.');
        }

        $request->validate([
            'bukti_pembayaran' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        if ($request->hasFile('bukti_pembayaran')) {
            $file = $request->file('bukti_pembayaran');
            $filename = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('images/bukti_pembayaran'), $filename);
            $buktiPath = '/images/bukti_pembayaran/' . $filename;

            $transaksi->update([
                'bukti_pembayaran' => $buktiPath,
                'tanggal_pembayaran' => now(),
            ]);

            return back()->with('success', 'Bukti pembayaran berhasil diunggah! Menunggu pengecekan admin.');
        }

        return back()->with('error', 'Gagal mengunggah bukti pembayaran.');
    }
}
