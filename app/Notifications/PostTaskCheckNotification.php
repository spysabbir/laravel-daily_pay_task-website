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
        if ($this->postTask['status'] === 'Rejected') {
            $message = 'Rejection Reason: ' . $this->postTask['rejection_reason'];
        } else if ($this->postTask['status'] === 'Canceled') {
            $message = 'Cancellation Reason: ' . $this->postTask['cancellation_reason'];
        } else if ($this->postTask['status'] === 'Paused') {
            $message = 'Pausing Reason: ' . $this->postTask['pausing_reason'];
        } else {
            $message = 'Please check the task post. Thank you!';
        }
        return [
            'title' => 'Now your task post status is ' . $this->postTask['status'] . ' and the task post id is ' . $this->postTask['id'] . '.',
            'message' => $message,
        ];
    }

    public function toMail($notifiable)
    {
        if ($this->postTask['status'] === 'Rejected') {
            $message = 'Rejection Reason: ' . $this->postTask['rejection_reason'];
        } else if ($this->postTask['status'] === 'Canceled') {
            $message = 'Cancellation Reason: ' . $this->postTask['cancellation_reason'];
        } else if ($this->postTask['status'] === 'Paused') {
            $message = 'Pausing Reason: ' . $this->postTask['pausing_reason'];
        } else {
            $message = 'Please check the task post. Thank you!';
        }
        return (new MailMessage)
                    ->subject('Post Task Status')
                    ->greeting('Hello ' . $notifiable->name . ',')
                    ->line('Now your task post status is ' . $this->postTask['status'] . ' and the task post id is ' . $this->postTask['id'] . '. and the title is ' . $this->postTask['title'] . '.')
                    ->line($message)
                    ->line('Updated on: ' . Carbon::parse($this->postTask['updated_at'])->format('d M, Y h:i:s A'))
                    ->line('Thank you for using our application!');
    }

}
