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

        // 1. Fetch user's current valid subscription (for display cards)
        // We want the latest one that HAS been paid (to show accurate active/expired dates)
        $subscription = Langganan::with('jenis_langganan')
            ->where('id_user', $user->id)
            ->whereNotNull('tanggal_pembayaran')
            ->latest()
            ->first();

        // 1b. Fetch any pending or rejected LATEST overall attempt
        // Only show pending/rejected banner if it is the absolute latest record
        // This prevents old rejected attempts from showing up after a new active one exists.
        $latestOverall = Langganan::where('id_user', $user->id)->latest()->first();
        $pendingSubscription = null;
        if ($latestOverall && in_array($latestOverall->status, ['pending', 'rejected'])) {
            $pendingSubscription = $latestOverall->load('jenis_langganan');
        }

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
        $expiryDate = $subscription?->jatuh_tempo ? \Carbon\Carbon::parse($subscription->jatuh_tempo) : ($subscription?->tanggal_pembayaran ? \Carbon\Carbon::parse($subscription->tanggal_pembayaran)->addDays(30) : null);

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
                'pendingSubscription' => $pendingSubscription,
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
            'pendingSubscription' => $pendingSubscription,
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

        // If there's already a pending subscription, delete it first to avoid duplicates
        Langganan::where('id_user', $user->id)
            ->where('status', 'pending')
            ->delete();

        Langganan::create([
            'id_user' => $user->id,
            'id_langganan' => $request->id_langganan,
            'jumlah_kamar' => $request->jumlah_kamar ?? 0,
            'status' => 'pending', // Await payment confirmation
            'tanggal_pembayaran' => null
        ]);

        return back()->with('success', 'Permintaan perubahan paket berhasil diajukan! Silakan lengkapi pembayaran.');
    }

    /**
     * Upload payment proof for a pending subscription.
     */
    public function uploadProof(Request $request)
    {
        $request->validate([
            'bukti_pembayaran' => ['required', 'image', 'mimes:jpeg,png,jpg', 'max:512000'], // 500MB as requested before
            'metode_pembayaran' => ['required', 'string'],
        ]);

        $user = Auth::user();
        $subscription = Langganan::where('id_user', $user->id)
            ->where('status', 'pending')
            ->latest()
            ->first();

        if (!$subscription) {
            return back()->with('error', 'Tidak ada permintaan paket yang aktif.');
        }

        if ($request->hasFile('bukti_pembayaran')) {
            // Delete old proof if exists
            if ($subscription->bukti_pembayaran) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($subscription->bukti_pembayaran);
            }

            $file = $request->file('bukti_pembayaran');
            $path = $file->store('pembayaran_paket_langganan', 'public');

            $subscription->update([
                'bukti_pembayaran' => $path,
                'metode_pembayaran' => $request->metode_pembayaran,
            ]);

            return back()->with('success', 'Bukti pembayaran berhasil diunggah! Hubungi admin untuk aktivasi cepat.');
        }

        return back()->with('error', 'Gagal mengunggah bukti pembayaran.');
    }
}
