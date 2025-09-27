<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ParentController extends Controller
{
    public function grades()
    {
        return view('parent.grades.index');
    }

    public function progress()
    {
        return view('parent.grades.progress');
    }

    public function download()
    {
        return view('parent.grades.download');
    }
}
