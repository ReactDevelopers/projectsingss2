<?php

namespace App\Http\Controllers\Api\V1\Uploads;

use App\Upload;
use Illuminate\Http\Request;
use App\Jobs\Uploads\UploadsCreate;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Input;
use App\Http\Requests\Uploads\UploadsStoreRequest;

class UploadController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\Races\UploadsStoreRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(UploadsStoreRequest $request)
    {
        $success = dispatch_now(new UploadsCreate($request));

        return response()->json($success ? $success : 422);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Upload  $upload
     * @return \Illuminate\Http\Response
     */
    public function show(Upload $upload)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Upload  $upload
     * @return \Illuminate\Http\Response
     */
    public function edit(Upload $upload)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Upload  $upload
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Upload $upload)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Upload  $upload
     * @param  String  $invoice_type
     * @return \Illuminate\Http\Response
     */
    public function destroy(Upload $upload, $invoice_type)
    {
        $upload->delete();
    }
}
