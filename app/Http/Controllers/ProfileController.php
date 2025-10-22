<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rules\Password;

class ProfileController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display the user's profile.
     */
    public function show()
    {
        $user = auth()->user()->load(['student.class', 'teacher']);
        return view('profile.show', compact('user'));
    }

    /**
     * Show the form for editing the user's profile.
     */
    public function edit()
    {
        $user = auth()->user()->load(['student', 'teacher']);
        return view('profile.edit', compact('user'));
    }

    /**
     * Update the user's profile.
     */
    public function update(Request $request)
    {
        $user = auth()->user();

        try {
            $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
                'phone' => 'nullable|string|max:20',
                'date_of_birth' => 'nullable|date|before:today',
                'gender' => 'nullable|in:male,female,other',
                'address' => 'nullable|string|max:500',
                'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
                // Teacher specific fields
                'employee_id' => 'nullable|string|max:50',
                'department' => 'nullable|string|max:100',
                'qualification' => 'nullable|string|max:255',
                'experience_years' => 'nullable|integer|min:0|max:50',
            ]);

            DB::beginTransaction();

            // Handle photo upload
            $photoPath = $user->photo;
            if ($request->hasFile('photo')) {
                // Delete old photo if exists
                if ($user->photo && Storage::disk('public')->exists($user->photo)) {
                    Storage::disk('public')->delete($user->photo);
                }

                $photo = $request->file('photo');
                $filename = time() . '_' . $photo->getClientOriginalName();
                $photoPath = $photo->storeAs('profile-photos', $filename, 'public');
            }

            // Update user information
            $user->update([
                'name' => $request->name,
                'email' => $request->email,
                'phone' => $request->phone,
                'date_of_birth' => $request->date_of_birth,
                'gender' => $request->gender,
                'address' => $request->address,
                'photo' => $photoPath,
            ]);

            // Update teacher-specific information if user is a teacher
            if ($user->user_type === 'teacher' && $user->teacher) {
                $user->teacher->update([
                    'employee_id' => $request->employee_id,
                    'department' => $request->department,
                    'qualification' => $request->qualification,
                    'experience_years' => $request->experience_years,
                ]);
            }

            DB::commit();

            return redirect()->route('profile.show')
                ->with('success', 'Profile updated successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('ProfileController update error: ' . $e->getMessage());
            
            return redirect()->back()
                ->with('error', 'Failed to update profile. Please try again.')
                ->withInput();
        }
    }

    /**
     * Show the form for changing password.
     */
    public function showChangePasswordForm()
    {
        return view('profile.change-password');
    }

    /**
     * Change the user's password.
     */
    public function changePassword(Request $request)
    {
        $user = auth()->user();

        $request->validate([
            'current_password' => 'required|current_password',
            'password' => ['required', 'confirmed', Password::defaults()],
        ]);

        try {
            $user->update([
                'password' => Hash::make($request->password),
            ]);

            return redirect()->route('profile.show')
                ->with('success', 'Password changed successfully.');

        } catch (\Exception $e) {
            \Log::error('ProfileController changePassword error: ' . $e->getMessage());
            
            return redirect()->back()
                ->with('error', 'Failed to change password. Please try again.');
        }
    }
}
