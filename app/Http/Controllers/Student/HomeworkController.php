<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\HomeworkAssignment;
use App\Models\HomeworkSubmission;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class HomeworkController extends Controller
{
    public function __construct()
    {
        $this->middleware('student');
    }

    /**
     * Display student's homework assignments
     */
    public function index(Request $request)
    {
        try {
            $student = $request->user()->student;
        } catch (\Exception $e) {
            return redirect()->route('student.dashboard')
                ->with('error', 'Student profile not available. Please contact administrator.');
        }
        
        if (!$student) {
            return redirect()->route('student.dashboard')
                ->with('error', 'Student record not found. Please contact administrator.');
        }

        // Get assignments for student's class
        $query = HomeworkAssignment::where('class_id', $student->class_id)
                                 ->where('is_published', true)
                                 ->where('is_active', true)
                                 ->with(['subject', 'teacher.user']);

        $assignments = $query->orderBy('due_date', 'asc')->paginate(15);

        // Add submission status for each assignment
        foreach ($assignments as $assignment) {
            $assignment->student_submission = HomeworkSubmission::where('homework_assignment_id', $assignment->id)
                                                               ->where('student_id', $student->id)
                                                               ->first();
            $assignment->is_overdue = now()->gt($assignment->due_date);
        }

        return view('student.homework.index', compact('assignments'));
    }

    /**
     * Show specific homework assignment
     */
    public function show(HomeworkAssignment $assignment)
    {
        try {
            $student = auth()->user()->student;
        } catch (\Exception $e) {
            return redirect()->route('student.dashboard')
                ->with('error', 'Student profile not available. Please contact administrator.');
        }
        
        if (!$student) {
            return redirect()->route('student.dashboard')
                ->with('error', 'Student record not found. Please contact administrator.');
        }
        
        // Verify student can access this assignment
        if ($assignment->class_id !== $student->class_id || !$assignment->is_published) {
            return redirect()->route('student.homework.index')
                ->with('error', 'Assignment not found.');
        }

        $assignment->load(['subject', 'teacher.user']);
        
        // Get student's submission if exists
        $submission = HomeworkSubmission::where('homework_assignment_id', $assignment->id)
                                       ->where('student_id', $student->id)
                                       ->first();

        $isOverdue = now()->gt($assignment->due_date);

        return view('student.homework.show', compact('assignment', 'submission', 'isOverdue'));
    }

    /**
     * Show form for submitting homework
     */
    public function create(HomeworkAssignment $assignment)
    {
        try {
            $student = auth()->user()->student;
        } catch (\Exception $e) {
            return redirect()->route('student.dashboard')
                ->with('error', 'Student profile not available. Please contact administrator.');
        }
        
        if (!$student) {
            return redirect()->route('student.dashboard')
                ->with('error', 'Student record not found. Please contact administrator.');
        }
        
        // Verify student can submit
        if ($assignment->class_id !== $student->class_id || !$assignment->is_published) {
            return redirect()->route('student.homework.index')
                ->with('error', 'Assignment not accessible.');
        }

        $assignment->load(['subject', 'teacher.user']);
        $isOverdue = now()->gt($assignment->due_date);

        return view('student.homework.create', compact('assignment', 'isOverdue'));
    }

    /**
     * Store homework submission
     */
    public function store(Request $request, HomeworkAssignment $assignment)
    {
        try {
            $student = $request->user()->student;
        } catch (\Exception $e) {
            return redirect()->route('student.dashboard')
                ->with('error', 'Student profile not available. Please contact administrator.');
        }
        
        if (!$student) {
            return redirect()->route('student.dashboard')
                ->with('error', 'Student record not found. Please contact administrator.');
        }
        
        $validated = $request->validate([
            'submission_text' => 'nullable|string',
            'attachments' => 'nullable|array',
            'attachments.*' => 'file|max:10240',
        ]);

        // Handle file uploads
        $attachmentPaths = [];
        if ($request->hasFile('attachments')) {
            foreach ($request->file('attachments') as $file) {
                $path = $file->store('homework/submissions', 'public');
                $attachmentPaths[] = [
                    'name' => $file->getClientOriginalName(),
                    'path' => $path,
                    'size' => $file->getSize(),
                    'type' => $file->getMimeType(),
                ];
            }
        }

        $isLate = now()->gt($assignment->due_date);

        $submission = HomeworkSubmission::create([
            'homework_assignment_id' => $assignment->id,
            'student_id' => $student->id,
            'submission_text' => $validated['submission_text'],
            'attachments' => $attachmentPaths,
            'submitted_at' => now(),
            'is_late' => $isLate,
            'days_late' => $isLate ? now()->diffInDays($assignment->due_date) : 0,
            'status' => 'submitted',
        ]);

        // Notify teacher of submission
        $this->notifyTeacherOfSubmission($submission);

        return redirect()->route('student.homework.show', $assignment)
                       ->with('success', 'Homework submitted successfully! Teacher has been notified.');
    }

    /**
     * Notify teacher when a student submits homework
     */
    private function notifyTeacherOfSubmission($submission)
    {
        try {
            $assignment = $submission->homeworkAssignment;
            $student = $submission->student;
            $teacher = $assignment->teacher;

            if ($teacher && $teacher->user) {
                $notification = new \App\Models\Notification([
                    'user_id' => $teacher->user->id,
                    'type' => 'homework_submitted',
                    'title' => 'New Homework Submission: ' . $assignment->title,
                    'message' => $student->user->name . ' has submitted their homework for "' . $assignment->title . '".',
                    'category' => 'academic',
                    'subcategory' => 'homework',
                    'priority' => 5, // Medium-High priority
                    'status' => 'pending',
                    'delivery_method' => 'in_app',
                    'delivery_status' => 'pending',
                    'action_url' => route('teacher.homework.show', $assignment),
                    'action_text' => 'Review Submission',
                    'related_model' => 'HomeworkSubmission',
                    'related_id' => $submission->id,
                    'metadata' => [
                        'submission_id' => $submission->id,
                        'assignment_id' => $assignment->id,
                        'student_name' => $student->user->name,
                        'submitted_at' => $submission->submitted_at->toISOString(),
                        'is_late' => $submission->is_late,
                        'subject_name' => $assignment->subject->name,
                        'class_name' => $assignment->classRoom->name
                    ],
                    'is_active' => true
                ]);
                $notification->save();
            }
        } catch (\Exception $e) {
            \Log::error('Failed to notify teacher of homework submission: ' . $e->getMessage());
        }
    }
}
