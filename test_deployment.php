<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== Deployment Test ===\n\n";

try {
    // Test database connection
    echo "1. Testing database connection...\n";
    DB::connection()->getPdo();
    echo "✅ Database connection successful\n\n";
    
    // Check if users table exists and has data
    echo "2. Checking users table...\n";
    if (Schema::hasTable('users')) {
        $userCount = DB::table('users')->count();
        echo "✅ Users table exists with $userCount users\n";
        
        if ($userCount > 0) {
            $users = DB::table('users')->select('email', 'user_type')->get();
            foreach ($users as $user) {
                echo "   - {$user->email} ({$user->user_type})\n";
            }
        } else {
            echo "❌ No users found in database\n";
        }
    } else {
        echo "❌ Users table does not exist\n";
    }
    echo "\n";
    
    // Check key tables
    echo "3. Checking key tables...\n";
    $tables = ['students', 'teachers', 'class_rooms', 'subjects', 'scholarships'];
    
    foreach ($tables as $table) {
        if (Schema::hasTable($table)) {
            $count = DB::table($table)->count();
            echo "✅ $table exists ($count records)\n";
        } else {
            echo "❌ $table does not exist\n";
        }
    }
    echo "\n";
    
    // Test authentication
    echo "4. Testing authentication...\n";
    $testUser = DB::table('users')->where('email', 'admin@school.com')->first();
    if ($testUser) {
        echo "✅ Test user found: {$testUser->email}\n";
        echo "   Password hash: " . substr($testUser->password, 0, 20) . "...\n";
    } else {
        echo "❌ Test user not found\n";
    }
    echo "\n";
    
    // Test routes
    echo "5. Testing route resolution...\n";
    $routes = [
        'dashboard' => '/dashboard',
        'admin.dashboard' => '/admin/dashboard',
        'teacher.dashboard' => '/teacher/dashboard',
        'student.dashboard' => '/student/dashboard',
        'finance.dashboard' => '/finance/dashboard',
    ];
    
    foreach ($routes as $name => $path) {
        try {
            $route = Route::getRoutes()->getByName($name);
            if ($route) {
                echo "✅ Route '$name' exists: {$route->uri()}\n";
            } else {
                echo "❌ Route '$name' not found\n";
            }
        } catch (Exception $e) {
            echo "❌ Route '$name' error: " . $e->getMessage() . "\n";
        }
    }
    
    echo "\n=== Test Complete ===\n";
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}
