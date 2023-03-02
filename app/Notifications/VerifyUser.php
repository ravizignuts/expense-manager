<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class VerifyUser extends Notification implements ShouldQueue
{
    use Queueable;
    public $user,$is_register;
    /**
     * Create a new notification instance.
     */
    public function __construct($user,$is_register)
    {
        $this->user        = $user;
        $this->is_register = $is_register;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $url = url('/api/auth/verifyuser/'.$this->user->email_verification_token.'/'.$this->is_register);
        return (new MailMessage)
                    ->greeting('Hello , '.$this->user->firstname)
                    ->line('Welcome to Expense Manager App .')
                    ->action('Verify User', $url)
                    ->line('Thank you for using our application!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }
}
