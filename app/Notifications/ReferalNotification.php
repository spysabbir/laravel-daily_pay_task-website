<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Carbon\Carbon;

class ReferralNotification extends Notification implements ShouldQueue
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
        return [
            'title' => 'Referral Notification',
            'message' => $this->user->name . ' has been referred by ' . $this->referrer->name . '.',
        ];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
                    ->subject('Referral Notification')
                    ->greeting('Hello ' . $this->user->name . '!')
                    ->line($this->user->name . ' has been referred by ' . $this->referrer->name . '.')
                    ->line('Updated on: ' . Carbon::parse($this->user->updated_at)->format('d M Y H:i:s'))
                    ->line('Thank you for using our application!');
    }
}
