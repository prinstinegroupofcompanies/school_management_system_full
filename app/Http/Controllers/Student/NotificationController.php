<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Notifications\DatabaseNotification;

class NotificationController extends Controller
{
    public function __construct()
    {
        $this->middleware('student');
    }

    /**
     * Display student's notifications
     */
    public function index(Request $request)
    {
        $student = $request->user()->student;
        
        if (!$student) {
            return redirect()->route('student.dashboard')
                           ->withErrors(['error' => 'Student profile not found.']);
        }

        // Get notifications for the user
        $notifications = $request->user()->notifications()
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        // Mark all as read when viewing
        $request->user()->unreadNotifications()->update(['read_at' => now()]);

        return view('student.notifications.index', compact('notifications'));
    }

    /**
     * Show specific notification
     */
    public function show(Request $request, $notificationId)
    {
        $student = $request->user()->student;
        
        if (!$student) {
            return redirect()->route('student.dashboard')
                           ->withErrors(['error' => 'Student profile not found.']);
        }

        $notification = $request->user()->notifications()->findOrFail($notificationId);
        
        // Mark as read
        $notification->markAsRead();

        return view('student.notifications.show', compact('notification'));
    }

    /**
     * Mark notification as read
     */
    public function markAsRead(Request $request, $notificationId)
    {
        $notification = $request->user()->notifications()->findOrFail($notificationId);
        $notification->markAsRead();

        return response()->json(['success' => true]);
    }
}
