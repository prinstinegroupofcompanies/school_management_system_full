<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Models\User;
use App\Models\ClassRoom;
use App\Models\StudentActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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
            \Log::error('StudentController index error: ' . $e->getMessage());
            $classes = collect();
            $students = collect()->paginate(15);
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
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:8',
            'class_id' => 'required|exists:class_rooms,id',
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'middle_name' => 'nullable|string|max:255',
            'date_of_birth' => 'required|date',
            'gender' => 'required|in:male,female,other',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:500',
            'nationality' => 'nullable|string|max:100',
            'religion' => 'nullable|string|max:100',
            'blood_group' => 'nullable|string|max:10',
            'guardian_name' => 'required|string|max:255',
            'guardian_email' => 'required|email',
            'guardian_phone' => 'required|string|max:20',
            'guardian_relationship' => 'required|string|max:100',
        ]);

        DB::beginTransaction();
        try {
            // Create user account
            $user = User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'password' => bcrypt($validated['password']),
                'user_type' => 'student',
                'phone' => $validated['phone'],
                'address' => $validated['address'],
                'status' => 'active',
            ]);

            // Create guardian user account
            $guardianUser = User::create([
                'name' => $validated['guardian_name'],
                'email' => $validated['guardian_email'],
                'password' => bcrypt('password123'), // Default password
                'user_type' => 'parent',
                'phone' => $validated['guardian_phone'],
                'status' => 'active',
            ]);

            // Create guardian record
            $guardian = \App\Models\Guardian::create([
                'user_id' => $guardianUser->id,
                'guardian_id' => 'G' . str_pad((\App\Models\Guardian::count() + 1), 4, '0', STR_PAD_LEFT),
                'relationship' => strtolower($validated['guardian_relationship']),
                'status' => 'active',
            ]);

            // Generate admission number
            $admissionNo = 'ADM' . date('Y') . str_pad((Student::count() + 1), 4, '0', STR_PAD_LEFT);

            // Create student record
            $student = Student::create([
                'user_id' => $user->id,
                'class_id' => $validated['class_id'],
                'admission_no' => $admissionNo,
                'first_name' => $validated['first_name'],
                'last_name' => $validated['last_name'],
                'middle_name' => $validated['middle_name'],
                'academic_year' => date('Y'),
                'admission_date' => now(),
                'date_of_birth' => $validated['date_of_birth'],
                'gender' => $validated['gender'],
                'phone' => $validated['phone'],
                'address' => $validated['address'],
                'nationality' => $validated['nationality'] ?? 'Liberian',
                'religion' => $validated['religion'],
                'blood_group' => $validated['blood_group'],
                'guardian_id' => $guardian->id,
                'status' => 'active',
                'is_active' => true,
            ]);

            DB::commit();

            return redirect()->route('admin.students.show', $student)
                           ->with('success', 'Student created successfully! Admission Number: ' . $student->admission_no);

        } catch (\Exception $e) {
            DB::rollback();
            \Log::error('Student creation error: ' . $e->getMessage());
            return back()->withErrors(['error' => 'Failed to create student: ' . $e->getMessage()])
                        ->withInput();
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
                'first_name' => 'required|string|max:255',
                'last_name' => 'required|string|max:255',
                'middle_name' => 'nullable|string|max:255',
                'date_of_birth' => 'required|date',
                'gender' => 'required|in:male,female,other',
                'phone' => 'nullable|string|max:20',
                'address' => 'nullable|string|max:500',
                'nationality' => 'nullable|string|max:100',
                'religion' => 'nullable|string|max:100',
                'blood_group' => 'nullable|string|max:10',
            ]);

            DB::beginTransaction();

            // Update user account
            $student->user->update([
                'name' => $request->name,
                'email' => $request->email,
                'phone' => $request->phone,
                'address' => $request->address,
            ]);

            // Update student record
            $student->update([
                'class_id' => $request->class_id,
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'middle_name' => $request->middle_name,
                'date_of_birth' => $request->date_of_birth,
                'gender' => $request->gender,
                'phone' => $request->phone,
                'address' => $request->address,
                'nationality' => $request->nationality ?? 'Liberian',
                'religion' => $request->religion,
                'blood_group' => $request->blood_group,
            ]);

            DB::commit();

            return redirect()->route('admin.students.show', $student)->with('success', 'Student updated successfully.');
        } catch (\Exception $e) {
            DB::rollback();
            \Log::error('Student update error: ' . $e->getMessage());
            return back()->withErrors(['error' => 'Failed to update student: ' . $e->getMessage()])
                        ->withInput();
        }
    }

    public function destroy(Student $student)
    {
        try {
            DB::beginTransaction();
            
            // Delete associated guardian if exists
            if ($student->guardian) {
                $student->guardian->user->delete();
                $student->guardian->delete();
            }
            
            // Delete student user account
            $student->user->delete();
            
            // Delete student record
            $student->delete();
            
            DB::commit();
            
            return redirect()->route('admin.students.index')->with('success', 'Student deleted successfully.');
        } catch (\Exception $e) {
            DB::rollback();
            \Log::error('Student deletion error: ' . $e->getMessage());
            return back()->withErrors(['error' => 'Failed to delete student: ' . $e->getMessage()]);
        }
    }
}