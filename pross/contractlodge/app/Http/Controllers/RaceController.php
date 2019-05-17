<?php

namespace App\Http\Controllers;

use App\Race;
use App\Currency;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Jobs\Races\RacesCreate;
use App\Jobs\Races\RacesUpdate;
use App\Http\Requests\Races\RacesStoreRequest;

class RaceController extends Controller
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
        $races = Race::getAll();

        return view('races.index', compact('races'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $currencies = Currency::orderBy('name', 'ASC')->get();
        return view('races.create', compact('currencies'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\Races\RacesStoreRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(RacesStoreRequest $request)
    {
        $race = dispatch_now(new RacesCreate($request));

        flash('The race has been saved.')->success();

        return redirect()->route('races.show', ['race_id' => $race->id]);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Race $race
     * @return \Illuminate\Http\Response
     */
    public function show(Race $race)
    {
        // Call to the function to get total inventory stats
        $inventory_stats = $race->get_total_inventory_stats($race);

        return view('races.show', compact('race', 'inventory_stats'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Race $race
     * @return \Illuminate\Http\Response
     */
    public function edit(Race $race)
    {
        $currencies = Currency::orderBy('name', 'ASC')->get();
        return view('races.edit', compact('race', 'currencies'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests|Races|RacesStoreRequest  $request
     * @param  \App\Race $race
     * @return \Illuminate\Http\Response
     */
    public function update(RacesStoreRequest $request, Race $race)
    {
        dispatch_now(new RacesUpdate($request, $race->id));

        flash('The race has been updated.')->success();

        return redirect()->route('races.show', ['race_id' => $race->id]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Race  $race
     * @return \Illuminate\Http\Response
     */
    public function destroy(Race $race)
    {
        $race->deleted_by = auth()->user()->id;
        // Save the deleted by and then delete
        $race->save();
        $race->delete();

        flash('The race has been archived.')->success();
        return redirect()->route('races.index');
    }

    /**
     * Display a listing of the archived races.
     *
     * @return \Illuminate\Http\Response
     */
    public function archived()
    {
        $races = Race::getArchived();
        $showArchived = true;

        return view('races.index', compact('races', 'showArchived'));
    }

    /**
     * Display a listing of the archived races.
     *
     * @param  int $race_id
     * @return \Illuminate\Http\Response
     */
    public function unarchive($race_id)
    {
        $race = Race::withTrashed()
            ->findOrFail($race_id);

        $race->deleted_by = null;
        $race->deleted_at = null;
        $race->save();

        flash('The race has been unarchived.')->success();

        return redirect()->route('races.archived');
    }
}
