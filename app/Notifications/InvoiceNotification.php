<?php

namespace App\Notifications;

use App\Models\StudentFee;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Storage;

class InvoiceNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public StudentFee $studentFee, public string $filePath)
    {
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        $url = Storage::disk('public')->url($this->filePath);
        return (new MailMessage)
            ->subject('Your School Fee Invoice')
            ->greeting('Hello '.$notifiable->name)
            ->line('Your invoice has been generated.')
            ->action('Download Invoice', $url)
            ->line('Thank you.');
    }

    public function toArray($notifiable)
    {
        return [
            'title' => 'Invoice Generated',
            'message' => 'Your school fee invoice is ready to download.',
            'student_fee_id' => $this->studentFee->id,
            'total_amount' => $this->studentFee->total_amount,
            'balance' => $this->studentFee->balance,
            'file_path' => $this->filePath,
            'url' => Storage::disk('public')->url($this->filePath),
        ];
    }

    public function toDatabase($notifiable)
    {
        return $this->toArray($notifiable);
    }
}


