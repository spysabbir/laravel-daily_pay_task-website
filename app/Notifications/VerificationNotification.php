<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Carbon\Carbon;

class VerificationNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $verification;

    public function __construct($verification)
    {
        $this->verification = $verification;
    }

    public function via($notifiable)
    {
        return ['database', 'mail'];
    }

    public function toDatabase($notifiable)
    {
        return [
            'title' => 'Now your ' . $this->verification['id_type'] . ' id verification status is ' . $this->verification['status'],
            'message' => $this->verification['remarks'],
        ];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
                    ->subject('Verification Status')
                    ->greeting('Hello ' . $notifiable->name . ',')
                    ->line('Your ' . $this->verification['id_type'] . ' verification status is now ' . $this->verification['status'] . '.')
                    ->line($this->verification['remarks'])
                    ->line('Updated on: ' . Carbon::parse($this->verification['created_at'])->format('d-F-Y H:i:s'))
                    ->line('Thank you for using our application!');
    }
}
