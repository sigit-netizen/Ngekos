<?php

namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;

class UserManagementController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->get('search');
        $type = $request->get('type', 'all');

        // Exclude admin and superadmin roles
        $query = User::whereDoesntHave('roles', function ($q) {
            $q->whereIn('name', ['admin', 'superadmin']);
        });

        // Filter by type
        if ($type === 'penyewa') {
            $query->whereNotNull('id_kos')->whereNotNull('id_kamar');
        } elseif ($type === 'user') {
            $query->where(function ($q) {
                $q->whereNull('id_kos')->orWhereNull('id_kamar');
            });
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'ilike', "%{$search}%")
                    ->orWhere('email', 'ilike', "%{$search}%")
                    ->orWhere('nik', 'ilike', "%{$search}%")
                    ->orWhere('nomor_wa', 'ilike', "%{$search}%");
            });
        }

        $users = $query->latest()->paginate(10);

        return view('superadmin.data_user', [
            'title' => 'Manajemen User',
            'users' => $users,
            'role' => 'superadmin',
            'search' => $search,
            'currentType' => $type
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
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'nik' => $request->nik,
            'nomor_wa' => $request->nomor_wa,
            'id_plans' => 1, // Default for Anak Kos
            'tanggal_lahir' => now(), // Default placeholder
            'alamat' => '-', // Default placeholder
        ]);

        $user->assignRole('users');

        return back()->with('success', 'User berhasil ditambahkan!');
    }

    public function update(Request $request, User $user)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,' . $user->id],
            'nik' => ['required', 'numeric', 'unique:users,nik,' . $user->id],
            'nomor_wa' => ['required', 'numeric', 'unique:users,nomor_wa,' . $user->id],
        ]);

        $user->update($request->only(['name', 'email', 'nik', 'nomor_wa']));

        if ($request->password) {
            $user->update(['password' => Hash::make($request->password)]);
        }

        return back()->with('success', 'Data user berhasil diperbarui!');
    }

    public function destroy(User $user)
    {
        $user->delete();
        return back()->with('success', 'User berhasil dihapus!');
    }
}
