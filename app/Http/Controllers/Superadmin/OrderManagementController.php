<?php

namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use App\Models\Langganan;
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
        $pendingMemberCount = \App\Models\PendingUser::where('id_plans', 2)->where('status', 'pending')->count();

        // Rejected counts from PendingUser table
        $rejectedPendingMemberCount = \App\Models\PendingUser::where('id_plans', 2)->where('status', 'rejected')->count();
        $rejectedPendingUserCount = \App\Models\PendingUser::where('id_plans', 1)->where('status', 'rejected')->count();

        // Counts for Active/Rejected from User Table (Role-based to match list visibility)
        $memberRoles = ['admin', 'pro', 'premium', 'per_kamar_pro', 'per_kamar_premium'];
        $userRoles = ['users'];

        $activeMemberCount = User::role($memberRoles)->where('status', 'active')->count();
        $rejectedMemberCountFromUser = User::role($memberRoles)->where('status', 'rejected')->count();
        $activeUserCount = User::role($userRoles)->where('status', 'active')->count();
        $rejectedUserCountFromUser = User::role($userRoles)->where('status', 'rejected')->count();

        $packetCounts = Langganan::selectRaw("
            SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) as pending,
            SUM(CASE WHEN status = 'active' THEN 1 ELSE 0 END) as active,
            SUM(CASE WHEN status = 'rejected' THEN 1 ELSE 0 END) as rejected
        ")->first();

        // 1. Pending Member (From PendingUser Table)
        $pendingMembers = \App\Models\PendingUser::where('id_plans', 2)->where('status', 'pending')->latest()->paginate(10, ['*'], 'member_p');

        // 2. Active/Rejected Member
        if ($statusFilter === 'rejected') {
            // Show rejected registrations from pending_users
            $activeMembers = \App\Models\PendingUser::where('id_plans', 2)->where('status', 'rejected')->latest()->paginate(10, ['*'], 'member_a');
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

        // 5. Pending Paket (Eager load to avoid N+1)
        $pendingPackets = Langganan::with(['user', 'jenis_langganan'])->where('status', 'pending')->latest()->paginate(10, ['*'], 'packet_p');

        // 6. Active/Rejected Paket
        $activePackets = Langganan::with(['user', 'jenis_langganan'])->where('status', $statusFilter)->latest()->paginate(10, ['*'], 'packet_a');

        return view('superadmin.order', [
            'title' => 'Order & Verifikasi',
            'role' => 'superadmin',
            'activeTab' => $activeTab,
            'statusFilter' => $statusFilter,

            'pendingMemberCount' => $pendingMemberCount,
            'activeMemberCount' => $activeMemberCount,
            'pendingUserCount' => $pendingUserCount,
            'activeUserCount' => $activeUserCount,
            'pendingPacketCount' => $packetCounts->pending ?? 0,
            'activePacketCount' => $packetCounts->active ?? 0,

            'rejectedMemberCount' => $rejectedPendingMemberCount + $rejectedMemberCountFromUser,
            'rejectedUserCount' => $rejectedPendingUserCount + $rejectedUserCountFromUser,
            'rejectedPacketCount' => $packetCounts->rejected ?? 0,

            'pendingMembers' => $pendingMembers,
            'activeMembers' => $activeMembers,
            'pendingUsers' => $pendingUsers,
            'activeUsers' => $activeUsers,
            'pendingPackets' => $pendingPackets,
            'activePackets' => $activePackets,
        ]);
    }

    public function verifyUser(\App\Models\PendingUser $pendingUser)
    {
        return \Illuminate\Support\Facades\DB::transaction(function () use ($pendingUser) {
            // 1. Create the official User
            $user = User::create([
                'name' => $pendingUser->name,
                'email' => $pendingUser->email,
                'password' => $pendingUser->password, // Password already hashed from signup
                'nik' => $pendingUser->nik,
                'nomor_wa' => $pendingUser->nomor_wa,
                'tanggal_lahir' => $pendingUser->tanggal_lahir,
                'alamat' => $pendingUser->alamat,
                'status' => 'active',
            ]);

            // 2. Role & Plan Logic
            if ($pendingUser->id_plans == 1) {
                // Anak Kos
                $user->id_plans = 1;
                $user->assignRole('users');

                // Link to kos via kode_kos
                if ($pendingUser->kode_kos) {
                    $kos = \App\Models\Kos::where('kode_kos', $pendingUser->kode_kos)->first();
                    if ($kos) {
                        $user->id_kos = $kos->id;
                    }
                }

                $user->save();
            } else {
                // Pemilik Kos
                $planType = $pendingUser->plan_type;

                $map = [
                    'pro' => 2,
                    'premium' => 3,
                    'premium_perkamar' => 4,
                    'pro_perkamar' => 5
                ];

                if (isset($map[$planType])) {
                    $user->id_plans = $map[$planType];
                }


                $user->activateStatus(); // Ensure status is 'aktif' and roles are mapped
                $user->save();

                // 3. Create active Langganan record
                $langgananNames = [
                    'pro' => 'MEMBER PRO',
                    'premium' => 'MEMBER PREMIUM',
                    'pro_perkamar' => 'PER KAMAR PRO',
                    'premium_perkamar' => 'PER KAMAR PREMIUM'
                ];

                if (isset($langgananNames[$planType])) {
                    $jenis = \App\Models\JenisLangganan::where('nama', $langgananNames[$planType])->first();
                    if ($jenis) {
                        \App\Models\Langganan::create([
                            'id_user' => $user->id,
                            'id_langganan' => $jenis->id,
                            'jumlah_kamar' => $pendingUser->jumlah_kamar ?? 0,
                            'status' => 'active',
                            'tanggal_pembayaran' => now('Asia/Jakarta'),
                            'jatuh_tempo' => now('Asia/Jakarta')->addDays(28),
                        ]);
                    }
                }

                // 4. Automatically create a default Kos record
                \App\Models\Kos::create([
                    'id_user' => $user->id,
                    'nama_kos' => 'Kos Baru ' . $user->name,
                    'alamat' => $pendingUser->alamat ?? 'Lokasi belum ditentukan',
                    'kode_kos' => rand(1000, 9999), // Generate a random 4-digit code
                    'is_kode_kos_edited' => false,
                ]);
            }

            // 5. Delete from staging
            $pendingUser->delete();

            return back()->with('success', 'Akun ' . $user->name . ' berhasil diverifikasi dan diaktifkan!');
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

    public function verifyPacket(Langganan $subscription)
    {
        $subscription->update([
            'status' => 'active',
            'tanggal_pembayaran' => now('Asia/Jakarta'),
            'jatuh_tempo' => now('Asia/Jakarta')->addDays(28),
        ]);

        // Auto-reactivate user if packet is verified
        if ($subscription->user) {
            $subscription->user->activateStatus();
        }

        return back()->with('success', 'Paket member berhasil diverifikasi!');
    }

    public function rejectPacket(Langganan $subscription)
    {
        $subscription->update([
            'status' => 'rejected',
        ]);

        return back()->with('success', 'Transaksi paket ditolak!');
    }
}
