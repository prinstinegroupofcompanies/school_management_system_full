<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class SubjectController extends Controller
{
    public function index()
    {
        return view('teacher.subjects.index');
    }

    public function show($id)
    {
        return view('teacher.subjects.show', compact('id'));
    }
}