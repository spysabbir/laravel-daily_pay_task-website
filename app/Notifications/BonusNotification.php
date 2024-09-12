<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Carbon\Carbon;

class BonusNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $referrerBonus;
    protected $userBonus;

    public function __construct($referrerBonus = null, $userBonus = null)
    {
        $this->referrerBonus = $referrerBonus;
        $this->userBonus = $userBonus;
    }

    public function via($notifiable)
    {
        return ['database', 'mail'];
    }

    public function toDatabase($notifiable)
    {
        $bonus = $this->referrerBonus ?? $this->userBonus;

        return [
            'title' => 'You have received a bonus of ' . get_site_settings('site_currency_symbol') . ' ' . $bonus['amount'],
            'message' => 'The bonus type is ' . $bonus['type'],
        ];
    }
    
    public function toMail($notifiable)
    {
        $bonus = $this->referrerBonus ?? $this->userBonus;

        return (new MailMessage)
            ->subject('Bonus Received')
            ->greeting('Hello ' . $notifiable->name . ',')
            ->line('You have received a bonus of ' . get_site_settings('site_currency_symbol') . ' ' . $bonus['amount'])
            ->line('The bonus type is ' . $bonus['type'])
            ->line('Updated on: ' . Carbon::now()->format('d-F-Y H:i:s'))
            ->line('Thank you for using our application!');
    }
}
