<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('pages.auth.signup', ['title' => 'Pendaftaran Akun']);
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'nik' => ['required', 'numeric', 'unique:users,nik'],
            'nomor_wa' => ['required', 'numeric', 'unique:users,nomor_wa'],
            'tanggal_lahir' => ['required', 'date'],
            'alamat' => ['required', 'string'],
            'id_plans' => ['required', 'integer', 'in:1,2,3,4,5,6'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:' . User::class],
            // Removed 'confirmed' because signup page doesn't have password_confirmation input
            'password' => ['required', Rules\Password::defaults()],
        ]);

        $user = User::create([
            'name' => $request->name,
            'nik' => $request->nik,
            'nomor_wa' => $request->nomor_wa,
            'tanggal_lahir' => $request->tanggal_lahir,
            'alamat' => $request->alamat,
            'id_plans' => $request->id_plans,
            'email' => $request->email,
            'password' => Hash::make($request->password), // Password is encrypted here
        ]);

        // Assign Roles based on dropdown (id_plans)
        if ($request->id_plans == 1) {
            $user->assignRole('users');
        } else {
            $user->assignRole('admin');
        }

        event(new Registered($user));

        // Membuang user kembali ke halaman login dengan menyertakan session pemberitahuan sukses
        return redirect()->route('login')->with('success', 'Registrasi berhasil! Silakan login masuk ke akun Anda.');
    }
}
