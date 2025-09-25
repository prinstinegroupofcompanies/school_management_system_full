<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Teacher;
use App\Models\User;
use App\Models\Subject;
use App\Models\Department;
use App\Models\Designation;
use App\Models\ClassRoom;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class TeacherController extends Controller
{
    public function __construct()
    {
        $this->middleware('admin');
    }

    public function index(Request $request)
    {
        $query = Teacher::with(['user', 'subjects', 'classes']);

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('user', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            })->orWhere('teacher_id', 'like', "%{$search}%");
        }

        // Filter by subject
        if ($request->filled('subject')) {
            $query->whereHas('subjects', function ($q) use ($request) {
                $q->where('id', $request->subject);
            });
        }

        // Filter by class
        if ($request->filled('class')) {
            $query->whereHas('classes', function ($q) use ($request) {
                $q->where('id', $request->class);
            });
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $teachers = $query->paginate(15);
        $subjects = Subject::all();
        $classes = ClassRoom::all();

        return view('admin.teachers.index', compact('teachers', 'subjects', 'classes'));
    }

    public function create()
    {
        $departments = Department::all();
        $subjects = Subject::all();
        $classes = ClassRoom::all();
        
        return view('admin.teachers.create', compact('departments','subjects','classes'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'employee_id' => 'required|string|unique:teachers,employee_id',
            'department_id' => 'nullable|exists:departments,id',
            'joining_date' => 'required|date',
            'basic_salary' => 'required|numeric|min:0',
            'qualification' => 'nullable|string|max:500',
            'subjects' => 'nullable|array',
            'subjects.*' => 'exists:subjects,id',
            'classes' => 'nullable|array',
            'classes.*' => 'exists:class_rooms,id',
            'class_teacher_classes' => 'nullable|array',
            'class_teacher_classes.*' => 'exists:class_rooms,id',
        ]);

        DB::beginTransaction();

        try {
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'user_type' => 'teacher',
                'phone' => $request->phone,
                'address' => $request->address,
                'status' => 'active',
            ]);

            // Ensure a valid department_id (fallback to a default department if none selected)
            $departmentId = $request->department_id;
            if (empty($departmentId)) {
                $defaultDept = Department::first();
                if (!$defaultDept) {
                    // Create a minimal valid department (code is required and unique)
                    $baseCode = 'GEN';
                    $code = $baseCode;
                    $suffix = 1;
                    while (\App\Models\Department::where('code', $code)->exists()) {
                        $code = $baseCode . $suffix;
                        $suffix++;
                    }
                    $defaultDept = Department::create([
                        'name' => 'General',
                        'code' => $code,
                        'status' => 'active',
                    ]);
                }
                $departmentId = $defaultDept->id;
            }

            // Ensure a valid designation_id (fallback to a default designation if required by schema)
            $designationId = $request->designation_id;
            if (empty($designationId)) {
                $defaultDes = Designation::first();
                if (!$defaultDes) {
                    // Create a minimal valid designation (code is required and unique)
                    $baseCode = 'TCH';
                    $code = $baseCode;
                    $suffix = 1;
                    while (\App\Models\Designation::where('code', $code)->exists()) {
                        $code = $baseCode . $suffix;
                        $suffix++;
                    }
                    $defaultDes = Designation::create([
                        'name' => 'Teacher',
                        'code' => $code,
                        'status' => 'active',
                    ]);
                }
                $designationId = $defaultDes->id;
            }

            $teacher = Teacher::create([
                'user_id' => $user->id,
                'teacher_id' => 'TCH' . str_pad(Teacher::count() + 1, 4, '0', STR_PAD_LEFT),
                'employee_id' => $request->employee_id,
                'department_id' => $departmentId,
                'designation_id' => $designationId,
                'joining_date' => $request->joining_date,
                'basic_salary' => $request->basic_salary,
                'salary' => $request->basic_salary,
                'qualification' => $request->qualification,
                'status' => 'active',
                'employment_status' => 'active',
            ]);

            // Assign subjects to this teacher (Subject has teacher_id)
            if ($request->filled('subjects')) {
                // Remove teacher from previously assigned subjects (if any overlapping)
                Subject::whereIn('id', $request->subjects)->update(['teacher_id' => $teacher->id]);
            }

            // Assign classes to this teacher via pivot
            $allClassData = [];
            
            // Regular class assignments
            if ($request->filled('classes')) {
                foreach ($request->classes as $classId) {
                    $allClassData[$classId] = [
                        'is_class_teacher' => false,
                        'assigned_at' => now(),
                    ];
                }
            }
            
            // Class teacher assignments
            if ($request->filled('class_teacher_classes')) {
                foreach ($request->class_teacher_classes as $classId) {
                    $allClassData[$classId] = [
                        'is_class_teacher' => true,
                        'assigned_at' => now(),
                    ];
                }
            }
            
            if (!empty($allClassData)) {
                $teacher->classes()->sync($allClassData);
            }

            DB::commit();

            return redirect()->route('admin.teachers.index')
                ->with('success', 'Teacher created successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()
                ->with('error', 'Failed to create teacher: ' . $e->getMessage());
        }
    }

    public function show(Teacher $teacher)
    {
        $teacher->load(['user', 'subjects', 'classes']);
        return view('admin.teachers.show', compact('teacher'));
    }

    public function edit(Teacher $teacher)
    {
        $subjects = Subject::all();
        $classes = ClassRoom::all();
        $assignedClasses = $teacher->classes()->pluck('class_rooms.id')->toArray();
        return view('admin.teachers.edit', compact('teacher', 'subjects', 'classes', 'assignedClasses'));
    }

    public function update(Request $request, Teacher $teacher)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $teacher->user_id,
            'status' => 'required|in:active,inactive,suspended',
            'subjects' => 'nullable|array',
            'subjects.*' => 'exists:subjects,id',
            'classes' => 'nullable|array',
            'classes.*' => 'exists:class_rooms,id',
            'class_teacher_classes' => 'nullable|array',
            'class_teacher_classes.*' => 'exists:class_rooms,id',
        ]);

        // Update user information
        $teacher->user->update([
            'name' => $request->name,
            'email' => $request->email,
        ]);

        // Update teacher information
        $teacher->update([
            'status' => $request->status,
        ]);

        // Update subjects if provided
        if ($request->has('subjects')) {
            // For HasMany relationship, we need to update the teacher_id on subjects
            // First, remove teacher from all current subjects
            $teacher->subjects()->update(['teacher_id' => null]);
            
            // Then assign the teacher to the selected subjects
            if (!empty($request->subjects)) {
                \App\Models\Subject::whereIn('id', $request->subjects)
                    ->update(['teacher_id' => $teacher->id]);
            }
        }

        // Update classes if provided
        $allClassData = [];
        
        // Regular class assignments
        if ($request->has('classes')) {
            foreach ($request->classes as $classId) {
                $allClassData[$classId] = [
                    'is_class_teacher' => false,
                    'assigned_at' => now(),
                ];
            }
        }
        
        // Class teacher assignments
        if ($request->has('class_teacher_classes')) {
            foreach ($request->class_teacher_classes as $classId) {
                $allClassData[$classId] = [
                    'is_class_teacher' => true,
                    'assigned_at' => now(),
                ];
            }
        }
        
        if (!empty($allClassData)) {
            $teacher->classes()->sync($allClassData);
        } else {
            $teacher->classes()->detach();
        }

        return redirect()->route('admin.teachers.index')
            ->with('success', 'Teacher updated successfully');
    }

    public function destroy(Teacher $teacher)
    {
        try {
            DB::beginTransaction();
            
            // Get the user before deleting the teacher
            $user = $teacher->user;
            
            // Detach all classes from the teacher
            $teacher->classes()->detach();
            
            // Delete all related records that might reference this teacher
            // Delete subjects assigned to this teacher
            \App\Models\Subject::where('teacher_id', $teacher->id)->update(['teacher_id' => null]);
            
            // Delete grades assigned by this teacher
            \App\Models\Grade::where('teacher_id', $teacher->id)->delete();
            
            // Delete homework assignments by this teacher
            \App\Models\Homework::where('teacher_id', $teacher->id)->delete();
            
            // Delete exam schedules for this teacher
            \App\Models\ExamSchedule::where('teacher_id', $teacher->id)->delete();
            
            // Delete teacher attendance records
            \App\Models\TeacherAttendance::where('teacher_id', $teacher->id)->delete();
            
            // Delete study materials by this teacher
            \App\Models\StudyMaterial::where('teacher_id', $teacher->id)->delete();
            
            // Delete notifications for this user (this prevents foreign key constraint violation)
            if ($user) {
                \App\Models\Notification::where('user_id', $user->id)->delete();
            }
            
            // Delete teacher record first
            $teacher->delete();
            
            // Try to delete the user, but handle foreign key constraints gracefully
            if ($user) {
                try {
                    $user->delete();
                } catch (\Illuminate\Database\QueryException $e) {
                    // If user deletion fails due to foreign key constraints,
                    // we'll just log it and continue since the teacher is already deleted
                    \Log::warning('Could not delete user ' . $user->id . ' due to foreign key constraints: ' . $e->getMessage());
                }
            }
            
            DB::commit();
            
            return redirect()->route('admin.teachers.index')
                ->with('success', 'Teacher deleted successfully');
                
        } catch (\Exception $e) {
            DB::rollback();
            
            return redirect()->route('admin.teachers.index')
                ->with('error', 'Failed to delete teacher: ' . $e->getMessage());
        }
    }
}
