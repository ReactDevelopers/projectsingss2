<?php

namespace App\Listeners\RoomingLists;

use App\Events\RoomsConfirmed;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Jobs\RoomingLists\AssignRoomingListToClient;

class AssignRoomsToClient
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
     * @param  RoomsConfirmed  $event
     * @return void
     */
    public function handle(RoomsConfirmed $event)
    {
        $success = dispatch_now(new AssignRoomingListToClient(
            $event->confirmation
        ));
    }
}
