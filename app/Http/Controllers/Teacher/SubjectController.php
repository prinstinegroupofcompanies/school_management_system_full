<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class SubjectController extends Controller
{
    public function index()
    {
        try {
            $user = auth()->user();
            $teacher = $user->teacher;
            
            if (!$teacher) {
                return redirect()->route('teacher.dashboard')
                    ->with('error', 'Teacher profile not found. Please contact administrator.');
            }

            // Get subjects taught by this teacher
            $subjects = \App\Models\Subject::where('teacher_id', $teacher->id)
                ->with(['classes', 'grades' => function($query) use ($teacher) {
                    $query->where('teacher_id', $teacher->id);
                }])
                ->orderBy('name')
                ->get();

            // Get subject statistics
            $stats = [
                'total_subjects' => $subjects->count(),
                'total_students' => $subjects->sum(function($subject) {
                    return $subject->grades->unique('student_id')->count();
                }),
                'total_grades' => $subjects->sum(function($subject) {
                    return $subject->grades->count();
                }),
                'average_grade' => $subjects->flatMap->grades->where('year_avg', '>', 0)->avg('year_avg') ?: 0
            ];

        } catch (\Exception $e) {
            // Fallback data if database queries fail
            $subjects = collect();
            $stats = [
                'total_subjects' => 0,
                'total_students' => 0,
                'total_grades' => 0,
                'average_grade' => 0
            ];
        }

        return view('teacher.subjects.index', compact('subjects', 'stats'));
    }

    public function show($id)
    {
        try {
            $user = auth()->user();
            $teacher = $user->teacher;
            
            if (!$teacher) {
                return redirect()->route('teacher.subjects.index')
                    ->with('error', 'Teacher profile not found. Please contact administrator.');
            }

            // Get the specific subject (ensure teacher teaches it)
            $subject = \App\Models\Subject::where('id', $id)
                ->where('teacher_id', $teacher->id)
                ->with(['classes', 'grades' => function($query) use ($teacher) {
                    $query->where('teacher_id', $teacher->id)->with(['student.user', 'class']);
                }])
                ->first();

            if (!$subject) {
                return redirect()->route('teacher.subjects.index')
                    ->with('error', 'Subject not found or you do not have permission to view it.');
            }

            // Get subject-specific statistics
            $subjectStats = [
                'total_students' => $subject->grades->unique('student_id')->count(),
                'total_grades' => $subject->grades->count(),
                'average_grade' => $subject->grades->where('year_avg', '>', 0)->avg('year_avg') ?: 0,
                'pending_grades' => $subject->grades->where('status', 'pending')->count(),
                'approved_grades' => $subject->grades->where('status', 'approved')->count()
            ];

            // Get recent activities for this subject
            $recentActivities = collect([
                ['description' => 'Subject assigned to teacher', 'created_at' => $subject->created_at],
                ['description' => 'Total grades recorded: ' . $subject->grades->count(), 'created_at' => $subject->updated_at],
            ]);

        } catch (\Exception $e) {
            return redirect()->route('teacher.subjects.index')
                ->with('error', 'Error loading subject details. Please try again.');
        }

        return view('teacher.subjects.show', compact('subject', 'subjectStats', 'recentActivities'));
    }
}