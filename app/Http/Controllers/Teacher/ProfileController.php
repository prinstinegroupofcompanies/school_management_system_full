<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ProfileController extends Controller
{
    public function show()
    {
        return view('teacher.profile.show');
    }

    public function edit()
    {
        return view('teacher.profile.edit');
    }

    public function update(Request $request)
    {
        // Placeholder for profile update
        return redirect()->route('teacher.profile.show')->with('success', 'Profile updated successfully');
    }
}