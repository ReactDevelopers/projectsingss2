<?php

namespace App\Http\Controllers\Api\V1\RacesHotelsBills;

use Fixerio;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Input;
use App\Jobs\RacesHotelsBills\RacesHotelsBillsUpdate;
use App\Http\Requests\RacesHotelsBills\RacesHotelsBillsStoreRequest;

class RaceHotelBillController extends Controller
{
    /**
     * Action to create new bill and bill payments.
     * @param  RacesHotelsBillsStoreRequest $request
     * @return Response
     */
    public function store(RacesHotelsBillsStoreRequest $request)
    {
        $success = dispatch_now(new RacesHotelsBillsUpdate($request));
        return response()->json([], $success ? 200 : 422);
    }
}
