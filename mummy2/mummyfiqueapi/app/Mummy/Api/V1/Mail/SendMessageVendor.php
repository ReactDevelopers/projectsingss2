<?php

namespace App\Mummy\Api\V1\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class SendMessageVendor extends Mailable // implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $tries = 1;
    
    protected $vendor;
    
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($vendor)
    {
        $this->vendor = $vendor;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $vendor = $this->vendor;
        $vendor_name = $vendor->profile ? $vendor->profile->business_name : "Business name";
        $url = env('APP_URL_WEBSITE') . "/vendor/messages";
        $message = 'Dear '.$vendor_name.',<br/>You have new message(s) from potential customer(s). Kindly login to <a href="'. $url .'">Mummyfique for Business</a> site to view the details.';
        return $this->subject("[Mummyfique] You have a new message!")
                    ->markdown('mail.template.template_business_mail',['content' => $message]);
    }
}
