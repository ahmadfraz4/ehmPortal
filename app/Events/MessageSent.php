<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class MessageSent implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;
      public $chat;
    /**
     * Create a new event instance.
     */
    public function __construct($chat)
    {
        $this->chat = $chat;
    }
    // public function broadcastOn()
    // {
    //     return new Channel('chat.' . $this->chat->room_id);
    // }
    // public function broadcastAs()
    // {
    //     return 'message.sent';
    // }

    public function broadcastOn()
    {
        Log::info('Broadcasting on channel: chat.' . $this->chat->room_id);
        // return new Channel('chat.' . $this->chat->room_id);
        return new PrivateChannel('chat.' . $this->chat->room_id);
    }

    public function broadcastAs()
    {
        return 'message.sent';
    }
   
   
}
