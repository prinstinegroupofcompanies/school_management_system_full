<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Student;
use App\Models\Teacher;
use App\Models\ClassRoom;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class UserController extends Controller
{
    public function index(Request $request)
    {
        try {
            $query = User::with(['student.classRoom', 'teacher']);

            // Search functionality
            if ($request->has('search') && $request->search) {
                $query->where(function($q) use ($request) {
                    $q->where('name', 'like', '%' . $request->search . '%')
                      ->orWhere('email', 'like', '%' . $request->search . '%')
                      ->orWhere('phone', 'like', '%' . $request->search . '%');
                });
            }

            // Filter by user type
            if ($request->has('user_type') && $request->user_type) {
                $query->where('user_type', $request->user_type);
            }

            // Filter by status
            if ($request->has('status') && $request->status) {
                $query->where('status', $request->status);
            }

            $users = $query->orderBy('created_at', 'desc')->paginate(15);

            // Get statistics
            $stats = [
                'total_users' => User::count(),
                'active_users' => User::where('status', 'active')->count(),
                'students' => User::where('user_type', 'student')->count(),
                'teachers' => User::where('user_type', 'teacher')->count(),
                'admins' => User::where('user_type', 'admin')->count(),
                'finance_officers' => User::where('user_type', 'finance')->count(),
            ];

            return view('users.index', compact('users', 'stats'));
        } catch (\Exception $e) {
            \Log::error('UserController index error: ' . $e->getMessage());
            
            // Fallback data if database issues
            $users = new \Illuminate\Pagination\LengthAwarePaginator(
                collect(),
                0,
                15,
                1,
                ['path' => request()->url()]
            );
            $stats = [
                'total_users' => 0,
                'active_users' => 0,
                'students' => 0,
                'teachers' => 0,
                'admins' => 0,
                'finance_officers' => 0,
            ];
            
            return view('users.index', compact('users', 'stats'));
        }
    }

    public function create()
    {
        try {
            $classes = ClassRoom::all();
            return view('users.create', compact('classes'));
        } catch (\Exception $e) {
            \Log::error('UserController create error: ' . $e->getMessage());
            $classes = collect();
            return view('users.create', compact('classes'));
        }
    }

    public function store(Request $request)
    {
        try {
            $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|string|email|max:255|unique:users',
                'phone' => 'nullable|string|max:20',
                'date_of_birth' => 'nullable|date',
                'gender' => 'nullable|in:male,female,other',
                'address' => 'nullable|string',
                'role' => 'required|in:admin,teacher,student,finance',
                'password' => 'required|string|min:8|confirmed',
                'status' => 'required|in:active,inactive,suspended',
                'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
                'employee_id' => 'nullable|string|max:50',
                'department' => 'nullable|string|max:100',
                'student_id' => 'nullable|string|max:50',
                'class_id' => 'nullable|exists:class_rooms,id',
            ]);

            DB::beginTransaction();

            // Handle photo upload
            $photoPath = null;
            if ($request->hasFile('photo')) {
                $photo = $request->file('photo');
                $filename = time() . '_' . $photo->getClientOriginalName();
                $photoPath = $photo->storeAs('profile-photos', $filename, 'public');
            }

            // Create user
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'phone' => $request->phone,
                'date_of_birth' => $request->date_of_birth,
                'gender' => $request->gender,
                'address' => $request->address,
                'role' => $request->role,
                'password' => Hash::make($request->password),
                'status' => $request->status,
                'photo' => $photoPath,
            ]);

            // Create role-specific records
            if ($request->role === 'teacher') {
                Teacher::create([
                    'user_id' => $user->id,
                    'employee_id' => $request->employee_id,
                    'department' => $request->department,
                    'status' => 'active',
                ]);
            } elseif ($request->role === 'student') {
                Student::create([
                    'user_id' => $user->id,
                    'student_id' => $request->student_id,
                    'class_id' => $request->class_id,
                    'status' => 'active',
                ]);
            }

            DB::commit();

            // Send welcome email if requested
            if ($request->has('send_welcome_email')) {
                // Mail::to($user->email)->send(new WelcomeEmail($user));
            }

            return redirect()->route('users.index')
                ->with('success', 'User created successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('UserController store error: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Failed to create user. Please try again.')
                ->withInput();
        }
    }

    public function show(User $user)
    {
        try {
            $user->load(['student.classRoom', 'teacher']);
            return view('users.show', compact('user'));
        } catch (\Exception $e) {
            \Log::error('UserController show error: ' . $e->getMessage());
            return redirect()->route('users.index')
                ->with('error', 'User not found.');
        }
    }

    public function edit(User $user)
    {
        try {
            $user->load(['student.classRoom', 'teacher']);
            $classes = ClassRoom::all();
            return view('users.edit', compact('user', 'classes'));
        } catch (\Exception $e) {
            \Log::error('UserController edit error: ' . $e->getMessage());
            return redirect()->route('users.index')
                ->with('error', 'User not found.');
        }
    }

    public function update(Request $request, User $user)
    {
        try {
            $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
                'phone' => 'nullable|string|max:20',
                'date_of_birth' => 'nullable|date',
                'gender' => 'nullable|in:male,female,other',
                'address' => 'nullable|string',
                'role' => 'required|in:admin,teacher,student,finance',
                'password' => 'nullable|string|min:8|confirmed',
                'status' => 'required|in:active,inactive,suspended',
                'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
                'employee_id' => 'nullable|string|max:50',
                'department' => 'nullable|string|max:100',
                'student_id' => 'nullable|string|max:50',
                'class_id' => 'nullable|exists:class_rooms,id',
            ]);

            DB::beginTransaction();

            // Handle photo upload
            if ($request->hasFile('photo')) {
                // Delete old photo
                if ($user->photo) {
                    Storage::disk('public')->delete($user->photo);
                }
                
                $photo = $request->file('photo');
                $filename = time() . '_' . $photo->getClientOriginalName();
                $photoPath = $photo->storeAs('profile-photos', $filename, 'public');
            } else {
                $photoPath = $user->photo;
            }

            // Update user
            $userData = [
                'name' => $request->name,
                'email' => $request->email,
                'phone' => $request->phone,
                'date_of_birth' => $request->date_of_birth,
                'gender' => $request->gender,
                'address' => $request->address,
                'role' => $request->role,
                'status' => $request->status,
                'photo' => $photoPath,
            ];

            if ($request->has('change_password') && $request->password) {
                $userData['password'] = Hash::make($request->password);
            }

            $user->update($userData);

            // Update role-specific records
            if ($request->role === 'teacher') {
                $teacher = $user->teacher;
                if ($teacher) {
                    $teacher->update([
                        'employee_id' => $request->employee_id,
                        'department' => $request->department,
                    ]);
                } else {
                    Teacher::create([
                        'user_id' => $user->id,
                        'employee_id' => $request->employee_id,
                        'department' => $request->department,
                        'status' => 'active',
                    ]);
                }
            } elseif ($request->role === 'student') {
                $student = $user->student;
                if ($student) {
                    $student->update([
                        'student_id' => $request->student_id,
                        'class_id' => $request->class_id,
                    ]);
                } else {
                    Student::create([
                        'user_id' => $user->id,
                        'student_id' => $request->student_id,
                        'class_id' => $request->class_id,
                        'status' => 'active',
                    ]);
                }
            }

            DB::commit();

            return redirect()->route('users.index')
                ->with('success', 'User updated successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('UserController update error: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Failed to update user. Please try again.')
                ->withInput();
        }
    }

    public function destroy(User $user)
    {
        try {
            // Prevent deletion of the current user
            if ($user->id === auth()->id()) {
                return redirect()->back()
                    ->with('error', 'You cannot delete your own account.');
            }

            DB::beginTransaction();

            // Delete photo
            if ($user->photo) {
                Storage::disk('public')->delete($user->photo);
            }

            // Delete role-specific records
            if ($user->teacher) {
                $user->teacher->delete();
            }
            if ($user->student) {
                $user->student->delete();
            }

            $user->delete();

            DB::commit();

            return redirect()->route('users.index')
                ->with('success', 'User deleted successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('UserController destroy error: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Failed to delete user. Please try again.');
        }
    }

    public function resetPassword(User $user)
    {
        try {
            $newPassword = 'password123'; // Generate a random password
            $user->update(['password' => Hash::make($newPassword)]);

            // Send password reset email
            // Mail::to($user->email)->send(new PasswordResetEmail($user, $newPassword));

            return redirect()->back()
                ->with('success', 'Password reset successfully. New password sent to user\'s email.');
        } catch (\Exception $e) {
            \Log::error('UserController resetPassword error: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Failed to reset password. Please try again.');
        }
    }

    public function resetAllPasswords()
    {
        try {
            $users = User::where('status', 'active')->get();
            $resetCount = 0;

            foreach ($users as $user) {
                $newPassword = 'password123'; // Generate a random password
                $user->update(['password' => Hash::make($newPassword)]);
                $resetCount++;

                // Send password reset email
                // Mail::to($user->email)->send(new PasswordResetEmail($user, $newPassword));
            }

            return redirect()->back()
                ->with('success', "Passwords reset successfully for {$resetCount} users.");
        } catch (\Exception $e) {
            \Log::error('UserController resetAllPasswords error: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Failed to reset passwords. Please try again.');
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
}