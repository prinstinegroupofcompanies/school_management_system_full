<?php

namespace App\Notifications;

use App\Models\PaymentRecord;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PaymentSubmittedNotification extends Notification implements ShouldQueue
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
        return (new MailMessage)
            ->subject('New Payment Submitted')
            ->line('A student submitted a payment for approval.')
            ->line('Amount: '.$this->payment->amount)
            ->line('Method: '.$this->payment->payment_method)
            ->line('Reference: '.$this->payment->transaction_reference)
            ->action('Review Payments', url(route('finance.payments.index')));
    }

    public function toArray($notifiable)
    {
        return [
            'title' => 'New Payment Submitted',
            'message' => 'A student submitted a payment of $' . number_format($this->payment->amount, 2) . ' for approval.',
            'payment_id' => $this->payment->id,
            'student_id' => $this->payment->student_id,
            'fee_id' => $this->payment->fee_id,
            'amount' => $this->payment->amount,
            'payment_method' => $this->payment->payment_method,
            'transaction_reference' => $this->payment->transaction_reference,
            'status' => $this->payment->status,
            'action_url' => route('finance.payments.index'),
        ];
    }
}


