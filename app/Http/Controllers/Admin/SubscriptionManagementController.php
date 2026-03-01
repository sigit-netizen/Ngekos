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
            ->paginate(10);

        // 4. Calculate metrics (securely in controller)
        // Primary truth is jatuh_tempo, fallback to specialized 30-day calculation if missing
        $expiryDate = $subscription?->jatuh_tempo ? \Carbon\Carbon::parse($subscription->jatuh_tempo) : ($subscription?->tanggal_pembayaran ? \Carbon\Carbon::parse($subscription->tanggal_pembayaran)->addDays(28) : null);

        // Use Asia/Jakarta for comparison
        $nowWib = now('Asia/Jakarta')->startOfDay();
        $expiryWib = $expiryDate ? $expiryDate->copy()->timezone('Asia/Jakarta')->startOfDay() : null;

        $daysRemaining = $expiryWib ? (int) $nowWib->diffInDays($expiryWib, false) : 0;

        // Categorize status for UI colors (Synchronized with Superadmin logic)
        $computedStatus = 'active';
        $graceDaysRemaining = 0;
        $matiDaysCount = 0;
        if ($daysRemaining < 0) {
            if ($daysRemaining >= -3) {
                $computedStatus = 'grace';
                $graceDaysRemaining = 3 - abs($daysRemaining) + 1;
            } else {
                $computedStatus = 'inactive';
                $matiDaysCount = abs($daysRemaining) - 3;
            }
        }

        // If it's the admin/member routes, we use the specific billing view
        if (request()->is('admin/tagihan-sistem')) {
            return view('member.tagihan_sistem', [
                'title' => 'Tagihan Sistem',
                'subscription' => $subscription,
                'availablePlans' => $availablePlans,
                'history' => $history,
                'expiryDate' => $expiryDate,
                'daysRemaining' => $daysRemaining,
                'computedStatus' => $computedStatus,
                'graceDaysRemaining' => $graceDaysRemaining,
                'matiDaysCount' => $matiDaysCount,
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
            'computedStatus' => $computedStatus,
            'graceDaysRemaining' => $graceDaysRemaining,
            'matiDaysCount' => $matiDaysCount,
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
