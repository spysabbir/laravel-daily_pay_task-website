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
        $message = 'Thank you for using our application!';

        if ($this->deposit['status'] === 'Rejected') {
            $message = 'Rejection Reason: ' . ($this->deposit['rejected_reason']) . '. Please check the deposit section for more details.';
        }

        $currencySymbol = get_site_settings('site_currency_symbol');

        return [
            'title' => 'Your deposit request amount ' . $currencySymbol . ' ' . $this->deposit['amount'] . ' is ' . $this->deposit['status'] . '.',
            'message' => $message
        ];
    }

    public function toMail($notifiable)
    {
        $message = 'Thank you for using our application!';

        if ($this->deposit['status'] === 'Rejected') {
            $message = 'Rejection Reason: ' . ($this->deposit['rejected_reason']) . '. Please check the deposit section for more details.';
        }

        $currencySymbol = get_site_settings('site_currency_symbol');

        return (new MailMessage)
                    ->subject('Deposit Status')
                    ->greeting('Hello ' . $notifiable->name . ',')
                    ->line('Your deposit request amount ' . $currencySymbol . ' ' . $this->deposit['amount'] . ' is ' . $this->deposit['status'] . '.')
                    ->line($message)
                    ->line('Updated on: ' . Carbon::parse($this->deposit['created_at'] ?? now())->format('d M, Y h:i:s A'))
                    ->line('Thank you for using our application!');
    }
}
