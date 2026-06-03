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
use App\Models\PaymentRecord;
use App\Models\StudentFee;
use App\Models\StudentAttendance;
use App\Models\TeacherAttendance;
use App\Models\Notification;
use App\Models\LessonPlan;

class AdminController extends Controller
{
    public function dashboard()
    {
        $session = [
            'academic_year' => (int) date('Y'),
            'semester' => (int) (date('n') <= 6 ? 1 : 2),
        ];
        
        // REAL-TIME DATA - NO MOCK DATA
        try {
            $studentCount = Student::count();
            $teacherCount = Teacher::count();
            $classCount = ClassRoom::count();
            $subjectCount = Subject::count();
            $examCount = ExamSchedule::count();
            
            // Debug logging for deployment
            \Log::info('Admin Dashboard Data', [
                'students' => $studentCount,
                'teachers' => $teacherCount,
                'classes' => $classCount,
                'subjects' => $subjectCount,
                'exams' => $examCount,
            ]);
        } catch (\Exception $e) {
            // If tables don't exist yet, use default values
            \Log::error('Admin Dashboard Data Error', ['error' => $e->getMessage()]);
            $studentCount = 0;
            $teacherCount = 0;
            $classCount = 0;
            $subjectCount = 0;
            $examCount = 0;
        }
        
        // Real-time financial data from both payment sources
        try {
            $feePaymentTotal = FeePayment::where('status', 'paid')->sum('amount_paid');
            $paymentRecordTotal = PaymentRecord::where('status', 'approved')->sum('amount');
            $totalFeePayments = $feePaymentTotal + $paymentRecordTotal;
        } catch (\Exception $e) {
            $totalFeePayments = 0;
        }
        
        // Get lesson plan statistics
        try {
            $lessonPlanCount = LessonPlan::count();
            $pendingLessonPlans = LessonPlan::whereIn('status', ['submitted', 'first_level_approved'])->count();
            $approvedLessonPlans = LessonPlan::where('status', 'second_level_approved')->count();
        } catch (\Exception $e) {
            $lessonPlanCount = 0;
            $pendingLessonPlans = 0;
            $approvedLessonPlans = 0;
        }

        // Get user statistics
        try {
            $totalUsers = User::count();
            $activeUsers = User::where('status', 'active')->count();
            $adminUsers = User::where('user_type', 'admin')->count();
            $financeUsers = User::where('user_type', 'finance')->count();
            $recentUsers = User::where('created_at', '>=', now()->subDays(30))->count();
        } catch (\Exception $e) {
            $totalUsers = 0;
            $activeUsers = 0;
            $adminUsers = 0;
            $financeUsers = 0;
            $recentUsers = 0;
        }

        $stats = [
            'total_students' => $studentCount,
            'total_teachers' => $teacherCount,
            'total_classes' => $classCount,
            'total_subjects' => $subjectCount,
            'total_exams' => $examCount,
            'total_fee_payments' => $totalFeePayments,
            'attendance_rate' => $this->getAttendanceRate(),
            'total_lesson_plans' => $lessonPlanCount,
            'pending_lesson_plans' => $pendingLessonPlans,
            'approved_lesson_plans' => $approvedLessonPlans,
            'total_users' => $totalUsers,
            'active_users' => $activeUsers,
            'admin_users' => $adminUsers,
            'finance_users' => $financeUsers,
            'recent_users' => $recentUsers,
        ];

        // Real-time fee collection data
        try {
            $collectedTodayFeePayments = FeePayment::whereDate('payment_date', today())
                ->where('status', 'paid')
                ->sum('amount_paid');
            $collectedTodayPaymentRecords = PaymentRecord::whereDate('created_at', today())
                ->where('status', 'approved')
                ->sum('amount');
            $collectedToday = $collectedTodayFeePayments + $collectedTodayPaymentRecords;
        } catch (\Exception $e) {
            $collectedToday = 0;
        }
        
        // Comprehensive financial statistics for admin dashboard
        $totalExpenses = 0; // TODO: Implement expense tracking
        $netProfit = $totalFeePayments - $totalExpenses;
        
        try {
            $feeStats = [
                'collected_today' => $collectedToday,
                'pending' => $this->getPendingPayments(),
                'pending_approvals' => PaymentRecord::where('status', 'pending')->count(),
                'total_revenue' => $totalFeePayments,
                'total_expenses' => $totalExpenses,
                'net_profit' => $netProfit,
                'monthly_revenue' => $collectedToday,
            ];
        } catch (\Exception $e) {
            $feeStats = [
                'collected_today' => 0,
                'pending' => 0,
                'pending_approvals' => 0,
                'total_revenue' => 0,
                'total_expenses' => 0,
                'net_profit' => 0,
                'monthly_revenue' => 0,
            ];
        }

        // Real-time attendance data for students
        try {
            $presentToday = StudentAttendance::whereDate('attendance_date', today())
                ->where('status', 'present')->count();
            $absentToday = StudentAttendance::whereDate('attendance_date', today())
                ->where('status', 'absent')->count();
        } catch (\Exception $e) {
            $presentToday = 0;
            $absentToday = 0;
        }
        try {
            $lateToday = StudentAttendance::whereDate('attendance_date', today())
                ->where('status', 'late')->count();
            $excusedToday = StudentAttendance::whereDate('attendance_date', today())
                ->where('status', 'excused')->count();
        } catch (\Exception $e) {
            $lateToday = 0;
            $excusedToday = 0;
        }
        
        // Recent student attendance records
        try {
            $recentStudentAttendance = StudentAttendance::with(['student.user', 'student.class'])
                ->whereDate('attendance_date', today())
                ->latest()
                ->limit(10)
                ->get();
        } catch (\Exception $e) {
            $recentStudentAttendance = collect();
        }
        
        // Teacher attendance data
        try {
            $teachersPresentToday = TeacherAttendance::whereDate('date', today())
                ->where('status', 'present')->count();
            $teachersAbsentToday = TeacherAttendance::whereDate('date', today())
                ->where('status', 'absent')->count();
            $teachersLateToday = TeacherAttendance::whereDate('date', today())
                ->where('status', 'late')->count();
        } catch (\Exception $e) {
            $teachersPresentToday = 0;
            $teachersAbsentToday = 0;
            $teachersLateToday = 0;
        }
        
        // Recent teacher attendance records
        try {
            $recentTeacherAttendance = TeacherAttendance::with(['teacher.user'])
                ->whereDate('date', today())
                ->latest()
                ->limit(5)
                ->get();
        } catch (\Exception $e) {
            $recentTeacherAttendance = collect();
        }
        
        $attendanceStats = [
            'students' => [
                'present' => $presentToday,
                'absent' => $absentToday,
                'late' => $lateToday,
                'excused' => $excusedToday,
                'total' => $presentToday + $absentToday + $lateToday + $excusedToday,
            ],
            'teachers' => [
                'present' => $teachersPresentToday,
                'absent' => $teachersAbsentToday,
                'late' => $teachersLateToday,
                'total' => $teachersPresentToday + $teachersAbsentToday + $teachersLateToday,
            ],
            // Keep backward compatibility
            'present' => $presentToday,
            'absent' => $absentToday,
            'late' => $lateToday,
            'total' => $presentToday + $absentToday + $lateToday + $excusedToday,
        ];

        // Real-time recent activities from notifications
        $recentActivities = $this->getRecentActivities();
        
        // Real-time upcoming exams
        try {
            $upcoming_exams = ExamSchedule::with(['examType', 'subject'])
                ->where('exam_date', '>=', now())
                ->orderBy('exam_date')
                ->limit(5)
                ->get();
        } catch (\Exception $e) {
            $upcoming_exams = collect();
        }

        // Always pass the authenticated user to the view
        $user = auth()->user();
        
        return view('dashboard.admin', compact(
            'stats', 'feeStats', 'attendanceStats', 'recentActivities', 'upcoming_exams',
            'recentStudentAttendance', 'recentTeacherAttendance', 'user'
        ) + ['session' => $session]);
    }

    private function getAttendanceRate()
    {
        try {
            $total_attendance = StudentAttendance::count();
            $present_attendance = StudentAttendance::where('status', 'present')->count();
            
            if ($total_attendance == 0) {
                return 0;
            }
            
            return round(($present_attendance / $total_attendance) * 100, 2);
        } catch (\Exception $e) {
            return 0;
        }
    }

    private function getPendingPayments()
    {
        try {
            // Real-time pending payments calculation using StudentFee balances
            return StudentFee::where('status', '!=', 'paid')->sum('balance');
        } catch (\Exception $e) {
            return 0;
        }
    }

    private function getRecentActivities()
    {
        try {
            $currentUserId = auth()->id();
            
            // Real-time activities from notifications and system events for the current user
            $recentNotifications = Notification::where('user_id', $currentUserId)
                ->latest()
                ->limit(3)
                ->get()
                ->map(function($notification) {
                    return [
                        'description' => $notification->title,
                        'created_at' => $notification->created_at,
                        'type' => $notification->type,
                    ];
                });
            
            // Add recent system activities
            $recentStudents = Student::latest()->limit(1)->get()->map(function($student) {
                return [
                    'description' => 'New student registered: ' . $student->user->name,
                    'created_at' => $student->created_at,
                    'type' => 'student_registration',
                ];
            });
            
            $recentExams = ExamSchedule::latest()->limit(1)->get()->map(function($exam) {
                return [
                    'description' => 'New exam scheduled: ' . ($exam->subject->name ?? 'Unknown Subject'),
                    'created_at' => $exam->created_at,
                    'type' => 'exam_scheduled',
                ];
            });
            
            $recentPayments = PaymentRecord::where('status', 'approved')
                ->latest()
                ->limit(1)
                ->get()
                ->map(function($payment) {
                    return [
                        'description' => 'Payment approved: $' . number_format($payment->amount, 2) . ' from ' . $payment->student->user->name,
                        'created_at' => $payment->approved_at ?? $payment->created_at,
                        'type' => 'payment_approved',
                    ];
                });
            
            // Combine all activities and sort by date
            return $recentNotifications
                ->concat($recentStudents)
                ->concat($recentExams)
                ->concat($recentPayments)
                ->sortByDesc('created_at')
                ->take(5)
                ->values();
                
        } catch (\Exception $e) {
            // Fallback to empty collection if there's an error
            return collect();
        }
    }

}
