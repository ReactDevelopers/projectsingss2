<?php

namespace App\Listeners\RoomingLists;

use App\Events\RoomingListDeleted;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Events\RoomingListPrepared;
use App\RoomingListGuest;

class RenumberRoomingListGuests
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
     * @param  RoomingListPrepared  $event
     * @return void
     */
    public function handle(RoomingListPrepared $event)
    {
        // We are only going to renumber the rows that are not assigned a client.

        // First find the last rooming list guest that has an assigned client
        $last_assigned_guest = RoomingListGuest::where('race_hotel_id', $event->race_hotel->id)
            ->whereNotNull('client_id')
            ->orderBy('list_row_number', 'desc')
            ->first();

        $start_list_row_number = 1;

        if ($last_assigned_guest) {
            $start_list_row_number = $last_assigned_guest->list_row_number + 1;
        }

        $this->renumberUnassignedGuests($event->race_hotel, $start_list_row_number);
    }

    /**
     * Method to renumber the guest rows which are not assigned any clients yet
     *
     * @param  App\RaceHotel $race_hotel
     * @param  integer       $start_list_row_number
     * @return void
     */
    private function renumberUnassignedGuests($race_hotel, $start_list_row_number)
    {
        $unassigned_guests = RoomingListGuest::where('race_hotel_id', $race_hotel->id)
            ->whereNull('client_id')
            ->orderBy('created_at', 'asc')
            ->get();

        $client_row_number = 1;

        foreach ($unassigned_guests as $index => $guest) {
            $list_row_number = $start_list_row_number + $index;
            $guest->list_row_number = $list_row_number;
            $guest->client_row_number = $client_row_number++;
            $guest->save();
        }
    }
}
