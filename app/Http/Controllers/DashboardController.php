<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\Teacher;
use App\Models\ClassRoom;
use App\Models\Subject;
use App\Models\ExamSchedule;
use App\Models\FeePayment;
use App\Models\FeeStructure;
use App\Models\StudentAttendance;
use Illuminate\Http\Request;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        
        // Redirect based on user type
        switch ($user->user_type) {
            case 'admin':
                return $this->adminDashboard();
            case 'teacher':
                return $this->teacherDashboard();
            case 'student':
                // Redirect students to their proper dashboard to avoid timeout issues
                return redirect()->route('student.dashboard');
            case 'finance':
                return $this->financeDashboard();
            default:
                return $this->defaultDashboard();
        }
    }

    public function adminDashboard()
    {
        $session = $this->currentSession();
        $data = [
            'stats' => [
                'total_students' => Student::count(),
                'total_teachers' => Teacher::count(),
                'total_classes' => ClassRoom::count(),
                'total_subjects' => Subject::count(),
            ],
            'attendanceStats' => [
                'present' => StudentAttendance::where('status', 'present')->whereDate('date', today())->count(),
                'absent' => StudentAttendance::where('status', 'absent')->whereDate('date', today())->count(),
                'late' => StudentAttendance::where('status', 'late')->whereDate('date', today())->count(),
                'total' => StudentAttendance::whereDate('date', today())->count(),
            ],
            'upcomingExams' => ExamSchedule::where('exam_date', '>=', today())->with(['examType', 'subject'])->take(5)->get(),
            'feeStats' => [
                'collected_today' => FeePayment::whereDate('payment_date', today())->sum('amount_paid'),
                'pending' => FeeStructure::sum('amount') - FeePayment::sum('amount_paid'),
                'overdue' => 0, // Calculate based on due dates
            ],
            'recentActivities' => $this->getRecentActivities(),
        ];

        return view('dashboard.admin', $data + ['session' => $session]);
    }

    public function teacherDashboard()
    {
        $user = auth()->user();
        $teacher = $user->teacher;
        $session = $this->currentSession();
        
        // Get actual data for the teacher
        $assignedSubjects = $teacher ? $teacher->subjects()->with('class')->get() : collect();
        $assignedClasses = $teacher ? $teacher->classes()->get() : collect();
        
        // Get unique students from assigned classes
        $assignedStudentIds = collect();
        foreach ($assignedClasses as $class) {
            $assignedStudentIds = $assignedStudentIds->merge($class->students()->pluck('id'));
        }
        $uniqueStudents = $assignedStudentIds->unique();
        
        $stats = [
            'total_classes' => $assignedClasses->count(),
            'total_subjects' => $assignedSubjects->count(),
            'total_students' => $uniqueStudents->count(),
            'upcoming_exams' => 0, // Will be implemented later
        ];

        $recent_activities = collect();
        // Pull recent teacher-related events from real models when available
        $recentHomework = \App\Models\Homework::whereIn('class_id', $assignedClasses->pluck('id'))
            ->latest()->take(3)->get(['title','created_at'])->map(fn($h) => [
                'description' => 'Homework created: ' . $h->title,
                'created_at' => $h->created_at,
            ]);
        $recentAttendance = StudentAttendance::whereIn('class_id', $assignedClasses->pluck('id'))
            ->latest('date')->take(3)->get(['date'])->map(fn($a) => [
                'description' => 'Attendance marked on ' . \Carbon\Carbon::parse($a->date)->toFormattedDateString(),
                'created_at' => $a->date,
            ]);
        $recent_activities = $recentHomework->merge($recentAttendance)->sortByDesc('created_at')->values();

        $recent_homework = \App\Models\Homework::whereIn('class_id', $assignedClasses->pluck('id'))
            ->whereDate('due_date','>=', today())
            ->orderBy('due_date')
            ->take(5)->get(['id','title','due_date']);

        // Use actual assigned classes data
        $classes = $assignedClasses->map(function($class) {
            return (object) [
                'id' => $class->id,
                'name' => $class->name,
                'code' => $class->code,
                'students' => $class->students()->get()->map(function($student) {
                    return (object) ['id' => $student->id, 'name' => $student->user->name];
                }),
            ];
        });

        // Get actual students from assigned classes
        $students = collect();
        foreach ($assignedClasses as $class) {
            $students = $students->merge($class->students()->with('user')->get()->map(function($student) {
                return (object) ['id' => $student->id, 'name' => $student->user->name];
            }));
        }
        $students = $students->unique('id');

        // Use actual assigned subjects data
        $subjects = $assignedSubjects->map(function($subject) {
            return (object) ['id' => $subject->id, 'name' => $subject->name];
        });

        $upcomingExams = ExamSchedule::with(['examType','subject'])
            ->whereIn('class_id', $assignedClasses->pluck('id'))
            ->whereDate('exam_date','>=', today())
            ->orderBy('exam_date')
            ->take(5)->get();

        $data = [
            'user' => $user,
            'stats' => $stats,
            'recentActivities' => $recent_activities,
            'recent_homework' => $recent_homework,
            'classes' => $classes,
            'students' => $students,
            'subjects' => $subjects,
            'upcomingExams' => $upcomingExams,
        ];

        return view('dashboard.teacher', $data + ['session' => $session]);
    }

    public function studentDashboard()
    {
        // Redirect students to their proper dashboard
        return redirect()->route('student.dashboard');
    }

    private function currentSession(): array
    {
        $year = (int) date('Y');
        $month = (int) date('n');
        $semester = $month <= 6 ? 1 : 2;
        return [
            'academic_year' => $year,
            'semester' => $semester,
        ];
    }

    public function financeDashboard()
    {
        $user = auth()->user();
        
        // Use mock data like the FinanceController
        $stats = [
            'total_revenue' => FeePayment::sum('amount_paid'),
            'total_fees' => FeeStructure::sum('amount'),
            'total_collected' => FeePayment::sum('amount_paid'),
            'total_pending' => max(FeeStructure::sum('amount') - FeePayment::sum('amount_paid'), 0),
            'collected_today' => FeePayment::whereDate('payment_date', today())->sum('amount_paid'),
            'pending_payments' => max(FeeStructure::sum('amount') - FeePayment::sum('amount_paid'), 0),
            'total_scholarships' => 0,
            'active_scholarships' => 0,
            'monthly_revenue' => FeePayment::whereYear('payment_date', date('Y'))
                ->whereMonth('payment_date', date('m'))
                ->sum('amount_paid'),
            'fee_structures' => FeeStructure::count(),
        ];

        // Mock recent payments data
        $recent_payments = FeePayment::with(['student.user'])
            ->latest('payment_date')->take(5)->get();

        // Mock pending scholarships data
        $pending_scholarships = collect();

        // Monthly collection data (SQLite-safe)
        $monthlyCollection = $this->getMonthlyCollection();

        // Mock class-wise collection data
        $classWiseCollection = $this->getClassWiseCollection();

        // Mock recent activities
        $recentActivities = $recent_payments->map(function($p){
            return [
                'description' => 'Payment ' . number_format($p->amount,2) . ' by ' . ($p->student->user->name ?? 'Student'),
                'created_at' => $p->payment_date,
            ];
        });

        $data = [
            'user' => $user,
            'stats' => $stats,
            'recentPayments' => $recent_payments,
            'pending_scholarships' => $pending_scholarships,
            'monthlyCollection' => $monthlyCollection,
            'classWiseCollection' => $classWiseCollection,
            'recentActivities' => $recentActivities,
        ];

        return view('dashboard.finance', $data);
    }

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
            'recentActivities' => $this->getRecentActivities(),
        ];

        return view('dashboard', $data);
    }

    private function getStudentFeeStatus($student)
    {
        $totalFees = $student->feeStructures()->sum('amount');
        $totalPaid = $student->feePayments()->sum('amount');
        $pending = $totalFees - $totalPaid;
        
        return [
            'total_fees' => $totalFees,
            'total_paid' => $totalPaid,
            'pending' => $pending,
            'percentage_paid' => $totalFees > 0 ? round(($totalPaid / $totalFees) * 100, 2) : 0,
        ];
    }

    private function getMonthlyCollection()
    {
        return FeePayment::selectRaw('strftime("%m", payment_date) as month, SUM(amount_paid) as total')
            ->whereRaw('strftime("%Y", payment_date) = ?', [date('Y')])
            ->groupBy('month')
            ->orderBy('month')
            ->get();
    }

    private function getClassWiseCollection()
    {
        return \DB::table('fee_payments')
            ->join('fee_structures', 'fee_payments.fee_structure_id', '=', 'fee_structures.id')
            ->join('class_rooms', 'fee_structures.class_id', '=', 'class_rooms.id')
            ->selectRaw('class_rooms.name as class_name, SUM(fee_payments.amount_paid) as total_collected')
            ->groupBy('class_rooms.id', 'class_rooms.name')
            ->orderBy('total_collected', 'desc')
            ->get();
    }

    private function getRecentActivities()
    {
        // This would typically come from an activities table
        // For now, we'll return sample data
        return collect([
            [
                'id' => 1,
                'type' => 'student_registration',
                'description' => 'New student registered',
                'created_at' => Carbon::now()->subHours(2),
            ],
            [
                'id' => 2,
                'type' => 'fee_payment',
                'description' => 'Fee payment received',
                'created_at' => Carbon::now()->subHours(4),
            ],
            [
                'id' => 3,
                'type' => 'exam_scheduled',
                'description' => 'Exam scheduled',
                'created_at' => Carbon::now()->subHours(6),
            ],
        ]);
    }
}
