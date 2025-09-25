<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Grade;
use App\Models\Student;
use App\Models\Subject;
use App\Helpers\GradeHelper;
use Illuminate\Http\Request;

class GradeController extends Controller
{
    public function __construct()
    {
        $this->middleware('student');
    }

    /**
     * Display student's approved grades
     */
    public function index(Request $request)
    {
        $student = $request->user()->student;
        
        if (!$student) {
            return redirect()->route('student.dashboard')
                           ->withErrors(['error' => 'Student profile not found.']);
        }

        $academicYear = $request->get('year', date('Y'));
        $classId = $student->class_id;

        // Ensure student has grade records for all class subjects
        GradeHelper::ensureStudentHasAllSubjectGrades($student, $academicYear, $classId);

        $query = Grade::where('student_id', $student->id)
                     ->where('class_id', $classId)
                     ->where('academic_year', $academicYear)
                     ->where('status', 'approved')
                     ->with(['subject', 'class', 'teacher.user']);

        // Apply filters
        if ($request->filled('subject_id')) {
            $query->where('subject_id', $request->subject_id);
        }

        $grades = $query->orderBy('subject_id')->get();

        // Get filter options
        $classes = $student->classRoom;
        $subjects = GradeHelper::getClassSubjects($classId);

        // Get comprehensive grade summary
        $gradeSummary = GradeHelper::getStudentGradeSummary($student, $academicYear, $classId);

        // Calculate summary statistics
        $stats = [
            'total_subjects' => $gradeSummary['total_subjects'],
            'average_score' => $gradeSummary['overall_average'],
            'highest_score' => $grades->whereNotNull('year_avg')->max('year_avg'),
            'lowest_score' => $grades->whereNotNull('year_avg')->min('year_avg'),
            'semester1_average' => $gradeSummary['semester1_average'],
            'semester2_average' => $gradeSummary['semester2_average'],
            'is_eligible_for_promotion' => $gradeSummary['is_eligible_for_promotion'],
            'period_averages' => $gradeSummary['period_averages'],
        ];

        return view('student.grades.index', compact('grades', 'classes', 'subjects', 'stats', 'gradeSummary', 'academicYear'));
    }

    /**
     * Show detailed grade for a specific subject
     */
    public function show(Grade $grade)
    {
        $student = auth()->user()->student;
        
        // Verify the grade belongs to the authenticated student
        if ($grade->student_id !== $student->id) {
            return redirect()->route('student.grades.index')
                           ->withErrors(['error' => 'You are not authorized to view this grade.']);
        }

        // Only show approved grades to students
        if ($grade->status !== 'approved') {
            return redirect()->route('student.grades.index')
                           ->withErrors(['error' => 'This grade has not been approved yet.']);
        }

        $grade->load(['subject', 'class', 'teacher.user', 'approvedBy']);
        
        return view('student.grades.show', compact('grade'));
    }

    /**
     * Get grade summary/transcript
     */
    public function transcript(Request $request)
    {
        $student = auth()->user()->student;
        
        if (!$student) {
            return redirect()->route('student.dashboard')
                           ->withErrors(['error' => 'Student profile not found.']);
        }

        $grades = Grade::where('student_id', $student->id)
                      ->where('status', 'approved')
                      ->with(['subject', 'class', 'teacher.user'])
                      ->orderBy('subject_id')
                      ->get()
                      ->groupBy('subject.name');

        // Calculate overall statistics
        $stats = [
            'total_subjects' => $grades->count(),
            'average_score' => Grade::where('student_id', $student->id)
                                  ->where('status', 'approved')
                                  ->whereNotNull('year_avg')
                                  ->avg('year_avg'),
            'total_credits' => $grades->count(), // Assuming each subject is worth 1 credit
        ];

        return view('student.grades.transcript', compact('grades', 'stats', 'student'));
    }

    /**
     * Download transcript as PDF
     */
    public function downloadTranscript()
    {
        $student = auth()->user()->student;
        
        if (!$student) {
            return redirect()->route('student.dashboard')
                           ->withErrors(['error' => 'Student profile not found.']);
        }

        $grades = Grade::where('student_id', $student->id)
                      ->where('status', 'approved')
                      ->with(['subject', 'class', 'teacher.user'])
                      ->orderBy('subject_id')
                      ->get()
                      ->groupBy('subject.name');

        $stats = [
            'total_subjects' => $grades->count(),
            'average_score' => Grade::where('student_id', $student->id)
                                  ->where('status', 'approved')
                                  ->whereNotNull('year_avg')
                                  ->avg('year_avg'),
        ];

        // Generate PDF (you can implement PDF generation here)
        // For now, just return the transcript view
        return view('student.grades.transcript-pdf', compact('grades', 'stats', 'student'));
    }

    /**
     * Display official grade sheet
     */
    public function gradeSheet($year = null)
    {
        $student = auth()->user()->student;
        
        if (!$student) {
            return redirect()->route('student.dashboard')
                           ->withErrors(['error' => 'Student profile not found.']);
        }

        $academicYear = $year ?? date('Y');
        $classId = $student->class_id;

        // Ensure student has grade records for all class subjects
        GradeHelper::ensureStudentHasAllSubjectGrades($student, $academicYear, $classId);
        
        $grades = Grade::where('student_id', $student->id)
                      ->where('class_id', $classId)
                      ->where('status', 'approved')
                      ->where('academic_year', $academicYear)
                      ->with(['subject', 'class', 'teacher.user', 'approvedBy'])
                      ->orderBy('subject_id')
                      ->get();

        // Get comprehensive grade summary
        $gradeSummary = GradeHelper::getStudentGradeSummary($student, $academicYear, $classId);

        // Calculate statistics
        $stats = [
            'total_subjects' => $gradeSummary['total_subjects'],
            'average_score' => $gradeSummary['overall_average'],
            'highest_score' => $grades->whereNotNull('year_avg')->max('year_avg'),
            'lowest_score' => $grades->whereNotNull('year_avg')->min('year_avg'),
            'semester1_average' => $gradeSummary['semester1_average'],
            'semester2_average' => $gradeSummary['semester2_average'],
            'is_eligible_for_promotion' => $gradeSummary['is_eligible_for_promotion'],
        ];

        // Get admin signature (from the user who approved the grades)
        $adminSignature = null;
        if ($grades->count() > 0) {
            $approvedBy = $grades->first()->approvedBy;
            if ($approvedBy && $approvedBy->signature) {
                $adminSignature = $approvedBy->signature;
            }
        }

        return view('student.grades.grade-sheet', compact('grades', 'stats', 'student', 'academicYear', 'adminSignature', 'gradeSummary'));
    }
}