<?php

namespace App\Notifications;

use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;
use App\Models\User;
use Illuminate\Contracts\Queue\ShouldQueue;

class UserApprovedAlert extends Notification implements ShouldQueue
{
    public $connection = 'database';
    public $queue = 'emails';
    public $delay = null;
    public $approvedUser;

    public function __construct(User $user)
    {
        $this->approvedUser = $user;
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('A User Has Been Approved')
            ->line('User ' . $this->approvedUser->name . ' has register as student')
            ->action('Check via login', url(route('login')))
            ->line('You can review user details in the admin panel.');
    }
}
