<?php

namespace App\Http\Controllers;

use App\Race;
use App\Hotel;
use App\RaceHotel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;

class RaceHotelInvoiceController extends Controller
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
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('invoices.index');
    }

    /**
     * Create a resource.
     *
     * @param  Race    $race
     * @param  Hotel   $hotel
     * @param  string  $invoice_type     "confirmations" or "extras"
     * @return \Illuminate\Http\Response
     */
    public function create(Race $race, Hotel $hotel, $invoice_type = "extras")
    {
        $meta = RaceHotel::where('race_id', $race->id)
            ->where('hotel_id', $hotel->id)
            ->with('room_type_inventories')
            ->first();

        $inventories = $meta->room_type_inventories;

        return view('invoices.create', compact('invoice_type', 'race', 'hotel', 'meta', 'inventories'));
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
        return view('invoices.show');
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
