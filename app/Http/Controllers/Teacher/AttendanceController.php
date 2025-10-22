<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AttendanceController extends Controller
{
    public function __construct()
    {
        $this->middleware('teacher');
    }

    /**
     * Display teacher attendance management
     */
    public function index()
    {
        try {
            $user = auth()->user();
            $teacher = $user->teacher;
            
            if (!$teacher) {
                return redirect()->route('teacher.dashboard')
                    ->with('error', 'Teacher profile not found. Please contact administrator.');
            }

            // Get teacher's classes
            $classes = \App\Models\ClassRoom::where('teacher_id', $teacher->id)
                ->with(['students'])
                ->get();

            // Get today's attendance for teacher's classes
            $today = now()->format('Y-m-d');
            $todayAttendance = \App\Models\StudentAttendance::whereIn('class_id', $classes->pluck('id'))
                ->where('attendance_date', $today)
                ->with(['student.user', 'class'])
                ->get();

            // Get attendance statistics
            $stats = [
                'total_students' => $classes->sum(function($class) {
                    return $class->students->count();
                }),
                'present_today' => $todayAttendance->where('status', 'present')->count(),
                'absent_today' => $todayAttendance->where('status', 'absent')->count(),
                'late_today' => $todayAttendance->where('status', 'late')->count(),
                'total_classes' => $classes->count(),
            ];

            // Get recent attendance records
            $recentAttendance = \App\Models\StudentAttendance::whereIn('class_id', $classes->pluck('id'))
                ->with(['student.user', 'class'])
                ->orderBy('attendance_date', 'desc')
                ->limit(10)
                ->get();

        } catch (\Exception $e) {
            // Fallback data if database queries fail
            $classes = collect();
            $todayAttendance = collect();
            $stats = [
                'total_students' => 0,
                'present_today' => 0,
                'absent_today' => 0,
                'late_today' => 0,
                'total_classes' => 0,
            ];
            $recentAttendance = collect();
        }

        return view('teacher.attendance.index', compact('classes', 'todayAttendance', 'stats', 'recentAttendance'));
    }

    /**
     * Take attendance for a specific class
     */
    public function takeAttendance(Request $request, $classId)
    {
        try {
            $user = auth()->user();
            $teacher = $user->teacher;
            
            if (!$teacher) {
                return redirect()->route('teacher.dashboard')
                    ->with('error', 'Teacher profile not found. Please contact administrator.');
            }

            // Verify teacher has access to this class
            $class = \App\Models\ClassRoom::where('id', $classId)
                ->where('teacher_id', $teacher->id)
                ->with(['students.user'])
                ->first();

            if (!$class) {
                return redirect()->route('teacher.attendance.index')
                    ->with('error', 'Class not found or access denied.');
            }

            $date = $request->get('date', now()->format('Y-m-d'));
            
            // Get existing attendance for this date
            $existingAttendance = \App\Models\StudentAttendance::where('class_id', $classId)
                ->where('attendance_date', $date)
                ->get()
                ->keyBy('student_id');

        } catch (\Exception $e) {
            return redirect()->route('teacher.attendance.index')
                ->with('error', 'Failed to load attendance data.');
        }

        return view('teacher.attendance.take', compact('class', 'date', 'existingAttendance'));
    }

    /**
     * Store attendance records
     */
    public function storeAttendance(Request $request)
    {
        $request->validate([
            'class_id' => 'required|exists:class_rooms,id',
            'date' => 'required|date',
            'attendance' => 'required|array',
            'attendance.*.student_id' => 'required|exists:students,id',
            'attendance.*.status' => 'required|in:present,absent,late',
        ]);

        try {
            $user = auth()->user();
            $teacher = $user->teacher;
            
            if (!$teacher) {
                return redirect()->route('teacher.dashboard')
                    ->with('error', 'Teacher profile not found. Please contact administrator.');
            }

            // Verify teacher has access to this class
            $class = \App\Models\ClassRoom::where('id', $request->class_id)
                ->where('teacher_id', $teacher->id)
                ->first();

            if (!$class) {
                return redirect()->route('teacher.attendance.index')
                    ->with('error', 'Class not found or access denied.');
            }

            DB::beginTransaction();

            foreach ($request->attendance as $attendanceData) {
                \App\Models\StudentAttendance::updateOrCreate(
                    [
                        'student_id' => $attendanceData['student_id'],
                        'class_id' => $request->class_id,
                        'attendance_date' => $request->date,
                    ],
                    [
                        'status' => $attendanceData['status'],
                        'remarks' => $attendanceData['remarks'] ?? null,
                        'marked_by' => auth()->id(),
                        'marked_at' => now(),
                    ]
                );
            }

            DB::commit();

            return redirect()->route('teacher.attendance.index')
                ->with('success', 'Attendance recorded successfully.');

        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()
                ->with('error', 'Failed to record attendance: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * View attendance history for a class
     */
    public function viewHistory(Request $request, $classId)
    {
        try {
            $user = auth()->user();
            $teacher = $user->teacher;
            
            if (!$teacher) {
                return redirect()->route('teacher.dashboard')
                    ->with('error', 'Teacher profile not found. Please contact administrator.');
            }

            // Verify teacher has access to this class
            $class = \App\Models\ClassRoom::where('id', $classId)
                ->where('teacher_id', $teacher->id)
                ->with(['students.user'])
                ->first();

            if (!$class) {
                return redirect()->route('teacher.attendance.index')
                    ->with('error', 'Class not found or access denied.');
            }

            // Get date range
            $startDate = $request->get('start_date', now()->subDays(30)->format('Y-m-d'));
            $endDate = $request->get('end_date', now()->format('Y-m-d'));

            // Get attendance history
            $attendanceHistory = \App\Models\StudentAttendance::where('class_id', $classId)
                ->whereBetween('attendance_date', [$startDate, $endDate])
                ->with(['student.user'])
                ->orderBy('attendance_date', 'desc')
                ->orderBy('student_id')
                ->get()
                ->groupBy('attendance_date');

            // Calculate statistics
            $totalDays = \Carbon\Carbon::parse($startDate)->diffInDays(\Carbon\Carbon::parse($endDate)) + 1;
            $stats = [
                'total_days' => $totalDays,
                'total_records' => $attendanceHistory->flatten()->count(),
                'present_count' => $attendanceHistory->flatten()->where('status', 'present')->count(),
                'absent_count' => $attendanceHistory->flatten()->where('status', 'absent')->count(),
                'late_count' => $attendanceHistory->flatten()->where('status', 'late')->count(),
            ];

        } catch (\Exception $e) {
            return redirect()->route('teacher.attendance.index')
                ->with('error', 'Failed to load attendance history.');
        }

        return view('teacher.attendance.history', compact('class', 'attendanceHistory', 'stats', 'startDate', 'endDate'));
    }

    /**
     * Get attendance summary for a student
     */
    public function studentSummary(Request $request, $studentId)
    {
        try {
            $user = auth()->user();
            $teacher = $user->teacher;
            
            if (!$teacher) {
                return redirect()->route('teacher.dashboard')
                    ->with('error', 'Teacher profile not found. Please contact administrator.');
            }

            // Get student
            $student = \App\Models\Student::where('id', $studentId)
                ->with(['user', 'classRoom'])
                ->first();

            if (!$student) {
                return redirect()->route('teacher.attendance.index')
                    ->with('error', 'Student not found.');
            }

            // Verify teacher has access to this student's class
            $class = \App\Models\ClassRoom::where('id', $student->class_id)
                ->where('teacher_id', $teacher->id)
                ->first();

            if (!$class) {
                return redirect()->route('teacher.attendance.index')
                    ->with('error', 'Access denied to this student\'s attendance.');
            }

            // Get date range
            $startDate = $request->get('start_date', now()->subDays(30)->format('Y-m-d'));
            $endDate = $request->get('end_date', now()->format('Y-m-d'));

            // Get student's attendance
            $attendance = \App\Models\StudentAttendance::where('student_id', $studentId)
                ->whereBetween('attendance_date', [$startDate, $endDate])
                ->orderBy('attendance_date', 'desc')
                ->get();

            // Calculate statistics
            $totalDays = \Carbon\Carbon::parse($startDate)->diffInDays(\Carbon\Carbon::parse($endDate)) + 1;
            $stats = [
                'total_days' => $totalDays,
                'present_days' => $attendance->where('status', 'present')->count(),
                'absent_days' => $attendance->where('status', 'absent')->count(),
                'late_days' => $attendance->where('status', 'late')->count(),
                'attendance_percentage' => $totalDays > 0 ? round(($attendance->where('status', 'present')->count() / $totalDays) * 100, 2) : 0,
            ];

        } catch (\Exception $e) {
            return redirect()->route('teacher.attendance.index')
                ->with('error', 'Failed to load student attendance summary.');
        }

        return view('teacher.attendance.student-summary', compact('student', 'attendance', 'stats', 'startDate', 'endDate'));
    }
}