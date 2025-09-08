<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class FinanceMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();
        if (!$user || !in_array($user->user_type, ['admin','finance'])) {
            abort(403, 'Access denied. Admin or Finance privileges required.');
        }
        return $next($request);
    }
}


