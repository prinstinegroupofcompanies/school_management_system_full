<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Student;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class SettingsController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        
        try {
            $student = $user->student;
        } catch (\Exception $e) {
            // Handle case where students table doesn't exist
            return redirect()->route('student.dashboard')
                ->with('error', 'Student profile not available. Please contact administrator.');
        }
        
        if (!$student) {
            return redirect()->route('student.dashboard')
                ->with('error', 'Student record not found. Please contact administrator.');
        }

        return view('student.settings.index', compact('user', 'student'));
    }
    
    public function profile()
    {
        $user = auth()->user();
        
        try {
            $student = $user->student;
        } catch (\Exception $e) {
            return redirect()->route('student.dashboard')
                ->with('error', 'Student profile not available. Please contact administrator.');
        }
        
        if (!$student) {
            return redirect()->route('student.dashboard')
                ->with('error', 'Student record not found. Please contact administrator.');
        }

        return view('student.profile', compact('user', 'student'));
    }

    public function updateProfile(Request $request)
    {
        $user = auth()->user();
        
        try {
            $student = $user->student;
        } catch (\Exception $e) {
            return redirect()->route('student.dashboard')
                ->with('error', 'Student profile not available. Please contact administrator.');
        }
        
        if (!$student) {
            return redirect()->route('student.dashboard')
                ->with('error', 'Student record not found. Please contact administrator.');
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:500',
            'emergency_contact' => 'nullable|string|max:255',
            'emergency_phone' => 'nullable|string|max:20',
        ]);

        // Update user information
        $user->update([
            'name' => $request->name,
            'email' => $request->email,
        ]);

        // Update student information
        $student->update([
            'phone' => $request->phone,
            'address' => $request->address,
            'emergency_contact' => $request->emergency_contact,
            'emergency_phone' => $request->emergency_phone,
        ]);

        return redirect()->route('student.settings.index')
            ->with('success', 'Profile updated successfully!');
    }

    public function changePassword(Request $request)
    {
        $user = auth()->user();
        
        $request->validate([
            'current_password' => 'required',
            'password' => ['required', 'confirmed', Password::defaults()],
        ]);

        if (!Hash::check($request->current_password, $user->password)) {
            return back()->withErrors(['current_password' => 'The current password is incorrect.']);
        }

        $user->update([
            'password' => Hash::make($request->password),
        ]);

        return redirect()->route('student.settings.index')
            ->with('success', 'Password changed successfully!');
    }

    public function notifications()
    {
        $user = auth()->user();
        
        try {
            $student = $user->student;
        } catch (\Exception $e) {
            return redirect()->route('student.dashboard')
                ->with('error', 'Student profile not available. Please contact administrator.');
        }
        
        if (!$student) {
            return redirect()->route('student.dashboard')
                ->with('error', 'Student record not found. Please contact administrator.');
        }

        // Mock notification preferences
        $preferences = [
            'email_notifications' => true,
            'sms_notifications' => false,
            'push_notifications' => true,
            'exam_reminders' => true,
            'assignment_reminders' => true,
            'fee_reminders' => true,
            'attendance_alerts' => true,
        ];

        return view('student.settings.notifications', compact('preferences'));
    }

    public function updateNotifications(Request $request)
    {
        $user = auth()->user();
        
        try {
            $student = $user->student;
        } catch (\Exception $e) {
            return redirect()->route('student.dashboard')
                ->with('error', 'Student profile not available. Please contact administrator.');
        }
        
        if (!$student) {
            return redirect()->route('student.dashboard')
                ->with('error', 'Student record not found. Please contact administrator.');
        }

        $request->validate([
            'email_notifications' => 'boolean',
            'sms_notifications' => 'boolean',
            'push_notifications' => 'boolean',
            'exam_reminders' => 'boolean',
            'assignment_reminders' => 'boolean',
            'fee_reminders' => 'boolean',
            'attendance_alerts' => 'boolean',
        ]);

        // Mock notification preferences update
        // In a real application, this would be stored in the database

        return redirect()->route('student.settings.notifications')
            ->with('success', 'Notification preferences updated successfully!');
    }

    public function privacy()
    {
        $user = auth()->user();
        
        try {
            $student = $user->student;
        } catch (\Exception $e) {
            return redirect()->route('student.dashboard')
                ->with('error', 'Student profile not available. Please contact administrator.');
        }
        
        if (!$student) {
            return redirect()->route('student.dashboard')
                ->with('error', 'Student record not found. Please contact administrator.');
        }

        // Mock privacy settings
        $privacySettings = [
            'profile_visibility' => 'classmates',
            'contact_info_visibility' => 'friends',
            'academic_info_visibility' => 'private',
            'allow_messages' => true,
            'show_online_status' => true,
        ];

        return view('student.settings.privacy', compact('privacySettings'));
    }

    public function updatePrivacy(Request $request)
    {
        $user = auth()->user();
        
        try {
            $student = $user->student;
        } catch (\Exception $e) {
            return redirect()->route('student.dashboard')
                ->with('error', 'Student profile not available. Please contact administrator.');
        }
        
        if (!$student) {
            return redirect()->route('student.dashboard')
                ->with('error', 'Student record not found. Please contact administrator.');
        }

        $request->validate([
            'profile_visibility' => 'required|in:public,classmates,friends,private',
            'contact_info_visibility' => 'required|in:public,classmates,friends,private',
            'academic_info_visibility' => 'required|in:public,classmates,friends,private',
            'allow_messages' => 'boolean',
            'show_online_status' => 'boolean',
        ]);

        // Mock privacy settings update
        // In a real application, this would be stored in the database

        return redirect()->route('student.settings.privacy')
            ->with('success', 'Privacy settings updated successfully!');
    }
    
    public function updatePassword(Request $request)
    {
        $user = auth()->user();
        
        try {
            $student = $user->student;
        } catch (\Exception $e) {
            return redirect()->route('student.dashboard')
                ->with('error', 'Student profile not available. Please contact administrator.');
        }
        
        if (!$student) {
            return redirect()->route('student.dashboard')
                ->with('error', 'Student record not found. Please contact administrator.');
        }

        $request->validate([
            'current_password' => 'required',
            'password' => ['required', 'confirmed', Password::defaults()],
        ]);

        // Check current password
        if (!Hash::check($request->current_password, $user->password)) {
            return back()->withErrors(['current_password' => 'The current password is incorrect.']);
        }

        // Update password
        $user->update([
            'password' => Hash::make($request->password),
        ]);

        return redirect()->route('student.settings.index')
            ->with('success', 'Password updated successfully!');
    }
}
