<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Grade;
use App\Models\Student;
use App\Models\Subject;
use App\Models\ClassRoom;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class GradeManagementController extends Controller
{
    protected $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->middleware('teacher');
        $this->notificationService = $notificationService;
    }

    public function index(Request $request)
    {
        $teacher = $request->user()->teacher;
        
        $query = Grade::where('teacher_id', $teacher->id)
            ->with(['student.user', 'subject', 'class']);

        // Filter by class
        if ($request->filled('class_id')) {
            $query->where('class_id', $request->class_id);
        }

        // Filter by subject
        if ($request->filled('subject_id')) {
            $query->where('subject_id', $request->subject_id);
        }

        // Filter by academic year
        if ($request->filled('academic_year')) {
            $query->where('academic_year', $request->academic_year);
        }

        $grades = $query->orderBy('created_at', 'desc')->paginate(15);
        
        $classes = $teacher->classes;
        $subjects = $teacher->subjects;
        $academicYears = Grade::distinct()->pluck('academic_year')->sort();

        return view('teacher.grades.index', compact('grades', 'classes', 'subjects', 'academicYears'));
    }

    public function create(Request $request)
    {
        $teacher = $request->user()->teacher;
        $selectedClassId = $request->get('class_id');
        $selectedSubjectId = $request->get('subject_id');

        $classes = $teacher->classes;
        $subjects = $teacher->subjects;

        $students = collect();
        if ($selectedClassId && $selectedSubjectId) {
            $students = Student::where('class_id', $selectedClassId)
                ->whereHas('subjects', function($q) use ($selectedSubjectId) {
                    $q->where('subjects.id', $selectedSubjectId);
                })
                ->with('user')
                ->get();
        }

        return view('teacher.grades.create', compact('classes', 'subjects', 'students', 'selectedClassId', 'selectedSubjectId'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'student_id' => 'required|exists:students,id',
            'class_id' => 'required|exists:class_rooms,id',
            'subject_id' => 'required|exists:subjects,id',
            'academic_year' => 'required|integer',
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

        $teacher = $request->user()->teacher;

        // Check if grade already exists
        $existingGrade = Grade::where('student_id', $request->student_id)
            ->where('class_id', $request->class_id)
            ->where('subject_id', $request->subject_id)
            ->where('academic_year', $request->academic_year)
            ->first();

        if ($existingGrade) {
            return back()->withErrors(['error' => 'Grade already exists for this student, subject, and academic year.']);
        }

        $grade = Grade::create([
            'student_id' => $request->student_id,
            'class_id' => $request->class_id,
            'subject_id' => $request->subject_id,
            'teacher_id' => $teacher->id,
            'academic_year' => $request->academic_year,
            'semester' => $request->semester,
            'sem1_p1' => $request->sem1_p1,
            'sem1_p2' => $request->sem1_p2,
            'sem1_p3' => $request->sem1_p3,
            'sem1_exam' => $request->sem1_exam,
            'sem2_p4' => $request->sem2_p4,
            'sem2_p5' => $request->sem2_p5,
            'sem2_p6' => $request->sem2_p6,
            'sem2_exam' => $request->sem2_exam,
            'status' => 'pending'
        ]);

        // Calculate averages
        $grade->calculateSemesterAverages();
        $grade->save();

        // Send notification to student
        $student = $grade->student;
        $this->notificationService->sendEmailNotification(
            $student->user,
            'Grade Published',
            "Your grade for {$grade->subject->name} has been published. Check your dashboard for details.",
            'grade_published'
        );

        return redirect()->route('teacher.grades.index')
            ->with('success', 'Grade submitted successfully.');
    }

    public function edit(Grade $grade)
    {
        $this->authorize('update', $grade);
        
        $grade->load(['student.user', 'subject', 'class']);
        return view('teacher.grades.edit', compact('grade'));
    }

    public function update(Request $request, Grade $grade)
    {
        $this->authorize('update', $grade);

        $request->validate([
            'sem1_p1' => 'nullable|numeric|min:0|max:100',
            'sem1_p2' => 'nullable|numeric|min:0|max:100',
            'sem1_p3' => 'nullable|numeric|min:0|max:100',
            'sem1_exam' => 'nullable|numeric|min:0|max:100',
            'sem2_p4' => 'nullable|numeric|min:0|max:100',
            'sem2_p5' => 'nullable|numeric|min:0|max:100',
            'sem2_p6' => 'nullable|numeric|min:0|max:100',
            'sem2_exam' => 'nullable|numeric|min:0|max:100',
        ]);

        $grade->update($request->only([
            'sem1_p1', 'sem1_p2', 'sem1_p3', 'sem1_exam',
            'sem2_p4', 'sem2_p5', 'sem2_p6', 'sem2_exam'
        ]));

        // Recalculate averages
        $grade->calculateSemesterAverages();
        $grade->save();

        return redirect()->route('teacher.grades.index')
            ->with('success', 'Grade updated successfully.');
    }

    public function bulkCreate(Request $request)
    {
        $teacher = $request->user()->teacher;
        $classId = $request->get('class_id');
        $subjectId = $request->get('subject_id');
        $academicYear = $request->get('academic_year', date('Y'));

        if (!$classId || !$subjectId) {
            return back()->withErrors(['error' => 'Please select class and subject.']);
        }

        $students = Student::where('class_id', $classId)
            ->whereHas('subjects', function($q) use ($subjectId) {
                $q->where('subjects.id', $subjectId);
            })
            ->with('user')
            ->get();

        $subject = Subject::find($subjectId);
        $class = ClassRoom::find($classId);

        return view('teacher.grades.bulk-create', compact('students', 'subject', 'class', 'academicYear'));
    }

    public function bulkStore(Request $request)
    {
        $request->validate([
            'class_id' => 'required|exists:class_rooms,id',
            'subject_id' => 'required|exists:subjects,id',
            'academic_year' => 'required|integer',
            'semester' => 'required|integer|in:1,2',
            'grades' => 'required|array',
            'grades.*.student_id' => 'required|exists:students,id',
        ]);

        $teacher = $request->user()->teacher;
        $created = 0;
        $updated = 0;

        foreach ($request->grades as $gradeData) {
            if (empty($gradeData['student_id'])) continue;

            $existingGrade = Grade::where('student_id', $gradeData['student_id'])
                ->where('class_id', $request->class_id)
                ->where('subject_id', $request->subject_id)
                ->where('academic_year', $request->academic_year)
                ->first();

            $gradeData = array_merge($gradeData, [
                'class_id' => $request->class_id,
                'subject_id' => $request->subject_id,
                'teacher_id' => $teacher->id,
                'academic_year' => $request->academic_year,
                'semester' => $request->semester,
                'status' => 'pending'
            ]);

            if ($existingGrade) {
                $existingGrade->update($gradeData);
                $existingGrade->calculateSemesterAverages();
                $existingGrade->save();
                $updated++;
            } else {
                $grade = Grade::create($gradeData);
                $grade->calculateSemesterAverages();
                $grade->save();
                $created++;
            }
        }

        return redirect()->route('teacher.grades.index')
            ->with('success', "Grades processed: {$created} created, {$updated} updated.");
    }

    public function approve(Grade $grade)
    {
        $this->authorize('approve', $grade);

        $grade->update([
            'status' => 'approved',
            'approved_by' => auth()->id(),
            'approved_at' => now()
        ]);

        return back()->with('success', 'Grade approved successfully.');
    }

    public function reject(Request $request, Grade $grade)
    {
        $this->authorize('approve', $grade);

        $request->validate([
            'rejection_reason' => 'required|string|max:500'
        ]);

        $grade->update([
            'status' => 'rejected',
            'approved_by' => auth()->id(),
            'approved_at' => now(),
            'rejection_reason' => $request->rejection_reason
        ]);

        return back()->with('success', 'Grade rejected successfully.');
    }

    public function getStudents(Request $request)
    {
        $classId = $request->get('class_id');
        $subjectId = $request->get('subject_id');

        if (!$classId || !$subjectId) {
            return response()->json([]);
        }

        $students = Student::where('class_id', $classId)
            ->whereHas('subjects', function($q) use ($subjectId) {
                $q->where('subjects.id', $subjectId);
            })
            ->with('user')
            ->get()
            ->map(function($student) {
                return [
                    'id' => $student->id,
                    'name' => $student->user->name,
                    'student_id' => $student->student_id
                ];
            });

        return response()->json($students);
    }

    public function export(Request $request)
    {
        $teacher = $request->user()->teacher;
        
        $query = Grade::where('teacher_id', $teacher->id)
            ->with(['student.user', 'subject', 'class']);

        if ($request->filled('class_id')) {
            $query->where('class_id', $request->class_id);
        }

        if ($request->filled('subject_id')) {
            $query->where('subject_id', $request->subject_id);
        }

        $grades = $query->get();

        // This would typically export to Excel or PDF
        // For now, return JSON response
        return response()->json([
            'grades' => $grades,
            'message' => 'Export functionality would be implemented here'
        ]);
    }
}
