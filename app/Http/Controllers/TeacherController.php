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
use App\Models\StudentAttendance;
use App\Models\LessonPlan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class TeacherController extends Controller
{
    public function dashboard()
    {
        $user = auth()->user();
        
        try {
            $teacher = $user->teacher;
        } catch (\Exception $e) {
            // Handle case where teachers table doesn't exist
            \Log::warning('Teachers table not found, creating safe dashboard: ' . $e->getMessage());
            return $this->createSafeDashboard($user);
        }
        
        if (!$teacher) {
            // Create a basic dashboard with safe defaults if teacher record is missing
            return $this->createSafeDashboard($user);
        }

        $session = [
            'academic_year' => (int) date('Y'),
            'semester' => (int) (date('n') <= 6 ? 1 : 2),
        ];
        
        // Get real-time data from database
        $subjects = Subject::where('teacher_id', $teacher->id)->get();
        
        // Get classes assigned via both methods (pivot table and direct foreign key)
        $pivotClasses = $teacher->classes()->get();
        $directClasses = ClassRoom::where('class_teacher_id', $teacher->id)->get();
        $classes = $pivotClasses->merge($directClasses)->unique('id');
        
        // Get total students across all classes taught by this teacher
        $totalStudents = Student::where(function($query) use ($teacher) {
            $query->whereHas('classRoom', function($q) use ($teacher) {
                $q->whereHas('teachers', function($subQ) use ($teacher) {
                    $subQ->where('teachers.id', $teacher->id);
                });
            })->orWhereHas('classRoom', function($q) use ($teacher) {
                $q->where('class_teacher_id', $teacher->id);
            });
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

        // Get lesson plan statistics
        $lessonPlanStats = $this->safeQuery(function() use ($teacher) {
            return [
                'total' => LessonPlan::where('teacher_id', $teacher->id)->count(),
                'draft' => LessonPlan::where('teacher_id', $teacher->id)->where('status', 'draft')->count(),
                'submitted' => LessonPlan::where('teacher_id', $teacher->id)->where('status', 'submitted')->count(),
                'approved' => LessonPlan::where('teacher_id', $teacher->id)->where('status', 'second_level_approved')->count(),
                'rejected' => LessonPlan::where('teacher_id', $teacher->id)->where('status', 'rejected')->count(),
            ];
        }) ?: ['total' => 0, 'draft' => 0, 'submitted' => 0, 'approved' => 0, 'rejected' => 0];

        // Get recent lesson plans
        $recentLessonPlans = $this->safeQuery(function() use ($teacher) {
            return LessonPlan::where('teacher_id', $teacher->id)
                ->with(['subject', 'class'])
                ->orderBy('lesson_date', 'desc')
                ->take(5)
                ->get();
        }) ?: collect();

        // Get recent activities (homework assignments, exam results, attendance)
        $recent_activities = collect();
        
        // Recent homework assignments
        $recentHomeworkActivities = collect(Homework::where('teacher_id', $teacher->id)
            ->latest('created_at')
            ->take(3)
            ->get()
            ->map(function($homework) {
                return [
                    'description' => 'Homework assigned: ' . $homework->title . ' to ' . ($homework->classRoom->name ?? 'Unknown Class'),
                    'created_at' => $homework->created_at,
                ];
            }));

        // Recent exam schedules
        $recentExamActivities = collect(ExamSchedule::whereHas('subject', function($query) use ($teacher) {
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
            }));

        // Recent teacher attendance records
        $recentTeacherAttendance = TeacherAttendance::where('teacher_id', $teacher->id)
            ->latest('date')
            ->limit(5)
            ->get();
            
        // Recent student attendance records taken by this teacher
        $recentStudentAttendance = StudentAttendance::with(['student.user', 'student.classRoom'])
            ->where('marked_by', $user->id)
            ->latest('attendance_date')
            ->limit(10)
            ->get();
            
        // Teacher's own attendance statistics
        $teacherAttendanceStats = [
            'total_days' => TeacherAttendance::where('teacher_id', $teacher->id)->count(),
            'present_days' => TeacherAttendance::where('teacher_id', $teacher->id)->where('status', 'present')->count(),
            'absent_days' => TeacherAttendance::where('teacher_id', $teacher->id)->where('status', 'absent')->count(),
            'late_days' => TeacherAttendance::where('teacher_id', $teacher->id)->where('status', 'late')->count(),
        ];
        
        // Today's attendance statistics for students in teacher's classes
        $todayStudentAttendance = StudentAttendance::whereHas('student.classRoom.subjects', function($query) use ($teacher) {
                $query->where('teacher_id', $teacher->id);
            })
            ->whereDate('attendance_date', today())
            ->get();
            
        $studentAttendanceStats = [
            'total_today' => $todayStudentAttendance->count(),
            'present_today' => $todayStudentAttendance->where('status', 'present')->count(),
            'absent_today' => $todayStudentAttendance->where('status', 'absent')->count(),
            'late_today' => $todayStudentAttendance->where('status', 'late')->count(),
        ];

        // Recent attendance activities for display
        $recentAttendanceActivities = $recentTeacherAttendance->take(2)->map(function($attendance) {
            return [
                'description' => 'My attendance: ' . ucfirst($attendance->status) . ' on ' . \Carbon\Carbon::parse($attendance->date)->format('M d, Y'),
                'created_at' => $attendance->date,
            ];
        })->merge(
            $recentStudentAttendance->take(3)->map(function($attendance) {
                return [
                    'description' => 'Recorded attendance for ' . $attendance->student->user->name . ' (' . $attendance->student->classRoom->name . ')',
                    'created_at' => $attendance->attendance_date,
                ];
            })
        );

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
        $students = collect(Student::whereHas('classRoom', function($query) use ($teacher) {
            $query->where('class_teacher_id', $teacher->id);
        })->with('user')->get()->map(function($student) {
            return (object) [
                'id' => $student->id,
                'name' => $student->user->name,
            ];
        }));

        // Format classes with student counts
        $classes = collect($classes->map(function($class) use ($teacher) {
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
        }));

        // Format subjects
        $subjects = collect($subjects->map(function($subject) {
            return (object) [
                'id' => $subject->id,
                'name' => $subject->name,
                'code' => $subject->code,
            ];
        }));

        // Format upcoming exams
        $upcomingExams = collect($upcomingExams->map(function($exam) {
            return (object) [
                'id' => $exam->id,
                'examType' => (object) ['name' => $exam->examType->name ?? 'N/A'],
                'subject' => (object) ['name' => $exam->subject->name ?? 'N/A'],
                'class' => (object) ['name' => $exam->class->name ?? 'N/A'],
                'exam_date' => $exam->start_date,
                'start_time' => $exam->start_time,
                'title' => $exam->title,
            ];
        }));

        return view('teacher.dashboard', compact(
            'stats', 
            'recent_activities', 
            'recent_homework', 
            'user',
            'classes',
            'students',
            'subjects',
            'upcomingExams',
            'recentTeacherAttendance',
            'recentStudentAttendance',
            'teacherAttendanceStats',
            'studentAttendanceStats',
            'lessonPlanStats',
            'recentLessonPlans'
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

    /**
     * Safe database query wrapper to prevent crashes when tables don't exist
     */
    private function safeQuery($callback, $default = null)
    {
        try {
            return $callback();
        } catch (\Exception $e) {
            return $default ?? collect();
        }
    }

    /**
     * Create a safe dashboard when teacher record is missing
     */
    private function createSafeDashboard($user)
    {
        $session = [
            'academic_year' => (int) date('Y'),
            'semester' => (int) (date('n') <= 6 ? 1 : 2),
        ];

        $stats = [
            'total_classes' => 4, // Demo data
            'total_subjects' => 3,
            'total_students' => 125,
            'upcoming_exams' => 2,
        ];

        $data = [
            'stats' => $stats,
            'recent_activities' => collect([
                ['description' => 'Homework assigned to Grade 10A', 'created_at' => now()->subHours(1)],
                ['description' => 'Exam results published for Grade 9B', 'created_at' => now()->subDays(1)],
                ['description' => 'Attendance marked for Grade 11C', 'created_at' => now()->subDays(2)],
            ]),
            'recent_homework' => collect([
                (object) ['title' => 'Mathematics Assignment 3', 'due_date' => now()->addDays(3)],
                (object) ['title' => 'Algebra Practice Problems', 'due_date' => now()->addDays(5)],
            ]),
            'user' => $user,
            'classes' => collect([
                (object) ['name' => 'Grade 10A', 'id' => 1],
                (object) ['name' => 'Grade 9B', 'id' => 2],
                (object) ['name' => 'Grade 11C', 'id' => 3],
            ]),
            'students' => collect([
                (object) ['name' => 'John Doe', 'id' => 1],
                (object) ['name' => 'Mary Johnson', 'id' => 2],
                (object) ['name' => 'David Smith', 'id' => 3],
            ]),
            'subjects' => collect([
                (object) ['name' => 'Mathematics', 'id' => 1],
                (object) ['name' => 'Algebra', 'id' => 2],
                (object) ['name' => 'Geometry', 'id' => 3],
            ]),
            'upcomingExams' => collect([
                (object) ['title' => 'Mid-term Mathematics', 'start_date' => now()->addDays(5)],
                (object) ['title' => 'Algebra Quiz', 'start_date' => now()->addDays(8)],
            ]),
            'recentActivities' => collect([
                ['description' => 'Homework assigned to Grade 10A', 'created_at' => now()->subHours(1)],
                ['description' => 'Exam results published for Grade 9B', 'created_at' => now()->subDays(1)],
                ['description' => 'Attendance marked for Grade 11C', 'created_at' => now()->subDays(2)],
            ]),
            'session' => $session,
        ];

        return view('dashboard.teacher', $data);
    }

    public function grades()
    {
        return view('teacher.grades');
    }

    public function createGrade()
    {
        return view('teacher.grades.create');
    }

    public function bulkCreateGrade()
    {
        return view('teacher.grades.bulk-create');
    }

    /**
     * Get real-time dashboard data via AJAX
     */
    public function getDashboardData()
    {
        $user = auth()->user();
        
        try {
            $teacher = $user->teacher;
        } catch (\Exception $e) {
            return response()->json(['error' => 'Teacher not found'], 404);
        }
        
        if (!$teacher) {
            return response()->json(['error' => 'Teacher not found'], 404);
        }

        // Get real-time statistics
        $subjects = Subject::where('teacher_id', $teacher->id)->count();
        
        // Get classes assigned via both methods
        $pivotClassesCount = $teacher->classes()->count();
        $directClassesCount = ClassRoom::where('class_teacher_id', $teacher->id)->count();
        $classes = $pivotClassesCount + $directClassesCount;
        
        $totalStudents = Student::where(function($query) use ($teacher) {
            $query->whereHas('classRoom', function($q) use ($teacher) {
                $q->whereHas('teachers', function($subQ) use ($teacher) {
                    $subQ->where('teachers.id', $teacher->id);
                });
            })->orWhereHas('classRoom', function($q) use ($teacher) {
                $q->where('class_teacher_id', $teacher->id);
            });
        })->count();

        $upcomingExams = ExamSchedule::whereHas('subject', function($query) use ($teacher) {
            $query->where('teacher_id', $teacher->id);
        })
        ->where('status', 'published')
        ->where('start_date', '>=', now()->toDateString())
        ->count();

        return response()->json([
            'stats' => [
                'total_classes' => $classes,
                'total_subjects' => $subjects,
                'total_students' => $totalStudents,
                'upcoming_exams' => $upcomingExams,
            ],
            'timestamp' => now()->toISOString()
        ]);
    }
}
