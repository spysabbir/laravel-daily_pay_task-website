<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Carbon\Carbon;

class UserStatusNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $userStatus;

    public function __construct($userStatus)
    {
        $this->userStatus = $userStatus;
    }

    public function via($notifiable)
    {
        return ['database', 'mail'];
    }

    public function toDatabase($notifiable)
    {
        return [
            'title' => 'Your account status is now ' . $this->userStatus['status'],
            'message' => 'Reason: ' . $this->userStatus['reason'] . $this->userStatus['blocked_duration'] ? 'Blocked until: ' . $this->userStatus['blocked_duration'] . ' hours' : 'Thank you for using our application!',
        ];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
                    ->subject('Account Status')
                    ->greeting('Hello ' . $notifiable->name . ',')
                    ->line('Your account status is now ' . $this->userStatus['status'] . '.')
                    ->line('Reason: ' . $this->userStatus['reason'] . $this->userStatus['blocked_duration'] ? 'Blocked until: ' . $this->userStatus['blocked_duration'] . ' hours' : 'Please stay active!')
                    ->line('Updated on: ' . Carbon::parse($this->userStatus['created_at'])->format('d M, Y h:i:s A'))
                    ->line('Thank you for using our application!');
    }
}
