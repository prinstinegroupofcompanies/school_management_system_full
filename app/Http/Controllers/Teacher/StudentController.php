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

            // Get students from classes assigned to this teacher (both methods)
            $pivotClasses = $teacher->classes()->pluck('class_rooms.id');
            $directClasses = \App\Models\ClassRoom::where('class_teacher_id', $teacher->id)->pluck('id');
            $teacherClasses = $pivotClasses->merge($directClasses)->unique();
            
            \Log::info('Teacher student index - Teacher ID: ' . $teacher->id);
            \Log::info('Teacher student index - Pivot classes: ' . $pivotClasses->toJson());
            \Log::info('Teacher student index - Direct classes: ' . $directClasses->toJson());
            \Log::info('Teacher student index - Combined classes: ' . $teacherClasses->toJson());
            
            $students = \App\Models\Student::whereIn('class_id', $teacherClasses)
                ->with(['user', 'classRoom'])
                ->orderBy('created_at', 'desc')
                ->paginate(15);
                
            \Log::info('Teacher student index - Found ' . $students->count() . ' students');

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
            // Use both assignment methods (pivot table and direct foreign key)
            $pivotClasses = $teacher->classes()->pluck('class_rooms.id');
            $directClasses = \App\Models\ClassRoom::where('class_teacher_id', $teacher->id)->pluck('id');
            $teacherClasses = $pivotClasses->merge($directClasses)->unique();
            
            \Log::info('Teacher student show - Teacher ID: ' . $teacher->id);
            \Log::info('Teacher student show - Pivot classes: ' . $pivotClasses->toJson());
            \Log::info('Teacher student show - Direct classes: ' . $directClasses->toJson());
            \Log::info('Teacher student show - Combined classes: ' . $teacherClasses->toJson());
            \Log::info('Teacher student show - Looking for student ID: ' . $id);
            
            $student = \App\Models\Student::where('id', $id)
                ->whereIn('class_id', $teacherClasses)
                ->with(['user', 'classRoom', 'grades'])
                ->first();

            if (!$student) {
                \Log::error('Student not found - Student ID: ' . $id . ', Teacher classes: ' . $teacherClasses->toJson());
                
                // Let's also check if the student exists at all
                $studentExists = \App\Models\Student::where('id', $id)->first();
                if ($studentExists) {
                    \Log::error('Student exists but not in teacher classes - Student class: ' . $studentExists->class_id);
                } else {
                    \Log::error('Student does not exist at all');
                }
                
                return redirect()->route('teacher.students.index')
                    ->with('error', 'Student not found or you do not have permission to view them.');
            }
            
            \Log::info('Student found: ' . $student->first_name . ' ' . $student->last_name);

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