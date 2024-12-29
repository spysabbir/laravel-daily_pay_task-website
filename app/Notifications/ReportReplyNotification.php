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
        if ($this->report['status'] === 'False') {
            $message = 'You submitted a false report. Please submit a valid report in the future. Please check the report section for more details.';
        } else if ($this->report['status'] === 'Received') {
            $message = 'We received your report. We will investigate and take necessary actions. Please check the report section for more details.';
        } else {
            $message = 'Your report has been resolved.';
        }

        return [
            'title' => 'Your report id ' . $this->report['id'] . ' has been resolved.',
            'message' => $message,
        ];
    }

    public function toMail($notifiable)
    {
        if ($this->report['status'] === 'False') {
            $message = 'You submitted a false report. Please submit a valid report in the future. Please check the report section for more details.';
        } else if ($this->report['status'] === 'Received') {
            $message = 'We received your report. We will investigate and take necessary actions. Please check the report section for more details.';
        } else {
            $message = 'Your report has been resolved.';
        }

        return (new MailMessage)
                    ->subject('Report Resolved')
                    ->greeting('Hello ' . $notifiable->name . ',')
                    ->line('Your report id ' . $this->report['id'] . ' has been resolved.')
                    ->line($message)
                    ->line('Report Reason: ' . $this->report['reason'])
                    ->line('Reply: ' . $this->report_reply['reply'])
                    ->line('Resolved on: ' . Carbon::parse($this->report_reply['created_at'])->format('d M, Y h:i:s A'))
                    ->line('Thank you for using our application!');
    }
}
