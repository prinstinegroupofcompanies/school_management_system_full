<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Grade;
use App\Models\Student;
use App\Models\Subject;
use Illuminate\Validation\Rule;
use Illuminate\Http\Request;

class GradeController extends Controller
{
    public function index(Request $request)
    {
        $teacher = $request->user()->teacher;
        $grades = Grade::where('teacher_id', $teacher->id)
            ->with(['student.user','subject'])
            ->latest()->paginate(15);
        return view('teacher.grades.index', compact('grades'));
    }

    public function create(Request $request)
    {
        $teacher = $request->user()->teacher;
        $selectedClassId = $request->get('class_id');
        $selectedSubjectId = $request->get('subject_id');

        $classes = $teacher->classes()->get();
        $subjectsQuery = $teacher->subjects();
        if ($selectedClassId) {
            $subjectsQuery->where('class_id', $selectedClassId);
        }
        $subjects = $subjectsQuery->get();

        $students = collect();
        if ($selectedClassId && $selectedSubjectId) {
            // Students enrolled in selected class AND enrolled in the selected subject
            $students = Student::where('class_id', $selectedClassId)
                ->whereHas('subjects', function($q) use ($selectedSubjectId) {
                    $q->where('subjects.id', $selectedSubjectId);
                })
                ->with('user','class')
                ->get();
        } elseif ($selectedClassId) {
            // Fallback: all students in class
            $students = Student::where('class_id', $selectedClassId)
                ->with('user','class')
                ->get();
        }

        return view('teacher.grades.create', [
            'classes' => $classes,
            'subjects' => $subjects,
            'students' => $students,
            'selectedClassId' => $selectedClassId,
            'selectedSubjectId' => $selectedSubjectId,
        ]);
    }

    public function store(Request $request)
    {
        $teacher = $request->user()->teacher;
        $request->validate([
            'student_id' => [
                'required',
                Rule::exists('students','id')->where(fn($q) => $q->where('class_id', $request->class_id)),
                // Ensure student is enrolled in the selected subject (real-time data)
                Rule::exists('student_subject','student_id')->where(fn($q) => $q->where('subject_id', $request->subject_id)),
            ],
            'class_id' => ['required', Rule::exists('class_rooms','id')],
            'subject_id' => ['required', Rule::exists('subjects','id')->where(fn($q) => $q->where('class_id', $request->class_id)->where('teacher_id', $teacher->id))],
            'academic_year' => 'required|integer|min:2000|max:2100',
            'semester' => 'required|integer|in:1,2',
            'sem1_p1' => 'nullable|numeric|min:0|max:100',
            'sem1_p2' => 'nullable|numeric|min:0|max:100',
            'sem1_p3' => 'nullable|numeric|min:0|max:100',
            'sem1_exam' => 'nullable|numeric|min:0|max:100',
            'sem2_p4' => 'nullable|numeric|min:0|max:100',
            'sem2_p5' => 'nullable|numeric|min:0|max:100',
            'sem2_p6' => 'nullable|numeric|min:0|max:100',
            'sem2_exam' => 'nullable|numeric|min:0|max:100',
        ]);

        Grade::updateOrCreate(
            [
                'student_id' => $request->student_id,
                'class_id' => $request->class_id,
                'subject_id' => $request->subject_id,
                'academic_year' => $request->academic_year,
            ],
            array_merge(
                $request->only(['sem1_p1','sem1_p2','sem1_p3','sem1_exam','sem2_p4','sem2_p5','sem2_p6','sem2_exam','semester']),
                ['teacher_id' => $teacher->id, 'status' => 'pending']
            )
        );

        return redirect()->route('teacher.grades.index')->with('success','Grades saved as pending and sent for approval.');
    }
}


