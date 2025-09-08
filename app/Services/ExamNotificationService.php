<?php

namespace App\Services;

use App\Models\Notification;
use App\Models\ExamSchedule;
use App\Models\ExamAttempt;
use App\Models\Student;
use App\Models\Teacher;
use Illuminate\Support\Facades\DB;

class ExamNotificationService
{
    /**
     * Send notification to students when exam is posted
     */
    public function notifyExamPosted(ExamSchedule $exam)
    {
        // Get all students in the class
        $students = Student::where('class_id', $exam->class_id)->get();
        
        foreach ($students as $student) {
            Notification::create([
                'user_id' => $student->user_id,
                'title' => 'New Exam Posted',
                'message' => "A new exam '{$exam->title}' has been posted for your class. Exam Date: {$exam->start_date}",
                'type' => 'exam',
                'category' => 'exam_posted',
                'priority' => 7,
                'status' => 'sent',
                'action_url' => route('student.exams.show', $exam->id),
                'action_text' => 'View Exam',
                'related_model' => 'ExamSchedule',
                'related_id' => $exam->id,
                'metadata' => [
                    'exam_title' => $exam->title,
                    'exam_date' => $exam->start_date,
                    'subject' => $exam->subject->name ?? 'N/A',
                    'class' => $exam->class->name ?? 'N/A'
                ],
                'sent_at' => now(),
                'delivery_method' => 'in_app',
                'delivery_status' => 'delivered'
            ]);
        }
    }

    /**
     * Send notification to teacher when student submits exam
     */
    public function notifyExamSubmission(ExamAttempt $attempt)
    {
        $exam = $attempt->examSchedule;
        $student = $attempt->student;
        
        // Get the teacher who created the exam (we need to find the teacher by subject)
        $teacher = null;
        if ($exam->subject && $exam->subject->teacher) {
            $teacher = $exam->subject->teacher;
        }
        
        if ($teacher) {
            Notification::create([
                'user_id' => $teacher->user_id,
                'title' => 'Exam Submission Received',
                'message' => "Student {$student->user->name} has submitted their exam '{$exam->title}'",
                'type' => 'exam',
                'category' => 'exam_submission',
                'priority' => 6,
                'status' => 'sent',
                'action_url' => route('teacher.exams.mark', $attempt->id),
                'action_text' => 'Mark Exam',
                'related_model' => 'ExamAttempt',
                'related_id' => $attempt->id,
                'metadata' => [
                    'student_name' => $student->user->name,
                    'exam_title' => $exam->title,
                    'submission_date' => $attempt->submitted_at,
                    'subject' => $exam->subject->name ?? 'N/A'
                ],
                'sent_at' => now(),
                'delivery_method' => 'in_app',
                'delivery_status' => 'delivered'
            ]);
        }
    }

    /**
     * Send notification to student when exam is marked
     */
    public function notifyExamMarked(ExamAttempt $attempt)
    {
        $exam = $attempt->examSchedule;
        
        Notification::create([
            'user_id' => $attempt->student->user_id,
            'title' => 'Exam Results Available',
            'message' => "Your exam '{$exam->title}' has been marked. Score: {$attempt->score}",
            'type' => 'exam',
            'category' => 'exam_results',
            'priority' => 6,
            'status' => 'sent',
            'action_url' => route('student.exams.results', $attempt->id),
            'action_text' => 'View Results',
            'related_model' => 'ExamAttempt',
            'related_id' => $attempt->id,
            'metadata' => [
                'exam_title' => $exam->title,
                'score' => $attempt->score,
                'marked_date' => now(),
                'subject' => $exam->subject->name ?? 'N/A'
            ],
            'sent_at' => now(),
            'delivery_method' => 'in_app',
            'delivery_status' => 'delivered'
        ]);
    }

    /**
     * Get unread notifications for user
     */
    public function getUnreadNotifications($userId)
    {
        return Notification::where('user_id', $userId)
            ->whereNull('read_at')
            ->where('status', 'sent')
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Mark notification as read
     */
    public function markAsRead($notificationId)
    {
        $notification = Notification::find($notificationId);
        if ($notification) {
            $notification->read_at = now();
            $notification->save();
        }
    }

    /**
     * Mark all notifications as read for user
     */
    public function markAllAsRead($userId)
    {
        Notification::where('user_id', $userId)
            ->whereNull('read_at')
            ->update(['read_at' => now()]);
    }
}
