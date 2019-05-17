<?php namespace Modules\Vendor\Events\Handlers;

use Illuminate\Mail\Message;
use Illuminate\Support\Facades\Mail;
use Modules\Core\Contracts\Authentication;
use Modules\Vendor\Events\VendorWasCreated;

class SendNotificationEmail
{

    public function handle(VendorWasCreated $event)
    {
        $vendor = $event->vendor;
        $users = $event->users;

        $singaporeTime = $vendor->created_at->timezone('Asia/Singapore');
        $date = $singaporeTime->format('Y-m-d');
        $time = $singaporeTime->format('H:i:s');

        $data = [
        	'name' => $vendor->first_name . ' ' .$vendor->last_name,
        	'date' => $date,
        	'time' => $time,
        	'email' => $vendor->email,
        	'mobile' => $vendor->vendorProfile->business_phone,
        	'id' => $vendor->id
        ];

        Mail::queue('vendor::emails.notification', conpact('data'), function (Message $m) use ($users) {
            $m->to($users)->subject(trans('vendor::vendors.email.new vendor'));
        });
    }
}
