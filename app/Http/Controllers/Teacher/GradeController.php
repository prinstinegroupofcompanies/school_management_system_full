<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\InternationalGrade;
use App\Models\Student;
use App\Models\Subject;
use App\Models\ClassRoom;
use App\Models\Teacher;
use App\Models\StudentActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class GradeController extends Controller
{
    public function __construct()
    {
        $this->middleware('teacher');
    }

    /**
     * Display teacher's grades with filtering
     */
    public function index(Request $request)
    {
        $teacher = $request->user()->teacher;
        
        if (!$teacher) {
            return redirect()->route('teacher.dashboard')
                           ->withErrors(['error' => 'Teacher profile not found.']);
        }
        
        // Get teacher's assigned subjects and classes
        $subjects = Subject::where('teacher_id', $teacher->id)->get();
        $classes = ClassRoom::whereHas('subjects', function($query) use ($teacher) {
            $query->where('teacher_id', $teacher->id);
        })->get();
        
        // Get academic years from grades
        $academicYears = InternationalGrade::where('teacher_id', $teacher->id)
            ->distinct()
            ->pluck('academic_year')
            ->sort()
            ->values();
        
        // Build grades query with filters
        $gradesQuery = InternationalGrade::where('teacher_id', $teacher->id)
            ->with(['student.user', 'subject', 'classRoom']);
            
        // Apply filters
        if ($request->filled('class_id')) {
            $gradesQuery->where('class_id', $request->class_id);
        }
        
        if ($request->filled('subject_id')) {
            $gradesQuery->where('subject_id', $request->subject_id);
        }
        
        if ($request->filled('academic_year')) {
            $gradesQuery->where('academic_year', $request->academic_year);
        }
        
        if ($request->filled('semester')) {
            $gradesQuery->where('semester', $request->semester);
        }
        
        if ($request->filled('status')) {
            $gradesQuery->where('status', $request->status);
        } else {
            // Default to show draft and submitted grades
            $gradesQuery->whereIn('status', ['draft', 'submitted']);
        }
        
        $grades = $gradesQuery->orderBy('created_at', 'desc')->paginate(15);
        
        // Get summary statistics
        $stats = [
            'total_grades' => InternationalGrade::where('teacher_id', $teacher->id)->count(),
            'draft_grades' => InternationalGrade::where('teacher_id', $teacher->id)->where('status', 'draft')->count(),
            'submitted_grades' => InternationalGrade::where('teacher_id', $teacher->id)->where('status', 'submitted')->count(),
            'approved_grades' => InternationalGrade::where('teacher_id', $teacher->id)->where('status', 'approved')->count(),
            'published_grades' => InternationalGrade::where('teacher_id', $teacher->id)->where('status', 'published')->count(),
        ];
        
        return view('teacher.grades.index', compact('grades', 'classes', 'subjects', 'academicYears', 'stats'));
    }

    /**
     * Show form for creating new grade
     */
    public function create(Request $request)
    {
        $teacher = $request->user()->teacher;
        
        if (!$teacher) {
            return redirect()->route('teacher.dashboard')
                           ->withErrors(['error' => 'Teacher profile not found.']);
        }
        
        // Get teacher's assigned subjects and classes
        $subjects = Subject::where('teacher_id', $teacher->id)->get();
        $classes = ClassRoom::whereHas('subjects', function($query) use ($teacher) {
            $query->where('teacher_id', $teacher->id);
        })->get();
        
        $selectedClassId = $request->get('class_id');
        $selectedSubjectId = $request->get('subject_id');
        
        // Get students if class and subject are selected
        $students = collect();
        if ($selectedClassId && $selectedSubjectId) {
            $students = Student::where('class_id', $selectedClassId)
                              ->with(['user', 'classRoom'])
                              ->get();
        }
        
        // Assessment types for dropdown
        $assessmentTypes = [
            'assignment' => 'Assignment',
            'quiz' => 'Quiz',
            'midterm' => 'Midterm Exam',
            'final' => 'Final Exam',
            'project' => 'Project',
            'participation' => 'Class Participation',
            'presentation' => 'Presentation',
            'lab_work' => 'Laboratory Work',
            'homework' => 'Homework',
            'test' => 'Test'
        ];
        
        // Semester options
        $semesters = [
            'fall' => 'Fall Semester',
            'spring' => 'Spring Semester', 
            'summer' => 'Summer Semester'
        ];
        
        $currentYear = date('Y');
        $currentSemester = $this->getCurrentSemester();
        
        return view('teacher.grades.create', compact(
            'classes', 'subjects', 'students', 'assessmentTypes', 'semesters',
            'selectedClassId', 'selectedSubjectId', 'currentYear', 'currentSemester'
        ));
    }
    
    /**
     * Store a newly created grade
     */
    public function store(Request $request)
    {
        $teacher = $request->user()->teacher;
        
        if (!$teacher) {
            return redirect()->route('teacher.dashboard')
                           ->withErrors(['error' => 'Teacher profile not found.']);
        }

        $validated = $request->validate([
            'student_id' => 'required|exists:students,id',
            'subject_id' => 'required|exists:subjects,id',
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
            'save_as_draft' => 'boolean',
        ]);

        // Verify teacher has permission to grade this subject
        $subject = Subject::where('id', $validated['subject_id'])
                         ->where('teacher_id', $teacher->id)
                         ->first();

        if (!$subject) {
            return back()->withErrors(['subject_id' => 'You are not authorized to grade this subject.'])
                        ->withInput();
        }

        DB::beginTransaction();
        try {
            // Set defaults
            $validated['teacher_id'] = $teacher->id;
            $validated['weight'] = $validated['weight'] ?? 1.0;
            $validated['counts_toward_final'] = $request->boolean('counts_toward_final', true);
            $validated['is_extra_credit'] = $request->boolean('is_extra_credit', false);

            // Create grade
            $grade = InternationalGrade::create($validated);

            // Set status based on save type
            if ($request->boolean('save_as_draft')) {
                $grade->status = 'draft';
            } else {
                $grade->submit();
            }
            $grade->save();

            DB::commit();

            $message = $request->boolean('save_as_draft') 
                     ? 'Grade saved as draft successfully.' 
                     : 'Grade submitted for admin approval successfully.';

            return redirect()->route('teacher.grades.index')
                           ->with('success', $message);

        } catch (\Exception $e) {
            DB::rollback();
            return back()->withErrors(['error' => 'Failed to create grade: ' . $e->getMessage()])
                        ->withInput();
        }
    }

    /**
     * Show specific grade
     */
    public function show(InternationalGrade $grade)
    {
        $teacher = auth()->user()->teacher;
        
        // Verify teacher owns this grade
        if ($grade->teacher_id !== $teacher->id) {
            return redirect()->route('teacher.grades.index')
                           ->withErrors(['error' => 'You are not authorized to view this grade.']);
        }

        $grade->load(['student.user', 'subject', 'classRoom', 'approvedBy']);
        
        return view('teacher.grades.show', compact('grade'));
    }

    /**
     * Show form for editing grade (only drafts and rejected)
     */
    public function edit(InternationalGrade $grade)
    {
        $teacher = auth()->user()->teacher;
        
        // Verify teacher owns this grade
        if ($grade->teacher_id !== $teacher->id) {
            return redirect()->route('teacher.grades.index')
                           ->withErrors(['error' => 'You are not authorized to edit this grade.']);
        }

        // Only allow editing of draft or rejected grades
        if (!in_array($grade->status, ['draft', 'rejected'])) {
            return redirect()->route('teacher.grades.show', $grade)
                           ->withErrors(['error' => 'Cannot edit grades that have been submitted or approved.']);
        }

        $subjects = Subject::where('teacher_id', $teacher->id)->get();
        $classes = ClassRoom::whereHas('subjects', function($query) use ($teacher) {
            $query->where('teacher_id', $teacher->id);
        })->get();
        
        $students = Student::where('class_id', $grade->class_id)->with(['user', 'classRoom'])->get();
        
        $assessmentTypes = [
            'assignment' => 'Assignment',
            'quiz' => 'Quiz',
            'midterm' => 'Midterm Exam',
            'final' => 'Final Exam',
            'project' => 'Project',
            'participation' => 'Class Participation',
            'presentation' => 'Presentation',
            'lab_work' => 'Laboratory Work',
            'homework' => 'Homework',
            'test' => 'Test'
        ];
        
        $semesters = [
            'fall' => 'Fall Semester',
            'spring' => 'Spring Semester', 
            'summer' => 'Summer Semester'
        ];

        return view('teacher.grades.edit', compact('grade', 'classes', 'subjects', 'students', 'assessmentTypes', 'semesters'));
    }

    /**
     * Update the specified grade
     */
    public function update(Request $request, InternationalGrade $grade)
    {
        $teacher = auth()->user()->teacher;
        
        // Verify teacher owns this grade
        if ($grade->teacher_id !== $teacher->id) {
            return redirect()->route('teacher.grades.index')
                           ->withErrors(['error' => 'You are not authorized to update this grade.']);
        }

        // Only allow updating of draft or rejected grades
        if (!in_array($grade->status, ['draft', 'rejected'])) {
            return redirect()->route('teacher.grades.show', $grade)
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
            'save_as_draft' => 'boolean',
        ]);

        DB::beginTransaction();
        try {
            // Set defaults
            $validated['weight'] = $validated['weight'] ?? 1.0;
            $validated['counts_toward_final'] = $request->boolean('counts_toward_final', true);
            $validated['is_extra_credit'] = $request->boolean('is_extra_credit', false);

            $grade->update($validated);

            // Update status based on save type
            if ($request->boolean('save_as_draft')) {
                $grade->status = 'draft';
            } else {
                $grade->submit();
            }
            $grade->save();

            DB::commit();

            $message = $request->boolean('save_as_draft') 
                     ? 'Grade updated and saved as draft.' 
                     : 'Grade updated and submitted for admin approval.';

            return redirect()->route('teacher.grades.show', $grade)
                           ->with('success', $message);

        } catch (\Exception $e) {
            DB::rollback();
            return back()->withErrors(['error' => 'Failed to update grade: ' . $e->getMessage()])
                        ->withInput();
        }
    }

    /**
     * Get current semester based on date
     */
    private function getCurrentSemester(): string
    {
        $month = date('n');
        if ($month >= 8 && $month <= 12) return 'fall';
        if ($month >= 1 && $month <= 5) return 'spring';
        return 'summer';
    }

    /**
     * Get subjects for a specific class (AJAX)
     */
    public function getSubjects(Request $request)
    {
        $teacher = $request->user()->teacher;
        
        $request->validate([
            'class_id' => 'required|exists:class_rooms,id',
        ]);

        // Get subjects that the teacher teaches in the selected class
        $subjects = Subject::where('teacher_id', $teacher->id)
                          ->whereHas('classes', function($query) use ($request) {
                              $query->where('class_rooms.id', $request->class_id);
                          })
                          ->get()
                          ->map(function ($subject) {
                              return [
                                  'id' => $subject->id,
                                  'name' => $subject->name,
                                  'code' => $subject->code,
                              ];
                          });

        return response()->json(['subjects' => $subjects]);
    }

    /**
     * Get students for a specific class and subject (AJAX)
     */
    public function getStudents(Request $request)
    {
        $teacher = $request->user()->teacher;
        
        $request->validate([
            'class_id' => 'required|exists:class_rooms,id',
            'subject_id' => 'required|exists:subjects,id',
        ]);

        // Verify teacher has permission for this subject
        $subject = Subject::where('id', $request->subject_id)
                         ->where('teacher_id', $teacher->id)
                         ->first();

        if (!$subject) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $students = Student::where('class_id', $request->class_id)
                          ->with(['user', 'classRoom'])
                          ->get()
                          ->map(function ($student) {
                              return [
                                  'id' => $student->id,
                                  'name' => $student->getDisplayName(),
                                  'admission_number' => $student->admission_number,
                                  'student_number' => $student->student_number,
                              ];
                          });

        return response()->json($students);
    }

    /**
     * Bulk grade entry for multiple students
     */
    public function bulkCreate(Request $request)
    {
        $teacher = $request->user()->teacher;
        
        if (!$teacher) {
            return redirect()->route('teacher.dashboard')
                           ->withErrors(['error' => 'Teacher profile not found.']);
        }

        $subjects = Subject::where('teacher_id', $teacher->id)->get();
        $classes = ClassRoom::whereHas('subjects', function($query) use ($teacher) {
            $query->where('teacher_id', $teacher->id);
        })->get();

        $selectedClassId = $request->get('class_id');
        $selectedSubjectId = $request->get('subject_id');
        
        $students = collect();
        if ($selectedClassId && $selectedSubjectId) {
            $students = Student::where('class_id', $selectedClassId)
                              ->with(['user', 'classRoom'])
                              ->get();
        }

        $assessmentTypes = [
            'assignment' => 'Assignment',
            'quiz' => 'Quiz',
            'midterm' => 'Midterm Exam',
            'final' => 'Final Exam',
            'project' => 'Project',
            'participation' => 'Class Participation',
        ];

        $semesters = [
            'fall' => 'Fall Semester',
            'spring' => 'Spring Semester', 
            'summer' => 'Summer Semester'
        ];

        return view('teacher.grades.bulk-create', compact(
            'classes', 'subjects', 'students', 'assessmentTypes', 'semesters',
            'selectedClassId', 'selectedSubjectId'
        ));
    }

    /**
     * Show exam questions interface
     */
    public function examQuestions(Request $request)
    {
        $teacher = $request->user()->teacher;
        
        if (!$teacher) {
            return redirect()->route('teacher.dashboard')
                           ->withErrors(['error' => 'Teacher profile not found.']);
        }

        // Get teacher's subjects and classes for filtering
        $subjects = Subject::where('teacher_id', $teacher->id)->get();
        $classes = ClassRoom::whereHas('subjects', function($query) use ($teacher) {
            $query->where('teacher_id', $teacher->id);
        })->get();

        return view('teacher.grades.exam-questions', compact('subjects', 'classes'));
    }

    /**
     * Store bulk grades
     */
    public function bulkStore(Request $request)
    {
        $teacher = $request->user()->teacher;
        
        if (!$teacher) {
            return redirect()->route('teacher.dashboard')
                           ->withErrors(['error' => 'Teacher profile not found.']);
        }

        $validated = $request->validate([
            'class_id' => 'required|exists:class_rooms,id',
            'subject_id' => 'required|exists:subjects,id',
            'assessment_type' => 'required|string|max:255',
            'assessment_title' => 'required|string|max:255',
            'assessment_description' => 'nullable|string',
            'assessment_date' => 'required|date',
            'academic_year' => 'required|string|max:10',
            'semester' => 'required|in:fall,spring,summer',
            'max_score' => 'required|numeric|min:0.01',
            'weight' => 'nullable|numeric|min:0|max:10',
            'counts_toward_final' => 'boolean',
            'is_extra_credit' => 'boolean',
            'save_as_draft' => 'boolean',
            'students' => 'required|array',
            'students.*.student_id' => 'required|exists:students,id',
            'students.*.raw_score' => 'required|numeric|min:0',
            'students.*.comments' => 'nullable|string',
        ]);

        // Verify teacher has permission for this subject
        $subject = Subject::where('id', $validated['subject_id'])
                         ->where('teacher_id', $teacher->id)
                         ->first();

        if (!$subject) {
            return back()->withErrors(['subject_id' => 'You are not authorized to grade this subject.'])
                        ->withInput();
        }

        DB::beginTransaction();
        try {
            $successCount = 0;
            $errors = [];

            foreach ($validated['students'] as $studentData) {
                try {
                    $gradeData = [
                        'student_id' => $studentData['student_id'],
                        'subject_id' => $validated['subject_id'],
                        'teacher_id' => $teacher->id,
                        'class_id' => $validated['class_id'],
                        'assessment_type' => $validated['assessment_type'],
                        'assessment_title' => $validated['assessment_title'],
                        'assessment_description' => $validated['assessment_description'],
                        'assessment_date' => $validated['assessment_date'],
                        'academic_year' => $validated['academic_year'],
                        'semester' => $validated['semester'],
                        'raw_score' => $studentData['raw_score'],
                        'max_score' => $validated['max_score'],
                        'teacher_comments' => $studentData['comments'],
                        'weight' => $validated['weight'] ?? 1.0,
                        'counts_toward_final' => $request->boolean('counts_toward_final', true),
                        'is_extra_credit' => $request->boolean('is_extra_credit', false),
                    ];

                    $grade = InternationalGrade::create($gradeData);

                    // Set status
                    if ($request->boolean('save_as_draft')) {
                        $grade->status = 'draft';
                    } else {
                        $grade->submit();
                    }
                    $grade->save();

                    $successCount++;

                } catch (\Exception $e) {
                    $student = Student::find($studentData['student_id']);
                    $errors[] = "Failed to create grade for {$student->getDisplayName()}: {$e->getMessage()}";
                }
            }

            DB::commit();

            $message = "Successfully created {$successCount} grades.";
            if (!empty($errors)) {
                $message .= " Errors: " . implode(', ', $errors);
            }

            return redirect()->route('teacher.grades.index')
                           ->with('success', $message);

        } catch (\Exception $e) {
            DB::rollback();
            return back()->withErrors(['error' => 'Failed to create grades: ' . $e->getMessage()])
                        ->withInput();
        }
    }

    /**
     * Grade analytics for teacher
     */
    public function analytics(Request $request)
    {
        $teacher = $request->user()->teacher;
        
        if (!$teacher) {
            return redirect()->route('teacher.dashboard');
        }

        $academicYear = $request->get('academic_year', date('Y'));
        $semester = $request->get('semester', $this->getCurrentSemester());

        // Grade distribution by teacher's subjects
        $gradeDistribution = InternationalGrade::where('teacher_id', $teacher->id)
                                             ->where('academic_year', $academicYear)
                                             ->where('semester', $semester)
                                             ->where('status', 'published')
                                             ->selectRaw('letter_grade, COUNT(*) as count')
                                             ->groupBy('letter_grade')
                                             ->orderBy('letter_grade')
                                             ->get();

        // Class performance for teacher's classes
        $classPerformance = InternationalGrade::with('classRoom')
                                            ->where('teacher_id', $teacher->id)
                                            ->where('academic_year', $academicYear)
                                            ->where('semester', $semester)
                                            ->where('status', 'published')
                                            ->selectRaw('class_id, AVG(percentage) as avg_percentage, COUNT(*) as total_grades')
                                            ->groupBy('class_id')
                                            ->orderBy('avg_percentage', 'desc')
                                            ->get();

        // Subject performance for teacher's subjects
        $subjectPerformance = InternationalGrade::with('subject')
                                              ->where('teacher_id', $teacher->id)
                                              ->where('academic_year', $academicYear)
                                              ->where('semester', $semester)
                                              ->where('status', 'published')
                                              ->selectRaw('subject_id, AVG(percentage) as avg_percentage, COUNT(*) as total_grades')
                                              ->groupBy('subject_id')
                                              ->orderBy('avg_percentage', 'desc')
                                              ->get();

        return view('teacher.grades.analytics', compact(
            'gradeDistribution', 'classPerformance', 'subjectPerformance',
            'academicYear', 'semester'
        ));
    }

    /**
     * Submit grade for admin approval
     */
    public function submit(InternationalGrade $grade)
    {
        $teacher = auth()->user()->teacher;
        
        if ($grade->teacher_id !== $teacher->id) {
            return back()->withErrors(['error' => 'Unauthorized']);
        }

        if ($grade->status !== 'draft') {
            return back()->withErrors(['error' => 'Only draft grades can be submitted.']);
        }

        $grade->submit();

        return back()->with('success', 'Grade submitted for admin approval.');
    }

    /**
     * Delete grade (only drafts)
     */
    public function destroy(InternationalGrade $grade)
    {
        $teacher = auth()->user()->teacher;
        
        if ($grade->teacher_id !== $teacher->id) {
            return back()->withErrors(['error' => 'Unauthorized']);
        }

        if ($grade->status !== 'draft') {
            return back()->withErrors(['error' => 'Only draft grades can be deleted.']);
        }

        $grade->delete();

        return redirect()->route('teacher.grades.index')
                       ->with('success', 'Grade deleted successfully.');
    }
}