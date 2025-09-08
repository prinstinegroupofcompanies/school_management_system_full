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
            ->whereYear('date', $currentYear)
            ->orderBy('date', 'desc')
            ->paginate(20);

        // Calculate attendance statistics
        $totalDays = StudentAttendance::where('student_id', $student->id)
            ->whereYear('date', $currentYear)
            ->count();

        $presentDays = StudentAttendance::where('student_id', $student->id)
            ->whereYear('date', $currentYear)
            ->where('status', 'present')
            ->count();

        $absentDays = StudentAttendance::where('student_id', $student->id)
            ->whereYear('date', $currentYear)
            ->where('status', 'absent')
            ->count();

        $lateDays = StudentAttendance::where('student_id', $student->id)
            ->whereYear('date', $currentYear)
            ->where('status', 'late')
            ->count();

        $attendancePercentage = $totalDays > 0 ? round(($presentDays / $totalDays) * 100, 2) : 0;

        // Get monthly attendance for the current year
        $monthlyAttendance = [];
        for ($i = 1; $i <= 12; $i++) {
            $monthStart = Carbon::create($currentYear, $i, 1)->startOfMonth();
            $monthEnd = Carbon::create($currentYear, $i, 1)->endOfMonth();
            
            $monthTotal = StudentAttendance::where('student_id', $student->id)
                ->whereBetween('date', [$monthStart, $monthEnd])
                ->count();
                
            $monthPresent = StudentAttendance::where('student_id', $student->id)
                ->whereBetween('date', [$monthStart, $monthEnd])
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
}
