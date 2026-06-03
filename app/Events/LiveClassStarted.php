<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use App\Models\LiveClass;

class LiveClassStarted implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $liveClass;

    /**
     * Create a new event instance.
     */
    public function __construct(LiveClass $liveClass)
    {
        $this->liveClass = $liveClass;
    }

    /**
     * Get the channels the event should broadcast on.
     */
    public function broadcastOn(): array
    {
        $channels = [
            new PrivateChannel('live-classes'),
        ];

        // Broadcast to class-specific channel if class_id exists
        if ($this->liveClass->class_id) {
            $channels[] = new PrivateChannel('class.' . $this->liveClass->class_id);
        }

        return $channels;
    }

    /**
     * The event's broadcast name.
     */
    public function broadcastAs(): string
    {
        return 'live-class.started';
    }

    /**
     * Get the data to broadcast.
     */
    public function broadcastWith(): array
    {
        return [
            'id' => $this->liveClass->id,
            'title' => $this->liveClass->title,
            'meeting_url' => $this->liveClass->meeting_url,
            'scheduled_at' => $this->liveClass->scheduled_at->toDateTimeString(),
            'teacher' => $this->liveClass->teacher->user->name ?? 'N/A',
        ];
    }
}
