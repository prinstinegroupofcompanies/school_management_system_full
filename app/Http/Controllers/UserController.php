<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index()
    {
        return view('users.index');
    }

    public function create()
    {
        return view('users.create');
    }

    public function store(Request $request)
    {
        // Placeholder for user creation
        return redirect()->route('users.index')->with('success', 'User created successfully');
    }

    public function show($id)
    {
        return view('users.show', compact('id'));
    }

    public function edit($id)
    {
        return view('users.edit', compact('id'));
    }

    public function update(Request $request, $id)
    {
        // Placeholder for user update
        return redirect()->route('users.index')->with('success', 'User updated successfully');
    }

    public function destroy($id)
    {
        // Placeholder for user deletion
        return redirect()->route('users.index')->with('success', 'User deleted successfully');
    }

    public function resetAllPasswords(Request $request)
    {
        // Placeholder for password reset
        return redirect()->route('users.index')->with('success', 'All passwords reset successfully');
    }

    public function profile()
    {
        try {
            $user = auth()->user();
            return view('users.profile', compact('user'));
        } catch (\Exception $e) {
            \Log::error('UserController profile error: ' . $e->getMessage());
            return redirect()->route('dashboard')->with('error', 'Unable to load profile.');
        }
    }

    public function myProfile()
    {
        try {
            $user = auth()->user();
            return view('users.profile', compact('user'));
        } catch (\Exception $e) {
            \Log::error('UserController myProfile error: ' . $e->getMessage());
            return redirect()->route('dashboard')->with('error', 'Unable to load profile.');
        }
    }

    public function updateProfile(Request $request)
    {
        try {
            $user = auth()->user();
            
            $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            ]);

            $user->update([
                'name' => $request->name,
                'email' => $request->email,
            ]);

            return redirect()->route('users.profile')->with('success', 'Profile updated successfully!');
        } catch (\Exception $e) {
            \Log::error('UserController updateProfile error: ' . $e->getMessage());
            return redirect()->back()->withInput()->with('error', 'Failed to update profile. Please try again.');
        }
    }
}