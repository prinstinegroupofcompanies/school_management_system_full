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
        return view('student.exams.marks');
    }

    public function upcoming()
    {
        return view('student.exams.upcoming');
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