<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $query = User::query();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if ($request->filled('user_type')) {
            $query->where('user_type', $request->user_type);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $users = $query->paginate(15);
        $userTypes = ['admin', 'teacher', 'student', 'finance'];

        return view('users.index', compact('users', 'userTypes'));
    }

    public function create()
    {
        $userTypes = ['admin', 'teacher', 'student', 'finance'];
        return view('users.create', compact('userTypes'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'phone' => 'nullable|string|max:20',
            'password' => 'required|string|min:8|confirmed',
            'user_type' => 'required|in:admin,teacher,student,finance',
            'status' => 'nullable|in:active,inactive',
            'locale' => 'nullable|string|max:5',
            'timezone' => 'nullable|string|max:50',
        ]);

        try {
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'phone' => $request->phone,
                'password' => Hash::make($request->password),
                'user_type' => $request->user_type,
                'status' => $request->status ?? 'active',
                'locale' => $request->locale ?? config('app.locale'),
                'timezone' => $request->timezone ?? config('app.timezone'),
            ]);

            return redirect()->route('users.index')
                ->with('success', 'User created successfully');
        } catch (\Exception $e) {
            return back()->withInput()
                ->with('error', 'Failed to create user: ' . $e->getMessage());
        }
    }

    public function show(User $user)
    {
        return view('users.show', compact('user'));
    }

    public function edit(User $user)
    {
        $userTypes = ['admin', 'teacher', 'student', 'finance'];
        return view('users.edit', compact('user', 'userTypes'));
    }

    public function update(Request $request, User $user)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'phone' => 'nullable|string|max:20',
            'user_type' => 'required|in:admin,teacher,student,finance',
            'status' => 'nullable|in:active,inactive',
            'locale' => 'nullable|string|max:5',
            'timezone' => 'nullable|string|max:50',
        ]);

        try {
            $user->update([
                'name' => $request->name,
                'email' => $request->email,
                'phone' => $request->phone,
                'user_type' => $request->user_type,
                'status' => $request->status,
                'locale' => $request->locale,
                'timezone' => $request->timezone,
            ]);

            return redirect()->route('users.index')
                ->with('success', 'User updated successfully');
        } catch (\Exception $e) {
            return back()->withInput()
                ->with('error', 'Failed to update user: ' . $e->getMessage());
        }
    }

    public function destroy(User $user)
    {
        try {
            if ($user->id === auth()->id()) {
                return back()->with('error', 'You cannot delete your own account');
            }

            $user->delete();
            return redirect()->route('users.index')
                ->with('success', 'User deleted successfully');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to delete user: ' . $e->getMessage());
        }
    }

    // Admin bulk password reset (sets a temporary password for all users)
    public function resetAllPasswords(Request $request)
    {
        if ((auth()->user()->user_type ?? null) !== 'admin') {
            abort(403, 'Admin privileges required');
        }

        $request->validate([
            'new_password' => 'required|string|min:8',
        ]);

        try {
            $count = 0;
            foreach (User::cursor() as $u) {
                // Skip currently authenticated admin for safety
                if ($u->id === auth()->id()) {
                    continue;
                }
                $u->password = \Hash::make($request->new_password);
                $u->setRememberToken(\Str::random(60));
                $u->save();
                $count++;
            }

            return redirect()->route('users.index')
                ->with('success', "Passwords reset for {$count} users.");
        } catch (\Throwable $e) {
            return back()->with('error', 'Failed to reset passwords: ' . $e->getMessage());
        }
    }

    public function profile(User $user = null)
    {
        // If no user is provided, use the authenticated user
        if (!$user) {
            $user = auth()->user();
        } else {
            if ($user->id !== auth()->id()) {
                abort(403, 'You can only view your own profile');
            }
        }

        // Check if user is a student and route accordingly
        if ($user->user_type === 'student') {
            return view('student.profile', compact('user'));
        }

        return view('users.profile', compact('user'));
    }

    public function editProfile(User $user = null)
    {
        // If no user is provided, use the authenticated user
        if (!$user) {
            $user = auth()->user();
        } else {
            if ($user->id !== auth()->id()) {
                abort(403, 'You can only edit your own profile');
            }
        }

        // Check if user is a student and route accordingly
        if ($user->user_type === 'student') {
            return view('student.edit-profile', compact('user'));
        }

        return view('users.edit-profile', compact('user'));
    }

    public function updateProfile(Request $request, User $user = null)
    {
        // If no user is provided, use the authenticated user
        if (!$user) {
            $user = auth()->user();
        } else {
            if ($user->id !== auth()->id()) {
                abort(403, 'You can only edit your own profile');
            }
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:500',
            'city' => 'nullable|string|max:120',
            'country' => 'nullable|string|max:120',
            'locale' => 'nullable|string|max:5',
            'timezone' => 'nullable|string|max:50',
            'profile_photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        try {
            $updateData = $request->only(['name', 'phone', 'address', 'city', 'country', 'locale', 'timezone']);
            
            // Handle profile photo upload
            if ($request->hasFile('profile_photo')) {
                // Delete old profile photo if exists
                if ($user->profile_photo && \Storage::disk('public')->exists($user->profile_photo)) {
                    \Storage::disk('public')->delete($user->profile_photo);
                }
                
                // Store new profile photo
                $profilePhotoPath = $request->file('profile_photo')->store('profile-photos', 'public');
                $updateData['profile_photo'] = $profilePhotoPath;
            }
            
            $user->update($updateData);

            // Redirect to self profile for non-admins
            if ($user->user_type === 'student') {
                return redirect()->route('student.profile')
                    ->with('success', 'Profile updated successfully');
            }
            if ($user->id === auth()->id() && auth()->user()->user_type !== 'admin') {
                return redirect()->route('me.profile')
                    ->with('success', 'Profile updated successfully');
            }

            return redirect()->route('users.profile', $user)
                ->with('success', 'Profile updated successfully');
        } catch (\Exception $e) {
            return back()->withInput()
                ->with('error', 'Failed to update profile: ' . $e->getMessage());
        }
    }

    public function changePasswordForm(User $user = null)
    {
        if (!$user) {
            $user = auth()->user();
        } else if ($user->id !== auth()->id() && (auth()->user()->user_type ?? null) !== 'admin') {
            abort(403, 'You can only change your own password');
        }

        return view('users.change-password', compact('user'));
    }

    public function changePassword(Request $request, User $user = null)
    {
        if (!$user) {
            $user = auth()->user();
        } else if ($user->id !== auth()->id() && (auth()->user()->user_type ?? null) !== 'admin') {
            abort(403, 'You can only change your own password');
        }

        // Admin can reset without current password
        $isAdminActingOnAnother = (auth()->user()->user_type ?? null) === 'admin' && $user->id !== auth()->id();

        $rules = [
            'new_password' => 'required|string|min:8|confirmed',
        ];
        if (!$isAdminActingOnAnother) {
            $rules['current_password'] = 'required|string';
        }
        $request->validate($rules);

        try {
            if (!$isAdminActingOnAnother) {
                if (!Hash::check($request->current_password, $user->password)) {
                    return back()->withErrors(['current_password' => 'Current password is incorrect']);
                }
            }

            $user->update(['password' => Hash::make($request->new_password)]);

            if ($isAdminActingOnAnother) {
                return redirect()->route('users.show', $user)
                    ->with('success', 'Password reset successfully');
            }

            if ($user->id === auth()->id() && auth()->user()->user_type !== 'admin') {
                return redirect()->route('me.profile')
                    ->with('success', 'Password changed successfully');
            }

            return redirect()->route('users.profile', $user)
                ->with('success', 'Password changed successfully');
        } catch (\Exception $e) {
            return back()->withInput()
                ->with('error', 'Failed to change password: ' . $e->getMessage());
        }
    }
}
