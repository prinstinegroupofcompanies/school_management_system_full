<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class HostelController extends Controller
{
    public function index()
    {
        return view('student.hostel.index');
    }

    public function rooms()
    {
        return view('student.hostel.rooms');
    }

    public function payments()
    {
        return view('student.hostel.payments');
    }

    public function myRoom()
    {
        return view('student.hostel.room');
    }

    public function facilities()
    {
        return view('student.hostel.facilities');
    }

    public function request(Request $request)
    {
        // Placeholder for hostel request
        return redirect()->route('student.hostel.index')->with('success', 'Hostel request submitted successfully');
    }
}