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
        } catch (\Exception $e) {
            // If tables don't exist yet, use default values
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
        
        $stats = [
            'total_students' => $studentCount,
            'total_teachers' => $teacherCount,
            'total_classes' => $classCount,
            'total_subjects' => $subjectCount,
            'total_exams' => $examCount,
            'total_fee_payments' => $totalFeePayments,
            'attendance_rate' => $this->getAttendanceRate(),
        ];

        // Real-time fee collection data
        $collectedTodayFeePayments = FeePayment::whereDate('payment_date', today())
            ->where('status', 'paid')
            ->sum('amount_paid');
        $collectedTodayPaymentRecords = PaymentRecord::whereDate('created_at', today())
            ->where('status', 'approved')
            ->sum('amount');
        $collectedToday = $collectedTodayFeePayments + $collectedTodayPaymentRecords;
        
        // Comprehensive financial statistics for admin dashboard
        $totalExpenses = 0; // TODO: Implement expense tracking
        $netProfit = $totalFeePayments - $totalExpenses;
        
        $feeStats = [
            'collected_today' => $collectedToday,
            'pending' => $this->getPendingPayments(),
            'pending_approvals' => PaymentRecord::where('status', 'pending')->count(),
            'total_revenue' => $totalFeePayments,
            'total_expenses' => $totalExpenses,
            'net_profit' => $netProfit,
            'monthly_revenue' => $collectedTodayFeePayments + $collectedTodayPaymentRecords,
        ];

        // Real-time attendance data for students
        $presentToday = StudentAttendance::whereDate('attendance_date', today())
            ->where('status', 'present')->count();
        $absentToday = StudentAttendance::whereDate('attendance_date', today())
            ->where('status', 'absent')->count();
        $lateToday = StudentAttendance::whereDate('attendance_date', today())
            ->where('status', 'late')->count();
        $excusedToday = StudentAttendance::whereDate('attendance_date', today())
            ->where('status', 'excused')->count();
        
        // Recent student attendance records
        $recentStudentAttendance = StudentAttendance::with(['student.user', 'student.classRoom'])
            ->whereDate('attendance_date', today())
            ->latest()
            ->limit(10)
            ->get();
        
        // Teacher attendance data
        $teachersPresentToday = TeacherAttendance::whereDate('date', today())
            ->where('status', 'present')->count();
        $teachersAbsentToday = TeacherAttendance::whereDate('date', today())
            ->where('status', 'absent')->count();
        $teachersLateToday = TeacherAttendance::whereDate('date', today())
            ->where('status', 'late')->count();
        
        // Recent teacher attendance records
        $recentTeacherAttendance = TeacherAttendance::with(['teacher.user'])
            ->whereDate('date', today())
            ->latest()
            ->limit(5)
            ->get();
        
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
        $upcoming_exams = ExamSchedule::with(['examType', 'subject'])
            ->where('exam_date', '>=', now())
            ->orderBy('exam_date')
            ->limit(5)
            ->get();

        return view('dashboard.admin', compact(
            'stats', 'feeStats', 'attendanceStats', 'recentActivities', 'upcoming_exams',
            'recentStudentAttendance', 'recentTeacherAttendance'
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
            // Real-time activities from notifications and system events
            $recentNotifications = Notification::where('user_id', auth()->id())
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
