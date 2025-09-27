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
            $teacher = $user->teacher;
            
            if (!$teacher) {
                return redirect()->route('teacher.dashboard')
                    ->with('error', 'Teacher profile not found. Please contact administrator.');
            }

            // Get teacher statistics
            $stats = [
                'classes_taught' => \App\Models\ClassRoom::where('teacher_id', $teacher->id)->count(),
                'total_students' => \App\Models\Student::whereIn('class_id', 
                    \App\Models\ClassRoom::where('teacher_id', $teacher->id)->pluck('id')
                )->count(),
                'subjects_taught' => \App\Models\Subject::whereHas('grades', function($query) use ($teacher) {
                    $query->where('teacher_id', $teacher->id);
                })->distinct()->count(),
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
            $user->update([
                'name' => $request->name,
                'email' => $request->email,
            ]);

            // Update teacher information
            $teacherData = [
                'phone' => $request->phone,
                'address' => $request->address,
                'bio' => $request->bio,
            ];

            // Handle profile photo upload
            if ($request->hasFile('profile_photo')) {
                // Delete old photo if exists
                if ($user->profile_photo) {
                    Storage::delete($user->profile_photo);
                }
                
                $path = $request->file('profile_photo')->store('profile_photos', 'public');
                $user->update(['profile_photo' => $path]);
            }

            $teacher->update($teacherData);

            return redirect()->route('teacher.profile.show')
                ->with('success', 'Profile updated successfully');

        } catch (\Exception $e) {
            return redirect()->route('teacher.profile.show')
                ->with('error', 'Error updating profile. Please try again.');
        }
    }
}