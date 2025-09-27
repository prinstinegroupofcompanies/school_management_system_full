<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class GradeController extends Controller
{
    public function index()
    {
        return view('student.grades.index');
    }

    public function transcript()
    {
        return view('student.grades.transcript');
    }

    public function downloadTranscript()
    {
        // Placeholder for transcript download
        return response()->download(storage_path('app/transcript.pdf'));
    }

    public function gradeSheet($year = null)
    {
        return view('student.grades.grade-sheet', compact('year'));
    }

    public function show($id)
    {
        return view('student.grades.show', compact('id'));
    }
}