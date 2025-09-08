<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Get locale from session, user preference, or default
        $locale = Session::get('locale') ?? 
                  ($request->user()?->locale ?? 
                   $request->getPreferredLanguage() ?? 
                   config('app.locale'));

        // Set the application locale
        App::setLocale($locale);

        return $next($request);
    }
}
