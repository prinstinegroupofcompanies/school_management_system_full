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
        
        // Fetch grades for the student
        try {
            $grades = \App\Models\Grade::where('student_id', $student->id)
                ->where('academic_year', $academicYear)
                ->with(['subject', 'class'])
                ->orderBy('subject_id')
                ->get();
            
            // Calculate statistics
            $stats = [
                'total_subjects' => $grades->count(),
                'average_score' => $grades->count() > 0 ? $grades->avg('year_avg') : 0,
                'highest_score' => $grades->count() > 0 ? $grades->max('year_avg') : 0,
                'lowest_score' => $grades->count() > 0 ? $grades->min('year_avg') : 0,
                'passed_subjects' => $grades->where('year_avg', '>=', 50)->count(),
                'failed_subjects' => $grades->where('year_avg', '<', 50)->count(),
            ];
        } catch (\Exception $e) {
            $grades = collect();
            $stats = [
                'total_subjects' => 0,
                'average_score' => 0,
                'highest_score' => 0,
                'lowest_score' => 0,
                'passed_subjects' => 0,
                'failed_subjects' => 0,
            ];
        }
        
        return view('student.grades.grade-sheet', compact('year', 'academicYear', 'student', 'grades', 'stats'));
    }

    public function show($id)
    {
        return view('student.grades.show', compact('id'));
    }
}