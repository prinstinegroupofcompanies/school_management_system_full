<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class ImpersonateController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware(function ($request, $next) {
            if (!auth()->user()->hasRole('super_admin')) {
                abort(403, 'Only Super Admins can impersonate users.');
            }
            return $next($request);
        });
    }

    /**
     * Start impersonating a user.
     */
    public function start(User $user)
    {
        // Store original user ID
        Session::put('impersonate_id', auth()->id());
        
        // Log in as the target user
        auth()->login($user);

        return redirect()->route('dashboard')
            ->with('success', 'Now impersonating ' . $user->name);
    }

    /**
     * Stop impersonating and return to original user.
     */
    public function stop()
    {
        if (!Session::has('impersonate_id')) {
            return redirect()->route('dashboard')
                ->with('error', 'Not currently impersonating anyone.');
        }

        $originalUserId = Session::get('impersonate_id');
        $originalUser = User::findOrFail($originalUserId);

        // Clear impersonation session
        Session::forget('impersonate_id');

        // Log back in as original user
        auth()->login($originalUser);

        return redirect()->route('dashboard')
            ->with('success', 'Returned to your account.');
    }
}
