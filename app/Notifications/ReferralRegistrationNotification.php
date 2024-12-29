<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Carbon\Carbon;

class ReferralRegistrationNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $referrer;
    protected $user;

    public function __construct($referrer, $user)
    {
        $this->referrer = $referrer;
        $this->user = $user;
    }

    public function via($notifiable)
    {
        return ['database', 'mail'];
    }

    public function toDatabase($notifiable)
    {
        if ($notifiable->id === $this->referrer->id) {
            $title = $this->user->name . ' has been referred by you.';
        } else {
            $title = 'You have been referred by ' . $this->referrer->name . '.';
        }

        return [
            'title' => $title,
            'message' => 'Thank you for using our application! Please share your referral link to earn more rewards.',
        ];
    }

    public function toMail($notifiable)
    {
        if ($notifiable->id === $this->referrer->id) {
            // Email for the referrer
            return (new MailMessage)
                ->subject('Referral Registration Notification')
                ->greeting('Hello ' . $this->referrer->name . '!')
                ->line($this->user->name . ' has been referred by you.')
                ->line('Updated on: ' . Carbon::now()->format('d M, Y h:i:s A'))
                ->line('Thank you for using our application!');
        } else {
            // Email for the referred user
            return (new MailMessage)
                ->subject('Referral Registration Notification')
                ->greeting('Hello ' . $this->user->name . '!')
                ->line('You have been referred by ' . $this->referrer->name . '.')
                ->line('Updated on: ' . Carbon::now()->format('d M, Y h:i:s A'))
                ->line('Thank you for using our application!');
        }
    }
}
