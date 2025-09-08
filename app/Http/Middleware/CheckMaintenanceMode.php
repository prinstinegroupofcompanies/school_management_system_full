<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Symfony\Component\HttpFoundation\Response;

class CheckMaintenanceMode
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (App::isDownForMaintenance()) {
            // Check if user has bypass maintenance mode permission
            if ($request->user() && $request->user()->can('bypass maintenance mode')) {
                return $next($request);
            }
            
            // Return maintenance mode response
            return response()->view('errors.maintenance', [], 503);
        }

        return $next($request);
    }
}
