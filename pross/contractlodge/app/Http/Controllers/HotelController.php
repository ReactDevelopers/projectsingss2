<?php

namespace App\Http\Controllers;

use App\Race;
use App\Hotel;
use App\Country;
use Illuminate\Http\Request;
use App\Jobs\Hotels\HotelsCreate;
use App\Jobs\Hotels\HotelsUpdate;
use App\Http\Requests\Hotels\HotelsStoreRequest;

class HotelController extends Controller
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
        $hotels = Hotel::getAll();
        return view('hotels.index', compact('hotels'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $countries = Country::all();
        return view('hotels.create', compact('countries'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\Hotels\HotelsStoreRequest  $request
     * @param  \App\Race  $race
     * @return \Illuminate\Http\Response
     */
    public function store(HotelsStoreRequest $request, Race $race)
    {
        $hotel = dispatch_now(new HotelsCreate($request->all(), $race));
        flash('The hotel has been saved.')->success();
        $url  = null;
        if(! empty($race->id) && ! empty($hotel->id) ) {
            $url =  url('races/' . $race->id . '/hotels/' . $hotel->id);

        }elseif (! empty($hotel->id)) {
            $url = url('hotels/' . $hotel->id);
        }
        return response()->json(['url' => $url ]);

    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Hotel  $hotel
     * @return \Illuminate\Http\Response
     */
    public function show(Hotel $hotel)
    {
        $contact = $hotel->contacts()->first();
        return view('hotels.show', compact('hotel', 'contact'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Hotel $hotel
     * @return \Illuminate\Http\Response
     */
    public function edit(Hotel $hotel)
    {
        $countries = Country::all();
        $contacts = $hotel->contacts()->get();
        return view('hotels.edit', compact('hotel', 'countries', 'contacts'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\Hotels\HotelsStoreRequest  $request
     * @param  \App\Hotel $hotel
     * @param  \App\Race $race
     * @return \Illuminate\Http\Response
     */
    public function update(HotelsStoreRequest $request, Hotel $hotel, Race $race)
    {
        dispatch_now(new HotelsUpdate($request, $hotel->id, $race));
        flash('The hotel has been updated.')->success();
        return response()->json(['url' => route('hotels.show', ['hotel_id' => $hotel->id] )]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Hotel  $hotel
     * @return \Illuminate\Http\Response
     */
    public function destroy(Hotel $hotel)
    {
        $hotel->deleted_by = auth()->user()->id;
        // Save the deleted by and then delete
        $hotel->save();
        $hotel->delete();
        flash('The hotel has been archived.')->success();
        return redirect()->route('hotels.index');
    }

    /**
     * Display a listing of the archived hotels.
     *
     * @return \Illuminate\Http\Response
     */
    public function archived()
    {
        $hotels = Hotel::getArchived();
        $showArchived = true;
        return view('hotels.index', compact('hotels', 'showArchived'));
    }

    /**
     * Display a listing of the archived hotels.
     *
     * @param  int $hotel_id
     * @return \Illuminate\Http\Response
     */
    public function unarchive($hotel_id)
    {
        $hotel = Hotel::withTrashed()
            ->findOrFail($hotel_id);
        $hotel->deleted_by = null;
        $hotel->deleted_at = null;
        $hotel->save();
        flash('The hotel has been unarchived.')->success();
        return redirect()->route('hotels.archived');
    }

    /**
     * Attaching hotel model to particular race model
     *
     * @param  \App\Race  $race
     * @param  \App\Hotel  $hotel
     * @return \Illuminate\Http\Response
     */
    public function attach(Race $race,Hotel $hotel)
    {
        $hotelExists = $race->hotels()
                            ->where('hotels.id', $hotel->id)
                            ->exists();

        if ($hotelExists) {
            flash("{$hotel->name} had already been added to {$race->name} race.")->info();
        } else {
            $race->hotels()->save(
                $hotel,
                [
                    'inventory_currency_id' => $race->currency_id
                ]
            );
            flash("{$hotel->name} has been added to {$race->name} race.")->success();
        }
        return redirect('races/' . $race->id . '/hotels/' . $hotel->id);
    }
}
