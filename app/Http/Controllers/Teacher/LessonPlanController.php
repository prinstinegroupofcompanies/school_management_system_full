<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\LessonPlan;
use App\Models\Subject;
use App\Models\ClassRoom;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class LessonPlanController extends Controller
{
    public function __construct()
    {
        $this->middleware('teacher');
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try {
            $teacher = Auth::user()->teacher;
            $query = LessonPlan::with(['subject', 'class', 'approvals.approver'])
                ->where('teacher_id', $teacher->id);

            // Apply filters
            if ($request->filled('status')) {
                $query->where('status', $request->status);
            }

            if ($request->filled('subject_id')) {
                $query->where('subject_id', $request->subject_id);
            }

            if ($request->filled('class_id')) {
                $query->where('class_id', $request->class_id);
            }

            if ($request->filled('date_from')) {
                $query->where('lesson_date', '>=', $request->date_from);
            }

            if ($request->filled('date_to')) {
                $query->where('lesson_date', '<=', $request->date_to);
            }

            $lessonPlans = $query->orderBy('lesson_date', 'desc')
                ->orderBy('created_at', 'desc')
                ->paginate(15);

            // Get filter options
            $subjects = Subject::where('teacher_id', $teacher->id)->get();
            $classes = ClassRoom::all();

            // Get statistics
            $stats = [
                'total' => LessonPlan::where('teacher_id', $teacher->id)->count(),
                'draft' => LessonPlan::where('teacher_id', $teacher->id)->where('status', 'draft')->count(),
                'submitted' => LessonPlan::where('teacher_id', $teacher->id)->where('status', 'submitted')->count(),
                'approved' => LessonPlan::where('teacher_id', $teacher->id)->where('status', 'second_level_approved')->count(),
                'rejected' => LessonPlan::where('teacher_id', $teacher->id)->where('status', 'rejected')->count(),
            ];

            return view('teacher.lesson-plans.index', compact('lessonPlans', 'subjects', 'classes', 'stats'));
        } catch (\Exception $e) {
            \Log::error('Error fetching lesson plans: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to load lesson plans.');
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        try {
            $teacher = Auth::user()->teacher;
            $subjects = Subject::where('teacher_id', $teacher->id)->get();
            $classes = ClassRoom::all();

            return view('teacher.lesson-plans.create', compact('subjects', 'classes'));
        } catch (\Exception $e) {
            \Log::error('Error loading lesson plan form: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to load lesson plan form.');
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'subject_id' => 'required|exists:subjects,id',
            'class_id' => 'required|exists:class_rooms,id',
            'description' => 'required|string',
            'objectives' => 'required|string',
            'materials_needed' => 'required|string',
            'activities' => 'required|string',
            'assessment' => 'required|string',
            'homework' => 'required|string',
            'lesson_date' => 'required|date|after_or_equal:today',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'notes' => 'nullable|string',
            'attachments' => 'nullable|array',
            'attachments.*' => 'file|mimes:pdf,doc,docx,jpg,jpeg,png|max:2048'
        ]);

        try {
            DB::beginTransaction();

            $teacher = Auth::user()->teacher;
            
            // Calculate duration
            $startTime = \Carbon\Carbon::createFromFormat('H:i', $request->start_time);
            $endTime = \Carbon\Carbon::createFromFormat('H:i', $request->end_time);
            $durationMinutes = $endTime->diffInMinutes($startTime);

            // Handle file uploads
            $attachments = [];
            if ($request->hasFile('attachments')) {
                foreach ($request->file('attachments') as $file) {
                    $path = $file->store('lesson-plans/attachments', 'public');
                    $attachments[] = [
                        'name' => $file->getClientOriginalName(),
                        'path' => $path,
                        'size' => $file->getSize(),
                        'type' => $file->getMimeType()
                    ];
                }
            }

            $lessonPlan = LessonPlan::create([
                'teacher_id' => $teacher->id,
                'subject_id' => $request->subject_id,
                'class_id' => $request->class_id,
                'title' => $request->title,
                'description' => $request->description,
                'objectives' => $request->objectives,
                'materials_needed' => $request->materials_needed,
                'activities' => $request->activities,
                'assessment' => $request->assessment,
                'homework' => $request->homework,
                'notes' => $request->notes,
                'lesson_date' => $request->lesson_date,
                'start_time' => $request->start_time,
                'end_time' => $request->end_time,
                'duration_minutes' => $durationMinutes,
                'status' => 'draft',
                'attachments' => $attachments,
                'metadata' => [
                    'created_by' => Auth::id(),
                    'created_at' => now()->toISOString()
                ]
            ]);

            DB::commit();

            return redirect()->route('teacher.lesson-plans.show', $lessonPlan)
                ->with('success', 'Lesson plan created successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error creating lesson plan: ' . $e->getMessage());
            return redirect()->back()
                ->withInput()
                ->with('error', 'Failed to create lesson plan.');
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(LessonPlan $lessonPlan)
    {
        try {
            // Check if teacher owns this lesson plan
            if ($lessonPlan->teacher_id !== Auth::user()->teacher->id) {
                abort(403, 'Unauthorized access to lesson plan.');
            }

            $lessonPlan->load(['subject', 'class', 'approvals.approver']);

            return view('teacher.lesson-plans.show', compact('lessonPlan'));
        } catch (\Exception $e) {
            \Log::error('Error loading lesson plan: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to load lesson plan.');
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(LessonPlan $lessonPlan)
    {
        try {
            // Check if teacher owns this lesson plan
            if ($lessonPlan->teacher_id !== Auth::user()->teacher->id) {
                abort(403, 'Unauthorized access to lesson plan.');
            }

            // Check if lesson plan can be edited
            if (!$lessonPlan->canBeEdited()) {
                return redirect()->route('teacher.lesson-plans.show', $lessonPlan)
                    ->with('error', 'This lesson plan cannot be edited.');
            }

            $teacher = Auth::user()->teacher;
            $subjects = Subject::where('teacher_id', $teacher->id)->get();
            $classes = ClassRoom::all();

            return view('teacher.lesson-plans.edit', compact('lessonPlan', 'subjects', 'classes'));
        } catch (\Exception $e) {
            \Log::error('Error loading lesson plan edit form: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to load lesson plan edit form.');
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, LessonPlan $lessonPlan)
    {
        // Check if teacher owns this lesson plan
        if ($lessonPlan->teacher_id !== Auth::user()->teacher->id) {
            abort(403, 'Unauthorized access to lesson plan.');
        }

        // Check if lesson plan can be edited
        if (!$lessonPlan->canBeEdited()) {
            return redirect()->route('teacher.lesson-plans.show', $lessonPlan)
                ->with('error', 'This lesson plan cannot be edited.');
        }

        $request->validate([
            'title' => 'required|string|max:255',
            'subject_id' => 'required|exists:subjects,id',
            'class_id' => 'required|exists:class_rooms,id',
            'description' => 'required|string',
            'objectives' => 'required|string',
            'materials_needed' => 'required|string',
            'activities' => 'required|string',
            'assessment' => 'required|string',
            'homework' => 'required|string',
            'lesson_date' => 'required|date',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'notes' => 'nullable|string',
            'attachments' => 'nullable|array',
            'attachments.*' => 'file|mimes:pdf,doc,docx,jpg,jpeg,png|max:2048'
        ]);

        try {
            DB::beginTransaction();

            // Calculate duration
            $startTime = \Carbon\Carbon::createFromFormat('H:i', $request->start_time);
            $endTime = \Carbon\Carbon::createFromFormat('H:i', $request->end_time);
            $durationMinutes = $endTime->diffInMinutes($startTime);

            // Handle file uploads
            $attachments = $lessonPlan->attachments ?? [];
            if ($request->hasFile('attachments')) {
                foreach ($request->file('attachments') as $file) {
                    $path = $file->store('lesson-plans/attachments', 'public');
                    $attachments[] = [
                        'name' => $file->getClientOriginalName(),
                        'path' => $path,
                        'size' => $file->getSize(),
                        'type' => $file->getMimeType()
                    ];
                }
            }

            $lessonPlan->update([
                'subject_id' => $request->subject_id,
                'class_id' => $request->class_id,
                'title' => $request->title,
                'description' => $request->description,
                'objectives' => $request->objectives,
                'materials_needed' => $request->materials_needed,
                'activities' => $request->activities,
                'assessment' => $request->assessment,
                'homework' => $request->homework,
                'notes' => $request->notes,
                'lesson_date' => $request->lesson_date,
                'start_time' => $request->start_time,
                'end_time' => $request->end_time,
                'duration_minutes' => $durationMinutes,
                'attachments' => $attachments,
                'metadata' => array_merge($lessonPlan->metadata ?? [], [
                    'updated_by' => Auth::id(),
                    'updated_at' => now()->toISOString()
                ])
            ]);

            DB::commit();

            return redirect()->route('teacher.lesson-plans.show', $lessonPlan)
                ->with('success', 'Lesson plan updated successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error updating lesson plan: ' . $e->getMessage());
            return redirect()->back()
                ->withInput()
                ->with('error', 'Failed to update lesson plan.');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(LessonPlan $lessonPlan)
    {
        try {
            // Check if teacher owns this lesson plan
            if ($lessonPlan->teacher_id !== Auth::user()->teacher->id) {
                abort(403, 'Unauthorized access to lesson plan.');
            }

            // Check if lesson plan can be deleted
            if (!in_array($lessonPlan->status, ['draft', 'rejected'])) {
                return redirect()->back()->with('error', 'This lesson plan cannot be deleted.');
            }

            $lessonPlan->delete();

            return redirect()->route('teacher.lesson-plans.index')
                ->with('success', 'Lesson plan deleted successfully.');
        } catch (\Exception $e) {
            \Log::error('Error deleting lesson plan: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to delete lesson plan.');
        }
    }

    /**
     * Submit lesson plan for approval
     */
    public function submit(LessonPlan $lessonPlan)
    {
        try {
            // Check if teacher owns this lesson plan
            if ($lessonPlan->teacher_id !== Auth::user()->teacher->id) {
                abort(403, 'Unauthorized access to lesson plan.');
            }

            if (!$lessonPlan->canBeSubmitted()) {
                return redirect()->back()->with('error', 'This lesson plan cannot be submitted.');
            }

            $lessonPlan->submit();

            return redirect()->route('teacher.lesson-plans.show', $lessonPlan)
                ->with('success', 'Lesson plan submitted for approval.');
        } catch (\Exception $e) {
            \Log::error('Error submitting lesson plan: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to submit lesson plan.');
        }
    }
}
