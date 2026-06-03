<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->web(append: [
            \App\Http\Middleware\SetLocale::class,
            \App\Http\Middleware\CheckMaintenanceMode::class,
        ]);

        $middleware->alias([
            'auth' => \App\Http\Middleware\Authenticate::class,
            'admin' => \App\Http\Middleware\AdminMiddleware::class,
            'super_admin' => \App\Http\Middleware\SuperAdminMiddleware::class,
            'teacher' => \App\Http\Middleware\TeacherMiddleware::class,
            'finance' => \App\Http\Middleware\FinanceMiddleware::class,
            'student' => \App\Http\Middleware\StudentMiddleware::class,
            'parent' => \App\Http\Middleware\ParentMiddleware::class,
            'unpaid.restrict' => \App\Http\Middleware\RestrictUnpaid::class,
            'force.password.change' => \App\Http\Middleware\ForcePasswordChange::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        // Fix for Spatie Laravel Ignition error with class_implements
        $exceptions->render(function (\Throwable $e, $request) {
            if (str_contains($e->getMessage(), 'class_implements(): Class Spatie')) {
                // Log and return a simple error response instead of triggering Ignition
                \Log::error('Spatie Ignition Error: ' . $e->getMessage());
                return response()->view('errors.500', [
                    'message' => 'An error occurred. Please try again or contact support.',
                ], 500);
            }
        });
    })->create(); 