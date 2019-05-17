<?php

namespace App\Http\Controllers;


use App\Race;
use App\Hotel;
use App\RaceHotel;
use App\RoomingListGuest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;

class RaceHotelReservationController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');

        // $this->middleware('subscribed');
    }

    /**
     * Display a listing of the resource.
     *
     * @param  \App\Race $race
     * @param  \App\Hotel $hotel
     * @return \Illuminate\Http\Response
     */
    public function index(Race $race, Hotel $hotel)
    {
        $meta = RaceHotel::where('race_id', $race->id)
            ->where('hotel_id', $hotel->id)
            ->whereNull('races_hotels.deleted_at')
            ->first()->load([
                'signed_confirmations_order_by_client_id.confirmation_items',
                'room_type_inventories',
        ]);

        $rooming_list_guests = RoomingListGuest::getRoomingList($meta->id);

        $room_type_breakdown = RoomingListGuest::getRoomtTypeBreakdown($meta->id);

        return view('reservations/index', compact('race', 'hotel', 'meta', 'rooming_list_guests', 'room_type_breakdown'));
    }

    /**
     * Create a resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
