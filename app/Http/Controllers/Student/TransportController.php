<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class TransportController extends Controller
{
    public function index()
    {
        return view('student.transport.index');
    }

    public function schedule()
    {
        return view('student.transport.schedule');
    }

    public function routes()
    {
        return view('student.transport.routes');
    }

    public function request(Request $request)
    {
        // Placeholder for transport request
        return redirect()->route('student.transport.index')->with('success', 'Transport request submitted successfully');
    }
}