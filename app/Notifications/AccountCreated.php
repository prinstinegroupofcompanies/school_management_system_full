<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AccountCreated extends Notification
{
    use Queueable;

    protected $username;
    protected $password;

    /**
     * Create a new notification instance.
     */
    public function __construct(string $username, string $password)
    {
        $this->username = $username;
        $this->password = $password;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Your Account Has Been Created')
            ->greeting('Hello ' . $notifiable->name . '!')
            ->line('Your account at ' . config('school.name') . ' has been created.')
            ->line('**Username:** ' . $this->username)
            ->line('**Password:** ' . $this->password)
            ->action('Login Now', url('/'))
            ->line('Please change your password after your first login for security purposes.')
            ->line('Thank you for being part of ' . config('school.name', 'our school') . '!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'message' => 'Your account has been created. Username: ' . $this->username,
            'username' => $this->username,
            'requires_password_change' => true,
        ];
    }
}
