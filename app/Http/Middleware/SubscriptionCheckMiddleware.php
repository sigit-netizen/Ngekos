<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
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

        // Simple validation: Ensure user has a valid plan ID
        if (!$user->id_plans) {
            if ($request->is('admin*') || $request->is('user*')) {
                return redirect()->route('home')->with('error', 'Akun Anda tidak memiliki paket aktif. Silakan hubungi admin.');
            }
        }

        return $next($request);
    }
}
