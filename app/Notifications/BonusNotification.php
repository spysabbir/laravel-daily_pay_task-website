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

    protected $bonus;

    public function __construct($bonus)
    {
        $this->bonus = $bonus;
    }

    public function via($notifiable)
    {
        return ['database', 'mail'];
    }

    public function toDatabase($notifiable)
    {
        return [
            'title' => 'You have received a bonus of ' . $this->bonus['amount'],
            'message' => 'The bonus type is ' . $this->bonus['bonus_type'],
        ];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
                    ->subject('Bonus Received')
                    ->greeting('Hello ' . $notifiable->name . ',')
                    ->line('You have received a bonus of ' . $this->bonus['amount'] . get_site_settings('site_currency_symbol'))
                    ->line('The bonus type is ' . $this->bonus['bonus_type'])
                    ->line('Updated on: ' . Carbon::parse($this->bonus['created_at'])->format('d-F-Y H:i:s'))
                    ->line('Thank you for using our application!');
    }
}
