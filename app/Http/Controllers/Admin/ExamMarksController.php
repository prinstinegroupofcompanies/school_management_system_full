<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ExamMark;
use App\Models\Subject;
use App\Models\ClassRoom;
use Illuminate\Http\Request;

class ExamMarksController extends Controller
{
    public function __construct()
    {
        $this->middleware('admin');
    }

    public function index(Request $request)
    {
        $query = ExamMark::with(['student.user', 'examSchedule.examType', 'examSchedule.subject', 'examSchedule.class', 'subject', 'teacher.user'])
            ->whereIn('status', ['draft', 'published', 'final', 'marked', 'approved', 'pending']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('student.user', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%");
            })->orWhereHas('student', function ($q) use ($search) {
                $q->where('student_id', 'like', "%{$search}%");
            });
        }
        if ($request->filled('subject')) {
            $query->where('subject_id', $request->subject);
        }
        if ($request->filled('class')) {
            $query->where('class_id', $request->class);
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $examMarks = $query->orderByDesc('updated_at')->paginate(15)->withQueryString();
        $subjects = Subject::orderBy('name')->get();
        $classes = ClassRoom::orderBy('name')->get();

        return view('admin.exams.marks.index', compact('examMarks', 'subjects', 'classes'));
    }

    public function create()
    {
        $subjects = Subject::orderBy('name')->get();
        $classes = ClassRoom::orderBy('name')->get();
        return view('admin.exams.marks.create', compact('subjects', 'classes'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'student_id' => 'required|exists:students,id',
            'exam_schedule_id' => 'required|exists:exam_schedules,id',
            'subject_id' => 'required|exists:subjects,id',
            'class_id' => 'required|exists:class_rooms,id',
            'academic_year' => 'required|string',
            'marks_obtained' => 'required|numeric|min:0',
            'total_marks' => 'required|numeric|min:1',
            'status' => 'nullable|in:draft,published,final,marked,approved,pending',
        ]);

        $data = $request->only([
            'student_id', 'exam_schedule_id', 'subject_id', 'class_id', 'academic_year',
            'marks_obtained', 'total_marks', 'remarks', 'teacher_comments', 'status'
        ]);
        $data['total_marks'] = $data['total_marks'] ?: 100;
        $data['percentage'] = $data['total_marks'] > 0 ? round(($data['marks_obtained'] / $data['total_marks']) * 100, 2) : 0;
        $data['marked_by'] = auth()->id();
        $data['marked_at'] = now();

        ExamMark::create($data);

        return redirect()->route('admin.exams.marks.index')->with('success', 'Exam mark created successfully.');
    }

    public function show(ExamMark $mark)
    {
        $mark->load(['student.user', 'examSchedule.examType', 'examSchedule.subject', 'examSchedule.class', 'subject', 'teacher.user', 'markedBy']);
        return view('admin.exams.marks.show', compact('mark'));
    }

    public function edit(ExamMark $mark)
    {
        $subjects = Subject::orderBy('name')->get();
        $classes = ClassRoom::orderBy('name')->get();
        return view('admin.exams.marks.edit', compact('mark', 'subjects', 'classes'));
    }

    public function update(Request $request, ExamMark $mark)
    {
        $request->validate([
            'marks_obtained' => 'nullable|numeric|min:0',
            'total_marks' => 'nullable|numeric|min:1',
            'remarks' => 'nullable|string',
            'teacher_comments' => 'nullable|string',
            'status' => 'nullable|in:draft,published,final,marked,approved,pending',
        ]);

        $data = $request->only(['marks_obtained', 'total_marks', 'remarks', 'teacher_comments', 'status']);
        if (isset($data['marks_obtained']) && isset($data['total_marks']) && $data['total_marks'] > 0) {
            $data['percentage'] = round(($data['marks_obtained'] / $data['total_marks']) * 100, 2);
        }
        $mark->update($data);

        return redirect()->route('admin.exams.marks.index')->with('success', 'Exam mark updated successfully.');
    }

    public function approve(ExamMark $mark)
    {
        $mark->update([
            'status' => 'approved',
            'verified_by' => auth()->id(),
            'verified_at' => now(),
        ]);
        return redirect()->route('admin.exams.marks.index')->with('success', 'Exam mark approved.');
    }

    public function destroy(ExamMark $mark)
    {
        $mark->delete();
        return redirect()->route('admin.exams.marks.index')->with('success', 'Exam mark deleted.');
    }
}
