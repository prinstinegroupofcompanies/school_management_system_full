<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Teacher;
use App\Models\Subject;
use Illuminate\Http\Request;

class TeacherController extends Controller
{
    public function index()
    {
        $student = auth()->user()->student;
        
        if (!$student) {
            abort(403, 'Student record not found');
        }
        
        // Get teachers who teach subjects in the student's class
        $teachers = Teacher::whereHas('subjects', function($query) use ($student) {
            $query->where('class_id', $student->class_id);
        })
        ->with(['subjects' => function($query) use ($student) {
            $query->where('class_id', $student->class_id);
        }])
        ->get();
            
        return view('student.teachers.index', compact('teachers'));
    }
}
