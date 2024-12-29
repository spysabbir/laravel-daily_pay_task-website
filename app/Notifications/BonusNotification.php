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

        $currencySymbol = get_site_settings('site_currency_symbol');

        return [
            'title' => 'You have received ' . $currencySymbol . ' ' . ($bonus['amount']) . ' ' . ($bonus['type']) . '.',
            'message' => 'Please check the bonus section for more details. Thank you for using our application!',
        ];
    }

    public function toMail($notifiable)
    {
        $bonus = $this->referrerBonus ?? $this->userBonus;

        $currencySymbol = get_site_settings('site_currency_symbol');

        return (new MailMessage)
            ->subject('Bonus Received')
            ->greeting('Hello ' . $notifiable->name . ',')
            ->line('You have received ' . $currencySymbol . ' ' . ($bonus['amount']) . ' ' . ($bonus['type']) . ' bonus.')
            ->line('Please check the bonus section for more details.')
            ->line('Updated on: ' . Carbon::now()->format('d M, Y h:i:s A'))
            ->line('Thank you for using our application!');
    }
}
