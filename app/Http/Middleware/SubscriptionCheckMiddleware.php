<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class SubscriptionCheckMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (!$user) {
            return $next($request);
        }

        // Bypass check for Superadmin (id_plans 6)
        if ($user->hasRole('superadmin') || $user->id_plans == 6) {
            return $next($request);
        }

        // CRITICAL: Handle Pending/Rejected Status
        if ($user->status !== 'active') {
            // If REJECTED, logout and notify
            if ($user->status === 'rejected') {
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();
                return redirect()->route('login')->with('error', 'Akun Anda ditolak oleh admin. Silakan hubungi dukungan.');
            }

            // If PENDING, redirect to dedicated pending dashboard (if not already there)
            if ($user->status === 'pending' && !$request->is('pending')) {
                return redirect()->route('pending.dashboard');
            }
        }

        // Enforce active subscription for Admin/Member
        if ($user->hasRole('admin')) {
            $hasActiveSubscription = \App\Models\Langganan::where('id_user', $user->id)
                ->where('status', 'active')
                ->exists();

            if (!$hasActiveSubscription) {
                if ($request->is('admin*')) {
                    Auth::logout();
                    return redirect()->route('login')->with('error', 'Akun Anda sedang menunggu verifikasi admin. Silakan coba lagi nanti.');
                }
            }
        }

        // Simple validation: Ensure user has a valid plan ID (Existing logic for others)
        if (!$user->id_plans && $request->is('user*')) {
            return redirect()->route('home')->with('error', 'Akun Anda tidak memiliki paket aktif. Silakan hubungi admin.');
        }

        return $next($request);
    }
}
