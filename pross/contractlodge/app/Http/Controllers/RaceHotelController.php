<?php

namespace App\Http\Controllers;

use File;
use Fixerio;
use App\Bill;
use App\Race;
use App\Hotel;
use App\Client;
use App\Country;
use App\Currency;
use App\RaceHotel;
use Carbon\Carbon;
use App\RoomingList;
use App\Confirmation;
use App\RoomingListGuest;
use App\RaceHotelInventory;
use Illuminate\Http\Request;
use App\Jobs\Hotels\HotelsCreate;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Input;
use App\Exports\RoomingListGuestsExport;
use App\Imports\RoomingListGuestsImport;
use App\Http\Requests\RacesHotels\RacesHotelsStoreRequest;

class RaceHotelController extends Controller
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
        return view('hotels.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @param  int  $race_id
     * @return \Illuminate\Http\Response
     */
    public function create($race_id)
    {
        $race = null;

        if ($race_id) {
            $race = Race::find($race_id);
        }

        $countries = Country::orderBy('name', 'ASC')->get();

        return view('hotels.create', compact('race_id', 'race', 'countries'));
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Race $race
     * @param  \App\Hotel $hotel
     * @return \Illuminate\Http\Response
     */
    public function show(Race $race, Hotel $hotel)
    {
        $contact = $hotel->contacts()->first();

        $meta = RaceHotel::where('race_id', $race->id)
            ->where('hotel_id', $hotel->id)
            ->whereNull('races_hotels.deleted_at')
            ->first()->load([
                'currency',
                'room_type_inventories.confirmation_items',
                'room_type_inventories.confirmation_items.confirmation',
                'room_type_inventories.race_hotel.on_offer_confirmations',
                'room_type_inventories.race_hotel.on_offer_confirmations.confirmation_items',
                'room_type_inventories.race_hotel.signed_confirmations',
                'room_type_inventories.race_hotel.signed_confirmations.confirmation_items',
            ]);

        $inventories = $meta->room_type_inventories;

        $totals = $this->get_inventory_totals($inventories, $meta->num_min_nights);
        set_inventory_stats($inventories);

        // Find all clients for the given race hotel having confirmations
        $clients = Client::with([
                'confirmations' => function ($query) use ($meta) {
                    $query->where('race_hotel_id', $meta->id);
                },
                'on_offer_confirmations' => function ($query) use ($meta) {
                    $query->where('race_hotel_id', $meta->id);
                },
                'signed_confirmations' => function ($query) use ($meta) {
                    $query->where('race_hotel_id', $meta->id);
                }
            ])
            ->whereHas('confirmations', function ($query) use ($meta) {
                $query->where('race_hotel_id', $meta->id);
            })
            ->get();

        if (! empty($clients)) {
            foreach ($clients as $client) {
                $client->min_stays_sold = $this->get_stays($client->signed_confirmations);
                $client->min_stays_on_offer = $this->get_stays($client->on_offer_confirmations);
                $client->pre_post_nights_on_offer = $this->get_room_nights($client->on_offer_confirmations, 'pre_post');
                $this->set_client_room_types($client);
            }
        }

        $bill = Bill::where('race_hotel_id', $meta->id)
            ->with([
                'payments',
                'currency',
                'currency_exchange'
            ])->first();

        $rooming_list_guests = RoomingListGuest::getRoomingList($meta->id);

        $room_type_breakdown = RoomingListGuest::getRoomtTypeBreakdown($meta->id);

        return view('hotels.show', compact([
            'hotel',
            'race',
            'contact',
            'meta',
            'inventories',
            'totals',
            'clients',
            'bill',
            'rooming_list_guests',
            'room_type_breakdown'
        ]));
    }

    /**
     * Method to find room types for all the confirmations
     * and group them on per client basis
     *
     * @param Client $client
     * @return void
     */
    private function set_client_room_types(Client $client)
    {
        // For each client, include the room type sum values
        $room_types = collect([]);
        $client->room_types = collect([]);

        foreach($client->signed_confirmations as $contract) {
            $room_types = $room_types->merge($contract->room_types());
        }

        $room_types->groupBy('id')
            ->each(function ($room_type) use ($client) {
                $client->room_types->push(collect([
                    'id' => $room_type->first()['id'],
                    'name' => $room_type->first()['name'],
                    'amount' => $room_type->sum('amount'),
                ]));
            });
    }

    /**
     * Method to get the totals for all the inventories.
     * The data is used to show the Totals row in the table
     *
     * @param \Illuminate\Database\Eloquent\Collection $inventories
     * @param integer                                  $num_min_nights
     * @return array
     */
    private function get_inventory_totals(Collection $inventories, $num_min_nights = 0)
    {
        $totals = [
            'min_stays_contracted' => $inventories->sum('min_stays_contracted'),
            'pre_post_nights_contracted' => $inventories->sum('pre_post_nights_contracted'),
            'min_night_hotel_amount' => 0,
            'min_night_client_amount' => 0,
            'pre_post_night_hotel_amount' => 0,
            'pre_post_night_client_amount' => 0,
            'min_stays_sold' => 0,
            'min_stays_on_offer' => 0,
            'pre_post_nights_sold' => 0,
            'pre_post_nights_on_offer' => 0,
        ];
        foreach ($inventories as $room_type) {
            $totals['min_night_hotel_amount'] += $room_type->min_night_hotel_rate * $room_type->min_stays_contracted * $num_min_nights;
            $totals['min_night_client_amount'] += $room_type->min_night_client_rate * $room_type->min_stays_contracted * $num_min_nights;
            $totals['pre_post_night_hotel_amount'] += $room_type->pre_post_night_hotel_rate * $room_type->pre_post_nights_contracted;
            $totals['pre_post_night_client_amount'] += $room_type->pre_post_night_client_rate * $room_type->pre_post_nights_contracted;
            if ($room_type->race_hotel && count($room_type->confirmation_items) > 0) {
                $totals['min_stays_sold'] += $this->get_stays($room_type->race_hotel->signed_confirmations, $room_type->id);
                $totals['min_stays_on_offer'] += $this->get_stays($room_type->race_hotel->on_offer_confirmations, $room_type->id);
                $totals['pre_post_nights_sold'] += $this->get_room_nights($room_type->race_hotel->signed_confirmations, 'pre_post', $room_type->id);
                $totals['pre_post_nights_on_offer'] += $this->get_room_nights($room_type->race_hotel->on_offer_confirmations, 'pre_post', $room_type->id);
            }
        }
        return $totals;
    }

    /**
     * Returns the number of "room nights" (quantity x num nights).
     *
     * @param  Collection $confirmations
     * @param  string     $night_type    "min", "pre_post", or null
     * @param  integer    $room_type_id  or null
     * @return integer
     */
    private function get_room_nights(Collection $confirmations, $night_type = null, $room_type_id = null)
    {
        return $this->get_total($confirmations, false, $night_type, $room_type_id);
    }

    /**
     * Returns the number of "stays" (number of rooms, irrespective of the date range).
     * This only applies to min_nights, not pre_post_nights
     *
     * @param  Collection $confirmations
     * @param  integer    $room_type_id  or null
     * @return integer
     */
    private function get_stays(Collection $confirmations, $room_type_id = null)
    {
        return $this->get_total($confirmations, true, 'min', $room_type_id);
    }

    /**
     * Gets the number of room nights (or stays) from a collection of confirmations. i.e.,
     * you can send in a collection of signed, unsigned, or on offer confirmations
     * and the response will be the product of # rooms x # nights in stay.
     *
     * @param  Collection $confirmations
     * @param  boolean    $count_as_stay If true, returns quantity rooms as a "stay". If false, returns total room nights.
     * @param  string     $night_type    "min", "pre_post", or null
     * @return integer
     */
    private function get_total(Collection $confirmations, $count_as_stay = false, $night_type = null, $room_type_id = null)
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

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Race $race
     * @param  \App\Hotel $hotel
     * @return \Illuminate\Http\Response
     */
    public function edit(Race $race, Hotel $hotel)
    {
        $countries = Country::orderBy('name', 'ASC')->get();
        $currencies = Currency::orderBy('name', 'ASC')->get();
        $contact = $hotel->contacts()->first();
        $meta = RaceHotel::where('race_id', $race->id)
            ->where('hotel_id', $hotel->id)
            ->whereNull('races_hotels.deleted_at')
            ->first();
        $inventories = $meta->load('room_type_inventories')->room_type_inventories;

        return view('hotels.edit', compact('hotel', 'race', 'countries', 'contact', 'currencies', 'meta', 'inventories'));
    }

    /**
     * Reconcile the race/hotel combo
     * @param  Race   $race
     * @param  Hotel  $hotel
     * @return Response
     */
    public function reconcile(Race $race, Hotel $hotel)
    {
        $meta = RaceHotel::where('race_id', $race->id)
            ->where('hotel_id', $hotel->id)
            ->whereNull('races_hotels.deleted_at')
            ->first();

        return view('hotels.reconcile', compact('race', 'hotel', 'meta'));
    }

    /**
     * Show the form for searching for a hotel
     * @param  Race   $race
     * @return \Illuminate\Http\Response
     */
    public function search(Race $race)
    {
        return view('hotels.search', compact('race'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(RacesHotelsStoreRequest $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Race  $race
     * @param  \App\Hotel  $hotel
     * @return \Illuminate\Http\Response
     */
    public function destroy(Race $race , Hotel $hotel)
    {
        $race_hotel = RaceHotel::where('race_id', $race->id)
            ->where('hotel_id', $hotel->id)
            ->whereNull('races_hotels.deleted_at')
            ->first();
        $race_hotel->deleted_by = auth()->user()->id;
        // Save the deleted by and then delete
        $race_hotel->save();
        $race_hotel->delete();
        $race_hotel->room_type_inventories()->delete();
        $race_hotel->confirmations()->delete();
        $race_hotel->custom_invoices()->delete();

        flash('The hotel has been disassociated from the race.')->success();

        return redirect()->route('races.show' , $race_hotel->race_id);
    }

    /**
     * Download the rooming list excelsheet.
     *
     * @param  \App\Race $race
     * @param  \App\Hotel $hotel
     * @return \Illuminate\Http\Response
     */
    public function export(Race $race, Hotel $hotel)
    {
        $race_code_stub = str_replace(' ', '-', strtoupper($race->race_code));
        $hotel_name_stub = str_replace(' ', '-', strtoupper($hotel->name));
        $file_name = 'Rooming_List_'.$race_code_stub.'_'.$hotel_name_stub.'.xlsx';

        return Excel::download(new RoomingListGuestsExport($race->id, $hotel->id), $file_name);
    }

    /**
     * Import/upload the rooming list excelsheet.
     *
     * @param  \App\Race $race
     * @param  \App\Hotel $hotel
     * @param  request $request
     * @return \Illuminate\Http\Response
     */
    public function import(Race $race, Hotel $hotel, Request $request)
    {
        if (!$request->hasFile('upload_file')) {
            return;
        }

        $extension = File::extension($request->upload_file->getClientOriginalName());

        if ($extension == "xlsx" || $extension == "xls") {
            Excel::import(new RoomingListGuestsImport($race->id, $hotel->id), request()->file('upload_file'));
            flash("Import has completed successfully.")->success();
        } else {
            flash("Please upload an .xlsx or .xls file")->error();
        }

        return redirect()->route('races.hotels.reservations', ['race_id' => $race->id ,'hotel_id' => $hotel->id]);
    }
}
