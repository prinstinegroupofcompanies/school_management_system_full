<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Check if user is authenticated
        if (!auth()->check()) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Unauthenticated.'], 401);
            }
            
            return redirect()->route('login')->with('error', 'Please log in to access this page.');
        }

        // School admin only: super admin has separate dashboard and must not use admin routes
        $user = auth()->user();
        if ($user->isSuperAdmin()) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Use Super Admin dashboard for system-wide actions.'], 403);
            }
            return redirect()->route('super_admin.dashboard')->with('info', 'Use Super Admin dashboard.');
        }

        $isAdmin = false;
        if (method_exists($user, 'hasRole')) {
            $isAdmin = $user->hasRole('admin') || $user->hasRole('vpi') || $user->hasRole('vpa');
        }
        if (!$isAdmin && $user->user_type !== 'admin') {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Access denied. Admin privileges required.'], 403);
            }
            
            abort(403, 'Access denied. Admin privileges required.');
        }

        return $next($request);
    }
}