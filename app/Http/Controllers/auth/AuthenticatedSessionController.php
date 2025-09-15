<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create()
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (!Auth::attempt($request->only('email', 'password'), $request->boolean('remember'))) {
            throw ValidationException::withMessages([
                'email' => trans('auth.failed'),
            ]);
        }

        $request->session()->regenerate();

        return $this->authenticated($request, Auth::user());
    }

    /**
     * Handle an authenticated user.
     */
    protected function authenticated(Request $request, $user)
    {
        // Update last login time
        $user->update(['last_login_at' => now()]);

        // Redirect based on user type
        switch ($user->user_type) {
            case 'admin':
                return redirect()->intended('/admin/dashboard');
            case 'teacher':
                return redirect()->intended('/teacher/dashboard');
            case 'student':
                return redirect()->intended('/student/dashboard');
            case 'finance':
                return redirect()->intended('/finance/dashboard');
            case 'parent':
                return redirect()->intended('/parent/dashboard');
            default:
                return redirect()->intended('/dashboard');
        }
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request)
    {
        // Update last logout time
        if (Auth::check()) {
            Auth::user()->update(['last_logout_at' => now()]);
        }

        Auth::logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/login')->with('success', 'You have been logged out successfully.');
    }
}
