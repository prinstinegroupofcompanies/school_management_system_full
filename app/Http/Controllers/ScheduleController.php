<?php

namespace App\Http\Controllers;

use App\Models\Schedule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ScheduleController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try {
            $query = Schedule::with(['createdBy', 'approvedBy', 'approvals.approver'])
                ->active()
                ->public();

            // Apply filters
            if ($request->filled('type')) {
                $query->where('type', $request->type);
            }

            if ($request->filled('category')) {
                $query->where('category', $request->category);
            }

            if ($request->filled('status')) {
                $query->where('status', $request->status);
            }

            if ($request->filled('date_from')) {
                $query->where('start_date', '>=', $request->date_from);
            }

            if ($request->filled('date_to')) {
                $query->where('start_date', '<=', $request->date_to);
            }

            if ($request->filled('search')) {
                $query->where(function($q) use ($request) {
                    $q->where('title', 'like', '%' . $request->search . '%')
                      ->orWhere('description', 'like', '%' . $request->search . '%')
                      ->orWhere('venue', 'like', '%' . $request->search . '%');
                });
            }

            $schedules = $query->orderBy('start_date', 'asc')
                ->orderBy('start_time', 'asc')
                ->paginate(20);

            // Get statistics
            $stats = [
                'total' => Schedule::active()->public()->count(),
                'upcoming' => Schedule::active()->public()->upcoming(30)->count(),
                'exams' => Schedule::active()->public()->byType('exam')->count(),
                'events' => Schedule::active()->public()->byType('event')->count(),
                'classes' => Schedule::active()->public()->byType('class')->count(),
                'meetings' => Schedule::active()->public()->byType('meeting')->count(),
                'holidays' => Schedule::active()->public()->byType('holiday')->count(),
            ];

            // Get upcoming schedules for calendar
            $upcomingSchedules = Schedule::active()
                ->public()
                ->upcoming(30)
                ->orderBy('start_date', 'asc')
                ->orderBy('start_time', 'asc')
                ->get();

            return view('schedules.index', compact('schedules', 'stats', 'upcomingSchedules'));
        } catch (\Exception $e) {
            \Log::error('Error fetching schedules: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to load schedules.');
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        try {
            return view('schedules.create');
        } catch (\Exception $e) {
            \Log::error('Error loading schedule form: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to load schedule form.');
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'type' => 'required|in:exam,event,class,meeting,holiday,other',
            'category' => 'required|in:academic,administrative,social,sports,cultural,other',
            'start_date' => 'required|date|after_or_equal:today',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'start_time' => 'nullable|date_format:H:i',
            'end_time' => 'nullable|date_format:H:i|after:start_time',
            'venue' => 'nullable|string|max:255',
            'instructions' => 'nullable|string',
            'important_notes' => 'nullable|string',
            'is_recurring' => 'boolean',
            'recurrence_type' => 'nullable|in:daily,weekly,monthly,yearly',
            'recurrence_interval' => 'nullable|integer|min:1',
            'recurrence_end_date' => 'nullable|date|after:start_date',
            'recurrence_days' => 'nullable|array',
            'requires_approval' => 'boolean',
            'is_public' => 'boolean',
            'attachments' => 'nullable|array',
            'attachments.*' => 'file|mimes:pdf,doc,docx,jpg,jpeg,png|max:2048'
        ]);

        try {
            DB::beginTransaction();

            // Handle file uploads
            $attachments = [];
            if ($request->hasFile('attachments')) {
                foreach ($request->file('attachments') as $file) {
                    $path = $file->store('schedules/attachments', 'public');
                    $attachments[] = [
                        'name' => $file->getClientOriginalName(),
                        'path' => $path,
                        'size' => $file->getSize(),
                        'type' => $file->getMimeType()
                    ];
                }
            }

            $schedule = Schedule::create([
                'title' => $request->title,
                'description' => $request->description,
                'type' => $request->type,
                'category' => $request->category,
                'start_date' => $request->start_date,
                'end_date' => $request->end_date,
                'start_time' => $request->start_time,
                'end_time' => $request->end_time,
                'venue' => $request->venue,
                'instructions' => $request->instructions,
                'important_notes' => $request->important_notes,
                'is_recurring' => $request->boolean('is_recurring'),
                'recurrence_type' => $request->recurrence_type,
                'recurrence_interval' => $request->recurrence_interval,
                'recurrence_end_date' => $request->recurrence_end_date,
                'recurrence_days' => $request->recurrence_days,
                'requires_approval' => $request->boolean('requires_approval'),
                'is_public' => $request->boolean('is_public'),
                'attachments' => $attachments,
                'created_by' => Auth::id(),
                'metadata' => [
                    'created_by' => Auth::id(),
                    'created_at' => now()->toISOString()
                ]
            ]);

            DB::commit();

            return redirect()->route('schedules.show', $schedule)
                ->with('success', 'Schedule created successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error creating schedule: ' . $e->getMessage());
            return redirect()->back()
                ->withInput()
                ->with('error', 'Failed to create schedule.');
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Schedule $schedule)
    {
        try {
            $schedule->load(['createdBy', 'approvedBy', 'approvals.approver']);

            return view('schedules.show', compact('schedule'));
        } catch (\Exception $e) {
            \Log::error('Error loading schedule: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to load schedule.');
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Schedule $schedule)
    {
        try {
            // Check if user can edit this schedule
            if ($schedule->created_by !== Auth::id() && !Auth::user()->hasRole('admin')) {
                abort(403, 'Unauthorized access to schedule.');
            }

            // Check if schedule can be edited
            if (!$schedule->canBeEdited()) {
                return redirect()->route('schedules.show', $schedule)
                    ->with('error', 'This schedule cannot be edited.');
            }

            return view('schedules.edit', compact('schedule'));
        } catch (\Exception $e) {
            \Log::error('Error loading schedule edit form: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to load schedule edit form.');
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Schedule $schedule)
    {
        // Check if user can edit this schedule
        if ($schedule->created_by !== Auth::id() && !Auth::user()->hasRole('admin')) {
            abort(403, 'Unauthorized access to schedule.');
        }

        // Check if schedule can be edited
        if (!$schedule->canBeEdited()) {
            return redirect()->route('schedules.show', $schedule)
                ->with('error', 'This schedule cannot be edited.');
        }

        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'type' => 'required|in:exam,event,class,meeting,holiday,other',
            'category' => 'required|in:academic,administrative,social,sports,cultural,other',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'start_time' => 'nullable|date_format:H:i',
            'end_time' => 'nullable|date_format:H:i|after:start_time',
            'venue' => 'nullable|string|max:255',
            'instructions' => 'nullable|string',
            'important_notes' => 'nullable|string',
            'is_recurring' => 'boolean',
            'recurrence_type' => 'nullable|in:daily,weekly,monthly,yearly',
            'recurrence_interval' => 'nullable|integer|min:1',
            'recurrence_end_date' => 'nullable|date|after:start_date',
            'recurrence_days' => 'nullable|array',
            'requires_approval' => 'boolean',
            'is_public' => 'boolean',
            'attachments' => 'nullable|array',
            'attachments.*' => 'file|mimes:pdf,doc,docx,jpg,jpeg,png|max:2048'
        ]);

        try {
            DB::beginTransaction();

            // Handle file uploads
            $attachments = $schedule->attachments ?? [];
            if ($request->hasFile('attachments')) {
                foreach ($request->file('attachments') as $file) {
                    $path = $file->store('schedules/attachments', 'public');
                    $attachments[] = [
                        'name' => $file->getClientOriginalName(),
                        'path' => $path,
                        'size' => $file->getSize(),
                        'type' => $file->getMimeType()
                    ];
                }
            }

            $schedule->update([
                'title' => $request->title,
                'description' => $request->description,
                'type' => $request->type,
                'category' => $request->category,
                'start_date' => $request->start_date,
                'end_date' => $request->end_date,
                'start_time' => $request->start_time,
                'end_time' => $request->end_time,
                'venue' => $request->venue,
                'instructions' => $request->instructions,
                'important_notes' => $request->important_notes,
                'is_recurring' => $request->boolean('is_recurring'),
                'recurrence_type' => $request->recurrence_type,
                'recurrence_interval' => $request->recurrence_interval,
                'recurrence_end_date' => $request->recurrence_end_date,
                'recurrence_days' => $request->recurrence_days,
                'requires_approval' => $request->boolean('requires_approval'),
                'is_public' => $request->boolean('is_public'),
                'attachments' => $attachments,
                'metadata' => array_merge($schedule->metadata ?? [], [
                    'updated_by' => Auth::id(),
                    'updated_at' => now()->toISOString()
                ])
            ]);

            DB::commit();

            return redirect()->route('schedules.show', $schedule)
                ->with('success', 'Schedule updated successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error updating schedule: ' . $e->getMessage());
            return redirect()->back()
                ->withInput()
                ->with('error', 'Failed to update schedule.');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Schedule $schedule)
    {
        try {
            // Check if user can delete this schedule
            if ($schedule->created_by !== Auth::id() && !Auth::user()->hasRole('admin')) {
                abort(403, 'Unauthorized access to schedule.');
            }

            // Check if schedule can be deleted
            if (!in_array($schedule->status, ['draft', 'rejected'])) {
                return redirect()->back()->with('error', 'This schedule cannot be deleted.');
            }

            $schedule->delete();

            return redirect()->route('schedules.index')
                ->with('success', 'Schedule deleted successfully.');
        } catch (\Exception $e) {
            \Log::error('Error deleting schedule: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to delete schedule.');
        }
    }

    /**
     * Submit schedule for approval
     */
    public function submit(Schedule $schedule)
    {
        try {
            // Check if user can submit this schedule
            if ($schedule->created_by !== Auth::id() && !Auth::user()->hasRole('admin')) {
                abort(403, 'Unauthorized access to schedule.');
            }

            if (!$schedule->canBeSubmitted()) {
                return redirect()->back()->with('error', 'This schedule cannot be submitted.');
            }

            $schedule->submit();

            return redirect()->route('schedules.show', $schedule)
                ->with('success', 'Schedule submitted for approval.');
        } catch (\Exception $e) {
            \Log::error('Error submitting schedule: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to submit schedule.');
        }
    }

    /**
     * Cancel schedule
     */
    public function cancel(Request $request, Schedule $schedule)
    {
        try {
            // Check if user can cancel this schedule
            if ($schedule->created_by !== Auth::id() && !Auth::user()->hasRole('admin')) {
                abort(403, 'Unauthorized access to schedule.');
            }

            if (!$schedule->canBeCancelled()) {
                return redirect()->back()->with('error', 'This schedule cannot be cancelled.');
            }

            $schedule->cancel($request->reason);

            return redirect()->route('schedules.show', $schedule)
                ->with('success', 'Schedule cancelled successfully.');
        } catch (\Exception $e) {
            \Log::error('Error cancelling schedule: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to cancel schedule.');
        }
    }

    /**
     * Get calendar data for AJAX requests
     */
    public function calendar(Request $request)
    {
        try {
            $startDate = $request->get('start', now()->startOfMonth());
            $endDate = $request->get('end', now()->endOfMonth());

            $schedules = Schedule::active()
                ->public()
                ->byDateRange($startDate, $endDate)
                ->get()
                ->map(function ($schedule) {
                    return [
                        'id' => $schedule->id,
                        'title' => $schedule->title,
                        'start' => $schedule->start_date->format('Y-m-d') . ($schedule->start_time ? 'T' . $schedule->start_time->format('H:i:s') : ''),
                        'end' => $schedule->end_date ? $schedule->end_date->format('Y-m-d') . ($schedule->end_time ? 'T' . $schedule->end_time->format('H:i:s') : '') : null,
                        'color' => $this->getTypeColor($schedule->type),
                        'url' => route('schedules.show', $schedule),
                        'extendedProps' => [
                            'type' => $schedule->type,
                            'category' => $schedule->category,
                            'venue' => $schedule->venue,
                            'status' => $schedule->status,
                        ]
                    ];
                });

            return response()->json($schedules);
        } catch (\Exception $e) {
            \Log::error('Error fetching calendar data: ' . $e->getMessage());
            return response()->json([], 500);
        }
    }

    private function getTypeColor($type)
    {
        return match($type) {
            'exam' => '#dc2626', // red
            'event' => '#2563eb', // blue
            'class' => '#16a34a', // green
            'meeting' => '#9333ea', // purple
            'holiday' => '#ea580c', // orange
            'other' => '#6b7280', // gray
            default => '#6b7280'
        };
    }
}
