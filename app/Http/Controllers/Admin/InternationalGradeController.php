<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\InternationalGrade;
use App\Models\Student;
use App\Models\Subject;
use App\Models\ClassRoom;
use App\Models\Teacher;
use App\Models\StudentActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class InternationalGradeController extends Controller
{
    public function __construct()
    {
        $this->middleware('admin');
    }

    /**
     * Display a listing of grades pending approval
     */
    public function index(Request $request)
    {
        $query = InternationalGrade::with(['student.user', 'subject', 'teacher.user', 'classRoom'])
                                  ->orderBy('submitted_at', 'desc');

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        } else {
            // Default to pending approval
            $query->where('status', 'submitted');
        }

        // Filter by academic year
        if ($request->filled('academic_year')) {
            $query->where('academic_year', $request->academic_year);
        }

        // Filter by semester
        if ($request->filled('semester')) {
            $query->where('semester', $request->semester);
        }

        // Filter by class
        if ($request->filled('class_id')) {
            $query->where('class_id', $request->class_id);
        }

        // Filter by subject
        if ($request->filled('subject_id')) {
            $query->where('subject_id', $request->subject_id);
        }

        $grades = $query->paginate(15);
        $classes = ClassRoom::all();
        $subjects = Subject::all();
        $academicYears = InternationalGrade::distinct()->pluck('academic_year')->sort()->values();

        return view('admin.grades.index', compact('grades', 'classes', 'subjects', 'academicYears'));
    }

    /**
     * Show the form for creating a new grade (admin override)
     */
    public function create()
    {
        $students = Student::with('user', 'classRoom')->get();
        $subjects = Subject::all();
        $teachers = Teacher::with('user')->get();
        $classes = ClassRoom::all();
        
        return view('admin.grades.create', compact('students', 'subjects', 'teachers', 'classes'));
    }

    /**
     * Store a newly created grade (admin override)
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'student_id' => 'required|exists:students,id',
            'subject_id' => 'required|exists:subjects,id',
            'teacher_id' => 'required|exists:teachers,id',
            'class_id' => 'required|exists:class_rooms,id',
            'assessment_type' => 'required|string|max:255',
            'assessment_title' => 'required|string|max:255',
            'assessment_description' => 'nullable|string',
            'assessment_date' => 'required|date',
            'academic_year' => 'required|string|max:10',
            'semester' => 'required|in:fall,spring,summer',
            'raw_score' => 'required|numeric|min:0',
            'max_score' => 'required|numeric|min:0.01',
            'teacher_comments' => 'nullable|string',
            'feedback' => 'nullable|string',
            'weight' => 'nullable|numeric|min:0|max:10',
            'counts_toward_final' => 'boolean',
            'is_extra_credit' => 'boolean',
            'auto_publish' => 'boolean',
        ]);

        DB::beginTransaction();
        try {
            // Set defaults
            $validated['weight'] = $validated['weight'] ?? 1.0;
            $validated['counts_toward_final'] = $request->boolean('counts_toward_final', true);
            $validated['is_extra_credit'] = $request->boolean('is_extra_credit', false);

            // Create grade
            $grade = InternationalGrade::create($validated);

            // If admin chooses to auto-publish
            if ($request->boolean('auto_publish')) {
                $grade->approve(auth()->user(), 'Admin override - auto-published');
                $grade->publish();
            } else {
                $grade->submit();
            }

            // Log the activity
            $student = Student::find($validated['student_id']);
            StudentActivityLog::logGradeUpdate($student, $grade, auth()->user());

            DB::commit();

            return redirect()->route('admin.grades.index')
                           ->with('success', 'Grade created successfully.');

        } catch (\Exception $e) {
            DB::rollback();
            return back()->withErrors(['error' => 'Failed to create grade: ' . $e->getMessage()])
                        ->withInput();
        }
    }

    /**
     * Display the specified grade for review
     */
    public function show(InternationalGrade $grade)
    {
        $grade->load(['student.user', 'subject', 'teacher.user', 'classRoom', 'approvedBy']);
        
        // Get related grades for context
        $relatedGrades = InternationalGrade::where('student_id', $grade->student_id)
                                         ->where('subject_id', $grade->subject_id)
                                         ->where('academic_year', $grade->academic_year)
                                         ->where('id', '!=', $grade->id)
                                         ->orderBy('assessment_date', 'desc')
                                         ->take(5)
                                         ->get();

        return view('admin.grades.show', compact('grade', 'relatedGrades'));
    }

    /**
     * Show the form for editing the specified grade
     */
    public function edit(InternationalGrade $grade)
    {
        // Only allow editing of draft or rejected grades
        if (!in_array($grade->status, ['draft', 'rejected'])) {
            return redirect()->route('admin.grades.show', $grade)
                           ->withErrors(['error' => 'Cannot edit grades that have been submitted or approved.']);
        }

        $students = Student::with('user', 'classRoom')->get();
        $subjects = Subject::all();
        $teachers = Teacher::with('user')->get();
        $classes = ClassRoom::all();

        return view('admin.grades.edit', compact('grade', 'students', 'subjects', 'teachers', 'classes'));
    }

    /**
     * Update the specified grade
     */
    public function update(Request $request, InternationalGrade $grade)
    {
        // Only allow updating of draft or rejected grades
        if (!in_array($grade->status, ['draft', 'rejected'])) {
            return redirect()->route('admin.grades.show', $grade)
                           ->withErrors(['error' => 'Cannot update grades that have been submitted or approved.']);
        }

        $validated = $request->validate([
            'assessment_type' => 'required|string|max:255',
            'assessment_title' => 'required|string|max:255',
            'assessment_description' => 'nullable|string',
            'assessment_date' => 'required|date',
            'raw_score' => 'required|numeric|min:0',
            'max_score' => 'required|numeric|min:0.01',
            'teacher_comments' => 'nullable|string',
            'feedback' => 'nullable|string',
            'weight' => 'nullable|numeric|min:0|max:10',
            'counts_toward_final' => 'boolean',
            'is_extra_credit' => 'boolean',
        ]);

        $validated['weight'] = $validated['weight'] ?? 1.0;
        $validated['counts_toward_final'] = $request->boolean('counts_toward_final', true);
        $validated['is_extra_credit'] = $request->boolean('is_extra_credit', false);

        $grade->update($validated);

        return redirect()->route('admin.grades.show', $grade)
                       ->with('success', 'Grade updated successfully.');
    }

    /**
     * Approve a grade
     */
    public function approve(Request $request, InternationalGrade $grade)
    {
        if ($grade->status !== 'submitted') {
            return back()->withErrors(['error' => 'Only submitted grades can be approved.']);
        }

        $request->validate([
            'approval_notes' => 'nullable|string|max:1000',
            'publish_immediately' => 'boolean',
        ]);

        DB::beginTransaction();
        try {
            $grade->approve(auth()->user(), $request->approval_notes);

            if ($request->boolean('publish_immediately')) {
                $grade->publish();
            }

            // Log the approval
            $student = $grade->student;
            StudentActivityLog::logGradeUpdate($student, $grade, auth()->user());

            DB::commit();

            $message = 'Grade approved successfully.';
            if ($request->boolean('publish_immediately')) {
                $message .= ' Grade is now visible to student and parents.';
            }

            return redirect()->route('admin.grades.index')
                           ->with('success', $message);

        } catch (\Exception $e) {
            DB::rollback();
            return back()->withErrors(['error' => 'Failed to approve grade: ' . $e->getMessage()]);
        }
    }

    /**
     * Reject a grade
     */
    public function reject(Request $request, InternationalGrade $grade)
    {
        if ($grade->status !== 'submitted') {
            return back()->withErrors(['error' => 'Only submitted grades can be rejected.']);
        }

        $request->validate([
            'approval_notes' => 'required|string|max:1000',
        ]);

        $grade->reject(auth()->user(), $request->approval_notes);

        return redirect()->route('admin.grades.index')
                       ->with('success', 'Grade rejected and returned to teacher for revision.');
    }

    /**
     * Publish approved grades to students
     */
    public function publish(InternationalGrade $grade)
    {
        if ($grade->status !== 'approved') {
            return back()->withErrors(['error' => 'Only approved grades can be published.']);
        }

        $grade->publish();

        return back()->with('success', 'Grade published successfully. Now visible to student and parents.');
    }

    /**
     * Bulk approve grades
     */
    public function bulkApprove(Request $request)
    {
        $validated = $request->validate([
            'grade_ids' => 'required|array',
            'grade_ids.*' => 'exists:international_grades,id',
            'approval_notes' => 'nullable|string|max:1000',
            'publish_immediately' => 'boolean',
        ]);

        $successCount = 0;
        $errors = [];

        DB::beginTransaction();
        try {
            foreach ($validated['grade_ids'] as $gradeId) {
                $grade = InternationalGrade::find($gradeId);
                
                if ($grade && $grade->status === 'submitted') {
                    $grade->approve(auth()->user(), $validated['approval_notes']);
                    
                    if ($request->boolean('publish_immediately')) {
                        $grade->publish();
                    }
                    
                    $successCount++;
                } else {
                    $errors[] = "Grade ID {$gradeId} could not be approved.";
                }
            }

            DB::commit();

            $message = "Successfully approved {$successCount} grades.";
            if (!empty($errors)) {
                $message .= " Errors: " . implode(', ', $errors);
            }

            return back()->with('success', $message);

        } catch (\Exception $e) {
            DB::rollback();
            return back()->withErrors(['error' => 'Failed to approve grades: ' . $e->getMessage()]);
        }
    }

    /**
     * Grade analytics and reports
     */
    public function analytics(Request $request)
    {
        $academicYear = $request->get('academic_year', date('Y'));
        $semester = $request->get('semester', 'fall');

        // Grade distribution
        $gradeDistribution = InternationalGrade::where('academic_year', $academicYear)
                                             ->where('semester', $semester)
                                             ->where('status', 'published')
                                             ->selectRaw('letter_grade, COUNT(*) as count')
                                             ->groupBy('letter_grade')
                                             ->orderBy('letter_grade')
                                             ->get();

        // Subject performance
        $subjectPerformance = InternationalGrade::with('subject')
                                              ->where('academic_year', $academicYear)
                                              ->where('semester', $semester)
                                              ->where('status', 'published')
                                              ->selectRaw('subject_id, AVG(percentage) as avg_percentage, COUNT(*) as total_grades')
                                              ->groupBy('subject_id')
                                              ->orderBy('avg_percentage', 'desc')
                                              ->get();

        // Class performance
        $classPerformance = InternationalGrade::with('classRoom')
                                            ->where('academic_year', $academicYear)
                                            ->where('semester', $semester)
                                            ->where('status', 'published')
                                            ->selectRaw('class_id, AVG(gpa_points) as avg_gpa, COUNT(*) as total_grades')
                                            ->groupBy('class_id')
                                            ->orderBy('avg_gpa', 'desc')
                                            ->get();

        // Pending approvals count
        $pendingCount = InternationalGrade::where('status', 'submitted')->count();

        return view('admin.grades.analytics', compact(
            'gradeDistribution', 'subjectPerformance', 'classPerformance', 
            'pendingCount', 'academicYear', 'semester'
        ));
    }

    /**
     * Remove the specified grade
     */
    public function destroy(InternationalGrade $grade)
    {
        // Only allow deletion of draft grades
        if ($grade->status !== 'draft') {
            return back()->withErrors(['error' => 'Only draft grades can be deleted.']);
        }

        $grade->delete();

        return redirect()->route('admin.grades.index')
                       ->with('success', 'Grade deleted successfully.');
    }
}
