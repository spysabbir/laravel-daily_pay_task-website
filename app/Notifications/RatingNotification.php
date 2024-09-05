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
            'title' => 'You have received a rating of ' . $this->rating['rating'],
            'message' => 'The rating is job completion rating',
        ];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
                    ->subject('Rating Received')
                    ->greeting('Hello ' . $notifiable->name . ',')
                    ->line('You have received a rating of ' . $this->rating['rating'] . ' stars')
                    ->line('The rating is job completion rating')
                    ->line('Updated on: ' . Carbon::parse($this->rating['created_at'])->format('d-F-Y H:i:s'))
                    ->line('Thank you for using our application!');
    }
}
