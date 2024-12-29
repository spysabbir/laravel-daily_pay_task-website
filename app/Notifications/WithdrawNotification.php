<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Carbon\Carbon;

class WithdrawNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $withdraw;

    public function __construct($withdraw)
    {
        $this->withdraw = $withdraw;
    }

    public function via($notifiable)
    {
        return ['database', 'mail'];
    }

    public function toDatabase($notifiable)
    {
        $message = 'Thank you for using our application!';

        if ($this->withdraw['status'] === 'Rejected') {
            $message = 'Rejection Reason: ' . ($this->withdraw['rejected_reason']) . '. Please check the withdraw section for more details.';
        }

        $currencySymbol = get_site_settings('site_currency_symbol');

        return [
            'title' => 'Your withdraw request amount ' . $currencySymbol . ' ' . $this->withdraw['amount'] . ' is ' . $this->withdraw['status'] . '.',
            'message' => $message
        ];
    }

    public function toMail($notifiable)
    {
        $message = 'Thank you for using our application!';

        if ($this->withdraw['status'] === 'Rejected') {
            $message = 'Rejection Reason: ' . ($this->withdraw['rejected_reason']) . '. Please check the withdraw section for more details.';
        }

        $currencySymbol = get_site_settings('site_currency_symbol');

        return (new MailMessage)
                    ->subject('Withdraw Status')
                    ->greeting('Hello ' . $notifiable->name . ',')
                    ->line('Your withdraw request amount ' . $currencySymbol . ' ' . $this->withdraw['amount'] . ' is ' . $this->withdraw['status'] . '.')
                    ->line($message)
                    ->line('Updated on: ' . Carbon::parse($this->withdraw['created_at'] ?? now())->format('d M, Y h:i:s A'))
                    ->line('Thank you for using our application!');
    }
}
