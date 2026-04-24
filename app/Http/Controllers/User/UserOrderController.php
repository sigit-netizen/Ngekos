<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Kos;
use App\Models\Kamar;
use App\Models\Transaksi;
use App\Models\User;
use Illuminate\Http\Request;

class UserOrderController extends Controller
{
    /**
     * Tampilkan halaman pesanan pengguna dengan pencarian dan riwayat pesanan.
     */
    public function index(Request $request)
    {
        $user = auth()->user();

        // Batalkan otomatis pesanan terverifikasi yang telah kedaluwarsa (expired verified orders)
        Transaksi::checkExpiry();

        // Dapatkan riwayat pesanan pengguna
        $orders = Transaksi::where('id_user', $user->id)
            ->with(['kamar.kos.user.nomorBank'])
            ->latest()
            ->paginate(10);

        // Hitung statistik (Count stats)
        $pendingCount = Transaksi::where('id_user', $user->id)->where('status', 'pending')->count();
        $verifiedCount = Transaksi::where('id_user', $user->id)->where('status', 'verified')->count();
        $rejectedCount = Transaksi::where('id_user', $user->id)->where('status', 'rejected')->count();

        $tab = $request->input('tab', 'all');

        // Ambil kode_kos dari query string jika user datang dari landing page
        $intendedKos = $request->input('kode_kos');

        return view('user.order', [
            'title' => 'Order Kamar',
            'role' => 'user',
            'orders' => $orders,
            'tab' => $tab,
            'pendingCount' => $pendingCount,
            'verifiedCount' => $verifiedCount,
            'rejectedCount' => $rejectedCount,
            'intendedKos' => $intendedKos,
        ]);
    }

    /**
     * Cari kos dengan filter: lokasi, harga (rentang yang telah ditentukan), kategori.
     */
    public function searchKos(Request $request)
    {
        // Variabel filter
        $kategori = $request->kategori;
        $lokasi = $request->lokasi;
        $kodeKos = $request->kode_kos; // Filter langsung by kode_kos
        $hargaMin = null;
        $hargaMax = null;

        if ($request->filled('harga')) {
            $parts = explode('-', $request->harga);
            if (count($parts) === 2) {
                $hargaMin = (int) $parts[0];
                $hargaMax = (int) $parts[1];
            }
        }

        $tipeSewa = $request->tipe_sewa;

        $searchPerformed = $request->filled('lokasi') || $request->filled('harga') || $request->filled('kategori') || $request->filled('kota') || $request->filled('tipe_sewa') || $request->filled('kode_kos');

        $query = Kos::query();

        // 0. Filter langsung by kode_kos (dari landing page redirect)
        if ($kodeKos) {
            $query->where('kode_kos', $kodeKos);
        }

        // 1. Filter berdasarkan lokasi / kota
        if ($lokasi) {
            $query->where(function ($q) use ($lokasi) {
                $q->where('alamat', 'like', '%' . $lokasi . '%')
                    ->orWhere('nama_kos', 'like', '%' . $lokasi . '%')
                    ->orWhere('kota', 'like', '%' . $lokasi . '%')
                    ->orWhere('nama_kota', 'like', '%' . $lokasi . '%');
            });
        }

        if ($request->filled('kota')) {
            $query->where('kota', $request->kota);
        }

        // 2. Filter berdasarkan kategori
        if ($kategori) {
            $query->where('kategori', $kategori);
        }

        // 3. Filter berdasarkan tipe_sewa (tipe_durasi di kamar)
        if ($tipeSewa) {
            $query->whereHas('kamars', function ($q) use ($tipeSewa) {
                $q->where('tipe_durasi', $tipeSewa);
            });
        }

        // Jika harga ditentukan, periksa apakah kos SETIDAKNYA memiliki kamar yang sesuai dengan harga tersebut. 
        // Jika mereka hanya mencari tanpa menentukan harga, tampilkan semua kos yang cocok.
        if (!is_null($hargaMin) || !is_null($hargaMax)) {
            $query->whereHas('kamars', function ($q) use ($hargaMin, $hargaMax) {
                if (!is_null($hargaMin)) {
                    $q->where('harga', '>=', $hargaMin);
                }
                if (!is_null($hargaMax)) {
                    $q->where('harga', '<=', $hargaMax);
                }
            });
        }

        // 4. Memuat langsung (Eager load): Hanya muat kamar tersedia yang cocok
        $query->with([
            'kamars' => function ($q) use ($hargaMin, $hargaMax, $tipeSewa) {
                $q->where('status', 'tersedia')
                    ->whereDoesntHave('transaksis', function ($sub) {
                        $sub->whereIn('status', ['pending', 'verified']);
                    });
                if ($tipeSewa) {
                    $q->where('tipe_durasi', $tipeSewa);
                }
                if (!is_null($hargaMin)) {
                    $q->where('harga', '>=', $hargaMin);
                }
                if (!is_null($hargaMax)) {
                    $q->where('harga', '<=', $hargaMax);
                }
                $q->with('fasilitas');
            },
            'favoritedBy' => function ($q) {
                $q->where('users.id', auth()->id());
            },
            'user.nomorBank'
        ])
            ->withMin('kamars as harga_termurah', 'harga')
            ->withMax('kamars as harga_termahal', 'harga');

        // 5. Urutkan berdasarkan Popularitas jika ini adalah panggilan rekomendasi (tanpa filter)
        $kosList = $query->get();

        if (!$searchPerformed) {
            // Hitung transaksi sukses per kota
            $cityPopularity = \App\Models\Transaksi::join('kamar', 'transaksi.id_kamar', '=', 'kamar.id')
                ->join('kos', 'kamar.id_kos', '=', 'kos.id')
                ->whereIn('transaksi.status', ['verified', 'paid'])
                ->selectRaw('kos.kota as city_name, count(transaksi.id) as tx_count')
                ->groupBy('kos.kota')
                ->pluck('tx_count', 'city_name')
                ->toArray();

            $kosList = $kosList->each(function ($kos) use ($cityPopularity) {
                $city = $kos->kota ?: $kos->nama_kota;
                $kos->popularity_score = $cityPopularity[$city] ?? 0;
            })->sortByDesc('popularity_score');
        }

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
                'kota' => $kos->kota ?: $kos->nama_kota,
                'no_rekening' => $kos->no_rekening,
                'nomor_bank' => $kos->user->nomorBank ? [
                    'nama_bank' => $kos->user->nomorBank->nama_bank,
                    'nomor_rekening' => $kos->user->nomorBank->nomor_rekening,
                    'nama_pemilik' => $kos->user->nomorBank->nama_pemilik,
                    'nama_bank_2' => $kos->user->nomorBank->nama_bank_2,
                    'nomor_rekening_2' => $kos->user->nomorBank->nomor_rekening_2,
                    'nama_pemilik_2' => $kos->user->nomorBank->nama_pemilik_2,
                ] : null,
                'kategori' => $kos->kategori,
                'foto' => $kos->foto,
                'harga_termurah' => $kos->harga_termurah, // Dari withMin
                'harga_termahal' => $kos->harga_termahal, // Dari withMax
                'owner' => [
                    'instagram' => $kos->user->instagram,
                    'twitter' => $kos->user->twitter,
                    'youtube' => $kos->user->youtube,
                    'tiktok' => $kos->user->tiktok,
                ],
                'kamars' => $kos->kamars->values()->map(function ($kamar) {
                    return [
                        'id' => $kamar->id,
                        'nomor_kamar' => $kamar->nomor_kamar,
                        'harga' => $kamar->harga,
                        'tipe_durasi' => $kamar->tipe_durasi,
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
     * Simpan pesanan (transaksi) baru dari pengguna.
     */
    public function store(Request $request)
    {
        $request->validate([
            'id_kamar' => 'required|exists:kamar,id',
            'kode_kos' => 'required|numeric',
            'jumlah_bayar' => 'required|numeric|min:0',
            'metode_pembayaran' => 'required|in:manual,pymen',
            'batas_bayar' => 'required_if:metode_pembayaran,manual|nullable|date_format:Y-m-d\TH:i',
            'catatan' => 'nullable|string|max:500',
        ]);

        $user = auth()->user();
        $isPenyewa = $user->isPenyewa();

        // Periksa apakah pengguna sudah memiliki pesanan aktif untuk kamar ini
        $existingOrder = Transaksi::where('id_user', $user->id)
            ->where('id_kamar', $request->id_kamar)
            ->where('status', 'pending')
            ->first();

        if ($existingOrder) {
            return back()->with('error', 'Anda sudah memiliki order yang menunggu verifikasi untuk kamar ini.');
        }

        // Tentukan apakah ini pembayaran sewa atau pesanan baru (booking)
        $isRentPayment = $isPenyewa && $user->id_kamar == $request->id_kamar;
        $tipe = $isRentPayment ? Transaksi::TYPE_SEWA : Transaksi::TYPE_BOOKING;

        // Dapatkan detail kamar
        $kamar = Kamar::findOrFail($request->id_kamar);

        // PASTIKAN pembayaran tetap: jumlah_bayar harus tepat sama dengan kamar->harga
        if ($request->jumlah_bayar != $kamar->harga) {
            return back()->with('error', 'Nominal pembayaran tidak sesuai dengan harga kamar. Silakan coba lagi.');
        }

        if (!$isRentPayment) {
            // BARU: Periksa apakah kamar dikunci oleh orang lain (status pending, verified, atau dibayar dalam 24 jam)
            $lockedOrder = Transaksi::where('id_kamar', $request->id_kamar)
                ->where(function ($q) {
                    $q->whereIn('status', ['pending', 'verified'])
                        ->orWhere(function ($sq) {
                            $sq->where('status', 'paid')
                                ->where('created_at', '>=', now()->subDay());
                        });
                })
                ->first();

            if ($lockedOrder) {
                return back()->with('error', 'Maaf, kamar ini baru saja dibooking oleh orang lain. Silakan pilih kamar lain.');
            }

            // Periksa apakah pengguna sudah menjadi penyewa (hanya untuk pesanan baru)
            if ($isPenyewa) {
                return back()->with('error', 'Anda sudah menjadi penyewa. Tidak bisa membuat order baru.');
            }

            // Periksa apakah kamar masih tersedia (hanya untuk pesanan baru)
            if ($kamar->status !== 'tersedia') {
                return back()->with('error', 'Maaf, kamar ini sudah tidak tersedia.');
            }
        }

        // Logika Kedaluwarsa: "jika pymen batas waktunya 3 hari"
        $batasBayar = $request->batas_bayar ? \Carbon\Carbon::parse($request->batas_bayar) : null;
        if ($request->metode_pembayaran === 'pymen') {
            $batasBayar = now()->addDays(3);
        }

        $duration = $kamar->durasi_sewa ?? 1;
        $type = $kamar->tipe_durasi ?? 'bulan';
        $initialJatuhTempo = now('Asia/Jakarta');

        if ($type === 'hari') {
            $initialJatuhTempo->addDays($duration);
        } elseif ($type === 'minggu') {
            $initialJatuhTempo->addWeeks($duration);
        } else {
            $initialJatuhTempo->addDays($duration * 30);
        }

        $transaksi = Transaksi::create([
            'jumlah_bayar' => $request->jumlah_bayar,
            'tanggal_pembayaran' => $request->metode_pembayaran === 'manual' ? $request->batas_bayar : null,
            'status' => $tipe === Transaksi::TYPE_SEWA ? 'verified' : 'pending',
            'tipe' => $tipe,
            'durasi_sewa' => $duration,
            'tipe_durasi' => $type,
            'id_user' => $user->id,
            'id_kamar' => $request->id_kamar,
            'kode_kos' => $kamar->kos->kode_kos,
            'catatan' => $request->catatan,
            'metode_pembayaran' => $request->metode_pembayaran,
            'batas_bayar' => $batasBayar,
            'bukti_pembayaran' => null,
            'jatuh_tempo' => $initialJatuhTempo,
        ]);

        // Beri tahu Pemilik Kos (Antrean/Queued untuk kecepatan)
        $owner = $kamar->kos->user;
        if ($owner && $owner->nomor_wa) {
            $owner->notify(new \App\Notifications\OrderMasukNotification([
                'nama' => $user->name,
                'kos' => $kamar->kos->nama_kos,
                'kamar' => $kamar->nomor_kamar,
                'jumlah' => $request->jumlah_bayar,
                'tipe' => $tipe,
                'is_superadmin' => false
            ]));
        }

        $message = $isRentPayment ? 'Pembayaran sewa berhasil dikirim! Segera unggah bukti pembayaran di menu Order.' : 'Order berhasil dikirim! Menunggu verifikasi admin.';
        return redirect()->route('user.order')->with('success', $message);
    }

    /**
     * Batalkan pesanan tertunda (pending order).
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

    public function uploadProof(Request $request, $id)
    {
        $request->validate([
            'bukti_pembayaran_camera' => 'nullable|image|max:512000',
            'bukti_pembayaran_gallery' => 'nullable|image|max:512000',
        ]);

        $order = Transaksi::where('id_user', auth()->id())->findOrFail($id);

        if ($order->status !== 'verified') {
            return back()->with('error', 'Silakan unggah bukti setelah pesanan disetujui.');
        }

        // Terapkan batas waktu 24 jam
        if ($order->batas_bayar && now()->gt($order->batas_bayar)) {
            $order->update(['status' => 'failed']);
            return back()->with('error', 'Waktu maksimal unggah bukti (1x24 jam) telah habis. Pesanan otomatis gagal.');
        }

        $file = $request->file('bukti_pembayaran_camera') ?: $request->file('bukti_pembayaran_gallery');

        if ($file) {
            $tempPath = $file->store('temp', 'public');

            $order->update([
                'bukti_pembayaran' => 'storage/' . $tempPath,
            ]);

            \App\Jobs\ProcessImageOptimization::dispatch(
                $tempPath,
                'bukti_pembayaran',
                $order,
                'bukti_pembayaran'
            );

            // Beri tahu Pemilik Kos (Notify Kos Owner)
            $owner = $order->kamar->kos->user;
            if ($owner && $owner->nomor_wa) {
                $owner->notify(new \App\Notifications\BuktiPembayaranNotification([
                    'type' => 'tenant_order',
                    'name' => auth()->user()->name,
                    'kos_name' => $order->kamar->kos->nama_kos,
                    'amount' => $order->jumlah_bayar,
                ]));
            }

            return back()->with('success', 'Bukti pembayaran berhasil diunggah. Mohon tunggu konfirmasi admin.');
        }

        return back()->with('error', 'Silakan pilih atau ambil foto bukti pembayaran.');
    }

    /**
     * Alihkan (Toggle) kos favorit untuk pengguna saat ini.
     */
    public function toggleFavorit(Request $request, $id)
    {
        $user = auth()->user();
        $kos = Kos::findOrFail($id);

        if ($user->favoritKos()->where('id_kos', $id)->exists()) {
            $user->favoritKos()->detach($id);
            $message = 'Berhasil dihapus dari favorit.';
            $isFavorit = false;
        } else {
            $user->favoritKos()->attach($id);
            $message = 'Berhasil ditambahkan ke favorit.';
            $isFavorit = true;
        }

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => $message,
                'is_favorit' => $isFavorit
            ]);
        }

        return back()->with('success', $message);
    }
}
