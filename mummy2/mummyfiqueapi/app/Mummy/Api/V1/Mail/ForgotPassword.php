<?php

namespace App\Mummy\Api\V1\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;
use URL;

class ForgotPassword extends Mailable
{
    use Queueable, SerializesModels;

    public $tries = 1;
    
    protected $customer;
    protected $token;
    
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($customer, $token)
    {
        $this->customer = $customer;
        $this->token = $token;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $customer = $this->customer;
        $token = $this->token;
        $url = URL::to('/').'/reset-password'.'/'.$customer->id.'/'.$token;

        $messageMail = 'You recently requested to reset your password for your MummyFique Account.';
        $messageURL = 'Please click here to reset your password.';
        $message = 'Dear ' . $customer->first_name . ',<br/>Seems like you have forgotten your password. No worries, you can just click on the link below to reset your password.<br/><br/><a href="' . $url . '"><button>Reset Password</button></a>';
        return $this->subject("[Mummyfique] Account Recovery")
                    ->markdown('mail.template.template_mail',['content' => $message]);
    }
}
