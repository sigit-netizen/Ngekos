<?php

namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use App\Models\Langganan;
use App\Models\LanggananBaru;
use App\Models\User;
use Illuminate\Http\Request;
use App\Notifications\OwnerPaymentNotification;

class OrderManagementController extends Controller
{
    public function index(Request $request)
    {
        $activeTab = $request->get('tab', 'pending_member');
        $statusFilter = $request->get('status', 'active');

        // Hitung Pengguna Tertunda (Pending User) dan Member Tertunda dari Tabel PendingUser
        $pendingUserCount = \App\Models\PendingUser::where('id_plans', 1)->where('status', 'pending')->count();
        $pendingMemberCount = \App\Models\PendingUser::where('id_plans', '!=', 1)->where('status', 'pending')->count();
        $pendingPaymentMemberCount = \App\Models\PendingUser::where('id_plans', '!=', 1)->where('status', 'konfirmasi')->count();

        // Hitung pendaftaran yang ditolak dari tabel PendingUser
        $rejectedPendingMemberCount = \App\Models\PendingUser::where('id_plans', '!=', 1)->where('status', 'rejected')->count();
        $rejectedPendingUserCount = \App\Models\PendingUser::where('id_plans', 1)->where('status', 'rejected')->count();

        // Hitung Aktif/Ditolak dari Tabel User (Berdasarkan peran untuk mencocokkan visibilitas daftar)
        $memberRoles = ['admin', 'pro', 'premium', 'per_kamar_pro', 'per_kamar_premium'];
        $userRoles = ['users'];

        $activeMemberCount = User::role($memberRoles)->where('status', 'active')->count();
        $rejectedMemberCountFromUser = User::role($memberRoles)->where('status', 'rejected')->count();
        $activeUserCount = User::role($userRoles)->where('status', 'active')->count();
        $rejectedUserCountFromUser = User::role($userRoles)->where('status', 'rejected')->count();

        $packetCounts = (object)[
            'pending' => LanggananBaru::where('status', 'pending')->count(),
            'active' => Langganan::where('status', 'active')->count(),
            'rejected' => Langganan::where('status', 'rejected')->count(),
        ];

        // 1. Member Tertunda (Dari Tabel PendingUser)
        $pendingMembers = \App\Models\PendingUser::where('id_plans', '!=', 1)->where('status', 'pending')->latest()->paginate(10, ['*'], 'member_p');

        // 1b. Member Menunggu Pembayaran (Dari Tabel PendingUser)
        $pendingPaymentMembers = \App\Models\PendingUser::where('id_plans', '!=', 1)->where('status', 'konfirmasi')->latest()->paginate(10, ['*'], 'member_pay');

        // 2. Member Aktif/Ditolak (Active/Rejected Member)
        if ($statusFilter === 'rejected') {
            // Tampilkan pendaftaran yang ditolak dari pending_users
            $activeMembers = \App\Models\PendingUser::where('id_plans', '!=', 1)->where('status', 'rejected')->latest()->paginate(10, ['*'], 'member_a');
        } else {
            $activeMembers = User::role(['admin', 'pro', 'premium', 'per_kamar_pro', 'per_kamar_premium'])
                ->where('status', $statusFilter)
                ->latest()
                ->paginate(10, ['*'], 'member_a');
        }

        // 3. Pengguna Tertunda (Dari Tabel PendingUser)
        $pendingUsers = \App\Models\PendingUser::where('id_plans', 1)->where('status', 'pending')->latest()->paginate(10, ['*'], 'user_p');

        // 4. Pengguna Aktif/Ditolak (Active/Rejected User)
        if ($statusFilter === 'rejected') {
            $activeUsers = \App\Models\PendingUser::where('id_plans', 1)->where('status', 'rejected')->latest()->paginate(10, ['*'], 'user_a');
        } else {
            $activeUsers = User::role('users')->where('status', $statusFilter)->latest()->paginate(10, ['*'], 'user_a');
        }

        // 5. Paket Tertunda (Dari tabel STAGING/penampung)
        $pendingPackets = LanggananBaru::with(['user', 'jenis_langganan'])->where('status', 'pending')->latest()->paginate(10, ['*'], 'packet_p');

        // 6. Paket Aktif/Ditolak (Active/Rejected Paket)
        $activePackets = Langganan::with(['user', 'jenis_langganan'])->where('status', $statusFilter)->latest()->paginate(10, ['*'], 'packet_a');

        return view('superadmin.order', [
            'title' => 'Order & Verifikasi',
            'role' => 'superadmin',
            'activeTab' => $activeTab,
            'statusFilter' => $statusFilter,

            'pendingMemberCount' => $pendingMemberCount,
            'pendingPaymentMemberCount' => $pendingPaymentMemberCount,
            'activeMemberCount' => $activeMemberCount,
            'pendingUserCount' => $pendingUserCount,
            'activeUserCount' => $activeUserCount,
            'pendingPacketCount' => $packetCounts->pending ?? 0,
            'activePacketCount' => $packetCounts->active ?? 0,

            'rejectedMemberCount' => $rejectedPendingMemberCount + $rejectedMemberCountFromUser,
            'rejectedUserCount' => $rejectedPendingUserCount + $rejectedUserCountFromUser,
            'rejectedPacketCount' => $packetCounts->rejected ?? 0,

            'pendingMembers' => $pendingMembers,
            'pendingPaymentMembers' => $pendingPaymentMembers,
            'activeMembers' => $activeMembers,
            'pendingUsers' => $pendingUsers,
            'activeUsers' => $activeUsers,
            'pendingPackets' => $pendingPackets,
            'activePackets' => $activePackets,
        ]);
    }

    public function verifyUser(\App\Models\PendingUser $pendingUser)
    {
        // Untuk Anak Kos (id_plans == 1), tetap lakukan aktivasi langsung
        if ($pendingUser->id_plans == 1) {
            return \Illuminate\Support\Facades\DB::transaction(function () use ($pendingUser) {
                // 1. Buat Pengguna resmi (official User)
                $user = User::create([
                    'name' => $pendingUser->name,
                    'email' => $pendingUser->email,
                    'password' => $pendingUser->password,
                    'nik' => $pendingUser->nik,
                    'nomor_wa' => $pendingUser->nomor_wa,
                    'tanggal_lahir' => $pendingUser->tanggal_lahir,
                    'alamat' => $pendingUser->alamat,
                    'status' => 'active',
                ]);

                // 2. Logika Peran & Paket (Role & Plan Logic)
                $user->id_plans = 1;

                // Berikan peran yang sesuai (appropriate role)
                if ($pendingUser->kode_kos) {
                    $kos = \App\Models\Kos::where('kode_kos', $pendingUser->kode_kos)->first();
                    if ($kos) {
                        $user->id_kos = $kos->id;
                        $user->assignRole('users'); // Penyewa
                    } else {
                        $user->assignRole('user'); // Pengguna Umum
                    }
                } else {
                    $user->assignRole('user'); // Pengguna Umum
                }

                $user->save();

                // 5. Hapus dari penampung (staging)
                $pendingUser->delete();

                return back()->with('success', 'Akun ' . $user->name . ' berhasil diverifikasi dan diaktifkan!');
            });
        }

        // Untuk Member (id_plans == 2), cukup verifikasi datanya
        $pendingUser->update(['status' => 'verified']);
        return back()->with('success', 'Data ' . $pendingUser->name . ' berhasil diverifikasi. Menunggu pembayaran dari calon member.');
    }

    public function confirmMemberPayment(\App\Models\PendingUser $pendingUser)
    {
        return \Illuminate\Support\Facades\DB::transaction(function () use ($pendingUser) {
            // 1. Buat Pengguna resmi (official User)
            $user = User::create([
                'name' => $pendingUser->name,
                'email' => $pendingUser->email,
                'password' => $pendingUser->password,
                'nik' => $pendingUser->nik,
                'nomor_wa' => $pendingUser->nomor_wa,
                'tanggal_lahir' => $pendingUser->tanggal_lahir,
                'alamat' => $pendingUser->alamat,
                'status' => 'active',
            ]);

            // 2. Logika Peran & Paket (Role & Plan Logic)
            $planName = trim($pendingUser->plan_type);
            
            // Petakan nama paket ke ID (Tidak peka huruf besar/kecil)
            $plan = \Illuminate\Support\Facades\DB::table('plans')
                ->whereRaw('LOWER(nama_plans) = ?', [strtolower($planName)])
                ->first();
            
            if ($plan) {
                $user->id_plans = $plan->id;
            }

            $user->activateStatus(); // Pastikan status 'aktif' dan peran dipetakan
            $user->save();

            // 3. Nonaktifkan langganan aktif yang ada
            \App\Models\Langganan::where('id_user', $user->id)
                ->where('status', 'active')
                ->update(['status' => 'expired']);

            // 4. Buat rekaman (record) Langganan aktif
            // Cocokkan nama paket secara tepat dengan Jenis Langganan
            $searchKey = $planName;
            if (strtolower($planName) === 'pro') $searchKey = 'MEMBER PRO';
            if (strtolower($planName) === 'premium') $searchKey = 'MEMBER PREMIUM';

            $jenis = \App\Models\JenisLangganan::whereRaw('LOWER(nama) LIKE ?', ['%' . strtolower($searchKey) . '%'])->first();
            
            if ($jenis) {
                \App\Models\Langganan::create([
                    'id_user' => $user->id,
                    'id_langganan' => $jenis->id,
                    'jumlah_kamar' => $pendingUser->jumlah_kamar ?? 0,
                    'status' => 'active',
                    'tanggal_pembayaran' => now('Asia/Jakarta'),
                    'jatuh_tempo' => now('Asia/Jakarta')->addDays(30),
                ]);

                // Beri tahu Superadmin
                $superadmins = User::where('id_plans', 6)->get();
                $uniqueTargets = collect();
                foreach ($superadmins as $admin) {
                    if ($admin->nomor_wa) {
                        $uniqueTargets->put($admin->nomor_wa, $admin);
                    }
                }

                foreach ($uniqueTargets as $admin) {
                    $admin->notify(new OwnerPaymentNotification([
                        'owner_name' => $user->name,
                        'plan_name' => $planName,
                        'jumlah' => 0 
                    ]));
                }
            }

            // 4. Buat rekaman Kos default secara otomatis
            \App\Models\Kos::create([
                'id_user' => $user->id,
                'nama_kos' => 'Kos Baru ' . $user->name,
                'alamat' => $pendingUser->alamat ?? 'Lokasi belum ditentukan',
                'kode_kos' => rand(1000, 9999), // Generate a random 4-digit code
                'is_kode_kos_edited' => false,
            ]);

            // 5. Delete from staging
            $pendingUser->delete();

            return back()->with('success', 'Pembayaran member ' . $user->name . ' berhasil dikonfirmasi. Akun resmi aktif!');
        });
    }

    public function rejectUser(\Illuminate\Http\Request $request, \App\Models\PendingUser $pendingUser)
    {
        $request->validate([
            'rejection_reason' => 'required|string|max:500',
        ]);

        $pendingUser->update([
            'status' => 'rejected',
            'rejection_reason' => $request->rejection_reason,
        ]);

        return back()->with('success', 'Pendaftaran oleh ' . $pendingUser->name . ' berhasil ditolak!');
    }

    public function verifyPacket($id)
    {
        $staging = LanggananBaru::findOrFail($id);

        return \Illuminate\Support\Facades\DB::transaction(function () use ($staging) {
            // Perbarui atau buat rekaman TUNGGAL dalam tabel utama Langganan
            $subscription = Langganan::updateOrCreate(
                ['id_user' => $staging->id_user],
                [
                    'id_langganan' => $staging->id_langganan,
                    'jumlah_kamar' => $staging->jumlah_kamar,
                    'status' => 'active',
                    'bukti_pembayaran' => $staging->bukti_pembayaran,
                    'metode_pembayaran' => $staging->metode_pembayaran,
                    'tanggal_pembayaran' => now('Asia/Jakarta'),
                    'jatuh_tempo' => now('Asia/Jakarta')->addDays(30),
                ]
            );

            // Beri tahu Superadmin
            $superadmins = User::where('id_plans', 6)->get();
            $uniqueTargets = collect();
            foreach ($superadmins as $admin) {
                if ($admin->nomor_wa) {
                    $uniqueTargets->put($admin->nomor_wa, $admin);
                }
            }

            foreach ($uniqueTargets as $admin) {
                $owner = $subscription->user;
                $admin->notify(new OwnerPaymentNotification([
                    'owner_name' => $owner ? $owner->name : 'N/A',
                    'plan_name' => $subscription->jenis_langganan ? $subscription->jenis_langganan->nama : 'Member',
                    'jumlah' => 0 
                ]));
            }

            // Aktifkan kembali pengguna secara otomatis dan sinkronkan ID paket
            if ($subscription->user) {
                $subscription->user->id_plans = $subscription->id_langganan;
                $subscription->user->save();
                $subscription->user->activateStatus();
            }

            // Hapus dari penampung (staging)
            $staging->delete();

            return back()->with('success', 'Paket member berhasil diverifikasi dan diaktifkan!');
        });
    }

    public function rejectPacket($id)
    {
        $staging = LanggananBaru::findOrFail($id);

        $staging->update([
            'status' => 'rejected',
        ]);

        // Opsional: hapus segera atau tunggu penghapusan manual/reset otomatis (auto-reset)
        // $staging->delete();

        return back()->with('success', 'Transaksi paket ditolak!');
    }
}
