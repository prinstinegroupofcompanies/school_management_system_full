<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Subject;
use Illuminate\Http\Request;

class SubjectController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $teacher = $user->teacher;
        
        if (!$teacher) {
            abort(403, 'Teacher profile not found.');
        }
        
        // Get subjects assigned to this teacher
        $subjects = $teacher->subjects()->with('class')->get();
        
        return view('teacher.subjects.index', compact('subjects'));
    }
    
    public function show(Subject $subject)
    {
        $user = auth()->user();
        $teacher = $user->teacher;
        
        if (!$teacher) {
            abort(403, 'Teacher profile not found.');
        }
        
        // Check if subject is assigned to this teacher
        if ($subject->teacher_id !== $teacher->id) {
            abort(403, 'Access denied. Subject not assigned to you.');
        }
        
        $subject->load(['class', 'teacher.user']);
        
        return view('teacher.subjects.show', compact('subject'));
    }
}
