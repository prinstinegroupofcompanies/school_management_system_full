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
}