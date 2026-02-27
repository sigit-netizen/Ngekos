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

        // Only get users with 'users' role (Anak Kos)
        $query = User::role('users');

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('nik', 'like', "%{$search}%")
                    ->orWhere('nomor_wa', 'like', "%{$search}%");
            });
        }

        $users = $query->get();

        return view('superadmin.data_user', [
            'title' => 'Manajemen User (Anak Kos)',
            'users' => $users,
            'role' => 'superadmin',
            'search' => $search
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
