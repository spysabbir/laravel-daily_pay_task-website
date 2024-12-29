<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Carbon\Carbon;

class RatingNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $rating;

    public function __construct($rating)
    {
        $this->rating = $rating;
    }

    public function via($notifiable)
    {
        return ['database', 'mail'];
    }

    public function toDatabase($notifiable)
    {
        return [
            'title' => 'You have received ' . $this->rating['rating'] . ' stars rating on task complete properly.',
            'message' => 'Please properly complete all tasks in the future. Thank you!',
        ];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
                    ->subject('Rating Received')
                    ->greeting('Hello ' . $notifiable->name . ',')
                    ->line('You have received ' . $this->rating['rating'] . ' stars rating on task complete properly.')
                    ->line('Please properly complete all tasks in the future.')
                    ->line('Updated on: ' . Carbon::parse($this->rating['created_at'])->format('d M, Y h:i:s A'))
                    ->line('Thank you for using our application!');
    }
}
