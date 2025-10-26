<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    public function show()
    {
        try {
            $user = auth()->user();
            $teacher = $user->teacher()->with(['department', 'designation'])->first();
            
            if (!$teacher) {
                return redirect()->route('teacher.dashboard')
                    ->with('error', 'Teacher profile not found. Please contact administrator.');
            }

            // Get teacher statistics
            $stats = [
                'classes_taught' => $teacher->classes()->count(),
                'total_students' => \App\Models\Student::whereIn('class_id', 
                    $teacher->classes()->pluck('class_rooms.id')
                )->count(),
                'subjects_taught' => $teacher->subjects()->count(),
                'total_grades' => \App\Models\Grade::where('teacher_id', $teacher->id)->count()
            ];

            // Get recent activities
            $recentActivities = collect([
                ['description' => 'Profile created', 'created_at' => $teacher->created_at],
                ['description' => 'Last login', 'created_at' => $user->updated_at],
                ['description' => 'Total grades entered: ' . $stats['total_grades'], 'created_at' => now()],
            ]);

        } catch (\Exception $e) {
            // Fallback data if database queries fail
            $teacher = null;
            $user = auth()->user();
            $stats = [
                'classes_taught' => 0,
                'total_students' => 0,
                'subjects_taught' => 0,
                'total_grades' => 0
            ];
            $recentActivities = collect([
                ['description' => 'Profile unavailable', 'created_at' => now()],
            ]);
        }

        return view('teacher.profile.show', compact('user', 'teacher', 'stats', 'recentActivities'));
    }

    public function edit()
    {
        try {
            $user = auth()->user();
            $teacher = $user->teacher;
            
            if (!$teacher) {
                return redirect()->route('teacher.profile.show')
                    ->with('error', 'Teacher profile not found. Please contact administrator.');
            }

        } catch (\Exception $e) {
            return redirect()->route('teacher.profile.show')
                ->with('error', 'Error loading profile for editing. Please try again.');
        }

        return view('teacher.profile.edit', compact('user', 'teacher'));
    }

    public function update(Request $request)
    {
        try {
            $user = auth()->user();
            $teacher = $user->teacher;
            
            if (!$teacher) {
                return redirect()->route('teacher.profile.show')
                    ->with('error', 'Teacher profile not found. Please contact administrator.');
            }

            $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|email|max:255|unique:users,email,' . $user->id,
                'phone' => 'nullable|string|max:20',
                'address' => 'nullable|string|max:500',
                'bio' => 'nullable|string|max:1000',
                'profile_photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
            ]);

            // Update user information
            $userData = [
                'name' => $request->name,
                'email' => $request->email,
            ];
            
            if ($request->filled('phone')) {
                $userData['phone'] = $request->phone;
            }
            
            if ($request->filled('address')) {
                $userData['address'] = $request->address;
            }
            
            $user->update($userData);

            // Update teacher information if provided
            $teacherData = [];
            
            if ($request->filled('qualification')) {
                $teacherData['qualification'] = $request->qualification;
            }
            
            if ($request->filled('experience')) {
                $teacherData['experience'] = $request->experience;
            }
            
            if ($request->filled('basic_salary')) {
                $teacherData['basic_salary'] = $request->basic_salary;
            }

            // Handle profile photo upload
            if ($request->hasFile('profile_photo')) {
                // Delete old photo if exists
                if ($user->profile_photo) {
                    Storage::delete($user->profile_photo);
                }
                
                $path = $request->file('profile_photo')->store('profile_photos', 'public');
                $user->update(['profile_photo' => $path]);
            }

            // Only update teacher data if there's data to update
            if (!empty($teacherData)) {
                $teacher->update($teacherData);
            }

            return redirect()->route('teacher.profile.show')
                ->with('success', 'Profile updated successfully');

        } catch (\Exception $e) {
            return redirect()->route('teacher.profile.show')
                ->with('error', 'Error updating profile. Please try again.');
        }
    }
}