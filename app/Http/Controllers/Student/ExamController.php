<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ExamController extends Controller
{
    public function index()
    {
        try {
            $user = auth()->user();
            $student = $user->student;
        } catch (\Exception $e) {
            return redirect()->route('student.dashboard')
                ->with('error', 'Student profile not available. Please contact administrator.');
        }
        
        if (!$student) {
            return redirect()->route('student.dashboard')
                ->with('error', 'Student record not found. Please contact administrator.');
        }

        // Get available exam schedules for student's class
        $examSchedules = \App\Models\ExamSchedule::with(['examType', 'subject'])
            ->where('class_id', $student->class_id)
            ->where('is_active', true)
            ->orderBy('start_date', 'desc')
            ->get();

        // Get available exam papers (online exams) for student's class
        $examPapers = \App\Models\ExamPaper::with(['subject', 'classRoom', 'teacher'])
            ->where('class_id', $student->class_id)
            ->where('is_published', true)
            ->orderBy('start_time', 'desc')
            ->get();

        // Get student's exam attempts
        $examAttempts = \App\Models\StudentExamAttempt::with(['examPaper.subject', 'examPaper.classRoom'])
            ->where('student_id', $student->id)
            ->orderBy('created_at', 'desc')
            ->get();

        return view('student.exams.index', compact('examSchedules', 'examPapers', 'examAttempts'));
    }

    public function marks()
    {
        try {
            $user = auth()->user();
            $student = $user->student;
        } catch (\Exception $e) {
            return redirect()->route('student.dashboard')
                ->with('error', 'Student profile not available. Please contact administrator.');
        }
        
        if (!$student) {
            return redirect()->route('student.dashboard')
                ->with('error', 'Student record not found. Please contact administrator.');
        }

        // Get exam attempts for the student
        $examAttempts = \App\Models\ExamAttempt::with(['examSchedule.subject', 'examSchedule.class'])
            ->where('student_id', $student->id)
            ->orderBy('created_at', 'desc')
            ->get();

        return view('student.exams.marks', compact('examAttempts'));
    }

    public function upcoming()
    {
        try {
            $user = auth()->user();
            $student = $user->student;
        } catch (\Exception $e) {
            return redirect()->route('student.dashboard')
                ->with('error', 'Student profile not available. Please contact administrator.');
        }
        
        if (!$student) {
            return redirect()->route('student.dashboard')
                ->with('error', 'Student record not found. Please contact administrator.');
        }

        // Get upcoming exam schedules for student's class
        $upcomingSchedules = \App\Models\ExamSchedule::with(['examType', 'subject'])
            ->where('class_id', $student->class_id)
            ->where('start_date', '>=', now())
            ->where('is_active', true)
            ->orderBy('start_date')
            ->get();

        // Get upcoming exam papers (online exams) for student's class
        $upcomingPapers = \App\Models\ExamPaper::with(['subject', 'classRoom', 'teacher'])
            ->where('class_id', $student->class_id)
            ->where('is_published', true)
            ->where('start_time', '>=', now())
            ->orderBy('start_time')
            ->get();

        // Combine both types of exams
        $upcomingExams = $upcomingSchedules->concat($upcomingPapers);

        return view('student.exams.upcoming', compact('upcomingExams', 'upcomingSchedules', 'upcomingPapers'));
    }

    public function show($id)
    {
        return view('student.exams.show', compact('id'));
    }

    public function start($id)
    {
        // Placeholder for exam start
        return redirect()->route('student.exams.take', ['attempt' => 1]);
    }

    public function take($attempt)
    {
        return view('student.exams.take', compact('attempt'));
    }

    public function submit($attempt)
    {
        // Placeholder for exam submission
        return redirect()->route('student.exams.result', ['attempt' => $attempt]);
    }

    public function result($attempt)
    {
        return view('student.exams.result', compact('attempt'));
    }

    public function saveAnswer(Request $request)
    {
        try {
            $request->validate([
                'exam_id' => 'required|exists:exam_papers,id',
                'question_id' => 'required|exists:exam_questions,id',
                'answer' => 'required|string',
                'attempt_id' => 'nullable|exists:student_exam_attempts,id'
            ]);

            $user = auth()->user();
            $student = $user->student;
            
            if (!$student) {
                return response()->json(['error' => 'Student profile not found'], 404);
            }

            // Find or create exam attempt
            $attempt = \App\Models\StudentExamAttempt::where('student_id', $student->id)
                ->where('exam_paper_id', $request->exam_id)
                ->first();

            if (!$attempt) {
                $attempt = \App\Models\StudentExamAttempt::create([
                    'student_id' => $student->id,
                    'exam_paper_id' => $request->exam_id,
                    'attempt_number' => 1,
                    'started_at' => now(),
                    'status' => 'in_progress',
                    'student_answers' => [],
                    'questions_answered' => 0
                ]);
            }

            // Update student answers
            $answers = $attempt->student_answers ?? [];
            $answers[$request->question_id] = $request->answer;
            
            $attempt->update([
                'student_answers' => $answers,
                'questions_answered' => count($answers),
                'updated_at' => now()
            ]);

            return response()->json([
                'success' => true,
                'attempt_id' => $attempt->id,
                'questions_answered' => count($answers)
            ]);

        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to save answer: ' . $e->getMessage()], 500);
        }
    }

    public function getAnswers($attemptId)
    {
        try {
            $user = auth()->user();
            $student = $user->student;
            
            if (!$student) {
                return response()->json(['error' => 'Student profile not found'], 404);
            }

            $attempt = \App\Models\StudentExamAttempt::where('id', $attemptId)
                ->where('student_id', $student->id)
                ->first();

            if (!$attempt) {
                return response()->json(['error' => 'Exam attempt not found'], 404);
            }

            return response()->json([
                'answers' => $attempt->student_answers ?? [],
                'questions_answered' => $attempt->questions_answered ?? 0,
                'status' => $attempt->status
            ]);

        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to get answers: ' . $e->getMessage()], 500);
        }
    }

    public function submitExam(Request $request)
    {
        try {
            $request->validate([
                'exam_id' => 'required|exists:exam_papers,id',
                'attempt_id' => 'required|exists:student_exam_attempts,id'
            ]);

            $user = auth()->user();
            $student = $user->student;
            
            if (!$student) {
                return response()->json(['error' => 'Student profile not found'], 404);
            }

            $attempt = \App\Models\StudentExamAttempt::where('id', $request->attempt_id)
                ->where('student_id', $student->id)
                ->where('exam_paper_id', $request->exam_id)
                ->first();

            if (!$attempt) {
                return response()->json(['error' => 'Exam attempt not found'], 404);
            }

            if ($attempt->status === 'submitted') {
                return response()->json(['error' => 'Exam already submitted'], 400);
            }

            // Calculate score automatically
            $exam = \App\Models\ExamPaper::find($request->exam_id);
            $questions = $exam->questions;
            $answers = $attempt->student_answers ?? [];
            $correctAnswers = 0;
            $totalQuestions = $questions->count();

            foreach ($questions as $question) {
                $studentAnswer = $answers[$question->id] ?? null;
                if ($studentAnswer && $studentAnswer === $question->correct_answer) {
                    $correctAnswers++;
                }
            }

            $marksObtained = ($correctAnswers / max($totalQuestions, 1)) * $exam->total_marks;
            $percentage = ($marksObtained / $exam->total_marks) * 100;

            // Update attempt with final submission
            $attempt->update([
                'submitted_at' => now(),
                'status' => 'submitted',
                'marks_obtained' => $marksObtained,
                'total_marks' => $exam->total_marks,
                'percentage' => $percentage,
                'questions_answered' => count($answers),
                'is_passed' => $percentage >= $exam->passing_marks
            ]);

            // Send real-time notification to teacher
            $this->notifyTeacherOfSubmission($attempt, $exam);

            return response()->json([
                'success' => true,
                'marks_obtained' => $marksObtained,
                'total_marks' => $exam->total_marks,
                'percentage' => $percentage,
                'is_passed' => $percentage >= $exam->passing_marks
            ]);

        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to submit exam: ' . $e->getMessage()], 500);
        }
    }

    private function notifyTeacherOfSubmission($attempt, $exam)
    {
        try {
            // Create notification for teacher
            $teacher = $exam->teacher;
            if ($teacher && $teacher->user) {
                $notification = new \App\Models\Notification([
                    'user_id' => $teacher->user->id,
                    'type' => 'exam_submission',
                    'title' => 'New Exam Submission',
                    'message' => $attempt->student->user->name . ' has submitted the exam: ' . $exam->title,
                    'category' => 'academic',
                    'subcategory' => 'exam_submission',
                    'priority' => 6, // High priority
                    'status' => 'pending',
                    'delivery_method' => 'in_app',
                    'delivery_status' => 'pending',
                    'action_url' => route('teacher.exams.show', $exam),
                    'action_text' => 'View Submission',
                    'related_model' => 'StudentExamAttempt',
                    'related_id' => $attempt->id,
                    'metadata' => [
                        'exam_id' => $exam->id,
                        'attempt_id' => $attempt->id,
                        'student_id' => $attempt->student_id,
                        'marks_obtained' => $attempt->marks_obtained,
                        'percentage' => $attempt->percentage,
                        'is_passed' => $attempt->is_passed
                    ],
                    'is_active' => true
                ]);
                $notification->save();
            }
        } catch (\Exception $e) {
            \Log::error('Failed to notify teacher of exam submission: ' . $e->getMessage());
        }
    }
}