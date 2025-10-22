<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\ExamPaper;
use App\Models\ExamQuestion;
use App\Models\StudentExamAttempt;
use App\Models\Student;
use App\Models\Subject;
use App\Models\ClassRoom;
use App\Models\Teacher;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ExamController extends Controller
{
    public function __construct()
    {
        $this->middleware('teacher');
    }

    /**
     * Display teacher's exams
     */
    public function index(Request $request)
    {
        $teacher = $request->user()->teacher;
        
        if (!$teacher) {
            return redirect()->route('teacher.dashboard')
                           ->withErrors(['error' => 'Teacher profile not found.']);
        }

        $query = ExamPaper::where('teacher_id', $teacher->id)
                         ->with(['subject', 'classRoom']);

        // Apply filters
        if ($request->filled('subject_id')) {
            $query->where('subject_id', $request->subject_id);
        }

        if ($request->filled('class_id')) {
            $query->where('class_id', $request->class_id);
        }

        if ($request->filled('exam_type')) {
            $query->where('exam_type', $request->exam_type);
        }

        if ($request->filled('status')) {
            if ($request->status === 'published') {
                $query->where('is_published', true);
            } elseif ($request->status === 'draft') {
                $query->where('is_published', false);
            }
        }

        $exams = $query->orderBy('created_at', 'desc')->paginate(15);
        
        // Get filter options
        $subjects = Subject::where('teacher_id', $teacher->id)->get();
        
        // Get classes assigned via both methods
        $pivotClasses = $teacher->classes()->get();
        $directClasses = ClassRoom::where('class_teacher_id', $teacher->id)->get();
        $classes = $pivotClasses->merge($directClasses)->unique('id');

        $examTypes = ['quiz', 'midterm', 'final', 'assignment'];

        // Get summary statistics
        $stats = [
            'total_exams' => ExamPaper::where('teacher_id', $teacher->id)->count(),
            'published_exams' => ExamPaper::where('teacher_id', $teacher->id)->where('is_published', true)->count(),
            'draft_exams' => ExamPaper::where('teacher_id', $teacher->id)->where('is_published', false)->count(),
            'active_exams' => ExamPaper::where('teacher_id', $teacher->id)
                                     ->where('is_published', true)
                                     ->where('start_time', '<=', now())
                                     ->where('end_time', '>=', now())
                                     ->count(),
        ];

        return view('teacher.exams.index', compact('exams', 'subjects', 'classes', 'examTypes', 'stats'))->with('examSchedules', $exams);
    }

    /**
     * Display upcoming exams for teacher
     */
    public function upcoming(Request $request)
    {
        $teacher = $request->user()->teacher;
        
        if (!$teacher) {
            return redirect()->route('teacher.dashboard')
                           ->withErrors(['error' => 'Teacher profile not found.']);
        }

        $upcomingExams = ExamPaper::where('teacher_id', $teacher->id)
                                 ->where('is_published', true)
                                 ->where('start_time', '>', now())
                                 ->with(['subject', 'classRoom'])
                                 ->orderBy('start_time', 'asc')
                                 ->paginate(15);

        return view('teacher.exams.upcoming', compact('upcomingExams'));
    }

    /**
     * Show form for creating new exam
     */
    public function create()
    {
        $teacher = auth()->user()->teacher;
        
        if (!$teacher) {
            return redirect()->route('teacher.dashboard')
                           ->withErrors(['error' => 'Teacher profile not found.']);
        }

        $subjects = Subject::where('teacher_id', $teacher->id)->get();
        
        // Get classes assigned via both methods
        $pivotClasses = $teacher->classes()->get();
        $directClasses = ClassRoom::where('class_teacher_id', $teacher->id)->get();
        $classes = $pivotClasses->merge($directClasses)->unique('id');

        // Debug logging
        \Log::info('Exam create - Teacher ID: ' . $teacher->id);
        \Log::info('Exam create - Subjects count: ' . $subjects->count());
        \Log::info('Exam create - Classes count: ' . $classes->count());
        \Log::info('Exam create - Classes: ' . $classes->pluck('name')->toJson());

        $examTypes = [
            'quiz' => 'Quiz',
            'midterm' => 'Midterm Exam',
            'final' => 'Final Exam',
            'assignment' => 'Assignment'
        ];

        return view('teacher.exams.create', compact('subjects', 'classes', 'examTypes'));
    }

    /**
     * Store newly created exam
     */
    public function store(Request $request)
    {
        \Log::info('Exam store method called');
        \Log::info('Request data: ' . json_encode($request->all()));
        
        $teacher = $request->user()->teacher;
        
        if (!$teacher) {
            \Log::error('Teacher profile not found for user: ' . $request->user()->id);
            return redirect()->route('teacher.dashboard')
                           ->withErrors(['error' => 'Teacher profile not found.']);
        }

        \Log::info('Teacher found: ' . $teacher->id);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'subject_id' => 'required|exists:subjects,id',
            'class_id' => 'required|exists:class_rooms,id',
            'exam_type' => 'required|string|max:50',
            'duration_minutes' => 'required|integer|min:1|max:480',
            'total_marks' => 'required|integer|min:1',
            'passing_marks' => 'required|integer|min:1',
            'start_time' => 'required|date|after:now',
            'instructions' => 'nullable|string',
            'randomize_questions' => 'boolean',
            'show_results_immediately' => 'boolean',
            'allow_review' => 'boolean',
        ]);

        \Log::info('Validation passed');

        // Verify teacher has permission for this subject
        $subject = Subject::where('id', $validated['subject_id'])
                         ->where('teacher_id', $teacher->id)
                         ->first();

        if (!$subject) {
            \Log::error('Subject not found or not authorized for teacher: ' . $teacher->id . ', subject: ' . $validated['subject_id']);
            return back()->withErrors(['subject_id' => 'You are not authorized to create exams for this subject.'])
                        ->withInput();
        }

        \Log::info('Subject authorized: ' . $subject->name);

        DB::beginTransaction();
        try {
            $validated['teacher_id'] = $teacher->id;
            $validated['end_time'] = Carbon::parse($validated['start_time'])
                                          ->addMinutes((int) $validated['duration_minutes']);
            $validated['randomize_questions'] = $request->boolean('randomize_questions', false);
            $validated['show_results_immediately'] = $request->boolean('show_results_immediately', false);
            $validated['allow_review'] = $request->boolean('allow_review', true);
            $validated['is_published'] = false; // Start as draft
            $validated['questions'] = []; // Initialize with empty questions array

            \Log::info('Creating exam with data: ' . json_encode($validated));

            $exam = ExamPaper::create($validated);

            \Log::info('Exam created successfully with ID: ' . $exam->id);

            DB::commit();

            return redirect()->route('teacher.exams.show', $exam)
                           ->with('success', 'Exam created successfully. Add questions to complete setup.');

        } catch (\Exception $e) {
            DB::rollback();
            \Log::error('Exam creation failed: ' . $e->getMessage());
            \Log::error('Stack trace: ' . $e->getTraceAsString());
            return back()->withErrors(['error' => 'Failed to create exam: ' . $e->getMessage()])
                        ->withInput();
        }
    }

    /**
     * Show specific exam with questions and submissions
     */
    public function show(ExamPaper $exam)
    {
        $teacher = auth()->user()->teacher;
        
        if ($exam->teacher_id !== $teacher->id) {
            return redirect()->route('teacher.exams.index')
                           ->withErrors(['error' => 'You are not authorized to view this exam.']);
        }

        $exam->load(['subject', 'classRoom']);
        
        // Get exam attempts/submissions
        $attempts = StudentExamAttempt::where('exam_paper_id', $exam->id)
                                    ->with(['student.user'])
                                    ->orderBy('submitted_at', 'desc')
                                    ->paginate(10);

        // Get statistics
        $questions = $exam->questions ?? [];
        $stats = [
            'total_questions' => is_array($questions) ? count($questions) : 0,
            'total_attempts' => StudentExamAttempt::where('exam_paper_id', $exam->id)->count(),
            'completed_attempts' => StudentExamAttempt::where('exam_paper_id', $exam->id)->whereNotNull('submitted_at')->count(),
            'in_progress_attempts' => StudentExamAttempt::where('exam_paper_id', $exam->id)->where('status', 'in_progress')->count(),
            'avg_score' => StudentExamAttempt::where('exam_paper_id', $exam->id)->whereNotNull('percentage_score')->avg('percentage_score'),
        ];

        return view('teacher.exams.show', compact('exam', 'attempts', 'stats'));
    }

    /**
     * Show form for editing exam
     */
    public function edit(ExamPaper $exam)
    {
        $teacher = auth()->user()->teacher;
        
        if ($exam->teacher_id !== $teacher->id) {
            return redirect()->route('teacher.exams.index')
                           ->withErrors(['error' => 'You are not authorized to edit this exam.']);
        }

        // Don't allow editing if exam is published and has started
        if ($exam->is_published && $exam->start_time <= now()) {
            return redirect()->route('teacher.exams.show', $exam)
                           ->withErrors(['error' => 'Cannot edit exam that has already started.']);
        }

        $subjects = Subject::where('teacher_id', $teacher->id)->get();
        
        // Get classes assigned via both methods
        $pivotClasses = $teacher->classes()->get();
        $directClasses = ClassRoom::where('class_teacher_id', $teacher->id)->get();
        $classes = $pivotClasses->merge($directClasses)->unique('id');

        // Debug logging
        \Log::info('Exam edit - Teacher ID: ' . $teacher->id);
        \Log::info('Exam edit - Subjects count: ' . $subjects->count());
        \Log::info('Exam edit - Classes count: ' . $classes->count());
        \Log::info('Exam edit - Classes: ' . $classes->pluck('name')->toJson());

        $examTypes = [
            'quiz' => 'Quiz',
            'midterm' => 'Midterm Exam',
            'final' => 'Final Exam',
            'assignment' => 'Assignment'
        ];

        return view('teacher.exams.edit', compact('exam', 'subjects', 'classes', 'examTypes'));
    }

    /**
     * Update exam
     */
    public function update(Request $request, ExamPaper $exam)
    {
        $teacher = auth()->user()->teacher;
        
        if ($exam->teacher_id !== $teacher->id) {
            return redirect()->route('teacher.exams.index')
                           ->withErrors(['error' => 'You are not authorized to update this exam.']);
        }

        // Don't allow editing if exam is published and has started
        if ($exam->is_published && $exam->start_time <= now()) {
            return redirect()->route('teacher.exams.show', $exam)
                           ->withErrors(['error' => 'Cannot edit exam that has already started.']);
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'exam_type' => 'required|string|max:50',
            'duration_minutes' => 'required|integer|min:1|max:480',
            'total_marks' => 'required|integer|min:1',
            'passing_marks' => 'required|integer|min:1',
            'start_time' => 'required|date',
            'randomize_questions' => 'boolean',
            'show_results_immediately' => 'boolean',
            'allow_review' => 'boolean',
        ]);

        $validated['end_time'] = Carbon::parse($validated['start_time'])
                                      ->addMinutes($validated['duration_minutes']);
        $validated['randomize_questions'] = $request->boolean('randomize_questions');
        $validated['show_results_immediately'] = $request->boolean('show_results_immediately');
        $validated['allow_review'] = $request->boolean('allow_review');

        $exam->update($validated);

        return redirect()->route('teacher.exams.show', $exam)
                       ->with('success', 'Exam updated successfully.');
    }

    /**
     * Publish exam to make it available to students
     */
    public function publish(ExamPaper $exam)
    {
        $teacher = auth()->user()->teacher;
        
        if ($exam->teacher_id !== $teacher->id) {
            return back()->withErrors(['error' => 'Unauthorized']);
        }

        if ($exam->questions()->count() === 0) {
            return back()->withErrors(['error' => 'Cannot publish exam without questions.']);
        }

        $exam->update(['is_published' => true]);

        return back()->with('success', 'Exam published successfully. Students can now access it.');
    }

    /**
     * Unpublish exam
     */
    public function unpublish(ExamPaper $exam)
    {
        $teacher = auth()->user()->teacher;
        
        if ($exam->teacher_id !== $teacher->id) {
            return back()->withErrors(['error' => 'Unauthorized']);
        }

        // Don't allow unpublishing if students have already started
        $hasAttempts = StudentExamAttempt::where('exam_paper_id', $exam->id)->exists();
        if ($hasAttempts) {
            return back()->withErrors(['error' => 'Cannot unpublish exam that students have already attempted.']);
        }

        $exam->update(['is_published' => false]);

        return back()->with('success', 'Exam unpublished successfully.');
    }

    /**
     * Delete exam
     */
    public function destroy(ExamPaper $exam)
    {
        $teacher = auth()->user()->teacher;
        
        if ($exam->teacher_id !== $teacher->id) {
            return back()->withErrors(['error' => 'Unauthorized']);
        }

        // Don't allow deletion if students have attempted
        $hasAttempts = StudentExamAttempt::where('exam_paper_id', $exam->id)->exists();
        if ($hasAttempts) {
            return back()->withErrors(['error' => 'Cannot delete exam that students have attempted.']);
        }

        $exam->delete();

        return redirect()->route('teacher.exams.index')
                       ->with('success', 'Exam deleted successfully.');
    }

    /**
     * Grade an exam submission
     */
    public function gradeSubmission(Request $request, $examId, $attemptId)
    {
        $teacher = auth()->user()->teacher;
        
        if (!$teacher) {
            return back()->withErrors(['error' => 'Teacher profile not found.']);
        }

        $request->validate([
            'marks_obtained' => 'required|numeric|min:0',
            'teacher_feedback' => 'nullable|string|max:1000',
            'teacher_comments' => 'nullable|string|max:1000'
        ]);

        try {
            $attempt = StudentExamAttempt::where('id', $attemptId)
                ->whereHas('onlineExam', function($query) use ($examId, $teacher) {
                    $query->where('id', $examId)
                          ->where('teacher_id', $teacher->id);
                })
                ->with(['student.user', 'onlineExam'])
                ->first();

            if (!$attempt) {
                return back()->withErrors(['error' => 'Exam attempt not found.']);
            }

            // Update the attempt with teacher grading
            $attempt->update([
                'marks_obtained' => $request->marks_obtained,
                'teacher_feedback' => $request->teacher_feedback,
                'teacher_comments' => $request->teacher_comments,
                'is_reviewed' => true,
                'reviewed_by' => $teacher->user_id,
                'reviewed_at' => now()
            ]);

            // Calculate final percentage
            $percentage = ($request->marks_obtained / $attempt->total_marks) * 100;
            $attempt->update(['percentage' => $percentage]);

            // Notify student of graded exam
            $this->notifyStudentOfGradedExam($attempt, $attempt->onlineExam);

            return back()->with('success', 'Exam graded successfully. Student has been notified.');

        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Failed to grade exam: ' . $e->getMessage()]);
        }
    }

    /**
     * Notify student when their exam is graded
     */
    private function notifyStudentOfGradedExam($attempt, $exam)
    {
        try {
            $student = $attempt->student;
            if ($student && $student->user) {
                $notification = new \App\Models\Notification([
                    'user_id' => $student->user->id,
                    'type' => 'exam_graded',
                    'title' => 'Exam Results Available',
                    'message' => 'Your exam "' . $exam->title . '" has been graded. You scored ' . $attempt->marks_obtained . '/' . $attempt->total_marks . ' (' . number_format($attempt->percentage, 2) . '%)',
                    'category' => 'academic',
                    'subcategory' => 'exam_results',
                    'priority' => 7, // Very high priority
                    'status' => 'pending',
                    'delivery_method' => 'in_app',
                    'delivery_status' => 'pending',
                    'action_url' => route('student.exams.result', $attempt),
                    'action_text' => 'View Results',
                    'related_model' => 'StudentExamAttempt',
                    'related_id' => $attempt->id,
                    'metadata' => [
                        'exam_id' => $exam->id,
                        'attempt_id' => $attempt->id,
                        'marks_obtained' => $attempt->marks_obtained,
                        'total_marks' => $attempt->total_marks,
                        'percentage' => $attempt->percentage,
                        'is_passed' => $attempt->percentage >= $exam->passing_marks,
                        'teacher_feedback' => $attempt->teacher_feedback
                    ],
                    'is_active' => true
                ]);
                $notification->save();
            }
        } catch (\Exception $e) {
            \Log::error('Failed to notify student of graded exam: ' . $e->getMessage());
        }
    }
}