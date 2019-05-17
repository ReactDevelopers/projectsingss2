<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class RoomingListGuest extends Model
{
    /**
     * Relation with RoomingListGuestNight
     *
     * @return Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function rooming_list_guest_nights()
    {
        return $this->hasMany(RoomingListGuestNight::class);
    }

    /**
     * Get the client who owns this rooming list guest
     * @return Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function client()
    {
        return $this->belongsTo('App\Client');
    }

    /**
     * Get the race/hotel combo for the inventory row of rooming list guest
     */
    public function race_hotel()
    {
        return $this->belongsTo('App\RaceHotel', 'race_hotel_id');
    }

    /**
     * Accessor to get the row number. It is the concatenation
     * of list and client parts of the row number
     *
     * @return string
     */
    public function getRowNumberAttribute()
    {
        // Row Number is "list_row_num-client_row_num"
        return "{$this->list_row_number}-{$this->client_row_number}";
    }

    /**
     * Method to add a new guest to the rooming list.
     *
     * A new guest always has no name and no client assigned. Client is assigned
     * when the confirmation takes place. This method is generally called
     * when new inventory is added and hence client and guest goes in as null (empty)
     *
     * @param RoomingList $roomingList
     *
     * @return App\RoomingListGuest The new guest that was created
     */
    public static function addGuestToRoomingList(RoomingList $roomingList)
    {
        // Add an empty guest row (i.e. no client and guest assigned yet)
        self::unguard();

        // Find the list and client row number to put in for the guest (without client assigned)
        list($listRowNumber, $clientRowNumber) = self::getNewRowNumberWithoutClient(
            $roomingList->room_type_inventory->race_hotel_id
        );

        return self::create(
            [
                'rooming_list_id' => $roomingList->id,
                'race_hotel_id' => $roomingList->room_type_inventory->race_hotel_id,
                'client_id' => null,
                'races_hotels_inventory_id' => $roomingList->room_type_inventory->id,
                'list_row_number' => $listRowNumber,
                'client_row_number' => $clientRowNumber,
                'guest_name' => null,
            ]
        );
    }

    /**
     * Method to get the new row number for the guest when no client is assigned.
     * This means that the guest will get the last row number in the entire rooming list
     *
     * @param integer $raceHotelId
     *
     * @return array Array with list and client row numbers
     */
    public static function getNewRowNumberWithoutClient($raceHotelId)
    {
        // Find row with highest list row number for the given race hotel
        $lastRow = self::where('race_hotel_id', $raceHotelId)
            ->orderBy('list_row_number', 'desc')
            ->first();

        // If there is no row, then return 1-1 as the first row number
        if (! $lastRow) {
            return [1, 1];
        }

        // New row number will be formed by incrementing the both the list and client part of the last row
        $newListRowNumber = $lastRow->list_row_number + 1;
        $newClientRowNumber = $lastRow->client_row_number + 1;

        return [$newListRowNumber, $newClientRowNumber];
    }

    /**
     * Method to add a guest night for the current rooming_list_guest
     *
     * The method takes an array of guest night data i.e.
     *  - race_hotel_id
     *  - date
     *  - status
     *  - status_updated_at
     * And creates associated guest night with this data
     *
     * @param array $guest_night_data
     *
     * @return App\RoomingListGuestNight
     */
    public function addNight($guest_night_data)
    {
        RoomingListGuestNight::unguard();

        // Prepare the RoomingListGuestNight object with the given data
        $rooming_list_guest_night = new RoomingListGuestNight(
            [
                'race_hotel_id' => $guest_night_data['race_hotel_id'],
                'date' => $guest_night_data['date'],
                'status' => $guest_night_data['status'],
                'status_updated_at' => $guest_night_data['status_updated_at'],
            ]
        );

        // Create and associate the guest night with the current RoomingListGuest
        $this->rooming_list_guest_nights()->save($rooming_list_guest_night);

        return $rooming_list_guest_night;
    }

    /**
     * Method to compute and save the row number (list and client)
     *
     * The logic is as follows:
     *  - Find the row with the highest list number for the same client
     *   - If found, then this row's list and client number will be 1 more than the above
     *   - If not found, then it means this is the first row to which this client is being assigned,
     *     so find the highest list number for other assigned rows and add 1 to it to get the
     *     list number for this row
     *
     * @return App\RoomingListGuest Rooming list guest with computed row number saved
     */
    public function computeAndSaveRowNumber()
    {
        // Find the last assigned row for this client
        $last_assigned_same_client = self::where('client_id', $this->client_id)
            ->where('id', '<>', $this->id)
            ->where('race_hotel_id', $this->race_hotel_id)
            ->orderBy('list_row_number', 'desc')
            ->first();

        // If no last assigned row found, then it means this client is being assigned
        // for the first time to a room. So, the list number for this will be 1 more
        // than the list number of last assigned room across all clients
        if (! $last_assigned_same_client) {
            // This will be the first row for the client
            $client_row_number = 1;
            // Get the list row number which will be 1 more than the list row number
            // of last assigned across all clients
            $last_assigned = self::whereNotNull('client_id')
                ->where('id', '<>', $this->id)
                ->where('race_hotel_id', $this->race_hotel_id)
                ->orderBy('list_row_number', 'desc')
                ->first();

            $list_row_number = $last_assigned ?
                ($last_assigned->list_row_number + 1) : 1;
        } else {
            // We got a row with the same client assigned.
            // List row number will be 1 more than last rows'
            // and so will be the client row number
            $list_row_number = $last_assigned_same_client->list_row_number + 1;
            $client_row_number = $last_assigned_same_client->client_row_number + 1;
        }

        // Update the list and client row number for the newly assigned client guest
        $this->list_row_number = $list_row_number;
        $this->client_row_number = $client_row_number;
        $this->save();

        return $this;
    }

    /**
     * Method to renumber (increment) list_row_number for all rows
     * after the given rooming_list_guest.
     * Also the client_row_number is renumbered for those rows which do not have client assigned.
     * This method is called after a client is assigned to a rooming list guest.
     *
     * @param RoomingListGuest $anchor_rooming_list_guest The guest row to which a client has just been assigned
     *
     * @return void
     */
    public static function renumberRowAfterGuest(RoomingListGuest $anchor_rooming_list_guest)
    {
        $starting_list_row_number = $anchor_rooming_list_guest->list_row_number + 1;

        // Find all guests having list row number equal to or more than anchor's list row number
        $rooming_list_guests = self::where('id', '<>', $anchor_rooming_list_guest->id)
            ->where('list_row_number', '>=', $anchor_rooming_list_guest->list_row_number)
            ->where('race_hotel_id', $anchor_rooming_list_guest->race_hotel_id)
            ->orderBy('list_row_number')
            ->get();

        foreach ($rooming_list_guests as $rooming_list_guest) {
            $rooming_list_guest->list_row_number = $starting_list_row_number++;
            $rooming_list_guest->save();
        }

        // Find all guests without any client and renumber client_row_numebr for them
        $rooming_list_guests = self::whereNull('client_id')
            ->where('race_hotel_id', $anchor_rooming_list_guest->race_hotel_id)
            ->orderBy('list_row_number')
            ->get();

        $client_row_number = 1;

        foreach ($rooming_list_guests as $rooming_list_guest) {
            $rooming_list_guest->client_row_number = $client_row_number++;
            $rooming_list_guest->save();
        }
    }

    /**
     * Method to get the rooming listings
     *
     * @param integer $raceHotelId
     *
     * @return Collection of RoomingListGuest
     */
    public static function getRoomingList($raceHotelId)
    {
        $rooming_list_guests = self::where('race_hotel_id', $raceHotelId)
            ->orderBY('list_row_number')
            ->get();

        return $rooming_list_guests;
    }

    /**
     * Method to get the room type breakdown
     *
     * @param integer $raceHotelId
     *
     * @return Collection of RoomingListGuest
     */
    public static function getRoomtTypeBreakdown($raceHotelId)
    {
        $room_type_breakdown = self::where('race_hotel_id', $raceHotelId)
            ->groupBy('races_hotels_inventory_id')
            ->get();

        $room_type_breakdown->load('race_hotel.signed_confirmations.confirmation_items');

        return $room_type_breakdown;
    }
}
