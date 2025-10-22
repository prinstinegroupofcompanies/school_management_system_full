<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ClassController extends Controller
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

            // Get classes assigned to this teacher (both methods)
            $pivotClasses = $teacher->classes()->with(['students', 'subjects'])->get();
            $directClasses = \App\Models\ClassRoom::where('class_teacher_id', $teacher->id)
                ->with(['students', 'subjects'])
                ->get();
            $classes = $pivotClasses->merge($directClasses)->unique('id')->sortBy('name');

            // Get class statistics
            $stats = [
                'total_classes' => $classes->count(),
                'total_students' => $classes->sum(function($class) {
                    return $class->students->count();
                }),
                'average_students' => $classes->count() > 0 
                    ? round($classes->sum(function($class) { return $class->students->count(); }) / $classes->count(), 1)
                    : 0,
                'subjects_taught' => $classes->flatMap->subjects->unique('id')->count()
            ];

        } catch (\Exception $e) {
            // Fallback data if database queries fail
            $classes = collect();
            $stats = [
                'total_classes' => 0,
                'total_students' => 0,
                'average_students' => 0,
                'subjects_taught' => 0
            ];
        }

        return view('teacher.classes.index', compact('classes', 'stats'));
    }

    public function show($id)
    {
        try {
            $user = auth()->user();
            $teacher = $user->teacher;
            
            if (!$teacher) {
                return redirect()->route('teacher.classes.index')
                    ->with('error', 'Teacher profile not found. Please contact administrator.');
            }

            // Get the specific class (ensure it belongs to this teacher using both methods)
            $pivotClasses = $teacher->classes()->where('class_rooms.id', $id)->get();
            $directClasses = \App\Models\ClassRoom::where('id', $id)
                ->where('class_teacher_id', $teacher->id)
                ->get();
            
            $class = $pivotClasses->merge($directClasses)->unique('id')->first();

            if (!$class) {
                return redirect()->route('teacher.classes.index')
                    ->with('error', 'Class not found or you do not have permission to view it.');
            }

            // Load relationships
            $class->load(['students.user', 'subjects', 'teachers.user']);

            // Get recent activities for this class
            $recentActivities = collect([
                ['description' => 'Class created', 'created_at' => $class->created_at],
                ['description' => 'Students enrolled: ' . $class->students->count(), 'created_at' => $class->updated_at],
            ]);

        } catch (\Exception $e) {
            return redirect()->route('teacher.classes.index')
                ->with('error', 'Error loading class details. Please try again.');
        }

        return view('teacher.classes.show', compact('class', 'recentActivities'));
    }
}