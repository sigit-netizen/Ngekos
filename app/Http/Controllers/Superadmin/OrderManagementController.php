<?php

namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use App\Models\Langganan;
use App\Models\LanggananBaru;
use App\Models\User;
use Illuminate\Http\Request;

class OrderManagementController extends Controller
{
    public function index(Request $request)
    {
        $activeTab = $request->get('tab', 'pending_member');
        $statusFilter = $request->get('status', 'active');

        // Counts for Pending User and Pending Member from PendingUser Table
        $pendingUserCount = \App\Models\PendingUser::where('id_plans', 1)->where('status', 'pending')->count();
        $pendingMemberCount = \App\Models\PendingUser::where('id_plans', '!=', 1)->where('status', 'pending')->count();
        $pendingPaymentMemberCount = \App\Models\PendingUser::where('id_plans', '!=', 1)->where('status', 'konfirmasi')->count();

        // Rejected counts from PendingUser table
        $rejectedPendingMemberCount = \App\Models\PendingUser::where('id_plans', '!=', 1)->where('status', 'rejected')->count();
        $rejectedPendingUserCount = \App\Models\PendingUser::where('id_plans', 1)->where('status', 'rejected')->count();

        // Counts for Active/Rejected from User Table (Role-based to match list visibility)
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

        // 1. Pending Member (From PendingUser Table)
        $pendingMembers = \App\Models\PendingUser::where('id_plans', '!=', 1)->where('status', 'pending')->latest()->paginate(10, ['*'], 'member_p');

        // 1b. Pending Payment Member (From PendingUser Table)
        $pendingPaymentMembers = \App\Models\PendingUser::where('id_plans', '!=', 1)->where('status', 'konfirmasi')->latest()->paginate(10, ['*'], 'member_pay');

        // 2. Active/Rejected Member
        if ($statusFilter === 'rejected') {
            // Show rejected registrations from pending_users
            $activeMembers = \App\Models\PendingUser::where('id_plans', '!=', 1)->where('status', 'rejected')->latest()->paginate(10, ['*'], 'member_a');
        } else {
            $activeMembers = User::role(['admin', 'pro', 'premium', 'per_kamar_pro', 'per_kamar_premium'])
                ->where('status', $statusFilter)
                ->latest()
                ->paginate(10, ['*'], 'member_a');
        }

        // 3. Pending User (From PendingUser Table)
        $pendingUsers = \App\Models\PendingUser::where('id_plans', 1)->where('status', 'pending')->latest()->paginate(10, ['*'], 'user_p');

        // 4. Active/Rejected User
        if ($statusFilter === 'rejected') {
            $activeUsers = \App\Models\PendingUser::where('id_plans', 1)->where('status', 'rejected')->latest()->paginate(10, ['*'], 'user_a');
        } else {
            $activeUsers = User::role('users')->where('status', $statusFilter)->latest()->paginate(10, ['*'], 'user_a');
        }

        // 5. Pending Paket (From STAGING table)
        $pendingPackets = LanggananBaru::with(['user', 'jenis_langganan'])->where('status', 'pending')->latest()->paginate(10, ['*'], 'packet_p');

        // 6. Active/Rejected Paket
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
        // For Anak Kos (id_plans == 1), keep immediate activation
        if ($pendingUser->id_plans == 1) {
            return \Illuminate\Support\Facades\DB::transaction(function () use ($pendingUser) {
                // 1. Create the official User
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

                // 2. Role & Plan Logic
                $user->id_plans = 1;

                // Assign appropriate role
                if ($pendingUser->kode_kos) {
                    $kos = \App\Models\Kos::where('kode_kos', $pendingUser->kode_kos)->first();
                    if ($kos) {
                        $user->id_kos = $kos->id;
                        $user->assignRole('users'); // Penyewa
                    } else {
                        $user->assignRole('user'); // User Umum
                    }
                } else {
                    $user->assignRole('user'); // User Umum
                }

                $user->save();

                // 5. Delete from staging
                $pendingUser->delete();

                return back()->with('success', 'Akun ' . $user->name . ' berhasil diverifikasi dan diaktifkan!');
            });
        }

        // For Members (id_plans == 2), just verify the data
        $pendingUser->update(['status' => 'verified']);
        return back()->with('success', 'Data ' . $pendingUser->name . ' berhasil diverifikasi. Menunggu pembayaran dari calon member.');
    }

    public function confirmMemberPayment(\App\Models\PendingUser $pendingUser)
    {
        return \Illuminate\Support\Facades\DB::transaction(function () use ($pendingUser) {
            // 1. Create the official User
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

            // 2. Role & Plan Logic
            $planName = trim($pendingUser->plan_type);
            
            // Map plan names to IDs (Case-insensitive)
            $plan = \Illuminate\Support\Facades\DB::table('plans')
                ->whereRaw('LOWER(nama_plans) = ?', [strtolower($planName)])
                ->first();
            
            if ($plan) {
                $user->id_plans = $plan->id;
            }

            $user->activateStatus(); // Ensure status is 'aktif' and roles are mapped
            $user->save();

            // 3. Deactivate existing active subscriptions
            \App\Models\Langganan::where('id_user', $user->id)
                ->where('status', 'active')
                ->update(['status' => 'expired']);

            // 4. Create active Langganan record
            // Match the plan name precisely with Jenis Langganan
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
            }

            // 4. Automatically create a default Kos record
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
            // Update or create the SINGLE record in the main Langganan table
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

            // Auto-reactivate user and sync plan ID
            if ($subscription->user) {
                $subscription->user->id_plans = $subscription->id_langganan;
                $subscription->user->save();
                $subscription->user->activateStatus();
            }

            // Delete from staging
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

        // Optionally delete it immediately or wait for manual deletion/auto-reset
        // $staging->delete();

        return back()->with('success', 'Transaksi paket ditolak!');
    }
}
