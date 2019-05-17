<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use App\RoomingListGuest;

class ClientAssigned
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Rooming list guest which was just assigned a client
     *
     * @var App\RoomingListGuest
     */
    public $rooming_list_guest;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(RoomingListGuest $rooming_list_guest)
    {
        $this->rooming_list_guest = $rooming_list_guest;
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
