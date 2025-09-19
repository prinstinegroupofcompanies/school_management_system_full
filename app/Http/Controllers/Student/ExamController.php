<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\ExamPaper;
use App\Models\ExamQuestion;
use App\Models\StudentExamAttempt;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ExamController extends Controller
{
    public function __construct()
    {
        $this->middleware('student');
    }

    /**
     * Display available exams for student
     */
    public function index(Request $request)
    {
        $student = $request->user()->student;
        
        if (!$student) {
            return redirect()->route('student.dashboard')
                           ->withErrors(['error' => 'Student profile not found.']);
        }

        // Get exams for student's class
        $query = ExamPaper::where('class_id', $student->class_id)
                         ->where('is_published', true)
                         ->where('is_active', true)
                         ->with(['subject', 'teacher.user']);

        $exams = $query->orderBy('start_time', 'asc')->paginate(15);

        return view('student.exams.index', compact('exams'));
    }

    /**
     * Show exam details
     */
    public function show(ExamPaper $exam)
    {
        $student = auth()->user()->student;
        
        // Verify student can access this exam
        if ($exam->class_id !== $student->class_id) {
            return redirect()->route('student.exams.index')
                           ->withErrors(['error' => 'You are not authorized to access this exam.']);
        }

        $exam->load(['subject', 'teacher.user', 'questions']);
        
        // Get student's attempt if exists
        $attempt = StudentExamAttempt::where('exam_paper_id', $exam->id)
                                   ->where('student_id', $student->id)
                                   ->first();

        return view('student.exams.show', compact('exam', 'attempt'));
    }

    /**
     * Start exam attempt
     */
    public function start(ExamPaper $exam)
    {
        $student = auth()->user()->student;
        
        // Verify student can attempt this exam
        if ($exam->class_id !== $student->class_id || !$exam->is_published) {
            return redirect()->route('student.exams.show', $exam)
                           ->withErrors(['error' => 'You cannot attempt this exam.']);
        }

        // Check if exam is within time window
        $now = now();
        if ($now->lt($exam->start_time) || $now->gt($exam->end_time)) {
            return redirect()->route('student.exams.show', $exam)
                           ->withErrors(['error' => 'Exam is not currently available.']);
        }

        // Create new attempt
        $autoSubmitTime = Carbon::parse($exam->end_time);
        if ($exam->duration_minutes) {
            $attemptEndTime = now()->addMinutes($exam->duration_minutes);
            if ($attemptEndTime->lt($autoSubmitTime)) {
                $autoSubmitTime = $attemptEndTime;
            }
        }

        $attempt = StudentExamAttempt::create([
            'exam_paper_id' => $exam->id,
            'student_id' => $student->id,
            'attempt_number' => 1,
            'started_at' => now(),
            'auto_submit_at' => $autoSubmitTime,
            'status' => 'in_progress',
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);

        return redirect()->route('student.exams.take', ['exam' => $exam, 'attempt' => $attempt]);
    }

    /**
     * Take exam (display questions)
     */
    public function take(ExamPaper $exam, StudentExamAttempt $attempt)
    {
        $student = auth()->user()->student;
        
        // Verify this is student's attempt
        if ($attempt->student_id !== $student->id) {
            return redirect()->route('student.exams.index')
                           ->withErrors(['error' => 'Invalid exam attempt.']);
        }

        // Check if exam time has expired
        if (now()->gt($attempt->auto_submit_at)) {
            $this->autoSubmitExam($attempt);
            return redirect()->route('student.exams.result', ['exam' => $exam, 'attempt' => $attempt]);
        }

        // Get questions
        $questions = $exam->questions()->orderBy('question_number')->get();
        $timeRemaining = now()->diffInSeconds($attempt->auto_submit_at, false);
        $timeRemaining = max(0, $timeRemaining);

        return view('student.exams.take', compact('exam', 'attempt', 'questions', 'timeRemaining'));
    }

    /**
     * Submit exam
     */
    public function submit(ExamPaper $exam, StudentExamAttempt $attempt)
    {
        $student = auth()->user()->student;
        
        if ($attempt->student_id !== $student->id) {
            return redirect()->route('student.exams.index')
                           ->withErrors(['error' => 'Invalid exam attempt.']);
        }

        $attempt->update([
            'submitted_at' => now(),
            'status' => 'submitted',
            'time_spent_minutes' => now()->diffInMinutes($attempt->started_at),
        ]);

        return redirect()->route('student.exams.result', ['exam' => $exam, 'attempt' => $attempt])
                       ->with('success', 'Exam submitted successfully!');
    }

    private function autoSubmitExam(StudentExamAttempt $attempt)
    {
        $attempt->update([
            'submitted_at' => now(),
            'status' => 'auto_submitted',
            'time_spent_minutes' => now()->diffInMinutes($attempt->started_at),
        ]);
    }
}
