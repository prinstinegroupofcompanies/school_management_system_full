<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== Dashboard Data Debug ===\n\n";

try {
    echo "1. Checking database connection and tables...\n";
    
    $driver = config('database.default');
    echo "Database driver: $driver\n";
    
    try {
        if ($driver === 'pgsql') {
            $tables = DB::select("SELECT tablename FROM pg_tables WHERE schemaname = 'public'");
            echo "Total tables: " . count($tables) . "\n";
            foreach ($tables as $table) {
                echo "   - " . $table->tablename . "\n";
            }
        } else {
            // SQLite
            $tables = DB::select("SELECT name FROM sqlite_master WHERE type='table'");
            echo "Total tables: " . count($tables) . "\n";
            foreach ($tables as $table) {
                echo "   - " . $table->name . "\n";
            }
        }
    } catch (Exception $e) {
        echo "❌ Table listing error: " . $e->getMessage() . "\n";
    }
    echo "\n";
    
    echo "2. Checking data counts...\n";
    
    // Check users
    try {
        $userCount = \App\Models\User::count();
        echo "Users: $userCount\n";
        
        $users = \App\Models\User::all(['email', 'user_type', 'status']);
        foreach ($users as $user) {
            echo "   - {$user->email} ({$user->user_type}) - {$user->status}\n";
        }
    } catch (Exception $e) {
        echo "Users table error: " . $e->getMessage() . "\n";
    }
    
    // Check students
    try {
        $studentCount = \App\Models\Student::count();
        echo "Students: $studentCount\n";
    } catch (Exception $e) {
        echo "Students table error: " . $e->getMessage() . "\n";
    }
    
    // Check teachers
    try {
        $teacherCount = \App\Models\Teacher::count();
        echo "Teachers: $teacherCount\n";
    } catch (Exception $e) {
        echo "Teachers table error: " . $e->getMessage() . "\n";
    }
    
    // Check classes
    try {
        $classCount = \App\Models\ClassRoom::count();
        echo "Classes: $classCount\n";
    } catch (Exception $e) {
        echo "Classes table error: " . $e->getMessage() . "\n";
    }
    
    // Check subjects
    try {
        $subjectCount = \App\Models\Subject::count();
        echo "Subjects: $subjectCount\n";
    } catch (Exception $e) {
        echo "Subjects table error: " . $e->getMessage() . "\n";
    }
    
    // Check fee structures
    try {
        $feeCount = \App\Models\FeeStructure::count();
        echo "Fee Structures: $feeCount\n";
    } catch (Exception $e) {
        echo "Fee Structures table error: " . $e->getMessage() . "\n";
    }
    
    // Check fee payments
    try {
        $paymentCount = \App\Models\FeePayment::count();
        echo "Fee Payments: $paymentCount\n";
    } catch (Exception $e) {
        echo "Fee Payments table error: " . $e->getMessage() . "\n";
    }
    
    // Check attendance
    try {
        $attendanceCount = \App\Models\StudentAttendance::count();
        echo "Student Attendance: $attendanceCount\n";
    } catch (Exception $e) {
        echo "Student Attendance table error: " . $e->getMessage() . "\n";
    }
    
    // Check scholarships
    try {
        $scholarshipCount = \App\Models\Scholarship::count();
        echo "Scholarships: $scholarshipCount\n";
    } catch (Exception $e) {
        echo "Scholarships table error: " . $e->getMessage() . "\n";
    }
    
    echo "\n=== Debug Complete ===\n";
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}
