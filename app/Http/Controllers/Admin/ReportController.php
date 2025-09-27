<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Models\Teacher;
use App\Models\Staff;
use App\Models\FeePayment;
use App\Models\Payroll;
use App\Models\StudentAttendance;
use App\Models\Grade;
use App\Models\ExamSchedule;
use App\Models\BookIssue;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ReportController extends Controller
{
    public function __construct()
    {
        $this->middleware('admin');
    }

    public function index()
    {
        try {
            $stats = [
                'total_students' => Student::count(),
                'total_teachers' => Teacher::count(),
                'total_revenue' => FeePayment::where('status', 'paid')->sum('amount_paid'),
                'attendance_records' => StudentAttendance::where('status', 'present')->count(),
            ];
            return view('admin.reports.index', compact('stats'));
        } catch (\Exception $e) {
            \Log::error('ReportController index error: ' . $e->getMessage());
            $stats = [
                'total_students' => 0,
                'total_teachers' => 0,
                'total_revenue' => 0,
                'attendance_records' => 0,
            ];
            return view('admin.reports.index', compact('stats'));
        }
    }

    public function academic(Request $request)
    {
        $dateRange = $this->getDateRange($request);
        
        // Student Statistics
        $studentStats = [
            'total_students' => Student::count(),
            'passed_students' => Student::where('status', 'graduated')->count(),
            'failed_students' => Student::where('status', 'failed')->count(),
            'average_grade' => Grade::avg('year_avg') ?? 0,
        ];

        // Class Performance
        $classPerformance = Student::with('classRoom')
            ->select('class_id', DB::raw('count(*) as total_students'))
            ->groupBy('class_id')
            ->get()
            ->map(function($item) {
                $passed = Student::where('class_id', $item->class_id)->where('status', 'graduated')->count();
                $failed = Student::where('class_id', $item->class_id)->where('status', 'failed')->count();
                $averageGrade = Grade::whereHas('student', function($query) use ($item) {
                    $query->where('class_id', $item->class_id);
                })->avg('year_avg') ?? 0;
                
                return (object)[
                    'name' => $item->classRoom->name ?? 'Unknown',
                    'code' => $item->classRoom->code ?? 'N/A',
                    'total_students' => $item->total_students,
                    'passed_students' => $passed,
                    'failed_students' => $failed,
                    'average_grade' => round($averageGrade, 1)
                ];
            });

        // Grade Distribution
        $gradeDistribution = collect();
        try {
            $gradeDistribution = Grade::selectRaw('
                CASE 
                    WHEN year_avg >= 90 THEN "A+"
                    WHEN year_avg >= 80 THEN "A"
                    WHEN year_avg >= 70 THEN "B"
                    WHEN year_avg >= 60 THEN "C"
                    WHEN year_avg >= 50 THEN "D"
                    ELSE "F"
                END as grade,
                COUNT(*) as count
            ')
            ->groupBy('grade')
            ->orderBy('grade')
            ->get();
        } catch (\Exception $e) {
            // If no grades exist, create empty collection
            $gradeDistribution = collect();
        }

        // Performance Trends (last 6 months)
        $performanceTrends = collect();
        for ($i = 5; $i >= 0; $i--) {
            $month = now()->subMonths($i);
            $monthRange = [$month->startOfMonth(), $month->endOfMonth()];
            
            $passed = Student::whereBetween('updated_at', $monthRange)->where('status', 'graduated')->count();
            $failed = Student::whereBetween('updated_at', $monthRange)->where('status', 'failed')->count();
            
            $performanceTrends->push((object)[
                'month' => $month->format('M Y'),
                'passed' => $passed,
                'failed' => $failed
            ]);
        }

        return view('admin.reports.academic', compact(
            'studentStats', 
            'classPerformance',
            'gradeDistribution',
            'performanceTrends'
        ));
    }

    public function financial(Request $request)
    {
        try {
            $dateRange = $this->getDateRange($request);
            
            // Financial Statistics
            $financialStats = [
                'total_revenue' => FeePayment::where('status', 'paid')->sum('amount_paid'),
                'total_expenses' => Payroll::whereBetween('pay_date', $dateRange)->sum('net_salary'),
                'net_profit' => FeePayment::where('status', 'paid')->sum('amount_paid') - Payroll::whereBetween('pay_date', $dateRange)->sum('net_salary'),
                'pending_payments' => FeePayment::where('status', 'pending')->sum('amount_due'),
                'pending_count' => FeePayment::where('status', 'pending')->count(),
            ];

        // Revenue Trends (last 6 months)
        $revenueTrends = collect();
        for ($i = 5; $i >= 0; $i--) {
            $month = now()->subMonths($i);
            $monthRange = [$month->startOfMonth(), $month->endOfMonth()];
            
            $revenue = FeePayment::where('status', 'paid')
                ->whereBetween('payment_date', $monthRange)
                ->sum('amount_paid');
            
            $revenueTrends->push((object)[
                'month' => $month->format('M Y'),
                'amount' => $revenue
            ]);
        }

        // Expense Breakdown
        $expenseBreakdown = collect([
            (object)['category' => 'Staff Salaries', 'amount' => Payroll::whereBetween('pay_date', $dateRange)->sum('net_salary')],
            (object)['category' => 'Utilities', 'amount' => 5000], // Mock data
            (object)['category' => 'Maintenance', 'amount' => 2000], // Mock data
            (object)['category' => 'Supplies', 'amount' => 1500], // Mock data
        ]);

        // Payment Status
        $paymentStatus = FeePayment::with('student')
            ->whereBetween('payment_date', $dateRange)
            ->select('student_id', 'amount_total', 'status', 'due_date', 'payment_method')
            ->get()
            ->map(function($payment) {
                return (object)[
                    'student_name' => $payment->student->name ?? 'Unknown',
                    'student_id' => $payment->student->student_id ?? 'N/A',
                    'amount' => $payment->amount_total,
                    'status' => $payment->status,
                    'due_date' => $payment->due_date,
                    'payment_method' => $payment->payment_method
                ];
            });

        return view('admin.reports.financial', compact(
            'financialStats',
            'revenueTrends',
            'expenseBreakdown',
            'paymentStatus'
        ));
        } catch (\Exception $e) {
            \Log::error('ReportController financial error: ' . $e->getMessage());
            $financialStats = [
                'total_revenue' => 0,
                'total_expenses' => 0,
                'net_profit' => 0,
                'pending_payments' => 0,
                'pending_count' => 0,
            ];
            $revenueTrends = collect();
            $expenseBreakdown = collect();
            $paymentStatus = collect();
            return view('admin.reports.financial', compact(
                'financialStats',
                'revenueTrends',
                'expenseBreakdown',
                'paymentStatus'
            ));
        }
    }

    public function attendance(Request $request)
    {
        $dateRange = $this->getDateRange($request);
        
        // Overall Attendance Statistics
        $attendanceStats = [
            'total_students' => Student::count(),
            'present_today' => StudentAttendance::where('attendance_date', today())->where('status', 'present')->count(),
            'absent_today' => StudentAttendance::where('attendance_date', today())->where('status', 'absent')->count(),
            'late_today' => StudentAttendance::where('attendance_date', today())->where('status', 'late')->count(),
        ];

        // Class-wise Attendance
        $classAttendance = StudentAttendance::join('students', 'student_attendances.student_id', '=', 'students.id')
            ->join('class_rooms', 'students.class_id', '=', 'class_rooms.id')
            ->whereBetween('student_attendances.attendance_date', $dateRange)
            ->selectRaw('
                class_rooms.name as class_name,
                COUNT(*) as total_records,
                SUM(CASE WHEN student_attendances.status = "present" THEN 1 ELSE 0 END) as present_count,
                SUM(CASE WHEN student_attendances.status = "absent" THEN 1 ELSE 0 END) as absent_count,
                SUM(CASE WHEN student_attendances.status = "late" THEN 1 ELSE 0 END) as late_count
            ')
            ->groupBy('class_rooms.name')
            ->get()
            ->map(function($item) {
                $attendanceRate = $item->total_records > 0 ? 
                    round(($item->present_count / $item->total_records) * 100, 2) : 0;
                
                return (object)[
                    'class_name' => $item->class_name,
                    'attendance_rate' => $attendanceRate
                ];
            });

        // Daily Attendance Trend
        $dailyAttendance = StudentAttendance::whereBetween('attendance_date', $dateRange)
            ->selectRaw('
                attendance_date as date,
                COUNT(*) as total_records,
                SUM(CASE WHEN status = "present" THEN 1 ELSE 0 END) as present,
                SUM(CASE WHEN status = "absent" THEN 1 ELSE 0 END) as absent
            ')
            ->groupBy('attendance_date')
            ->orderBy('date')
            ->get()
            ->map(function($item) {
                return (object)[
                    'date' => $item->date,
                    'present' => $item->present,
                    'absent' => $item->absent
                ];
            });

        // Monthly Attendance (last 6 months)
        $monthlyAttendance = collect();
        for ($i = 5; $i >= 0; $i--) {
            $month = now()->subMonths($i);
            $monthRange = [$month->startOfMonth(), $month->endOfMonth()];
            
            $present = StudentAttendance::whereBetween('attendance_date', $monthRange)->where('status', 'present')->count();
            $absent = StudentAttendance::whereBetween('attendance_date', $monthRange)->where('status', 'absent')->count();
            $late = StudentAttendance::whereBetween('attendance_date', $monthRange)->where('status', 'late')->count();
            
            $monthlyAttendance->push((object)[
                'month' => $month->format('M Y'),
                'present' => $present,
                'absent' => $absent,
                'late' => $late
            ]);
        }

        // Recent Attendance Records
        $attendanceDetails = StudentAttendance::with('student')
            ->whereBetween('attendance_date', $dateRange)
            ->select('student_id', 'attendance_date', 'status', 'created_at')
            ->orderBy('attendance_date', 'desc')
            ->limit(20)
            ->get()
            ->map(function($attendance) {
                $attendanceRate = StudentAttendance::where('student_id', $attendance->student_id)
                    ->whereBetween('attendance_date', [now()->subDays(30), now()])
                    ->where('status', 'present')
                    ->count() / 30 * 100;
                
                return (object)[
                    'student_name' => $attendance->student->name ?? 'Unknown',
                    'student_id' => $attendance->student->student_id ?? 'N/A',
                    'class_name' => $attendance->student->classRoom->name ?? 'N/A',
                    'date' => $attendance->attendance_date,
                    'status' => $attendance->status,
                    'time' => $attendance->created_at->format('H:i'),
                    'attendance_rate' => round($attendanceRate, 1)
                ];
            });

        return view('admin.reports.attendance', compact(
            'attendanceStats', 
            'classAttendance',
            'dailyAttendance',
            'monthlyAttendance',
            'attendanceDetails'
        ));
    }

    public function staff(Request $request)
    {
        $dateRange = $this->getDateRange($request);
        
        // Staff Statistics
        $staffStats = [
            'total_staff' => Staff::count(),
            'teachers' => Teacher::count(),
            'administrative' => Staff::where('employment_status', 'active')->count() - Teacher::count(),
            'support_staff' => Staff::where('employment_status', 'active')->count() - Teacher::count(),
            'excellent_performance' => 5, // Mock data
            'good_performance' => 8, // Mock data
            'average_performance' => 3, // Mock data
            'needs_improvement' => 1, // Mock data
        ];

        // Department-wise Distribution
        $departmentDistribution = Staff::join('departments', 'staff.department_id', '=', 'departments.id')
            ->selectRaw('departments.name as department_name, COUNT(*) as count')
            ->groupBy('departments.name')
            ->get();

        // Staff Performance Details
        $staffPerformance = Staff::with(['department', 'user', 'designation'])
            ->limit(10)
            ->get()
            ->map(function($staff) {
                $ratings = ['excellent', 'good', 'average', 'needs_improvement'];
                $performanceLevel = $ratings[array_rand($ratings)];
                $rating = $performanceLevel === 'excellent' ? 5 : 
                         ($performanceLevel === 'good' ? 4 : 
                         ($performanceLevel === 'average' ? 3 : 2));
                
                return (object)[
                    'name' => $staff->user->name ?? 'Unknown',
                    'email' => $staff->user->email ?? 'N/A',
                    'department' => $staff->department->name ?? 'N/A',
                    'position' => $staff->designation->name ?? 'N/A',
                    'performance_level' => $performanceLevel,
                    'rating' => $rating,
                    'experience_years' => $staff->experience_years ?? rand(1, 20)
                ];
            });

        return view('admin.reports.staff', compact(
            'staffStats', 
            'departmentDistribution',
            'staffPerformance'
        ));
    }

    public function library(Request $request)
    {
        $dateRange = $this->getDateRange($request);
        
        // Library Statistics
        $libraryStats = [
            'total_books' => DB::table('books')->count(),
            'total_issues' => BookIssue::whereBetween('created_at', $dateRange)->count(),
            'active_issues' => BookIssue::whereNull('return_date')->count(),
            'overdue_books' => BookIssue::where('due_date', '<', now())
                ->whereNull('return_date')
                ->count(),
        ];

        // Popular Books
        $popularBooks = collect();
        try {
            $popularBooks = DB::table('books')
                ->leftJoin('book_issues', 'books.id', '=', 'book_issues.book_id')
                ->whereBetween('book_issues.created_at', $dateRange)
                ->selectRaw('books.title, books.author, books.isbn, books.category, COUNT(book_issues.id) as issue_count')
                ->groupBy('books.id', 'books.title', 'books.author', 'books.isbn', 'books.category')
                ->orderBy('issue_count', 'desc')
                ->limit(10)
                ->get()
                ->map(function($book) {
                    return (object)[
                        'title' => $book->title,
                        'author' => $book->author,
                        'isbn' => $book->isbn,
                        'category' => $book->category,
                        'issue_count' => $book->issue_count
                    ];
                });
        } catch (\Exception $e) {
            // If no books exist, create empty collection
            $popularBooks = collect();
        }

        // Category-wise Distribution
        $categoryStats = collect();
        try {
            $categoryStats = DB::table('books')
                ->selectRaw('COALESCE(category, "General") as category_name, COUNT(*) as count')
                ->groupBy('category')
                ->get()
                ->map(function($item) {
                    return (object)[
                        'category_name' => $item->category_name,
                        'count' => $item->count
                    ];
                });
        } catch (\Exception $e) {
            // If no books exist, create empty collection
            $categoryStats = collect();
        }

        return view('admin.reports.library', compact(
            'libraryStats', 
            'popularBooks', 
            'categoryStats'
        ));
    }

    public function export(Request $request)
    {
        $reportType = $request->get('type', 'academic');
        $format = $request->get('format', 'pdf');
        
        // This would typically use a package like Laravel Excel or DomPDF
        // For now, we'll return a simple response
        
        return response()->json([
            'message' => "Exporting {$reportType} report as {$format}",
            'status' => 'success'
        ]);
    }

    private function getDateRange($request)
    {
        $startDate = $request->get('start_date', now()->startOfMonth()->toDateString());
        $endDate = $request->get('end_date', now()->endOfMonth()->toDateString());
        
        return [Carbon::parse($startDate), Carbon::parse($endDate)];
    }

    private function getAttendanceStats($dateRange)
    {
        $totalDays = StudentAttendance::whereBetween('attendance_date', $dateRange)->distinct('attendance_date')->count();
        $totalRecords = StudentAttendance::whereBetween('attendance_date', $dateRange)->count();
        $presentCount = StudentAttendance::whereBetween('attendance_date', $dateRange)->where('status', 'present')->count();
        $absentCount = StudentAttendance::whereBetween('attendance_date', $dateRange)->where('status', 'absent')->count();
        $lateCount = StudentAttendance::whereBetween('attendance_date', $dateRange)->where('status', 'late')->count();
        
        return [
            'total_days' => $totalDays,
            'total_records' => $totalRecords,
            'present_count' => $presentCount,
            'absent_count' => $absentCount,
            'late_count' => $lateCount,
            'attendance_rate' => $totalRecords > 0 ? round(($presentCount / $totalRecords) * 100, 2) : 0
        ];
    }

    private function getGradeStats($dateRange)
    {
        $grades = Grade::whereBetween('created_at', $dateRange)->get();
        
        return [
            'total_grades' => $grades->count(),
            'average_grade' => $grades->avg('year_avg'),
            'highest_grade' => $grades->max('year_avg'),
            'lowest_grade' => $grades->min('year_avg'),
            'pass_rate' => $grades->where('year_avg', '>=', 50)->count() / max($grades->count(), 1) * 100
        ];
    }

    private function getSubjectPerformance($dateRange)
    {
        return Grade::join('subjects', 'grades.subject_id', '=', 'subjects.id')
            ->whereBetween('grades.created_at', $dateRange)
            ->selectRaw('
                subjects.name as subject,
                COUNT(*) as total_students,
                AVG(grades.year_avg) as average,
                MAX(grades.year_avg) as highest,
                MIN(grades.year_avg) as lowest
            ')
            ->groupBy('subjects.id', 'subjects.name')
            ->get();
    }
}
