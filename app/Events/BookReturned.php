<?php

namespace App\Events;

use App\Models\BookIssue;
use Illuminate\Broadcasting\Channel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;

class BookReturned implements ShouldBroadcast
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
            'type' => 'library.book_returned',
            'issue_id' => $this->issue->id,
            'book_id' => $this->issue->book_id,
            'member_id' => $this->issue->member_id,
            'return_date' => optional($this->issue->return_date)->toDateString(),
        ];
    }
}


