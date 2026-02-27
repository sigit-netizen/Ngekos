<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Langganan;
use App\Models\JenisLangganan;
use Illuminate\Support\Facades\Auth;

class SubscriptionManagementController extends Controller
{
    /**
     * Display a listing of the subscription.
     */
    public function index()
    {
        $user = Auth::user();

        // 1. Fetch user's active subscription (latest one)
        $subscription = Langganan::with('jenis_langganan')
            ->where('id_user', $user->id)
            ->latest()
            ->first();

        // 2. Fetch all available plans for purchasing/upgrading
        $availablePlans = JenisLangganan::all();

        // 3. Fetch purchase history (all except maybe the very latest active one, or just all)
        $history = Langganan::with('jenis_langganan')
            ->where('id_user', $user->id)
            ->whereNotNull('tanggal_pembayaran')
            ->orderBy('tanggal_pembayaran', 'desc')
            ->get();

        // 4. Calculate metrics (securely in controller)
        $purchaseDate = $subscription?->tanggal_pembayaran ? \Carbon\Carbon::parse($subscription->tanggal_pembayaran) : null;
        $expiryDate = $purchaseDate ? $purchaseDate->copy()->addDays(30) : null;
        $daysRemaining = $expiryDate ? max(0, now()->diffInDays($expiryDate, false)) : 0;

        // If it's the admin/member routes, we use the specific billing view
        if (request()->is('admin/tagihan-sistem')) {
            return view('member.tagihan_sistem', [
                'title' => 'Tagihan Sistem',
                'subscription' => $subscription,
                'availablePlans' => $availablePlans,
                'history' => $history,
                'expiryDate' => $expiryDate,
                'daysRemaining' => $daysRemaining,
                'role' => 'admin'
            ]);
        }

        return view('admin.subscription', [
            'title' => 'Manajemen Langganan',
            'subscription' => $subscription,
            'availablePlans' => $availablePlans,
            'history' => $history,
            'expiryDate' => $expiryDate,
            'daysRemaining' => $daysRemaining,
            'role' => 'admin'
        ]);
    }

    /**
     * Upgrade or change subscription plan.
     */
    public function update(Request $request)
    {
        $request->validate([
            'id_langganan' => 'required|exists:jenis_langganans,id',
            'jumlah_kamar' => 'nullable|integer|min:1',
        ]);

        $user = Auth::user();

        Langganan::updateOrCreate(
            ['id_user' => $user->id],
            [
                'id_langganan' => $request->id_langganan,
                'jumlah_kamar' => $request->jumlah_kamar ?? 0,
                'status' => 'pending', // Await payment confirmation
                'tanggal_pembayaran' => null
            ]
        );

        return back()->with('success', 'Permintaan perubahan paket berhasil diajukan!');
    }
}
