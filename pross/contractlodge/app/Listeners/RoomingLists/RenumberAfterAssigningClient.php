<?php

namespace App\Listeners\RoomingLists;

use App\Events\ClientAssigned;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\RoomingListGuest;

class RenumberAfterAssigningClient
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
     * @param  ClientAssigned  $event
     * @return void
     */
    public function handle(ClientAssigned $event)
    {
        // Call the method on rooming list guest to compute and save the row number
        $rooming_list_guest = $event->rooming_list_guest->computeAndSaveRowNumber();

        // Now we need to renumber all rows succeeding the list number of this rooming list guest
        // Only the list_row_number
        RoomingListGuest::renumberRowAfterGuest($rooming_list_guest);
    }
}
