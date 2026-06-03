<?php

namespace App\Events;

use App\Models\Student;
use App\Models\User;
use Illuminate\Broadcasting\Channel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;

class StudentPickedUp implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets;

    public Student $student;
    public User $driver;

    public function __construct(Student $student, User $driver)
    {
        $this->student = $student;
        $this->driver = $driver;
    }

    public function broadcastOn(): Channel
    {
        return new Channel('school');
    }

    public function broadcastWith(): array
    {
        return [
            'type' => 'transport.student_picked_up',
            'student_id' => $this->student->id,
            'driver_id' => $this->driver->id,
            'timestamp' => now()->toIso8601String(),
        ];
    }
}


