<?php

namespace App\Http\Controllers;

use App\Client;
use App\Country;
use App\Confirmation;
use App\CustomInvoice;
use Illuminate\Http\Request;
use App\Jobs\Clients\ClientsCreate;
use App\Jobs\Clients\ClientsUpdate;
use Illuminate\Support\Facades\Input;
use App\Http\Requests\Clients\ClientsStoreRequest;

class ClientController extends Controller
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
        $clients = Client::getAll();

        return view('clients.index', compact('clients'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $countries = Country::all();

        return view('clients/create', compact('countries'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(ClientsStoreRequest $request, Client $client)
    {
        $client = dispatch_now(new ClientsCreate($request->all(), $client));

        flash('The client has been saved.')->success();

        return redirect('clients');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App|Cliient  $client
     * @return \Illuminate\Http\Response
     */
    public function show(Client $client)
    {
        $contact = $client->contacts()->first();

        $confirmations = $client->confirmations()
            ->get()
            ->load('currency', 'client', 'confirmation_items', 'payments', 'race_hotel.hotel', 'race_hotel.race');

        $invoices = $client->invoices()
            ->get()
            ->load('currency', 'client', 'invoice_items', 'payments', 'race_hotel.hotel', 'race_hotel.race');

        $recievables = $confirmations->concat($invoices)->sortBy('created_at');

        return view('clients.show', compact('client', 'contact', 'recievables'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Client  $client
     * @return \Illuminate\Http\Response
     */
    public function edit(Client $client)
    {
        $countries = Country::all();
        $contact = $client->contacts()->first();

        return view('clients.edit', compact('client', 'countries', 'contact'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\Clients\ClientsStoreRequest  $request
     * @param  \App\Client $client
     * @return \Illuminate\Http\Response
     */
    public function update(ClientsStoreRequest $request, Client $client)
    {
        dispatch_now(new ClientsUpdate($request, $client->id));

        flash('The client has been updated.')->success();

        return redirect()->route('clients.show', ['client_id' => $client->id]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Client  $client
     * @return \Illuminate\Http\Response
     */
    public function destroy(Client $client)
    {
        $client->deleted_by = auth()->user()->id;
        // Save the deleted by and then delete
        $client->save();
        $client->delete();

        flash('The client has been archived.')->success();

        return redirect()->route('clients.index');
    }

    /**
     * Display a listing of the archived clients.
     *
     * @return \Illuminate\Http\Response
     */
    public function archived()
    {
        $clients = Client::getArchived();
        $showArchived = true;

        return view('clients.index', compact('clients', 'showArchived'));
    }

    /**
     * Display a listing of the archived clients.
     *
     * @param  int $client_id
     * @return \Illuminate\Http\Response
     */
    public function unarchive($client_id)
    {
        $client = Client::withTrashed()
            ->findOrFail($client_id);

        $client->deleted_by = null;
        $client->deleted_at = null;
        $client->save();

        flash('The client has been unarchived.')->success();

        return redirect()->route('clients.archived');
    }
}
