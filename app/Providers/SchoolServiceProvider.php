<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\View;
use App\Models\User;
use App\Models\Student;
use App\Models\Teacher;
use App\Models\Staff;

class SchoolServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        // Register school configuration
        $this->app->singleton('school.config', function ($app) {
            return config('school');
        });
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Share common data with all views
        View::composer('*', function ($view) {
            $view->with('schoolConfig', config('school'));
            
            // Share current user data if authenticated
            if (auth()->check()) {
                $user = auth()->user();
                $view->with('currentUser', $user);
                
                // Share user-specific data with safe database access
                try {
                    if ($user->user_type === 'student') {
                        $student = $user->student;
                        $view->with('currentStudent', $student);
                    } elseif ($user->user_type === 'teacher') {
                        $teacher = $user->teacher;
                        $view->with('currentTeacher', $teacher);
                    } elseif ($user->user_type === 'staff') {
                        $staff = $user->staff;
                        $view->with('currentStaff', $staff);
                    }
                } catch (\Exception $e) {
                    // Tables don't exist yet - set safe defaults
                    $view->with('currentStudent', null);
                    $view->with('currentTeacher', null);
                    $view->with('currentStaff', null);
                }
            }
        });

        // Register custom Blade directives
        Blade::directive('role', function ($expression) {
            return "<?php if(auth()->check() && auth()->user()->user_type === {$expression}): ?>";
        });

        Blade::directive('endrole', function () {
            return "<?php endif; ?>";
        });

        Blade::directive('permission', function ($expression) {
            return "<?php if(auth()->check() && auth()->user()->user_type === 'admin'): ?>";
        });

        Blade::directive('endpermission', function () {
            return "<?php endif; ?>";
        });

        Blade::directive('school', function ($expression) {
            return "<?php echo config('school.' . {$expression}); ?>";
        });
    }
}
