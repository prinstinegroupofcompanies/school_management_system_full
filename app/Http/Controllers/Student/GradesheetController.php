<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Grade;
use App\Models\Student;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class GradesheetController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function show(Request $request)
    {
        $user = $request->user();
        $student = $user->student;
        $year = (int)($request->get('year', date('Y')));
        $period = $request->get('period', 'year'); // period1..period6, sem1, sem2, year

        $grades = Grade::where('student_id', $student->id)
            ->where('academic_year', $year)
            ->with(['subject','teacher.user'])
            ->get();

        return view('student.gradesheet.show', compact('student','grades','year','period'));
    }

    public function pdf(Request $request)
    {
        $user = $request->user();
        $student = $user->student;
        $year = (int)($request->get('year', date('Y')));
        $period = $request->get('period', 'year');

        $grades = Grade::where('student_id', $student->id)
            ->where('academic_year', $year)
            ->with(['subject','teacher.user'])
            ->get();

        $pdf = Pdf::loadView('student.gradesheet.pdf', [
            'student' => $student,
            'grades' => $grades,
            'year' => $year,
            'period' => $period,
        ])->setPaper('a4');

        $filename = 'gradesheet_'.$student->student_id.'_'.$year.'_'.$period.'.pdf';
        return $pdf->download($filename);
    }
}


