<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Carbon\Carbon;

class PostTaskCheckNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $postTask;

    public function __construct($postTask)
    {
        $this->postTask = $postTask;
    }

    public function via($notifiable)
    {
        return ['database', 'mail'];
    }

    private function getMessage()
    {
        switch ($this->postTask['status']) {
            case 'Running':
                return 'Please check running posted task list for checking the progress of your posted task.';
            case 'Rejected':
                return 'Rejection Reason: ' . ($this->postTask['rejection_reason'] ?? 'N/A') . '. Please check rejected posted task list section for more details.';
            case 'Canceled':
                return 'Cancellation Reason: ' . ($this->postTask['cancellation_reason'] ?? 'N/A') . '. Please check canceled posted task list section for more details.';
            case 'Paused':
                return 'Pausing Reason: ' . ($this->postTask['pausing_reason'] ?? 'N/A') . '. Please check paused posted task list section for more details.';
            default:
                return 'Please check the posted task list. Thank you!';
        }
    }

    public function toDatabase($notifiable)
    {
        return [
            'title' => 'Your posted task request ID ' . $this->postTask['id'] . ' is ' . $this->postTask['status'] . '.',
            'message' => $this->getMessage(),
        ];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
                    ->subject('Posted Task Status')
                    ->greeting('Hello ' . $notifiable->name . ',')
                    ->line('Your posted task request ID ' . $this->postTask['id'] . ' is ' . $this->postTask['status'] . '.')
                    ->line($this->getMessage())
                    ->line('Updated on: ' . Carbon::parse($this->postTask['updated_at'] ?? now())->format('d M, Y h:i:s A'))
                    ->line('Thank you for using our application!');
    }
}
