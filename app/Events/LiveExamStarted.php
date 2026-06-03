<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use App\Models\LiveExam;

class LiveExamStarted implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $liveExam;

    /**
     * Create a new event instance.
     */
    public function __construct(LiveExam $liveExam)
    {
        $this->liveExam = $liveExam;
    }

    /**
     * Get the channels the event should broadcast on.
     */
    public function broadcastOn(): array
    {
        $channels = [
            new PrivateChannel('live-exams'),
        ];

        // Broadcast to class-specific channel if class_id exists
        if ($this->liveExam->class_id) {
            $channels[] = new PrivateChannel('class.' . $this->liveExam->class_id);
        }

        return $channels;
    }

    /**
     * The event's broadcast name.
     */
    public function broadcastAs(): string
    {
        return 'live-exam.started';
    }

    /**
     * Get the data to broadcast.
     */
    public function broadcastWith(): array
    {
        return [
            'id' => $this->liveExam->id,
            'title' => $this->liveExam->title,
            'start_time' => $this->liveExam->start_time->toDateTimeString(),
            'end_time' => $this->liveExam->end_time->toDateTimeString(),
            'duration_minutes' => $this->liveExam->duration_minutes,
            'total_marks' => $this->liveExam->total_marks,
            'teacher' => $this->liveExam->teacher->user->name ?? 'N/A',
        ];
    }
}
