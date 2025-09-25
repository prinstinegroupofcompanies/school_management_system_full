<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use App\Models\Grade;

class GradeApproved implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $grade;

    /**
     * Create a new event instance.
     */
    public function __construct(Grade $grade)
    {
        $this->grade = $grade->load(['student.user', 'subject', 'class', 'teacher.user', 'approvedBy']);
    }

    /**
     * Get the channels the event should broadcast on.
     */
    public function broadcastOn(): array
    {
        return [
            // Channel for the student whose grade was approved
            new PrivateChannel('student.' . $this->grade->student_id),
            // Channel for the teacher who submitted the grade
            new PrivateChannel('teacher.' . $this->grade->teacher_id),
            // Channel for all admins (confirmation)
            new Channel('admin.grades'),
        ];
    }

    /**
     * The event's broadcast name.
     */
    public function broadcastAs(): string
    {
        return 'grade.approved';
    }

    /**
     * Get the data to broadcast.
     */
    public function broadcastWith(): array
    {
        return [
            'grade_id' => $this->grade->id,
            'student_name' => $this->grade->student->user->name,
            'subject_name' => $this->grade->subject->name,
            'class_name' => $this->grade->class->name,
            'teacher_name' => $this->grade->teacher->user->name,
            'year_avg' => $this->grade->year_avg,
            'sem1_avg' => $this->grade->sem1_avg,
            'sem2_avg' => $this->grade->sem2_avg,
            'approved_at' => $this->grade->approved_at->toISOString(),
            'approved_by' => $this->grade->approvedBy->name,
            'timestamp' => now()->toISOString(),
        ];
    }
}