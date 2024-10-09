<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Carbon\Carbon;

class DepositNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $deposit;

    public function __construct($deposit)
    {
        $this->deposit = $deposit;
    }

    public function via($notifiable)
    {
        return ['database', 'mail'];
    }

    public function toDatabase($notifiable)
    {
        return [
            'title' => 'Now your deposit status is ' . $this->deposit['status'],
            'message' => $this->deposit['rejected_reason'] ?? 'Thank you for using our application!',
        ];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
                    ->subject('Deposit Status')
                    ->greeting('Hello ' . $notifiable->name . ',')
                    ->line('Now your deposit status is ' . $this->deposit['status'] . ' and the amount is ' . get_site_settings('site_currency_symbol') . $this->deposit['amount'])
                    ->line($this->deposit['rejected_reason'] ?? 'Thank you for using our application!')
                    ->line('Updated on: ' . Carbon::parse($this->deposit['created_at'])->format('d-F-Y h:i:s'))
                    ->line('Thank you for using our application!');
    }
}
