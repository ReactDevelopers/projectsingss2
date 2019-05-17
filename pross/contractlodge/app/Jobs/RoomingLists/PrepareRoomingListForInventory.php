<?php

namespace App\Jobs\RoomingLists;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\RaceHotel;
use App\RaceHotelInventory;
use App\RoomingList;
use App\Events\RoomingListPrepared;

class PrepareRoomingListForInventory implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * RaceHotel object
     *
     * @var App\RaceHotel
     */
    private $race_hotel;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(RaceHotel $race_hotel)
    {
        $this->race_hotel = $race_hotel;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        // Find all the inventories for the given race hotel
        $inventories = $this->race_hotel->room_type_inventories()->get();

        foreach ($inventories as $inventory) {
            $this->prepare($inventory);
        }

        event(new RoomingListPrepared($this->race_hotel));
    }

    /**
     * Method to prepare rooming list for the given inventory
     *
     * @param RaceHotelInventory $inventory
     * @return void
     */
    private function prepare(RaceHotelInventory $inventory)
    {
        // Check if rows are needed to be added or removed from rooming_lists table
        // Find the number of rows (rooms) already added to rooming_lists table for this inventory
        $rooms_added = $inventory->rooming_lists()->count();

        // Find the number of rooms contracted.
        $rooms_contracted = $this->getRoomsContractedCount($inventory);

        // If number of rows added is less than the currently contracted rooms, then add more rows
        if ($rooms_added < $rooms_contracted) {
            RoomingList::addRoomsToTheList(
                $inventory,
                ($rooms_contracted - $rooms_added)
            );
        } else if ($rooms_added > $rooms_contracted) {
            // This means the inventory has changed and now there are fewer rooms
            // then originally contracted. So we need to remove some unassigned rooms
            // from the rooming list
            RoomingList::removeRoomsFromTheList(
                $inventory,
                ($rooms_added - $rooms_contracted)
            );
        }

    }

    private function getRoomsContractedCount($inventory)
    {
        /* The below logic was wrong. Can't compare min stays with pre_post nights.
        // Rooms contracted will be the maximum of min_stays_contracted and pre_post_nights_contracted
        // This is because if 10 min stays are contracted and 5 pre_post, then the 5 pre_post are actually the
        // same room as the min night just for different dates. Similarly if 10 pre_post are contracted and
        // 5 min nights, then again there are just 10 rooms contracted.
        return max($inventory->min_stays_contracted, $inventory->pre_post_nights_contracted);
         */

         // The rooms contracted count will simply be the min_stays_contracted.
         return $inventory->min_stays_contracted;
    }
}
