<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  mixed ...$roles
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function handle($request, Closure $next, ...$roles)
    {
        if (!Auth::check()) {
            abort(403, 'Unauthorized - Not authenticated');
        }

        $user = Auth::user();
        
        // First check Spatie roles (if user has roles assigned)
        if (method_exists($user, 'hasRole') && $user->roles()->count() > 0) {
            $hasRole = false;
            foreach ($roles as $role) {
                if ($user->hasRole($role)) {
                    $hasRole = true;
                    break;
                }
            }
            if (!$hasRole) {
                abort(403, 'Unauthorized - Insufficient role permissions');
            }
        } else {
            // Fallback to user_type if no Spatie roles assigned
            if (!in_array($user->user_type, $roles)) {
                abort(403, 'Unauthorized - Insufficient user type permissions');
            }
        }

        return $next($request);
    }
}