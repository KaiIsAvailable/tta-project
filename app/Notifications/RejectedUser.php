<?php

namespace App\Notifications;

use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Contracts\Queue\ShouldQueue;

class RejectedUser extends Notification implements ShouldQueue
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
            ->subject('User has been rejected')
            ->greeting('Unfortunately, ' . $notifiable->name . '!')
            ->line('You are receiving this email because your account has been rejected by our Admin.')
            ->line('You may not recognize by our admin. Pleace contact your instructor if you are really our student.')
            ->line('Thank you for register with us!');
    }
}
