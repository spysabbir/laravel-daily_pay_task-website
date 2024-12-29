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
        if ($this->proofTask['status'] === 'Approved') {
            $message = 'Please check approved worked list section for more details.';
        } else
        if ($this->proofTask['status'] === 'Rejected') {
            $message = 'The reason is ' . $this->proofTask['rejected_reason'] . '. Please check rejected worked list section for more details.';
        } else {
            $message = 'Thank you for using our application!';
        }

        return [
            'title' => 'Your reviewed working task id ' . $this->proofTask['id'] . ' is ' . $this->proofTask['status'] . ' finally.',
            'message' => $message,
        ];
    }

    public function toMail($notifiable)
    {
        if ($this->proofTask['status'] === 'Approved') {
            $message = 'Please check approved worked list section for more details.';
        } else
        if ($this->proofTask['status'] === 'Rejected') {
            $message = 'The reason is ' . $this->proofTask['rejected_reason'] . '. Please check rejected worked list section for more details.';
        } else {
            $message = 'Thank you for using our application!';
        }

        return (new MailMessage)
                    ->subject('Task Proof Reviewed Resolved')
                    ->greeting('Hello ' . $notifiable->name . ',')
                    ->line('Your reviewed working task id ' . $this->proofTask['id'] . ' is ' . $this->proofTask['status'] . ' finally.')
                    ->line( $message )
                    ->line('Updated on: ' . Carbon::parse($this->proofTask['updated_at'])->format('d M, Y h:i:s A'))
                    ->line('Thank you for using our application!');
    }
}
