<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * Main dashboard index - redirects users to their appropriate dashboards
     */
    public function index()
    {
        $user = auth()->user();
        
        // Redirect based on user type to their specific dashboard controllers
        switch ($user->user_type) {
            case 'admin':
                return redirect()->route('admin.dashboard');
            case 'teacher':
                return redirect()->route('teacher.dashboard');
            case 'student':
                return redirect()->route('student.dashboard');
            case 'finance':
                return redirect()->route('finance.dashboard');
            default:
                return $this->defaultDashboard();
        }
    }

    /**
     * Default dashboard for unknown user types
     */
    public function defaultDashboard()
    {
        $data = [
            'stats' => [
                'total_students' => 0,
                'total_teachers' => 0,
                'total_classes' => 0,
                'total_subjects' => 0,
            ],
            'attendanceStats' => [
                'present' => 0,
                'absent' => 0,
                'late' => 0,
                'total' => 0,
            ],
            'upcomingExams' => collect(),
            'feeStats' => [
                'collected_today' => 0,
                'pending' => 0,
                'overdue' => 0,
            ],
            'recentActivities' => collect(),
        ];

        return view('dashboard', $data);
    }
}