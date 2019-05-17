<?php

namespace App\Mummy\Api\V1\Listeners;

use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Mummy\Api\V1\Events\SendReviewVendor;
use App\Mummy\Api\V1\Mail\SendReviewToVendor;
use Illuminate\Support\Facades\Mail;

class SendReviewEmailToVendor
{
    public $tries = 1;
    
    /**
     * Create the event listener.
     *
     * @return void
     */
    protected $vendor;

    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  SendMessageVendor  $event
     * @return void
     */
    public function handle(SendReviewVendor $event)
    {
        // $when = \Carbon\Carbon::now()->addSeconds(30);
        // return $event->vendor->notify( (new SendMessageToVendor($event->vendor))->delay($when) );
        $vendor = $event->vendor;
        $emails = [];
        $emails[] = $vendor->email;

        // get addition and send
        $vendorSetting = $vendor->vendorSetting;
        $vendorAdditionEmails = $vendorSetting->addition_emails;
        if(!empty($vendorAdditionEmails)){
            $arr = explode(',', $vendorAdditionEmails);
            if(!empty($arr)){
                foreach ($arr as $key => $item) {
                    if(!in_array(trim($item), $emails)){
                        $emails[] = trim($item);
                    }
                }
            }
        }
        if(sizeof($emails)){
           foreach ($emails as $key => $item) {
                Mail::to($item)->queue(new SendReviewToVendor($vendor));
            } 
        }
    }
}
