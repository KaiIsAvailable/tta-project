<?php

namespace App\Notifications;

use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Contracts\Queue\ShouldQueue;

class ApprovedUser extends Notification implements ShouldQueue
{
    public $connection = 'database';
    public $queue = 'emails';
    public $delay = null;

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('Congratulations!!')
            ->greeting('Hello, ' . $notifiable->name . '!')
            ->line('You are receiving this email because your account has been approved.')
            ->line('You may now log in to Tham\'s Taekwon-Do Academy.')
            ->action('Login Now', url(route('login')))
            ->line('Thank you for joining us!');
    }
}
