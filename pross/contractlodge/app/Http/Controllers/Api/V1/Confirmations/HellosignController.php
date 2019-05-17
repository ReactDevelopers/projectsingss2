<?php

namespace App\Http\Controllers\Api\V1\Confirmations;

use App\Confirmation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Input;

class HellosignController extends Controller
{
    /**
     * Callback from Hellosign for events
     * @return Response
     */
    public function callback(Request $request)
    {
        Log::info($request);

        if (! isset($request['json'])) {
            return response()->json(['message' => 'Hello API Event Received']);
        }

        $data = json_decode($request['json']);

        // FIXME: This might need tweaking to handle other events rather than a signature request
        if (! isset($data->signature_request)) {
            return response()->json(['message' => 'Hello API Event Received']);
        }

        $confirmation = Confirmation::where('signature_request_id', '=', $data->signature_request->signature_request_id)->first();

        if ($confirmation &&
            isset($data->event->event_type) &&
            $data->event->event_type == 'signature_request_signed'
        ) {
            $confirmation->signed_on = date('Y-m-d', $data->event->event_time);
            $confirmation->save();
        }

        return response()->json(['message' => 'Hello API Event Received']);
    }
}
