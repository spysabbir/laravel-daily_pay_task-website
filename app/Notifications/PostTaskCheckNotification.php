<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Carbon\Carbon;

class PostTaskCheckNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $postTask;

    public function __construct($postTask)
    {
        $this->postTask = $postTask;
    }

    public function via($notifiable)
    {
        return ['database', 'mail'];
    }

    public function toDatabase($notifiable)
    {
        return [
            'title' => 'Now your task post status is ' . $this->postTask['status'],
            'message' => $this->postTask['rejection_reason'] ?? 'Thank you for using our application!',
        ];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
                    ->subject('Post Task Status')
                    ->greeting('Hello ' . $notifiable->name . ',')
                    ->line('Now your task post status is ' . $this->postTask['status'] . ' and the title is ' . $this->postTask['title'] . '.' . $this->postTask['status'] == 'Rejected' ? ' The reason is ' . $this->postTask['rejection_reason'] : '' . ' If you have any questions, please feel free to contact us.')
                    ->line('Updated on: ' . Carbon::parse($this->postTask['created_at'])->format('d-F-Y h:i:s'))
                    ->line('Thank you for using our application!');
    }

}
