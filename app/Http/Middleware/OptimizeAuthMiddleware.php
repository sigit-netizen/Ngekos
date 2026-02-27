<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class OptimizeAuthMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (\Illuminate\Support\Facades\Auth::check()) {
            // Eager load roles explicitly for this request cycle
            // Spatie caches the list of roles, but checking user->hasRole might hit model_has_roles if not pre-loaded
            $request->user()->loadMissing('roles');
        }

        return $next($request);
    }
}
