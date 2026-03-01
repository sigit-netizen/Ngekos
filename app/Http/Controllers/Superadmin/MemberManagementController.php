<?php

namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;

class MemberManagementController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->get('search');
        $paket = $request->get('paket');

        // Get users with 'admin' role (The Owners) and 'nonaktif' role
        $query = User::role(['admin', 'nonaktif']);

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('nik', 'like', "%{$search}%")
                    ->orWhere('nomor_wa', 'like', "%{$search}%");
            });
        }

        if ($paket) {
            $query->where('id_plans', $paket);
        }

        $members = $query->with('statusUser')->latest()->paginate(10);

        return view('superadmin.data_member', [
            'title' => 'Manajemen Member (Pemilik Kos)',
            'members' => $members,
            'role' => 'superadmin',
            'search' => $search,
            'paket' => $paket,
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:users'],
            'password' => ['required', Rules\Password::defaults()],
            'nik' => ['required', 'numeric', 'unique:users'],
            'nomor_wa' => ['required', 'numeric', 'unique:users'],
            'id_plans' => ['required', 'integer', 'in:2,3,4,5'],
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'nik' => $request->nik,
            'nomor_wa' => $request->nomor_wa,
            'id_plans' => $request->id_plans,
            'tanggal_lahir' => now(), // Placeholder or add to form
            'alamat' => '-', // Placeholder or add to form
        ]);

        $user->assignRole('admin');

        // Mapping plan role
        $roleMap = [
            2 => 'pro',
            3 => 'premium',
            4 => 'per_kamar_premium',
            5 => 'per_kamar_pro'
        ];
        if (isset($roleMap[$request->id_plans])) {
            $user->assignRole($roleMap[$request->id_plans]);
        }

        return back()->with('success', 'Member berhasil ditambahkan!');
    }

    public function update(Request $request, User $user)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,' . $user->id],
            'nik' => ['required', 'numeric', 'unique:users,nik,' . $user->id],
            'nomor_wa' => ['required', 'numeric', 'unique:users,nomor_wa,' . $user->id],
            'id_plans' => ['required', 'integer', 'in:2,3,4,5'],
        ]);

        $user->update($request->only(['name', 'email', 'nik', 'nomor_wa', 'id_plans']));

        if ($request->password) {
            $user->update(['password' => Hash::make($request->password)]);
        }

        // Re-sync roles if plan changed
        $user->syncRoles(['admin']);
        $roleMap = [
            2 => 'pro',
            3 => 'premium',
            4 => 'per_kamar_premium',
            5 => 'per_kamar_pro'
        ];
        if (isset($roleMap[$request->id_plans])) {
            $user->assignRole($roleMap[$request->id_plans]);
        }

        return back()->with('success', 'Data member berhasil diperbarui!');
    }

    public function toggleStatus(User $user)
    {
        // Get current status from the statusUser relationship (default to 'aktif' if no record exists)
        $currentStatus = $user->statusUser ? $user->statusUser->status : 'aktif';

        if ($currentStatus === 'inactive') {
            // Activate using model helper (updates DB and restores roles)
            $user->activateStatus();
            $message = "Member {$user->name} berhasil diaktifkan kembali!";
        } else {
            // Deactivate using model helper
            $user->deactivateStatus();
            $message = "Member {$user->name} telah dinonaktifkan!";
        }

        return back()->with('success', $message);
    }

    public function destroy(User $user)
    {
        $user->delete();
        return back()->with('success', 'Member berhasil dihapus!');
    }
}
