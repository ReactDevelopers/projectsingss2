<?php

namespace App\Imports;

use App\RaceHotel;
use App\RoomingListGuest;
use App\RoomingListGuestNight;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;

class RoomingListGuestsImport implements ToCollection
{
    /**
     * Race
     * @var integer
     */
    public $race_id;

    /**
     * Hotel
     * @var integer
     */
    public $hotel_id;

    /**
     * Create a new controller instance.
     * @param  integer $race_id
     * @param  integer $hotel_id
     * @return void
     */
    public function __construct($race_id, $hotel_id)
    {
        $this->race_id = $race_id;
        $this->hotel_id = $hotel_id;
    }

    public function collection(Collection $rows)
    {
        $meta = RaceHotel::where('race_id', $this->race_id)
            ->where('hotel_id', $this->hotel_id)
            ->whereNull('races_hotels.deleted_at')
            ->first();

        $dates_range = generate_date_range($meta->inventory_min_check_in->subDay(7), $meta->inventory_min_check_out->addDay(5));
        $date_range_count = count($dates_range);

        $count = count($rows);

        if ($count >=  3) {
            unset($rows[$count - 1]);
            unset($rows[0]);
            unset($rows[1]);
        }

        foreach ($rows as $row) {
            foreach ($row as $key => $value) {
                if ($key !== 0) {
                    continue;
                }

                $row_client_arr = explode('-', $value);
                $list_row_number_id = $row_client_arr[0];
                $client_row_number_id = $row_client_arr[1];

                $rooming_list_guests = RoomingListGuest::where('list_row_number', $list_row_number_id)
                    ->where('client_row_number', $client_row_number_id)
                    ->where('race_hotel_id', $meta->id)
                    ->whereNotNull('client_id')
                    ->first();

                if ($rooming_list_guests) {

                    // Next update guest_name , notes and confirmation_number fields in rooming_list_guest table

                    if (!empty($row[3])) {
                        $rooming_list_guests->guest_name = $row[3];
                    }

                    if (!empty($row[$date_range_count + 4])) {
                        $rooming_list_guests->notes = $row[$date_range_count + 4];
                    }

                    if (!empty($row[$date_range_count + 5])) {
                        $rooming_list_guests->confirmation_number = $row[$date_range_count + 5];
                    }

                    $rooming_list_guests->save();

                    // Next update status and status_updated_at fields in rooming_list_guest_nights table

                    $rooming_list_guest_id = $rooming_list_guests->id;
                    $race_hotel_id = $rooming_list_guests->race_hotel_id;
                    $counter = 1;

                    foreach ($dates_range as $key => $date) {

                        $rooming_list_guests_nights = RoomingListGuestNight::where('rooming_list_guest_id', $rooming_list_guest_id)
                            ->where('race_hotel_id', $race_hotel_id)
                            ->where('date', $date)
                            ->first();

                        if (! $rooming_list_guests_nights) {
                            $counter++;
                            continue;
                        }

                        if ($row[$key + 4] == config('rooming_list.statuses_abbreviations.'.\App\RoomingListGuestNight::STATUS_USED)) {
                            $status = \App\RoomingListGuestNight::STATUS_USED;
                        } elseif ($row[$key + 4] == config('rooming_list.statuses_abbreviations.'.\App\RoomingListGuestNight::STATUS_UNUSED)) {
                            $status = \App\RoomingListGuestNight::STATUS_UNUSED;
                        } elseif ($row[$key + 4] == config('rooming_list.statuses_abbreviations.'.\App\RoomingListGuestNight::STATUS_RESELL)) {
                            $status = \App\RoomingListGuestNight::STATUS_RESELL;
                        } elseif ($row[$key + 4] == config('rooming_list.statuses_abbreviations.'.\App\RoomingListGuestNight::STATUS_EARLY_CHECKIN)) {
                            $status = \App\RoomingListGuestNight::STATUS_EARLY_CHECKIN;
                        } elseif ($row[$key + 4] == config('rooming_list.statuses_abbreviations.'.\App\RoomingListGuestNight::STATUS_LATE_CHECKOUT)) {
                            $status = \App\RoomingListGuestNight::STATUS_LATE_CHECKOUT;
                        } elseif ($row[$key + 4] == config('rooming_list.statuses_abbreviations.'.\App\RoomingListGuestNight::STATUS_RESOLD)) {
                            $status = \App\RoomingListGuestNight::STATUS_RESOLD;
                        }

                        if (!empty($status)) {
                            $rooming_list_guests_nights->status = $status;
                            $rooming_list_guests_nights->status_updated_at = now();

                            $rooming_list_guests_nights->save();
                        }
                        $counter++;
                    }
                }
            }
        }
    }
}
