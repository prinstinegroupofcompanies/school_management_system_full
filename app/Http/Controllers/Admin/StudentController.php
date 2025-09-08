<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Models\ClassRoom;
use App\Models\User;
use App\Services\Fees\StudentFeeService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class StudentController extends Controller
{
    public function __construct()
    {
        $this->middleware('admin');
    }

    public function index(Request $request)
    {
        $query = Student::with(['user', 'class']);

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('user', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            })->orWhere('student_id', 'like', "%{$search}%");
        }

        // Filter by class
        if ($request->filled('class')) {
            $query->where('class_id', $request->class);
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $students = $query->paginate(15);
        $classes = ClassRoom::all();

        return view('admin.students.index', compact('students', 'classes'));
    }

    public function create()
    {
        $classes = ClassRoom::all();
        $currentYear = (int) date('Y');
        $semesters = ['Semester 1', 'Semester 2'];
        return view('admin.students.create', compact('classes', 'currentYear', 'semesters'));
    }

    public function store(Request $request, StudentFeeService $studentFeeService)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6|confirmed',
            'class_id' => 'required|exists:class_rooms,id',
            'semester' => 'nullable|string|max:32',
            'year' => 'required|integer|min:2000|max:2100',
            'student_id' => 'nullable|string|max:50',
            'phone' => 'nullable|string|max:50',
            'address' => 'nullable|string|max:255',
        ]);

        // Create user for the student
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        // Create student record
        $student = Student::create([
            'user_id' => $user->id,
            'student_id' => $request->student_id,
            'class_id' => $request->class_id,
            'phone' => $request->phone,
            'address' => $request->address,
            'status' => 'active',
            'academic_year' => (string) $request->year,
        ]);

        // Assign class subjects to the student
        $class = ClassRoom::with('subjects')->find($request->class_id);
        if ($class && $class->subjects->count() > 0) {
            $student->subjects()->sync($class->subjects->pluck('id')->all());
        }

        // Compute and persist student fees
        $studentFeeService->createStudentFeeFor($student, $request->semester, (int) $request->year);

        return redirect()->route('admin.students.index')
            ->with('success', 'Student created and fees initialized successfully.');
    }

    public function show(Student $student)
    {
        $student->load(['user', 'class', 'subjects']);
        return view('admin.students.show', compact('student'));
    }

    public function edit(Student $student)
    {
        $classes = ClassRoom::all();
        return view('admin.students.edit', compact('student', 'classes'));
    }

    public function update(Request $request, Student $student)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $student->user_id,
            'class_id' => 'required|exists:class_rooms,id',
            'status' => 'required|in:active,inactive,suspended',
        ]);

        // Update user information
        $student->user->update([
            'name' => $request->name,
            'email' => $request->email,
        ]);

        // Detect class change
        $originalClassId = $student->class_id;

        // Update student information
        $student->update([
            'class_id' => $request->class_id,
            'status' => $request->status,
        ]);

        // If class changed, reassign subjects to match the new class
        if ((int) $originalClassId !== (int) $request->class_id) {
            $newClass = ClassRoom::with('subjects')->find($request->class_id);
            if ($newClass) {
                $student->subjects()->sync($newClass->subjects->pluck('id')->all());
            }
        }

        return redirect()->route('admin.students.index')
            ->with('success', 'Student updated successfully');
    }

    public function destroy(Student $student)
    {
        // Delete associated user
        $student->user->delete();
        
        // Delete student record
        $student->delete();

        return redirect()->route('admin.students.index')
            ->with('success', 'Student deleted successfully');
    }
}
