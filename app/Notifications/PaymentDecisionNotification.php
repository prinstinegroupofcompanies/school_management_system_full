<?php

namespace App\Notifications;

use App\Models\PaymentRecord;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PaymentDecisionNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public PaymentRecord $payment)
    {
    }

    public function via($notifiable)
    {
        return ['database', 'mail'];
    }

    public function toMail($notifiable)
    {
        $subject = $this->payment->status === 'approved' ? 'Payment Approved' : 'Payment Rejected';
        $message = (new MailMessage)
            ->subject($subject)
            ->line('Your fee payment has been '.$this->payment->status.'.')
            ->line('Amount: '.$this->payment->amount)
            ->line('Reference: '.$this->payment->transaction_reference);
        return $message;
    }

    public function toArray($notifiable)
    {
        return [
            'payment_id' => $this->payment->id,
            'fee_id' => $this->payment->fee_id,
            'status' => $this->payment->status,
        ];
    }
}


