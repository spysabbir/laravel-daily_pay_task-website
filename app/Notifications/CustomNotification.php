<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Carbon\Carbon;

class CustomNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $notificationData;

    public function __construct($notificationData = null)
    {
        $this->notificationData = $notificationData;
    }

    public function via($notifiable)
    {
        return ['database', 'mail'];
    }

    public function toDatabase($notifiable)
    {
        return [
            'title' => $this->notificationData['title'],
            'message' => $this->notificationData['message'],
        ];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject(config('app.name') . ' - Notification')
            ->greeting('Hello ' . $notifiable->name . ',')
            ->line($this->notificationData['title'])
            ->line($this->notificationData['message'])
            ->line('Updated on: ' . Carbon::now()->format('d M, Y h:i:s A'))
            ->line('Thank you for using our application!');
    }
}
