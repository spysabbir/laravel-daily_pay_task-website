<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Carbon\Carbon;

class JobPostNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $jobPost;

    public function __construct($jobPost)
    {
        $this->jobPost = $jobPost;
    }

    public function via($notifiable)
    {
        return ['database', 'mail'];
    }

    public function toDatabase($notifiable)
    {
        return [
            'title' => 'Job Post Status',
            'message' => 'Now your job post status is ' . $this->jobPost['status'] . ' and the title is ' . $this->jobPost['title'] . '.' . $this->jobPost['status'] == 'Rejected' ? ' The reason is ' . $this->jobPost['rejection_reason'] : '' . ' If you have any questions, please feel free to contact us.',
        ];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
                    ->subject('Job Post Status')
                    ->greeting('Hello ' . $notifiable->name . ',')
                    ->line('Now your job post status is ' . $this->jobPost['status'] . ' and the title is ' . $this->jobPost['title'] . '.' . $this->jobPost['status'] == 'Rejected' ? ' The reason is ' . $this->jobPost['rejection_reason'] : '' . ' If you have any questions, please feel free to contact us.')
                    ->line('Updated on: ' . Carbon::parse($this->jobPost['created_at'])->format('d-F-Y H:i:s'))
                    ->line('Thank you for using our application!');
    }
}
