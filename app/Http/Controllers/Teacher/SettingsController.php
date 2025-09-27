<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class SettingsController extends Controller
{
    public function __construct()
    {
        $this->middleware('teacher');
    }

    /**
     * Display teacher settings
     */
    public function index()
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
                ['description' => 'Profile updated', 'created_at' => $teacher->updated_at],
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
                ['description' => 'Settings unavailable', 'created_at' => now()],
            ]);
        }

        return view('teacher.settings.index', compact('user', 'teacher', 'stats', 'recentActivities'));
    }

    /**
     * Update password
     */
    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'password' => 'required|min:8|confirmed',
        ]);

        $user = auth()->user();

        if (!Hash::check($request->current_password, $user->password)) {
            return back()->withErrors(['current_password' => 'Current password is incorrect.']);
        }

        $user->update([
            'password' => Hash::make($request->password),
        ]);

        return back()->with('success', 'Password updated successfully.');
    }

    /**
     * Update notification preferences
     */
    public function updateNotifications(Request $request)
    {
        $request->validate([
            'email_notifications' => 'boolean',
            'sms_notifications' => 'boolean',
            'push_notifications' => 'boolean',
        ]);

        $user = auth()->user();
        $teacher = $user->teacher;

        if ($teacher) {
            $teacher->update([
                'email_notifications' => $request->boolean('email_notifications'),
                'sms_notifications' => $request->boolean('sms_notifications'),
                'push_notifications' => $request->boolean('push_notifications'),
            ]);
        }

        return back()->with('success', 'Notification preferences updated successfully.');
    }

    /**
     * Update privacy settings
     */
    public function updatePrivacy(Request $request)
    {
        $request->validate([
            'profile_visibility' => 'required|in:public,private,friends',
            'show_email' => 'boolean',
            'show_phone' => 'boolean',
        ]);

        $user = auth()->user();
        $teacher = $user->teacher;

        if ($teacher) {
            $teacher->update([
                'profile_visibility' => $request->profile_visibility,
                'show_email' => $request->boolean('show_email'),
                'show_phone' => $request->boolean('show_phone'),
            ]);
        }

        return back()->with('success', 'Privacy settings updated successfully.');
    }

    /**
     * Delete account
     */
    public function deleteAccount(Request $request)
    {
        $request->validate([
            'password' => 'required',
            'confirmation' => 'required|in:DELETE',
        ]);

        $user = auth()->user();

        if (!Hash::check($request->password, $user->password)) {
            return back()->withErrors(['password' => 'Password is incorrect.']);
        }

        // Delete profile photo if exists
        if ($user->profile_photo) {
            Storage::delete($user->profile_photo);
        }

        // Delete teacher record
        $teacher = $user->teacher;
        if ($teacher) {
            $teacher->delete();
        }

        // Delete user account
        $user->delete();

        return redirect()->route('login')->with('success', 'Your account has been deleted successfully.');
    }
}