<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Grade;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RealtimeController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Check for updates since last check
     */
    public function checkUpdates(Request $request)
    {
        $user = Auth::user();
        $lastCheck = $request->get('last_check', now()->subMinutes(5));
        
        $updates = [];

        // Get grade-related updates based on user type
        if ($user->user_type === 'admin') {
            $updates = array_merge($updates, $this->getAdminUpdates($lastCheck));
        } elseif ($user->user_type === 'teacher') {
            $updates = array_merge($updates, $this->getTeacherUpdates($user, $lastCheck));
        } elseif ($user->user_type === 'student') {
            $updates = array_merge($updates, $this->getStudentUpdates($user, $lastCheck));
        }

        return response()->json([
            'grades' => $updates,
            'timestamp' => now()->toISOString(),
        ]);
    }

    /**
     * Get updates for admin users
     */
    private function getAdminUpdates($lastCheck)
    {
        $updates = [];

        // Get newly submitted grades
        $newGrades = Grade::where('status', 'pending')
                         ->where('created_at', '>', $lastCheck)
                         ->with(['student.user', 'subject', 'class', 'teacher.user'])
                         ->get();

        foreach ($newGrades as $grade) {
            $updates[] = [
                'type' => 'grade_submitted',
                'grade_id' => $grade->id,
                'student_name' => $grade->student->user->name,
                'subject_name' => $grade->subject->name,
                'class_name' => $grade->class->name,
                'teacher_name' => $grade->teacher->user->name,
                'year_avg' => $grade->year_avg,
                'timestamp' => $grade->created_at->toISOString(),
            ];
        }

        return $updates;
    }

    /**
     * Get updates for teacher users
     */
    private function getTeacherUpdates($user, $lastCheck)
    {
        $updates = [];
        $teacher = $user->teacher;

        if (!$teacher) {
            return $updates;
        }

        // Get grades that were approved or rejected
        $statusChanges = Grade::where('teacher_id', $teacher->id)
                             ->where('approved_at', '>', $lastCheck)
                             ->whereIn('status', ['approved', 'rejected'])
                             ->with(['student.user', 'subject', 'class', 'approvedBy'])
                             ->get();

        foreach ($statusChanges as $grade) {
            $updates[] = [
                'type' => $grade->status === 'approved' ? 'grade_approved' : 'grade_rejected',
                'grade_id' => $grade->id,
                'student_name' => $grade->student->user->name,
                'subject_name' => $grade->subject->name,
                'class_name' => $grade->class->name,
                'year_avg' => $grade->year_avg,
                'approved_by' => $grade->approvedBy ? $grade->approvedBy->name : null,
                'timestamp' => $grade->approved_at->toISOString(),
            ];
        }

        return $updates;
    }

    /**
     * Get updates for student users
     */
    private function getStudentUpdates($user, $lastCheck)
    {
        $updates = [];
        $student = $user->student;

        if (!$student) {
            return $updates;
        }

        // Get newly approved grades
        $approvedGrades = Grade::where('student_id', $student->id)
                              ->where('status', 'approved')
                              ->where('approved_at', '>', $lastCheck)
                              ->with(['subject', 'class', 'teacher.user', 'approvedBy'])
                              ->get();

        foreach ($approvedGrades as $grade) {
            $updates[] = [
                'type' => 'grade_approved',
                'grade_id' => $grade->id,
                'subject_name' => $grade->subject->name,
                'class_name' => $grade->class->name,
                'teacher_name' => $grade->teacher->user->name,
                'year_avg' => $grade->year_avg,
                'sem1_avg' => $grade->sem1_avg,
                'sem2_avg' => $grade->sem2_avg,
                'approved_by' => $grade->approvedBy ? $grade->approvedBy->name : null,
                'timestamp' => $grade->approved_at->toISOString(),
            ];
        }

        return $updates;
    }

    /**
     * Mark notifications as read
     */
    public function markAsRead(Request $request)
    {
        $user = Auth::user();
        $notificationIds = $request->get('notification_ids', []);

        if (!empty($notificationIds)) {
            Notification::where('user_id', $user->id)
                       ->whereIn('id', $notificationIds)
                       ->update(['read_at' => now()]);
        }

        return response()->json(['success' => true]);
    }

    /**
     * Get unread notification count
     */
    public function unreadCount()
    {
        $user = Auth::user();
        
        $count = Notification::where('user_id', $user->id)
                            ->whereNull('read_at')
                            ->count();

        return response()->json(['count' => $count]);
    }
}