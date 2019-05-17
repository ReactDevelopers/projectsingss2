<?php

namespace App\Events;

use App\RoomingList;
use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class RoomingListDeleted
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Rooming list being deleted
     *
     * @var App\RoomingList
     */
    public $rooming_list;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(RoomingList $rooming_list)
    {
        $this->rooming_list = $rooming_list;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new PrivateChannel('channel-name');
    }
}
