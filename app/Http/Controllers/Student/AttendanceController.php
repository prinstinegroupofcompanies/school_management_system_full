<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AttendanceController extends Controller
{
    public function index()
    {
        try {
            $user = auth()->user();
            $student = $user->student;
        } catch (\Exception $e) {
            return redirect()->route('student.dashboard')
                ->with('error', 'Student profile not available. Please contact administrator.');
        }
        
        if (!$student) {
            return redirect()->route('student.dashboard')
                ->with('error', 'Student record not found. Please contact administrator.');
        }

        // Get real-time attendance statistics with error handling
        try {
            $attendanceStats = [
                'total_days' => \App\Models\StudentAttendance::where('student_id', $student->id)->count(),
                'present_days' => \App\Models\StudentAttendance::where('student_id', $student->id)->where('status', 'present')->count(),
                'absent_days' => \App\Models\StudentAttendance::where('student_id', $student->id)->where('status', 'absent')->count(),
                'late_days' => \App\Models\StudentAttendance::where('student_id', $student->id)->where('status', 'late')->count(),
            ];

            // Calculate attendance percentage
            $attendanceStats['attendance_percentage'] = $attendanceStats['total_days'] > 0 
                ? round(($attendanceStats['present_days'] / $attendanceStats['total_days']) * 100, 2) 
                : 0;

            // Get recent attendance records
            $recentAttendance = \App\Models\StudentAttendance::where('student_id', $student->id)
                ->orderBy('attendance_date', 'desc')
                ->take(10)
                ->get();

            // Get this month's attendance
            $thisMonthAttendance = \App\Models\StudentAttendance::where('student_id', $student->id)
                ->whereMonth('attendance_date', now()->month)
                ->whereYear('attendance_date', now()->year)
                ->get();

            // Get today's attendance
            $todayAttendance = \App\Models\StudentAttendance::where('student_id', $student->id)
                ->whereDate('attendance_date', today())
                ->first();
        } catch (\Exception $e) {
            $attendanceStats = [
                'total_days' => 0,
                'present_days' => 0,
                'absent_days' => 0,
                'late_days' => 0,
                'attendance_percentage' => 0,
            ];
            $recentAttendance = collect();
            $thisMonthAttendance = collect();
            $todayAttendance = null;
        }

        return view('student.attendance.index', compact('attendanceStats', 'recentAttendance', 'thisMonthAttendance', 'todayAttendance'));
    }

    public function history()
    {
        try {
            $user = auth()->user();
            $student = $user->student;
        } catch (\Exception $e) {
            return redirect()->route('student.dashboard')
                ->with('error', 'Student profile not available. Please contact administrator.');
        }
        
        if (!$student) {
            return redirect()->route('student.dashboard')
                ->with('error', 'Student record not found. Please contact administrator.');
        }

        // Get attendance history with pagination
        try {
            $attendanceHistory = \App\Models\StudentAttendance::where('student_id', $student->id)
                ->orderBy('attendance_date', 'desc')
                ->paginate(20);
        } catch (\Exception $e) {
            $attendanceHistory = collect()->paginate(20);
        }

        return view('student.attendance.history', compact('attendanceHistory'));
    }

    public function summary()
    {
        try {
            $user = auth()->user();
            $student = $user->student;
        } catch (\Exception $e) {
            return redirect()->route('student.dashboard')
                ->with('error', 'Student profile not available. Please contact administrator.');
        }
        
        if (!$student) {
            return redirect()->route('student.dashboard')
                ->with('error', 'Student record not found. Please contact administrator.');
        }

        // Get monthly attendance summary
        try {
            $monthlySummary = \App\Models\StudentAttendance::where('student_id', $student->id)
                ->selectRaw('YEAR(attendance_date) as year, MONTH(attendance_date) as month, 
                           COUNT(*) as total_days,
                           SUM(CASE WHEN status = "present" THEN 1 ELSE 0 END) as present_days,
                           SUM(CASE WHEN status = "absent" THEN 1 ELSE 0 END) as absent_days,
                           SUM(CASE WHEN status = "late" THEN 1 ELSE 0 END) as late_days')
                ->groupBy('year', 'month')
                ->orderBy('year', 'desc')
                ->orderBy('month', 'desc')
                ->get()
                ->map(function($item) {
                    $item->attendance_percentage = $item->total_days > 0 
                        ? round(($item->present_days / $item->total_days) * 100, 2) 
                        : 0;
                    return $item;
                });
        } catch (\Exception $e) {
            $monthlySummary = collect();
        }

        return view('student.attendance.summary', compact('monthlySummary'));
    }
}