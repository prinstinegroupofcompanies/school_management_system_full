<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Grade;
use App\Models\Student;
use App\Models\Subject;
use App\Models\ClassRoom;
use App\Models\Teacher;
use App\Events\GradeSubmitted;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class GradeController extends Controller
{
    public function __construct()
    {
        $this->middleware('teacher');
    }

    /**
     * Display teacher's grades with filtering
     */
    public function index(Request $request)
    {
        $teacher = $request->user()->teacher;
        
        if (!$teacher) {
            return redirect()->route('teacher.dashboard')
                           ->withErrors(['error' => 'Teacher profile not found.']);
        }
        
        // Get teacher's assigned subjects and classes
        $subjects = Subject::where('teacher_id', $teacher->id)->get();
        $classes = ClassRoom::whereHas('subjects', function($query) use ($teacher) {
            $query->where('teacher_id', $teacher->id);
        })->get();
        
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
        
        if ($request->filled('status')) {
            $gradesQuery->where('status', $request->status);
        }
        
        $grades = $gradesQuery->orderBy('created_at', 'desc')->paginate(15);
        
        // Get summary statistics
        $stats = [
            'total_grades' => Grade::where('teacher_id', $teacher->id)->count(),
            'pending_grades' => Grade::where('teacher_id', $teacher->id)->where('status', 'pending')->count(),
            'approved_grades' => Grade::where('teacher_id', $teacher->id)->where('status', 'approved')->count(),
            'rejected_grades' => Grade::where('teacher_id', $teacher->id)->where('status', 'rejected')->count(),
        ];
        
        return view('teacher.grades.index', compact('grades', 'classes', 'subjects', 'stats'));
    }

    /**
     * Show form for creating new grade entry
     */
    public function create(Request $request)
    {
        $teacher = $request->user()->teacher;
        
        if (!$teacher) {
            return redirect()->route('teacher.dashboard')
                           ->withErrors(['error' => 'Teacher profile not found.']);
        }
        
        // Get teacher's assigned subjects and classes
        $subjects = Subject::where('teacher_id', $teacher->id)->get();
        $classes = ClassRoom::whereHas('subjects', function($query) use ($teacher) {
            $query->where('teacher_id', $teacher->id);
        })->get();
        
        $selectedClassId = $request->get('class_id');
        $selectedSubjectId = $request->get('subject_id');
        
        // Get students if class and subject are selected
        $students = collect();
        if ($selectedClassId && $selectedSubjectId) {
            $students = Student::where('class_id', $selectedClassId)
                              ->with(['user', 'classRoom'])
                              ->get();
        }
        
        return view('teacher.grades.create', compact(
            'classes', 'subjects', 'students', 'selectedClassId', 'selectedSubjectId'
        ));
    }
    
    /**
     * Store grades for selected students
     */
    public function store(Request $request)
    {
        $teacher = $request->user()->teacher;
        
        if (!$teacher) {
            return redirect()->route('teacher.dashboard')
                           ->withErrors(['error' => 'Teacher profile not found.']);
        }

        $validated = $request->validate([
            'class_id' => 'required|exists:class_rooms,id',
            'subject_id' => 'required|exists:subjects,id',
            'students' => 'required|array|min:1',
            'students.*.student_id' => 'required|exists:students,id',
            'students.*.sem1_p1' => 'nullable|numeric|min:0|max:100',
            'students.*.sem1_p2' => 'nullable|numeric|min:0|max:100',
            'students.*.sem1_p3' => 'nullable|numeric|min:0|max:100',
            'students.*.sem1_exam' => 'nullable|numeric|min:0|max:100',
            'students.*.sem2_p4' => 'nullable|numeric|min:0|max:100',
            'students.*.sem2_p5' => 'nullable|numeric|min:0|max:100',
            'students.*.sem2_p6' => 'nullable|numeric|min:0|max:100',
            'students.*.sem2_exam' => 'nullable|numeric|min:0|max:100',
        ]);

        // Verify teacher has permission to grade this subject
        $subject = Subject::where('id', $validated['subject_id'])
                         ->where('teacher_id', $teacher->id)
                         ->first();

        if (!$subject) {
            return back()->withErrors(['subject_id' => 'You are not authorized to grade this subject.'])
                        ->withInput();
        }

        DB::beginTransaction();
        try {
            $successCount = 0;
            $errors = [];

            foreach ($validated['students'] as $studentData) {
                try {
                    // Check if grade already exists for this student/subject/class combination
                    $existingGrade = Grade::where('student_id', $studentData['student_id'])
                                        ->where('class_id', $validated['class_id'])
                                        ->where('subject_id', $validated['subject_id'])
                                        ->first();

                    if ($existingGrade) {
                        // Update existing grade
                        $existingGrade->update([
                            'academic_year' => date('Y'),
                            'semester' => 1,
                            'sem1_p1' => $studentData['sem1_p1'] ?? null,
                            'sem1_p2' => $studentData['sem1_p2'] ?? null,
                            'sem1_p3' => $studentData['sem1_p3'] ?? null,
                            'sem1_exam' => $studentData['sem1_exam'] ?? null,
                            'sem2_p4' => $studentData['sem2_p4'] ?? null,
                            'sem2_p5' => $studentData['sem2_p5'] ?? null,
                            'sem2_p6' => $studentData['sem2_p6'] ?? null,
                            'sem2_exam' => $studentData['sem2_exam'] ?? null,
                            'status' => 'pending',
                        ]);
                        $existingGrade->calculateSemesterAverages();
                        $existingGrade->save();
                        
                        // Fire grade submitted event for updated grade
                        event(new GradeSubmitted($existingGrade));
                    } else {
                        // Create new grade
                        $grade = Grade::create([
                            'student_id' => $studentData['student_id'],
                            'class_id' => $validated['class_id'],
                            'subject_id' => $validated['subject_id'],
                            'teacher_id' => $teacher->id,
                            'academic_year' => date('Y'),
                            'semester' => 1,
                            'sem1_p1' => $studentData['sem1_p1'] ?? null,
                            'sem1_p2' => $studentData['sem1_p2'] ?? null,
                            'sem1_p3' => $studentData['sem1_p3'] ?? null,
                            'sem1_exam' => $studentData['sem1_exam'] ?? null,
                            'sem2_p4' => $studentData['sem2_p4'] ?? null,
                            'sem2_p5' => $studentData['sem2_p5'] ?? null,
                            'sem2_p6' => $studentData['sem2_p6'] ?? null,
                            'sem2_exam' => $studentData['sem2_exam'] ?? null,
                            'status' => 'pending',
                        ]);
                        $grade->calculateSemesterAverages();
                        $grade->save();
                        
                        // Fire grade submitted event
                        event(new GradeSubmitted($grade));
                    }

                    $successCount++;

                } catch (\Exception $e) {
                    $student = Student::find($studentData['student_id']);
                    $errors[] = "Failed to save grade for {$student->user->name}: {$e->getMessage()}";
                }
            }

            DB::commit();

            $message = "Successfully saved grades for {$successCount} students.";
            if (!empty($errors)) {
                $message .= " Errors: " . implode(', ', $errors);
            }

            return redirect()->route('teacher.grades.index')
                           ->with('success', $message);

        } catch (\Exception $e) {
            DB::rollback();
            return back()->withErrors(['error' => 'Failed to save grades: ' . $e->getMessage()])
                        ->withInput();
        }
    }

    /**
     * Show specific grade
     */
    public function show(Grade $grade)
    {
        $teacher = auth()->user()->teacher;
        
        // Verify teacher owns this grade
        if ($grade->teacher_id !== $teacher->id) {
            return redirect()->route('teacher.grades.index')
                           ->withErrors(['error' => 'You are not authorized to view this grade.']);
        }

        $grade->load(['student.user', 'subject', 'class', 'approvedBy']);
        
        return view('teacher.grades.show', compact('grade'));
    }

    /**
     * Show form for editing grade (only pending grades)
     */
    public function edit(Grade $grade)
    {
        $teacher = auth()->user()->teacher;
        
        // Verify teacher owns this grade
        if ($grade->teacher_id !== $teacher->id) {
            return redirect()->route('teacher.grades.index')
                           ->withErrors(['error' => 'You are not authorized to edit this grade.']);
        }

        // Only allow editing of pending grades
        if ($grade->status !== 'pending') {
            return redirect()->route('teacher.grades.show', $grade)
                           ->withErrors(['error' => 'Cannot edit grades that have been approved or rejected.']);
        }

        $subjects = Subject::where('teacher_id', $teacher->id)->get();
        $classes = ClassRoom::whereHas('subjects', function($query) use ($teacher) {
            $query->where('teacher_id', $teacher->id);
        })->get();
        
        $students = Student::where('class_id', $grade->class_id)->with(['user', 'classRoom'])->get();

        return view('teacher.grades.edit', compact('grade', 'classes', 'subjects', 'students'));
    }

    /**
     * Update the specified grade
     */
    public function update(Request $request, Grade $grade)
    {
        $teacher = auth()->user()->teacher;
        
        // Verify teacher owns this grade
        if ($grade->teacher_id !== $teacher->id) {
            return redirect()->route('teacher.grades.index')
                           ->withErrors(['error' => 'You are not authorized to update this grade.']);
        }

        // Only allow updating of pending grades
        if ($grade->status !== 'pending') {
            return redirect()->route('teacher.grades.show', $grade)
                           ->withErrors(['error' => 'Cannot update grades that have been approved or rejected.']);
        }

        $validated = $request->validate([
            'sem1_p1' => 'nullable|numeric|min:0|max:100',
            'sem1_p2' => 'nullable|numeric|min:0|max:100',
            'sem1_p3' => 'nullable|numeric|min:0|max:100',
            'sem1_exam' => 'nullable|numeric|min:0|max:100',
            'sem2_p4' => 'nullable|numeric|min:0|max:100',
            'sem2_p5' => 'nullable|numeric|min:0|max:100',
            'sem2_p6' => 'nullable|numeric|min:0|max:100',
            'sem2_exam' => 'nullable|numeric|min:0|max:100',
        ]);

        DB::beginTransaction();
        try {
            $grade->update($validated);
            $grade->calculateSemesterAverages();
            $grade->save();

            DB::commit();

            return redirect()->route('teacher.grades.show', $grade)
                           ->with('success', 'Grade updated successfully.');

        } catch (\Exception $e) {
            DB::rollback();
            return back()->withErrors(['error' => 'Failed to update grade: ' . $e->getMessage()])
                        ->withInput();
        }
    }

    /**
     * Get subjects for a specific class (AJAX)
     */
    public function getSubjects(Request $request)
    {
        $teacher = $request->user()->teacher;
        
        $request->validate([
            'class_id' => 'required|exists:class_rooms,id',
        ]);

        // Get subjects that the teacher teaches in the selected class
        $subjects = Subject::where('teacher_id', $teacher->id)
                          ->whereHas('classes', function($query) use ($request) {
                              $query->where('class_rooms.id', $request->class_id);
                          })
                          ->get()
                          ->map(function ($subject) {
                              return [
                                  'id' => $subject->id,
                                  'name' => $subject->name,
                                  'code' => $subject->code,
                              ];
                          });

        return response()->json(['subjects' => $subjects]);
    }

    /**
     * Get students for a specific class and subject (AJAX)
     */
    public function getStudents(Request $request)
    {
        $teacher = $request->user()->teacher;
        
        $request->validate([
            'class_id' => 'required|exists:class_rooms,id',
            'subject_id' => 'required|exists:subjects,id',
        ]);

        // Verify teacher has permission for this subject
        $subject = Subject::where('id', $request->subject_id)
                         ->where('teacher_id', $teacher->id)
                         ->first();

        if (!$subject) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $students = Student::where('class_id', $request->class_id)
                          ->with(['user', 'classRoom'])
                          ->get()
                          ->map(function ($student) {
                              return [
                                  'id' => $student->id,
                                  'name' => $student->user->name,
                                  'admission_number' => $student->admission_number,
                              ];
                          });

        return response()->json($students);
    }

    /**
     * Bulk grade entry for multiple students
     */
    public function bulkCreate(Request $request)
    {
        $teacher = $request->user()->teacher;
        
        if (!$teacher) {
            return redirect()->route('teacher.dashboard')
                           ->withErrors(['error' => 'Teacher profile not found.']);
        }

        $subjects = Subject::where('teacher_id', $teacher->id)->get();
        $classes = ClassRoom::whereHas('subjects', function($query) use ($teacher) {
            $query->where('teacher_id', $teacher->id);
        })->get();

        $selectedClassId = $request->get('class_id');
        $selectedSubjectId = $request->get('subject_id');
        
        $students = collect();
        if ($selectedClassId && $selectedSubjectId) {
            $students = Student::where('class_id', $selectedClassId)
                              ->with(['user', 'classRoom'])
                              ->get();
        }

        return view('teacher.grades.bulk-create', compact(
            'classes', 'subjects', 'students', 'selectedClassId', 'selectedSubjectId'
        ));
    }

    /**
     * Delete grade (only pending grades)
     */
    public function destroy(Grade $grade)
    {
        $teacher = auth()->user()->teacher;
        
        if ($grade->teacher_id !== $teacher->id) {
            return back()->withErrors(['error' => 'Unauthorized']);
        }

        if ($grade->status !== 'pending') {
            return back()->withErrors(['error' => 'Only pending grades can be deleted.']);
        }

        $grade->delete();

        return redirect()->route('teacher.grades.index')
                       ->with('success', 'Grade deleted successfully.');
    }
}