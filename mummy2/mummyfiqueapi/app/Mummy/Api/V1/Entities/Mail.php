<?php namespace App\Mummy\Api\V1\Entities;

use Illuminate\Mail\Mailer;
use Illuminate\Mail\Message;
use URL;

class Mail
{
    protected $mailer;
    public function __construct(Mailer $mailer)
    {
        $this->mailer = $mailer;
    }
    public function sendMailForgot($customer,$token)
    {
        $url = URL::to('/').'/reset-password'.'/'.$customer->id.'/'.$token;

        $messageMail = 'You recently requested to reset your password for your MummyFique Account.';
        $messageURL = 'Please click here to reset your password.';
         $this->mailer->send('mail.forgot_password', ['url' => $url,'name' => $customer->first_name,'messageMail' => $messageMail,'messageURL' => $messageURL], function (Message $m) use ($customer) {
            $m->to($customer->email)->subject('[Mummyfique] Account Recovery');
        });

    }
}