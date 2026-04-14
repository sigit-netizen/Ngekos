<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Langganan;
use App\Models\LanggananBaru;
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

        // Fetch Superadmin bank accounts for payment instructions
        $superadminBanks = \App\Models\User::role('superadmin')
            ->with('nomorBank')
            ->get()
            ->filter(fn($u) => $u->nomorBank)
            ->map(fn($u) => $u->nomorBank);

        // 1. Fetch user's current valid subscription (for display cards)
        $subscription = Langganan::with('jenis_langganan')
            ->where('id_user', $user->id)
            ->whereNotNull('tanggal_pembayaran')
            ->latest()
            ->first();

        // 2. Fetch pending order from the NEW staging table
        $pendingSubscription = LanggananBaru::with('jenis_langganan')
            ->where('id_user', $user->id)
            ->where('status', 'pending')
            ->first();

        // --- AUTO RESET LOGIC (For Staging Table) ---
        if ($pendingSubscription) {
            $isExpired = $pendingSubscription->updated_at->addDay()->isPast();
            if ($isExpired && !$pendingSubscription->bukti_pembayaran) {
                $pendingSubscription->delete();
                $pendingSubscription = null;
            }
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

        $kos = $user->kos()->first();
        $currentRoomsCount = $kos ? $kos->kamars()->count() : 0;

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
                'currentRoomsCount' => $currentRoomsCount,
                'role' => 'admin',
                'superadminBanks' => $superadminBanks
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

        // Prevent new purchase ONLY if proof is already uploaded and not yet expired
        $existingPending = LanggananBaru::where('id_user', $user->id)
            ->where('status', 'pending')
            ->first();

        if ($existingPending && $existingPending->bukti_pembayaran && !$existingPending->updated_at->addDay()->isPast()) {
            return back()->with('error', 'Pesanan Anda sedang diverifikasi oleh Admin. Tunggu verifikasi selesai atau tunggu 24 jam hingga sistem reset otomatis.');
        }

        // Room count validation for "per kamar" plans
        if (in_array($request->id_langganan, [4, 5])) {
            $kos = $user->kos()->first();
            $currentRoomsCount = $kos ? $kos->kamars()->count() : 0;
            $requestedRooms = $request->jumlah_kamar ?? 0;

            if ($requestedRooms < $currentRoomsCount) {
                return back()->with('error', "Jumlah kamar dalam paket ($requestedRooms) tidak boleh kurang dari jumlah kamar yang telah Anda miliki ($currentRoomsCount). Silakan hapus beberapa kamar terlebih dahulu atau tambahkan jumlah kamar dalam paket.");
            }
        }

        // Use updateOrCreate on the STAGING table
        LanggananBaru::updateOrCreate(
            ['id_user' => $user->id],
            [
                'id_langganan' => $request->id_langganan,
                'jumlah_kamar' => $request->jumlah_kamar ?? 0,
                'status' => 'pending',
                'bukti_pembayaran' => null, // Reset proof when changing plan
                'metode_pembayaran' => null,
            ]
        );

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
        $subscription = LanggananBaru::where('id_user', $user->id)
            ->where('status', 'pending')
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

            // Notify Superadmins
            $superadmins = \App\Models\User::role('superadmin')->get();
            foreach ($superadmins as $admin) {
                if ($admin->nomor_wa) {
                    $admin->notify(new \App\Notifications\BuktiPembayaranNotification([
                        'type' => 'owner_sub',
                        'name' => $user->name,
                        'plan_name' => $subscription->jenis_langganan->nama_paket ?? 'Member',
                    ]));
                }
            }

            return back()->with('success', 'Bukti pembayaran berhasil diunggah! Hubungi admin untuk aktivasi cepat.');
        }

        return back()->with('error', 'Gagal mengunggah bukti pembayaran.');
    }
}
