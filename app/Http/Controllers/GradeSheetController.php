<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\Grade;
use App\Models\ClassRoom;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\DB;

class GradeSheetController extends Controller
{
    /**
     * Generate grade sheet PDF for a student.
     */
    public function generate(Request $request, Student $student)
    {
        $request->validate([
            'term' => 'required|string',
            'year' => 'required|integer|min:2000|max:2100',
        ]);

        $term = $request->term;
        $year = $request->year;

        // Get grades for the term
        $grades = Grade::where('student_id', $student->id)
            ->where('academic_year', $year)
            ->with(['subject', 'teacher', 'class'])
            ->get();

        // Calculate totals
        $totalScore = $grades->sum('year_avg');
        $subjectCount = $grades->count();
        $averageScore = $subjectCount > 0 ? $totalScore / $subjectCount : 0;
        
        // Calculate GPA (on 4.0 scale)
        $gpa = $this->calculateGPA($grades);

        // Get school details
        $schoolName = \App\Models\SystemSetting::get('school_name', config('app.name'));
        $schoolAddress = \App\Models\SystemSetting::get('school_address', '');
        $schoolLogo = \App\Models\SystemSetting::get('school_logo', '');
        $schoolLogoPath = $schoolLogo ? storage_path('app/public/' . $schoolLogo) : null;

        // Prepare grade data
        $gradeData = [];
        foreach ($grades as $grade) {
            $midTerm = $grade->sem1_avg ?? 0;
            $final = $grade->sem2_avg ?? 0;
            $total = $grade->year_avg ?? 0;
            
            $gradeData[] = [
                'subject' => $grade->subject->name ?? 'N/A',
                'mid_term' => round($midTerm, 2),
                'final' => round($final, 2),
                'total' => round($total, 2),
                'grade' => $this->getLetterGrade($total),
                'remark' => $this->getRemark($total),
            ];
        }

        $data = [
            'student' => $student->load('user'),
            'class' => $student->classRoom,
            'term' => $term,
            'year' => $year,
            'grades' => $gradeData,
            'averageScore' => round($averageScore, 2),
            'gpa' => round($gpa, 2),
            'schoolName' => $schoolName,
            'schoolAddress' => $schoolAddress,
            'schoolLogoPath' => $schoolLogoPath,
            'generatedDate' => now()->format('F d, Y'),
        ];

        $pdf = Pdf::loadView('grades.grade-sheet', $data);
        return $pdf->stream("{$student->admission_no}_grade_sheet_{$term}_{$year}.pdf");
    }

    /**
     * Download grade sheet PDF.
     */
    public function download(Request $request, Student $student)
    {
        $request->validate([
            'term' => 'required|string',
            'year' => 'required|integer|min:2000|max:2100',
        ]);

        $term = $request->term;
        $year = $request->year;

        // Same logic as generate but with download response
        $grades = Grade::where('student_id', $student->id)
            ->where('academic_year', $year)
            ->with(['subject', 'teacher', 'class'])
            ->get();

        $totalScore = $grades->sum('year_avg');
        $subjectCount = $grades->count();
        $averageScore = $subjectCount > 0 ? $totalScore / $subjectCount : 0;
        $gpa = $this->calculateGPA($grades);

        $schoolName = \App\Models\SystemSetting::get('school_name', config('app.name'));
        $schoolAddress = \App\Models\SystemSetting::get('school_address', '');
        $schoolLogo = \App\Models\SystemSetting::get('school_logo', '');
        $schoolLogoPath = $schoolLogo ? storage_path('app/public/' . $schoolLogo) : null;

        $gradeData = [];
        foreach ($grades as $grade) {
            $midTerm = $grade->sem1_avg ?? 0;
            $final = $grade->sem2_avg ?? 0;
            $total = $grade->year_avg ?? 0;
            
            $gradeData[] = [
                'subject' => $grade->subject->name ?? 'N/A',
                'mid_term' => round($midTerm, 2),
                'final' => round($final, 2),
                'total' => round($total, 2),
                'grade' => $this->getLetterGrade($total),
                'remark' => $this->getRemark($total),
            ];
        }

        $data = [
            'student' => $student->load('user'),
            'class' => $student->classRoom,
            'term' => $term,
            'year' => $year,
            'grades' => $gradeData,
            'averageScore' => round($averageScore, 2),
            'gpa' => round($gpa, 2),
            'schoolName' => $schoolName,
            'schoolAddress' => $schoolAddress,
            'schoolLogoPath' => $schoolLogoPath,
            'generatedDate' => now()->format('F d, Y'),
        ];

        $pdf = Pdf::loadView('grades.grade-sheet', $data);
        return $pdf->download("{$student->admission_no}_grade_sheet_{$term}_{$year}.pdf");
    }

    /**
     * Calculate GPA on 4.0 scale.
     */
    private function calculateGPA($grades): float
    {
        if ($grades->isEmpty()) {
            return 0.0;
        }

        $totalPoints = 0;
        $count = 0;

        foreach ($grades as $grade) {
            $percentage = $grade->year_avg ?? 0;
            $gpaPoints = $this->percentageToGPA($percentage);
            $totalPoints += $gpaPoints;
            $count++;
        }

        return $count > 0 ? $totalPoints / $count : 0.0;
    }

    /**
     * Convert percentage to GPA points.
     */
    private function percentageToGPA(float $percentage): float
    {
        if ($percentage >= 97) return 4.0;
        if ($percentage >= 93) return 4.0;
        if ($percentage >= 90) return 3.7;
        if ($percentage >= 87) return 3.3;
        if ($percentage >= 83) return 3.0;
        if ($percentage >= 80) return 2.7;
        if ($percentage >= 77) return 2.3;
        if ($percentage >= 73) return 2.0;
        if ($percentage >= 70) return 1.7;
        if ($percentage >= 67) return 1.3;
        if ($percentage >= 65) return 1.0;
        return 0.0;
    }

    /**
     * Get letter grade from percentage.
     */
    private function getLetterGrade(?float $percentage): string
    {
        if (!$percentage) return 'N/A';
        
        if ($percentage >= 97) return 'A+';
        if ($percentage >= 93) return 'A';
        if ($percentage >= 90) return 'A-';
        if ($percentage >= 87) return 'B+';
        if ($percentage >= 83) return 'B';
        if ($percentage >= 80) return 'B-';
        if ($percentage >= 77) return 'C+';
        if ($percentage >= 73) return 'C';
        if ($percentage >= 70) return 'C-';
        if ($percentage >= 67) return 'D+';
        if ($percentage >= 65) return 'D';
        return 'F';
    }

    /**
     * Get remark from percentage.
     */
    private function getRemark(?float $percentage): string
    {
        if (!$percentage) return 'N/A';
        
        if ($percentage >= 90) return 'Excellent';
        if ($percentage >= 80) return 'Very Good';
        if ($percentage >= 70) return 'Good';
        if ($percentage >= 60) return 'Satisfactory';
        if ($percentage >= 50) return 'Fair';
        return 'Needs Improvement';
    }
}
