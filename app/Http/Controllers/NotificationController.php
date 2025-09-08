<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use App\Services\ExamNotificationService;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    protected $notificationService;

    public function __construct(ExamNotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    /**
     * Display notifications for the authenticated user
     */
    public function index()
    {
        $user = auth()->user();
        
        $notifications = Notification::where('user_id', $user->id)
            ->where('status', 'sent')
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        $unreadCount = Notification::where('user_id', $user->id)
            ->whereNull('read_at')
            ->where('status', 'sent')
            ->count();

        return view('notifications.index', compact('notifications', 'unreadCount'));
    }

    /**
     * Mark a specific notification as read
     */
    public function markAsRead(Request $request, Notification $notification)
    {
        $user = auth()->user();
        
        if ($notification->user_id !== $user->id) {
            abort(403, 'Unauthorized access to notification.');
        }

        $this->notificationService->markAsRead($notification->id);

        return response()->json([
            'success' => true,
            'message' => 'Notification marked as read.'
        ]);
    }

    /**
     * Mark all notifications as read for the authenticated user
     */
    public function markAllAsRead(Request $request)
    {
        $user = auth()->user();
        
        $this->notificationService->markAllAsRead($user->id);

        return response()->json([
            'success' => true,
            'message' => 'All notifications marked as read.'
        ]);
    }

    /**
     * Get unread notifications count (for AJAX requests)
     */
    public function getUnreadCount()
    {
        $user = auth()->user();
        
        $count = $this->notificationService->getUnreadNotifications($user->id)->count();

        return response()->json([
            'count' => $count
        ]);
    }
}