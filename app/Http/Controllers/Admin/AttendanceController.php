<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\StudentAttendance;
use App\Models\TeacherAttendance;
use App\Models\Student;
use App\Models\Teacher;
use App\Models\ClassRoom;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AttendanceController extends Controller
{
    public function __construct()
    {
        $this->middleware('admin');
    }

    /**
     * Display admin attendance dashboard with both student and teacher records
     */
    public function index(Request $request)
    {
        try {
            // Get filter parameters
            $classId = $request->get('class_id');
            $month = $request->get('month', now()->format('Y-m'));
            $type = $request->get('type', 'all'); // all, student, teacher
            $status = $request->get('status');

            // Calculate date range for the month
            $startDate = Carbon::parse($month)->startOfMonth();
            $endDate = Carbon::parse($month)->endOfMonth();

            // Get student attendance records
            $studentQuery = StudentAttendance::with(['student.user', 'class', 'markedBy']);
            if ($classId) {
                $studentQuery->where('class_id', $classId);
            }
            if ($status) {
                $studentQuery->where('status', $status);
            }
            $studentQuery->whereBetween('attendance_date', [$startDate, $endDate]);
            $studentRecords = $studentQuery->orderBy('attendance_date', 'desc')->paginate(25, ['*'], 'student_page');

            // Get teacher attendance records
            $teacherQuery = TeacherAttendance::with(['teacher.user']);
            if ($status) {
                $teacherQuery->where('status', $status);
            }
            $teacherQuery->whereBetween('date', [$startDate, $endDate]);
            $teacherRecords = $teacherQuery->orderBy('date', 'desc')->paginate(25, ['*'], 'teacher_page');

            // Get statistics
            $stats = [
                'student' => [
                    'total_today' => StudentAttendance::whereDate('attendance_date', today())->count(),
                    'present_today' => StudentAttendance::whereDate('attendance_date', today())->where('status', 'present')->count(),
                    'absent_today' => StudentAttendance::whereDate('attendance_date', today())->where('status', 'absent')->count(),
                    'late_today' => StudentAttendance::whereDate('attendance_date', today())->where('status', 'late')->count(),
                    'total_month' => StudentAttendance::whereBetween('attendance_date', [$startDate, $endDate])->count(),
                    'present_month' => StudentAttendance::whereBetween('attendance_date', [$startDate, $endDate])->where('status', 'present')->count(),
                ],
                'teacher' => [
                    'total_today' => TeacherAttendance::whereDate('date', today())->count(),
                    'present_today' => TeacherAttendance::whereDate('date', today())->where('status', 'present')->count(),
                    'absent_today' => TeacherAttendance::whereDate('date', today())->where('status', 'absent')->count(),
                    'late_today' => TeacherAttendance::whereDate('date', today())->where('status', 'late')->count(),
                    'total_month' => TeacherAttendance::whereBetween('date', [$startDate, $endDate])->count(),
                    'present_month' => TeacherAttendance::whereBetween('date', [$startDate, $endDate])->where('status', 'present')->count(),
                ]
            ];

            // Get filter options
            $classes = ClassRoom::all();
            $months = $this->getAvailableMonths();

            return view('admin.attendance.index', compact(
                'studentRecords', 'teacherRecords', 'stats', 'classes', 'months', 'classId', 'month', 'type', 'status'
            ));

        } catch (\Exception $e) {
            \Log::error('Admin Attendance Index Error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to load attendance data.');
        }
    }

    /**
     * Display student attendance records
     */
    public function students(Request $request)
    {
        try {
            $query = StudentAttendance::with(['student.user', 'class', 'markedBy']);

            // Apply filters
            if ($request->filled('class_id')) {
                $query->where('class_id', $request->class_id);
            }
            if ($request->filled('month')) {
                $start = Carbon::parse($request->month)->startOfMonth();
                $end = Carbon::parse($request->month)->endOfMonth();
                $query->whereBetween('attendance_date', [$start->toDateString(), $end->toDateString()]);
            }
            if ($request->filled('status')) {
                $query->where('status', $request->status);
            }

            $records = $query->orderByDesc('attendance_date')->paginate(25);
            $classes = ClassRoom::all();

            return view('admin.attendance.students', compact('records', 'classes'));

        } catch (\Exception $e) {
            \Log::error('Admin Student Attendance Error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to load student attendance data.');
        }
    }

    /**
     * Display teacher attendance records
     */
    public function teachers(Request $request)
    {
        try {
            $query = TeacherAttendance::with(['teacher.user']);

            // Apply filters
            if ($request->filled('month')) {
                $start = Carbon::parse($request->month)->startOfMonth();
                $end = Carbon::parse($request->month)->endOfMonth();
                $query->whereBetween('date', [$start->toDateString(), $end->toDateString()]);
            }
            if ($request->filled('status')) {
                $query->where('status', $request->status);
            }

            $records = $query->orderByDesc('date')->paginate(25);

            return view('admin.attendance.teachers', compact('records'));

        } catch (\Exception $e) {
            \Log::error('Admin Teacher Attendance Error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to load teacher attendance data.');
        }
    }

    /**
     * Display combined daily attendance report
     */
    public function dailyReport(Request $request)
    {
        try {
            $date = $request->get('date', today()->format('Y-m-d'));
            
            $studentAttendance = StudentAttendance::where('attendance_date', $date)
                ->with(['student.user', 'class'])
                ->get();
            
            $teacherAttendance = TeacherAttendance::where('date', $date)
                ->with(['teacher.user'])
                ->get();

            $studentStats = $this->calculateDailyStats($studentAttendance);
            $teacherStats = $this->calculateDailyStats($teacherAttendance);

            return view('admin.attendance.daily-report', compact(
                'date', 'studentAttendance', 'teacherAttendance', 'studentStats', 'teacherStats'
            ));

        } catch (\Exception $e) {
            \Log::error('Admin Daily Report Error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to load daily report.');
        }
    }

    /**
     * Display monthly attendance report
     */
    public function monthlyReport(Request $request)
    {
        try {
            $month = $request->get('month', now()->format('Y-m'));
            $startDate = Carbon::parse($month)->startOfMonth();
            $endDate = Carbon::parse($month)->endOfMonth();

            $studentAttendance = StudentAttendance::whereBetween('attendance_date', [$startDate, $endDate])
                ->with(['student.user', 'class'])
                ->get();

            $teacherAttendance = TeacherAttendance::whereBetween('date', [$startDate, $endDate])
                ->with(['teacher.user'])
                ->get();

            $studentStats = $this->calculateMonthlyStats($studentAttendance, $startDate, $endDate);
            $teacherStats = $this->calculateMonthlyStats($teacherAttendance, $startDate, $endDate);

            return view('admin.attendance.monthly-report', compact(
                'month', 'studentAttendance', 'teacherAttendance', 'studentStats', 'teacherStats'
            ));

        } catch (\Exception $e) {
            \Log::error('Admin Monthly Report Error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to load monthly report.');
        }
    }

    /**
     * Mark attendance for students
     */
    public function markStudentAttendance(Request $request)
    {
        $request->validate([
            'class_id' => 'required|exists:class_rooms,id',
            'date' => 'required|date|before_or_equal:today',
            'attendance' => 'required|array',
            'attendance.*.student_id' => 'required|exists:students,id',
            'attendance.*.status' => 'required|in:present,absent,late,excused',
            'attendance.*.remarks' => 'nullable|string',
        ]);

        try {
            DB::beginTransaction();

            $classId = $request->class_id;
            $date = $request->date;

            foreach ($request->attendance as $attendanceData) {
                StudentAttendance::updateOrCreate(
                    [
                        'student_id' => $attendanceData['student_id'],
                        'class_id' => $classId,
                        'attendance_date' => $date,
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

            return redirect()->route('admin.attendance.students')
                ->with('success', 'Student attendance marked successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Mark Student Attendance Error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to mark student attendance.');
        }
    }

    /**
     * Mark attendance for teachers
     */
    public function markTeacherAttendance(Request $request)
    {
        $request->validate([
            'date' => 'required|date|before_or_equal:today',
            'attendance' => 'required|array',
            'attendance.*.teacher_id' => 'required|exists:teachers,id',
            'attendance.*.status' => 'required|in:present,absent,late,excused',
            'attendance.*.remarks' => 'nullable|string',
        ]);

        try {
            DB::beginTransaction();

            $date = $request->date;

            foreach ($request->attendance as $attendanceData) {
                TeacherAttendance::updateOrCreate(
                    [
                        'teacher_id' => $attendanceData['teacher_id'],
                        'date' => $date,
                    ],
                    [
                        'status' => $attendanceData['status'],
                        'remarks' => $attendanceData['remarks'] ?? null,
                        'marked_by' => auth()->id(),
                    ]
                );
            }

            DB::commit();

            return redirect()->route('admin.attendance.teachers')
                ->with('success', 'Teacher attendance marked successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Mark Teacher Attendance Error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to mark teacher attendance.');
        }
    }

    /**
     * Get students for a class to mark attendance
     */
    public function getClassStudents(Request $request)
    {
        $request->validate([
            'class_id' => 'required|exists:class_rooms,id',
            'date' => 'required|date',
        ]);

        try {
            $class = ClassRoom::with(['students.user'])->findOrFail($request->class_id);
            $students = $class->students;

            // Get existing attendance for the date
            $existingAttendance = StudentAttendance::where('class_id', $request->class_id)
                ->where('attendance_date', $request->date)
                ->pluck('status', 'student_id')
                ->toArray();

            return response()->json([
                'success' => true,
                'students' => $students->map(function ($student) use ($existingAttendance) {
                    return [
                        'id' => $student->id,
                        'name' => $student->user->name ?? 'Unknown',
                        'student_id' => $student->student_id ?? 'N/A',
                        'status' => $existingAttendance[$student->id] ?? 'present',
                    ];
                }),
                'class_name' => $class->name,
            ]);

        } catch (\Exception $e) {
            \Log::error('Get Class Students Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to load students.',
            ], 500);
        }
    }

    /**
     * Get all teachers for marking attendance
     */
    public function getTeachers(Request $request)
    {
        try {
            $teachers = Teacher::with('user')->get();
            $date = $request->get('date', today()->format('Y-m-d'));

            // Get existing attendance for the date
            $existingAttendance = TeacherAttendance::where('date', $date)
                ->pluck('status', 'teacher_id')
                ->toArray();

            return response()->json([
                'success' => true,
                'teachers' => $teachers->map(function ($teacher) use ($existingAttendance) {
                    return [
                        'id' => $teacher->id,
                        'name' => $teacher->user->name ?? 'Unknown',
                        'employee_id' => $teacher->employee_id ?? 'N/A',
                        'status' => $existingAttendance[$teacher->id] ?? 'present',
                    ];
                }),
            ]);

        } catch (\Exception $e) {
            \Log::error('Get Teachers Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to load teachers.',
            ], 500);
        }
    }

    /**
     * Calculate daily statistics
     */
    private function calculateDailyStats($attendance)
    {
        $total = $attendance->count();
        $present = $attendance->where('status', 'present')->count();
        $absent = $attendance->where('status', 'absent')->count();
        $late = $attendance->where('status', 'late')->count();
        $excused = $attendance->where('status', 'excused')->count();

        $attendanceRate = $total > 0 ? round((($present + $late + $excused) / $total) * 100, 2) : 0;

        return [
            'total' => $total,
            'present' => $present,
            'absent' => $absent,
            'late' => $late,
            'excused' => $excused,
            'attendance_rate' => $attendanceRate,
        ];
    }

    /**
     * Calculate monthly statistics
     */
    private function calculateMonthlyStats($attendance, $startDate, $endDate)
    {
        $totalDays = $startDate->diffInDays($endDate) + 1;
        $totalRecords = $attendance->count();
        $present = $attendance->where('status', 'present')->count();
        $absent = $attendance->where('status', 'absent')->count();
        $late = $attendance->where('status', 'late')->count();
        $excused = $attendance->where('status', 'excused')->count();

        $attendanceRate = $totalRecords > 0 ? round((($present + $late + $excused) / $totalRecords) * 100, 2) : 0;

        return [
            'total_days' => $totalDays,
            'total_records' => $totalRecords,
            'present' => $present,
            'absent' => $absent,
            'late' => $late,
            'excused' => $excused,
            'attendance_rate' => $attendanceRate,
        ];
    }

    /**
     * Show individual student attendance record
     */
    public function showStudentAttendance(StudentAttendance $studentAttendance)
    {
        try {
            $studentAttendance->load(['student.user', 'class', 'section', 'markedBy']);
            
            return view('admin.attendance.show-student', compact('studentAttendance'));
            
        } catch (\Exception $e) {
            \Log::error('Show Student Attendance Error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to load student attendance record.');
        }
    }

    /**
     * Show individual teacher attendance record
     */
    public function showTeacherAttendance(TeacherAttendance $teacherAttendance)
    {
        try {
            $teacherAttendance->load(['teacher.user', 'markedBy']);
            
            return view('admin.attendance.show-teacher', compact('teacherAttendance'));
            
        } catch (\Exception $e) {
            \Log::error('Show Teacher Attendance Error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to load teacher attendance record.');
        }
    }

    /**
     * Get available months for filtering
     */
    private function getAvailableMonths()
    {
        $months = collect();
        $currentDate = now();
        
        // Generate last 12 months
        for ($i = 0; $i < 12; $i++) {
            $date = $currentDate->copy()->subMonths($i);
            $months->push([
                'value' => $date->format('Y-m'),
                'label' => $date->format('F Y'),
            ]);
        }

        return $months;
    }
}
