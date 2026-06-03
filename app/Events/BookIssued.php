<?php

namespace App\Events;

use App\Models\BookIssue;
use Illuminate\Broadcasting\Channel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;

class BookIssued implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets;

    public BookIssue $issue;

    public function __construct(BookIssue $issue)
    {
        $this->issue = $issue;
    }

    public function broadcastOn(): Channel
    {
        return new Channel('school');
    }

    public function broadcastWith(): array
    {
        return [
            'type' => 'library.book_issued',
            'issue_id' => $this->issue->id,
            'book_id' => $this->issue->book_id,
            'member_id' => $this->issue->member_id,
            'issued_by' => $this->issue->issued_by,
            'issue_date' => optional($this->issue->issue_date)->toDateString(),
            'due_date' => optional($this->issue->due_date)->toDateString(),
        ];
    }
}


