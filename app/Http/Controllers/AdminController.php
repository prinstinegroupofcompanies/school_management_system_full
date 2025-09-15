<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Student;
use App\Models\Teacher;
use App\Models\ClassRoom;
use App\Models\Subject;
use App\Models\ExamSchedule;
use App\Models\FeePayment;
use App\Models\StudentAttendance;

class AdminController extends Controller
{
    public function dashboard()
    {
        $session = [
            'academic_year' => (int) date('Y'),
            'semester' => (int) (date('n') <= 6 ? 1 : 2),
        ];
        $stats = [
            'total_students' => $this->safeCount(Student::class),
            'total_teachers' => $this->safeCount(Teacher::class),
            'total_classes' => $this->safeCount(ClassRoom::class),
            'total_subjects' => $this->safeCount(Subject::class),
            'total_exams' => $this->safeCount(ExamSchedule::class),
            'total_fee_payments' => $this->safeSum(FeePayment::class, 'amount'),
            'attendance_rate' => $this->getAttendanceRate(),
        ];

        $feeStats = [
            'collected_today' => $this->safeQuery(function() {
                return FeePayment::whereDate('created_at', today())->sum('amount');
            }),
            'pending' => $this->getPendingPayments(),
        ];

        $attendanceStats = [
            'present' => $this->safeQuery(function() {
                return StudentAttendance::whereDate('date', today())->where('status', 'present')->count();
            }),
            'absent' => $this->safeQuery(function() {
                return StudentAttendance::whereDate('date', today())->where('status', 'absent')->count();
            }),
            'late' => $this->safeQuery(function() {
                return StudentAttendance::whereDate('date', today())->where('status', 'late')->count();
            }),
            'total' => $this->safeQuery(function() {
                return StudentAttendance::whereDate('date', today())->count();
            }),
        ];

        $recentActivities = $this->getRecentActivities();
        $upcoming_exams = $this->safeQuery(function() {
            return ExamSchedule::with('examType', 'subject')
                ->where('exam_date', '>=', now())
                ->orderBy('exam_date')
                ->limit(5)
                ->get();
        }) ?: collect();

        return view('dashboard.admin', compact('stats', 'feeStats', 'attendanceStats', 'recentActivities', 'upcoming_exams') + ['session' => $session]);
    }

    private function getAttendanceRate()
    {
        $total_attendance = StudentAttendance::count();
        $present_attendance = StudentAttendance::where('status', 'present')->count();
        
        if ($total_attendance == 0) {
            return 0;
        }
        
        return round(($present_attendance / $total_attendance) * 100, 2);
    }

    private function getPendingPayments()
    {
        // Calculate pending payments based on fee structures and existing payments
        $total_students = Student::count();
        $total_paid = FeePayment::where('status', 'paid')->sum('amount');
        
        // This is a simplified calculation - in reality, you'd check against fee structures
        return $total_students * 1000 - $total_paid; // Assuming 1000 LRD per student
    }

    private function getRecentActivities()
    {
        // This would typically come from an activity log
        return collect([
            [
                'description' => 'New student registered',
                'created_at' => now()->subMinutes(5),
            ],
            [
                'description' => 'New exam scheduled',
                'created_at' => now()->subMinutes(15),
            ],
            [
                'description' => 'Fee payment received',
                'created_at' => now()->subMinutes(30),
            ],
        ]);
    }

    /**
     * Safely count records from a model, returning 0 if table doesn't exist
     */
    private function safeCount($model)
    {
        try {
            return $model::count();
        } catch (\Exception $e) {
            return 0;
        }
    }

    /**
     * Safely sum a column from a model, returning 0 if table doesn't exist
     */
    private function safeSum($model, $column)
    {
        try {
            return $model::sum($column) ?? 0;
        } catch (\Exception $e) {
            return 0;
        }
    }

    /**
     * Safely execute a database query, returning 0 if it fails
     */
    private function safeQuery($callback)
    {
        try {
            return $callback();
        } catch (\Exception $e) {
            return 0;
        }
    }
}
