<?php

use App\Bill;
use App\Race;
use App\Hotel;
use App\Client;
use App\Contact;
use App\Currency;
use App\RaceHotel;
use App\Confirmation;
use App\CustomInvoice;
use App\ConfirmationItem;
use App\RaceHotelInventory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register the API routes for your application as
| the routes are automatically authenticated using the API guard and
| loaded automatically by this application's RouteServiceProvider.
|
*/

Route::group([
    'middleware' => 'auth:api'
], function () {

    //----------------------------------------------------------------------------------
    // FIXME: Move anything using a closure into a controller and do this the right way!
    // Then remove this comment, because although it's pretty, it won't be needed.
    //----------------------------------------------------------------------------------

    Route::get('/currencies', 'Api\V1\Currencies\CurrencyController@index');
    Route::get('/currencies/exchange/{from_currency}/{to_currency}', 'Api\V1\Currencies\CurrencyController@exchange');

    Route::get('/hotels/search', function () {
        $query = Input::get('query');
        $hotels = Hotel::where('name', 'like', '%'.$query.'%')->get()->load(['country']);

        return response()->json($hotels);
    });

    Route::get('/clients/search', function () {
        $query = Input::get('query');
        $clients = Client::where('name', 'like', '%'.$query.'%')->get();
        $clients->load('contacts', 'country');
        return response()->json($clients);
    });

    Route::get('/races/search', function () {
        $query = Input::get('query');
        $races = Race::where('name', 'like', '%'.$query.'%')->get();

        return response()->json($races);
    });

    Route::get('/races/{race_id}', function (Request $request, $race_id) {
        return response()->json(Race::find($race_id));
    });

    Route::get('/hotels/{hotel_id}', function (Request $request, $hotel_id) {
        return response()->json(Hotel::find($hotel_id));
    });

    Route::get('/clients/{client_id}', function (Request $request, $client_id) {
        return response()->json(Client::with('contacts','country')->find($client_id));
    });

    Route::get('/currencies/{currency_id}', function (Request $request, $currency_id) {
        return response()->json(Currency::find($currency_id));
    });

    Route::get('/custom_invoices/{custom_invoice_id}', function (Request $request, $custom_invoice_id) {
        return response()->json(CustomInvoice::with('invoice_items', 'payments')->find($custom_invoice_id));
    });

    Route::get('/confirmations/{confirmation_id}', function (Request $request, $confirmation_id) {
        return response()->json(Confirmation::with('confirmation_items.races_hotels_inventory', 'payments')->find($confirmation_id));
    });

    Route::get('/uploads/{invoice_type}/{invoice_id}', function (Request $request, $invoice_type, $invoice_id ) {
        if ($invoice_type == 'confirmations') {
            return response()->json(Confirmation::with('uploads')->find($invoice_id));
        } else {
            return response()->json(CustomInvoice::with('uploads')->find($invoice_id));
        }
    });

    Route::get('/uploads_race_hotel/{race_hotel_id}', function (Request $request, $race_hotel_id ) {
        return response()->json(RaceHotel::with('uploads')->find($race_hotel_id));
    });

    // Get quantity rooms (by room type) already used in (outstanding and signed) confirmations.
    // If confirmation_id param sent in, exclude that confirmation's
    // rooms (by specified room type) from the quantity.
    Route::get('/rooms/{race_hotel_inventory_id}/quantity/{race_hotel_id}/{confirmation_id?}', function (
        Request $request,
        $race_hotel_inventory_id,
        $race_hotel_id,
        $confirmation_id = null
    ) {
        $confirmationIds = Confirmation::where(function ($query) {
                $query->whereDate('expires_on', '>=', date('Y-m-d'))
                    ->orWhereNull('expires_on')
                    ->orWhereNotNull('signed_on');
            })
            ->whereNull('deleted_at')
            ->pluck('id')
            ->toArray();

        $query = ConfirmationItem::with('races_hotels_inventory')
            ->whereIn('confirmation_id', $confirmationIds)
            ->where('races_hotels_inventory_id', '=', $race_hotel_inventory_id);

        if ($confirmation_id) {
            $query = $query->where('confirmation_id', '!=', $confirmation_id);
        }

        $quantity = $query->whereNull('deleted_at')->sum('quantity');
        $quantity = (! empty($quantity)) ? $quantity : 0;

        $race_hotel = RaceHotel::select('inventory_min_check_in', 'inventory_min_check_out')
                        ->where('id', $race_hotel_id)
                        ->whereNull('deleted_at')
                        ->first();
        $min_nt_quantity = $query->whereDate('check_in', '>=', $race_hotel->inventory_min_check_in)
                            ->whereDate('check_out', '<=', $race_hotel->inventory_min_check_out)
                            ->whereNull('deleted_at')
                            ->sum('quantity');
        $min_nt_quantity = (! empty($min_nt_quantity)) ? $min_nt_quantity : 0;

        return response()->json(['min_nt_qty' => $min_nt_quantity, 'pp_nt_qty' => ($quantity - $min_nt_quantity)]);
    });

    Route::get('/rooms/{race_hotel_inventory_id}', function (
        Request $request,
        $race_hotel_inventory_id
    ) {
        return response()->json(RaceHotelInventory::find($race_hotel_inventory_id));
    });

    Route::get('/bills/{bill_id}', function (Request $request, $bill_id) {
        return response()->json(Bill::with('payments')->find($bill_id));
    });

    Route::get('/races/{race_id}/hotels/{hotel_id}', 'Api\V1\RacesHotels\RaceHotelController@show');
    Route::get('/races/{race_id}/hotels/{hotel_id}/rooming-list-data', 'Api\V1\RacesHotels\RaceHotelController@roomingListData');
    Route::put('/races/{race_id}/hotels/{hotel_id}/invoices/confirmations/edit', 'Api\V1\RacesHotelsInvoices\RaceHotelInvoiceController@update');
    Route::put('/clients/edit', 'Api\V1\Clients\ClientController@update');
    Route::put('/races/{race_id}/hotels/{hotel_id}/invoices/extras/edit', 'Api\V1\RacesHotelsInvoices\RaceHotelInvoiceController@update');
    Route::post('/races/{race_id}/hotels/{hotel_id}', 'Api\V1\RacesHotels\RaceHotelController@store');
    Route::post('/races/{race_id}/hotels/{hotel_id}/invoices/confirmations', 'Api\V1\RacesHotelsInvoices\RaceHotelInvoiceController@store');
    Route::post('/races/{race_id}/hotels/{hotel_id}/invoices/extras', 'Api\V1\RacesHotelsInvoices\RaceHotelInvoiceController@store');
    Route::post('/uploads', 'Api\V1\Uploads\UploadController@store');
    Route::post('/races/{race_id}/hotels/{hotel_id}/bills', 'Api\V1\RacesHotelsBills\RaceHotelBillController@store');
    Route::post('/clients/create', 'Api\V1\Clients\ClientController@store');

    Route::post('/client/{client_id}/contact/{contact_id}/invoices/{invoice_type}/{confirmation_id}', function (
        Request $request,
        $client_id,
        $contact_id,
        $invoice_type,
        $confirmation_id
    ) {
        $pivot_field = ($invoice_type == 'confirmations') ? 'confirmation_contact_id' : 'invoice_contact_id';
        $client = Client::find($client_id);
        $contact_client = $client->contacts()->get();
        $client->contacts()->updateExistingPivot($contact_client, [$pivot_field => null]);
        $contact_client = $client->contacts()->where('contacts.id', $contact_id)->first();
        $client->contacts()->updateExistingPivot($contact_client, [$pivot_field => $confirmation_id]);

        return response()->json($contact_client);
    });

    Route::delete('/delete_uploads/{upload}/{invoice_type}', 'Api\V1\Uploads\UploadController@destroy');

    Route::post('/callback', 'Api\V1\Confirmations\HellosignController@callback');
});

Route::get('/cache-urls-list', 'Api\V1\OfflineModeController@getPageUrlList');
