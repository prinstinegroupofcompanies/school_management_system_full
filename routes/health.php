<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\Student;
use App\Models\Teacher;
use App\Models\ClassRoom;

// Health check endpoint for deployment verification
Route::get('/health', function () {
    try {
        // Check database connection
        DB::connection()->getPdo();
        
        // Check if basic data exists
        $userCount = User::count();
        $studentCount = Student::count();
        $teacherCount = Teacher::count();
        $classCount = ClassRoom::count();
        
        return response()->json([
            'status' => 'healthy',
            'database' => 'connected',
            'data' => [
                'users' => $userCount,
                'students' => $studentCount,
                'teachers' => $teacherCount,
                'classes' => $classCount,
            ],
            'timestamp' => now()->toISOString()
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'status' => 'unhealthy',
            'database' => 'disconnected',
            'error' => $e->getMessage(),
            'timestamp' => now()->toISOString()
        ], 500);
    }
});

// Debug endpoint for testing data
Route::get('/debug', function () {
    try {
        $data = [
            'users' => User::all(['id', 'name', 'email', 'user_type']),
            'students' => Student::with('user')->get(['id', 'student_id', 'first_name', 'last_name']),
            'teachers' => Teacher::with('user')->get(['id', 'employee_id', 'qualification']),
            'classes' => ClassRoom::all(['id', 'name', 'description']),
        ];
        
        return response()->json($data);
    } catch (\Exception $e) {
        return response()->json(['error' => $e->getMessage()], 500);
    }
});
