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
            'id_plans' => ['required', 'integer', 'in:1,2'], // 1: Anak Kos, 2: Pemilik Kos
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:' . User::class],
            'password' => ['required', Rules\Password::defaults()],
            'plan_type' => ['nullable', 'string', 'required_if:id_plans,2'],
            'package_type' => ['nullable', 'string', 'required_if:plan_type,premium'],
            'jumlah_kamar' => ['nullable', 'integer', 'min:1'],
        ]);

        \App\Models\PendingUser::create([
            'name' => $request->name,
            'nik' => $request->nik,
            'nomor_wa' => $request->nomor_wa,
            'tanggal_lahir' => $request->tanggal_lahir,
            'alamat' => $request->alamat,
            'id_plans' => $request->id_plans,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'plan_type' => $request->plan_type,
            'package_type' => $request->package_type,
            'jumlah_kamar' => $request->jumlah_kamar ?? 0,
            'status' => 'pending',
        ]);

        // Registration event might not be needed yet as they are pending
        // but keeping it for potential listeners
        // event(new Registered($user));

        // Membuang user kembali ke halaman login dengan menyertakan session pemberitahuan sukses
        return redirect()->route('login')->with('success', 'Pendaftaran berhasil! Akun Anda sedang dalam antrian verifikasi admin. Mohon tunggu konfirmasi selanjutnya.');
    }
}
