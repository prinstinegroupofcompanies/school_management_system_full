<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LessonPlan;
use App\Models\LessonPlanApproval;
use App\Models\Subject;
use App\Models\ClassRoom;
use App\Models\Teacher;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;

class LessonPlanApprovalController extends Controller
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
            $query = LessonPlan::with(['teacher.user', 'subject', 'class', 'approvals.approver'])
                ->whereIn('status', ['submitted', 'first_level_approved', 'second_level_approved', 'rejected']);

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

            // Get statistics
            $stats = [
                'total' => LessonPlan::whereIn('status', ['submitted', 'first_level_approved', 'second_level_approved', 'rejected'])->count(),
                'pending' => LessonPlan::whereIn('status', ['submitted', 'first_level_approved'])->count(),
                'approved' => LessonPlan::where('status', 'second_level_approved')->count(),
                'rejected' => LessonPlan::where('status', 'rejected')->count(),
            ];

            return view('admin.lesson-plans.index', compact('lessonPlans', 'teachers', 'subjects', 'classes', 'stats'));
        } catch (\Exception $e) {
            \Log::error('Error fetching lesson plans for approval: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to load lesson plans.');
        }
    }

    /**
     * Display pending lesson plans
     */
    public function pending(Request $request)
    {
        try {
            $query = LessonPlan::with(['teacher.user', 'subject', 'class', 'approvals.approver'])
                ->whereIn('status', ['submitted', 'first_level_approved']);

            // Apply filters
            if ($request->filled('teacher_id')) {
                $query->where('teacher_id', $request->teacher_id);
            }

            if ($request->filled('subject_id')) {
                $query->where('subject_id', $request->subject_id);
            }

            if ($request->filled('class_id')) {
                $query->where('class_id', $request->class_id);
            }

            $lessonPlans = $query->orderBy('created_at', 'asc')
                ->paginate(20);

            // Get filter options
            $teachers = Teacher::with('user')->get();
            $subjects = Subject::with('teacher.user')->get();
            $classes = ClassRoom::all();

            return view('admin.lesson-plans.pending', compact('lessonPlans', 'teachers', 'subjects', 'classes'));
        } catch (\Exception $e) {
            \Log::error('Error fetching pending lesson plans: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to load pending lesson plans.');
        }
    }

    /**
     * Display approved lesson plans
     */
    public function approved(Request $request)
    {
        try {
            $query = LessonPlan::with(['teacher.user', 'subject', 'class', 'approvals.approver'])
                ->where('status', 'second_level_approved');

            // Apply filters
            if ($request->filled('teacher_id')) {
                $query->where('teacher_id', $request->teacher_id);
            }

            if ($request->filled('subject_id')) {
                $query->where('subject_id', $request->subject_id);
            }

            if ($request->filled('class_id')) {
                $query->where('class_id', $request->class_id);
            }

            $lessonPlans = $query->orderBy('lesson_date', 'desc')
                ->paginate(20);

            // Get filter options
            $teachers = Teacher::with('user')->get();
            $subjects = Subject::with('teacher.user')->get();
            $classes = ClassRoom::all();

            return view('admin.lesson-plans.approved', compact('lessonPlans', 'teachers', 'subjects', 'classes'));
        } catch (\Exception $e) {
            \Log::error('Error fetching approved lesson plans: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to load approved lesson plans.');
        }
    }

    /**
     * Display rejected lesson plans
     */
    public function rejected(Request $request)
    {
        try {
            $query = LessonPlan::with(['teacher.user', 'subject', 'class', 'approvals.approver'])
                ->where('status', 'rejected');

            // Apply filters
            if ($request->filled('teacher_id')) {
                $query->where('teacher_id', $request->teacher_id);
            }

            if ($request->filled('subject_id')) {
                $query->where('subject_id', $request->subject_id);
            }

            if ($request->filled('class_id')) {
                $query->where('class_id', $request->class_id);
            }

            $lessonPlans = $query->orderBy('created_at', 'desc')
                ->paginate(20);

            // Get filter options
            $teachers = Teacher::with('user')->get();
            $subjects = Subject::with('teacher.user')->get();
            $classes = ClassRoom::all();

            return view('admin.lesson-plans.rejected', compact('lessonPlans', 'teachers', 'subjects', 'classes'));
        } catch (\Exception $e) {
            \Log::error('Error fetching rejected lesson plans: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to load rejected lesson plans.');
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
     * Approve lesson plan
     */
    public function approve(Request $request, LessonPlan $lessonPlan)
    {
        $request->validate([
            'comments' => 'nullable|string|max:1000',
            'e_signature' => 'nullable|string'
        ]);

        try {
            DB::beginTransaction();

            $user = Auth::user();
            $approvalLevel = $lessonPlan->status === 'submitted' ? 'first_level' : 'second_level';

            $lessonPlan->approve($user->id, $approvalLevel, $request->comments, $request->e_signature);

            // Send notification to teacher
            $lessonPlan->teacher->user->notifications()->create([
                'title' => 'Lesson Plan Approved',
                'message' => "Your lesson plan '{$lessonPlan->title}' has been approved.",
                'type' => 'lesson_plan_approved',
                'category' => 'academic',
                'related_model' => 'LessonPlan',
                'related_id' => $lessonPlan->id,
                'action_url' => route('teacher.lesson-plans.show', $lessonPlan)
            ]);

            DB::commit();

            return redirect()->route('admin.lesson-plans.show', $lessonPlan)
                ->with('success', 'Lesson plan approved successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error approving lesson plan: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to approve lesson plan.');
        }
    }

    /**
     * Reject lesson plan
     */
    public function reject(Request $request, LessonPlan $lessonPlan)
    {
        $request->validate([
            'rejection_reason' => 'required|string|max:1000',
            'e_signature' => 'nullable|string'
        ]);

        try {
            DB::beginTransaction();

            $user = Auth::user();
            $approvalLevel = $lessonPlan->status === 'submitted' ? 'first_level' : 'second_level';

            $lessonPlan->reject($user->id, $approvalLevel, $request->rejection_reason, $request->e_signature);

            // Send notification to teacher
            $lessonPlan->teacher->user->notifications()->create([
                'title' => 'Lesson Plan Rejected',
                'message' => "Your lesson plan '{$lessonPlan->title}' has been rejected. Reason: {$request->rejection_reason}",
                'type' => 'lesson_plan_rejected',
                'category' => 'academic',
                'related_model' => 'LessonPlan',
                'related_id' => $lessonPlan->id,
                'action_url' => route('teacher.lesson-plans.show', $lessonPlan)
            ]);

            DB::commit();

            return redirect()->route('admin.lesson-plans.show', $lessonPlan)
                ->with('success', 'Lesson plan rejected successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error rejecting lesson plan: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to reject lesson plan.');
        }
    }

    /**
     * Download lesson plan as PDF
     */
    public function download(LessonPlan $lessonPlan)
    {
        try {
            $lessonPlan->load(['teacher.user', 'subject', 'class', 'approvals.approver']);

            $pdf = Pdf::loadView('admin.lesson-plans.pdf', compact('lessonPlan'));
            
            return $pdf->download("lesson-plan-{$lessonPlan->id}-{$lessonPlan->title}.pdf");
        } catch (\Exception $e) {
            \Log::error('Error downloading lesson plan: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to download lesson plan.');
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
        try {
            $request->validate([
                'title' => 'required|string|max:255',
                'subject_id' => 'required|exists:subjects,id',
                'class_id' => 'required|exists:class_rooms,id',
                'teacher_id' => 'required|exists:teachers,id',
                'week_start_date' => 'required|date',
                'week_end_date' => 'required|date|after:week_start_date',
                'objectives' => 'required|string',
                'materials' => 'required|string',
                'activities' => 'required|string',
                'assessment' => 'required|string',
                'homework' => 'nullable|string',
                'notes' => 'nullable|string',
            ]);

            $lessonPlan = LessonPlan::create([
                'title' => $request->title,
                'subject_id' => $request->subject_id,
                'class_id' => $request->class_id,
                'teacher_id' => $request->teacher_id,
                'week_start_date' => $request->week_start_date,
                'week_end_date' => $request->week_end_date,
                'objectives' => $request->objectives,
                'materials' => $request->materials,
                'activities' => $request->activities,
                'assessment' => $request->assessment,
                'homework' => $request->homework,
                'notes' => $request->notes,
                'status' => 'submitted',
                'submitted_at' => now(),
            ]);

            return redirect()->route('admin.lesson-plans.show', $lessonPlan)
                ->with('success', 'Lesson plan created successfully.');
        } catch (\Exception $e) {
            \Log::error('Error creating lesson plan: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to create lesson plan.');
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
        try {
            $request->validate([
                'title' => 'required|string|max:255',
                'subject_id' => 'required|exists:subjects,id',
                'class_id' => 'required|exists:class_rooms,id',
                'teacher_id' => 'required|exists:teachers,id',
                'week_start_date' => 'required|date',
                'week_end_date' => 'required|date|after:week_start_date',
                'objectives' => 'required|string',
                'materials' => 'required|string',
                'activities' => 'required|string',
                'assessment' => 'required|string',
                'homework' => 'nullable|string',
                'notes' => 'nullable|string',
            ]);

            $lessonPlan->update([
                'title' => $request->title,
                'subject_id' => $request->subject_id,
                'class_id' => $request->class_id,
                'teacher_id' => $request->teacher_id,
                'week_start_date' => $request->week_start_date,
                'week_end_date' => $request->week_end_date,
                'objectives' => $request->objectives,
                'materials' => $request->materials,
                'activities' => $request->activities,
                'assessment' => $request->assessment,
                'homework' => $request->homework,
                'notes' => $request->notes,
            ]);

            return redirect()->route('admin.lesson-plans.show', $lessonPlan)
                ->with('success', 'Lesson plan updated successfully.');
        } catch (\Exception $e) {
            \Log::error('Error updating lesson plan: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to update lesson plan.');
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
}
