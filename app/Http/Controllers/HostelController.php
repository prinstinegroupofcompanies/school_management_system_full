<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class HostelController extends Controller
{
    public function index()
    {
        return view('hostel.index');
    }

    public function createHostel()
    {
        return view('hostel.create');
    }

    public function storeHostel(Request $request)
    {
        // Placeholder for hostel creation
        return redirect()->route('hostel.index')->with('success', 'Hostel created successfully');
    }

    public function rooms()
    {
        return view('hostel.rooms');
    }

    public function students()
    {
        return view('hostel.students');
    }

    public function facilities()
    {
        return view('hostel.facilities');
    }
}