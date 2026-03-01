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
        $tab = $request->get('tab', 'pending');
        $statusFilter = $request->get('status', 'active');

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

        // Count pending order transaksi for this kos
        $orderPendingCount = Transaksi::where('kode_kos', $kos->kode_kos)
            ->where('status', 'pending')
            ->count();

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
                if ($statusFilter === 'verified') {
                    $q->where('status', 'verified');
                } elseif ($statusFilter === 'rejected') {
                    $q->where('status', 'rejected');
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
            'pendingPenyewa' => $pendingPenyewa,
            'riwayatPenyewa' => $riwayatPenyewa,
            'orderTransaksi' => $orderTransaksi,
            'kos' => $kos,
        ]);
    }

    /**
     * Verify an order: user becomes tenant of the kos + kamar.
     */
    public function verifyOrder(Transaksi $transaksi)
    {
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

        // Update transaksi status
        $transaksi->update([
            'status' => 'verified',
            'tanggal_pembayaran' => now(),
        ]);

        // Update user: link to kos and kamar
        $orderUser = User::find($transaksi->id_user);
        if ($orderUser) {
            $orderUser->update([
                'id_kos' => $kos->id,
                'id_kamar' => $kamar->id,
            ]);
        }

        // Update kamar status to occupied
        $kamar->update(['status' => 'disewa']);

        // Reject any other pending orders for this kamar
        Transaksi::where('id_kamar', $kamar->id)
            ->where('status', 'pending')
            ->where('id', '!=', $transaksi->id)
            ->update(['status' => 'rejected']);

        return back()->with('success', 'Order berhasil diverifikasi! Penyewa telah ditambahkan.');
    }

    /**
     * Reject an order.
     */
    public function rejectOrder(Transaksi $transaksi)
    {
        $user = auth()->user();
        $kos = Kos::where('id_user', $user->id)->first();

        // Ensure this order belongs to admin's kos
        if (!$kos || $transaksi->kode_kos != $kos->kode_kos) {
            return back()->with('error', 'Order tidak valid.');
        }

        $transaksi->update([
            'status' => 'rejected',
        ]);

        return back()->with('success', 'Order berhasil ditolak.');
    }
}
