<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ReviewedTaskProofCheckNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $proofTask;

    public function __construct($proofTask)
    {
        $this->proofTask = $proofTask;
    }

    public function via(object $notifiable)
    {
        return ['database', 'mail'];
    }

    public function toDatabase($notifiable)
    {
        return [
            'title' => 'Now your task proof reviewed status is ' . $this->proofTask['status'],
            'message' => 'Task ID: ' . $this->proofTask['id'] . ' has been resolved.'
        ];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
                    ->subject('Task Proof Reviewed Resolved')
                    ->greeting('Hello ' . $notifiable->name . ',')
                    ->line('Now your task proof reviewed status is ' . $this->proofTask['status'])
                    ->line('Task ID: ' . $this->proofTask['id'] . ' has been resolved.')
                    ->line( $this->proofTask['status'] == 'Rejected' ? ' The reason is ' . $this->proofTask['rejected_reason'] : '' . ' If you have any questions, please feel free to contact us.')
                    ->line('Resolved on: ' . $this->proofTask['updated_at'])
                    ->line('Thank you for using our application!');
    }
}
