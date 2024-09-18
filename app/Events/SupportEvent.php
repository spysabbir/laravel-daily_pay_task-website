<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class SupportEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $support;

    public function __construct($support)
    {
        $this->support = $support;
    }

    public function broadcastOn(): array
    {
        return [
            new Channel('support')
        ];
    }

    public function broadcastWith()
    {
        return [
            'support' => [
                'sender_id' => $this->support->sender_id,
                'sender_name' => $this->support->sender->name,
                'sender_photo' => $this->support->sender->profile_photo,
                'receiver_id' => $this->support->receiver_id,
                'receiver_name' => $this->support->receiver->name,
                'receiver_photo' => $this->support->receiver->profile_photo,
                'message' => $this->support->message,
                'photo' => $this->support->photo,
                'created_at' => $this->support->created_at->diffForHumans(),
            ],
        ];
    }
}
