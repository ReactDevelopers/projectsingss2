<?php

namespace App\Jobs\Uploads;

use File;
use Auth;
use Storage;
use App\Upload;
use App\RaceHotel;
use App\Confirmation;
use App\CustomInvoice;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\Http\Requests\Uploads\UploadsStoreRequest;

class UploadsCreate implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * RacesStoreRequest
     *
     * @var Request
     */
    private $request;

    /**
     * Create a new job instance.
     * @param  \App\Http\Requests\Races\UploadsStoreRequest  $request
     * @return void
     */
    public function __construct(UploadsStoreRequest $request)
    {
        $this->request = $request;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {

        if ((isset($this->request->race_hotel_id)) && (! isset($this->request->invoice_type))) {

            $storage_upload_folder_path = config('paths.race_hotel_storage_upload_path');

        } elseif ($this->request->invoice_type == 'confirmations') {

            $storage_upload_folder_path = config('paths.invoice_type_confirmation_storage_upload_path');

        } else {

            $storage_upload_folder_path = config('paths.invoice_type_extra_storage_upload_path');

        }

        $file_storage_path = Storage::disk('local')->put(
            $storage_upload_folder_path, // $path
            $this->request->file('upload_file'), // $fileContent
            ['visibility' => 'public'] // $visibility
        );

        $upload = new Upload;
        $upload->orig_filename = $this->request->upload_file->getClientOriginalName();
        $upload->filepath = $storage_upload_folder_path . basename($file_storage_path);
        $upload->mime_type = $this->request->upload_file->getClientmimeType();

        $upload->save();

        if ((isset($this->request->race_hotel_id)) && (! isset($this->request->invoice_type))) {

            $race_hotel = RaceHotel::find($this->request->race_hotel_id);
            $race_hotel->uploads()->syncWithoutDetaching([$upload->id]);

        } elseif ($this->request->invoice_type == 'confirmations') {

            $confirmation = Confirmation::find($this->request->common_invoice_id);
            $confirmation->uploads()->syncWithoutDetaching([$upload->id]);

        } else {

            $custom_invoice = CustomInvoice::find($this->request->common_invoice_id);
            $custom_invoice->uploads()->syncWithoutDetaching([$upload->id]);

        }

        return $upload;
    }
}
