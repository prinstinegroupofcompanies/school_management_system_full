<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\LiveClass;
use App\Models\ClassRoom;
use App\Models\Subject;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class LiveClassController extends Controller
{
    /**
     * Display a listing of live classes.
     */
    public function index(Request $request)
    {
        $teacher = Auth::user()->teacher;
        
        if (!$teacher) {
            return redirect()->route('teacher.dashboard')->with('error', 'Teacher profile not found.');
        }

        $query = LiveClass::where('teacher_id', $teacher->id)
            ->with(['classRoom', 'subject'])
            ->orderBy('scheduled_at', 'desc');

        // Filter by status
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        // Filter upcoming
        if ($request->has('upcoming') && $request->upcoming) {
            $query->upcoming();
        }

        $liveClasses = $query->paginate(15);

        return view('teacher.live-classes.index', compact('liveClasses'));
    }

    /**
     * Show the form for creating a new live class.
     */
    public function create()
    {
        $teacher = Auth::user()->teacher;
        
        if (!$teacher) {
            return redirect()->route('teacher.dashboard')->with('error', 'Teacher profile not found.');
        }

        $classes = ClassRoom::all();
        $subjects = Subject::all();

        return view('teacher.live-classes.create', compact('classes', 'subjects'));
    }

    /**
     * Store a newly created live class.
     */
    public function store(Request $request)
    {
        $request->validate([
            'class_id' => 'nullable|exists:class_rooms,id',
            'subject_id' => 'nullable|exists:subjects,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'meeting_url' => 'required|url',
            'meeting_id' => 'nullable|string',
            'meeting_password' => 'nullable|string',
            'platform' => 'required|in:zoom,google_meet,microsoft_teams,custom,other',
            'scheduled_at' => 'required|date|after:now',
            'duration_minutes' => 'required|integer|min:15|max:480',
            'is_recorded' => 'boolean',
            'notes' => 'nullable|string',
        ]);

        $teacher = Auth::user()->teacher;
        
        LiveClass::create([
            'teacher_id' => $teacher->id,
            'class_id' => $request->class_id,
            'subject_id' => $request->subject_id,
            'title' => $request->title,
            'description' => $request->description,
            'meeting_url' => $request->meeting_url,
            'meeting_id' => $request->meeting_id,
            'meeting_password' => $request->meeting_password,
            'platform' => $request->platform,
            'scheduled_at' => $request->scheduled_at,
            'duration_minutes' => $request->duration_minutes,
            'is_recorded' => $request->has('is_recorded'),
            'notes' => $request->notes,
            'status' => 'scheduled',
        ]);

        return redirect()->route('teacher.live-classes.index')
            ->with('success', 'Live class scheduled successfully.');
    }

    /**
     * Display the specified live class.
     */
    public function show(LiveClass $liveClass)
    {
        $liveClass->load(['teacher', 'classRoom', 'subject']);
        
        // Get students for attendance
        $students = [];
        if ($liveClass->class_id) {
            $students = \App\Models\Student::where('class_id', $liveClass->class_id)
                ->with('user')
                ->get();
        }

        return view('teacher.live-classes.show', compact('liveClass', 'students'));
    }

    /**
     * Start a live class.
     */
    public function start(LiveClass $liveClass)
    {
        $liveClass->update(['status' => 'live']);

        // Broadcast event
        event(new \App\Events\LiveClassStarted($liveClass));

        return redirect()->route('teacher.live-classes.show', $liveClass)
            ->with('success', 'Live class started.');
    }

    /**
     * End a live class.
     */
    public function end(LiveClass $liveClass, Request $request)
    {
        $attendanceData = $request->input('attendance', []);
        $recordingUrls = $request->input('recording_urls', []);

        $liveClass->update([
            'status' => 'completed',
            'attendance_data' => $attendanceData,
            'recording_urls' => is_array($recordingUrls) ? $recordingUrls : [$recordingUrls],
        ]);

        return redirect()->route('teacher.live-classes.index')
            ->with('success', 'Live class ended and attendance recorded.');
    }

    /**
     * Cancel a live class.
     */
    public function cancel(LiveClass $liveClass)
    {
        $liveClass->update(['status' => 'cancelled']);

        return redirect()->route('teacher.live-classes.index')
            ->with('success', 'Live class cancelled.');
    }
}
