<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Subject;
use App\Models\Teacher;
use App\Models\ClassRoom;
use Illuminate\Http\Request;

class SubjectController extends Controller
{
    public function __construct()
    {
        $this->middleware('admin');
    }

    public function index(Request $request)
    {
        $query = Subject::with(['teacher', 'classes'])->whereNotNull('id');

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where('name', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%");
        }

        // Filter by teacher
        if ($request->filled('teacher')) {
            $query->where('teacher_id', $request->teacher);
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by level
        if ($request->filled('level')) {
            $query->where('level', $request->level);
        }

        $subjects = $query->paginate(15);
        $teachers = Teacher::all();

        return view('admin.subjects.index', compact('subjects', 'teachers'));
    }

    public function create()
    {
        $teachers = Teacher::all();
        $classes = ClassRoom::all();
        return view('subjects.create', compact('teachers','classes'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:subjects,code',
            'teacher_id' => 'required|exists:teachers,id',
            'description' => 'nullable|string',
            'level' => 'required|in:junior,senior',
            'status' => 'nullable|in:active,inactive',
            'class_ids' => 'required|array|min:1',
            'class_ids.*' => 'exists:class_rooms,id',
        ]);

        $data = $request->only(['name','code','teacher_id','description','level']);
        $data['status'] = $request->input('status', 'active');
        
        $subject = Subject::create($data);
        
        // Sync the classes
        $subject->classes()->sync($request->class_ids);

        return redirect()->route('admin.subjects.index')->with('success','Subject created successfully');
    }

    public function show(Subject $subject)
    {
        $subject->load(['teacher', 'classes']);
        return view('subjects.show', compact('subject'));
    }

    public function edit(Subject $subject)
    {
        $teachers = Teacher::all();
        $classes = ClassRoom::all();
        return view('subjects.edit', compact('subject', 'teachers', 'classes'));
    }

    public function update(Request $request, Subject $subject)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:subjects,code,' . $subject->id,
            'teacher_id' => 'required|exists:teachers,id',
            'description' => 'nullable|string',
            'level' => 'required|in:junior,senior',
            'status' => 'nullable|in:active,inactive',
            'class_ids' => 'required|array|min:1',
            'class_ids.*' => 'exists:class_rooms,id',
        ]);

        $data = $request->only(['name','code','teacher_id','description','level']);
        $data['status'] = $request->input('status', $subject->status ?? 'active');
        $subject->update($data);

        // Sync the classes
        $subject->classes()->sync($request->class_ids);

        return redirect()->route('admin.subjects.index')
            ->with('success', 'Subject updated successfully');
    }

    public function destroy(Subject $subject)
    {
        // Safe delete: clear simple associations if any, then delete
        try {
            // If your schema enforces FKs elsewhere (e.g., grades/homework),
            // database will block and throw; otherwise this will delete.
            $subject->delete();
        } catch (\Throwable $e) {
            return redirect()->route('admin.subjects.index')
                ->with('error', 'Failed to delete subject: ' . $e->getMessage());
        }

        return redirect()->route('admin.subjects.index')
            ->with('success', 'Subject deleted successfully');
    }
}
