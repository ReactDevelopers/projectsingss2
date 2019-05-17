<?php

namespace App\Listeners\RoomingLists;

use App\Events\InventoryChanged;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Jobs\RoomingLists\PrepareRoomingListForInventory;

class PrepareForInventory
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  InventoryChanged  $event
     * @return void
     */
    public function handle(InventoryChanged $event)
    {
        $success = dispatch_now(new PrepareRoomingListForInventory(
            $event->race_hotel
        ));
    }
}
