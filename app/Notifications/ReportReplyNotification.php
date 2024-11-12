<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Carbon\Carbon;

class ReportReplyNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $report;
    protected $report_reply;

    public function __construct($report, $report_reply)
    {
        $this->report = $report;
        $this->report_reply = $report_reply;
    }

    public function via($notifiable)
    {
        return ['database', 'mail'];
    }

    public function toDatabase($notifiable)
    {
        return [
            'title' => 'Your report has been resolved',
            'message' => 'Report ID: ' . $this->report['id'] . ' has been resolved.'
        ];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
                    ->subject('Report Resolved')
                    ->greeting('Hello ' . $notifiable->name . ',')
                    ->line('Your report has been resolved.')
                    ->line('Report ID: ' . $this->report['id'] . ' has been resolved.')
                    ->line('Report Reason: ' . $this->report['reason'])
                    ->line('Reply: ' . $this->report_reply['reply'])
                    ->line('Resolved on: ' . Carbon::parse($this->report_reply['created_at'])->format('d M, Y h:i:s A'))
                    ->line('Thank you for using our application!');
    }
}
