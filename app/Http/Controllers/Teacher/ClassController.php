<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ClassController extends Controller
{
    public function index()
    {
        return view('teacher.classes.index');
    }

    public function show($id)
    {
        return view('teacher.classes.show', compact('id'));
    }
}