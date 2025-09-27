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
        try {
            $user = auth()->user();
            $student = $user->student;
        } catch (\Exception $e) {
            return redirect()->route('student.dashboard')
                ->with('error', 'Student profile not available. Please contact administrator.');
        }
        
        if (!$student) {
            return redirect()->route('student.dashboard')
                ->with('error', 'Student record not found. Please contact administrator.');
        }

        $academicYear = $year ?? date('Y');
        
        return view('student.grades.grade-sheet', compact('year', 'academicYear', 'student'));
    }

    public function show($id)
    {
        return view('student.grades.show', compact('id'));
    }
}