<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use App\Race;
use App\Hotel;
use App\RaceHotel;
use App\Bill;
use App\Currency;

class RaceHotelBillController extends Controller
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
    public function edit(Race $race, Hotel $hotel)
    {
        $meta = RaceHotel::where('race_id', $race->id)
            ->where('hotel_id', $hotel->id)
            ->with('room_type_inventories')
            ->first()->load(['currency']);

        $bill = Bill::where('race_hotel_id', $meta->id)->first();

        if ($bill) {
            $bill = $bill->load(['currency','currency_exchange']);
        }

        $currencies = Currency::orderBy('name', 'ASC')->get();

        return view('bills.edit', compact('race', 'hotel', 'meta', 'bill', 'currencies'));
    }

    /**
     * Create a resource.
     *
     * @param  int     $race_id
     * @param  int     $hotel_id
     * @param  string  $bill_type     "confirmations" or "extras"
     * @return \Illuminate\Http\Response
     */
    public function create($race_id, $hotel_id, $bill_type = "extras")
    {
        return view('bills.create', compact('race_id', 'hotel_id', 'bill_type'));
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
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $race_id
     * @param  int  $hotel_id
     * @param  int  $bill_id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, $race_id, $hotel_id, $bill_id)
    {
        // FIXME: This is temporary to allow for different views based on URL.
        // This is not necessary for real functionality when we get beyond HTML prototypes.
        $bill_type = $request->input('bill_type', 'extras');

        return view('bills.show', compact('race_id', 'hotel_id', 'bill_id', 'bill_type'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $race_id
     * @param  int  $race_id
     * @param  int  $bill_id
     * @return \Illuminate\Http\Response
     */
    // public function edit(Request $request, $race_id, $hotel_id, $bill_id)
    // {
    //     // FIXME: This is temporary to allow for different views based on URL.
    //     // This is not necessary for real functionality when we get beyond HTML prototypes.
    //     $bill_type = $request->input('bill_type', 'extras');

    //     return view('bills/edit', compact('race_id', 'bill_id', 'hotel_id', 'bill_type'));
    // }

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
