<?php

namespace App\Events;

use App\Models\Chat;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class MessageSent implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $chat;

    public function __construct(Chat $chat)
    {
        $this->chat = $chat;
    }

    public function broadcastOn(): array
    {
        // Membuat channel publik bernama chat.room.ID_BANTUAN
        return [
            new Channel('chat.room.' . $this->chat->help_request_id),
        ];
    }

    public function broadcastWith(): array
    {
        return [
            'id'              => $this->chat->id,
            'help_request_id' => $this->chat->help_request_id,
            'sender_id'       => $this->chat->sender_id,
            'receiver_id'     => $this->chat->receiver_id,
            'message'         => $this->chat->message,
            'created_at'      => $this->chat->created_at->format('H:i'),
        ];
    }
}