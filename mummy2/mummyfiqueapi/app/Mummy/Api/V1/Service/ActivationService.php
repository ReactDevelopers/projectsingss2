<?php namespace App\Mummy\Api\V1\Service;

use App\Mummy\Api\V1\Repositories\ActivationRepository;
use Illuminate\Mail\Mailer;
use Illuminate\Mail\Message;
use App\Mummy\Api\V1\Entities\Customer;
use App\Mummy\Api\V1\Entities\CustomerActivation;
use Illuminate\Support\Facades\Mail;
use App\Mummy\Api\V1\Mail\ActiveCustomer;

class ActivationService
{

    protected $mailer;

    protected $activationRepo;

    protected $resendAfter = 24;

    public function __construct(Mailer $mailer, ActivationRepository $activationRepo)
    {
        $this->mailer = $mailer;
        $this->activationRepo = $activationRepo;
    }

    public function sendActivationMail($user)
    {
        if ($user->status || !$this->shouldSend($user)) {
            return;
        }
        $token = $this->activationRepo->createActivation($user);

        $link = route('user.activate', $token);
        $url = sprintf('%s', $link, $link);

        // $messageMail = 'We just need to verify your email address before your sign up is complete!';
        // $messageURL = 'Please click here to verify your email address.';
        //  $this->mailer->send('mail.mail', ['url' => $url,'name' => $user->first_name,'messageMail' => $messageMail,'messageURL' => $messageURL], function (Message $m) use ($user) {
        //     $m->to($user->email)->subject("$user->first_name, welcome to Mummyfique!");
        // });

        $subject = $user->first_name . ", welcome to Mummyfique!";
        Mail::to($user->email)->send(new ActiveCustomer($user, $url, $subject));
    }

    public function reSendMail($user,$customerResend)
    {
        $tokenResend = $this->getToken();
        $customerResend->token = $tokenResend;
        $customerResend->save();
        $link = route('user.activate', $customerResend->token);
        $url = sprintf('%s', $link, $link);
        

        // $messageMail = 'We just need to verify your email address before your sign up is complete!';
        // $messageURL = 'Please click here to verify your email address.';
        //  $this->mailer->send('mail.mail', ['url' => $url,'name' => $user->first_name,'messageMail' => $messageMail,'messageURL' => $messageURL], function (Message $m) use ($user) {
        //     $m->to($user->email)->subject('MummyFique Apps: Please verify your email address');
        // });
        
        $subject = "MummyFique Apps: Please verify your email address";
        Mail::to($user->email)->send(new ActiveCustomer($user, $url, $subject));
        
    }
    public function activateUser($token)
    {

        $activation = $this->activationRepo->getActivationByToken($token);
        if ($activation === null) {
            return null;
        }

        $user = Customer::find($activation->customer_id);

        $user->status = 1;

        $user->save();

        $this->activationRepo->deleteActivation($token);

        return $user;

    }

    private function shouldSend($user)
    {
        $activation = $this->activationRepo->getActivation($user);
        return $activation === null || strtotime($activation->created_at) + 60 * 60 * $this->resendAfter < time();
    }
     protected function getToken()
    {
        return hash_hmac('sha256', str_random(40), config('app.key'));
    }

}