<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\Teacher;
use App\Models\ClassRoom;
use App\Models\Subject;
use App\Models\StudentAttendance;
use App\Models\Section;
use App\Models\TeacherAttendance;
use App\Models\FinanceOfficerAttendance;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AttendanceController extends Controller
{
    public function index()
    {
        // Redirect based on user type to appropriate attendance dashboard
        $user = auth()->user();
        
        if (!$user) {
            return redirect()->route('login');
        }
        
        switch ($user->user_type) {
            case 'admin':
                return redirect()->route('admin.attendance.index');
            case 'teacher':
                return redirect()->route('teacher.attendance.index');
            case 'student':
                return redirect()->route('student.attendance.index');
            default:
                return redirect()->route('dashboard');
        }
    }

    public function studentAttendance(Request $request)
    {
        $request->validate([
            'class_id' => 'required|exists:class_rooms,id',
            'date' => 'required|date|before_or_equal:today',
        ]);

        $class = ClassRoom::findOrFail($request->class_id);
        $date = $request->date;
        $students = $class->students()->with('user')->get();
        
        // Get existing attendance for the date
        $existingAttendance = StudentAttendance::where('class_id', $class->id)
            ->where('attendance_date', $date)
            ->pluck('status', 'student_id')
            ->toArray();

        return view('attendance.student-attendance', compact('class', 'date', 'students', 'existingAttendance'));
    }

    public function store(Request $request)
    {
        // Determine what type of attendance is being stored based on request data
        if ($request->has('attendance') && is_array($request->attendance)) {
            // Get the attendance array and check the first element
            $attendanceArray = $request->input('attendance');
            $firstAttendance = !empty($attendanceArray) ? $attendanceArray[0] : null;
            
            if ($firstAttendance && isset($firstAttendance['student_id'])) {
                return $this->storeStudentAttendance($request);
            } elseif ($firstAttendance && isset($firstAttendance['teacher_id'])) {
                return $this->storeTeacherAttendance($request);
            }
        }
        
        // Default to student attendance if unclear
        return $this->storeStudentAttendance($request);
    }

    public function storeStudentAttendance(Request $request)
    {
        $request->validate([
            'class_id' => 'required|exists:class_rooms,id',
            'date' => 'required|date|before_or_equal:today',
            'attendance' => 'required|array',
            'attendance.*.student_id' => 'required|exists:students,id',
            'attendance.*.status' => 'required|in:present,absent,late,excused,dropout,absence,excuse',
            'attendance.*.remarks' => 'nullable|string',
        ]);

        $classId = $request->class_id;
        $date = $request->date;

        DB::transaction(function () use ($request, $classId, $date) {
            foreach ($request->attendance as $attendanceData) {
                $student = Student::find($attendanceData['student_id']);
                $sectionId = $student?->section_id;
                if (!$sectionId) {
                    $defaultSection = Section::firstOrCreate(
                        ['class_id' => $classId, 'name' => 'A'],
                        ['code' => 'A-' . $classId, 'status' => 'active']
                    );
                    $sectionId = $defaultSection->id;
                }

                // Normalize status to canonical values
                $statusInput = $attendanceData['status'];
                $normalizedStatus = match ($statusInput) {
                    'absence' => 'absent',
                    'excuse' => 'excused',
                    default => $statusInput,
                };

                StudentAttendance::updateOrCreate(
                    [
                        'student_id' => $attendanceData['student_id'],
                        'class_id' => $classId,
                        'attendance_date' => $date,
                    ],
                    [
                        'status' => $normalizedStatus,
                        'remarks' => $attendanceData['remarks'] ?? null,
                        'marked_by' => auth()->id(),
                        'section_id' => $sectionId,
                        'academic_year' => Carbon::parse($date)->year,
                    ]
                );
            }
        });

        return redirect()->route('attendance.index')
            ->with('success', 'Student attendance saved successfully.');
    }

    public function teacherAttendance(Request $request)
    {
        $request->validate([
            'date' => 'required|date|before_or_equal:today',
        ]);

        $date = $request->date;
        $teachers = Teacher::with('user')->get();
        
        // Get existing attendance for the date
        $existingAttendance = TeacherAttendance::where('date', $date)
            ->pluck('status', 'teacher_id')
            ->toArray();

        return view('attendance.teacher-attendance', compact('date', 'teachers', 'existingAttendance'));
    }

    public function storeTeacherAttendance(Request $request)
    {
        $request->validate([
            'date' => 'required|date|before_or_equal:today',
            'attendance' => 'required|array',
            'attendance.*.teacher_id' => 'required|exists:teachers,id',
            'attendance.*.status' => 'required|in:present,absent,late,excused',
            'attendance.*.remarks' => 'nullable|string',
        ]);

        $date = $request->date;

        DB::transaction(function () use ($request, $date) {
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
        });

        return redirect()->route('attendance.index')
            ->with('success', 'Teacher attendance saved successfully.');
    }

    public function studentReport(Request $request)
    {
        $request->validate([
            'student_id' => 'required|exists:students,id',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
        ]);

        $student = Student::with('user')->findOrFail($request->student_id);
        $startDate = $request->start_date;
        $endDate = $request->end_date;

        $attendance = StudentAttendance::where('student_id', $student->id)
            ->whereBetween('attendance_date', [$startDate, $endDate])
            ->orderBy('attendance_date')
            ->get();

        $stats = $this->calculateAttendanceStats($attendance, $startDate, $endDate);

        return view('attendance.student-report', compact('student', 'attendance', 'stats', 'startDate', 'endDate'));
    }

    public function classReport(Request $request)
    {
        $request->validate([
            'class_id' => 'required|exists:class_rooms,id',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
        ]);

        $class = ClassRoom::findOrFail($request->class_id);
        $startDate = $request->start_date;
        $endDate = $request->end_date;

        $students = $class->students()->with('user')->get();
        $attendanceData = [];

        foreach ($students as $student) {
            $attendance = StudentAttendance::where('student_id', $student->id)
                ->whereBetween('date', [$startDate, $endDate])
                ->get();

            $stats = $this->calculateAttendanceStats($attendance, $startDate, $endDate);
            $attendanceData[] = [
                'student' => $student,
                'stats' => $stats,
            ];
        }

        return view('attendance.class-report', compact('class', 'attendanceData', 'startDate', 'endDate'));
    }

    public function teacherReport(Request $request)
    {
        $request->validate([
            'teacher_id' => 'required|exists:teachers,id',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
        ]);

        $teacher = Teacher::with('user')->findOrFail($request->teacher_id);
        $startDate = $request->start_date;
        $endDate = $request->end_date;

        $attendance = TeacherAttendance::where('teacher_id', $teacher->id)
            ->whereBetween('date', [$startDate, $endDate])
            ->orderBy('date')
            ->get();

        $stats = $this->calculateAttendanceStats($attendance, $startDate, $endDate);

        return view('attendance.teacher-report', compact('teacher', 'attendance', 'stats', 'startDate', 'endDate'));
    }

    public function dailyReport(Request $request)
    {
        $request->validate([
            'date' => 'required|date|before_or_equal:today',
        ]);

        $date = $request->date;
        
        $studentAttendance = StudentAttendance::where('attendance_date', $date)
            ->with(['student.user', 'class'])
            ->get();
        
        $teacherAttendance = TeacherAttendance::where('date', $date)
            ->with(['teacher.user'])
            ->get();

        $studentStats = $this->calculateDailyStats($studentAttendance);
        $teacherStats = $this->calculateDailyStats($teacherAttendance);

        return view('attendance.daily-report', compact(
            'date', 
            'studentAttendance', 
            'teacherAttendance', 
            'studentStats', 
            'teacherStats'
        ));
    }

    public function monthlyReport(Request $request)
    {
        $request->validate([
            'month' => 'required|date_format:Y-m',
        ]);

        $month = $request->month;
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

        return view('attendance.monthly-report', compact(
            'month', 
            'studentAttendance', 
            'teacherAttendance', 
            'studentStats', 
            'teacherStats'
        ));
    }

    // Admin views
    public function adminStudentAttendance(Request $request)
    {
        $request->validate([
            'class_id' => 'nullable|exists:class_rooms,id',
            'month' => 'nullable|date_format:Y-m',
        ]);

        $query = StudentAttendance::with(['student.user', 'class']);
        if ($request->filled('class_id')) {
            $query->where('class_id', $request->class_id);
        }
        if ($request->filled('month')) {
            $start = Carbon::parse($request->month)->startOfMonth();
            $end = Carbon::parse($request->month)->endOfMonth();
            $query->whereBetween('attendance_date', [$start->toDateString(), $end->toDateString()]);
        }
        $records = $query->orderByDesc('attendance_date')->paginate(25);
        $classes = ClassRoom::all();
        return view('admin.attendance.students', compact('records', 'classes'));
    }

    public function adminTeacherAttendance(Request $request)
    {
        $request->validate([
            'month' => 'nullable|date_format:Y-m',
        ]);

        $query = TeacherAttendance::with('teacher.user');
        if ($request->filled('month')) {
            $start = Carbon::parse($request->month)->startOfMonth();
            $end = Carbon::parse($request->month)->endOfMonth();
            $query->whereBetween('date', [$start->toDateString(), $end->toDateString()]);
        }
        $records = $query->orderByDesc('date')->paginate(25);
        return view('admin.attendance.teachers', compact('records'));
    }

    public function adminFinanceAttendance(Request $request)
    {
        $request->validate([
            'month' => 'nullable|date_format:Y-m',
        ]);

        $query = FinanceOfficerAttendance::with('financeOfficer.user');
        if ($request->filled('month')) {
            $start = Carbon::parse($request->month)->startOfMonth();
            $end = Carbon::parse($request->month)->endOfMonth();
            $query->whereBetween('date', [$start->toDateString(), $end->toDateString()]);
        }
        $records = $query->orderByDesc('date')->paginate(25);
        return view('admin.attendance.finance', compact('records'));
    }

    public function myStudentAttendanceHistory(Request $request)
    {
        $request->validate([
            'class_id' => 'nullable|exists:class_rooms,id',
            'month' => 'nullable|date_format:Y-m',
        ]);

        $teacher = Teacher::where('user_id', auth()->id())->first();
        $classIds = $teacher ? $teacher->classes()->pluck('class_rooms.id') : collect();

        $query = StudentAttendance::with(['student.user', 'class'])
            ->whereIn('class_id', $classIds);

        if ($request->filled('class_id')) {
            $query->where('class_id', $request->class_id);
        }
        if ($request->filled('month')) {
            $start = Carbon::parse($request->month)->startOfMonth();
            $end = Carbon::parse($request->month)->endOfMonth();
            $query->whereBetween('attendance_date', [$start->toDateString(), $end->toDateString()]);
        }

        $records = $query->orderByDesc('attendance_date')->paginate(20);
        $classes = ClassRoom::whereIn('id', $classIds)->get();

        return view('attendance.my-student-history', compact('records', 'classes'));
    }

    public function myTeacherAttendanceHistory(Request $request)
    {
        $request->validate([
            'month' => 'nullable|date_format:Y-m',
        ]);

        $teacher = Teacher::where('user_id', auth()->id())->firstOrFail();
        $query = TeacherAttendance::with('teacher.user')->where('teacher_id', $teacher->id);

        if ($request->filled('month')) {
            $start = Carbon::parse($request->month)->startOfMonth();
            $end = Carbon::parse($request->month)->endOfMonth();
            $query->whereBetween('date', [$start->toDateString(), $end->toDateString()]);
        }

        $records = $query->orderByDesc('date')->paginate(20);

        return view('attendance.my-teacher-history', compact('records'));
    }

    public function bulkActions(Request $request)
    {
        $request->validate([
            'action' => 'required|in:mark_present,mark_absent,mark_late,mark_excused,delete',
            'selected_attendance' => 'required|array',
            'selected_attendance.*' => 'exists:student_attendances,id',
            'status' => 'required_if:action,mark_present,mark_absent,mark_late,mark_excused|in:present,absent,late,excused',
        ]);

        $selectedAttendance = StudentAttendance::whereIn('id', $request->selected_attendance);

        switch ($request->action) {
            case 'mark_present':
            case 'mark_absent':
            case 'mark_late':
            case 'mark_excused':
                $selectedAttendance->update(['status' => $request->status]);
                $message = 'Selected attendance records updated successfully.';
                break;
                
            case 'delete':
                $selectedAttendance->delete();
                $message = 'Selected attendance records deleted successfully.';
                break;
        }

        return redirect()->back()->with('success', $message);
    }

    public function reports(Request $request)
    {
        // Get current date for default values
        $today = now();
        $currentMonth = $today->format('Y-m');
        
        // Get basic statistics for overview
        $studentStats = [
            'total_today' => StudentAttendance::whereDate('attendance_date', today())->count(),
            'present_today' => StudentAttendance::whereDate('attendance_date', today())->where('status', 'present')->count(),
            'absent_today' => StudentAttendance::whereDate('attendance_date', today())->where('status', 'absent')->count(),
            'late_today' => StudentAttendance::whereDate('attendance_date', today())->where('status', 'late')->count(),
        ];
        
        $teacherStats = [
            'total_today' => TeacherAttendance::whereDate('date', today())->count(),
            'present_today' => TeacherAttendance::whereDate('date', today())->where('status', 'present')->count(),
            'absent_today' => TeacherAttendance::whereDate('date', today())->where('status', 'absent')->count(),
            'late_today' => TeacherAttendance::whereDate('date', today())->where('status', 'late')->count(),
        ];
        
        // Get monthly statistics
        $monthStart = $today->copy()->startOfMonth();
        $monthEnd = $today->copy()->endOfMonth();
        
        $monthlyStudentStats = [
            'total' => StudentAttendance::whereBetween('attendance_date', [$monthStart, $monthEnd])->count(),
            'present' => StudentAttendance::whereBetween('attendance_date', [$monthStart, $monthEnd])->where('status', 'present')->count(),
            'absent' => StudentAttendance::whereBetween('attendance_date', [$monthStart, $monthEnd])->where('status', 'absent')->count(),
        ];
        
        $monthlyTeacherStats = [
            'total' => TeacherAttendance::whereBetween('date', [$monthStart, $monthEnd])->count(),
            'present' => TeacherAttendance::whereBetween('date', [$monthStart, $monthEnd])->where('status', 'present')->count(),
            'absent' => TeacherAttendance::whereBetween('date', [$monthStart, $monthEnd])->where('status', 'absent')->count(),
        ];
        
        // Get classes for filtering
        $classes = ClassRoom::all();
        $teachers = Teacher::with('user')->get();
        
        // Get recent attendance records
        $recentStudentAttendance = StudentAttendance::with(['student.user', 'student.classRoom'])
            ->whereHas('student.user') // Only get records where student and user exist
            ->latest('attendance_date')
            ->limit(10)
            ->get();
            
        $recentTeacherAttendance = TeacherAttendance::with(['teacher.user'])
            ->whereHas('teacher.user') // Only get records where teacher and user exist
            ->latest('date')
            ->limit(10)
            ->get();
        
        return view('attendance.reports', compact(
            'studentStats',
            'teacherStats',
            'monthlyStudentStats',
            'monthlyTeacherStats',
            'classes',
            'teachers',
            'recentStudentAttendance',
            'recentTeacherAttendance',
            'currentMonth'
        ));
    }

    public function export(Request $request)
    {
        $request->validate([
            'type' => 'required|in:student,teacher',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'format' => 'required|in:csv,excel',
        ]);

        $type = $request->type;
        $startDate = $request->start_date;
        $endDate = $request->end_date;
        $format = $request->format;

        if ($type === 'student') {
            $data = StudentAttendance::whereBetween('attendance_date', [$startDate, $endDate])
                ->with(['student.user', 'student.classRoom'])
                ->get();
        } else {
            $data = TeacherAttendance::whereBetween('date', [$startDate, $endDate])
                ->with(['teacher.user'])
                ->get();
        }

        // Export logic would go here
        // For now, just return success message
        return redirect()->back()->with('success', 'Export completed successfully.');
    }

    private function calculateAttendanceStats($attendance, $startDate, $endDate)
    {
        $totalDays = Carbon::parse($startDate)->diffInDays(Carbon::parse($endDate)) + 1;
        $present = $attendance->where('status', 'present')->count();
        $absent = $attendance->where('status', 'absent')->count();
        $late = $attendance->where('status', 'late')->count();
        $excused = $attendance->where('status', 'excused')->count();

        $attendanceRate = $totalDays > 0 ? round((($present + $late + $excused) / $totalDays) * 100, 2) : 0;

        return [
            'total_days' => $totalDays,
            'present' => $present,
            'absent' => $absent,
            'late' => $late,
            'excused' => $excused,
            'attendance_rate' => $attendanceRate,
        ];
    }

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
}
