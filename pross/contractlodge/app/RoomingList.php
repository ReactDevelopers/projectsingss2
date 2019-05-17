<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Events\RoomingListDeleted;
use App\Events\ClientAssigned;

class RoomingList extends Model
{
    /**
     * The event map for the model.
     *
     * @var array
     */
    protected $dispatchesEvents = [
        'deleting' => RoomingListDeleted::class,
    ];

    /**
     * Relation with Room Type i.e. RaceHotelInventory
     *
     * @return Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function room_type_inventory()
    {
        return $this->belongsTo(RaceHotelInventory::class, 'races_hotels_inventory_id');
    }

    /**
     * Relation with RoomingListGuest
     *
     * @return Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function rooming_list_guests()
    {
        return $this->hasMany(RoomingListGuest::class);
    }

    /**
     * Scope a query to only include client unassigned rooming lists
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeUnassigned($query)
    {
        return $query->whereNull('client_id');
    }

    /**
     * Scope a query to only include client assigned rooming lists
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeAssigned($query)
    {
        return $query->whereNotNull('client_id');
    }


    /**
     * Method to added new rooms to the rooming list for the given inventory.
     *
     * This method also adds empty guests to each new room being added to the list.
     *
     * @param RaceHotelInventory $inventory Inventory for which the rooms are to be added
     * @param integer $number_of_rooms        Number of rooms to add
     *
     * @return void
     */
    public static function addRoomsToTheList(RaceHotelInventory $inventory, $number_of_rooms)
    {
        self::unguard();

        for ($i = 1; $i <= $number_of_rooms; $i++) {
            $rooming_list = self::create(
                [
                    'race_hotel_id' => $inventory->race_hotel_id,
                    'client_id' => null,
                    'races_hotels_inventory_id' => $inventory->id,
                ]
            );

            // Add empty guest rows for this rooming list
            RoomingListGuest::addGuestToRoomingList($rooming_list);
        }
    }

    /**
     * Method to remove unassigned rooms from the rooming list
     *
     * This method is called when the inventory is decreased.
     *
     * @param App\RaceHotelInventory $inventory
     * @param integer $number_of_rooms
     *
     * @return void
     */
    public static function removeRoomsFromTheList(RaceHotelInventory $inventory, $number_of_rooms)
    {
        // Only those rooms can be removed which are not assigned a client i.e. which is not confirmed
        // Find all such rooms from the rooming list
        $rooming_lists_to_remove = self::where('races_hotels_inventory_id', $inventory->id)
            ->whereNull('client_id')
            ->orderBy('created_at', 'desc')
            ->limit($number_of_rooms)
            ->get();

        foreach ($rooming_lists_to_remove as $rooming_list) {
            // Delete rooming
            $rooming_list->delete();
        }
    }

    /**
     * Method to assign client to rooming list for the just signed confirmation
     *
     * Here we are assuming that the confirmation being passed is signed.
     *
     * @param Confirmation $confirmation
     *
     * @return void
     */
    public static function assignClient(Confirmation $confirmation)
    {
        $confirmation_items = $confirmation->confirmation_items()->get();
        $min_guest_nights = collect();

        // Assign client to all the rooming list for the inventories of this confirmation
        foreach ($confirmation_items as $confirmation_item) {
            $guest_nights = self::assignClientForConfirmationItemWithinMinNights($confirmation_item, $confirmation->client_id);
            $min_guest_nights = $min_guest_nights->merge($guest_nights);
        }

        $guest_nights_group = [];

        foreach ($min_guest_nights as $min_guest_night) {
            $guest_nights_group[$min_guest_night->rooming_list_guest_id][] = $min_guest_night;
        }

        // Run a loop on confirmation_items again and insert the guest nights for pre-post
        foreach ($confirmation_items as $confirmation_item) {
            // If this is a pre-post confirmation item
            if (! $confirmation_item->is_within_min_night_range) {
                self::addPrePostGuestNightsForConfirmationItem($confirmation_item);
            }
        }
    }

    /**
     * Method to assigned client to rooming list for the given confirmation items
     *
     * @param  App\ConfirmationItem $confirmation_item
     * @param  integer $client_id
     *
     * @return void
     */
    public static function assignClientForConfirmationItemWithinMinNights(ConfirmationItem $confirmation_item, $client_id)
    {
        $race_hotel_id = $confirmation_item->confirmation->race_hotel_id;

        $guest_nights = collect();

        if ($confirmation_item->is_within_min_night_range) {
            // Find unassigned rooming lists for the given inventory (confirmation item)
            $unassigned_rooming_lists = self::unassigned()
                ->where('races_hotels_inventory_id', $confirmation_item->races_hotels_inventory_id)
                ->limit($confirmation_item->quantity)
                // sort by created_at so that clients are assigned serially from top to bottom
                ->orderBy('created_at', 'asc')
                ->get();

            // Assign the given client to the rooming lists
            foreach ($unassigned_rooming_lists as $rooming_list) {
                $rooming_list->client_id = $client_id;
                $rooming_list->save();
                // Also assign the client to the related guest row
                foreach ($rooming_list->rooming_list_guests()->get() as $guest) {
                    $guest->client_id = $client_id;
                    $guest->save();
                    event(new ClientAssigned($guest));

                    // Add guest nights as well
                    $rooming_list_guest_nights = self::addGuestNightsForAssignedClient($guest, $confirmation_item);
                    $guest_nights = $guest_nights->merge($rooming_list_guest_nights);
                }
            }
        }

        return $guest_nights;
    }

    /**
     * Method to add guest nights while assigning client to rooming list.
     *
     * This method should be called only when a new confirmation is signed
     *
     * @param App\RoomingListGuest $rooming_list_guest
     * @param  App\ConfirmationItem $confirmation_item
     * @return void
     */
    public static function addGuestNightsForAssignedClient(RoomingListGuest $rooming_list_guest, ConfirmationItem $confirmation_item)
    {
        // Get the date range between check in and checkout. We are subtracting 1 day from the checkout because
        // the guest will not stay on checkout date night.
        $dates = generate_date_range($confirmation_item->check_in, $confirmation_item->check_out->subDay());

        $return = collect();

        // Add guest nights for the date range
        foreach ($dates as $date) {
            $guest_night_data = [
                'race_hotel_id' => $rooming_list_guest->race_hotel_id,
                'date' => $date,
                'status' => RoomingListGuestNight::STATUS_USED,
                'status_updated_at' => now(),
            ];

            $rooming_list_guest_night = $rooming_list_guest->addNight($guest_night_data);

            $return->push($rooming_list_guest_night);

        }

        return $return;
    }

    /**
     * Method to add pre-post guest nights for the given ConfirmationItem
     *
     * We will add pre-post nights to existing RoomingListGuest for the given inventory
     *
     * @param App\ConfirmationItem $confirmation_item
     *
     * @return void
     */
    public static function addPrePostGuestNightsForConfirmationItem(ConfirmationItem $confirmation_item)
    {
        $client_id = $confirmation_item->confirmation->client_id;

        // Find all guests matching the races_hotels_inventory_id for the given confirmation item
        // Find it in the order of client_row_number so that pre-post nights are first assigned serially
        $rooming_list_guests = RoomingListGuest::where('client_id', $client_id)
            ->where('races_hotels_inventory_id', $confirmation_item->races_hotels_inventory_id)
            ->orderBy('client_row_number')
            ->get();

        // Run a loop for the number of nights booked
        for ($i = 1; $i <= $confirmation_item->quantity; $i++) {
            // Get the date range between check in and checkout. We are subtracting 1 day from the checkout because
            // the guest will not stay on checkout date night.
            $dates = generate_date_range($confirmation_item->check_in, $confirmation_item->check_out->subDay());
            self::addPrePostGuestNights($rooming_list_guests, $dates);
        }
    }

    /**
     * Method to add guest nights for the given dates
     *
     * Nights are added serially to the RoomingListGuest so that 1st guest
     * gets the maximum nights and so on
     *
     * @param Illuminate\Support\Collection $rooming_list_guests Collection of RoomingListGuest
     * @param array                         $dates               Array of dates
     *
     * @return void
     */
    public static function addPrePostGuestNights($rooming_list_guests, $dates)
    {
        foreach ($dates as $date) {
            // Find the first rooming_list_guest without this date
            // and then insert this date row in rooming_list_guest_nights
            $key = $rooming_list_guests->search(function ($rooming_list_guest, $key) use ($date) {
                $guest_night = $rooming_list_guest->rooming_list_guest_nights()
                    ->where('date', $date)
                    ->first();

                // If no guest night found for the given date, then it means we can add
                // the given date/night for this rooming_list_guest
                return ! $guest_night ? true : false;
            });

            // If we find a rooming list guest without having an associated night for the given date
            if ($key !== false) {
                // Insert the row in rooming_list_guest_nights for the given date and rooming_list_guest
                $guest_night_data = [
                    'race_hotel_id' => $rooming_list_guests[$key]->race_hotel_id,
                    'date' => $date,
                    'status' => RoomingListGuestNight::STATUS_USED,
                    'status_updated_at' => now(),
                ];

                $rooming_list_guests[$key]->addNight($guest_night_data);
            }
        }
    }
}
