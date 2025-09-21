<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\ClassRoom;
use Illuminate\Http\Request;

class ClassController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $teacher = $user->teacher;
        
        if (!$teacher) {
            abort(403, 'Teacher profile not found.');
        }
        
        // Get classes assigned to this teacher
        $classes = $teacher->classes()->with(['students.user'])->get();
        
        return view('teacher.classes.index', compact('classes'));
    }
    
    public function show(ClassRoom $class)
    {
        $user = auth()->user();
        $teacher = $user->teacher;
        
        if (!$teacher) {
            abort(403, 'Teacher profile not found.');
        }
        
        // Check if class is assigned to this teacher (class teacher, direct assignment, or teaches subjects in this class)
        $isAssigned = ($class->class_teacher_id === $teacher->id)
            || $class->teachers()->where('teachers.id', $teacher->id)->exists()
            || $class->subjects()->where('teacher_id', $teacher->id)->exists();
        
        if (!$isAssigned) {
            abort(403, 'Access denied. Class not assigned to you.');
        }
        
        $class->load(['students.user', 'subjects.teacher.user']);
        
        return view('teacher.classes.show', compact('class'));
    }
}
