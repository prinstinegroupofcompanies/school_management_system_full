<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Subject;
use App\Models\Student;
use Illuminate\Http\Request;

class SubjectController extends Controller
{
    public function index()
    {
        $student = auth()->user()->student;
        
        if (!$student) {
            abort(403, 'Student record not found');
        }
        
        // Get subjects assigned to the student (synced from class on enrollment)
        $subjects = $student->subjects()
            ->with(['teacher', 'class'])
            ->get();
            
        return view('student.subjects.index', compact('subjects'));
    }
    
    public function show(Subject $subject)
    {
        $student = auth()->user()->student;
        
        if (!$student) {
            abort(403, 'Student record not found');
        }
        
        // Check if student has access to this subject (via pivot assignment)
        $hasAccess = $student->subjects()->where('subjects.id', $subject->id)->exists();
        if (!$hasAccess) {
            abort(403, 'You do not have access to this subject');
        }
        
        $subject->load(['teacher', 'class']);
        
        return view('student.subjects.show', compact('subject'));
    }
}