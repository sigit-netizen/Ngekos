<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Menampilkan halaman login.
     */
    public function create(): View
    {
        // Simpan kode_kos ke session jika user datang dari landing page
        if (request()->has('kode_kos')) {
            session(['intended_kos' => request('kode_kos')]);
        }

        return view('pages.auth.signin', ['title' => 'Masuk']);
    }

    /**
     * Menangani permintaan autentikasi yang masuk.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        // 1. Coba autentikasi normal terlebih dahulu
        $user = \App\Models\User::where('email', $request->email)->first();

        if ($user) {
            $request->authenticate();
            // ...logika login normal...
        } else {
            $pendingUser = \App\Models\PendingUser::where('email', $request->email)->first();
            if ($pendingUser && \Illuminate\Support\Facades\Hash::check($request->password, $pendingUser->password)) {
                // Simpan informasi user pending dengan aman di session
                session(['pending_user_id' => $pendingUser->id]);

                if (in_array($pendingUser->status, ['pending', 'verified', 'konfirmasi'])) {
                    return redirect()->route('registration.pending');
                }

                if ($pendingUser->status === 'rejected') {
                    return redirect()->route('registration.rejected');
                }
            } else {
                // Jika ini adalah sesi pending tetapi pengguna sekarang ada di tabel 'users' atau dihapus
                $officialUser = \App\Models\User::where('email', $request->email)->first();
                if ($officialUser && \Illuminate\Support\Facades\Hash::check($request->password, $officialUser->password)) {
                    session()->forget('pending_user_id');
                }
            }

            // 3. Kembali ke authenticate() normal yang akan menangani rate limiting dan menampilkan error
            $request->authenticate();
        }

        $user = Auth::user();

        // Periksa apakah ada sesi aktif di perangkat LAIN
        $hasOtherSession = \Illuminate\Support\Facades\DB::table('sessions')
            ->where('user_id', $user->id)
            ->where('id', '!=', session()->getId())
            ->exists();

        if ($hasOtherSession) {
            // Keluarkan mereka sementara dan simpan ID ke sesi untuk halaman Pemilihan OTP
            Auth::guard('web')->logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            session(['pending_otp_user_id' => $user->id]);

            return redirect()->route('otp.select');
        }

        // Ambil kode_kos sebelum session diregenerasi agar tidak hilang
        $intendedKos = session()->pull('intended_kos');

        $request->session()->regenerate();

        if ($user->hasRole('superadmin')) {
            $route = 'superadmin.dashboard';
        } elseif ($user->hasRole('admin') || $user->hasRole('nonaktif')) {
            $route = 'admin.dashboard';
        } elseif ($user->hasRole(['users', 'user'])) {
            // Cek apakah ada kos yang dipilih sebelum login
            if ($intendedKos) {
                return redirect()->route('user.dashboard', ['kode_kos' => $intendedKos])
                    ->with('success_login', 'Berhasil Login! Menampilkan kos pilihanmu.');
            }
            $route = 'user.dashboard';
        } elseif ($user->hasRole('member')) {
            $route = 'member.dashboard';
        } else {
            $route = 'home';
        }

        return redirect()->route($route)->with('success_login', 'Berhasil Login!');
    }

    /**
     * Hancurkan sesi yang terautentikasi.
     */
    public function destroy(Request $request): \Illuminate\Http\RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
