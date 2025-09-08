<?php

namespace App\Http\Controllers;

use App\Models\Subject;
use App\Models\Department;
use Illuminate\Http\Request;

class SubjectController extends Controller
{
    public function index(Request $request)
    {
        $query = Subject::with(['class', 'teacher']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%");
            });
        }

        if ($request->filled('class_id')) {
            $query->where('class_id', $request->class_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $subjects = $query->paginate(15);
        $classes = \App\Models\ClassRoom::all();

        return view('subjects.index', compact('subjects', 'classes'));
    }

    public function create()
    {
        $classes = \App\Models\ClassRoom::all();
        $teachers = \App\Models\Teacher::all();
        return view('subjects.create', compact('classes', 'teachers'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|unique:subjects,code',
            'description' => 'nullable|string',
            'class_id' => 'nullable|exists:class_rooms,id',
            'teacher_id' => 'nullable|exists:teachers,id',
            'credits' => 'required|integer|min:1',
            'hours_per_week' => 'required|integer|min:1',
            'is_compulsory' => 'boolean',
            'is_elective' => 'boolean',
        ]);

        try {
            $subject = Subject::create([
                'name' => $request->name,
                'code' => $request->code,
                'description' => $request->description,
                'class_id' => $request->class_id,
                'teacher_id' => $request->teacher_id,
                'credits' => $request->credits,
                'hours_per_week' => $request->hours_per_week,
                'is_compulsory' => $request->boolean('is_compulsory'),
                'is_elective' => $request->boolean('is_elective'),
                'status' => 'active',
            ]);

            return redirect()->route('subjects.index')
                ->with('success', 'Subject created successfully');
        } catch (\Exception $e) {
            return back()->withInput()
                ->with('error', 'Failed to create subject: ' . $e->getMessage());
        }
    }

    public function show(Subject $subject)
    {
        $subject->load(['class', 'teacher.user']);
        return view('subjects.show', compact('subject'));
    }

    public function edit(Subject $subject)
    {
        $classes = \App\Models\ClassRoom::all();
        $teachers = \App\Models\Teacher::all();
        return view('subjects.edit', compact('subject', 'classes', 'teachers'));
    }

    public function update(Request $request, Subject $subject)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|unique:subjects,code,' . $subject->id,
            'description' => 'nullable|string',
            'class_id' => 'nullable|exists:class_rooms,id',
            'teacher_id' => 'nullable|exists:teachers,id',
            'credits' => 'required|integer|min:1',
            'hours_per_week' => 'required|integer|min:1',
            'is_compulsory' => 'boolean',
            'is_elective' => 'boolean',
        ]);

        try {
            $subject->update([
                'name' => $request->name,
                'code' => $request->code,
                'description' => $request->description,
                'class_id' => $request->class_id,
                'teacher_id' => $request->teacher_id,
                'credits' => $request->credits,
                'hours_per_week' => $request->hours_per_week,
                'is_compulsory' => $request->boolean('is_compulsory'),
                'is_elective' => $request->boolean('is_elective'),
            ]);

            return redirect()->route('subjects.index')
                ->with('success', 'Subject updated successfully');
        } catch (\Exception $e) {
            return back()->withInput()
                ->with('error', 'Failed to update subject: ' . $e->getMessage());
        }
    }

    public function destroy(Subject $subject)
    {
        try {
            $subject->delete();
            return redirect()->route('subjects.index')
                ->with('success', 'Subject deleted successfully');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to delete subject: ' . $e->getMessage());
        }
    }

    public function students(Subject $subject)
    {
        $students = $subject->students()->with(['user'])->paginate(15);
        return view('subjects.students', compact('subject', 'students'));
    }

    public function teachers(Subject $subject)
    {
        $teachers = $subject->teachers()->with(['user', 'department'])->paginate(15);
        return view('subjects.teachers', compact('subject', 'teachers'));
    }

    public function materials(Subject $subject)
    {
        $materials = $subject->materials()->latest()->paginate(15);
        return view('subjects.materials', compact('subject', 'materials'));
    }
}
