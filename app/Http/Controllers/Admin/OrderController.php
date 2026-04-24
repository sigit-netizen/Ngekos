<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Kos;
use App\Models\Kamar;
use App\Models\User;
use App\Models\Transaksi;
use Illuminate\Http\Request;
use App\Notifications\OrderVerifiedNotification;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();

        // Batalkan otomatis pesanan terverifikasi yang kedaluwarsa
        Transaksi::checkExpiry();

        // Keluarkan otomatis akun nonaktif untuk kos ini
        $kos = Kos::where('id_user', $user->id)->first();
        if ($kos) {
            Transaksi::checkDeadAccounts($kos->kode_kos);
        }

        // Batalkan otomatis permintaan pendaftaran yang kedaluwarsa
        \App\Models\PendingUser::checkExpiry();

        $tab = $request->get('tab', 'order');
        $statusFilter = $request->get('status');

        // Setel default yang masuk akal untuk statusFilter berdasarkan tab jika tidak disediakan
        if (!$statusFilter) {
            $statusFilter = ($tab === 'order') ? 'verif' : (($tab === 'riwayat') ? 'active' : 'pending');
        }

        // Dapatkan data kos milik member
        $kos = Kos::where('id_user', $user->id)->first();

        if (!$kos) {
            return view('member.order', [
                'title' => 'Order & Verifikasi',
                'role' => 'admin',
                'tab' => $tab,
                'statusFilter' => $statusFilter,
                'pendingCount' => 0,
                'activeCount' => 0,
                'rejectedCount' => 0,
                'orderPendingCount' => 0,
                'pendingPenyewa' => collect(),
                'riwayatPenyewa' => collect(),
                'orderTransaksi' => collect(),
            ]);
        }

        // Hitung penyewa tertunda dari tabel pending_users yang mendaftar dengan kode_kos kos ini
        $pendingCount = \App\Models\PendingUser::where('kode_kos', $kos->kode_kos)
            ->where('status', 'pending')
            ->count();

        // Hitung penyewa aktif (pengguna yang terhubung ke kos ini)
        $activeCount = User::where('id_kos', $kos->id)->where('status', 'active')->count();

        // Hitung pendaftaran yang ditolak dari pending_users dengan kode_kos ini
        $rejectedCount = \App\Models\PendingUser::where('kode_kos', $kos->kode_kos)
            ->where('status', 'rejected')
            ->count();

        // Hitung transaksi pesanan tertunda (Verifikasi)
        $orderPendingCount = Transaksi::where('kode_kos', $kos->kode_kos)
            ->where('status', 'pending')
            ->count();

        // Hitung untuk 'Menunggu' (Diterima tapi belum unggah bukti)
        $orderMenungguCount = Transaksi::where('kode_kos', $kos->kode_kos)
            ->where('status', 'verified')
            ->where('tipe', Transaksi::TYPE_BOOKING)
            ->whereNull('bukti_pembayaran')
            ->count();

        // Hitung untuk 'Konfirmasi' (Sudah unggah bukti tapi belum dikonfirmasi admin) - Pemesanan Baru (New Booking)
        $orderKonfirmasiCount = Transaksi::where('kode_kos', $kos->kode_kos)
            ->where('status', 'verified')
            ->where('tipe', Transaksi::TYPE_BOOKING)
            ->whereNotNull('bukti_pembayaran')
            ->count();

        // Hitung untuk 'Verifikasi Sewa' (Sudah unggah bukti tapi belum dikonfirmasi admin) - Sewa Berulang (Recurring Rent)
        $rentKonfirmasiCount = Transaksi::where('kode_kos', $kos->kode_kos)
            ->whereIn('status', ['pending', 'verified'])
            ->where('tipe', Transaksi::TYPE_SEWA)
            ->count();

        // Total jumlah terverifikasi (Diterima)
        $orderVerifiedCount = $orderMenungguCount + $orderKonfirmasiCount;

        // Penyewa tertunda dari pending_users
        $pendingPenyewa = \App\Models\PendingUser::where('kode_kos', $kos->kode_kos)
            ->where('status', 'pending')
            ->latest()
            ->paginate(10, ['*'], 'pending_page');

        // Riwayat: pengguna aktif yang terhubung ke kos ATAU pendaftaran tertunda yang ditolak (rejected pending_users)
        if ($statusFilter === 'rejected') {
            $riwayatPenyewa = \App\Models\PendingUser::where('kode_kos', $kos->kode_kos)
                ->where('status', 'rejected')
                ->latest()
                ->paginate(10, ['*'], 'riwayat_page');
        } else {
            $riwayatPenyewa = User::where('id_kos', $kos->id)
                ->where('status', 'active')
                ->latest()
                ->paginate(10, ['*'], 'riwayat_page');
        }

        // Transaksi Pesanan (pesanan tertunda dari pengguna)
        $orderTransaksi = Transaksi::where('kode_kos', $kos->kode_kos)
            ->with(['user', 'kamar'])
            ->when($tab === 'order', function ($q) use ($statusFilter) {
                if ($statusFilter === 'verif') {
                    $q->where('status', 'pending')->where('tipe', Transaksi::TYPE_BOOKING);
                } elseif ($statusFilter === 'menunggu') {
                    $q->where('status', 'verified')->where('tipe', Transaksi::TYPE_BOOKING)->whereNull('bukti_pembayaran');
                } elseif ($statusFilter === 'konfirmasi') {
                    $q->where('status', 'verified')->where('tipe', Transaksi::TYPE_BOOKING)->whereNotNull('bukti_pembayaran');
                } elseif ($statusFilter === 'sewa') {
                    $q->whereIn('status', ['pending', 'verified'])->where('tipe', Transaksi::TYPE_SEWA);
                } elseif ($statusFilter === 'rejected') {
                    $q->where('status', 'rejected');
                } elseif ($statusFilter === 'paid') {
                    $q->where('status', 'paid');
                } elseif ($statusFilter === 'failed') {
                    $q->where('status', 'failed');
                } elseif ($statusFilter === 'gagal') {
                    $q->whereIn('status', ['failed', 'rejected']);
                } else {
                    $q->where('status', 'pending')->where('tipe', Transaksi::TYPE_BOOKING);
                }
            })
            ->latest()
            ->paginate(10, ['*'], 'order_page');

        return view('member.order', [
            'title' => 'Order & Verifikasi',
            'role' => 'admin',
            'tab' => $tab,
            'statusFilter' => $statusFilter,
            'pendingCount' => $pendingCount,
            'activeCount' => $activeCount,
            'rejectedCount' => $rejectedCount,
            'orderPendingCount' => $orderPendingCount,
            'orderMenungguCount' => $orderMenungguCount,
            'orderKonfirmasiCount' => $orderKonfirmasiCount,
            'rentKonfirmasiCount' => $rentKonfirmasiCount,
            'orderVerifiedCount' => $orderVerifiedCount,
            'pendingPenyewa' => $pendingPenyewa,
            'riwayatPenyewa' => $riwayatPenyewa,
            'orderTransaksi' => $orderTransaksi,
            'kos' => $kos,
        ]);
    }

    /**
     * Verifikasi pesanan: Setel status ke terverifikasi (verified) dan mulai penghitung waktu 24 jam.
     * Juga tandai kamar sebagai 'terisi' untuk menahan reservasi.
     */
    public function verifyOrder($id)
    {
        $transaksi = Transaksi::find($id);

        if (!$transaksi) {
            return back()->with('error', 'Pesanan tidak ditemukan. Kemungkinan user telah membatalkan order ini.');
        }

        $user = auth()->user();
        $kos = Kos::where('id_user', $user->id)->first();

        // Pastikan pesanan ini milik kos admin
        if (!$kos || $transaksi->kode_kos != $kos->kode_kos) {
            return back()->with('error', 'Order tidak valid.');
        }

        // Periksa apakah kamar masih tersedia
        $kamar = Kamar::find($transaksi->id_kamar);
        if (!$kamar || $kamar->status !== 'tersedia') {
            return back()->with('error', 'Kamar sudah tidak tersedia.');
        }

        \DB::beginTransaction();
        try {
            // Perbarui status transaksi menjadi terverifikasi (diterima oleh admin)
            // Untuk pembayaran manual, gunakan tanggal yang direncanakan pengguna. Untuk lainnya, gunakan 1 hari h+1.
            $batasBayar = ($transaksi->metode_pembayaran === 'manual' && $transaksi->tanggal_pembayaran)
                ? $transaksi->tanggal_pembayaran
                : now()->addDay();

            $transaksi->update([
                'status' => 'verified',
                'batas_bayar' => $batasBayar,
            ]);

            // TAHAN KAMAR (HOLD THE ROOM)
            $kamar->update(['status' => 'terisi']);

            // Beri tahu Penyewa (Antrean/Queued untuk kecepatan)
            $orderUser = User::find($transaksi->id_user);
            if ($orderUser) {
                $orderUser->notify(new OrderVerifiedNotification([
                    'status' => 'verified',
                    'nama_kos' => $kos->nama_kos,
                    'nomor_kamar' => $kamar->nomor_kamar,
                    'kategori' => $transaksi->tipe
                ]));
            }

            \DB::commit();
            return back()->with('success', 'Order telah diterima! Kamar telah diblokir sementara. User memiliki waktu 24 jam untuk bayar.');
        } catch (\Exception $e) {
            \DB::rollBack();
            return back()->with('error', 'Gagal memverifikasi: ' . $e->getMessage());
        }
    }

    /**
     * Konfirmasi pembayaran dan aktifkan pengguna.
     */
    public function confirmPayment($id)
    {
        $transaksi = Transaksi::find($id);

        if (!$transaksi) {
            return back()->with('error', 'Pesanan tidak ditemukan. Kemungkinan user telah membatalkan order ini.');
        }

        $user = auth()->user();
        $kos = Kos::where('id_user', $user->id)->first();

        if (!$kos || $transaksi->kode_kos != $kos->kode_kos) {
            return back()->with('error', 'Akses ditolak.');
        }

        if ($transaksi->metode_pembayaran !== 'manual' && !$transaksi->bukti_pembayaran) {
            return back()->with('error', 'Bukti pembayaran belum diunggah.');
        }

        // Periksa apakah sudah kedaluwarsa
        if ($transaksi->batas_bayar && now()->gt($transaksi->batas_bayar)) {
            $transaksi->update(['status' => 'failed']);
            return back()->with('error', 'Batas waktu pembayaran telah habis. Pesanan otomatis gagal.');
        }

        \DB::beginTransaction();
        try {
            // Update transaksi status to success/active (using verified as final state or similar)
            // But let's use 'verified' as the final state since it was used before.
            // Wait, if 'verified' means "Accepted", then what is "Paid"?
            // Let's keep 'verified' as the state where they are accepted. 
            // Once paid, maybe status => 'active' or keep 'verified' but activate user.

            // Actually, the original code used 'verified' for "successfully joined".
            // Let's use 'paid' or just stick to 'active'.
            // Original code: 'status' => 'verified' in transaksi, and user 'status' => 'active'.

            $paymentDate = now('Asia/Jakarta');
            $duration = $transaksi->durasi_sewa ?? 1;
            $type = $transaksi->tipe_durasi ?? 'bulan';

            if ($type === 'hari') {
                $jatuhTempo = $paymentDate->copy()->addDays($duration);
            } elseif ($type === 'minggu') {
                $jatuhTempo = $paymentDate->copy()->addWeeks($duration);
            } else {
                $jatuhTempo = $paymentDate->copy()->addDays($duration * 30);
            }

            $transaksi->update([
                'status' => 'paid',
                'tanggal_pembayaran' => $paymentDate,
                'jatuh_tempo' => $jatuhTempo,
            ]);

            // Perbarui Pengguna (Update User)
            $orderUser = User::find($transaksi->id_user);
            if ($orderUser) {
                $orderUser->update([
                    'id_plans' => 1,
                    'status' => 'active',
                    'id_kos' => $kos->id,
                    'id_kamar' => $transaksi->id_kamar,
                ]);

                if (!$orderUser->hasRole('users')) {
                    $orderUser->assignRole('users');
                }
            }

            // Perbarui Kamar (Update Kamar)
            $kamar = Kamar::find($transaksi->id_kamar);
            if ($kamar) {
                $kamar->update(['status' => 'terisi']);
            }

            // Tolak pesanan tertunda lainnya untuk kamar ini
            Transaksi::where('id_kamar', $transaksi->id_kamar)
                ->where('status', 'pending')
                ->where('id', '!=', $transaksi->id)
                ->update(['status' => 'rejected']);

            \DB::commit();

            // Beri tahu Penyewa (Antrean/Queued untuk kecepatan)
            $orderUser = User::find($transaksi->id_user);
            if ($orderUser) {
                $orderUser->notify(new OrderVerifiedNotification([
                    'status' => 'paid',
                    'nama_kos' => $kos->nama_kos,
                    'nomor_kamar' => $kamar->nomor_kamar,
                    'kategori' => $transaksi->tipe
                ]));
            }

            return back()->with('success', 'Pembayaran dikonfirmasi! Penyewa sekarang aktif.');
        } catch (\Exception $e) {
            \DB::rollBack();
            return back()->with('error', 'Gagal mengonfirmasi: ' . $e->getMessage());
        }
    }

    /**
     * Tolak pesanan (Reject order).
     */
    public function rejectOrder($id)
    {
        $transaksi = Transaksi::find($id);

        if (!$transaksi) {
            return back()->with('error', 'Pesanan tidak ditemukan. Kemungkinan user telah membatalkan order ini.');
        }

        $user = auth()->user();
        $kos = Kos::where('id_user', $user->id)->first();

        // Pastikan pesanan ini milik kos admin
        if (!$kos || $transaksi->kode_kos != $kos->kode_kos) {
            return back()->with('error', 'Order tidak valid.');
        }

        \DB::beginTransaction();
        try {
            $transaksi->update([
                'status' => 'rejected',
            ]);

            // LEPASKAN KAMAR (RELEASE THE ROOM)
            $kamar = Kamar::find($transaksi->id_kamar);
            if ($kamar) {
                $kamar->update(['status' => 'tersedia']);
            }

            \DB::commit();

            // Beri tahu Penyewa (Antrean/Queued untuk kecepatan)
            $orderUser = User::find($transaksi->id_user);
            if ($orderUser) {
                $orderUser->notify(new OrderVerifiedNotification([
                    'status' => 'rejected',
                    'nama_kos' => $kos->nama_kos,
                    'nomor_kamar' => $kamar ? $kamar->nomor_kamar : '-',
                    'kategori' => $transaksi->tipe
                ]));
            }

            return back()->with('success', 'Order berhasil ditolak. Kamar sekarang tersedia kembali.');
        } catch (\Exception $e) {
            \DB::rollBack();
            return back()->with('error', 'Gagal menolak order: ' . $e->getMessage());
        }
    }

    /**
     * Verifikasi pendaftaran penyewa baru (PendingUser).
     */
    public function verifyPenyewa($id)
    {
        $pendingUser = \App\Models\PendingUser::findOrFail($id);
        $user = auth()->user();
        $kos = Kos::where('id_user', $user->id)->first();

        // Pemeriksaan keamanan: pastikan pengguna tertunda (pending user) ini untuk kos ini
        if (!$kos || $pendingUser->kode_kos !== $kos->kode_kos) {
            return back()->with('error', 'Akses ditolak.');
        }

        \DB::beginTransaction();
        try {
            // Periksa apakah pengguna sudah ada
            $userRecord = User::where('email', $pendingUser->email)->first();

            if (!$userRecord) {
                $userRecord = new User();
                $userRecord->name = $pendingUser->name;
                $userRecord->email = $pendingUser->email;
                $userRecord->password = $pendingUser->password; // Sudah di-hash di PendingUser jika terdaftar normal
                $userRecord->nik = $pendingUser->nik;
                $userRecord->nomor_wa = $pendingUser->nomor_wa;
                $userRecord->alamat = $pendingUser->alamat;
            }

            // Berikan peran Anak Kos dan detailnya
            $userRecord->id_plans = 1; // Anak Kos
            $userRecord->id_kos = $kos->id;
            $userRecord->status = 'active';
            $userRecord->save();

            // Sinkronisasi peran (Sync role)
            $userRecord->assignRole('users');

            // Perbarui status tertunda (Update pending status)
            $pendingUser->update(['status' => 'verified']);

            \DB::commit();

            // Beri tahu Penyewa (Antrean/Queued untuk kecepatan)
            $userRecord->notify(new OrderVerifiedNotification([
                'status' => 'verified',
                'nama_kos' => $kos->nama_kos,
                'nomor_kamar' => '-',
                'kategori' => 'registrasi'
            ]));

            return back()->with('success', "Akun {$userRecord->name} berhasil diverifikasi sebagai penyewa!");
        } catch (\Exception $e) {
            \DB::rollBack();
            return back()->with('error', 'Gagal memverifikasi user: ' . $e->getMessage());
        }
    }

    /**
     * Tolak pendaftaran penyewa baru.
     */
    public function rejectPenyewa($id)
    {
        $pendingUser = \App\Models\PendingUser::findOrFail($id);
        $user = auth()->user();
        $kos = Kos::where('id_user', $user->id)->first();

        // Pemeriksaan Keamanan: pastikan pengguna tertunda ini untuk kos ini
        if (!$kos || $pendingUser->kode_kos !== $kos->kode_kos) {
            return back()->with('error', 'Akses ditolak.');
        }

        $pendingUser->update(['status' => 'rejected']);

        // Beri tahu Penyewa (Antrean/Queued untuk kecepatan)
        $notifiable = new \App\Models\User([
            'name' => $pendingUser->name,
            'email' => $pendingUser->email,
            'nomor_wa' => $pendingUser->nomor_wa
        ]);
        $notifiable->notify(new OrderVerifiedNotification([
            'status' => 'rejected',
            'nama_kos' => $kos->nama_kos,
            'nomor_kamar' => '-',
            'kategori' => 'registrasi'
        ]));

        return back()->with('success', 'Pendaftaran user berhasil ditolak.');
    }
}
