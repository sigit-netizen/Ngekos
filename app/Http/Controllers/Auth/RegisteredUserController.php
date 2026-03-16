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
            'id_plans' => ['required', 'integer', 'in:1,2'], // 1: Anak Kos, 2: Pemilik Kos
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:users,email', 'unique:pending_users,email'],
            'password' => ['required', Rules\Password::defaults()],
            // Optional fields (for later completion)
            'nik' => ['nullable', 'numeric', 'unique:users,nik', 'unique:pending_users,nik'],
            'nomor_wa' => ['nullable', 'numeric', 'unique:users,nomor_wa', 'unique:pending_users,nomor_wa'],
            'tanggal_lahir' => ['nullable', 'date'],
            'alamat' => ['nullable', 'string'],
        ]);

        \App\Models\PendingUser::create([
            'name' => $request->name,
            'id_plans' => $request->id_plans,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'status' => 'pending',
            // Other fields remain null/default
        ]);

        return redirect()->route('login')->with('success', 'Pendaftaran berhasil! Silahkan Login menggunakan akun yang telah anda daftarkan.');
    }
}
