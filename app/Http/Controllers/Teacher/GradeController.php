<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Grade;
use App\Models\Student;
use App\Models\Subject;
use App\Models\ExamPaper;
use Illuminate\Validation\Rule;
use Illuminate\Http\Request;

class GradeController extends Controller
{
    public function index(Request $request)
    {
        $teacher = $request->user()->teacher;
        
        // Get classes and subjects for filters
        $classes = $teacher->classes()->get();
        $subjects = $teacher->subjects()->get();
        
        // Get academic years from grades
        $academicYears = Grade::where('teacher_id', $teacher->id)
            ->distinct()
            ->pluck('academic_year')
            ->sort()
            ->values();
        
        // Build grades query with filters
        $gradesQuery = Grade::where('teacher_id', $teacher->id)
            ->with(['student.user', 'subject', 'class']);
            
        // Apply filters
        if ($request->filled('class_id')) {
            $gradesQuery->where('class_id', $request->class_id);
        }
        
        if ($request->filled('subject_id')) {
            $gradesQuery->where('subject_id', $request->subject_id);
        }
        
        if ($request->filled('academic_year')) {
            $gradesQuery->where('academic_year', $request->academic_year);
        }
        
        $grades = $gradesQuery->latest()->paginate(15);
        
        return view('teacher.grades.index', compact('grades', 'classes', 'subjects', 'academicYears'));
    }

    public function create(Request $request)
    {
        $teacher = $request->user()->teacher;
        $selectedClassId = $request->get('class_id');
        $selectedSubjectId = $request->get('subject_id');
        $selectedPeriodId = $request->get('academic_period_id');

        $classes = $teacher->classes()->get();
        $subjectsQuery = $teacher->subjects();
        if ($selectedClassId) {
            $subjectsQuery->where('class_id', $selectedClassId);
        }
        $subjects = $subjectsQuery->get();
        
        // Get academic periods
        $academicPeriods = \App\Models\AcademicPeriod::currentYear()->orderBy('name')->get();

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
            'academicPeriods' => $academicPeriods,
            'students' => $students,
            'selectedClassId' => $selectedClassId,
            'selectedSubjectId' => $selectedSubjectId,
            'selectedPeriodId' => $selectedPeriodId,
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
            'academic_period_id' => 'required|exists:academic_periods,id',
            'period_1' => 'nullable|numeric|min:0|max:100',
            'period_2' => 'nullable|numeric|min:0|max:100',
            'period_3' => 'nullable|numeric|min:0|max:100',
            'period_4' => 'nullable|numeric|min:0|max:100',
            'period_5' => 'nullable|numeric|min:0|max:100',
            'period_6' => 'nullable|numeric|min:0|max:100',
            'exam' => 'nullable|numeric|min:0|max:100',
        ]);

        $grade = Grade::updateOrCreate(
            [
                'student_id' => $request->student_id,
                'class_id' => $request->class_id,
                'subject_id' => $request->subject_id,
                'academic_year' => $request->academic_year,
                'academic_period_id' => $request->academic_period_id,
            ],
            array_merge(
                $request->only(['period_1','period_2','period_3','period_4','period_5','period_6','exam']),
                ['teacher_id' => $teacher->id, 'status' => 'pending']
            )
        );
        
        // Calculate period average
        $grade->calculatePeriodAverage();
        $grade->save();

        return redirect()->route('teacher.grades.index')->with('success','Grades saved as pending and sent for approval.');
    }

    public function getSubjects(Request $request)
    {
        $user = $request->user();
        if (!$user) {
            return response()->json(['subjects' => []]);
        }
        
        $teacher = $user->teacher;
        if (!$teacher) {
            return response()->json(['subjects' => []]);
        }
        
        $classId = $request->get('class_id');
        
        if (!$classId) {
            return response()->json(['subjects' => []]);
        }
        
        $subjects = $teacher->subjects()
            ->where('class_id', $classId)
            ->get(['id', 'name']);
            
        return response()->json(['subjects' => $subjects]);
    }

    public function getEligibleStudents(Request $request)
    {
        $classId = $request->get('class_id');
        $subjectId = $request->get('subject_id');
        
        if (!$classId || !$subjectId) {
            return response()->json(['data' => []]);
        }
        
        $students = Student::where('class_id', $classId)
            ->whereHas('subjects', function($q) use ($subjectId) {
                $q->where('subjects.id', $subjectId);
            })
            ->with(['user', 'class'])
            ->get()
            ->map(function($student) {
                return [
                    'id' => $student->id,
                    'name' => $student->user->name,
                    'class' => $student->class->name ?? 'Unknown'
                ];
            });
            
        return response()->json(['data' => $students]);
    }

    public function examQuestions(Request $request)
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

        $examPapers = ExamPaper::where('teacher_id', $teacher->id)
            ->with(['classRoom', 'subject'])
            ->when($selectedClassId, function($query) use ($selectedClassId) {
                return $query->where('class_id', $selectedClassId);
            })
            ->when($selectedSubjectId, function($query) use ($selectedSubjectId) {
                return $query->where('subject_id', $selectedSubjectId);
            })
            ->latest()
            ->paginate(15);

        return view('teacher.grades.exam-questions', compact(
            'classes', 
            'subjects', 
            'examPapers',
            'selectedClassId',
            'selectedSubjectId'
        ));
    }

    public function createExamQuestions(Request $request)
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

        return view('teacher.grades.create-exam-questions', compact(
            'classes', 
            'subjects',
            'selectedClassId',
            'selectedSubjectId'
        ));
    }

    public function storeExamQuestions(Request $request)
    {
        $teacher = $request->user()->teacher;
        
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'class_id' => 'required|exists:class_rooms,id',
            'subject_id' => 'required|exists:subjects,id',
            'total_marks' => 'required|integer|min:1',
            'duration_minutes' => 'required|integer|min:1',
            'exam_date' => 'nullable|date|after:now',
            'questions' => 'required|array|min:1',
            'questions.*.question' => 'required|string',
            'questions.*.type' => 'required|in:multiple_choice,short_answer,essay',
            'questions.*.marks' => 'required|integer|min:1',
            'questions.*.options' => 'required_if:questions.*.type,multiple_choice|array',
            'questions.*.correct_answer' => 'required|string',
        ]);

        $examPaper = ExamPaper::create([
            'teacher_id' => $teacher->id,
            'class_id' => $request->class_id,
            'subject_id' => $request->subject_id,
            'title' => $request->title,
            'description' => $request->description,
            'total_marks' => $request->total_marks,
            'duration_minutes' => $request->duration_minutes,
            'questions' => $request->questions,
            'exam_date' => $request->exam_date,
            'status' => 'draft'
        ]);

        return redirect()->route('teacher.grades.exam-questions')
            ->with('success', 'Exam questions created successfully!');
    }

    public function publishExam(ExamPaper $examPaper)
    {
        $examPaper->update(['status' => 'published']);
        
        return redirect()->back()
            ->with('success', 'Exam published successfully!');
    }

    public function sendExamToClass(ExamPaper $examPaper)
    {
        // Get all students in the class
        $students = Student::where('class_id', $examPaper->class_id)
            ->with('user')
            ->get();

        // Here you would typically send notifications to students
        // For now, we'll just update the status
        $examPaper->update(['status' => 'published']);

        return redirect()->back()
            ->with('success', "Exam sent to {$students->count()} students in {$examPaper->classRoom->name}!");
    }
}


