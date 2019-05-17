<?php

namespace App\Http\Controllers;

use App\Client;
use App\RaceHotel;
use App\Confirmation;
use App\CustomInvoice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;

class ClientInvoiceController extends Controller
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
     * @param  int     $client_id
     * @param  string  $invoice_type     "confirmations" or "extras"
     * @return \Illuminate\Http\Response
     */
    public function create($client_id, $invoice_type = "extras")
    {
        return view('invoices.create', compact('client_id', 'invoice_type'));
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
     * @param  int  $client_id
     * @param  int  $invoice_id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, Client $client, CustomInvoice $custom_invoice)
    {
        $contact_client = $client->contacts()->first();
        $race_hotel = $custom_invoice->load('race_hotel');
        $hotel = null;
        $race = null;
        $contact_hotel = null;
        $meta = null;

        if (! empty($race_hote)) {
            $hotel = $race_hotel->hotel;
            $race = $race_hotel->race;
            $contact_hotel = $hotel->contacts()->first();
            $meta = RaceHotel::where('race_id', $race->id)
                ->where('hotel_id', $hotel->id)
                ->first()
                ->load('currency');
        }

        return view('invoices.show', compact('custom_invoice' ,'race', 'hotel' , 'client' , 'contact_client' , 'contact_hotel', 'meta'));
    }

    /**
     * Display the Rooms Confirmation.
     *
     * @param  \App\Race $race
     * @param  \App\Hotel $hotel
     * @param  \App\Client $client
     * @param  \App\Confirmation $confirmation
     * @return \Illuminate\Http\Response
     */
    public function showConfirmation(Request $request, Client $client, Confirmation $confirmation)
    {
        $confirmation->load(['race_hotel']);
        $confirmation->confirmation_items->load('races_hotels_inventory');
        $contact_client = $client->contacts()->first();
        $hotel = $confirmation->race_hotel->hotel;
        $race = $confirmation->race_hotel->race;
        $contact_hotel = $hotel->contacts()->first();
        $meta = RaceHotel::where('race_id', $race->id)
            ->where('hotel_id', $hotel->id)
            ->whereNull('races_hotels.deleted_at')
            ->first()
            ->load('currency');

        return view('invoices.show', compact('confirmation' ,'race', 'hotel' , 'client' , 'contact_client' , 'contact_hotel', 'meta'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $client_id
     * @param  int  $invoice_id
     * @return \Illuminate\Http\Response
     */
    public function edit($client_id, $invoice_id)
    {
        return view('invoices.edit', compact('client_id', 'invoice_id'));
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
