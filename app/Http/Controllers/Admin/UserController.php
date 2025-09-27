<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class UserController extends Controller
{
    public function __construct()
    {
        $this->middleware('admin');
    }

    public function index(Request $request)
    {
        try {
            $query = User::with(['student', 'teacher', 'staff']);

            // Search functionality
            if ($request->filled('search')) {
                $search = $request->search;
                $query->where('name', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%");
            }

            // Filter by user type
            if ($request->filled('user_type')) {
                $query->where('user_type', $request->user_type);
            }

            // Filter by status
            if ($request->filled('status')) {
                $query->where('is_active', $request->status === 'active');
            }

            $users = $query->orderBy('created_at', 'desc')->paginate(15);

            // Statistics
            $stats = [
                'total_users' => User::count(),
                'active_users' => User::where('is_active', true)->count(),
                'students' => User::where('user_type', 'student')->count(),
                'teachers' => User::where('user_type', 'teacher')->count(),
                'staff' => User::where('user_type', 'staff')->count(),
                'admins' => User::where('user_type', 'admin')->count(),
                'finance' => User::where('user_type', 'finance')->count(),
            ];

            return view('admin.users.index', compact('users', 'stats'));
        } catch (\Exception $e) {
            \Log::error('UserController index error: ' . $e->getMessage());
            $users = collect()->paginate(15);
            $stats = [
                'total_users' => 0,
                'active_users' => 0,
                'students' => 0,
                'teachers' => 0,
                'staff' => 0,
                'admins' => 0,
                'finance' => 0,
            ];
            return view('admin.users.index', compact('users', 'stats'));
        }
    }

    public function create()
    {
        return view('admin.users.create');
    }

    public function store(Request $request)
    {
        try {
            $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|string|email|max:255|unique:users',
                'password' => ['required', 'confirmed', Password::defaults()],
                'user_type' => 'required|in:admin,student,teacher,staff,finance',
                'is_active' => 'boolean',
            ]);

            User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'user_type' => $request->user_type,
                'is_active' => $request->boolean('is_active', true),
            ]);

            return redirect()->route('admin.users.index')
                ->with('success', 'User created successfully!');
        } catch (\Exception $e) {
            \Log::error('UserController store error: ' . $e->getMessage());
            return redirect()->back()
                ->withInput()
                ->with('error', 'Failed to create user. Please try again.');
        }
    }

    public function show(User $user)
    {
        try {
            $user->load(['student', 'teacher', 'staff']);
            return view('admin.users.show', compact('user'));
        } catch (\Exception $e) {
            \Log::error('UserController show error: ' . $e->getMessage());
            return redirect()->route('admin.users.index')
                ->with('error', 'User not found.');
        }
    }

    public function edit(User $user)
    {
        try {
            return view('admin.users.edit', compact('user'));
        } catch (\Exception $e) {
            \Log::error('UserController edit error: ' . $e->getMessage());
            return redirect()->route('admin.users.index')
                ->with('error', 'User not found.');
        }
    }

    public function update(Request $request, User $user)
    {
        try {
            $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
                'user_type' => 'required|in:admin,student,teacher,staff,finance',
                'is_active' => 'boolean',
            ]);

            $user->update([
                'name' => $request->name,
                'email' => $request->email,
                'user_type' => $request->user_type,
                'is_active' => $request->boolean('is_active', true),
            ]);

            return redirect()->route('admin.users.index')
                ->with('success', 'User updated successfully!');
        } catch (\Exception $e) {
            \Log::error('UserController update error: ' . $e->getMessage());
            return redirect()->back()
                ->withInput()
                ->with('error', 'Failed to update user. Please try again.');
        }
    }

    public function destroy(User $user)
    {
        try {
            // Prevent deleting the last admin
            if ($user->user_type === 'admin' && User::where('user_type', 'admin')->count() <= 1) {
                return redirect()->back()
                    ->with('error', 'Cannot delete the last admin user.');
            }

            $user->delete();

            return redirect()->route('admin.users.index')
                ->with('success', 'User deleted successfully!');
        } catch (\Exception $e) {
            \Log::error('UserController destroy error: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Failed to delete user. Please try again.');
        }
    }

    public function resetPassword(Request $request, User $user)
    {
        try {
            $request->validate([
                'password' => ['required', 'confirmed', Password::defaults()],
            ]);

            $user->update([
                'password' => Hash::make($request->password),
            ]);

            return redirect()->back()
                ->with('success', 'Password reset successfully!');
        } catch (\Exception $e) {
            \Log::error('UserController resetPassword error: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Failed to reset password. Please try again.');
        }
    }
}
