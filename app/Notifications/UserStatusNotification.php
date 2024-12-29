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
        if ($this->userStatus['status'] == 'Active') {
            $message = 'Reason: ' . $this->userStatus['reason'] . '. Please stay active!';
        } else if ($this->userStatus['status'] == 'Blocked') {
            $message = 'Reason: ' . $this->userStatus['reason'] . '. Blocked until: ' . $this->userStatus['blocked_duration'] . ' hours. Please wait for the unblock!';
        } else if ($this->userStatus['status'] == 'Banned') {
            $message = 'Reason: ' . $this->userStatus['reason'] . '. Sorry, you are banned! If you think this is a mistake, please contact us!';
        } else {
            $message = 'Reason: ' . $this->userStatus['reason'] . '. Please contact us for more information!';
        }

        return [
            'title' => 'Your account status is now ' . $this->userStatus['status'] . '.',
            'message' => $message,
        ];
    }

    public function toMail($notifiable)
    {
        if ($this->userStatus['status'] == 'Active') {
            $message = 'Reason: ' . $this->userStatus['reason'] . '. Please stay active!';
        } else if ($this->userStatus['status'] == 'Blocked') {
            $message = 'Reason: ' . $this->userStatus['reason'] . '. Blocked until: ' . $this->userStatus['blocked_duration'] . ' hours. Please wait for the unblock!';
        } else if ($this->userStatus['status'] == 'Banned') {
            $message = 'Reason: ' . $this->userStatus['reason'] . '. Sorry, you are banned! If you think this is a mistake, please contact us!';
        } else {
            $message = 'Reason: ' . $this->userStatus['reason'] . '. Please contact us for more information!';
        }

        return (new MailMessage)
                    ->subject('Account Status')
                    ->greeting('Hello ' . $notifiable->name . ',')
                    ->line('Your account status is now ' . $this->userStatus['status'] . '.')
                    ->line($message)
                    ->line('Updated on: ' . Carbon::parse($this->userStatus['created_at'])->format('d M, Y h:i:s A'))
                    ->line('Thank you for using our application!');
    }
}
