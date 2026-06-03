<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ExamSchedule;
use App\Models\ExamType;
use App\Models\Subject;
use App\Models\ClassRoom;
use Illuminate\Http\Request;

class ExamSchedulesController extends Controller
{
    public function __construct()
    {
        $this->middleware('admin');
    }

    public function index(Request $request)
    {
        $query = ExamSchedule::with(['examType', 'subject', 'class']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where('title', 'like', "%{$search}%")
                ->orWhereHas('examType', function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%");
                });
        }
        if ($request->filled('subject')) {
            $query->where('subject_id', $request->subject);
        }
        if ($request->filled('class')) {
            $query->where('class_id', $request->class);
        }
        if ($request->filled('date')) {
            $query->whereDate('start_date', $request->date);
        }

        $examSchedules = $query->orderByDesc('start_date')->paginate(15)->withQueryString();
        $subjects = Subject::orderBy('name')->get();
        $classes = ClassRoom::orderBy('name')->get();

        return view('admin.exams.schedules.index', compact('examSchedules', 'subjects', 'classes'));
    }

    public function create()
    {
        $examTypes = ExamType::where('status', 'active')->orderBy('name')->get();
        $subjects = Subject::orderBy('name')->get();
        $classes = ClassRoom::orderBy('name')->get();
        return view('admin.exams.schedules.create', compact('examTypes', 'subjects', 'classes'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'exam_type_id' => 'required|exists:exam_types,id',
            'class_id' => 'required|exists:class_rooms,id',
            'subject_id' => 'nullable|exists:subjects,id',
            'academic_year' => 'required|string',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'start_time' => 'nullable',
            'end_time' => 'nullable',
            'venue' => 'nullable|string|max:255',
            'status' => 'nullable|in:draft,published,ongoing,completed,cancelled',
        ]);

        ExamSchedule::create($request->only([
            'title', 'description', 'exam_type_id', 'class_id', 'subject_id', 'academic_year',
            'start_date', 'end_date', 'start_time', 'end_time', 'venue', 'instructions', 'important_notes', 'status', 'is_active'
        ]));

        return redirect()->route('admin.exams.schedules.index')->with('success', 'Exam schedule created successfully.');
    }

    public function show(ExamSchedule $schedule)
    {
        $schedule->load(['examType', 'subject', 'class', 'examMarks.student.user']);
        return view('admin.exams.schedules.show', compact('schedule'));
    }

    public function edit(ExamSchedule $schedule)
    {
        $examTypes = ExamType::where('status', 'active')->orderBy('name')->get();
        $subjects = Subject::orderBy('name')->get();
        $classes = ClassRoom::orderBy('name')->get();
        return view('admin.exams.schedules.edit', compact('schedule', 'examTypes', 'subjects', 'classes'));
    }

    public function update(Request $request, ExamSchedule $schedule)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'exam_type_id' => 'required|exists:exam_types,id',
            'class_id' => 'required|exists:class_rooms,id',
            'subject_id' => 'nullable|exists:subjects,id',
            'academic_year' => 'required|string',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'start_time' => 'nullable',
            'end_time' => 'nullable',
            'venue' => 'nullable|string|max:255',
            'status' => 'nullable|in:draft,published,ongoing,completed,cancelled',
        ]);

        $schedule->update($request->only([
            'title', 'description', 'exam_type_id', 'class_id', 'subject_id', 'academic_year',
            'start_date', 'end_date', 'start_time', 'end_time', 'venue', 'instructions', 'important_notes', 'status', 'is_active'
        ]));

        return redirect()->route('admin.exams.schedules.index')->with('success', 'Exam schedule updated successfully.');
    }

    public function destroy(ExamSchedule $schedule)
    {
        $schedule->delete();
        return redirect()->route('admin.exams.schedules.index')->with('success', 'Exam schedule deleted.');
    }
}
