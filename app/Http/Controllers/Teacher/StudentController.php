<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class StudentController extends Controller
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

            // Get students from classes assigned to this teacher
            $teacherClasses = \App\Models\ClassRoom::where('teacher_id', $teacher->id)->pluck('id');
            
            $students = \App\Models\Student::whereIn('class_id', $teacherClasses)
                ->with(['user', 'classRoom'])
                ->orderBy('created_at', 'desc')
                ->paginate(15);

            // Get student statistics
            $stats = [
                'total_students' => $students->total(),
                'active_students' => $students->where('status', 'active')->count(),
                'classes_taught' => $teacherClasses->count(),
                'recent_enrollments' => \App\Models\Student::whereIn('class_id', $teacherClasses)
                    ->where('created_at', '>=', now()->subDays(30))
                    ->count()
            ];

        } catch (\Exception $e) {
            // Fallback data if database queries fail
            $students = new \Illuminate\Pagination\LengthAwarePaginator(
                collect(),
                0,
                15,
                1,
                ['path' => request()->url()]
            );
            $stats = [
                'total_students' => 0,
                'active_students' => 0,
                'classes_taught' => 0,
                'recent_enrollments' => 0
            ];
        }

        return view('teacher.students.index', compact('students', 'stats'));
    }

    public function show($id)
    {
        try {
            $user = auth()->user();
            $teacher = $user->teacher;
            
            if (!$teacher) {
                return redirect()->route('teacher.students.index')
                    ->with('error', 'Teacher profile not found. Please contact administrator.');
            }

            // Get the specific student (ensure they're in one of teacher's classes)
            $teacherClasses = \App\Models\ClassRoom::where('teacher_id', $teacher->id)->pluck('id');
            
            $student = \App\Models\Student::where('id', $id)
                ->whereIn('class_id', $teacherClasses)
                ->with(['user', 'classRoom', 'grades'])
                ->first();

            if (!$student) {
                return redirect()->route('teacher.students.index')
                    ->with('error', 'Student not found or you do not have permission to view them.');
            }

            // Get student's grades in subjects taught by this teacher
            $grades = \App\Models\Grade::where('student_id', $student->id)
                ->where('teacher_id', $teacher->id)
                ->with(['subject', 'class'])
                ->orderBy('created_at', 'desc')
                ->get();

            // Get recent activities for this student
            $recentActivities = collect([
                ['description' => 'Student enrolled', 'created_at' => $student->created_at],
                ['description' => 'Total grades recorded: ' . $grades->count(), 'created_at' => $student->updated_at],
            ]);

        } catch (\Exception $e) {
            return redirect()->route('teacher.students.index')
                ->with('error', 'Error loading student details. Please try again.');
        }

        return view('teacher.students.show', compact('student', 'grades', 'recentActivities'));
    }
}