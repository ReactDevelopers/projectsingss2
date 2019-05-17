<?php

namespace App\Events;

use App\RaceHotel;
use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class InventoryChanged
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Race hotel object
     *
     * @var App\RaceHotel
     */
    public $race_hotel;

    /**
     * Create a new event instance.
     *
     * @param App\RaceHotel
     *
     * @return void
     */
    public function __construct(RaceHotel $race_hotel)
    {
        $this->race_hotel = $race_hotel;
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
