<?php

namespace App\Http\Controllers\Api\V1\Clients;

use Illuminate\Http\Request;
use App\Jobs\Clients\ClientsCreate;
use App\Jobs\Clients\ClientsUpdate;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Input;
use App\Http\Requests\Clients\ClientsStoreRequest;

class ClientController extends Controller
{
    /**
     * Action to create New Client and its Contacts
     * @param  ClientStoreRequest $request
     * @return Response
     */
    public function store(ClientsStoreRequest $request)
    {
        $success = dispatch_now(new ClientsCreate($request->all()));

        return response()->json($success ? $success : 422);
    }

    /**
     * Action to update Client and its Contacts
     * @param  ClientsStoreRequest $request
     * @return \Illuminate\Http\Response
     */
    public function update(ClientsStoreRequest $request)
    {
        $success = dispatch_now(new ClientsUpdate($request));

        return response()->json($success ? $success : 422);
    }
}
