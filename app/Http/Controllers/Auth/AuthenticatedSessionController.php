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
        return view('pages.auth.signin', ['title' => 'Sign In']);
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $request->session()->regenerate();

        $user = Auth::user();

        if ($user->hasRole('superadmin')) {
            return redirect()->route('superadmin.dashboard')->with('success_login', 'Berhasil Login!');
        } elseif ($user->hasRole('admin')) {
            return redirect()->route('admin.dashboard')->with('success_login', 'Berhasil Login!');
        } elseif ($user->hasRole('member')) {
            return redirect()->route('member.dashboard')->with('success_login', 'Berhasil Login!');
        } elseif ($user->hasRole('users')) {
            return redirect()->route('user.dashboard')->with('success_login', 'Berhasil Login!');
        }

        return redirect()->route('home')->with('success_login', 'Berhasil Login!');
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
