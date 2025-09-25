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

class GradeSubmitted implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $grade;

    /**
     * Create a new event instance.
     */
    public function __construct(Grade $grade)
    {
        $this->grade = $grade->load(['student.user', 'subject', 'class', 'teacher.user']);
    }

    /**
     * Get the channels the event should broadcast on.
     */
    public function broadcastOn(): array
    {
        return [
            // Channel for all admins to notify them of new grade submission
            new Channel('admin.grades'),
            // Channel for the teacher who submitted the grade (confirmation)
            new PrivateChannel('teacher.' . $this->grade->teacher_id),
        ];
    }

    /**
     * The event's broadcast name.
     */
    public function broadcastAs(): string
    {
        return 'grade.submitted';
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
            'submitted_at' => $this->grade->created_at->toISOString(),
            'timestamp' => now()->toISOString(),
        ];
    }
}