<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;

class VerifyCsrfToken extends Middleware
{
    /**
     * The URIs that should be excluded from CSRF verification.
     *
     * @var array<int, string>
     */
    protected $except = [
        // Temporarily exclude login route to test
        'login',
        // Add specific routes that should be excluded from CSRF verification
        // 'api/*', // Uncomment if you have API routes that need to be excluded
    ];

}
