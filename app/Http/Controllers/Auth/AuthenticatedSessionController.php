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
     * Display the login view.
     */
    public function create(): View
    {
        return view('pages.auth.signin', ['title' => 'Masuk']);
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        // 1. Try normal authentication first
        $user = \App\Models\User::where('email', $request->email)->first();

        if ($user) {
            $request->authenticate();
            // ... normal flow continues below ...
        } else {
            $pendingUser = \App\Models\PendingUser::where('email', $request->email)->first();
            if ($pendingUser && \Illuminate\Support\Facades\Hash::check($request->password, $pendingUser->password)) {
                // Securely store pending user info in session
                session(['pending_user_id' => $pendingUser->id]);

                if (in_array($pendingUser->status, ['pending', 'verified', 'konfirmasi'])) {
                    return redirect()->route('registration.pending');
                }

                if ($pendingUser->status === 'rejected') {
                    return redirect()->route('registration.rejected');
                }
            } else {
                // If it was a pending session but user is now in 'users' table or deleted
                $officialUser = \App\Models\User::where('email', $request->email)->first();
                if ($officialUser && \Illuminate\Support\Facades\Hash::check($request->password, $officialUser->password)) {
                    session()->forget('pending_user_id');
                }
            }

            // 3. Fallback to normal authenticate() which will handle rate limiting and throw error
            $request->authenticate();
        }

        $user = Auth::user();

        // Check if there are active sessions on OTHER devices
        $hasOtherSession = \Illuminate\Support\Facades\DB::table('sessions')
            ->where('user_id', $user->id)
            ->where('id', '!=', session()->getId())
            ->exists();

        if ($hasOtherSession) {
            // Temporarily log them out and save ID to session for the OTP Selection page
            Auth::guard('web')->logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            session(['pending_otp_user_id' => $user->id]);

            return redirect()->route('otp.select');
        }

        $request->session()->regenerate();

        if ($user->hasRole('superadmin')) {
            $route = 'superadmin.dashboard';
        } elseif ($user->hasRole('admin') || $user->hasRole('nonaktif')) {
            $route = 'admin.dashboard';
        } elseif ($user->hasRole(['users', 'user'])) {
            $route = 'user.dashboard';
        } elseif ($user->hasRole('member')) {
            $route = 'member.dashboard';
        } else {
            $route = 'home';
        }

        return redirect()->route($route)->with('success_login', 'Berhasil Login!');
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): \Illuminate\Http\RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
