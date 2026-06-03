<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\LiveExam;
use App\Models\LiveExamAttempt;
use App\Models\ClassRoom;
use App\Models\Subject;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class LiveExamController extends Controller
{
    /**
     * Display a listing of live exams.
     */
    public function index(Request $request)
    {
        $teacher = Auth::user()->teacher;
        
        if (!$teacher) {
            return redirect()->route('teacher.dashboard')->with('error', 'Teacher profile not found.');
        }

        $query = LiveExam::where('teacher_id', $teacher->id)
            ->with(['classRoom', 'subject'])
            ->orderBy('start_time', 'desc');

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        $liveExams = $query->paginate(15);

        return view('teacher.live-exams.index', compact('liveExams'));
    }

    /**
     * Show the form for creating a new live exam.
     */
    public function create()
    {
        $teacher = Auth::user()->teacher;
        $classes = ClassRoom::all();
        $subjects = Subject::all();

        return view('teacher.live-exams.create', compact('classes', 'subjects'));
    }

    /**
     * Store a newly created live exam.
     */
    public function store(Request $request)
    {
        $request->validate([
            'class_id' => 'nullable|exists:class_rooms,id',
            'subject_id' => 'nullable|exists:subjects,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'instructions' => 'nullable|string',
            'start_time' => 'required|date|after:now',
            'end_time' => 'required|date|after:start_time',
            'duration_minutes' => 'required|integer|min:15|max:480',
            'total_marks' => 'required|integer|min:1',
            'passing_marks' => 'required|integer|min:0',
            'allow_late_submission' => 'boolean',
            'late_submission_penalty' => 'nullable|integer|min:0|max:100',
            'randomize_questions' => 'boolean',
            'show_results_immediately' => 'boolean',
            'attempts_allowed' => 'required|integer|min:1|max:10',
            'questions' => 'nullable|array',
        ]);

        $teacher = Auth::user()->teacher;

        LiveExam::create([
            'teacher_id' => $teacher->id,
            'class_id' => $request->class_id,
            'subject_id' => $request->subject_id,
            'title' => $request->title,
            'description' => $request->description,
            'instructions' => $request->instructions,
            'start_time' => $request->start_time,
            'end_time' => $request->end_time,
            'duration_minutes' => $request->duration_minutes,
            'total_marks' => $request->total_marks,
            'passing_marks' => $request->passing_marks,
            'allow_late_submission' => $request->has('allow_late_submission'),
            'late_submission_penalty' => $request->late_submission_penalty ?? 0,
            'randomize_questions' => $request->has('randomize_questions'),
            'show_results_immediately' => $request->has('show_results_immediately'),
            'attempts_allowed' => $request->attempts_allowed,
            'questions' => $request->questions ?? [],
            'status' => 'scheduled',
        ]);

        return redirect()->route('teacher.live-exams.index')
            ->with('success', 'Live exam created successfully.');
    }

    /**
     * Display the specified live exam.
     */
    public function show(LiveExam $liveExam)
    {
        $liveExam->load(['teacher', 'classRoom', 'subject', 'attempts.student']);
        
        $students = [];
        if ($liveExam->class_id) {
            $students = \App\Models\Student::where('class_id', $liveExam->class_id)
                ->with('user')
                ->get();
        }

        return view('teacher.live-exams.show', compact('liveExam', 'students'));
    }

    /**
     * Start a live exam.
     */
    public function start(LiveExam $liveExam)
    {
        $liveExam->update(['status' => 'active']);

        // Broadcast event
        event(new \App\Events\LiveExamStarted($liveExam));

        return redirect()->route('teacher.live-exams.show', $liveExam)
            ->with('success', 'Live exam started.');
    }

    /**
     * View exam attempts.
     */
    public function attempts(LiveExam $liveExam)
    {
        $attempts = $liveExam->attempts()
            ->with('student.user')
            ->orderBy('submitted_at', 'desc')
            ->get();

        return view('teacher.live-exams.attempts', compact('liveExam', 'attempts'));
    }

    /**
     * Grade an exam attempt.
     */
    public function gradeAttempt(Request $request, LiveExam $liveExam, LiveExamAttempt $attempt)
    {
        $request->validate([
            'score' => 'required|integer|min:0|max:' . $liveExam->total_marks,
            'remarks' => 'nullable|string',
        ]);

        $percentage = ($request->score / $liveExam->total_marks) * 100;

        $attempt->update([
            'score' => $request->score,
            'percentage' => $percentage,
            'status' => 'graded',
            'teacher_remarks' => $request->remarks,
        ]);

        return redirect()->route('teacher.live-exams.attempts', $liveExam)
            ->with('success', 'Exam attempt graded successfully.');
    }
}
