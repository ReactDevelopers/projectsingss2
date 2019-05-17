<?php
namespace App\Http\Controllers;

use App\Bill;
use App\Race;
use App\Hotel;
use App\Client;
use App\RaceHotel;
use App\Confirmation;
use App\CustomInvoice;
use Illuminate\Http\Request;


class RaceHotelClientController extends Controller
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
        // return view('clients/index', compact('race_id'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @param  integer $hold_id ID of a hold that we want to use as data to create contract from
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        // return view('clients/create', compact('race_id', 'hold_id'));
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
     * @param  \App\Race $race
     * @param  \App\Hotel $hotel
     * @param  \App\Client $client
     * @return \Illuminate\Http\Response
     */
    public function show(Race $race, Hotel $hotel, Client $client)
    {
        $meta = RaceHotel::where('race_id', $race->id)
            ->where('hotel_id', $hotel->id)
            ->whereNull('races_hotels.deleted_at')
            ->first()
            ->load('currency');

        $confirmations = Confirmation::where('client_id', $client->id)
            ->where('race_hotel_id', $meta->id)
            ->get()
            ->load('currency', 'client', 'confirmation_items', 'payments', 'race_hotel.hotel', 'race_hotel.race');

        $invoices = CustomInvoice::where('client_id', $client->id)
            ->where('race_hotel_id', $meta->id)
            ->get()
            ->load('currency', 'client', 'invoice_items', 'payments', 'race_hotel.hotel', 'race_hotel.race');

        $recievables = $confirmations->concat($invoices)->sortBy('created_at');

        $bill = Bill::where('race_hotel_id', $meta->id)
            ->with([
                'payments',
                'currency',
                'currency_exchange'
            ])->first();

        return view('clients.show', compact([
            'race',
            'hotel',
            'client',
            'confirmations',
            'invoices',
            'recievables',
            'bill',
        ]));
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
