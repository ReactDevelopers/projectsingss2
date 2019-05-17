<?php

namespace App\Exports;

use App\RaceHotel;
use App\RoomingListGuest;
use App\RoomingListGuestNight;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class RoomingListGuestsExport implements FromView
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

    /**
    * @return \Illuminate\Support\Collection
    */
    /*
    public function collection()
    {
        return RoomingListGuest::all();
    }
    */

    public function view(): View
    {
        $meta = RaceHotel::where('race_id', $this->race_id)
            ->where('hotel_id', $this->hotel_id)
            ->whereNull('races_hotels.deleted_at')
            ->first();

        $rooming_list_guests = RoomingListGuest::getRoomingList($meta->id);

        $export = true;

        return view('partials.common.rooming-listing', [
            'rooming_list_guests' => $rooming_list_guests,
            'meta' => $meta,
            'export' => $export,
        ]);
    }
}
