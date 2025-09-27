<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use App\Models\User;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class NotificationController extends Controller
{
    protected $notificationService;

    public function __construct()
    {
        $this->middleware('admin');
        $this->notificationService = new NotificationService();
    }

    public function index(Request $request)
    {
        try {
            $query = Notification::with('user');

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where('title', 'like', "%{$search}%")
                  ->orWhere('message', 'like', "%{$search}%")
                  ->orWhereHas('user', function ($q) use ($search) {
                      $q->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                  });
        }

        // Filter by type
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by delivery method
        if ($request->filled('delivery_method')) {
            $query->where('delivery_method', $request->delivery_method);
        }

        $notifications = $query->orderBy('created_at', 'desc')->paginate(15);

        // Statistics
        $stats = $this->notificationService->getNotificationStats();

        return view('admin.notifications.index', compact('notifications', 'stats'));
        } catch (\Exception $e) {
            \Log::error('NotificationController index error: ' . $e->getMessage());
            $notifications = collect()->paginate(15);
            $stats = [
                'total' => 0,
                'pending' => 0,
                'delivered' => 0,
                'failed' => 0
            ];
            return view('admin.notifications.index', compact('notifications', 'stats'));
        }
    }

    public function create()
    {
        $users = User::whereIn('user_type', ['student', 'teacher', 'staff'])->get();
        $userGroups = [
            'all_students' => 'All Students',
            'all_teachers' => 'All Teachers',
            'all_staff' => 'All Staff',
            'specific_users' => 'Specific Users'
        ];

        return view('admin.notifications.create', compact('users', 'userGroups'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'message' => 'required|string',
            'type' => 'required|string',
            'delivery_method' => 'required|in:email,sms,both',
            'user_selection' => 'required|in:all_students,all_teachers,all_staff,specific_users',
            'user_ids' => 'required_if:user_selection,specific_users|array',
            'user_ids.*' => 'exists:users,id',
            'scheduled_at' => 'nullable|date|after:now'
        ]);

        $userIds = $this->getUserIds($request->user_selection, $request->user_ids ?? []);

        if (empty($userIds)) {
            return back()->withErrors(['error' => 'No users selected for notification.']);
        }

        $successCount = 0;
        $failedCount = 0;

        foreach ($userIds as $userId) {
            $user = User::find($userId);
            if (!$user) continue;

            // Handle "both" delivery method by creating separate notifications
            $deliveryMethods = $request->delivery_method === 'both' ? ['email', 'sms'] : [$request->delivery_method];
            
            foreach ($deliveryMethods as $method) {
                // Create notification record in database
                $notification = Notification::create([
                    'user_id' => $user->id,
                    'title' => $request->title,
                    'message' => $request->message,
                    'type' => $request->type,
                    'delivery_method' => $method,
                    'status' => 'pending',
                    'scheduled_at' => $request->scheduled_at ? now()->parse($request->scheduled_at) : now(),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                try {
                    // Send notification based on method
                    if ($method === 'email') {
                        $this->sendEmailNotification($user, $request->title, $request->message, $request->type);
                    } else {
                        $this->sendSMSNotification($user, $request->message, $request->type);
                    }

                    // Update notification status to sent
                    $notification->update([
                        'status' => 'sent',
                        'sent_at' => now(),
                    ]);

                    $successCount++;

                } catch (\Exception $e) {
                    // Update notification status to failed
                    $notification->update([
                        'status' => 'failed',
                        'failed_at' => now(),
                        'error_message' => $e->getMessage(),
                    ]);

                    $failedCount++;
                }
            }
        }

        $message = "Notifications processed: {$successCount} sent successfully";
        if ($failedCount > 0) {
            $message .= ", {$failedCount} failed";
        }

        return redirect()->route('admin.notifications.index')
            ->with('success', $message);
    }

    public function show(Notification $notification)
    {
        $notification->load('user');
        return view('admin.notifications.show', compact('notification'));
    }

    public function bulkAction(Request $request)
    {
        $request->validate([
            'action' => 'required|in:mark_read,mark_unread,delete,resend',
            'notification_ids' => 'required|array',
            'notification_ids.*' => 'exists:notifications,id'
        ]);

        $notificationIds = $request->notification_ids;

        switch ($request->action) {
            case 'mark_read':
                Notification::whereIn('id', $notificationIds)
                    ->update(['read_at' => now()]);
                $message = 'Notifications marked as read.';
                break;

            case 'mark_unread':
                Notification::whereIn('id', $notificationIds)
                    ->update(['read_at' => null]);
                $message = 'Notifications marked as unread.';
                break;

            case 'delete':
                Notification::whereIn('id', $notificationIds)->delete();
                $message = 'Notifications deleted.';
                break;

            case 'resend':
                $notifications = Notification::whereIn('id', $notificationIds)->get();
                foreach ($notifications as $notification) {
                    $user = $notification->user;
                    if ($notification->delivery_method === 'email') {
                        $this->notificationService->sendEmailNotification(
                            $user,
                            $notification->title,
                            $notification->message,
                            $notification->type
                        );
                    } else {
                        $this->notificationService->sendSMSNotification(
                            $user,
                            $notification->message,
                            $notification->type
                        );
                    }
                }
                $message = 'Notifications resent.';
                break;
        }

        return back()->with('success', $message);
    }

    public function templates()
    {
        $templates = [
            'fee_due' => [
                'title' => 'Fee Payment Due',
                'message' => 'Dear {student_name}, your fee payment of L$ {amount} is due on {due_date}. Please make payment to avoid late fees.',
                'variables' => ['student_name', 'amount', 'due_date']
            ],
            'exam_schedule' => [
                'title' => 'Exam Scheduled',
                'message' => 'Dear {student_name}, {exam_title} is scheduled for {exam_date} at {start_time}.',
                'variables' => ['student_name', 'exam_title', 'exam_date', 'start_time']
            ],
            'attendance_alert' => [
                'title' => 'Attendance Alert',
                'message' => 'Dear {student_name}, you were marked {status} on {date}.',
                'variables' => ['student_name', 'status', 'date']
            ],
            'grade_published' => [
                'title' => 'Grade Published',
                'message' => 'Dear {student_name}, your grade for {subject_name} has been published. Check your dashboard for details.',
                'variables' => ['student_name', 'subject_name']
            ],
            'homework_assigned' => [
                'title' => 'New Homework Assigned',
                'message' => 'Dear {student_name}, new homework "{homework_title}" has been assigned. Due date: {due_date}.',
                'variables' => ['student_name', 'homework_title', 'due_date']
            ],
            'payment_received' => [
                'title' => 'Payment Received',
                'message' => 'Dear {student_name}, your payment of L$ {amount} has been received and processed successfully.',
                'variables' => ['student_name', 'amount']
            ]
        ];

        return view('admin.notifications.templates', compact('templates'));
    }

    public function reports()
    {
        $stats = $this->notificationService->getNotificationStats();

        // Monthly notification trends
        $monthlyTrends = Notification::selectRaw('strftime("%Y-%m", created_at) as month, COUNT(*) as count')
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        // Delivery method performance
        $deliveryStats = Notification::selectRaw('
                delivery_method,
                status,
                COUNT(*) as count
            ')
            ->groupBy('delivery_method', 'status')
            ->get();

        // Type distribution
        $typeStats = Notification::selectRaw('type, COUNT(*) as count')
            ->groupBy('type')
            ->orderBy('count', 'desc')
            ->get();

        return view('admin.notifications.reports', compact('stats', 'monthlyTrends', 'deliveryStats', 'typeStats'));
    }

    private function getUserIds($userSelection, $specificUserIds = [])
    {
        switch ($userSelection) {
            case 'all_students':
                return User::where('user_type', 'student')->pluck('id')->toArray();
            case 'all_teachers':
                return User::where('user_type', 'teacher')->pluck('id')->toArray();
            case 'all_staff':
                return User::where('user_type', 'staff')->pluck('id')->toArray();
            case 'specific_users':
                return $specificUserIds;
            default:
                return [];
        }
    }

    private function sendEmailNotification($user, $title, $message, $type)
    {
        try {
            // For now, we'll just log the email. In production, you'd use a real email service
            Log::info('Email Notification Sent', [
                'user' => $user->email,
                'title' => $title,
                'type' => $type,
                'message' => $message
            ]);

            // In a real implementation, you would use:
            // Mail::to($user->email)->send(new NotificationMail($title, $message, $type));
            
            return true;
        } catch (\Exception $e) {
            Log::error('Email Notification Failed', [
                'user' => $user->email,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    private function sendSMSNotification($user, $message, $type)
    {
        try {
            // For now, we'll just log the SMS. In production, you'd use a real SMS service
            Log::info('SMS Notification Sent', [
                'user' => $user->phone ?? $user->email,
                'type' => $type,
                'message' => $message
            ]);

            // In a real implementation, you would use:
            // SMS::to($user->phone)->send($message);
            
            return true;
        } catch (\Exception $e) {
            Log::error('SMS Notification Failed', [
                'user' => $user->phone ?? $user->email,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }
}
