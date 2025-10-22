<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LessonPlan;
use App\Models\Subject;
use App\Models\ClassRoom;
use App\Models\Teacher;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class LessonPlanController extends Controller
{
    public function __construct()
    {
        $this->middleware('admin');
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try {
            $query = LessonPlan::with(['teacher.user', 'subject', 'class', 'approvals.approver']);

            // Apply filters
            if ($request->filled('status')) {
                $query->where('status', $request->status);
            }

            if ($request->filled('teacher_id')) {
                $query->where('teacher_id', $request->teacher_id);
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
                ->paginate(20);

            // Get filter options
            $teachers = Teacher::with('user')->get();
            $subjects = Subject::with('teacher.user')->get();
            $classes = ClassRoom::all();
            $statuses = ['draft', 'submitted', 'first_level_approved', 'second_level_approved', 'rejected'];

            // Get statistics
            $stats = [
                'total' => LessonPlan::count(),
                'draft' => LessonPlan::where('status', 'draft')->count(),
                'submitted' => LessonPlan::where('status', 'submitted')->count(),
                'first_level_approved' => LessonPlan::where('status', 'first_level_approved')->count(),
                'second_level_approved' => LessonPlan::where('status', 'second_level_approved')->count(),
                'rejected' => LessonPlan::where('status', 'rejected')->count(),
            ];

            return view('admin.lesson-plans.index', compact('lessonPlans', 'teachers', 'subjects', 'classes', 'stats', 'statuses'));
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
            $subjects = Subject::orderBy('name')->get();
            $classes = ClassRoom::orderBy('name')->get();
            $teachers = Teacher::with('user')->get();

            return view('admin.lesson-plans.create', compact('subjects', 'classes', 'teachers'));
        } catch (\Exception $e) {
            \Log::error('Error loading lesson plan create form: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to load create form.');
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'subject_id' => 'required|exists:subjects,id',
            'class_id' => 'required|exists:class_rooms,id',
            'teacher_id' => 'required|exists:teachers,id',
            'description' => 'required|string',
            'objectives' => 'required|string',
            'materials_needed' => 'required|string',
            'activities' => 'required|string',
            'assessment' => 'required|string',
            'homework' => 'nullable|string',
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
                'teacher_id' => $request->teacher_id,
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
                'status' => 'submitted',
                'attachments' => $attachments,
                'metadata' => [
                    'created_by' => Auth::id(),
                    'created_at' => now()->toISOString()
                ]
            ]);

            DB::commit();

            return redirect()->route('admin.lesson-plans.index')
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
            $lessonPlan->load(['teacher.user', 'subject', 'class', 'approvals.approver']);

            return view('admin.lesson-plans.show', compact('lessonPlan'));
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
            $lessonPlan->load(['teacher.user', 'subject', 'class']);
            $subjects = Subject::orderBy('name')->get();
            $classes = ClassRoom::orderBy('name')->get();
            $teachers = Teacher::with('user')->get();

            return view('admin.lesson-plans.edit', compact('lessonPlan', 'subjects', 'classes', 'teachers'));
        } catch (\Exception $e) {
            \Log::error('Error loading lesson plan edit form: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to load edit form.');
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, LessonPlan $lessonPlan)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'subject_id' => 'required|exists:subjects,id',
            'class_id' => 'required|exists:class_rooms,id',
            'teacher_id' => 'required|exists:teachers,id',
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
                'teacher_id' => $request->teacher_id,
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

            return redirect()->route('admin.lesson-plans.show', $lessonPlan)
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
            $lessonPlan->delete();

            return redirect()->route('admin.lesson-plans.index')
                ->with('success', 'Lesson plan deleted successfully.');
        } catch (\Exception $e) {
            \Log::error('Error deleting lesson plan: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to delete lesson plan.');
        }
    }

    public function download(LessonPlan $lessonPlan)
    {
        try {
            $lessonPlan->load(['teacher.user', 'subject', 'class']);
            
            $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('admin.lesson-plans.pdf', compact('lessonPlan'));
            $pdf->setPaper('A4', 'portrait');
            
            $filename = 'lesson-plan-' . $lessonPlan->id . '-' . now()->format('Y-m-d') . '.pdf';
            
            return $pdf->download($filename);
        } catch (\Exception $e) {
            \Log::error('Error downloading lesson plan PDF: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to download lesson plan.');
        }
    }
}