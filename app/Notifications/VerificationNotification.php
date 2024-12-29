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
        $message = 'Thank you for using our application!';

        if ($this->verification['status'] === 'Rejected') {
            $message = 'Rejection Reason: ' . $this->verification['rejected_reason'] . '. Please check the verification section for more details.';
        }

        return [
            'title' => 'Your ' . $this->verification['id_type'] . ' ID verification request  is ' . $this->verification['status'] . '.',
            'message' => $message,
        ];
    }

    public function toMail($notifiable)
    {
        $message = 'Thank you for using our application!';

        if ($this->verification['status'] === 'Rejected') {
            $message = 'Rejection Reason: ' . $this->verification['rejected_reason'] . '. Please check the verification section for more details.';
        }

        return (new MailMessage)
                    ->subject('Verification Status')
                    ->greeting('Hello ' . $notifiable->name . ',')
                    ->line('Your ' . $this->verification['id_type'] . ' ID verification request is ' . $this->verification['status'] . '.')
                    ->line($message)
                    ->line('Updated on: ' . Carbon::parse($this->verification['created_at'])->format('d M, Y h:i:s A'))
                    ->line('Thank you for using our application!');
    }
}
