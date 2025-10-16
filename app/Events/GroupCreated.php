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

class GroupCreated implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     */
    public $group;
    public $members;
    public function __construct($room, $members)
    {
        $this->group = $room;
        $this->members = $members;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        Log::info($this->members);
        return collect($this->members)->map(function ($user_id) {
            return new PrivateChannel('user.'.$user_id);
        })->toArray();
    }

    public function broadcastAs(){
        return 'group.created';
    }

    public function broadcastWith()
    {
        return [
            'group' => [
                'id' => $this->group->id,
                'group_name' => $this->group->group_name,
            ],
        ];
    }
}
