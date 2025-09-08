<?php
namespace App\Http\Controllers\auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthenticatedSessionControll extends Controller
{
    public function store(Request $request)
    {
        // Debug: Log the request data
        \Log::info('Login attempt', [
            'email' => $request->email,
            'csrf_token' => $request->_token,
            'session_token' => csrf_token(),
            'session_id' => session()->getId()
        ]);

        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt($request->only('email', 'password'), $request->filled('remember'))) {
            $request->session()->regenerate();
            return $this->authenticated($request, Auth::user());
        }

        return back()->withErrors([
            'email' => 'Invalid credentials.',
        ]);
    }
    public function authenticated(Request $request, $user)
    {
        switch ($user->user_type) {
            case 'admin':
                return redirect('/admin/dashboard');
            case 'teacher':
                return redirect('/teacher/dashboard');
            case 'student':
                return redirect('/student/dashboard');
            case 'finance':
                return redirect('/finance/dashboard');
            default:
                return redirect('/dashboard');
        }
    }

    public function destroy(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        
        return redirect('/login')->with('success', 'You have been logged out successfully.');
    }
}