<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;

class RaceClientInvoiceController extends Controller
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
    // public function index()
    // {
    //     return view('invoices.index');
    // }

    /**
     * Create a resource.
     *
     * @param  int     $race_id
     * @param  int     $client_id
     * @param  string  $invoice_type     "confirmations" or "extras"
     * @return \Illuminate\Http\Response
     */
    public function create($race_id, $client_id, $invoice_type = "extras")
    {
        return view('invoices.create', compact('race_id', 'client_id', 'invoice_type'));
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
     * @param  int  $client_id
     * @param  int  $invoice_id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, $race_id, $client_id, $invoice_id)
    {
        // FIXME: This is temporary to allow for different views based on URL.
        // This is not necessary for real functionality when we get beyond HTML prototypes.
        $invoice_type = $request->input('invoice_type', 'extras');

        return view('invoices.show', compact('race_id', 'client_id', 'invoice_id', 'invoice_type'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $race_id
     * @param  int  $race_id
     * @param  int  $invoice_id
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request, $race_id, $client_id, $invoice_id)
    {
        // FIXME: This is temporary to allow for different views based on URL.
        // This is not necessary for real functionality when we get beyond HTML prototypes.
        $invoice_type = $request->input('invoice_type', 'extras');

        return view('invoices.edit', compact('race_id', 'invoice_id', 'client_id', 'invoice_type'));
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
