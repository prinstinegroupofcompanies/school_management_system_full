<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Grade;
use App\Models\Student;
use App\Models\Subject;
use App\Models\ClassRoom;
use App\Events\GradeApproved;
use App\Events\GradeStatusChanged;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class GradeApprovalController extends Controller
{
    public function __construct()
    {
        $this->middleware('admin');
    }

    /**
     * Display grades pending approval
     */
    public function index(Request $request)
    {
        try {
            $query = Grade::with(['student.user', 'subject', 'class', 'teacher.user'])
                     ->where('status', 'pending');

        // Apply filters
        if ($request->filled('class_id')) {
            $query->where('class_id', $request->class_id);
        }
        
        if ($request->filled('subject_id')) {
            $query->where('subject_id', $request->subject_id);
        }
        
        if ($request->filled('teacher_id')) {
            $query->where('teacher_id', $request->teacher_id);
        }

        $grades = $query->orderBy('created_at', 'desc')->paginate(20);

        // Get filter options
        $classes = ClassRoom::all();
        $subjects = Subject::with('teacher.user')->get();
        $teachers = \App\Models\Teacher::with('user')->get();

        // Get summary statistics
        $stats = [
            'pending_grades' => Grade::where('status', 'pending')->count(),
            'approved_grades' => Grade::where('status', 'approved')->count(),
            'rejected_grades' => Grade::where('status', 'rejected')->count(),
            'total_grades' => Grade::count(),
        ];

        return view('admin.grades.approval', compact('grades', 'classes', 'subjects', 'teachers', 'stats'));
        } catch (\Exception $e) {
            \Log::error('GradeApprovalController index error: ' . $e->getMessage());
            $grades = collect()->paginate(20);
            $classes = collect();
            $subjects = collect();
            $teachers = collect();
            $stats = [
                'pending_grades' => 0,
                'approved_grades' => 0,
                'rejected_grades' => 0,
                'total_grades' => 0,
            ];
            return view('admin.grades.approval', compact('grades', 'classes', 'subjects', 'teachers', 'stats'));
        }
    }

    /**
     * Display grades analytics dashboard
     */
    public function analytics(Request $request)
    {
        // Get date range from request or default to 2025 (where the data is)
        $year = $request->get('year', 2025);
        $semester = $request->get('semester', null);
        
        // Base query for grades in the specified year
        $baseQuery = Grade::where('academic_year', $year);
        
        if ($semester) {
            $baseQuery->where('semester', $semester);
        }

        // Overall statistics
        $totalGrades = $baseQuery->count();
        $pendingGrades = $baseQuery->where('status', 'pending')->count();
        $approvedGrades = $baseQuery->where('status', 'approved')->count();
        $rejectedGrades = $baseQuery->where('status', 'rejected')->count();

        // Grade distribution by status
        $statusDistribution = [
            'pending' => $pendingGrades,
            'approved' => $approvedGrades,
            'rejected' => $rejectedGrades,
        ];

        // Performance statistics
        $approvedGradesQuery = $baseQuery->where('status', 'approved');
        $averageYearGrade = $approvedGradesQuery->avg('year_avg');
        $averageSem1Grade = $approvedGradesQuery->avg('sem1_avg');
        $averageSem2Grade = $approvedGradesQuery->avg('sem2_avg');

        // Grade distribution by ranges
        $gradeRanges = [
            'A+ (90-100)' => $approvedGradesQuery->whereBetween('year_avg', [90, 100])->count(),
            'A (80-89)' => $approvedGradesQuery->whereBetween('year_avg', [80, 89.99])->count(),
            'B+ (70-79)' => $approvedGradesQuery->whereBetween('year_avg', [70, 79.99])->count(),
            'B (60-69)' => $approvedGradesQuery->whereBetween('year_avg', [60, 69.99])->count(),
            'C+ (50-59)' => $approvedGradesQuery->whereBetween('year_avg', [50, 59.99])->count(),
            'C (40-49)' => $approvedGradesQuery->whereBetween('year_avg', [40, 49.99])->count(),
            'D (0-39)' => $approvedGradesQuery->whereBetween('year_avg', [0, 39.99])->count(),
        ];

        // Top performing students
        $topStudents = $approvedGradesQuery
            ->with(['student.user', 'subject', 'class'])
            ->orderBy('year_avg', 'desc')
            ->limit(10)
            ->get()
            ->groupBy('student_id')
            ->map(function ($grades) {
                $student = $grades->first()->student;
                return [
                    'student' => $student,
                    'average' => round($grades->avg('year_avg'), 2),
                    'subject_count' => $grades->count(),
                ];
            })
            ->sortByDesc('average')
            ->take(10);

        // Subject-wise statistics
        $subjectStats = $approvedGradesQuery
            ->with('subject')
            ->get()
            ->groupBy('subject_id')
            ->map(function ($grades) {
                $subject = $grades->first()->subject;
                return [
                    'subject' => $subject,
                    'count' => $grades->count(),
                    'average' => round($grades->avg('year_avg'), 2),
                    'highest' => round($grades->max('year_avg'), 2),
                    'lowest' => round($grades->min('year_avg'), 2),
                ];
            });

        // Class-wise statistics
        $classStats = $approvedGradesQuery
            ->with('class')
            ->get()
            ->groupBy('class_id')
            ->map(function ($grades) {
                $class = $grades->first()->class;
                return [
                    'class' => $class,
                    'count' => $grades->count(),
                    'average' => round($grades->avg('year_avg'), 2),
                    'student_count' => $grades->unique('student_id')->count(),
                ];
            });

        // Teacher performance statistics
        $teacherStats = $approvedGradesQuery
            ->with('teacher.user')
            ->get()
            ->groupBy('teacher_id')
            ->map(function ($grades) {
                $teacher = $grades->first()->teacher;
                return [
                    'teacher' => $teacher,
                    'grades_count' => $grades->count(),
                    'average' => round($grades->avg('year_avg'), 2),
                    'pending_count' => Grade::where('teacher_id', $teacher->id)
                        ->where('status', 'pending')
                        ->where('academic_year', $year)
                        ->count(),
                ];
            });

        // Monthly trends (approvals)
        $monthlyTrends = [];
        for ($month = 1; $month <= 12; $month++) {
            $monthlyTrends[$month] = Grade::where('academic_year', $year)
                ->where('status', 'approved')
                ->whereMonth('approved_at', $month)
                ->count();
        }

        // Get filter options
        $years = range(date('Y') - 5, date('Y') + 1);
        $classes = ClassRoom::all();
        $subjects = Subject::all();

        $stats = [
            'total_grades' => $totalGrades,
            'pending_grades' => $pendingGrades,
            'approved_grades' => $approvedGrades,
            'rejected_grades' => $rejectedGrades,
            'average_year_grade' => round($averageYearGrade ?? 0, 2),
            'average_sem1_grade' => round($averageSem1Grade ?? 0, 2),
            'average_sem2_grade' => round($averageSem2Grade ?? 0, 2),
            'approval_rate' => $totalGrades > 0 ? round(($approvedGrades / $totalGrades) * 100, 2) : 0,
        ];

        return view('admin.grades.analytics', compact(
            'stats',
            'statusDistribution',
            'gradeRanges',
            'topStudents',
            'subjectStats',
            'classStats',
            'teacherStats',
            'monthlyTrends',
            'years',
            'classes',
            'subjects',
            'year',
            'semester'
        ));
    }

    /**
     * Get real-time analytics data via AJAX
     */
    public function getAnalyticsData(Request $request)
    {
        try {
            $year = $request->get('year', 2025);
            $semester = $request->get('semester', null);
            
            // Check if grades table exists and has data
            try {
                $baseQuery = Grade::where('academic_year', $year);
            } catch (\Exception $e) {
                // Return empty data if table doesn't exist
                return response()->json([
                    'stats' => [
                        'total_grades' => 0,
                        'pending_grades' => 0,
                        'approved_grades' => 0,
                        'rejected_grades' => 0,
                        'average_year_grade' => 0,
                        'approval_rate' => 0,
                    ],
                    'statusDistribution' => [
                        'pending' => 0,
                        'approved' => 0,
                        'rejected' => 0,
                    ],
                    'gradeRanges' => [],
                    'topStudents' => [],
                    'teacherStats' => [],
                    'monthlyTrends' => [],
                    'timestamp' => now()->toISOString()
                ]);
            }
        
        if ($semester) {
            $baseQuery->where('semester', $semester);
        }

        // Overall statistics - create separate queries to avoid query modification
        $totalGrades = $baseQuery->count();
        $pendingGrades = Grade::where('academic_year', $year)
            ->when($semester, function($query) use ($semester) {
                return $query->where('semester', $semester);
            })
            ->where('status', 'pending')->count();
        $approvedGrades = Grade::where('academic_year', $year)
            ->when($semester, function($query) use ($semester) {
                return $query->where('semester', $semester);
            })
            ->where('status', 'approved')->count();
        $rejectedGrades = Grade::where('academic_year', $year)
            ->when($semester, function($query) use ($semester) {
                return $query->where('semester', $semester);
            })
            ->where('status', 'rejected')->count();

        // Performance statistics
        $approvedGradesQuery = Grade::where('academic_year', $year)
            ->when($semester, function($query) use ($semester) {
                return $query->where('semester', $semester);
            })
            ->where('status', 'approved');
        $averageYearGrade = $approvedGradesQuery->avg('year_avg');

        // Grade distribution by ranges - create separate queries for each range
        $gradeRanges = [
            'A+ (90-100)' => Grade::where('academic_year', $year)
                ->when($semester, function($query) use ($semester) {
                    return $query->where('semester', $semester);
                })
                ->where('status', 'approved')
                ->whereBetween('year_avg', [90, 100])->count(),
            'A (80-89)' => Grade::where('academic_year', $year)
                ->when($semester, function($query) use ($semester) {
                    return $query->where('semester', $semester);
                })
                ->where('status', 'approved')
                ->whereBetween('year_avg', [80, 89.99])->count(),
            'B+ (70-79)' => Grade::where('academic_year', $year)
                ->when($semester, function($query) use ($semester) {
                    return $query->where('semester', $semester);
                })
                ->where('status', 'approved')
                ->whereBetween('year_avg', [70, 79.99])->count(),
            'B (60-69)' => Grade::where('academic_year', $year)
                ->when($semester, function($query) use ($semester) {
                    return $query->where('semester', $semester);
                })
                ->where('status', 'approved')
                ->whereBetween('year_avg', [60, 69.99])->count(),
            'C+ (50-59)' => Grade::where('academic_year', $year)
                ->when($semester, function($query) use ($semester) {
                    return $query->where('semester', $semester);
                })
                ->where('status', 'approved')
                ->whereBetween('year_avg', [50, 59.99])->count(),
            'C (40-49)' => Grade::where('academic_year', $year)
                ->when($semester, function($query) use ($semester) {
                    return $query->where('semester', $semester);
                })
                ->where('status', 'approved')
                ->whereBetween('year_avg', [40, 49.99])->count(),
            'D (0-39)' => Grade::where('academic_year', $year)
                ->when($semester, function($query) use ($semester) {
                    return $query->where('semester', $semester);
                })
                ->where('status', 'approved')
                ->whereBetween('year_avg', [0, 39.99])->count(),
        ];

        // Top performing students (last 10) - create fresh query
        $topStudents = Grade::where('academic_year', $year)
            ->when($semester, function($query) use ($semester) {
                return $query->where('semester', $semester);
            })
            ->where('status', 'approved')
            ->with(['student.user', 'subject', 'class'])
            ->orderBy('year_avg', 'desc')
            ->limit(10)
            ->get()
            ->groupBy('student_id')
            ->map(function ($grades) {
                $student = $grades->first()->student;
                return [
                    'student' => $student,
                    'average' => round($grades->avg('year_avg'), 2),
                    'subject_count' => $grades->count(),
                ];
            })
            ->sortByDesc('average')
            ->take(10);

        // Teacher performance statistics - create fresh query
        $teacherStats = Grade::where('academic_year', $year)
            ->when($semester, function($query) use ($semester) {
                return $query->where('semester', $semester);
            })
            ->where('status', 'approved')
            ->with('teacher.user')
            ->get()
            ->groupBy('teacher_id')
            ->map(function ($grades) use ($year) {
                $teacher = $grades->first()->teacher;
                return [
                    'teacher' => $teacher,
                    'grades_count' => $grades->count(),
                    'average' => round($grades->avg('year_avg'), 2),
                    'pending_count' => Grade::where('teacher_id', $teacher->id)
                        ->where('status', 'pending')
                        ->where('academic_year', $year)
                        ->count(),
                ];
            });

        // Monthly trends (approvals)
        $monthlyTrends = [];
        for ($month = 1; $month <= 12; $month++) {
            $monthlyTrends[$month] = Grade::where('academic_year', $year)
                ->where('status', 'approved')
                ->whereMonth('approved_at', $month)
                ->count();
        }


        return response()->json([
            'stats' => [
                'total_grades' => $totalGrades,
                'pending_grades' => $pendingGrades,
                'approved_grades' => $approvedGrades,
                'rejected_grades' => $rejectedGrades,
                'average_year_grade' => round($averageYearGrade ?? 0, 2),
                'approval_rate' => $totalGrades > 0 ? round(($approvedGrades / $totalGrades) * 100, 2) : 0,
            ],
            'statusDistribution' => [
                'pending' => $pendingGrades,
                'approved' => $approvedGrades,
                'rejected' => $rejectedGrades,
            ],
            'gradeRanges' => $gradeRanges,
            'topStudents' => $topStudents->values(),
            'teacherStats' => $teacherStats->values(),
            'monthlyTrends' => $monthlyTrends,
            'timestamp' => now()->toISOString()
        ]);
        } catch (\Exception $e) {
            \Log::error('Analytics data error: ' . $e->getMessage(), [
                'year' => $year ?? 'unknown',
                'semester' => $semester ?? 'unknown',
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'error' => 'Failed to fetch analytics data',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Approve single grade
     */
    public function approve(Grade $grade)
    {
        $previousStatus = $grade->status;
        
        $grade->update([
            'status' => 'approved',
            'approved_by' => auth()->id(),
            'approved_at' => now(),
        ]);

        // Fire events
        event(new GradeStatusChanged($grade, $previousStatus, 'approved'));
        event(new GradeApproved($grade));

        return back()->with('success', 'Grade approved successfully.');
    }

    /**
     * Reject single grade
     */
    public function reject(Request $request, Grade $grade)
    {
        $request->validate([
            'rejection_reason' => 'nullable|string|max:500',
        ]);

        $previousStatus = $grade->status;

        $grade->update([
            'status' => 'rejected',
            'approved_by' => auth()->id(),
            'approved_at' => now(),
        ]);

        // Fire event
        event(new GradeStatusChanged($grade, $previousStatus, 'rejected'));

        return back()->with('success', 'Grade rejected successfully.');
    }

    /**
     * Bulk approve grades
     */
    public function bulkApprove(Request $request)
    {
        $request->validate([
            'grade_ids' => 'required|array',
            'grade_ids.*' => 'exists:grades,id',
        ]);

        $grades = Grade::whereIn('id', $request->grade_ids)
                      ->where('status', 'pending')
                      ->get();

        $updated = 0;
        foreach ($grades as $grade) {
            $previousStatus = $grade->status;
            
            $grade->update([
                'status' => 'approved',
                'approved_by' => auth()->id(),
                'approved_at' => now(),
            ]);

            // Fire events for each grade
            event(new GradeStatusChanged($grade, $previousStatus, 'approved'));
            event(new GradeApproved($grade));
            
            $updated++;
        }

        return back()->with('success', "Successfully approved {$updated} grades.");
    }

    /**
     * Bulk reject grades
     */
    public function bulkReject(Request $request)
    {
        $request->validate([
            'grade_ids' => 'required|array',
            'grade_ids.*' => 'exists:grades,id',
            'rejection_reason' => 'nullable|string|max:500',
        ]);

        $grades = Grade::whereIn('id', $request->grade_ids)
                      ->where('status', 'pending')
                      ->get();

        $updated = 0;
        foreach ($grades as $grade) {
            $previousStatus = $grade->status;
            
            $grade->update([
                'status' => 'rejected',
                'approved_by' => auth()->id(),
                'approved_at' => now(),
            ]);

            // Fire event for each grade
            event(new GradeStatusChanged($grade, $previousStatus, 'rejected'));
            
            $updated++;
        }

        return back()->with('success', "Successfully rejected {$updated} grades.");
    }

    /**
     * Show all grades (approved, rejected, pending)
     */
    public function allGrades(Request $request)
    {
        $query = Grade::with(['student.user', 'subject', 'class', 'teacher.user', 'approvedBy']);

        // Apply filters
        if ($request->filled('class_id')) {
            $query->where('class_id', $request->class_id);
        }
        
        if ($request->filled('subject_id')) {
            $query->where('subject_id', $request->subject_id);
        }
        
        if ($request->filled('teacher_id')) {
            $query->where('teacher_id', $request->teacher_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $grades = $query->orderBy('created_at', 'desc')->paginate(20);

        // Get filter options
        $classes = ClassRoom::all();
        $subjects = Subject::with('teacher.user')->get();
        $teachers = \App\Models\Teacher::with('user')->get();

        return view('admin.grades.all', compact('grades', 'classes', 'subjects', 'teachers'));
    }

    /**
     * Show grade details
     */
    public function show(Grade $grade)
    {
        $grade->load(['student.user', 'subject', 'class', 'teacher.user', 'approvedBy']);
        
        return view('admin.grades.show', compact('grade'));
    }
}