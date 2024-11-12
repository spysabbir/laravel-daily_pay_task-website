<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Carbon\Carbon;

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
            'title' => 'Now your task proof reviewed status is ' . $this->proofTask['status'] . ' and the post task ID is ' . $this->proofTask['post_task_id'] . '.',
            'message' => $this->proofTask['status'] === 'Rejected' ? 'Reason: ' . $this->proofTask['rejection_reason'] : 'Thank you for using our application!',
        ];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
                    ->subject('Task Proof Reviewed Resolved')
                    ->greeting('Hello ' . $notifiable->name . ',')
                    ->line('Now your task proof reviewed status is ' . $this->proofTask['status'] . ' and the post task ID is ' . $this->proofTask['post_task_id'] . '.')
                    ->line( $this->proofTask['status'] == 'Rejected' ? ' The reason is ' . $this->proofTask['rejected_reason'] : '' . ' If you have any questions, please feel free to contact us.')
                    ->line('Updated on: ' . Carbon::parse($this->proofTask['updated_at'])->format('d M, Y h:i:s A'))
                    ->line('Thank you for using our application!');
    }
}
