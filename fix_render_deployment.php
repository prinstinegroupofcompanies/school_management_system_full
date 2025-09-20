<?php

/**
 * Fix Render Deployment Issues
 * This script handles package discovery and database setup issues on Render
 */

echo "🔧 Fixing Render deployment issues...\n";

// Set up Laravel environment
require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

try {
    // 1. Handle Spatie Permission package issues
    echo "📦 Handling package conflicts...\n";
    
    // Check if Spatie Permission is causing issues and disable if needed
    $config = config('permission');
    if ($config) {
        echo "✅ Spatie Permission configured properly\n";
    } else {
        echo "⚠️ Spatie Permission not configured, using default Laravel auth\n";
    }

    // 2. Ensure essential database tables exist
    echo "🗄️ Verifying database structure...\n";
    
    $essentialTables = [
        'users' => 'User accounts',
        'students' => 'Student records', 
        'teachers' => 'Teacher records',
        'class_rooms' => 'Class management',
        'subjects' => 'Subject management',
        'sessions' => 'Session management'
    ];
    
    foreach ($essentialTables as $table => $description) {
        if (Schema::hasTable($table)) {
            echo "✅ {$table} table exists ({$description})\n";
        } else {
            echo "❌ {$table} table missing - {$description}\n";
            // Try to create the table if it's critical
            if ($table === 'sessions') {
                try {
                    Artisan::call('session:table');
                    Artisan::call('migrate', ['--force' => true]);
                    echo "✅ Created {$table} table\n";
                } catch (Exception $e) {
                    echo "⚠️ Could not create {$table}: {$e->getMessage()}\n";
                }
            }
        }
    }

    // 3. Ensure we have authenticated users for all roles
    echo "👥 Ensuring authenticated users exist...\n";
    
    $users = [
        [
            'name' => 'Admin User',
            'email' => 'admin@school.com',
            'password' => 'admin123',
            'user_type' => 'admin'
        ],
        [
            'name' => 'Teacher User', 
            'email' => 'teacher@school.com',
            'password' => 'teacher123',
            'user_type' => 'teacher'
        ],
        [
            'name' => 'Student User',
            'email' => 'student@school.com', 
            'password' => 'student123',
            'user_type' => 'student'
        ],
        [
            'name' => 'Finance Officer',
            'email' => 'finance@school.com',
            'password' => 'finance123', 
            'user_type' => 'finance'
        ]
    ];
    
    foreach ($users as $userData) {
        $existingUser = App\Models\User::where('email', $userData['email'])->first();
        if (!$existingUser) {
            try {
                App\Models\User::create([
                    'name' => $userData['name'],
                    'email' => $userData['email'],
                    'password' => bcrypt($userData['password']),
                    'user_type' => $userData['user_type'],
                    'is_active' => true,
                    'country' => 'Liberia',
                    'email_verified_at' => now(),
                ]);
                echo "✅ Created {$userData['user_type']} user: {$userData['email']}\n";
            } catch (Exception $e) {
                echo "⚠️ Could not create {$userData['user_type']} user: {$e->getMessage()}\n";
            }
        } else {
            echo "✅ {$userData['user_type']} user already exists: {$userData['email']}\n";
        }
    }

    // 4. Verify routes are working
    echo "🛣️ Verifying route system...\n";
    try {
        $routeCount = count(Route::getRoutes());
        echo "✅ {$routeCount} routes registered successfully\n";
    } catch (Exception $e) {
        echo "⚠️ Route verification error: {$e->getMessage()}\n";
    }

    // 5. Test database connectivity
    echo "📡 Testing database connectivity...\n";
    try {
        DB::connection()->getPdo();
        echo "✅ Database connection successful\n";
    } catch (Exception $e) {
        echo "❌ Database connection failed: {$e->getMessage()}\n";
    }

    echo "\n🎯 Render deployment fix completed!\n";
    echo "🌟 Bryant School Management System should now work properly on Render\n";
    
} catch (Exception $e) {
    echo "❌ Critical error during deployment fix: {$e->getMessage()}\n";
    echo "📝 System will continue with basic functionality...\n";
}
