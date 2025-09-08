<?php

namespace App\Http\Controllers;

use App\Models\Teacher;
use App\Models\User;
use App\Models\Department;
use App\Models\Designation;
use App\Models\Subject;
use App\Models\ClassRoom;
use App\Models\Student;
use App\Models\ExamSchedule;
use App\Models\Homework;
use App\Models\TeacherAttendance;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class TeacherController extends Controller
{
    public function dashboard()
    {
        $user = auth()->user();
        $teacher = $user->teacher;
        
        if (!$teacher) {
            abort(403, 'Teacher record not found');
        }

        $session = [
            'academic_year' => (int) date('Y'),
            'semester' => (int) (date('n') <= 6 ? 1 : 2),
        ];
        
        // Get real-time data from database
        $subjects = Subject::where('teacher_id', $teacher->id)->get();
        $classes = ClassRoom::whereHas('subjects', function($query) use ($teacher) {
            $query->where('teacher_id', $teacher->id);
        })->get();
        
        // Get total students across all classes taught by this teacher
        $totalStudents = Student::whereHas('classRoom.subjects', function($query) use ($teacher) {
            $query->where('teacher_id', $teacher->id);
        })->count();

        // Get upcoming exams for subjects taught by this teacher
        $upcomingExams = ExamSchedule::whereHas('subject', function($query) use ($teacher) {
                $query->where('teacher_id', $teacher->id);
            })
            ->where('status', 'published')
            ->where('start_date', '>=', now()->toDateString())
            ->orderBy('start_date')
            ->take(5)
            ->with(['examType', 'subject', 'class'])
            ->get();

        // Get recent homework assigned by this teacher
        $recent_homework = Homework::where('teacher_id', $teacher->id)
            ->where('due_date', '>=', now())
            ->orderBy('due_date')
            ->take(5)
            ->get();

        // Get recent activities (homework assignments, exam results, attendance)
        $recent_activities = collect();
        
        // Recent homework assignments
        $recentHomeworkActivities = Homework::where('teacher_id', $teacher->id)
            ->latest('created_at')
            ->take(3)
            ->get()
            ->map(function($homework) {
                return [
                    'description' => 'Homework assigned: ' . $homework->title . ' to ' . ($homework->classRoom->name ?? 'Unknown Class'),
                    'created_at' => $homework->created_at,
                ];
            });

        // Recent exam schedules
        $recentExamActivities = ExamSchedule::whereHas('subject', function($query) use ($teacher) {
                $query->where('teacher_id', $teacher->id);
            })
            ->latest('created_at')
            ->take(2)
            ->get()
            ->map(function($exam) {
                return [
                    'description' => 'Exam scheduled: ' . $exam->title . ' for ' . ($exam->class->name ?? 'Unknown Class'),
                    'created_at' => $exam->created_at,
                ];
            });

        // Recent attendance marking
        $recentAttendanceActivities = TeacherAttendance::where('teacher_id', $teacher->id)
            ->latest('date')
            ->take(2)
            ->get()
            ->map(function($attendance) {
                return [
                    'description' => 'Attendance marked: ' . ucfirst($attendance->status) . ' on ' . \Carbon\Carbon::parse($attendance->date)->format('M d, Y'),
                    'created_at' => $attendance->date,
                ];
            });

        // Merge and sort all activities
        $recent_activities = $recentHomeworkActivities
            ->merge($recentExamActivities)
            ->merge($recentAttendanceActivities)
            ->sortByDesc('created_at')
            ->take(5);

        // Calculate stats
        $stats = [
            'total_classes' => $classes->count(),
            'total_subjects' => $subjects->count(),
            'total_students' => $totalStudents,
            'upcoming_exams' => $upcomingExams->count(),
        ];

        // Get all students from classes taught by this teacher
        $students = Student::whereHas('classRoom.subjects', function($query) use ($teacher) {
            $query->where('teacher_id', $teacher->id);
        })->with('user')->get()->map(function($student) {
            return (object) [
                'id' => $student->id,
                'name' => $student->user->name,
            ];
        });

        // Format classes with student counts
        $classes = $classes->map(function($class) use ($teacher) {
            $studentCount = Student::where('class_id', $class->id)->count();
            return (object) [
                'id' => $class->id,
                'name' => $class->name,
                'code' => $class->code ?? $class->name,
                'students_count' => $studentCount,
                'students' => Student::where('class_id', $class->id)
                    ->with('user')
                    ->get()
                    ->map(function($student) {
                        return (object) [
                            'id' => $student->id,
                            'name' => $student->user->name,
                        ];
                    }),
            ];
        });

        // Format subjects
        $subjects = $subjects->map(function($subject) {
            return (object) [
                'id' => $subject->id,
                'name' => $subject->name,
                'code' => $subject->code,
            ];
        });

        // Format upcoming exams
        $upcomingExams = $upcomingExams->map(function($exam) {
            return (object) [
                'id' => $exam->id,
                'examType' => (object) ['name' => $exam->examType->name ?? 'N/A'],
                'subject' => (object) ['name' => $exam->subject->name ?? 'N/A'],
                'class' => (object) ['name' => $exam->class->name ?? 'N/A'],
                'exam_date' => $exam->start_date,
                'start_time' => $exam->start_time,
                'title' => $exam->title,
            ];
        });

        return view('dashboard.teacher', compact(
            'stats', 
            'recent_activities', 
            'recent_homework', 
            'user',
            'classes',
            'students',
            'subjects',
            'upcomingExams'
        ) + ['recentActivities' => $recent_activities, 'session' => $session]);
    }

    private function getUpcomingExams($teacher)
    {
        return ExamSchedule::whereHas('subject', function($query) use ($teacher) {
                $query->where('teacher_id', $teacher->id);
            })
            ->where('exam_date', '>=', now())
            ->orderBy('exam_date')
            ->limit(5)
            ->get();
    }

    private function getRecentActivities($teacher)
    {
        return collect([
            [
                'description' => 'New homework assigned to Class 10A',
                'created_at' => now()->subHours(1),
            ],
            [
                'description' => 'Exam results published',
                'created_at' => now()->subDays(1),
            ],
            [
                'description' => 'Attendance marked for Class 9B',
                'created_at' => now()->subDays(2),
            ],
        ]);
    }

    private function getRecentHomework($teacher)
    {
        return Homework::where('teacher_id', $teacher->id)
            ->where('due_date', '>=', now())
            ->orderBy('due_date')
            ->limit(5)
            ->get();
    }

    public function index(Request $request)
    {
        $query = Teacher::with(['user', 'department', 'designation']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('employee_id', 'like', "%{$search}%")
                  ->orWhereHas('user', function ($userQuery) use ($search) {
                      $userQuery->where('name', 'like', "%{$search}%");
                  });
            });
        }

        if ($request->filled('department_id')) {
            $query->where('department_id', $request->department_id);
        }

        if ($request->filled('employment_status')) {
            $query->where('employment_status', $request->employment_status);
        }

        $teachers = $query->paginate(15);
        $departments = Department::all();

        return view('teachers.index', compact('teachers', 'departments'));
    }

    public function create()
    {
        $departments = Department::all();
        $designations = Designation::all();
        
        return view('teachers.create', compact('departments', 'designations'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'employee_id' => 'required|string|unique:teachers,employee_id',
            'department_id' => 'nullable|exists:departments,id',
            'designation_id' => 'nullable|exists:designations,id',
            'joining_date' => 'required|date',
            'basic_salary' => 'required|numeric|min:0',
        ]);

        DB::beginTransaction();

        try {
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'user_type' => 'teacher',
                'phone' => $request->phone,
                'address' => $request->address,
                'status' => 'active',
            ]);

            $teacher = Teacher::create([
                'user_id' => $user->id,
                'employee_id' => $request->employee_id,
                'department_id' => $request->department_id,
                'designation_id' => $request->designation_id,
                'joining_date' => $request->joining_date,
                'basic_salary' => $request->basic_salary,
                'employment_type' => 'full_time',
                'employment_status' => 'active',
                'currency' => 'LRD',
            ]);

            DB::commit();

            return redirect()->route('teachers.index')
                ->with('success', 'Teacher created successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()
                ->with('error', 'Failed to create teacher: ' . $e->getMessage());
        }
    }

    public function show(Teacher $teacher)
    {
        $teacher->load(['user', 'department', 'designation']);
        return view('teachers.show', compact('teacher'));
    }

    public function edit(Teacher $teacher)
    {
        $departments = Department::all();
        $designations = Designation::all();
        
        return view('teachers.edit', compact('teacher', 'departments', 'designations'));
    }

    public function update(Request $request, Teacher $teacher)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $teacher->user_id,
            'department_id' => 'nullable|exists:departments,id',
            'designation_id' => 'nullable|exists:designations,id',
            'basic_salary' => 'required|numeric|min:0',
        ]);

        try {
            $teacher->user->update([
                'name' => $request->name,
                'email' => $request->email,
            ]);

            $teacher->update([
                'department_id' => $request->department_id,
                'designation_id' => $request->designation_id,
                'basic_salary' => $request->basic_salary,
            ]);

            return redirect()->route('teachers.index')
                ->with('success', 'Teacher updated successfully');
        } catch (\Exception $e) {
            return back()->withInput()
                ->with('error', 'Failed to update teacher: ' . $e->getMessage());
        }
    }

    public function destroy(Teacher $teacher)
    {
        try {
            $teacher->user->delete();
            return redirect()->route('teachers.index')
                ->with('success', 'Teacher deleted successfully');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to delete teacher: ' . $e->getMessage());
        }
    }

    public function classes(Teacher $teacher)
    {
        $classes = $teacher->classes()->with(['classRoom'])->paginate(15);
        return view('teachers.classes', compact('teacher', 'classes'));
    }

    public function subjects(Teacher $teacher)
    {
        $subjects = $teacher->subjects()->paginate(15);
        return view('teachers.subjects', compact('teacher', 'subjects'));
    }

    public function attendance(Teacher $teacher)
    {
        $attendance = $teacher->attendance()->latest()->paginate(30);
        return view('teachers.attendance', compact('teacher', 'attendance'));
    }

    public function schedule(Teacher $teacher)
    {
        $schedule = $teacher->schedule()->orderBy('day_of_week')->orderBy('start_time')->get();
        return view('teachers.schedule', compact('teacher', 'schedule'));
    }
}
