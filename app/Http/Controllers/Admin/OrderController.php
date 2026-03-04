<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Kos;
use App\Models\Kamar;
use App\Models\User;
use App\Models\Transaksi;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();

        // Auto-cancel expired verified orders
        Transaksi::checkExpiry();

        // Auto-cancel expired registration requests
        \App\Models\PendingUser::checkExpiry();

        $tab = $request->get('tab', 'order');
        $statusFilter = $request->get('status');

        // Set sensible defaults for statusFilter based on tab if not provided
        if (!$statusFilter) {
            $statusFilter = ($tab === 'order') ? 'verif' : (($tab === 'riwayat') ? 'active' : 'pending');
        }

        // Get the member's kos
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

        // Count pending penyewa from pending_users table who registered with this kos's kode_kos
        $pendingCount = \App\Models\PendingUser::where('kode_kos', $kos->kode_kos)
            ->where('status', 'pending')
            ->count();

        // Count active penyewa (users linked to this kos)
        $activeCount = User::where('id_kos', $kos->id)->where('status', 'active')->count();

        // Count rejected from pending_users with this kode_kos
        $rejectedCount = \App\Models\PendingUser::where('kode_kos', $kos->kode_kos)
            ->where('status', 'rejected')
            ->count();

        // Count pending order transaksi (Verifikasi)
        $orderPendingCount = Transaksi::where('kode_kos', $kos->kode_kos)
            ->where('status', 'pending')
            ->count();

        // Count for 'Menunggu' (Diterima tapi belum upload bukti)
        $orderMenungguCount = Transaksi::where('kode_kos', $kos->kode_kos)
            ->where('status', 'verified')
            ->whereNull('bukti_pembayaran')
            ->count();

        // Count for 'Konfirmasi' (Sudah upload bukti tapi belum dikonfirmasi admin)
        $orderKonfirmasiCount = Transaksi::where('kode_kos', $kos->kode_kos)
            ->where('status', 'verified')
            ->whereNotNull('bukti_pembayaran')
            ->count();

        // Total verified count (Diterima)
        $orderVerifiedCount = $orderMenungguCount + $orderKonfirmasiCount;

        // Pending penyewa from pending_users
        $pendingPenyewa = \App\Models\PendingUser::where('kode_kos', $kos->kode_kos)
            ->where('status', 'pending')
            ->latest()
            ->paginate(10, ['*'], 'pending_page');

        // Riwayat: active users linked to kos OR rejected pending_users
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

        // Order Transaksi (pending orders from users)
        $orderTransaksi = Transaksi::where('kode_kos', $kos->kode_kos)
            ->with(['user', 'kamar'])
            ->when($tab === 'order', function ($q) use ($statusFilter) {
                if ($statusFilter === 'verif') {
                    $q->where('status', 'pending');
                } elseif ($statusFilter === 'menunggu') {
                    $q->where('status', 'verified')->whereNull('bukti_pembayaran');
                } elseif ($statusFilter === 'konfirmasi') {
                    $q->where('status', 'verified')->whereNotNull('bukti_pembayaran');
                } elseif ($statusFilter === 'rejected') {
                    $q->where('status', 'rejected');
                } elseif ($statusFilter === 'paid') {
                    $q->where('status', 'paid');
                } elseif ($statusFilter === 'failed') {
                    $q->where('status', 'failed');
                } else {
                    $q->where('status', 'pending');
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
            'orderVerifiedCount' => $orderVerifiedCount,
            'pendingPenyewa' => $pendingPenyewa,
            'riwayatPenyewa' => $riwayatPenyewa,
            'orderTransaksi' => $orderTransaksi,
            'kos' => $kos,
        ]);
    }

    /**
     * Verify an order: Set status to verified and start 24h timer.
     * Also marks kamar as 'terisi' to hold reservation.
     */
    public function verifyOrder($id)
    {
        $transaksi = Transaksi::find($id);

        if (!$transaksi) {
            return back()->with('error', 'Pesanan tidak ditemukan. Kemungkinan user telah membatalkan order ini.');
        }

        $user = auth()->user();
        $kos = Kos::where('id_user', $user->id)->first();

        // Ensure this order belongs to admin's kos
        if (!$kos || $transaksi->kode_kos != $kos->kode_kos) {
            return back()->with('error', 'Order tidak valid.');
        }

        // Check if kamar still available
        $kamar = Kamar::find($transaksi->id_kamar);
        if (!$kamar || $kamar->status !== 'tersedia') {
            return back()->with('error', 'Kamar sudah tidak tersedia.');
        }

        \DB::beginTransaction();
        try {
            // Update transaksi status to verified (accepted by admin)
            // This starts the 24h window to upload proof
            $transaksi->update([
                'status' => 'verified',
                'batas_bayar' => now()->addDay(),
            ]);

            // HOLD THE ROOM
            $kamar->update(['status' => 'terisi']);

            \DB::commit();
            return back()->with('success', 'Order telah diterima! Kamar telah diblokir sementara. User memiliki waktu 24 jam untuk bayar.');
        } catch (\Exception $e) {
            \DB::rollBack();
            return back()->with('error', 'Gagal memverifikasi: ' . $e->getMessage());
        }
    }

    /**
     * Confirm payment and activate user.
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

        if (!$transaksi->bukti_pembayaran) {
            return back()->with('error', 'Bukti pembayaran belum diunggah.');
        }

        // Check if expired
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

            $transaksi->update([
                'status' => 'paid', // New state for paid
                'tanggal_pembayaran' => now(),
            ]);

            // Update user
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

            // Update kamar
            $kamar = Kamar::find($transaksi->id_kamar);
            if ($kamar) {
                $kamar->update(['status' => 'terisi']);
            }

            // Reject any other pending orders for this kamar
            Transaksi::where('id_kamar', $transaksi->id_kamar)
                ->where('status', 'pending')
                ->where('id', '!=', $transaksi->id)
                ->update(['status' => 'rejected']);

            \DB::commit();
            return back()->with('success', 'Pembayaran dikonfirmasi! Penyewa sekarang aktif.');
        } catch (\Exception $e) {
            \DB::rollBack();
            return back()->with('error', 'Gagal mengonfirmasi: ' . $e->getMessage());
        }
    }

    /**
     * Reject an order.
     */
    public function rejectOrder($id)
    {
        $transaksi = Transaksi::find($id);

        if (!$transaksi) {
            return back()->with('error', 'Pesanan tidak ditemukan. Kemungkinan user telah membatalkan order ini.');
        }

        $user = auth()->user();
        $kos = Kos::where('id_user', $user->id)->first();

        // Ensure this order belongs to admin's kos
        if (!$kos || $transaksi->kode_kos != $kos->kode_kos) {
            return back()->with('error', 'Order tidak valid.');
        }

        \DB::beginTransaction();
        try {
            $transaksi->update([
                'status' => 'rejected',
            ]);

            // RELEASE THE ROOM
            $kamar = Kamar::find($transaksi->id_kamar);
            if ($kamar) {
                $kamar->update(['status' => 'tersedia']);
            }

            \DB::commit();
            return back()->with('success', 'Order berhasil ditolak. Kamar sekarang tersedia kembali.');
        } catch (\Exception $e) {
            \DB::rollBack();
            return back()->with('error', 'Gagal menolak order: ' . $e->getMessage());
        }
    }

    /**
     * Verify a new tenant registration (PendingUser).
     */
    public function verifyPenyewa($id)
    {
        $pendingUser = \App\Models\PendingUser::findOrFail($id);
        $user = auth()->user();
        $kos = Kos::where('id_user', $user->id)->first();

        // Security check: ensure this pending user is for this kos
        if (!$kos || $pendingUser->kode_kos !== $kos->kode_kos) {
            return back()->with('error', 'Akses ditolak.');
        }

        \DB::beginTransaction();
        try {
            // Check if user already exists
            $userRecord = User::where('email', $pendingUser->email)->first();

            if (!$userRecord) {
                $userRecord = new User();
                $userRecord->name = $pendingUser->name;
                $userRecord->email = $pendingUser->email;
                $userRecord->password = $pendingUser->password; // Already hashed in PendingUser if registered normally
                $userRecord->nik = $pendingUser->nik;
                $userRecord->nomor_wa = $pendingUser->nomor_wa;
                $userRecord->alamat = $pendingUser->alamat;
            }

            // Assign Anak Kos role and details
            $userRecord->id_plans = 1; // Anak Kos
            $userRecord->id_kos = $kos->id;
            $userRecord->status = 'active';
            $userRecord->save();

            // Sync role
            $userRecord->assignRole('users');

            // Update pending status
            $pendingUser->update(['status' => 'verified']);

            \DB::commit();
            return back()->with('success', "Akun {$userRecord->name} berhasil diverifikasi sebagai penyewa!");
        } catch (\Exception $e) {
            \DB::rollBack();
            return back()->with('error', 'Gagal memverifikasi user: ' . $e->getMessage());
        }
    }

    /**
     * Reject a new tenant registration.
     */
    public function rejectPenyewa($id)
    {
        $pendingUser = \App\Models\PendingUser::findOrFail($id);
        $pendingUser->update(['status' => 'rejected']);
        return back()->with('success', 'Pendaftaran user berhasil ditolak.');
    }
}
