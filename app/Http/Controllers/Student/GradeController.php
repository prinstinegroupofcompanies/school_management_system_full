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

    public function index()
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

        // Get available academic periods for the student
        try {
            $periods = \App\Models\Grade::where('student_id', $student->id)
                ->select('academic_year', 'semester')
                ->distinct()
                ->orderBy('academic_year', 'desc')
                ->orderBy('semester', 'desc')
                ->get()
                ->map(function($grade) {
                    return [
                        'year' => $grade->academic_year,
                        'semester' => $grade->semester,
                        'period_name' => "Period {$grade->semester} - {$grade->academic_year}",
                        'period_key' => "{$grade->academic_year}_{$grade->semester}"
                    ];
                });
        } catch (\Exception $e) {
            $periods = collect();
        }

        return view('student.grades.index', compact('periods'));
    }

    public function gradeSheet($year, $semester)
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

        // Fetch grades for the specific period
        try {
            $grades = \App\Models\Grade::where('student_id', $student->id)
                ->where('academic_year', $year)
                ->where('semester', $semester)
                ->where('status', 'approved') // Only show approved grades
                ->with(['subject', 'class', 'teacher.user'])
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

        // Get admin signature
        try {
            $adminUser = \App\Models\User::where('user_type', 'admin')->first();
            $adminSignature = $adminUser ? $adminUser->signature : null;
        } catch (\Exception $e) {
            $adminSignature = null;
        }

        // Get school information
        try {
            $school = \App\Models\School::first();
        } catch (\Exception $e) {
            $school = null;
        }
        
        return view('student.grades.grade-sheet', compact('year', 'semester', 'student', 'grades', 'stats', 'adminSignature', 'school'));
    }

    public function downloadGradeSheet($year, $semester)
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

        // Fetch grades for the specific period
        try {
            $grades = \App\Models\Grade::where('student_id', $student->id)
                ->where('academic_year', $year)
                ->where('semester', $semester)
                ->where('status', 'approved')
                ->with(['subject', 'class', 'teacher.user'])
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

        // Get admin signature
        try {
            $adminUser = \App\Models\User::where('user_type', 'admin')->first();
            $adminSignature = $adminUser ? $adminUser->signature : null;
        } catch (\Exception $e) {
            $adminSignature = null;
        }

        // Get school information
        try {
            $school = \App\Models\School::first();
        } catch (\Exception $e) {
            $school = null;
        }

        // Generate PDF
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('student.grades.grade-sheet-pdf', compact('year', 'semester', 'student', 'grades', 'stats', 'adminSignature', 'school'));
        
        $filename = "Grade_Sheet_Period_{$semester}_{$year}_{$student->student_id}.pdf";
        
        return $pdf->download($filename);
    }

    public function show($id)
    {
        return view('student.grades.show', compact('id'));
    }
}