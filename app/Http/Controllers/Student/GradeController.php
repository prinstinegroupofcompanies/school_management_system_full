<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\InternationalGrade;
use App\Models\Student;
use App\Models\Subject;
use App\Models\ClassRoom;
use Illuminate\Http\Request;

class GradeController extends Controller
{
    public function __construct()
    {
        $this->middleware('student');
    }

    /**
     * Display student's grades
     */
    public function index(Request $request)
    {
        $student = $request->user()->student;
        
        if (!$student) {
            return redirect()->route('student.dashboard')
                           ->withErrors(['error' => 'Student profile not found.']);
        }

        $query = InternationalGrade::where('student_id', $student->id)
                                  ->where('status', 'published')
                                  ->where('visible_to_student', true)
                                  ->with(['subject', 'teacher.user', 'classRoom']);

        // Apply filters
        if ($request->filled('subject_id')) {
            $query->where('subject_id', $request->subject_id);
        }

        if ($request->filled('academic_year')) {
            $query->where('academic_year', $request->academic_year);
        }

        if ($request->filled('semester')) {
            $query->where('semester', $request->semester);
        }

        if ($request->filled('assessment_type')) {
            $query->where('assessment_type', $request->assessment_type);
        }

        $grades = $query->orderBy('assessment_date', 'desc')->paginate(15);
        
        // Get filter options
        $subjects = Subject::whereIn('id', $student->assigned_subjects ? 
                                   collect($student->assigned_subjects)->pluck('id') : [])->get();
        
        $academicYears = InternationalGrade::where('student_id', $student->id)
                                         ->where('status', 'published')
                                         ->distinct()
                                         ->pluck('academic_year')
                                         ->sort()
                                         ->values();

        // Calculate GPA and academic standing
        $currentGPA = $this->calculateCurrentGPA($student);
        $academicStanding = $this->getAcademicStanding($currentGPA);
        
        // Get grade summary by subject
        $subjectSummary = $this->getSubjectGradeSummary($student);

        // Get recent achievements
        $recentAchievements = InternationalGrade::where('student_id', $student->id)
                                              ->where('status', 'published')
                                              ->where('letter_grade', 'in', ['A+', 'A', 'A-'])
                                              ->orderBy('published_at', 'desc')
                                              ->take(5)
                                              ->get();

        return view('student.grades.index', compact(
            'grades', 'subjects', 'academicYears', 'currentGPA', 
            'academicStanding', 'subjectSummary', 'recentAchievements'
        ));
    }

    /**
     * Show specific grade details
     */
    public function show(InternationalGrade $grade)
    {
        $student = auth()->user()->student;
        
        // Verify student owns this grade and it's published
        if ($grade->student_id !== $student->id || 
            $grade->status !== 'published' || 
            !$grade->visible_to_student) {
            return redirect()->route('student.grades.index')
                           ->withErrors(['error' => 'Grade not found or not accessible.']);
        }

        $grade->load(['subject', 'teacher.user', 'classRoom', 'approvedBy']);
        
        // Get related grades for context
        $relatedGrades = InternationalGrade::where('student_id', $student->id)
                                         ->where('subject_id', $grade->subject_id)
                                         ->where('academic_year', $grade->academic_year)
                                         ->where('status', 'published')
                                         ->where('id', '!=', $grade->id)
                                         ->orderBy('assessment_date', 'desc')
                                         ->take(5)
                                         ->get();

        return view('student.grades.show', compact('grade', 'relatedGrades'));
    }

    /**
     * Display grade report/transcript
     */
    public function transcript(Request $request)
    {
        $student = $request->user()->student;
        
        if (!$student) {
            return redirect()->route('student.dashboard')
                           ->withErrors(['error' => 'Student profile not found.']);
        }

        $academicYear = $request->get('academic_year', $student->academic_year ?? date('Y'));
        
        // Get all published grades for the academic year
        $grades = InternationalGrade::where('student_id', $student->id)
                                   ->where('academic_year', $academicYear)
                                   ->where('status', 'published')
                                   ->where('visible_to_student', true)
                                   ->with(['subject', 'teacher.user'])
                                   ->orderBy('semester')
                                   ->orderBy('subject_id')
                                   ->orderBy('assessment_date')
                                   ->get();

        // Group grades by semester and subject
        $gradesBySubject = $grades->groupBy(['semester', 'subject_id']);
        
        // Calculate semester GPAs
        $semesterGPAs = [];
        foreach (['fall', 'spring', 'summer'] as $semester) {
            $semesterGrades = $grades->where('semester', $semester)
                                   ->where('counts_toward_final', true);
            
            if ($semesterGrades->isNotEmpty()) {
                $totalPoints = 0;
                $totalCredits = 0;
                
                foreach ($semesterGrades as $grade) {
                    $credits = $grade->weight ?? 1;
                    $totalPoints += $grade->gpa_points * $credits;
                    $totalCredits += $credits;
                }
                
                $semesterGPAs[$semester] = $totalCredits > 0 ? round($totalPoints / $totalCredits, 2) : 0.0;
            }
        }

        // Calculate overall GPA
        $overallGPA = $this->calculateCurrentGPA($student, $academicYear);
        $academicStanding = $this->getAcademicStanding($overallGPA);

        // Get student info
        $student->load(['user', 'classRoom']);

        return view('student.grades.transcript', compact(
            'student', 'gradesBySubject', 'semesterGPAs', 'overallGPA', 
            'academicStanding', 'academicYear'
        ));
    }

    /**
     * Calculate current GPA for student
     */
    private function calculateCurrentGPA(Student $student, $academicYear = null): float
    {
        $academicYear = $academicYear ?? $student->academic_year ?? date('Y');
        
        $grades = InternationalGrade::where('student_id', $student->id)
                                   ->where('academic_year', $academicYear)
                                   ->where('status', 'published')
                                   ->where('counts_toward_final', true)
                                   ->get();

        if ($grades->isEmpty()) return 0.0;

        $totalPoints = 0;
        $totalCredits = 0;

        foreach ($grades as $grade) {
            $credits = $grade->weight ?? 1;
            $totalPoints += $grade->gpa_points * $credits;
            $totalCredits += $credits;
        }

        return $totalCredits > 0 ? round($totalPoints / $totalCredits, 2) : 0.0;
    }

    /**
     * Get academic standing based on GPA
     */
    private function getAcademicStanding($gpa): array
    {
        if ($gpa >= 3.85) {
            return ['status' => 'Summa Cum Laude', 'color' => 'text-yellow-600', 'bg' => 'bg-yellow-100'];
        } elseif ($gpa >= 3.7) {
            return ['status' => 'Magna Cum Laude', 'color' => 'text-yellow-600', 'bg' => 'bg-yellow-50'];
        } elseif ($gpa >= 3.5) {
            return ['status' => 'Cum Laude', 'color' => 'text-blue-600', 'bg' => 'bg-blue-100'];
        } elseif ($gpa >= 3.0) {
            return ['status' => 'Good Standing', 'color' => 'text-green-600', 'bg' => 'bg-green-100'];
        } elseif ($gpa >= 2.5) {
            return ['status' => 'Satisfactory', 'color' => 'text-gray-600', 'bg' => 'bg-gray-100'];
        } elseif ($gpa >= 2.0) {
            return ['status' => 'Academic Probation', 'color' => 'text-orange-600', 'bg' => 'bg-orange-100'];
        } else {
            return ['status' => 'Academic Warning', 'color' => 'text-red-600', 'bg' => 'bg-red-100'];
        }
    }

    /**
     * Get grade summary by subject
     */
    private function getSubjectGradeSummary(Student $student): array
    {
        $academicYear = $student->academic_year ?? date('Y');
        
        $grades = InternationalGrade::where('student_id', $student->id)
                                   ->where('academic_year', $academicYear)
                                   ->where('status', 'published')
                                   ->with('subject')
                                   ->get();

        $summary = [];
        
        foreach ($grades->groupBy('subject_id') as $subjectId => $subjectGrades) {
            $subject = $subjectGrades->first()->subject;
            $finalGrades = $subjectGrades->where('counts_toward_final', true);
            
            if ($finalGrades->isNotEmpty()) {
                $totalPoints = 0;
                $totalCredits = 0;
                
                foreach ($finalGrades as $grade) {
                    $credits = $grade->weight ?? 1;
                    $totalPoints += $grade->gpa_points * $credits;
                    $totalCredits += $credits;
                }
                
                $subjectGPA = $totalCredits > 0 ? round($totalPoints / $totalCredits, 2) : 0.0;
                $latestGrade = $finalGrades->sortByDesc('assessment_date')->first();
                
                $summary[] = [
                    'subject' => $subject,
                    'gpa' => $subjectGPA,
                    'letter_grade' => $latestGrade->letter_grade,
                    'latest_percentage' => $latestGrade->percentage,
                    'total_assessments' => $finalGrades->count(),
                    'avg_percentage' => $finalGrades->avg('percentage'),
                ];
            }
        }

        return collect($summary)->sortByDesc('gpa')->values()->all();
    }

    /**
     * Download grade report as PDF
     */
    public function downloadTranscript(Request $request)
    {
        $student = $request->user()->student;
        
        if (!$student) {
            return redirect()->route('student.dashboard')
                           ->withErrors(['error' => 'Student profile not found.']);
        }

        $academicYear = $request->get('academic_year', $student->academic_year ?? date('Y'));
        
        // Get all published grades for the academic year
        $grades = InternationalGrade::where('student_id', $student->id)
                                   ->where('academic_year', $academicYear)
                                   ->where('status', 'published')
                                   ->where('visible_to_student', true)
                                   ->with(['subject', 'teacher.user'])
                                   ->orderBy('semester')
                                   ->orderBy('subject_id')
                                   ->orderBy('assessment_date')
                                   ->get();

        // Group grades by semester and subject
        $gradesBySubject = $grades->groupBy(['semester', 'subject_id']);
        
        // Calculate GPAs
        $semesterGPAs = [];
        foreach (['fall', 'spring', 'summer'] as $semester) {
            $semesterGrades = $grades->where('semester', $semester)
                                   ->where('counts_toward_final', true);
            
            if ($semesterGrades->isNotEmpty()) {
                $totalPoints = 0;
                $totalCredits = 0;
                
                foreach ($semesterGrades as $grade) {
                    $credits = $grade->weight ?? 1;
                    $totalPoints += $grade->gpa_points * $credits;
                    $totalCredits += $credits;
                }
                
                $semesterGPAs[$semester] = $totalCredits > 0 ? round($totalPoints / $totalCredits, 2) : 0.0;
            }
        }

        $overallGPA = $this->calculateCurrentGPA($student, $academicYear);
        $academicStanding = $this->getAcademicStanding($overallGPA);

        $student->load(['user', 'classRoom']);

        // Generate PDF (you can use a package like dompdf or tcpdf)
        $pdf = \PDF::loadView('student.grades.transcript-pdf', compact(
            'student', 'gradesBySubject', 'semesterGPAs', 'overallGPA', 
            'academicStanding', 'academicYear'
        ));

        return $pdf->download("transcript_{$student->student_number}_{$academicYear}.pdf");
    }
}