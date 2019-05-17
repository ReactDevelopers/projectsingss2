<?php

namespace App\Http\Controllers\Api\V1\RacesHotelsInvoices;

use App\Race;
use App\Hotel;
use App\Country;
use App\Currency;
use App\RaceHotel;
use App\RaceHotelInventory;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Input;
use App\Jobs\RacesHotelsInvoices\RacesHotelsInvoicesCreate;
use App\Jobs\RacesHotelsInvoices\RacesHotelsInvoicesUpdate;
use App\Http\Requests\RacesHotelsInvoices\RacesHotelsInvoicesStoreRequest;
use App\Http\Requests\RacesHotelsInvoices\RacesHotelsInvoicesUpdateRequest;

class RaceHotelInvoiceController extends Controller
{
    /**
     * Action to create New Confirmation (Rooms)
     * @param  RacesHotelsInvoicesStoreRequest $request
     * @return Response
     */
    public function store(RacesHotelsInvoicesStoreRequest $request)
    {
        $uri = $request->path();
        $code = 422;

        if (strpos($uri,'confirmations') !== false) {
            $invoice_type = "confirmations";
        } elseif (strpos($uri,'extras') !== false) {
            $invoice_type = "extras";
        }

        $success = dispatch_now(new RacesHotelsInvoicesCreate($request, $invoice_type));

        if (isset($success->id)) {
            $code = 200;
        }

        return response()->json($success, $code);
    }

    /**
     * Action to update New Confirmation (Rooms)
     * @param  RacesHotelsInvoicesUpdateRequest $request
     * @return \Illuminate\Http\Response
     */
    public function update(RacesHotelsInvoicesUpdateRequest $request)
    {
        $success = dispatch_now(new RacesHotelsInvoicesUpdate($request));

        return response()->json([], $success ? 200 : 422);
    }
}
