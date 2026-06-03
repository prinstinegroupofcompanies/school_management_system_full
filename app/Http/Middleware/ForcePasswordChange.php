<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ForcePasswordChange
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = auth()->user();

        if ($user && $user->must_change_password) {
            // Allow access to password change route and logout
            $allowedRoutes = [
                'password.change',
                'password.update',
                'logout',
                'profile.password.change',
                'profile.password.update',
            ];

            $routeName = $request->route() ? $request->route()->getName() : null;

            if ($routeName && !in_array($routeName, $allowedRoutes)) {
                return redirect()->route('password.change')
                    ->with('warning', 'Please change your password before continuing.');
            }
        }

        return $next($request);
    }
}
