<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AttendanceController extends Controller
{
    public function index()
    {
        return view('student.attendance.index');
    }

    public function history()
    {
        return view('student.attendance.history');
    }

    public function summary()
    {
        return view('student.attendance.summary');
    }
}