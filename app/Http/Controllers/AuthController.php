<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class AuthController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
            
            $user = Auth::user();
            
            // Check for role-based redirects first
            if ($user->hasRole('conductor_driver')) {
                return redirect()->intended(route('transport.driver.dashboard'));
            }
            
            if ($user->hasRole('registrar')) {
                return redirect()->intended(route('registrar.enrollment.index'));
            }
            
            // Redirect based on user type
            switch ($user->user_type) {
                case 'admin':
                    return redirect()->route('admin.dashboard');
                case 'teacher':
                    return redirect()->route('teacher.dashboard');
                case 'student':
                    return redirect()->route('student.dashboard');
                case 'finance':
                    return redirect()->route('finance.dashboard');
                case 'parent':
                    return redirect()->route('parent.dashboard');
                case 'librarian':
                    return redirect()->route('library.index');
                case 'staff':
                    if ($user->hasRole('conductor_driver')) {
                        return redirect()->route('transport.driver.dashboard');
                    }
                    return redirect()->route('library.index');
                default:
                    return redirect()->route('dashboard');
            }
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->onlyInput('email');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/');
    }
}
