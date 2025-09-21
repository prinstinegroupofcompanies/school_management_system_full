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
        $classes = ClassRoom::whereHas('subjects', function($query) use ($teacher) {
            $query->where('teacher_id', $teacher->id);
        })->get();

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
        $classes = ClassRoom::whereHas('subjects', function($query) use ($teacher) {
            $query->where('teacher_id', $teacher->id);
        })->get();

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
        $teacher = $request->user()->teacher;
        
        if (!$teacher) {
            return redirect()->route('teacher.dashboard')
                           ->withErrors(['error' => 'Teacher profile not found.']);
        }

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
            'randomize_questions' => 'boolean',
            'show_results_immediately' => 'boolean',
            'allow_review' => 'boolean',
        ]);

        // Verify teacher has permission for this subject
        $subject = Subject::where('id', $validated['subject_id'])
                         ->where('teacher_id', $teacher->id)
                         ->first();

        if (!$subject) {
            return back()->withErrors(['subject_id' => 'You are not authorized to create exams for this subject.'])
                        ->withInput();
        }

        DB::beginTransaction();
        try {
            $validated['teacher_id'] = $teacher->id;
            $validated['end_time'] = Carbon::parse($validated['start_time'])
                                          ->addMinutes($validated['duration_minutes']);
            $validated['randomize_questions'] = $request->boolean('randomize_questions', false);
            $validated['show_results_immediately'] = $request->boolean('show_results_immediately', false);
            $validated['allow_review'] = $request->boolean('allow_review', true);
            $validated['is_published'] = false; // Start as draft

            $exam = ExamPaper::create($validated);

            DB::commit();

            return redirect()->route('teacher.exams.show', $exam)
                           ->with('success', 'Exam created successfully. Add questions to complete setup.');

        } catch (\Exception $e) {
            DB::rollback();
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

        $exam->load(['subject', 'classRoom', 'questions']);
        
        // Get exam attempts/submissions
        $attempts = StudentExamAttempt::where('exam_paper_id', $exam->id)
                                    ->with(['student.user'])
                                    ->orderBy('submitted_at', 'desc')
                                    ->paginate(10);

        // Get statistics
        $stats = [
            'total_questions' => $exam->questions->count(),
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
        $classes = ClassRoom::whereHas('subjects', function($query) use ($teacher) {
            $query->where('teacher_id', $teacher->id);
        })->get();

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
}