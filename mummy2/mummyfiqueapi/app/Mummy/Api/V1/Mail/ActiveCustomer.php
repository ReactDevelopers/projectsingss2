<?php

namespace App\Mummy\Api\V1\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class ActiveCustomer extends Mailable 
{
    use Queueable, SerializesModels;

    public $tries = 1;
    
    public $user;
    public $url;
    public $subject;
    
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($user, $url, $subject)
    {
        $this->user = $user;
        $this->url = $url;
        $this->subject = $subject;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $user = $this->user;
        $url = $this->url;
        $subject = $this->subject;

        $message = 'Dear '. $user->first_name .',<br/>Thank you for signing up with us! Simply click on the link below to activate your account.<br/><br/><a href="' . $url . '"><button>Activate</button></a>';
        return $this->subject($subject)
                    ->markdown('mail.template.template_mail',['content' => $message]);
    }
}
