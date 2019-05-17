<?php

namespace App\Http\Controllers;

use App\Race;
use App\Hotel;
use App\Client;
use App\Payment;
use App\RaceHotel;
use HelloSign\Error;
use App\Confirmation;
use App\CustomInvoice;
use Illuminate\Http\Request;
use App\Events\RoomsConfirmed;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Input;
use HelloSign\ApiApp as HellosignApiApp;
use Industrious\HelloSignLaravel\Classes\SignatureRequest;
use Industrious\HelloSignLaravel\Client as HellosignClient;

class RaceHotelClientInvoiceController extends Controller
{

    public $hellosignClient;
    public $hellosignRequest;
    public $hellosignApiApp;

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
     * @param  \App\Race $race
     * @param  \App\Hotel $hotel
     * @param  \App\Client $client
     * @param  string  $invoice_type     "confirmations" or "extras"
     * @return \Illuminate\Http\Response
     */
    public function create(Race $race, Hotel $hotel, Client $client, $invoice_type = "extras")
    {
        $meta = RaceHotel::where('race_id', $race->id)
            ->where('hotel_id', $hotel->id)
            ->whereNull('races_hotels.deleted_at')
            ->first()
            ->load('currency');

        return view('invoices.create', compact('race', 'hotel', 'client', 'invoice_type', 'meta'));
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
     * Display the Extra Invoices.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Race $race
     * @param  \App\Hotel $hotel
     * @param  \App\Client $client
     * @param  \App\CustomInvoice $custom_invoice
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, Race $race, Hotel $hotel, Client $client, CustomInvoice $custom_invoice)
    {
        $contact_client = $this->getContactClient('extras', $custom_invoice->id, $client);
        $contact_hotel = $hotel->contacts()->first();
        $meta = RaceHotel::where('race_id', $race->id)
            ->where('hotel_id', $hotel->id)
            ->whereNull('races_hotels.deleted_at')
            ->first()
            ->load('currency');

        $all_client_contact = $client->contacts()->get();

        return view('invoices.show', compact('custom_invoice' ,'race', 'hotel' , 'client' , 'contact_client' , 'contact_hotel', 'meta', 'all_client_contact'));
    }

    /**
     * Display the Rooms Confirmation.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Race $race
     * @param  \App\Hotel $hotel
     * @param  \App\Client $client
     * @param  \App\Confirmation $confirmation
     * @return \Illuminate\Http\Response
     */
    public function showConfirmation(Request $request, Race $race, Hotel $hotel, Client $client, Confirmation $confirmation)
    {
        $confirmation->confirmation_items->load('races_hotels_inventory');
        $contact_client = $this->getContactClient('confirmations', $confirmation->id, $client);
        $contact_hotel = $hotel->contacts()->first();
        $meta = RaceHotel::where('race_id', $race->id)
            ->where('hotel_id', $hotel->id)
            ->whereNull('races_hotels.deleted_at')
            ->first()
            ->load('currency');

        $all_client_contact = $client->contacts()->get();



        return view('invoices.show', compact('confirmation' ,'race', 'hotel' , 'client' , 'contact_client' , 'contact_hotel', 'meta', 'all_client_contact'));
    }

    /**
     * Send's PDF to the client
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Race         $race
     * @param  \App\Hotel        $hotel
     * @param  \App\Client       $client
     * @param  \App\Confirmation $confirmation
     * @return \Illuminate\Http\Response
     */
    public function sendConfirmation(Request $request, Race $race, Hotel $hotel, Client $client, Confirmation $confirmation)
    {
        dd('Make this sendConfirmation() method work.');
        $contact_client = $this->getContactClient('confirmations', $confirmation->id, $client);

        flash('This confirmation has been sent to '.$contact_client->name.' ('.$contact_client->email.').')->success();

        return redirect()->route('races.hotels.clients.confirmations.show', [
            'race' => $race->id,
            'hotel' => $hotel->id,
            'client' => $client->id,
            'confirmation' => $confirmation->id,
        ]);
    }

    /**
     * Send's PDF to the client
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Race         $race
     * @param  \App\Hotel        $hotel
     * @param  \App\Client       $client
     * @param  \App\Confirmation $confirmation
     * @return \Illuminate\Http\Response
     */
    public function send(Request $request, Race $race, Hotel $hotel, Client $client, CustomInvoice $custom_invoice)
    {
        dd('Make this send() method work.');
        $contact_client = $this->getContactClient('extras', $custom_invoice->id, $client);

        flash('This confirmation has been sent to '.$contact_client->name.' ('.$contact_client->email.').')->success();

        return redirect()->route('races.hotels.clients.invoices.show', [
            'race' => $race->id,
            'hotel' => $hotel->id,
            'client' => $client->id,
            'custom_invoice' => $custom_invoice->id,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Race $race
     * @param  \App\Hotel $hotel
     * @param  \App\Client $client
     * @param  \App\CustomInvoice $custom_invoice
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request, Race $race, Hotel $hotel, Client $client, CustomInvoice $custom_invoice)
    {
        $contact_client = $this->getContactClient('extras', $custom_invoice->id, $client);
        $contact_hotel = $hotel->contacts()->first();

        $meta = RaceHotel::where('race_id', $race->id)
            ->where('hotel_id', $hotel->id)
            ->whereNull('races_hotels.deleted_at')
            ->first()
            ->load('currency');

        return view('invoices.edit', compact('race', 'hotel', 'client', 'custom_invoice', 'meta', 'contact_client', 'contact_hotel'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Race $race
     * @param  \App\Hotel $hotel
     * @param  \App\Client $client
     * @param  \App\Confirmation $confirmation
     * @return \Illuminate\Http\Response
     */
    public function editConfirmation(Request $request, Race $race, Hotel $hotel, Client $client, Confirmation $confirmation)
    {
        $contact_client = $this->getContactClient('confirmations', $confirmation->id, $client);
        $contact_hotel = $hotel->contacts()->first();

        $meta = RaceHotel::where('race_id', $race->id)
            ->where('hotel_id', $hotel->id)
            ->whereNull('races_hotels.deleted_at')
            ->first()
            ->load('currency');

        return view('invoices.edit', compact('race', 'hotel', 'client', 'confirmation', 'meta', 'contact_client', 'contact_hotel'));
    }

    /**
     * Send's e-signature request to the client for the Rooms Confirmation.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Race         $race
     * @param  \App\Hotel        $hotel
     * @param  \App\Client       $client
     * @param  \App\Confirmation $confirmation
     * @param  SignatureRequest  $hellosignRequest
     * @param  HellosignClient   $hellosignClient
     * @param  HellosignApiApp   $hellosignApiApp
     * @return \Illuminate\Http\Response
     */
    public function requestSignature(
        Request $request,
        Race $race,
        Hotel $hotel,
        Client $client,
        Confirmation $confirmation,
        SignatureRequest $hellosignRequest,
        HellosignClient $hellosignClient,
        HellosignApiApp $hellosignApiApp
    ) {
        $confirmation->confirmation_items->load('races_hotels_inventory');

        $this->hellosignClient = $hellosignClient;
        $this->hellosignRequest = $hellosignRequest;

        // Update white labeling at HelloSign
        $this->hellosignApiApp = $hellosignApiApp->setWhiteLabeling(config('hellosign.whitelabel_options'));
        $this->hellosignClient->updateApiApp(config('hellosign.default_client_id'), $this->hellosignApiApp);

        $contact_client = $this->getContactClient('confirmations', $confirmation->id, $client);
        $contact_hotel = $hotel->contacts()->first();

        $meta = RaceHotel::where('race_id', $race->id)
            ->where('hotel_id', $hotel->id)
            ->whereNull('races_hotels.deleted_at')
            ->first()
            ->load('currency');

        // Create the HTML
        $content = (string) View::make('confirmations.pdf', compact(
            'confirmation',
            'race',
            'hotel',
            'client',
            'contact_client',
            'contact_hotel',
            'meta'
        ));

        if (empty($contact_client->email)) {
            flash('The client you are trying to send to does not have an email specified for their contact person.
                Please <a href="/clients/'.$client->id.'/edit">edit the client record</a> to assign one.')->error();

            return $this->redirectToConfirmationsShow($race, $hotel, $client, $confirmation);
        }

        // FIXME: Maybe only send reminder if nothing has been changed?
        // Or else perhaps create and send an entirely new document?
        if ($confirmation->signature_request_id) {

            $response = $this->hellosignClient->getSignatureRequest(
                $confirmation->signature_request_id
            );

            if ($response->isComplete()) {

                $confirmation->signature_request_id = $response->getId();
                $confirmation->signed_on = date("Y-m-d");

                if (isset($confirmation->prevent_room_list_duplication) && ($confirmation->prevent_room_list_duplication != 1)) {
                    event(new RoomsConfirmed($confirmation));
                }

                if (isset($confirmation->prevent_room_list_duplication) && ($confirmation->prevent_room_list_duplication == 0)) {
                    $confirmation->prevent_room_list_duplication = 1;
                }

                $confirmation->save();
                flash('This confirmation has been signed.')->success();

                return $this->redirectToConfirmationsShow($race, $hotel, $client, $confirmation);
            }

            try {
                $this->hellosignClient->requestEmailReminder(
                    $confirmation->signature_request_id,
                    $contact_client->email
                );
                flash('A reminder has been sent to '.$contact_client->name.' ('.$contact_client->email.') for signature.')
                    ->success();

                return $this->redirectToConfirmationsShow($race, $hotel, $client, $confirmation);

            } catch (Error $e) {

                flash('Oops: '.$e->getMessage())->error();

                return $this->redirectToConfirmationsShow($race, $hotel, $client, $confirmation);
            }
        }

        $filename = 'Confirmation-'.$confirmation->id.'.pdf';
        $pdf = app('pdflayer');
        $pdf->loadHtml($content)->save(storage_path('app/public/confirmations/'.$filename));

        $this->hellosignClient->getAccount();
        $this->hellosignRequest
            ->enableTestMode() // FIXME: Remove this when going live
            ->setTitle('Signature Request for Room Confirmation prior to ' . $confirmation->friendly_expires_on)
            ->setSubject('Signature Request for Room Confirmation prior to ' . $confirmation->friendly_expires_on)
            ->setMessage('Please review the details of this document carefully and sign to confirm your room requests prior to ' . $confirmation->friendly_expires_on)
            ->addSigner($contact_client->email, $contact_client->name)
            ->addFile(storage_path('app/public/confirmations/'.$filename));
        $response = $this->hellosignRequest->send();
        $confirmation->signature_request_id = $response->getId();
        $confirmation->sent_on = date('Y-m-d');
        $confirmation->save();

        flash('This confirmation has been sent to '.$contact_client->name.' ('.$contact_client->email.') for signature.')->success();

        return $this->redirectToConfirmationsShow($race, $hotel, $client, $confirmation);
    }

    /**
     * Simple helper function so I can stop repeating this same return everywhere
     * @param  Race         $race
     * @param  Hotel        $hotel
     * @param  Client       $client
     * @param  Confirmation $confirmation
     * @return [type]
     */
    private function redirectToConfirmationsShow(
        Race $race,
        Hotel $hotel,
        Client $client,
        Confirmation $confirmation
    ) {
        return redirect()->route('races.hotels.clients.confirmations.show', [
            'race' => $race->id,
            'hotel' => $hotel->id,
            'client' => $client->id,
            'confirmation' => $confirmation->id,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Race $race
     * @param  \App\Hotel $hotel
     * @param  \App\Client $client
     * @param  \App\CustomInvoice $custom_invoice
     * @return \Illuminate\Http\Response
     */
    public function pdf(Request $request, Race $race, Hotel $hotel, Client $client, CustomInvoice $custom_invoice)
    {
        $custom_invoice->load('invoice_items');
        $contact_client = $this->getContactClient('extras', $custom_invoice->id, $client);
        $contact_hotel = $hotel->contacts()->first();

        $meta = RaceHotel::where('race_id', $race->id)
            ->where('hotel_id', $hotel->id)
            ->whereNull('races_hotels.deleted_at')
            ->first()
            ->load('currency');

        $content = (string) View::make('invoices.pdf', compact(
            'custom_invoice',
            'race',
            'hotel',
            'client',
            'contact_client',
            'contact_hotel',
            'meta'
        ));

        $filename = 'Invoice-'.$custom_invoice->id.'.pdf';
        $pdf = app('pdflayer');

        return $pdf
            ->loadHtml($content)
            ->save(storage_path('app/public/custom_invoices/'.$filename))
            ->stream($filename);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Race $race
     * @param  \App\Hotel $hotel
     * @param  \App\Client $client
     * @param  \App\Confirmation $confirmation
     * @return \Illuminate\Http\Response
     */
    public function pdfConfirmation(
        Request $request,
        Race $race,
        Hotel $hotel,
        Client $client,
        Confirmation $confirmation
    ) {
        $confirmation->confirmation_items->load('races_hotels_inventory');
        $contact_client = $this->getContactClient('confirmations', $confirmation->id, $client);
        $contact_hotel = $hotel->contacts()->first();

        $meta = RaceHotel::where('race_id', $race->id)
            ->where('hotel_id', $hotel->id)
            ->whereNull('races_hotels.deleted_at')
            ->first()
            ->load('currency');

        $content = (string) View::make('confirmations.pdf', compact(
            'confirmation',
            'race',
            'hotel',
            'client',
            'contact_client',
            'contact_hotel',
            'meta'
        ));

        $pdf = app('pdflayer');

        $filename = 'Confirmation-'.$confirmation->id.'.pdf';

        return $pdf
            ->loadHTML($content)
            ->save(storage_path('confirmations/'.$filename))
            ->stream($filename);
    }

    /**
     * Manually Signed process for the Rooms Confirmation.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Race         $race
     * @param  \App\Hotel        $hotel
     * @param  \App\Client       $client
     * @param  \App\Confirmation $confirmation
     * @return \Illuminate\Http\Response
     */
    public function markAsSigned(
        Request $request,
        Race $race,
        Hotel $hotel,
        Client $client,
        Confirmation $confirmation
    ) {
        $confirmation->signed_on = date("Y-m-d");

        if (isset($confirmation->prevent_room_list_duplication) && ($confirmation->prevent_room_list_duplication != 1)) {
            event(new RoomsConfirmed($confirmation));
        }

        if (isset($confirmation->prevent_room_list_duplication) && ($confirmation->prevent_room_list_duplication == 0)) {
            $confirmation->prevent_room_list_duplication = 1;
        }

        $confirmation->save();
        flash('This confirmation has been signed manually.')->success();

        return $this->redirectToConfirmationsShow($race, $hotel, $client, $confirmation);
    }

    /**
     * Mark as unsigned process for the Rooms Confirmation.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Race         $race
     * @param  \App\Hotel        $hotel
     * @param  \App\Client       $client
     * @param  \App\Confirmation $confirmation
     * @return \Illuminate\Http\Response
     */
    public function markAsUnsigned(
        Request $request,
        Race $race,
        Hotel $hotel,
        Client $client,
        Confirmation $confirmation
    ) {
        $confirmation->signed_on = null;
        $confirmation->save();
        flash('This confirmation has been marked as unsigned.')->success();

        return $this->redirectToConfirmationsShow($race, $hotel, $client, $confirmation);
    }


    /**
     * Simple helper function to get choosen client Contact OR first client contact
     * @param  String $invoice_type
     * @param  Integer $confirmation_id
     * @param  \App\Client $client
     * @return \App\Contact
     */
    private function getContactClient($invoice_type, $confirmation_id, Client $client)
    {
        $pivot_field = ($invoice_type == 'confirmations') ? 'confirmation_contact_id' : 'invoice_contact_id';
        $contact_client = $client->contacts()->wherePivot($pivot_field, $confirmation_id)->first();

        if (empty($contact_client)) {
            $contact_client = $client->contacts()->first();
        }

        return $contact_client;
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
     * and Cancel the request for signature (HelloSign)
     *
     * @param  \App\Race $race
     * @param  \App\Hotel $hotel
     * @param  \App\Client $client
     * @param  HellosignClient $hellosignClient
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(
        Race $race,
        Hotel $hotel,
        Client $client,
        Confirmation $confirmation,
        HellosignClient $hellosignClient
    ) {
        // Call to HelloSign to Cancel the Request for Signature (if it's been sent out)

        if (! empty($confirmation->signature_request_id)) {
            $this->hellosignClient = $hellosignClient;
            $this->hellosignClient->cancelSignatureRequest($confirmation->signature_request_id);
        }

        // Soft Delete Confirmation and it's Confirmation Items

        $confirmation->deleted_by = auth()->user()->id;
        $confirmation->save();
        $confirmation->delete();
        $confirmation->confirmation_items()->delete();

        flash('The confirmation has been disassociated.')->success();

        return redirect()->route('races.hotels.clients.show', [
            'race' => $race->id,
            'hotel' => $hotel->id,
            'client' => $client->id
        ]);
    }
}
