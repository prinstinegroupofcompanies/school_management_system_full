<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Models\User;
use App\Models\ClassRoom;
use Illuminate\Http\Request;

class StudentController extends Controller
{
    public function index(Request $request)
    {
        try {
            $query = Student::with(['user', 'classRoom']);

            // Search functionality
            if ($request->filled('search')) {
                $search = $request->search;
                $query->whereHas('user', function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%");
                })->orWhere('admission_number', 'like', "%{$search}%");
            }

            // Filter by class
            if ($request->filled('class_id')) {
                $query->where('class_id', $request->class_id);
            }

            // Filter by status
            if ($request->filled('status')) {
                $query->whereHas('user', function ($q) use ($request) {
                    $q->where('status', $request->status);
                });
            }

            $students = $query->paginate(15);
            $classes = ClassRoom::all();

            return view('admin.students.index', compact('students', 'classes'));
        } catch (\Exception $e) {
            $classes = collect();
            $students = collect();
            return view('admin.students.index', compact('students', 'classes'));
        }
    }

    public function create()
    {
        try {
            $classes = ClassRoom::all();
            return view('admin.students.create', compact('classes'));
        } catch (\Exception $e) {
            return view('admin.students.create', ['classes' => collect()]);
        }
    }

    public function store(Request $request)
    {
        try {
            $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|email|unique:users,email',
                'password' => 'required|min:8',
                'class_id' => 'required|exists:class_rooms,id',
            ]);

            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => bcrypt($request->password),
                'user_type' => 'student',
                'is_active' => true,
            ]);

            Student::create([
                'user_id' => $user->id,
                'class_id' => $request->class_id,
                'student_id' => 'STU' . str_pad(Student::count() + 1, 4, '0', STR_PAD_LEFT),
                'date_of_birth' => $request->date_of_birth ?? '2000-01-01',
                'address' => $request->address ?? '',
                'phone' => $request->phone ?? '',
            ]);

            return redirect()->route('students.index')->with('success', 'Student created successfully.');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Failed to create student: ' . $e->getMessage()]);
        }
    }

    public function show(Student $student)
    {
        try {
            $student->load(['user', 'classRoom']);
            return view('admin.students.show', compact('student'));
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Student not found.']);
        }
    }

    public function edit(Student $student)
    {
        try {
            $classes = ClassRoom::all();
            $student->load(['user', 'classRoom']);
            return view('admin.students.edit', compact('student', 'classes'));
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Student not found.']);
        }
    }

    public function update(Request $request, Student $student)
    {
        try {
            $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|email|unique:users,email,' . $student->user_id,
                'class_id' => 'required|exists:class_rooms,id',
            ]);

            $student->user->update([
                'name' => $request->name,
                'email' => $request->email,
            ]);

            $student->update([
                'class_id' => $request->class_id,
                'date_of_birth' => $request->date_of_birth ?? $student->date_of_birth,
                'address' => $request->address ?? $student->address,
                'phone' => $request->phone ?? $student->phone,
            ]);

            return redirect()->route('students.index')->with('success', 'Student updated successfully.');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Failed to update student: ' . $e->getMessage()]);
        }
    }

    public function destroy(Student $student)
    {
        try {
            $student->user->delete();
            $student->delete();
            return redirect()->route('students.index')->with('success', 'Student deleted successfully.');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Failed to delete student: ' . $e->getMessage()]);
        }
    }
}