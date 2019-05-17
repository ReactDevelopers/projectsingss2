<?php

namespace App\Listeners\RoomingLists;

use App\Events\RoomingListDeleted;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class DeleteRoomingListGuests
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
     * @param  RoomingListDeleted  $event
     * @return void
     */
    public function handle(RoomingListDeleted $event)
    {
        // Delete all rooming_list_guests for the rooming_list beign deleted
        $rooming_list_guests = $event->rooming_list->rooming_list_guests()->get();

        foreach ($rooming_list_guests as $rooming_list_guest) {
            $rooming_list_guest->delete();
        }
    }
}
