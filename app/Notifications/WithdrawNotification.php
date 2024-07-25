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
        return [
            'title' => 'Now your withdraw status is ' . $this->withdraw['status'],
            'message' => $this->withdraw['remarks'],
        ];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
                    ->subject('Withdraw Status')
                    ->greeting('Hello ' . $notifiable->name . ',')
                    ->line('Now your withdraw status is ' . $this->withdraw['status'] . ' and the amount is ' . get_site_settings('site_currency_symbol') . $this->withdraw['amount'])
                    ->line($this->withdraw['remarks'])
                    ->line('Updated on: ' . Carbon::parse($this->withdraw['created_at'])->format('d-F-Y H:i:s'))
                    ->line('Thank you for using our application!');
    }
}
