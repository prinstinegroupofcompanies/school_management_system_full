<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Models\ClassRoom;
use App\Models\Subject;
use Illuminate\Http\Request;

class StudentController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $teacher = $user->teacher;
        
        if (!$teacher) {
            abort(403, 'Teacher profile not found.');
        }
        
        // Get students from assigned classes
        $assignedClasses = $teacher->classes()->get();
        $students = collect();
        
        foreach ($assignedClasses as $class) {
            $students = $students->merge($class->students()->with('user')->get());
        }
        
        $students = $students->unique('id');
        
        return view('teacher.students.index', compact('students', 'assignedClasses'));
    }
    
    public function show(Student $student)
    {
        $user = auth()->user();
        $teacher = $user->teacher;
        
        if (!$teacher) {
            abort(403, 'Teacher profile not found.');
        }
        
        // Check if student is in teacher's assigned classes
        $assignedClasses = $teacher->classes()->pluck('id');
        if (!$assignedClasses->contains($student->class_id)) {
            abort(403, 'Access denied. Student not in your assigned classes.');
        }
        
        $student->load(['user', 'class', 'guardian']);
        
        return view('teacher.students.show', compact('student'));
    }

    public function searchEligible(Request $request)
    {
        $request->validate([
            'class_id' => 'required|exists:class_rooms,id',
            'subject_id' => 'required|exists:subjects,id',
            'q' => 'nullable|string|max:255',
        ]);
        $teacher = $request->user()->teacher;
        $subject = Subject::where('id', $request->subject_id)
            ->where('class_id', $request->class_id)
            ->where('teacher_id', $teacher->id)
            ->firstOrFail();

        $query = Student::where('class_id', $request->class_id)->with(['user','class']);
        if ($request->filled('q')) {
            $q = $request->q;
            $query->whereHas('user', function($u) use ($q) { $u->where('name','like',"%{$q}%"); });
        }
        $students = $query->limit(50)->get()->map(function($s){
            return [ 'id' => $s->id, 'name' => $s->user->name, 'class' => $s->class->name ?? '' ];
        });
        return response()->json([ 'data' => $students ]);
    }
}
