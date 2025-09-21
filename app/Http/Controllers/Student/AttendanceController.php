<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\StudentAttendance;
use Illuminate\Http\Request;
use Carbon\Carbon;

class AttendanceController extends Controller
{
    public function index()
    {
        $student = auth()->user()->student;
        
        if (!$student) {
            abort(403, 'Student record not found');
        }

        // Get attendance records for the current academic year
        $currentYear = date('Y');
        $attendanceRecords = StudentAttendance::where('student_id', $student->id)
            ->whereYear('attendance_date', $currentYear)
            ->orderBy('attendance_date', 'desc')
            ->paginate(20);

        // Calculate attendance statistics
        $totalDays = StudentAttendance::where('student_id', $student->id)
            ->whereYear('attendance_date', $currentYear)
            ->count();

        $presentDays = StudentAttendance::where('student_id', $student->id)
            ->whereYear('attendance_date', $currentYear)
            ->where('status', 'present')
            ->count();

        $absentDays = StudentAttendance::where('student_id', $student->id)
            ->whereYear('attendance_date', $currentYear)
            ->where('status', 'absent')
            ->count();

        $lateDays = StudentAttendance::where('student_id', $student->id)
            ->whereYear('attendance_date', $currentYear)
            ->where('status', 'late')
            ->count();

        $attendancePercentage = $totalDays > 0 ? round(($presentDays / $totalDays) * 100, 2) : 0;

        // Get monthly attendance for the current year
        $monthlyAttendance = [];
        for ($i = 1; $i <= 12; $i++) {
            $monthStart = Carbon::create($currentYear, $i, 1)->startOfMonth();
            $monthEnd = Carbon::create($currentYear, $i, 1)->endOfMonth();
            
            $monthTotal = StudentAttendance::where('student_id', $student->id)
                ->whereBetween('attendance_date', [$monthStart, $monthEnd])
                ->count();
                
            $monthPresent = StudentAttendance::where('student_id', $student->id)
                ->whereBetween('attendance_date', [$monthStart, $monthEnd])
                ->where('status', 'present')
                ->count();
                
            $monthPercentage = $monthTotal > 0 ? round(($monthPresent / $monthTotal) * 100, 2) : 0;
            
            $monthlyAttendance[] = [
                'month' => $monthStart->format('M'),
                'total' => $monthTotal,
                'present' => $monthPresent,
                'percentage' => $monthPercentage
            ];
        }

        $stats = [
            'total_days' => $totalDays,
            'present_days' => $presentDays,
            'absent_days' => $absentDays,
            'late_days' => $lateDays,
            'attendance_percentage' => $attendancePercentage
        ];

        return view('student.attendance.index', compact('attendanceRecords', 'stats', 'monthlyAttendance'));
    }

    public function show($id)
    {
        $student = auth()->user()->student;
        
        if (!$student) {
            abort(403, 'Student record not found');
        }

        $attendance = StudentAttendance::where('id', $id)
            ->where('student_id', $student->id)
            ->firstOrFail();

        return view('student.attendance.show', compact('attendance'));
    }

    public function history(Request $request)
    {
        $student = auth()->user()->student;
        
        if (!$student) {
            abort(403, 'Student record not found');
        }

        // Get filter parameters
        $year = $request->get('year', date('Y'));
        $month = $request->get('month', null);
        $status = $request->get('status', null);

        // Build query for attendance records
        $query = StudentAttendance::where('student_id', $student->id)
            ->with(['student.user'])
            ->whereYear('attendance_date', $year);

        // Apply month filter if provided
        if ($month) {
            $query->whereMonth('attendance_date', $month);
        }

        // Apply status filter if provided
        if ($status) {
            $query->where('status', $status);
        }

        // Get attendance records with pagination
        $attendanceRecords = $query->orderBy('attendance_date', 'desc')->paginate(15);

        // Calculate statistics for the filtered period
        $totalDays = StudentAttendance::where('student_id', $student->id)
            ->whereYear('attendance_date', $year)
            ->when($month, function($q) use ($month) {
                return $q->whereMonth('attendance_date', $month);
            })
            ->count();

        $presentDays = StudentAttendance::where('student_id', $student->id)
            ->whereYear('attendance_date', $year)
            ->when($month, function($q) use ($month) {
                return $q->whereMonth('attendance_date', $month);
            })
            ->where('status', 'present')
            ->count();

        $absentDays = StudentAttendance::where('student_id', $student->id)
            ->whereYear('attendance_date', $year)
            ->when($month, function($q) use ($month) {
                return $q->whereMonth('attendance_date', $month);
            })
            ->where('status', 'absent')
            ->count();

        $lateDays = StudentAttendance::where('student_id', $student->id)
            ->whereYear('attendance_date', $year)
            ->when($month, function($q) use ($month) {
                return $q->whereMonth('attendance_date', $month);
            })
            ->where('status', 'late')
            ->count();

        $excusedDays = StudentAttendance::where('student_id', $student->id)
            ->whereYear('attendance_date', $year)
            ->when($month, function($q) use ($month) {
                return $q->whereMonth('attendance_date', $month);
            })
            ->where('status', 'excused')
            ->count();

        $attendancePercentage = $totalDays > 0 ? round(($presentDays / $totalDays) * 100, 2) : 0;

        $stats = [
            'total_days' => $totalDays,
            'present_days' => $presentDays,
            'absent_days' => $absentDays,
            'late_days' => $lateDays,
            'excused_days' => $excusedDays,
            'attendance_percentage' => $attendancePercentage
        ];

        // Get available years for filter dropdown
        $availableYears = StudentAttendance::where('student_id', $student->id)
            ->selectRaw('DISTINCT YEAR(attendance_date) as year')
            ->orderBy('year', 'desc')
            ->pluck('year')
            ->toArray();

        if (empty($availableYears)) {
            $availableYears = [date('Y')];
        }

        return view('student.attendance.history', compact(
            'attendanceRecords', 
            'stats', 
            'year', 
            'month', 
            'status', 
            'availableYears'
        ));
    }

    public function summary(Request $request)
    {
        $student = auth()->user()->student;
        
        if (!$student) {
            abort(403, 'Student record not found');
        }

        // Get attendance summary for different periods
        $currentYear = date('Y');
        
        // This year's summary
        $yearlyStats = [
            'total' => StudentAttendance::where('student_id', $student->id)
                ->whereYear('attendance_date', $currentYear)
                ->count(),
            'present' => StudentAttendance::where('student_id', $student->id)
                ->whereYear('attendance_date', $currentYear)
                ->where('status', 'present')
                ->count(),
            'absent' => StudentAttendance::where('student_id', $student->id)
                ->whereYear('attendance_date', $currentYear)
                ->where('status', 'absent')
                ->count(),
            'late' => StudentAttendance::where('student_id', $student->id)
                ->whereYear('attendance_date', $currentYear)
                ->where('status', 'late')
                ->count(),
            'excused' => StudentAttendance::where('student_id', $student->id)
                ->whereYear('attendance_date', $currentYear)
                ->where('status', 'excused')
                ->count(),
        ];
        
        $yearlyStats['percentage'] = $yearlyStats['total'] > 0 ? 
            round(($yearlyStats['present'] / $yearlyStats['total']) * 100, 2) : 0;

        // This month's summary
        $currentMonth = date('n');
        $monthlyStats = [
            'total' => StudentAttendance::where('student_id', $student->id)
                ->whereYear('attendance_date', $currentYear)
                ->whereMonth('attendance_date', $currentMonth)
                ->count(),
            'present' => StudentAttendance::where('student_id', $student->id)
                ->whereYear('attendance_date', $currentYear)
                ->whereMonth('attendance_date', $currentMonth)
                ->where('status', 'present')
                ->count(),
            'absent' => StudentAttendance::where('student_id', $student->id)
                ->whereYear('attendance_date', $currentYear)
                ->whereMonth('attendance_date', $currentMonth)
                ->where('status', 'absent')
                ->count(),
            'late' => StudentAttendance::where('student_id', $student->id)
                ->whereYear('attendance_date', $currentYear)
                ->whereMonth('attendance_date', $currentMonth)
                ->where('status', 'late')
                ->count(),
            'excused' => StudentAttendance::where('student_id', $student->id)
                ->whereYear('attendance_date', $currentYear)
                ->whereMonth('attendance_date', $currentMonth)
                ->where('status', 'excused')
                ->count(),
        ];
        
        $monthlyStats['percentage'] = $monthlyStats['total'] > 0 ? 
            round(($monthlyStats['present'] / $monthlyStats['total']) * 100, 2) : 0;

        // Recent attendance (last 10 records)
        $recentAttendance = StudentAttendance::where('student_id', $student->id)
            ->orderBy('attendance_date', 'desc')
            ->limit(10)
            ->get();

        return view('student.attendance.summary', compact(
            'yearlyStats', 
            'monthlyStats', 
            'recentAttendance'
        ));
    }
}
