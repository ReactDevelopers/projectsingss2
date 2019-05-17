<?php

namespace App\Http\Controllers\Api\V1\RacesHotels;

use App\Race;
use App\Hotel;
use App\Country;
use App\Currency;
use App\RaceHotel;
use App\RaceHotelInventory;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Input;
use App\Jobs\RacesHotels\RacesHotelsUpdate;
use App\Http\Requests\RacesHotels\RacesHotelsStoreRequest;

class RaceHotelController extends Controller
{
    /**
     * Action to create new room types.
     * @param  RacesHotelsStoreRequest $request
     * @return Response
     */
    public function store(RacesHotelsStoreRequest $request)
    {
        $success = dispatch_now(new RacesHotelsUpdate($request));

        return response()->json([], $success ? 200 : 422);
    }

    /**
     * Get inventory data for a race and hotel
     * @param  int $race_id
     * @param  int $hotel_id
     * @return Response
     */
    public function show($race_id, $hotel_id)
    {
        $meta = RaceHotel::where('race_id', $race_id)
            ->where('hotel_id', $hotel_id)
            ->with('room_type_inventories')
            ->first();

        return response()->json(['meta' => $meta]);
    }

    /**
     * Get rooming list data from RaceHotel model
     * @param  int $race_id
     * @param  int $hotel_id
     * @return Response
     */
    public function roomingListData($race_id, $hotel_id)
    {
        $meta = RaceHotel::where('race_id', $race_id)
            ->where('hotel_id', $hotel_id)
            ->whereNull('races_hotels.deleted_at')
            ->first();

        return response()->json(['meta' => $meta]);
    }
}
