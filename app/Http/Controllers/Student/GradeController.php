<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class GradeController extends Controller
{
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

        // Get available academic periods (by term/semester) and years for end-of-year
        try {
            $periods = \App\Models\Grade::where('student_id', $student->id)
                ->select('academic_year', 'semester')
                ->distinct()
                ->orderBy('academic_year', 'desc')
                ->orderBy('semester', 'desc')
                ->get()
                ->map(function($grade) {
                    $termLabel = $grade->semester == 1 ? 'Semester 1 (Term 1)' : ($grade->semester == 2 ? 'Semester 2 (Term 2)' : "Period {$grade->semester}");
                    return [
                        'year' => $grade->academic_year,
                        'semester' => $grade->semester,
                        'period_name' => "{$termLabel} - {$grade->academic_year}",
                        'period_key' => "{$grade->academic_year}_{$grade->semester}"
                    ];
                });
            $yearsWithGrades = \App\Models\Grade::where('student_id', $student->id)
                ->where('status', 'approved')
                ->select('academic_year')
                ->distinct()
                ->orderBy('academic_year', 'desc')
                ->pluck('academic_year');
        } catch (\Exception $e) {
            $periods = collect();
            $yearsWithGrades = collect();
        }

        return view('student.grades.index', compact('periods', 'yearsWithGrades'));
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

    /**
     * Full academic year grade sheet: all terms/semesters, final yearly average, promotion eligibility (70%+).
     */
    public function fullYearGradeSheet($year)
    {
        try {
            $user = auth()->user();
            $student = $user->student;
        } catch (\Exception $e) {
            return redirect()->route('student.dashboard')->with('error', 'Student profile not available.');
        }
        if (!$student) {
            return redirect()->route('student.dashboard')->with('error', 'Student record not found.');
        }

        $grades = \App\Models\Grade::where('student_id', $student->id)
            ->where('academic_year', $year)
            ->where('status', 'approved')
            ->with(['subject', 'class', 'teacher.user'])
            ->orderBy('semester')
            ->orderBy('subject_id')
            ->get();

        $yearlyAverage = $grades->count() > 0 ? round($grades->avg('year_avg'), 2) : 0;
        $eligibleForPromotion = $yearlyAverage >= 70.0;
        $bySemester = $grades->groupBy('semester');

        $stats = [
            'total_subjects' => $grades->unique('subject_id')->count(),
            'yearly_average' => $yearlyAverage,
            'eligible_for_promotion' => $eligibleForPromotion,
            'passed_subjects' => $grades->where('year_avg', '>=', 50)->count(),
            'failed_subjects' => $grades->where('year_avg', '<', 50)->count(),
        ];

        try {
            $adminUser = \App\Models\User::where('user_type', 'admin')->first();
            $adminSignature = $adminUser ? $adminUser->signature : null;
        } catch (\Exception $e) {
            $adminSignature = null;
        }
        try {
            $school = \App\Models\School::first();
        } catch (\Exception $e) {
            $school = null;
        }

        return view('student.grades.grade-sheet-full-year', compact('year', 'student', 'grades', 'bySemester', 'stats', 'adminSignature', 'school'));
    }

    public function downloadFullYearGradeSheet($year)
    {
        try {
            $user = auth()->user();
            $student = $user->student;
        } catch (\Exception $e) {
            return redirect()->route('student.dashboard')->with('error', 'Student profile not available.');
        }
        if (!$student) {
            return redirect()->route('student.dashboard')->with('error', 'Student record not found.');
        }

        $grades = \App\Models\Grade::where('student_id', $student->id)
            ->where('academic_year', $year)
            ->where('status', 'approved')
            ->with(['subject', 'class', 'teacher.user'])
            ->orderBy('semester')
            ->orderBy('subject_id')
            ->get();

        $yearlyAverage = $grades->count() > 0 ? round($grades->avg('year_avg'), 2) : 0;
        $eligibleForPromotion = $yearlyAverage >= 70.0;
        $bySemester = $grades->groupBy('semester');
        $stats = [
            'total_subjects' => $grades->unique('subject_id')->count(),
            'yearly_average' => $yearlyAverage,
            'eligible_for_promotion' => $eligibleForPromotion,
            'passed_subjects' => $grades->where('year_avg', '>=', 50)->count(),
            'failed_subjects' => $grades->where('year_avg', '<', 50)->count(),
        ];
        $adminSignature = null;
        $school = null;
        try {
            $adminUser = \App\Models\User::where('user_type', 'admin')->first();
            $adminSignature = $adminUser ? $adminUser->signature : null;
            $school = \App\Models\School::first();
        } catch (\Exception $e) {}

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('student.grades.grade-sheet-full-year-pdf', compact('year', 'student', 'grades', 'bySemester', 'stats', 'adminSignature', 'school'));
        $filename = "Grade_Sheet_Full_Year_{$year}_" . ($student->admission_no ?? $student->id) . ".pdf";
        return $pdf->download($filename);
    }

    public function show($id)
    {
        return view('student.grades.show', compact('id'));
    }
}