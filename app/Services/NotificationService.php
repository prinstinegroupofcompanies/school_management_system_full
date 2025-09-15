<?php

namespace App\Services;

use App\Models\Notification;
use App\Models\User;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Exception;

class NotificationService
{
    public function sendEmailNotification($user, $title, $message, $type = 'general', $metadata = [])
    {
        try {
            // Create notification record
            $notification = Notification::create([
                'user_id' => $user->id,
                'title' => $title,
                'message' => $message,
                'type' => $type,
                'delivery_method' => 'email',
                'status' => 'pending',
                'metadata' => $metadata
            ]);

            // Send email
            Mail::send('emails.notification', [
                'title' => $title,
                'message' => $message,
                'user' => $user
            ], function ($mail) use ($user, $title) {
                $mail->to($user->email, $user->name)
                     ->subject($title);
            });

            $notification->update([
                'status' => 'sent',
                'sent_at' => now(),
                'delivery_status' => 'delivered'
            ]);

            return true;
        } catch (Exception $e) {
            Log::error('Email notification failed: ' . $e->getMessage());
            $notification->update([
                'status' => 'failed',
                'error_message' => $e->getMessage()
            ]);
            return false;
        }
    }

    public function sendSMSNotification($user, $message, $type = 'general', $metadata = [])
    {
        try {
            // Create notification record
            $notification = Notification::create([
                'user_id' => $user->id,
                'title' => 'SMS Notification',
                'message' => $message,
                'type' => $type,
                'delivery_method' => 'sms',
                'status' => 'pending',
                'metadata' => $metadata
            ]);

            // Send SMS using Twilio or other SMS provider
            $this->sendSMS($user->phone, $message);

            $notification->update([
                'status' => 'sent',
                'sent_at' => now(),
                'delivery_status' => 'delivered'
            ]);

            return true;
        } catch (Exception $e) {
            Log::error('SMS notification failed: ' . $e->getMessage());
            $notification->update([
                'status' => 'failed',
                'error_message' => $e->getMessage()
            ]);
            return false;
        }
    }

    public function sendBulkNotification($userIds, $title, $message, $type = 'general', $deliveryMethod = 'email')
    {
        $successCount = 0;
        $failCount = 0;

        foreach ($userIds as $userId) {
            $user = User::find($userId);
            if (!$user) continue;

            if ($deliveryMethod === 'email') {
                $result = $this->sendEmailNotification($user, $title, $message, $type);
            } else {
                $result = $this->sendSMSNotification($user, $message, $type);
            }

            if ($result) {
                $successCount++;
            } else {
                $failCount++;
            }
        }

        return [
            'success' => $successCount,
            'failed' => $failCount,
            'total' => count($userIds)
        ];
    }

    public function sendEventNotification($event, $userIds = null)
    {
        $notifications = [];

        switch ($event['type']) {
            case 'fee_due':
                $notifications = $this->handleFeeDueNotification($event);
                break;
            case 'exam_schedule':
                $notifications = $this->handleExamScheduleNotification($event);
                break;
            case 'attendance_alert':
                $notifications = $this->handleAttendanceAlertNotification($event);
                break;
            case 'grade_published':
                $notifications = $this->handleGradePublishedNotification($event);
                break;
            case 'homework_assigned':
                $notifications = $this->handleHomeworkAssignedNotification($event);
                break;
            case 'payment_received':
                $notifications = $this->handlePaymentReceivedNotification($event);
                break;
        }

        if ($userIds) {
            $notifications = array_filter($notifications, function($notification) use ($userIds) {
                return in_array($notification['user_id'], $userIds);
            });
        }

        foreach ($notifications as $notification) {
            $user = User::find($notification['user_id']);
            if ($user) {
                if ($notification['delivery_method'] === 'email') {
                    $this->sendEmailNotification($user, $notification['title'], $notification['message'], $notification['type']);
                } else {
                    $this->sendSMSNotification($user, $notification['message'], $notification['type']);
                }
            }
        }

        return count($notifications);
    }

    private function handleFeeDueNotification($event)
    {
        $student = $event['student'];
        $feeAmount = $event['amount'];
        $dueDate = $event['due_date'];

        return [
            [
                'user_id' => $student->user_id,
                'title' => 'Fee Payment Due',
                'message' => "Dear {$student->user->name}, your fee payment of L$ {$feeAmount} is due on {$dueDate}. Please make payment to avoid late fees.",
                'type' => 'fee_due',
                'delivery_method' => 'email'
            ],
            [
                'user_id' => $student->user_id,
                'title' => 'Fee Payment Due',
                'message' => "Fee payment of L$ {$feeAmount} due on {$dueDate}",
                'type' => 'fee_due',
                'delivery_method' => 'sms'
            ]
        ];
    }

    private function handleExamScheduleNotification($event)
    {
        $students = $event['students'];
        $exam = $event['exam'];

        $notifications = [];
        foreach ($students as $student) {
            $notifications[] = [
                'user_id' => $student->user_id,
                'title' => 'Exam Scheduled',
                'message' => "Dear {$student->user->name}, {$exam->title} is scheduled for {$exam->exam_date} at {$exam->start_time}.",
                'type' => 'exam_schedule',
                'delivery_method' => 'email'
            ];
        }

        return $notifications;
    }

    private function handleAttendanceAlertNotification($event)
    {
        $student = $event['student'];
        $attendance = $event['attendance'];

        return [
            [
                'user_id' => $student->user_id,
                'title' => 'Attendance Alert',
                'message' => "Dear {$student->user->name}, you were marked {$attendance->status} on {$attendance->date}.",
                'type' => 'attendance_alert',
                'delivery_method' => 'email'
            ]
        ];
    }

    private function handleGradePublishedNotification($event)
    {
        $student = $event['student'];
        $grade = $event['grade'];

        return [
            [
                'user_id' => $student->user_id,
                'title' => 'Grade Published',
                'message' => "Dear {$student->user->name}, your grade for {$grade->subject->name} has been published. Check your dashboard for details.",
                'type' => 'grade_published',
                'delivery_method' => 'email'
            ]
        ];
    }

    private function handleHomeworkAssignedNotification($event)
    {
        $students = $event['students'];
        $homework = $event['homework'];

        $notifications = [];
        foreach ($students as $student) {
            $notifications[] = [
                'user_id' => $student->user_id,
                'title' => 'New Homework Assigned',
                'message' => "Dear {$student->user->name}, new homework '{$homework->title}' has been assigned. Due date: {$homework->due_date}.",
                'type' => 'homework_assigned',
                'delivery_method' => 'email'
            ];
        }

        return $notifications;
    }

    private function handlePaymentReceivedNotification($event)
    {
        $student = $event['student'];
        $payment = $event['payment'];

        return [
            [
                'user_id' => $student->user_id,
                'title' => 'Payment Received',
                'message' => "Dear {$student->user->name}, your payment of L$ {$payment->amount_paid} has been received and processed successfully.",
                'type' => 'payment_received',
                'delivery_method' => 'email'
            ]
        ];
    }

    private function sendSMS($phone, $message)
    {
        // Implement SMS sending logic using Twilio or other SMS provider
        // This is a placeholder implementation
        Log::info("SMS sent to {$phone}: {$message}");
        return true;
    }

    public function getNotificationStats()
    {
        return [
            'total' => Notification::count(),
            'sent' => Notification::where('status', 'sent')->count(),
            'pending' => Notification::where('status', 'pending')->count(),
            'failed' => Notification::where('status', 'failed')->count(),
            'by_type' => Notification::selectRaw('type, count(*) as count')
                ->groupBy('type')
                ->get(),
            'by_method' => Notification::selectRaw('delivery_method, count(*) as count')
                ->groupBy('delivery_method')
                ->get()
        ];
    }
}
