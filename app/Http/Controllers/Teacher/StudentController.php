<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class StudentController extends Controller
{
    public function index()
    {
        return view('teacher.students.index');
    }

    public function show($id)
    {
        return view('teacher.students.show', compact('id'));
    }
}