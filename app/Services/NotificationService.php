<?php

namespace App\Services;

use App\Models\User;
use App\Models\Student;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class NotificationService
{
    /**
     * Send real-time notification to user
     */
    public function sendNotification($userId, $type, $title, $message, $data = [])
    {
        try {
            return DB::table('notifications')->insert([
                'user_id' => $userId,
                'type' => $type,
                'title' => $title,
                'message' => $message,
                'data' => json_encode($data),
                'is_read' => false,
                'priority' => $data['priority'] ?? 'normal',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to send notification: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Get unread notifications for a user
     */
    public function getUnreadNotifications($userId, $limit = 10)
    {
        return DB::table('notifications')
            ->where('user_id', $userId)
            ->where('is_read', false)
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * Mark notification as read
     */
    public function markAsRead($notificationId, $userId = null)
    {
        $query = DB::table('notifications')->where('id', $notificationId);
        
        if ($userId) {
            $query->where('user_id', $userId);
        }
        
        return $query->update([
            'is_read' => true,
            'read_at' => now(),
            'updated_at' => now()
        ]);
    }

    /**
     * Send system-wide announcement
     */
    public function sendSystemAnnouncement($title, $message, $priority = 'normal')
    {
        $users = User::pluck('id')->toArray();
        
        foreach ($users as $userId) {
            $this->sendNotification(
                $userId,
                'system_announcement',
                $title,
                $message,
                ['priority' => $priority]
            );
        }
        
        return true;
    }

    /**
     * Get notification statistics
     */
    public function getNotificationStats()
    {
        try {
            $total = DB::table('notifications')->count();
            $unread = DB::table('notifications')->where('is_read', false)->count();
            $today = DB::table('notifications')->whereDate('created_at', today())->count();
            $thisWeek = DB::table('notifications')->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])->count();

            // Get failed notifications if status column exists
            $failed = 0;
            try {
                $failed = DB::table('notifications')->where('status', 'failed')->count();
            } catch (\Exception $e) {
                // Status column might not exist, default to 0
                $failed = 0;
            }

            return [
                'total' => $total,
                'unread' => $unread,
                'read' => $total - $unread,
                'sent' => $total, // All notifications are considered "sent" once created
                'pending' => $unread, // Unread notifications can be considered "pending"
                'failed' => $failed, // Failed notifications
                'today' => $today,
                'this_week' => $thisWeek,
                'read_percentage' => $total > 0 ? round(($total - $unread) / $total * 100, 1) : 0
            ];
        } catch (\Exception $e) {
            Log::error('Failed to get notification stats: ' . $e->getMessage());
            return [
                'total' => 0,
                'unread' => 0,
                'read' => 0,
                'sent' => 0,
                'pending' => 0,
                'failed' => 0,
                'today' => 0,
                'this_week' => 0,
                'read_percentage' => 0
            ];
        }
    }
}