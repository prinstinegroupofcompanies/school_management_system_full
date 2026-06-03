<?php

namespace App\Http\Controllers\Parent;

use App\Http\Controllers\Controller;
use App\Models\Grade;
use App\Models\Student;
use App\Models\Guardian;
use Illuminate\Http\Request;

class GradeController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        
        // Get guardian record for the parent user
        $guardian = \App\Models\Guardian::where('user_id', $user->id)->first();
        
        if (!$guardian) {
            return redirect()->route('parent.dashboard')
                ->with('error', 'Guardian profile not found.');
        }
        
        // Get all students linked to this guardian
        $students = Student::where('guardian_id', $guardian->id)
            ->orWhere('father_id', $guardian->id)
            ->orWhere('mother_id', $guardian->id)
            ->orWhere('local_guardian_id', $guardian->id)
            ->with('user')
            ->get();

        $selectedStudentId = $request->get('student_id', $students->first()->id ?? null);
        
        $grades = collect();
        if ($selectedStudentId) {
            $grades = Grade::where('student_id', $selectedStudentId)
                ->with(['subject', 'class', 'teacher.user'])
                ->orderBy('academic_year', 'desc')
                ->orderBy('created_at', 'desc')
                ->get();
        }

        return view('parent.grades.index', compact('students', 'grades', 'selectedStudentId'));
    }

    public function show(Grade $grade)
    {
        $this->authorize('view', $grade);
        
        $grade->load(['student.user', 'subject', 'class', 'teacher.user']);
        
        return view('parent.grades.show', compact('grade'));
    }

    public function studentGrades(Student $student)
    {
        $user = auth()->user();
        $guardian = \App\Models\Guardian::where('user_id', $user->id)->first();
        
        // Verify the student belongs to this guardian
        if (!$guardian || !in_array($guardian->id, [
            $student->guardian_id,
            $student->father_id,
            $student->mother_id,
            $student->local_guardian_id
        ])) {
            abort(403, 'You do not have access to view this student\'s grades.');
        }
        
        $grades = Grade::where('student_id', $student->id)
            ->with(['subject', 'class', 'teacher.user'])
            ->orderBy('academic_year', 'desc')
            ->orderBy('created_at', 'desc')
            ->get()
            ->groupBy('academic_year');

        return view('parent.grades.student-grades', compact('student', 'grades'));
    }

    public function subjectGrades(Student $student, $subjectId)
    {
        $user = auth()->user();
        $guardian = \App\Models\Guardian::where('user_id', $user->id)->first();
        
        // Verify the student belongs to this guardian
        if (!$guardian || !in_array($guardian->id, [
            $student->guardian_id,
            $student->father_id,
            $student->mother_id,
            $student->local_guardian_id
        ])) {
            abort(403, 'You do not have access to view this student\'s grades.');
        }
        
        $grades = Grade::where('student_id', $student->id)
            ->where('subject_id', $subjectId)
            ->with(['subject', 'class', 'teacher.user'])
            ->orderBy('academic_year', 'desc')
            ->get();

        return view('parent.grades.subject-grades', compact('student', 'grades'));
    }

    public function academicProgress(Student $student)
    {
        $user = auth()->user();
        $guardian = \App\Models\Guardian::where('user_id', $user->id)->first();
        
        // Verify the student belongs to this guardian
        if (!$guardian || !in_array($guardian->id, [
            $student->guardian_id,
            $student->father_id,
            $student->mother_id,
            $student->local_guardian_id
        ])) {
            abort(403, 'You do not have access to view this student\'s progress.');
        }
        
        // Get grades for the current academic year
        $currentYear = date('Y');
        $grades = Grade::where('student_id', $student->id)
            ->where('academic_year', $currentYear)
            ->with(['subject'])
            ->get();

        // Calculate progress statistics
        $progress = [
            'total_subjects' => $grades->count(),
            'average_grade' => $grades->avg('year_avg'),
            'highest_grade' => $grades->max('year_avg'),
            'lowest_grade' => $grades->min('year_avg'),
            'subjects_above_80' => $grades->where('year_avg', '>=', 80)->count(),
            'subjects_above_60' => $grades->where('year_avg', '>=', 60)->count(),
            'subjects_below_50' => $grades->where('year_avg', '<', 50)->count(),
        ];

        // Monthly progress trend
        $monthlyTrend = Grade::where('student_id', $student->id)
            ->where('academic_year', $currentYear)
            ->selectRaw('MONTH(created_at) as month, AVG(year_avg) as average_grade')
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        return view('parent.grades.academic-progress', compact('student', 'progress', 'monthlyTrend'));
    }

    public function downloadReport(Student $student)
    {
        $user = auth()->user();
        $guardian = \App\Models\Guardian::where('user_id', $user->id)->first();
        
        // Verify the student belongs to this guardian
        if (!$guardian || !in_array($guardian->id, [
            $student->guardian_id,
            $student->father_id,
            $student->mother_id,
            $student->local_guardian_id
        ])) {
            abort(403, 'You do not have access to download this student\'s report.');
        }
        
        // This would typically generate and download a PDF report
        // For now, return a JSON response
        return response()->json([
            'message' => 'Grade report download functionality would be implemented here',
            'student' => $student->user->name
        ]);
    }
}
