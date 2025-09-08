<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Teacher;
use App\Models\Department;
use App\Models\Designation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    public function show()
    {
        $user = auth()->user();
        $teacher = $user->teacher;
        
        if (!$teacher) {
            abort(403, 'Teacher profile not found.');
        }
        
        $teacher->load(['user', 'department', 'designation']);
        
        return view('teacher.profile.show', compact('teacher'));
    }
    
    public function edit()
    {
        $user = auth()->user();
        $teacher = $user->teacher;
        
        if (!$teacher) {
            abort(403, 'Teacher profile not found.');
        }
        
        $departments = Department::all();
        $designations = Designation::all();
        
        return view('teacher.profile.edit', compact('teacher', 'departments', 'designations'));
    }
    
    public function update(Request $request)
    {
        $user = auth()->user();
        $teacher = $user->teacher;
        
        if (!$teacher) {
            abort(403, 'Teacher profile not found.');
        }
        
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:500',
            'city' => 'nullable|string|max:100',
            'state' => 'nullable|string|max:100',
            'country' => 'nullable|string|max:100',
            'postal_code' => 'nullable|string|max:20',
            'profile_photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'qualification' => 'nullable|string|max:255',
            'experience' => 'nullable|string|max:255',
            'bio' => 'nullable|string|max:1000',
        ]);
        
        // Update user information
        $user->update([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'address' => $request->address,
            'city' => $request->city,
            'state' => $request->state,
            'country' => $request->country,
            'postal_code' => $request->postal_code,
        ]);
        
        // Handle profile photo upload
        if ($request->hasFile('profile_photo')) {
            // Delete old photo if exists
            if ($user->profile_photo) {
                Storage::disk('public')->delete($user->profile_photo);
            }
            
            $path = $request->file('profile_photo')->store('profile-photos', 'public');
            $user->update(['profile_photo' => $path]);
        }
        
        // Update teacher information
        $teacher->update([
            'qualification' => $request->qualification,
            'experience' => $request->experience,
        ]);
        
        return redirect()->route('teacher.profile')
            ->with('success', 'Profile updated successfully.');
    }
}
