<?php

use App\Client;
use Carbon\Carbon;
use App\RaceHotel;
use App\Confirmation;
use App\ConfirmationItem;
use App\RoomingListGuest;
use App\RaceHotelInventory;
use App\RoomingListGuestNight;
use Illuminate\Support\Collection;

if (! function_exists('format_input_date_to_system')) {
    /**
     * Function to convert the form input dates (typically in d/m/Y format)
     * to system/mysql format i.e. Y-m-d
     *
     * @param string $inputDate
     * @return string
     */
    function format_input_date_to_system($inputDate)
    {
        $dateArray = explode('/', $inputDate);

        // If the input date has slashes in it, then we assume it is in the d/m/Y format (UK)
        if (strpos($inputDate, '/') !== false && count($dateArray) === 3) {

            $day = $dateArray[0];
            $month = $dateArray[1];
            $year = $dateArray[2];

            return "$year-$month-$day";
        }

        return $inputDate;
    }
}

if (! function_exists('set_inventory_stats')) {
    /**
     * Appends inventory models with "stays sold", "stays on offer" (for min night stays)
     * and pre/post as "nights" instead of "stays"
     *
     * @param Collection $inventories
     * @return Collection
     */
    function set_inventory_stats(Collection $inventories)
    {
        foreach ($inventories as $room_type) {
            if ($room_type->race_hotel && count($room_type->confirmation_items) > 0) {
                $room_type->min_stays_sold = get_stays($room_type->race_hotel->signed_confirmations, $room_type->id);
                $room_type->min_stays_on_offer = get_stays($room_type->race_hotel->on_offer_confirmations, $room_type->id);
                $room_type->pre_post_nights_sold = get_room_nights($room_type->race_hotel->signed_confirmations, 'pre_post', $room_type->id);
                $room_type->pre_post_nights_on_offer = get_room_nights($room_type->race_hotel->on_offer_confirmations, 'pre_post', $room_type->id);
            }
        }
    }
}

if (! function_exists('get_room_nights')) {
    /**
     * Returns the number of "room nights" (quantity x num nights).
     *
     * @param  Collection $confirmations
     * @param  string     $night_type    "min", "pre_post", or null
     * @param  integer    $room_type_id  or null
     * @return integer
     */
    function get_room_nights(Collection $confirmations, $night_type = null, $room_type_id = null)
    {
        return get_total($confirmations, false, $night_type, $room_type_id);
    }
}

if (! function_exists('get_stays')) {
    /**
     * Returns the number of "stays" (number of rooms, irrespective of the date range).
     * This only applies to min_nights, not pre_post_nights
     *
     * @param  Collection $confirmations
     * @param  integer    $room_type_id  or null
     * @return integer
     */
    function get_stays(Collection $confirmations, $room_type_id = null)
    {
        return get_total($confirmations, true, 'min', $room_type_id);
    }
}

if (! function_exists('get_total')) {
    /**
     * Gets the number of room nights (or stays) from a collection of confirmations. i.e.,
     * you can send in a collection of signed, unsigned, or on offer confirmations
     * and the response will be the product of # rooms x # nights in stay.
     *
     * @param  Collection $confirmations
     * @param  boolean    $count_as_stay If true, returns quantity rooms as a "stay". If false, returns total room nights.
     * @param  string     $night_type    "min", "pre_post", or null
     * @param  integer    $room_type_id  or null
     * @return integer
     */
    function get_total(Collection $confirmations, $count_as_stay = false, $night_type = null, $room_type_id = null)
    {
        if (empty($confirmations)) {
            return 0;
        }

        $count = 0;
        // FIXME: I'm not liking nested foreach loops again. Ugh.
        foreach ($confirmations as $confirmation) {

            if (empty($confirmation->confirmation_items)) {
                continue;
            }

            foreach ($confirmation->confirmation_items as $item) {
                if ($item->races_hotels_inventory_id == $room_type_id || $room_type_id == null) {
                    if (($night_type == 'min' && $item->is_within_min_night_range)
                        || ($night_type == 'pre_post' && ! $item->is_within_min_night_range)
                        || (empty($night_type))) {

                        if ($count_as_stay) {
                            $count += $item->quantity;
                        } else {
                            $count += $item->friendly_room_nights;
                        }
                    }
                }
            }
        }

        return $count;
    }
}

if (! function_exists('get_client_name')) {
    /**
     * Function to get client name
     *
     * @param int $client_id
     * @return string
     */
    function get_client_name($client_id)
    {
        $client = Client::find($client_id);
        if ($client) {
            return $client->name;
        }
        return '';
    }
}

if (! function_exists('get_room_name')) {
    /**
     * Function to get room name
     *
     * @param int $room_id
     * @return string
     */
    function get_room_name($room_id)
    {
        $room = RaceHotelInventory::find($room_id);
        return $room->room_name;
    }
}

if (! function_exists('generate_date_range')) {
    /**
     * Generate an array of date ranges for the given start and end dates
     *
     * @param  \Illuminate\Support\Carbon $start_date Start date as a Carbon object
     * @param  \Illuminate\Support\Carbon $end_date   End date as a Carbon object
     *
     * @return array Array of dates between the start and end
     */
    function generate_date_range(Illuminate\Support\Carbon $start_date, Illuminate\Support\Carbon $end_date)
    {
        $dates = [];

        for ($date = $start_date; $date->lte($end_date); $date->addDay()) {
            $dates[] = $date->format('Y-m-d');
        }

        return $dates;
    }
}

if (! function_exists('generate_date_range_rooming_list')) {
    /**
     * Generate an array of date ranges for the given start and end dates
     *
     * @param  \Illuminate\Support\Carbon $start_date Start date as a Carbon object
     * @param  \Illuminate\Support\Carbon $end_date   End date as a Carbon object
     *
     * @return array Array of dates between the start and end
     */
    function generate_date_range_rooming_list(Illuminate\Support\Carbon $start_date, Illuminate\Support\Carbon $end_date)
    {
        $dates = [];

        for ($date = $start_date; $date->lte($end_date); $date->addDay()) {
            $dates[] = Carbon::parse($date)->format('D j M');
        }

        return $dates;
    }
}

if (! function_exists('check_night_assigned')) {
    /**
     * check night date and status matches or not with passed argument date and status
     *
     * @param  int $rooming_list_guest_id
     * @param  int $race_hotel_id
     * @param  string $date
     * @param  string $status
     *
     * @return return boolean true or false
     */
    function check_night_status($rooming_list_guest_id, $race_hotel_id, $date, $status)
    {
        $nightExists = RoomingListGuestNight::where('rooming_list_guest_id', $rooming_list_guest_id)
            ->where('race_hotel_id', $race_hotel_id)
            ->where('date', $date)
            ->where('status', $status)
            ->exists();

        if ($nightExists) {
            return true;
        }

        return false;
    }
}

if (! function_exists('check_min_night_range')) {
    /**
     * check passed argument date is between min-check-in and min-check-out dates
     *
     * @param  \Illuminate\Support\Carbon  $inventory_min_check_in
     * @param  \Illuminate\Support\Carbon  $inventory_min_check_out
     * @param  string $date
     *
     * @return return boolean true or false
     */
    function check_min_night_range(Illuminate\Support\Carbon $inventory_min_check_in, Illuminate\Support\Carbon $inventory_min_check_out, $date)
    {
        $first = Carbon::parse($inventory_min_check_in);
        $second = Carbon::parse($inventory_min_check_out);
        $is_within_range = Carbon::parse($date)->between($first, $second->subDay());

        if ($is_within_range) {
            return true;
        }

        return false;
    }
}

if (! function_exists('get_confirmation_total_rooms')) {
    /**
     * Gets the number of room nights (or stays) from an instance of confirmation. i.e.,
     * you can send in a instance of signed, unsigned, or on offer confirmations
     * and the response will be the product of # rooms x # nights in stay.
     *
     * @param  App\Confirmation $confirmations
     * @return integer
     */
    function get_confirmation_total_rooms(Confirmation $confirmation)
    {
        $count = 0;

        if (empty($confirmation) || empty($confirmation->confirmation_items)) {
            return $count;
        }

        foreach ($confirmation->confirmation_items as $item) {
            if ($item->is_within_min_night_range) {
                $count += $item->quantity;
            } else {
                $count += $item->friendly_room_nights;
            }
        }

        return $count;
    }
}

if (! function_exists('get_confirmation_total_amount')) {
    /**
     * Gets the total dollar amount of the confirmation.
     *
     * @param  Confirmation $confirmation
     *
     * @return integer
     */
    function get_confirmation_total_amount(Confirmation $confirmation)
    {
        $amount = 0;

        if (empty($confirmation) || empty($confirmation->confirmation_items)) {
            return $amount;
        }

        foreach ($confirmation->confirmation_items as $item) {
            $amount += $item->quantity * $item->rate;
        }

        return $amount;
    }
}

if (! function_exists('get_rows_total_nights')) {
    /**
     * get total night of rooming list by passed argument date
     *
     * @param  int $race_hotel_id
     * @param  string $date
     *
     * @return return count
     */
    function get_rows_total_nights($race_hotel_id, $date)
    {
        $count = RoomingListGuestNight::where('race_hotel_id', $race_hotel_id)
            ->where('date', $date)
            ->count();

        if ($count) {
            return $count;
        }

        return $count = 0;
    }
}

if (! function_exists('get_total_confirmed_rooms')) {
    /**
     * Gets the number of room nights (or stays) from an Collection of confirmation. i.e.,
     * you can send in a instance of signed, unsigned, or on offer confirmations
     * and the response will be the product of # rooms x # nights in stay.
     *
     * @param  App\Confirmation $confirmations
     * @return integer
     */
    function get_total_confirmed_rooms(Collection $confirmations, $races_hotels_inventory_id)
    {
        $count = 0;

        if (empty($confirmations)) {
            return $count;
        }

        foreach ($confirmations as $items) {
            foreach ($items->confirmation_items as $item) {
                if ($item->races_hotels_inventory_id == $races_hotels_inventory_id) {
                    if ($item->is_within_min_night_range) {
                        $count += $item->quantity;
                    } else {
                        $count += $item->friendly_room_nights;
                    }
                }
            }
        }

        return $count;
    }
}

if (! function_exists('getNumRoomsInSignedConfirmations')) {
     /**
     * Returns the number of rooms in Signed confirmations (same room type)
     * for date range (ie, min night vs pre/post).
     *
     * @param  {Integer} room_id       Room Id
     * @param  {Integer} race_hotel_id Race Hotel Id
     * @param  {String} night_type min_nights / pre_post_nights
     *
     * @return {Integer}
     */
    function getNumRoomsInSignedConfirmations($room_id, $race_hotel_id, $night_type)
    {
        $confirmationIds = Confirmation::where(function ($query) {
            $query->whereDate('expires_on', '>=', date('Y-m-d'))
                ->orWhereNull('expires_on')
                ->orWhereNotNull('signed_on');
            })->whereNull('deleted_at')
            ->pluck('id')
            ->toArray();

        $query = ConfirmationItem::with('races_hotels_inventory')
            ->whereIn('confirmation_id', $confirmationIds)
            ->where('races_hotels_inventory_id', '=', $room_id);

        $quantity = $query->whereNull('deleted_at')->sum('quantity');
        $quantity = (! empty($quantity)) ? $quantity : 0;

        $race_hotel = RaceHotel::select('inventory_min_check_in', 'inventory_min_check_out')
            ->where('id', $race_hotel_id)
            ->whereNull('deleted_at')
            ->first();

        $min_nt_quantity = $query->whereDate('check_in', '>=', $race_hotel->inventory_min_check_in)
            ->whereDate('check_out', '<=', $race_hotel->inventory_min_check_out)
            ->whereNull('deleted_at')
            ->sum('quantity');

        if ($night_type == 'min_nights') {
            return $min_nt_quantity = (! empty($min_nt_quantity)) ? $min_nt_quantity : 0;
        } elseif ($night_type == 'pre_post_nights') {
            return $pp_nt_quantity = $quantity - $min_nt_quantity;
        }
    }
}
