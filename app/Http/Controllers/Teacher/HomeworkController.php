<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\HomeworkAssignment;
use App\Models\HomeworkSubmission;
use App\Models\Student;
use App\Models\Subject;
use App\Models\ClassRoom;
use App\Models\Teacher;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class HomeworkController extends Controller
{
    public function __construct()
    {
        $this->middleware('teacher');
    }

    /**
     * Display teacher's homework assignments
     */
    public function index(Request $request)
    {
        $teacher = $request->user()->teacher;
        
        if (!$teacher) {
            return redirect()->route('teacher.dashboard')
                           ->withErrors(['error' => 'Teacher profile not found.']);
        }

        $query = HomeworkAssignment::where('teacher_id', $teacher->id)
                                 ->with(['subject', 'classRoom']);

        // Apply filters
        if ($request->filled('subject_id')) {
            $query->where('subject_id', $request->subject_id);
        }

        if ($request->filled('class_id')) {
            $query->where('class_id', $request->class_id);
        }

        if ($request->filled('assignment_type')) {
            $query->where('assignment_type', $request->assignment_type);
        }

        if ($request->filled('status')) {
            if ($request->status === 'published') {
                $query->where('is_published', true);
            } elseif ($request->status === 'draft') {
                $query->where('is_published', false);
            }
        }

        $assignments = $query->orderBy('due_date', 'desc')->paginate(15);
        
        // Get filter options
        $subjects = Subject::where('teacher_id', $teacher->id)->get();
        $classes = ClassRoom::whereHas('subjects', function($query) use ($teacher) {
            $query->where('teacher_id', $teacher->id);
        })->get();

        $assignmentTypes = ['homework', 'project', 'research', 'presentation'];

        // Get summary statistics
        $stats = [
            'total_assignments' => HomeworkAssignment::where('teacher_id', $teacher->id)->count(),
            'published_assignments' => HomeworkAssignment::where('teacher_id', $teacher->id)->where('is_published', true)->count(),
            'draft_assignments' => HomeworkAssignment::where('teacher_id', $teacher->id)->where('is_published', false)->count(),
            'overdue_assignments' => HomeworkAssignment::where('teacher_id', $teacher->id)
                                                      ->where('is_published', true)
                                                      ->where('due_date', '<', now())
                                                      ->count(),
            'pending_submissions' => HomeworkSubmission::whereHas('homeworkAssignment', function($query) use ($teacher) {
                                                          $query->where('teacher_id', $teacher->id);
                                                      })
                                                      ->where('status', 'submitted')
                                                      ->count(),
        ];

        return view('teacher.homework.index', compact('assignments', 'subjects', 'classes', 'assignmentTypes', 'stats'));
    }

    /**
     * Show form for creating new homework assignment
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

        $assignmentTypes = [
            'homework' => 'Homework',
            'project' => 'Project',
            'research' => 'Research Assignment',
            'presentation' => 'Presentation'
        ];

        return view('teacher.homework.create', compact('subjects', 'classes', 'assignmentTypes'));
    }

    /**
     * Store newly created homework assignment
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
            'description' => 'required|string',
            'subject_id' => 'required|exists:subjects,id',
            'class_id' => 'required|exists:class_rooms,id',
            'assignment_type' => 'required|string|max:50',
            'instructions' => 'nullable|array',
            'due_date' => 'required|date|after:now',
            'total_points' => 'required|integer|min:1',
            'allow_late_submission' => 'boolean',
            'late_penalty_percentage' => 'nullable|numeric|min:0|max:100',
            'attachments' => 'nullable|array',
            'attachments.*' => 'file|max:10240', // 10MB max per file
        ]);

        // Verify teacher has permission for this subject
        $subject = Subject::where('id', $validated['subject_id'])
                         ->where('teacher_id', $teacher->id)
                         ->first();

        if (!$subject) {
            return back()->withErrors(['subject_id' => 'You are not authorized to create assignments for this subject.'])
                        ->withInput();
        }

        DB::beginTransaction();
        try {
            $validated['teacher_id'] = $teacher->id;
            $validated['assigned_at'] = now();
            $validated['allow_late_submission'] = $request->boolean('allow_late_submission', true);
            $validated['late_penalty_percentage'] = $validated['late_penalty_percentage'] ?? 10.0;
            $validated['is_published'] = false; // Start as draft

            // Handle file uploads
            $attachmentPaths = [];
            if ($request->hasFile('attachments')) {
                foreach ($request->file('attachments') as $file) {
                    $path = $file->store('homework/attachments', 'public');
                    $attachmentPaths[] = [
                        'name' => $file->getClientOriginalName(),
                        'path' => $path,
                        'size' => $file->getSize(),
                        'type' => $file->getMimeType(),
                    ];
                }
            }
            $validated['attachments'] = $attachmentPaths;

            $assignment = HomeworkAssignment::create($validated);

            DB::commit();

            return redirect()->route('teacher.homework.show', $assignment)
                           ->with('success', 'Homework assignment created successfully.');

        } catch (\Exception $e) {
            DB::rollback();
            return back()->withErrors(['error' => 'Failed to create assignment: ' . $e->getMessage()])
                        ->withInput();
        }
    }

    /**
     * Show specific homework assignment with submissions
     */
    public function show(HomeworkAssignment $assignment)
    {
        $teacher = auth()->user()->teacher;
        
        if ($assignment->teacher_id !== $teacher->id) {
            return redirect()->route('teacher.homework.index')
                           ->withErrors(['error' => 'You are not authorized to view this assignment.']);
        }

        $assignment->load(['subject', 'classRoom']);
        
        // Get submissions
        $submissions = HomeworkSubmission::where('homework_assignment_id', $assignment->id)
                                        ->with(['student.user'])
                                        ->orderBy('submitted_at', 'desc')
                                        ->paginate(15);

        // Get statistics
        $totalStudents = Student::where('class_id', $assignment->class_id)->count();
        $stats = [
            'total_students' => $totalStudents,
            'submitted_count' => HomeworkSubmission::where('homework_assignment_id', $assignment->id)->count(),
            'graded_count' => HomeworkSubmission::where('homework_assignment_id', $assignment->id)->where('status', 'graded')->count(),
            'pending_review' => HomeworkSubmission::where('homework_assignment_id', $assignment->id)->where('status', 'submitted')->count(),
            'submission_rate' => $totalStudents > 0 ? (HomeworkSubmission::where('homework_assignment_id', $assignment->id)->count() / $totalStudents) * 100 : 0,
            'avg_score' => HomeworkSubmission::where('homework_assignment_id', $assignment->id)->whereNotNull('score')->avg('score'),
        ];

        return view('teacher.homework.show', compact('assignment', 'submissions', 'stats'));
    }

    /**
     * Grade homework submission
     */
    public function gradeSubmission(Request $request, HomeworkSubmission $submission)
    {
        $teacher = auth()->user()->teacher;
        $assignment = $submission->homeworkAssignment;
        
        if ($assignment->teacher_id !== $teacher->id) {
            return back()->withErrors(['error' => 'Unauthorized']);
        }

        $validated = $request->validate([
            'score' => 'required|numeric|min:0|max:' . $assignment->total_points,
            'teacher_feedback' => 'nullable|string',
            'detailed_feedback' => 'nullable|array',
            'needs_revision' => 'boolean',
            'revision_notes' => 'nullable|string',
        ]);

        DB::beginTransaction();
        try {
            $validated['percentage'] = ($validated['score'] / $assignment->total_points) * 100;
            $validated['letter_grade'] = $this->calculateLetterGrade($validated['percentage']);
            $validated['status'] = $request->boolean('needs_revision') ? 'returned' : 'graded';
            $validated['reviewed_at'] = now();
            $validated['reviewed_by'] = $teacher->user->id;
            $validated['feedback_sent'] = true;
            $validated['feedback_sent_at'] = now();

            $submission->update($validated);

            DB::commit();

            $message = $request->boolean('needs_revision') 
                     ? 'Submission returned to student for revision.' 
                     : 'Submission graded successfully.';

            return back()->with('success', $message);

        } catch (\Exception $e) {
            DB::rollback();
            return back()->withErrors(['error' => 'Failed to grade submission: ' . $e->getMessage()]);
        }
    }

    /**
     * Publish homework assignment
     */
    public function publish(HomeworkAssignment $assignment)
    {
        $teacher = auth()->user()->teacher;
        
        if ($assignment->teacher_id !== $teacher->id) {
            return back()->withErrors(['error' => 'Unauthorized']);
        }

        $assignment->update(['is_published' => true]);

        return back()->with('success', 'Assignment published successfully. Students can now view and submit.');
    }

    /**
     * Delete homework assignment
     */
    public function destroy(HomeworkAssignment $assignment)
    {
        $teacher = auth()->user()->teacher;
        
        if ($assignment->teacher_id !== $teacher->id) {
            return back()->withErrors(['error' => 'Unauthorized']);
        }

        // Check if there are submissions
        $hasSubmissions = HomeworkSubmission::where('homework_assignment_id', $assignment->id)->exists();
        if ($hasSubmissions) {
            return back()->withErrors(['error' => 'Cannot delete assignment that has submissions.']);
        }

        $assignment->delete();

        return redirect()->route('teacher.homework.index')
                       ->with('success', 'Assignment deleted successfully.');
    }

    /**
     * Calculate letter grade from percentage
     */
    private function calculateLetterGrade($percentage): string
    {
        if ($percentage >= 97) return 'A+';
        if ($percentage >= 93) return 'A';
        if ($percentage >= 90) return 'A-';
        if ($percentage >= 87) return 'B+';
        if ($percentage >= 83) return 'B';
        if ($percentage >= 80) return 'B-';
        if ($percentage >= 77) return 'C+';
        if ($percentage >= 73) return 'C';
        if ($percentage >= 70) return 'C-';
        if ($percentage >= 67) return 'D+';
        if ($percentage >= 65) return 'D';
        return 'F';
    }
}