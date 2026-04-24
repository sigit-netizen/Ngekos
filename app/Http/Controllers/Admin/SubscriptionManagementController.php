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

        // Ambil akun bank Superadmin untuk instruksi pembayaran
        $superadminBanks = \App\Models\User::role('superadmin')
            ->with('nomorBank')
            ->get()
            ->filter(fn($u) => $u->nomorBank)
            ->map(fn($u) => $u->nomorBank);

        // 1. Ambil langganan pengguna yang valid saat ini (untuk tampilan kartu)
        $subscription = Langganan::with('jenis_langganan')
            ->where('id_user', $user->id)
            ->whereNotNull('tanggal_pembayaran')
            ->latest()
            ->first();

        // 2. Ambil pesanan tertunda dari tabel penampung (staging) BARU
        $pendingSubscription = LanggananBaru::with('jenis_langganan')
            ->where('id_user', $user->id)
            ->where('status', 'pending')
            ->first();

        // --- LOGIKA RESET OTOMATIS (Untuk Tabel Staging) ---
        if ($pendingSubscription) {
            $isExpired = $pendingSubscription->updated_at->addDay()->isPast();
            if ($isExpired && !$pendingSubscription->bukti_pembayaran) {
                $pendingSubscription->delete();
                $pendingSubscription = null;
            }
        }

        // 2. Ambil semua paket yang tersedia untuk pembelian/peningkatan (upgrade)
        $availablePlans = JenisLangganan::all();

        // 3. Ambil riwayat pembelian (history)
        $history = Langganan::with('jenis_langganan')
            ->where('id_user', $user->id)
            ->whereNotNull('tanggal_pembayaran')
            ->orderBy('tanggal_pembayaran', 'desc')
            ->paginate(10);

        // 4. Hitung metrik (secara aman di controller)
        // Kebenaran utama adalah jatuh_tempo, cadangan (fallback) ke perhitungan khusus 30 hari jika tidak ada
        $expiryDate = $subscription?->jatuh_tempo ? \Carbon\Carbon::parse($subscription->jatuh_tempo) : ($subscription?->tanggal_pembayaran ? \Carbon\Carbon::parse($subscription->tanggal_pembayaran)->addDays(30) : null);

        // Gunakan Asia/Jakarta untuk perbandingan
        $nowWib = now('Asia/Jakarta')->startOfDay();
        $expiryWib = $expiryDate ? $expiryDate->copy()->timezone('Asia/Jakarta')->startOfDay() : null;

        $daysRemaining = $expiryWib ? (int) $nowWib->diffInDays($expiryWib, false) : 0;

        // Kategorikan status untuk warna UI (Sinkron dengan logika Superadmin)
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

        // Jika ini adalah rute admin/member, kami menggunakan tampilan tagihan khusus
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
     * Tingkatkan atau ubah paket langganan.
     */
    public function update(Request $request)
    {
        $request->validate([
            'id_langganan' => 'required|exists:jenis_langganans,id',
            'jumlah_kamar' => 'nullable|integer|min:1',
        ]);

        $user = Auth::user();

        // Cegah pembelian baru HANYA jika bukti sudah diunggah dan belum kedaluwarsa
        $existingPending = LanggananBaru::where('id_user', $user->id)
            ->where('status', 'pending')
            ->first();

        if ($existingPending && $existingPending->bukti_pembayaran && !$existingPending->updated_at->addDay()->isPast()) {
            return back()->with('error', 'Pesanan Anda sedang diverifikasi oleh Admin. Tunggu verifikasi selesai atau tunggu 24 jam hingga sistem reset otomatis.');
        }

        // Validasi jumlah kamar untuk paket "per kamar"
        if (in_array($request->id_langganan, [4, 5])) {
            $kos = $user->kos()->first();
            $currentRoomsCount = $kos ? $kos->kamars()->count() : 0;
            $requestedRooms = $request->jumlah_kamar ?? 0;

            if ($requestedRooms < $currentRoomsCount) {
                return back()->with('error', "Jumlah kamar dalam paket ($requestedRooms) tidak boleh kurang dari jumlah kamar yang telah Anda miliki ($currentRoomsCount). Silakan hapus beberapa kamar terlebih dahulu atau tambahkan jumlah kamar dalam paket.");
            }
        }

        // Gunakan updateOrCreate pada tabel STAGING (LanggananBaru)
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
     * Unggah bukti pembayaran untuk langganan yang tertunda.
     */
    public function uploadProof(Request $request)
    {
        $request->validate([
            'bukti_pembayaran' => ['required', 'image', 'mimes:jpeg,png,jpg', 'max:512000'], // 500MB seperti yang diminta sebelumnya
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
            // Hapus bukti lama jika ada
            if ($subscription->bukti_pembayaran) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($subscription->bukti_pembayaran);
            }

            $file = $request->file('bukti_pembayaran');
            $path = $file->store('pembayaran_paket_langganan', 'public');

            $subscription->update([
                'bukti_pembayaran' => $path,
                'metode_pembayaran' => $request->metode_pembayaran,
            ]);

            // Beri tahu Superadmin
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
