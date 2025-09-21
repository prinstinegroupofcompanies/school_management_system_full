<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\URL;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Bridge INSTALL_TOKEN env to config for use at runtime
        config(['app.install_token' => env('INSTALL_TOKEN')]);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Register Blade components
        Blade::component('app-layout', \App\View\Components\AppLayout::class);

        // Register model observers
        \App\Models\Student::observe(\App\Observers\StudentObserver::class);

        if (config('app.env') === 'production') {
            URL::forceScheme('https');
        }
    }
}
