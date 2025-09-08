<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ClassRoom;
use App\Models\Teacher;
use Illuminate\Http\Request;

class ClassController extends Controller
{
    public function __construct()
    {
        $this->middleware('admin');
    }

    public function index(Request $request)
    {
        $query = ClassRoom::with(['teachers.user', 'classTeachers.user', 'students']);

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where('name', 'like', "%{$search}%")
                  ->orWhere('session', 'like', "%{$search}%");
        }

        // Filter by teacher
        if ($request->filled('teacher')) {
            $query->whereHas('teachers', function ($q) use ($request) {
                $q->where('teachers.id', $request->teacher);
            });
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $classes = $query->paginate(15);
        $teachers = Teacher::with('user')->get();

        return view('admin.classes.index', compact('classes', 'teachers'));
    }

    public function show(ClassRoom $class)
    {
        $class->load(['teachers.user', 'students.user', 'subjects']);
        return view('admin.classes.show', compact('class'));
    }

    public function create()
    {
        $teachers = Teacher::with('user')->get();
        return view('admin.classes.create', compact('teachers'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'session' => 'required|in:A,B,C,D,E,F',
            'capacity' => 'required|integer|min:1',
            'status' => 'required|in:active,inactive',
            'teachers' => 'nullable|array',
            'teachers.*' => 'exists:teachers,id',
            'class_teacher_id' => 'nullable|exists:teachers,id',
        ]);

        // Create the class
        $class = ClassRoom::create([
            'name' => $request->name,
            'code' => $request->name, // Use name as code for now
            'session' => $request->session,
            'capacity' => $request->capacity,
            'status' => $request->status,
            'description' => $request->description,
            'room_number' => $request->room_number,
            'building' => $request->building,
            'floor' => $request->floor,
        ]);

        // Assign teachers to the class
        $teacherData = [];
        
        // Add regular teachers
        if ($request->has('teachers')) {
            foreach ($request->teachers as $teacherId) {
                $teacherData[$teacherId] = [
                    'is_class_teacher' => $request->class_teacher_id == $teacherId,
                    'assigned_at' => now(),
                ];
            }
        }
        
        // Add class teacher if selected and not already in teachers array
        if ($request->filled('class_teacher_id')) {
            $classTeacherId = $request->class_teacher_id;
            if (!isset($teacherData[$classTeacherId])) {
                $teacherData[$classTeacherId] = [
                    'is_class_teacher' => true,
                    'assigned_at' => now(),
                ];
            } else {
                // Update existing teacher to be class teacher
                $teacherData[$classTeacherId]['is_class_teacher'] = true;
            }
        }
        
        if (!empty($teacherData)) {
            $class->teachers()->sync($teacherData);
        }

        return redirect()->route('admin.classes.index')
            ->with('success', 'Class created successfully');
    }

    public function edit(ClassRoom $class)
    {
        $teachers = Teacher::with('user')->get();
        $assignedTeachers = $class->teachers()->pluck('teachers.id')->toArray();
        $classTeacherId = $class->classTeachers()->first()?->id;
        return view('admin.classes.edit', compact('class', 'teachers', 'assignedTeachers', 'classTeacherId'));
    }

    public function update(Request $request, ClassRoom $class)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'session' => 'required|in:A,B,C,D,E,F',
            'capacity' => 'required|integer|min:1',
            'status' => 'required|in:active,inactive',
            'teachers' => 'nullable|array',
            'teachers.*' => 'exists:teachers,id',
            'class_teacher_id' => 'nullable|exists:teachers,id',
        ]);

        // Update basic class information
        $class->update([
            'name' => $request->name,
            'code' => $request->name, // Use name as code for now
            'session' => $request->session,
            'capacity' => $request->capacity,
            'status' => $request->status,
            'description' => $request->description,
            'room_number' => $request->room_number,
            'building' => $request->building,
            'floor' => $request->floor,
        ]);

        // Sync teachers with the class
        $teacherData = [];
        
        // Add regular teachers
        if ($request->has('teachers')) {
            foreach ($request->teachers as $teacherId) {
                $teacherData[$teacherId] = [
                    'is_class_teacher' => $request->class_teacher_id == $teacherId,
                    'assigned_at' => now(),
                ];
            }
        }
        
        // Add class teacher if selected and not already in teachers array
        if ($request->filled('class_teacher_id')) {
            $classTeacherId = $request->class_teacher_id;
            if (!isset($teacherData[$classTeacherId])) {
                $teacherData[$classTeacherId] = [
                    'is_class_teacher' => true,
                    'assigned_at' => now(),
                ];
            } else {
                // Update existing teacher to be class teacher
                $teacherData[$classTeacherId]['is_class_teacher'] = true;
            }
        }
        
        if (!empty($teacherData)) {
            $class->teachers()->sync($teacherData);
        } else {
            $class->teachers()->detach();
        }

        return redirect()->route('admin.classes.index')
            ->with('success', 'Class updated successfully');
    }

    public function destroy(ClassRoom $class)
    {
        // Check if class has students
        if ($class->students()->count() > 0) {
            return redirect()->route('admin.classes.index')
                ->with('error', 'Cannot delete class with enrolled students');
        }

        // Detach all teachers from the class
        $class->teachers()->detach();
        
        $class->delete();

        return redirect()->route('admin.classes.index')
            ->with('success', 'Class deleted successfully');
    }
}
