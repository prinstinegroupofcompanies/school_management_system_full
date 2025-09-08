<?php

namespace App\Http\Controllers;

use App\Models\ExamType;
use App\Models\ExamSchedule;
use App\Models\ExamMark;
use App\Models\ExamQuestion;
use App\Models\ExamAttempt;
use App\Models\ExamAnswer;
use App\Services\ExamNotificationService;
use Illuminate\Support\Str;
use App\Models\ClassRoom;
use App\Models\Subject;
use App\Models\Student;
use App\Models\Teacher;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class ExamController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        
        // Check if user is a student
        if ($user->user_type === 'student' && $user->student) {
            $student = $user->student;
            
            // Get upcoming exams for the student's class
            $upcomingExams = ExamSchedule::with(['examType', 'class', 'subject', 'attempts' => function($query) use ($student) {
                $query->where('student_id', $student->id);
            }])
            ->where('class_id', $student->class_id)
            ->where('status', 'published')
            ->where('start_date', '>=', now()->toDateString())
            ->orderBy('start_date')
            ->get();
            
            // Get completed exams
            $completedExams = ExamSchedule::with(['examType', 'class', 'subject', 'attempts' => function($query) use ($student) {
                $query->where('student_id', $student->id);
            }])
            ->where('class_id', $student->class_id)
            ->where('status', 'published')
            ->where(function($query) {
                $query->where('start_date', '<', now()->toDateString())
                      ->orWhereHas('attempts', function($q) {
                          $q->where('status', 'submitted');
                      });
            })
            ->orderBy('start_date', 'desc')
            ->get();
            
            return view('student.exams.index', compact('upcomingExams', 'completedExams'));
        }
        
        // Admin/Teacher view
        // Ensure only First/Second Semester Exam types are available
        $requiredTypes = ['First Semester Exam', 'Second Semester Exam'];
        foreach ($requiredTypes as $name) {
            ExamType::updateOrCreate(['name' => $name], ['is_active' => true]);
        }
        $examTypes = ExamType::whereIn('name', $requiredTypes)->orderBy('name')->get();
        $examSchedules = ExamSchedule::with(['examType', 'class', 'subject'])->paginate(15);
        return view('exams.index', compact('examTypes', 'examSchedules'));
    }

    public function create()
    {
        // Allow teachers to create for their classes only
        $user = auth()->user();
        $teacher = $user->teacher;
        $examTypes = ExamType::all();
        $classes = $teacher ? $teacher->classes()->get() : ClassRoom::all();
        $subjects = $teacher ? Subject::whereIn('class_id', $classes->pluck('id'))->get() : Subject::all();
        return view('exams.create', compact('examTypes', 'classes', 'subjects'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'exam_type' => 'required|in:First Semester Exam,Second Semester Exam',
            'class_id' => 'required|exists:class_rooms,id',
            'subject_id' => 'required|exists:subjects,id',
            'exam_date' => 'required|date',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'total_marks' => 'required|numeric|min:1',
            'passing_marks' => 'required|numeric|min:1|lte:total_marks',
            'room_number' => 'nullable|string|max:255',
            'instructions' => 'nullable|string',
        ]);

        // Resolve or create exam type by name (ensure required code column populated)
        $typeName = $request->exam_type;
        $typeCode = $typeName === 'First Semester Exam' ? 'FIRST_SEM' : 'SECOND_SEM';
        $examType = ExamType::firstOrCreate(
            ['name' => $typeName],
            ['code' => $typeCode]
        );
        $payload = $request->only(['class_id','subject_id','exam_date','start_time','end_time','total_marks','passing_marks','room_number','instructions']);
        $payload['exam_type_id'] = $examType->id;
        $payload['is_live'] = false;
        // Map required fields expected by schema
        $class = ClassRoom::find($request->class_id);
        $subject = Subject::find($request->subject_id);
        $examDate = \Carbon\Carbon::parse($request->exam_date);
        $payload['title'] = $typeName . ' - ' . ($class->name ?? 'Class') . ' - ' . ($subject->name ?? 'Subject');
        $payload['description'] = $request->instructions;
        $payload['academic_year'] = (string) $examDate->year;
        $payload['start_date'] = $examDate->toDateString();
        $payload['end_date'] = $examDate->toDateString();
        $payload['status'] = 'published';
        $payload['is_active'] = true;
        $examSchedule = ExamSchedule::create($payload);

        // Send notifications to students
        $notificationService = new ExamNotificationService();
        $notificationService->notifyExamPosted($examSchedule);

        return redirect()->route('teacher.exams.index')
            ->with('success', 'Exam schedule created successfully.');
    }

    public function show(ExamSchedule $examSchedule)
    {
        $examSchedule->load(['examType', 'class', 'subject', 'marks.student']);
        return view('exams.show', compact('examSchedule'));
    }

    public function edit(ExamSchedule $examSchedule)
    {
        $examTypes = ExamType::all();
        $classes = ClassRoom::all();
        $subjects = Subject::all();
        return view('exams.edit', compact('examSchedule', 'examTypes', 'classes', 'subjects'));
    }

    public function update(Request $request, ExamSchedule $examSchedule)
    {
        $request->validate([
            'exam_type_id' => 'required|exists:exam_types,id',
            'class_id' => 'required|exists:class_rooms,id',
            'subject_id' => 'required|exists:subjects,id',
            'exam_date' => 'required|date',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'total_marks' => 'required|numeric|min:1',
            'passing_marks' => 'required|numeric|min:1|lte:total_marks',
            'room_number' => 'nullable|string|max:255',
            'instructions' => 'nullable|string',
        ]);

        $examSchedule->update($request->all());

        return redirect()->route('exams.index')
            ->with('success', 'Exam schedule updated successfully.');
    }

    public function destroy(ExamSchedule $examSchedule)
    {
        $examSchedule->delete();

        return redirect()->route('exams.index')
            ->with('success', 'Exam schedule deleted successfully.');
    }

    public function types()
    {
        $examTypes = ExamType::all();
        return view('exams.types', compact('examTypes'));
    }

    public function schedules()
    {
        $examSchedules = ExamSchedule::with(['examType', 'subject', 'class'])->paginate(15);
        return view('exams.schedules', compact('examSchedules'));
    }

    public function createType()
    {
        return view('exams.create-type');
    }

    public function storeType(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:exam_types',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        ExamType::create($request->all());

        return redirect()->route('exams.types')
            ->with('success', 'Exam type created successfully.');
    }

    public function editType(ExamType $examType)
    {
        return view('exams.edit-type', compact('examType'));
    }

    public function updateType(Request $request, ExamType $examType)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:exam_types,name,' . $examType->id,
            'description' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        $examType->update($request->all());

        return redirect()->route('exams.types')
            ->with('success', 'Exam type updated successfully.');
    }

    public function destroyType(ExamType $examType)
    {
        $examType->delete();

        return redirect()->route('exams.types')
            ->with('success', 'Exam type deleted successfully.');
    }

    public function marks(ExamSchedule $examSchedule)
    {
        $students = $examSchedule->class->students;
        $marks = $examSchedule->marks()->with('student')->get();
        
        return view('exams.marks', compact('examSchedule', 'students', 'marks'));
    }

    public function storeMarks(Request $request, ExamSchedule $examSchedule)
    {
        $request->validate([
            'marks' => 'required|array',
            'marks.*.student_id' => 'required|exists:students,id',
            'marks.*.marks_obtained' => 'required|numeric|min:0|lte:' . $examSchedule->total_marks,
            'marks.*.remarks' => 'nullable|string',
        ]);

        foreach ($request->marks as $markData) {
            ExamMark::updateOrCreate(
                [
                    'exam_schedule_id' => $examSchedule->id,
                    'student_id' => $markData['student_id'],
                ],
                [
                    'marks_obtained' => $markData['marks_obtained'],
                    'remarks' => $markData['remarks'] ?? null,
                ]
            );
        }

        return redirect()->route('exams.marks', $examSchedule)
            ->with('success', 'Exam marks saved successfully.');
    }

    public function studentMarks(Student $student)
    {
        $marks = $student->examMarks()->with(['examSchedule.examType', 'examSchedule.subject'])->get();
        $examTypes = ExamType::all();
        
        return view('exams.student-marks', compact('student', 'marks', 'examTypes'));
    }

    public function classMarks(ClassRoom $class)
    {
        $examSchedules = $class->examSchedules()->with(['examType', 'subject'])->get();
        $students = $class->students;
        
        return view('exams.class-marks', compact('class', 'examSchedules', 'students'));
    }

    public function results()
    {
        $classes = ClassRoom::all();
        $examTypes = ExamType::all();
        
        return view('exams.results', compact('classes', 'examTypes'));
    }

    public function generateResults(Request $request)
    {
        $request->validate([
            'class_id' => 'required|exists:class_rooms,id',
            'exam_type_id' => 'required|exists:exam_types,id',
        ]);

        $class = ClassRoom::findOrFail($request->class_id);
        $examType = ExamType::findOrFail($request->exam_type_id);
        
        $examSchedules = $class->examSchedules()
            ->where('exam_type_id', $examType->id)
            ->with(['subject', 'marks.student'])
            ->get();
        
        $students = $class->students;
        $results = [];
        
        foreach ($students as $student) {
            $totalMarks = 0;
            $obtainedMarks = 0;
            $subjects = [];
            
            foreach ($examSchedules as $schedule) {
                $mark = $schedule->marks()->where('student_id', $student->id)->first();
                $totalMarks += $schedule->total_marks;
                $obtainedMarks += $mark ? $mark->marks_obtained : 0;
                
                $subjects[] = [
                    'subject' => $schedule->subject->name,
                    'total_marks' => $schedule->total_marks,
                    'obtained_marks' => $mark ? $mark->marks_obtained : 0,
                    'percentage' => $mark ? round(($mark->marks_obtained / $schedule->total_marks) * 100, 2) : 0,
                ];
            }
            
            $percentage = $totalMarks > 0 ? round(($obtainedMarks / $totalMarks) * 100, 2) : 0;
            $grade = $this->calculateGrade($percentage);
            
            $results[] = [
                'student' => $student,
                'total_marks' => $totalMarks,
                'obtained_marks' => $obtainedMarks,
                'percentage' => $percentage,
                'grade' => $grade,
                'subjects' => $subjects,
            ];
        }
        
        // Sort by percentage descending
        usort($results, function($a, $b) {
            return $b['percentage'] <=> $a['percentage'];
        });
        
        return view('exams.results-view', compact('class', 'examType', 'results'));
    }

    private function calculateGrade($percentage)
    {
        if ($percentage >= 90) return 'A+';
        if ($percentage >= 80) return 'A';
        if ($percentage >= 70) return 'B+';
        if ($percentage >= 60) return 'B';
        if ($percentage >= 50) return 'C+';
        if ($percentage >= 40) return 'C';
        if ($percentage >= 35) return 'D';
        return 'F';
    }

    public function reports()
    {
        $totalExams = ExamSchedule::count();
        $totalMarks = ExamMark::count();
        $averageScore = ExamMark::avg('marks_obtained') ?? 0;
        
        $classPerformance = DB::table('exam_marks')
            ->join('exam_schedules', 'exam_marks.exam_schedule_id', '=', 'exam_schedules.id')
            ->join('class_rooms', 'exam_schedules.class_id', '=', 'class_rooms.id')
            ->selectRaw('class_rooms.name as class_name, AVG(exam_marks.marks_obtained) as average_score')
            ->groupBy('class_rooms.id', 'class_rooms.name')
            ->orderBy('average_score', 'desc')
            ->get();
        
        $subjectPerformance = DB::table('exam_marks')
            ->join('exam_schedules', 'exam_marks.exam_schedule_id', '=', 'exam_schedules.id')
            ->join('subjects', 'exam_schedules.subject_id', '=', 'subjects.id')
            ->selectRaw('subjects.name as subject_name, AVG(exam_marks.marks_obtained) as average_score')
            ->groupBy('subjects.id', 'subjects.name')
            ->orderBy('average_score', 'desc')
            ->get();
        
        return view('exams.reports', compact(
            'totalExams',
            'totalMarks',
            'averageScore',
            'classPerformance',
            'subjectPerformance'
        ));
    }

    public function upcoming()
    {
        $upcomingExams = ExamSchedule::where('exam_date', '>=', now())
            ->with(['examType', 'class', 'subject'])
            ->orderBy('exam_date')
            ->paginate(15);
        
        return view('exams.upcoming', compact('upcomingExams'));
    }

    public function calendar()
    {
        $examSchedules = ExamSchedule::with(['examType', 'class', 'subject'])
            ->where('exam_date', '>=', now()->startOfMonth())
            ->where('exam_date', '<=', now()->endOfMonth())
            ->get();
        
        return view('exams.calendar', compact('examSchedules'));
    }

    // Teacher-facing exams list for their assigned classes/subjects
    public function teacherExams(Request $request)
    {
        $teacher = Teacher::where('user_id', auth()->id())->firstOrFail();
        $classIds = $teacher->classes()->pluck('class_rooms.id');

        $query = ExamSchedule::with(['examType','class','subject'])
            ->whereIn('class_id', $classIds);

        if ($request->filled('class_id')) {
            $query->where('class_id', $request->class_id);
        }
        if ($request->filled('subject_id')) {
            $query->where('subject_id', $request->subject_id);
        }
        if ($request->filled('date')) {
            $query->whereDate('start_date', $request->date);
        }

        $examSchedules = $query->orderBy('start_date')->orderBy('start_time')->paginate(15);
        $classes = ClassRoom::whereIn('id', $classIds)->get();
        $subjects = Subject::whereIn('class_id', $classIds)->get();

        return view('teacher.exams.index', compact('examSchedules','classes','subjects'));
    }

    public function teacherUpcoming(Request $request)
    {
        $teacher = Teacher::where('user_id', auth()->id())->firstOrFail();
        $classIds = $teacher->classes()->pluck('class_rooms.id');

        $examSchedules = ExamSchedule::with(['examType','class','subject'])
            ->whereIn('class_id', $classIds)
            ->whereDate('start_date','>=', now())
            ->orderBy('start_date')
            ->orderBy('start_time')
            ->paginate(15);

        return view('teacher.exams.index', [
            'examSchedules' => $examSchedules,
            'classes' => ClassRoom::whereIn('id', $classIds)->get(),
            'subjects' => Subject::whereIn('class_id', $classIds)->get(),
        ]);
    }

    public function teacherMarks(ExamSchedule $examSchedule)
    {
        $teacher = Teacher::where('user_id', auth()->id())->firstOrFail();
        $isAuthorized = ($examSchedule->subject && $examSchedule->subject->teacher_id === $teacher->id)
            || $teacher->classes()->where('class_rooms.id', $examSchedule->class_id)->exists();
        abort_unless($isAuthorized, 403, 'Not authorized to manage marks for this exam.');

        $students = $examSchedule->class->students()->with('user')->get();
        $marks = $examSchedule->examMarks()->get()->keyBy('student_id');

        return view('teacher.exams.marks', compact('examSchedule','students','marks'));
    }

    public function storeTeacherMarks(Request $request, ExamSchedule $examSchedule)
    {
        $teacher = Teacher::where('user_id', auth()->id())->firstOrFail();
        $isAuthorized = ($examSchedule->subject && $examSchedule->subject->teacher_id === $teacher->id)
            || $teacher->classes()->where('class_rooms.id', $examSchedule->class_id)->exists();
        abort_unless($isAuthorized, 403, 'Not authorized to manage marks for this exam.');

        $request->validate([
            'marks' => 'required|array',
            'marks.*.student_id' => 'required|exists:students,id',
            'marks.*.marks_obtained' => 'required|numeric|min:0|lte:' . $examSchedule->total_marks,
            'marks.*.remarks' => 'nullable|string',
        ]);

        foreach ($request->marks as $markData) {
            if (!$examSchedule->class->students()->where('students.id', $markData['student_id'])->exists()) {
                continue; // skip students not in the class (safety)
            }
            ExamMark::updateOrCreate(
                [
                    'exam_schedule_id' => $examSchedule->id,
                    'student_id' => $markData['student_id'],
                ],
                [
                    'marks_obtained' => $markData['marks_obtained'],
                    'remarks' => $markData['remarks'] ?? null,
                ]
            );
        }

        return redirect()->route('teacher.exams.marks', $examSchedule)
            ->with('success', 'Marks saved.');
    }

    /**
     * Add questions to an exam
     */
    public function addQuestions(Request $request, ExamSchedule $examSchedule)
    {
        $request->validate([
            'questions' => 'required|array|min:1',
            'questions.*.question_text' => 'required|string|max:1000',
            'questions.*.type' => 'required|in:mcq,short',
            'questions.*.marks' => 'required|numeric|min:0.5|max:100',
            'questions.*.options' => 'required_if:questions.*.type,mcq|array',
            'questions.*.correct_answer' => 'required_if:questions.*.type,mcq|string',
        ]);

        foreach ($request->questions as $questionData) {
            ExamQuestion::create([
                'exam_schedule_id' => $examSchedule->id,
                'question_text' => $questionData['question_text'],
                'type' => $questionData['type'],
                'marks' => $questionData['marks'],
                'options' => $questionData['options'] ?? null,
                'correct_answer' => $questionData['correct_answer'] ?? null,
            ]);
        }

        return redirect()->route('teacher.exams.show', $examSchedule)
            ->with('success', 'Questions added successfully.');
    }

    /**
     * Show exam for student to take
     */
    public function showForStudent(ExamSchedule $examSchedule)
    {
        $user = auth()->user();
        $student = $user->student;
        
        if (!$student || $student->class_id !== $examSchedule->class_id) {
            abort(403, 'You are not authorized to take this exam.');
        }

        // Check if student has already attempted this exam
        $attempt = ExamAttempt::where('exam_schedule_id', $examSchedule->id)
            ->where('student_id', $student->id)
            ->first();

        if ($attempt && $attempt->status === 'submitted') {
            return redirect()->route('student.exams.results', $attempt->id)
                ->with('info', 'You have already taken this exam.');
        }

        // Load questions
        $questions = ExamQuestion::where('exam_schedule_id', $examSchedule->id)->get();
        
        if ($questions->isEmpty()) {
            return redirect()->route('student.exams.index')
                ->with('error', 'No questions available for this exam.');
        }

        return view('student.exams.take', compact('examSchedule', 'questions', 'attempt'));
    }

    /**
     * Start exam attempt
     */
    public function startExam(ExamSchedule $examSchedule)
    {
        $user = auth()->user();
        $student = $user->student;
        
        if (!$student || $student->class_id !== $examSchedule->class_id) {
            abort(403, 'You are not authorized to take this exam.');
        }

        // Check if student has already attempted this exam
        $attempt = ExamAttempt::where('exam_schedule_id', $examSchedule->id)
            ->where('student_id', $student->id)
            ->first();

        if (!$attempt) {
            $attempt = ExamAttempt::create([
                'exam_schedule_id' => $examSchedule->id,
                'student_id' => $student->id,
                'started_at' => now(),
                'status' => 'in_progress'
            ]);
        }

        return redirect()->route('student.exams.take', $examSchedule);
    }

    /**
     * Submit exam answers
     */
    public function submitExam(Request $request, ExamSchedule $examSchedule)
    {
        $user = auth()->user();
        $student = $user->student;
        
        if (!$student || $student->class_id !== $examSchedule->class_id) {
            abort(403, 'You are not authorized to submit this exam.');
        }

        $attempt = ExamAttempt::where('exam_schedule_id', $examSchedule->id)
            ->where('student_id', $student->id)
            ->first();

        if (!$attempt || $attempt->status === 'submitted') {
            return redirect()->route('student.exams.index')
                ->with('error', 'Invalid exam attempt.');
        }

        DB::beginTransaction();
        try {
            // Save answers
            foreach ($request->answers as $questionId => $answer) {
                ExamAnswer::updateOrCreate([
                    'exam_attempt_id' => $attempt->id,
                    'exam_question_id' => $questionId,
                ], [
                    'answer_text' => $answer,
                ]);
            }

            // Update attempt status
            $attempt->update([
                'submitted_at' => now(),
                'status' => 'submitted'
            ]);

            // Send notification to teacher
            $notificationService = new ExamNotificationService();
            $notificationService->notifyExamSubmission($attempt);

            DB::commit();

            return redirect()->route('student.exams.index')
                ->with('success', 'Exam submitted successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to submit exam. Please try again.');
        }
    }

    /**
     * Show exam for teacher to mark
     */
    public function showForMarking(ExamAttempt $attempt)
    {
        $attempt->load(['examSchedule.subject', 'examSchedule.class', 'student.user', 'answers.question']);
        return view('teacher.exams.mark', compact('attempt'));
    }

    /**
     * Mark exam
     */
    public function markExam(Request $request, ExamAttempt $attempt)
    {
        $request->validate([
            'answers' => 'required|array',
            'answers.*.marks_awarded' => 'required|numeric|min:0',
        ]);

        DB::beginTransaction();
        try {
            $totalMarks = 0;
            
            foreach ($request->answers as $answerId => $answerData) {
                $answer = ExamAnswer::find($answerId);
                if ($answer && $answer->exam_attempt_id === $attempt->id) {
                    $answer->update([
                        'marks_awarded' => $answerData['marks_awarded']
                    ]);
                    $totalMarks += $answerData['marks_awarded'];
                }
            }

            // Update attempt with total score
            $attempt->update([
                'score' => $totalMarks,
                'status' => 'graded'
            ]);

            // Send notification to student
            $notificationService = new ExamNotificationService();
            $notificationService->notifyExamMarked($attempt);

            DB::commit();

            return redirect()->route('teacher.exams.submissions', $attempt->examSchedule)
                ->with('success', 'Exam marked successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to mark exam. Please try again.');
        }
    }

    /**
     * Show exam results for student
     */
    public function showResults(ExamAttempt $attempt)
    {
        $user = auth()->user();
        $student = $user->student;
        
        if (!$student || $attempt->student_id !== $student->id) {
            abort(403, 'You are not authorized to view these results.');
        }

        $attempt->load(['examSchedule.subject', 'examSchedule.class', 'answers.question']);
        return view('student.exams.results', compact('attempt'));
    }

    /**
     * Show exam submissions for teacher
     */
    public function showSubmissions(ExamSchedule $examSchedule)
    {
        $attempts = ExamAttempt::with(['student.user', 'answers'])
            ->where('exam_schedule_id', $examSchedule->id)
            ->get();
            
        return view('teacher.exams.submissions', compact('examSchedule', 'attempts'));
    }

    /**
     * Get upcoming exams for student
     */
    public function upcomingExams()
    {
        $user = auth()->user();
        
        // Check if user is a student
        if ($user->user_type === 'student') {
            $student = $user->student;
            
            if (!$student) {
                abort(403, 'Student record not found');
            }

            $upcomingExams = ExamSchedule::with(['subject', 'class', 'examType'])
                ->where('class_id', $student->class_id)
                ->where('status', 'published')
                ->where('start_date', '>=', now()->toDateString())
                ->orderBy('start_date')
                ->get();

            return view('student.exams.upcoming', compact('upcomingExams'));
        }
        
        // For admin/teacher users - show all upcoming exams
        $upcomingExams = ExamSchedule::with(['subject', 'class', 'examType'])
            ->where('status', 'published')
            ->where('start_date', '>=', now()->toDateString())
            ->orderBy('start_date')
            ->get();

        return view('exams.upcoming', compact('upcomingExams'));
    }
    
    public function myMarks()
    {
        $student = auth()->user()->student;
        
        if (!$student) {
            abort(403, 'Student record not found');
        }
        
        $examAttempts = ExamAttempt::where('student_id', $student->id)
            ->where('status', 'completed')
            ->with(['examSchedule.subject', 'examSchedule.class', 'examSchedule.examType'])
            ->orderBy('created_at', 'desc')
            ->get();
            
        return view('student.exams.marks', compact('examAttempts'));
    }
}
