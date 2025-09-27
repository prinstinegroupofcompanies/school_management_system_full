<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ExamController extends Controller
{
    public function index()
    {
        return view('student.exams.index');
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

        // Get upcoming exams for student's class
        $upcomingExams = \App\Models\ExamSchedule::with(['examType', 'subject'])
            ->where('class_id', $student->class_id)
            ->where('exam_date', '>=', now())
            ->orderBy('exam_date')
            ->get();

        return view('student.exams.upcoming', compact('upcomingExams'));
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
        // Placeholder for saving answers
        return response()->json(['success' => true]);
    }

    public function getAnswers($attempt)
    {
        // Placeholder for getting answers
        return response()->json(['answers' => []]);
    }
}