<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

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
                    ->line('Your report has been resolved.')
                    ->line('Report ID: ' . $this->report['id'] . ' has been resolved.')
                    ->line('Report Reason: ' . $this->report['reason'])
                    ->line('Reply: ' . $this->report_reply['reply'])
                    ->line('Resolved on: ' . $this->report_reply['resolved_at'])
                    ->line('Thank you for using our application!');
    }
}
