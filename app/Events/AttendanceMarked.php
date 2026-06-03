<?php

namespace App\Events;

use App\Models\Attendance;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class AttendanceMarked implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $attendance;

    /**
     * Create a new event instance.
     */
    public function __construct(Attendance $attendance)
    {
        $this->attendance = $attendance->load(['attendable', 'class', 'subject']);
    }

    /**
     * Get the channels the event should broadcast on.
     */
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('attendance'),
        ];
    }

    /**
     * Get the data to broadcast.
     */
    public function broadcastWith(): array
    {
        return [
            'attendance' => [
                'id' => $this->attendance->id,
                'attendable_type' => $this->attendance->attendable_type,
                'attendable_id' => $this->attendance->attendable_id,
                'date' => $this->attendance->date->toDateString(),
                'status' => $this->attendance->status,
                'class' => $this->attendance->class ? $this->attendance->class->name : null,
                'subject' => $this->attendance->subject ? $this->attendance->subject->name : null,
            ]
        ];
    }
}

