<?php

namespace App\Http\Controllers;

use App\Models\ClassRoom;
use App\Models\User;
use App\Models\Teacher;
use Illuminate\Http\Request;

class ClassController extends Controller
{
    public function index(Request $request)
    {
        $query = ClassRoom::with(['classTeacher']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('session', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $classes = $query->paginate(15);
        return view('classes.index', compact('classes'));
    }

    public function create()
    {
        $teachers = Teacher::with('user')->get();
        return view('classes.create', compact('teachers'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'session' => 'required|in:A,B,C,D,E,F',
            'description' => 'nullable|string',
            'capacity' => 'required|integer|min:1',
            'class_teacher_id' => 'nullable|exists:teachers,id',
            'room_number' => 'nullable|string|max:50',
            'building' => 'nullable|string|max:100',
            'floor' => 'nullable|string|max:20',
            'wing' => 'nullable|string|max:50',
            'teachers' => 'nullable|array',
            'teachers.*' => 'exists:teachers,id',
        ]);

        try {
            // Generate a unique code if not provided (required by schema)
            $base = preg_replace('/[^A-Za-z0-9]/', '', strtoupper(substr($request->name, 0, 6)) ?: 'CLS');
            $code = $base;
            $suffix = 1;
            while (\App\Models\ClassRoom::where('code', $code)->exists()) {
                $code = $base . $suffix;
                $suffix++;
            }

            $class = ClassRoom::create([
                'name' => $request->name,
                'code' => $code,
                'session' => $request->session,
                'description' => $request->description,
                'capacity' => $request->capacity,
                // class_teacher handled via pivot below
                'room_number' => $request->room_number,
                'building' => $request->building,
                'floor' => $request->floor,
                'wing' => $request->wing,
                'status' => 'active',
                'display_order' => 0,
            ]);

            // Sync teachers via pivot
            if ($request->has('teachers')) {
                $data = [];
                foreach ($request->teachers as $tid) {
                    $data[$tid] = [
                        'is_class_teacher' => $request->class_teacher_id == $tid,
                        'assigned_at' => now(),
                    ];
                }
                try { $class->teachers()->sync($data); } catch (\Throwable $e) { /* ignore */ }
            }

            return redirect()->route('classes.index')
                ->with('success', 'Class created successfully');
        } catch (\Exception $e) {
            return back()->withInput()
                ->with('error', 'Failed to create class: ' . $e->getMessage());
        }
    }

    public function show(ClassRoom $class)
    {
        $class->load(['classTeacher', 'students.user', 'subjects']);
        return view('classes.show', compact('class'));
    }

    public function edit(ClassRoom $class)
    {
        $teachers = Teacher::with('user')->get();
        $assignedTeachers = method_exists($class, 'teachers') ? $class->teachers()->pluck('teachers.id')->toArray() : [];
        return view('classes.edit', compact('class', 'teachers', 'assignedTeachers'));
    }

    public function update(Request $request, ClassRoom $class)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'session' => 'required|in:A,B,C,D,E,F',
            'description' => 'nullable|string',
            'capacity' => 'required|integer|min:1',
            'class_teacher_id' => 'nullable|exists:teachers,id',
            'room_number' => 'nullable|string|max:50',
            'building' => 'nullable|string|max:100',
            'floor' => 'nullable|string|max:20',
            'wing' => 'nullable|string|max:50',
            'teachers' => 'nullable|array',
            'teachers.*' => 'exists:teachers,id',
        ]);

        try {
            $class->update([
                'name' => $request->name,
                'session' => $request->session,
                'description' => $request->description,
                'capacity' => $request->capacity,
                // class_teacher handled via pivot below
                'room_number' => $request->room_number,
                'building' => $request->building,
                'floor' => $request->floor,
                'wing' => $request->wing,
            ]);

            // Sync teachers via pivot
            if ($request->has('teachers')) {
                $data = [];
                foreach ($request->teachers as $tid) {
                    $data[$tid] = [
                        'is_class_teacher' => $request->class_teacher_id == $tid,
                        'assigned_at' => now(),
                    ];
                }
                try { $class->teachers()->sync($data); } catch (\Throwable $e) { /* ignore */ }
            } else {
                try { $class->teachers()->detach(); } catch (\Throwable $e) {}
            }

            return redirect()->route('classes.index')
                ->with('success', 'Class updated successfully');
        } catch (\Exception $e) {
            return back()->withInput()
                ->with('error', 'Failed to update class: ' . $e->getMessage());
        }
    }

    public function destroy(ClassRoom $class)
    {
        try {
            // Detach related records as needed before delete
            try { $class->teachers()->detach(); } catch (\Throwable $e) {}
            $class->delete();
            return redirect()->route('classes.index')
                ->with('success', 'Class deleted successfully');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to delete class: ' . $e->getMessage());
        }
    }

    public function students(ClassRoom $class)
    {
        $students = $class->students()->with(['user'])->paginate(15);
        return view('classes.students', compact('class', 'students'));
    }

    public function subjects(ClassRoom $class)
    {
        $subjects = $class->subjects()->paginate(15);
        return view('classes.subjects', compact('class', 'subjects'));
    }

    public function attendance(ClassRoom $class)
    {
        $date = request('date', now()->format('Y-m-d'));
        $attendance = $class->attendance()->whereDate('date', $date)->get();
        return view('classes.attendance', compact('class', 'attendance', 'date'));
    }

    public function schedule(ClassRoom $class)
    {
        $schedule = $class->schedule()->orderBy('day_of_week')->orderBy('start_time')->get();
        return view('classes.schedule', compact('class', 'schedule'));
    }

    public function addStudent(Request $request, ClassRoom $class)
    {
        $request->validate([
            'student_id' => 'required|exists:students,id',
        ]);

        try {
            $class->students()->attach($request->student_id);
            return back()->with('success', 'Student added to class successfully');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to add student to class: ' . $e->getMessage());
        }
    }

    public function removeStudent(ClassRoom $class, $studentId)
    {
        try {
            $class->students()->detach($studentId);
            return back()->with('success', 'Student removed from class successfully');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to remove student from class: ' . $e->getMessage());
        }
    }
}
