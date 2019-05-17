<?php

namespace App\Mummy\Api\V1\Controllers;

use Illuminate\Http\Request;

use Prettus\Validator\Contracts\ValidatorInterface;
use Prettus\Validator\Exceptions\ValidatorException;
use App\Mummy\Api\V1\Requests\CustomerCreateRequest;
use App\Mummy\Api\V1\Requests\CustomerUpdateRequest;
use App\Mummy\Api\V1\Requests\ResetCompleteRequest;
use App\Mummy\Api\V1\Repositories\CustomerRepository;
use App\Mummy\Api\V1\Validators\CustomerValidator;
use Carbon\Carbon;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;
use Illuminate\Foundation\Auth\ResetsPasswords;
use Illuminate\Auth\Passwords\PasswordBroker;
use App\Mummy\Api\V1\Service\ActivationService;
use App\Mummy\Api\V1\Entities\Customer;
use App\Mummy\Api\V1\Entities\Mail;
use App\Mummy\Api\V1\Entities\ResetPasswordCustomer;
use App\Mummy\Api\V1\Entities\CustomerActivation;
use App\Mummy\Api\V1\Entities\PageTranslations;
use App\Mummy\Api\V1\Entities\UserRole;
use App\Mummy\Api\V1\Entities\UserDeviceToken;
use App\Mummy\Api\V1\Entities\CustomerSetting;
use App\Mummy\Api\V1\Requests\Authenticate\LoginRequest;
use App\Mummy\Api\V1\Requests\Authenticate\RegisterRequest;
use App\Mummy\Api\V1\Requests\Authenticate\ForgotPasswordRequest;
use App\Mummy\Api\V1\Requests\Authenticate\SocialLoginRequest;
use Illuminate\Support\Facades\Mail as FacadesMail;
use App\Mummy\Api\V1\Mail\ForgotPassword;
use Helper;


class CustomersController extends ApiController
{

    protected $passwords;
    private $customer;
    protected $activationService;
    /**
     * @var Entities Mail
     */
    protected $mailer;



    public function __construct(PasswordBroker $passwords,CustomerRepository $customer,ActivationService $activationService,Mail $mailer)
    {
        $this->passwords = $passwords;
        $this->customer = $customer;
        $this->activationService = $activationService;
        $this->mailer = $mailer;
        //$this->middleware('auth');
    }

    /**
     * @SWG\Get(
     *   path="/v1/customers/getLogout",
     *   description="",
     *   summary="",
     *   operationId="api.v1.customers.getLogout",
     *   produces={"application/json"},
     *   tags={"CUSTOMERS"},
     *   @SWG\Response(response=401, description="unauthorized"),
     *   @SWG\Response(response=200, description="Success"),
     *   security={
     *       {"api_key": {}}
     *   }
     * )
     */
    public function getLogout(Request $request)
    {
        $request->user()->token()->revoke();
        return response([
                    'data' => [
                    'message'   =>  'Logout Success'
                    ],
                ],Response::HTTP_OK);
    }

    /**
     * @SWG\Post(
     *   path="/v1/customers/postLogin",
     *   description="<ul>
     *     <li>email : string (required)</li>
     *     <li>password : string (required)</li></ul>",
     *   summary="View",
     *   operationId="api.v1.customers.postLogin",
     *   produces={"application/json"},
     *   tags={"CUSTOMERS"},
     *   @SWG\Parameter(
     *     name="body",
     *     in="body",
     *     description="Target customer.",
     *     required=true,
     *    @SWG\Schema(ref="#/definitions/Customer")
     *   ),
     *   @SWG\Response(response=101, description="Wrong email or password"),
     *   @SWG\Response(response=102, description="You need to confirm your account"),
     *   @SWG\Response(response=500, description="internal server error")
     * )
     */
    public function postLogin(LoginRequest $request)
    {
         $customer = Customer::where('email',$request->email)->first();
         $deviceToken = !empty($request->device_token) ? $request->device_token : false;
         $deviceType = !empty($request->device_type) ? $request->device_type : false;
         $first_app_login = !empty($request->first_app_login) ? $request->first_app_login : false;
         $check_first_login = false;

        if(isset($customer) && !empty($customer))
        {
            if (Hash::check($request->password, $customer->password))
            {
                if($customer->status == 1)
                {
                    $token = $customer->createToken('Login Token')->accessToken;
                    $item = $customer->withAccessToken($token);
                    if($deviceToken)
                    {
                        //Helper::checkCustomerSubcriber($customer,$deviceToken,$deviceType);
                        Helper::checkDevicetoken($customer,$deviceToken,$deviceType);
                    }
                    if($first_app_login)
                    {
                        if(!$item->first_app_login)
                        {
                            // $item->first_app_login =  date('Y-m-d', strtotime($first_app_login));
                            $item->first_app_login =  Carbon::now();//->subDay()->format('Y-m-d');
                            $item->save();
                            $check_first_login = true;
                        }
                    }
                    
                    // Update last login
                    $item->last_login = Carbon::now();//->subDay()->format('Y-m-d H:i:s');
                    $item->save();

                    // Rate us pop up not to be shown on first login
                    // $isCheck = Helper::promptReviewMessage($item);
                    $isCheck = !$check_first_login ? Helper::promptReviewMessage($item) : false;
                    //$request->user()->token()->revoke();

                    return response([
                        'data' => [
                        'message'   =>  'Login Success',
                        'isCheck'   =>  $isCheck,
                        'customer'   =>  Helper::formatCustomer($item,$token)
                        ],
                    ],Response::HTTP_OK);
                }
                else
                {
                    return response([
                        'error' => [
                        'code' => config('constant.status_code.REQUIRED_ACTIVE_ACCOUNT'),
                        'message'   =>  'You need to confirm your account']
                    ],Response::HTTP_OK);
                }

            }
                
        }
       return response([
                        'error' => [
                        'code' => config('constant.status_code.EMAIL_PASSWORD_INVALID'),
                        'message'   =>  'Wrong email or password'],
                    ],Response::HTTP_OK);

    }
    /**
     * @SWG\Post(
     *   path="/v1/customers/postRegister",
     *   description="<ul>
     *     <li>email : string (required)</li>
     *     <li>password : string (required)</li>
     *     <li>name : string (required)</li></ul>",
     *   summary="View",
     *   operationId="api.v1.customers.postRegister",
     *   produces={"application/json"},
     *   tags={"CUSTOMERS"},
     *   @SWG\Parameter(
     *     name="body",
     *     in="body",
     *     description="Target customer.",
     *     required=true,
     *    @SWG\Schema(ref="#/definitions/CustomerRegister")
     *   ),
     *   @SWG\Response(response=103, description="The email has already been taken"),
     *   @SWG\Response(response=104, description="Send mail error"),
     *   @SWG\Response(response=500, description="internal server error")
     * )
     *
     */
    public function postRegister(RegisterRequest $request)
    {
        //get country Name
        $xml = simplexml_load_file("http://www.geoplugin.net/xml.gp?ip=".$this->getRealIpAddr());
        $countryName = 'Singapore';
        if(isset($xml->geoplugin_countryName) && !empty($xml->geoplugin_countryName))
        {
            $countryName = $xml->geoplugin_countryName->__toString();
        }
        $customer = Customer::where('email',$request->email)->first();
        if(isset($customer) && !empty($customer))
        {
            return response([
                        'error' => [
                        'code' => config('constant.status_code.EMAIL_ALREADY_EXIST'),
                        'message'   =>  'The email has already been taken.'],
                    ],Response::HTTP_OK);
        }
        else
        {
            $customer = new Customer();
            $customer->email = $request->email;
            $customer->password = Hash::make($request->password);
            $customer->first_name = $request->name;
            $customer->created_at = date('Y-m-d H:i:s', strtotime(Carbon::now()));
            $customer->country_name = $countryName;
            $customer->save();
            $this->checkRoleUserCustomer($customer);
            //Helper::addSupcriber($customer);
            $this->createDefaultCustomerSettings($customer);
            if($this->activationService->sendActivationMail($customer))
            {
                return response([
                        'error' => [
                        'code' => config('constant.status_code.SEND_MAIL_ERROR'),
                        'message'   =>  'Send mail error'],
                    ],Response::HTTP_OK);

            }
            else
            {
                return response([
                        'data' => ['message'   =>  'We sent you an activation code. Check your email'],
                    ],Response::HTTP_OK);
                // return $this->response->array(['data' => ['message' => "We sent you an activation code. Check your email'" ]],
                //         Response::HTTP_OK);
            }

        }
        
    }

    /**
     * @SWG\Post(
     *   path="/v1/customers/postForgotPassword",
     *   description="<ul><li>email : string (required)</li></ul>",
     *   summary="View",
     *   operationId="api.v1customers.postForgotPassword",
     *   produces={"application/json"},
     *   tags={"CUSTOMERS"},
     *   @SWG\Parameter(
     *     name="body",
     *     in="body",
     *     description="Target customer.",
     *     required=true,
     *    @SWG\Schema(ref="#/definitions/CustomerForgotPassword")
     *   ),
     *   @SWG\Response(response=104, description="Send mail error"),
     *   @SWG\Response(response=108, description="We can't find a user with that e-mail address"),
     *   @SWG\Response(response=500, description="internal server error")
     * )
     *
     */
    public function postForgotPassword(ForgotPasswordRequest $request)
    {
        $customer = Customer::where('email',$request->email)->first();
        $resetPassword = ResetPasswordCustomer::where('email',$request->email)->first();
        $token = $this->getToken();
        if(isset($customer) && !empty($customer))
        {
            if(isset($resetPassword) && !empty($resetPassword))
            {
                 
                $resetPassword->token = $token;
                $resetPassword->save();
                // if($this->mailer->sendMailForgot($customer,$token))
                // {
                //     return response([
                //             'error' => [
                //             'code' => config('constant.status_code.SEND_MAIL_ERROR'),
                //             'message'   =>  'Send mail error'],
                //         ],Response::HTTP_OK);

                // }
                // else
                // {
                //     return response([
                //     'data' => ['message' => "We have sent you an email with a link to reset your password."],
                                 
                //     ],Response::HTTP_OK);
                // }
                FacadesMail::to($customer->email)->send(new ForgotPassword($customer, $token));
                return response([
                    'data' => ['message' => "We have sent you an email with a link to reset your password."],
                ],Response::HTTP_OK);
            }
            else
            {
                $resetPasswordCustomer = new ResetPasswordCustomer();

                $resetPasswordCustomer->email = $customer->email;

                $resetPasswordCustomer->token = $token;
                $resetPasswordCustomer->created_at = date('Y-m-d H:i:s', strtotime(Carbon::now()));

                $resetPasswordCustomer->save();
                if($this->mailer->sendMailForgot($customer,$token))
                {
                    return response([
                            'error' => [
                            'code' => config('constant.status_code.SEND_MAIL_ERROR'),
                            'message'   =>  'Send mail error'],
                        ],Response::HTTP_OK);

                }
                else
                {
                    return response([
                    'data' => ['message' => "We have sent you an email with a link to reset your password."],
                                 
                    ],Response::HTTP_OK);
                }
                 
            }
        }
        else
        {
            return response([
                'error' => [
                'code' => config('constant.status_code.EMAIL_NOT_FOUND'),
                'message' => "Email does not exist in the system. Please try again."],
                             
            ],Response::HTTP_OK);
        }
    }

    /**
     * @SWG\Post(
     *   path="/v1/customers/postResendEmail",
     *   description="<ul><li>email : string (required)</li></ul>",
     *   summary="View",
     *   operationId="api.v1.customers.postResendEmail",
     *   produces={"application/json"},
     *   tags={"CUSTOMERS"},
     *   @SWG\Parameter(
     *     name="body",
     *     in="body",
     *     description="Target customer.",
     *     required=true,
     *    @SWG\Schema(ref="#/definitions/CustomerResendEmail")
     *   ),
     *   @SWG\Response(response=104, description="Send mail error"),
     *   @SWG\Response(response=108, description="We can't find a user with that e-mail address"),
     *   @SWG\Response(response=500, description="internal server error")
     * )
     *
     */
    public function postResendEmail(ForgotPasswordRequest $request)
    {
        $customer = Customer::where('email',$request->email)->first();
        if(isset($customer) && !empty($customer))
        {
            if($customer->status == 1)
            {
                return response([
                        'data' => ['message'   =>  'Your account has been activated'],
                    ],Response::HTTP_OK);
            }
            $customerResend = CustomerActivation::where('customer_id',$customer->id)->first();
            if($customerResend)
            {
                if($this->activationService->reSendMail($customer,$customerResend))
                {
                    return response([
                            'error' => [
                            'code' => config('constant.status_code.SEND_MAIL_ERROR'),
                            'message'   =>  'Send mail error'],
                        ],Response::HTTP_OK);

                }
                else
                {
                    return response([
                            'data' => ['message'   =>  'We sent you an activation code. Check your email'],
                        ],Response::HTTP_OK);
                }
            }
            
        }
        else
        {
            return response([
                'error' => [
                'code' => config('constant.status_code.EMAIL_NOT_FOUND'),
                'message' => "We can't find a user with that e-mail address"],
                             
            ],Response::HTTP_OK);
        }
    }
    /**
     * @SWG\Post(
     *   path="/v1/customers/postSocialLogin",
     *   description="<ul><li>facebook_id or google_id : string (required)</li>
     *     <li>name : string</li></ul>",
     *   summary="View",
     *   operationId="api.v1.customers.postSocialLogin",
     *   produces={"application/json"},
     *   tags={"CUSTOMERS"},
     *   @SWG\Parameter(
     *     name="body",
     *     in="body",
     *     description="Target customer.",
     *     required=true,
     *    @SWG\Schema(ref="#/definitions/CustomerSocialLogin")
     *   ),
     *   @SWG\Response(response=500, description="internal server error")
     * )
     *
     */
    public function postSocialLogin(SocialLoginRequest $request)
    {
        $facebook_id = !empty($request->facebook_id) ? $request->facebook_id : false;
        $google_id = !empty($request->google_id) ? $request->google_id : false;
        $name = !empty($request->name) ? $request->name : false;
        $email = !empty($request->email) ? $request->email : false;
        $deviceToken = !empty($request->device_token) ? $request->device_token : false;
        $deviceType = !empty($request->device_type) ? $request->device_type : false;
        $first_app_login = !empty($request->first_app_login) ? $request->first_app_login : false;
        //get country Name
        $xml = simplexml_load_file("http://www.geoplugin.net/xml.gp?ip=".$this->getRealIpAddr());
        $countryName = 'Singapore';
        if(isset($xml->geoplugin_countryName) && !empty($xml->geoplugin_countryName))
        {
            $countryName = $xml->geoplugin_countryName->__toString();
        }
        $check_first_login = false;

        if($facebook_id && $google_id)
        {   
            return response([
                    'error' => [
                        'message' => "Only 1 in 2 facebook_id or google_id"],
                ],Response::HTTP_OK);
        }
        if($facebook_id)
        {
            $customer = Customer::where('facebook_id', $facebook_id)->first();

            if(isset($customer) && !empty($customer))
            {
                if($customer->email && $customer->first_name)
                {
                    $token = $customer->createToken('Login Token')->accessToken;
                    $item = $customer->withAccessToken($token);
                    if($deviceToken)
                    {
                        //Helper::checkCustomerSubcriber($customer,$deviceToken,$deviceType);
                        Helper::checkDevicetoken($customer,$request->device_token,$deviceType); 
                    }

                    if($first_app_login)
                    {
                        if(!$item->first_app_login)
                        {
                            // $item->first_app_login =  date('Y-m-d', strtotime($first_app_login));
                            $item->first_app_login =  Carbon::now();//->subDay()->format('Y-m-d');
                            $item->save();
                            $check_first_login = true;
                        }
                    }
                    
                    // Update last login
                    $item->last_login = Carbon::now();//->subDay()->format('Y-m-d H:i:s');
                    $item->save();

                    // Rate us pop up not to be shown on first login
                    // $isCheck = Helper::promptReviewMessage($item);
                    $isCheck = !$check_first_login ? Helper::promptReviewMessage($item) : false;
                    //$request->user()->token()->revoke();
                    return response([
                        'data' => [
                            'isCheck' => $isCheck,
                            'message' => "Login Successfully",
                            'customer'   =>  Helper::formatCustomer($item,$token)
                            ],
                    ],Response::HTTP_OK);
                }
                if($name && $email)
                {
                    if(!$customer->first_name)
                    {
                        $customer->first_name = $name;
                    }
                    if(!$customer->email)
                    {
                        $customer->email = $email;
                    }
                    $customer->status = 1;
                    $customer->save();
                    $this->checkRoleUserCustomer($customer);
                    $token = $customer->createToken('Login Token')->accessToken;
                    $item = $customer->withAccessToken($token);
                    if($deviceToken)
                    {
                        //Helper::checkCustomerSubcriber($customer,$deviceToken,$deviceType);
                        Helper::checkDevicetoken($customer,$request->device_token,$deviceType);  
                    }
                    if($first_app_login)
                    {
                        if(!$item->first_app_login)
                        {
                            // $item->first_app_login =  date('Y-m-d', strtotime($first_app_login));
                            $item->first_app_login =  Carbon::now()->subDay()->format('Y-m-d');
                            $item->save();
                            $check_first_login = true;
                        }
                    }

                    // Update last login
                    $item->last_login = Carbon::now();//->subDay()->format('Y-m-d H:i:s');
                    $item->save();

                    // Rate us pop up not to be shown on first login
                    // $isCheck = Helper::promptReviewMessage($item);
                    $isCheck = !$check_first_login ? Helper::promptReviewMessage($item) : false;
                    return response([
                        'data' => [
                            'isCheck' => $isCheck,
                            'message' => "Login Successfully",
                            'customer'   =>  Helper::formatCustomer($item,$token)
                            ],
                    ],Response::HTTP_OK);

                }
                return response([
                    'error' => [
                        'code' => config('constant.status_code.ACCOUNT_WAS_CREATED'),
                        'message' => "Account does not have email"
                        ],
                ],Response::HTTP_OK);
                
            }
            else
            {
                if($email)
                {
                    $customerNew = Customer::where('email', $email)->first();
                    if($customerNew)
                    {
                        if($name)
                        {
                            $customerNew->first_name = $name;
                        }
                        $customerNew->country_name = $countryName;
                        $customerNew->facebook_id = $facebook_id;
                        $customerNew->status = 1;
                        $customerNew->save();

                        $this->checkRoleUserCustomer($customerNew);
                        $token = $customerNew->createToken('Login Token')->accessToken;
                        $item = $customerNew->withAccessToken($token);
                        if($deviceToken)
                        {
                            //Helper::checkCustomerSubcriber($customerNew,$deviceToken,$deviceType);
                            Helper::checkDevicetoken($customerNew,$request->device_token,$deviceType);
                        }
                        if($first_app_login)
                        {
                            if(!$item->first_app_login)
                            {
                                // $item->first_app_login =  date('Y-m-d', strtotime($first_app_login));
                                $item->first_app_login =  Carbon::now()->subDay()->format('Y-m-d');
                                $item->save();
                                $check_first_login = true;
                            }
                        }

                        // Update last login
                        $item->last_login = Carbon::now();//->subDay()->format('Y-m-d H:i:s');
                        $item->save();

                        // Rate us pop up not to be shown on first login
                        // $isCheck = Helper::promptReviewMessage($item);
                        $isCheck = !$check_first_login ? Helper::promptReviewMessage($item) : false;

                        return response([
                            'data' => [
                                'isCheck' => $isCheck,
                                'message' => "Login Successfully",
                                'customer'   =>  Helper::formatCustomer($item,$token)
                                ],
                        ],Response::HTTP_OK);
                    }
                    $newCustomer = new Customer();
                    $newCustomer->facebook_id = $facebook_id;
                    $newCustomer->country_name = $countryName;
                    $newCustomer->created_at = date('Y-m-d H:i:s', strtotime(Carbon::now()));
                    $newCustomer->updated_at = date('Y-m-d H:i:s', strtotime(Carbon::now()));
                    if($name)
                    {
                        $newCustomer->first_name = $name;
                    }
                    
                    $newCustomer->email = $email;
                    $newCustomer->status = 1;
                    $newCustomer->save();
                    $this->checkRoleUserCustomer($newCustomer);
                    $token = $newCustomer->createToken('Login Token')->accessToken;
                    $item = $newCustomer->withAccessToken($token);
                    if($deviceToken)
                    {
                        //Helper::checkCustomerSubcriber($newCustomer,$deviceToken,$deviceType);
                        Helper::checkDevicetoken($newCustomer,$request->device_token,$deviceType);
                    }
                    if($first_app_login)
                    {
                        if(!$item->first_app_login)
                        {
                            // $item->first_app_login =  date('Y-m-d', strtotime($first_app_login));
                            $item->first_app_login =  Carbon::now()->subDay()->format('Y-m-d');
                            $item->save();
                            $check_first_login = true;
                        }
                    }

                    // Update last login
                    $item->last_login = Carbon::now();//->subDay()->format('Y-m-d H:i:s');
                    $item->save();

                    // Rate us pop up not to be shown on first login
                    // $isCheck = Helper::promptReviewMessage($item);
                    $isCheck = !$check_first_login ? Helper::promptReviewMessage($item) : false;
                    return response([
                            'data' => [
                                'isCheck' => $isCheck,
                                'message' => "Login Successfully",
                                'customer'   =>  Helper::formatCustomer($item,$token)
                                ],
                        ],Response::HTTP_OK);
                }
                return response([
                    'error' => [
                        'code' => config('constant.status_code.ACCOUNT_WAS_CREATED'),
                        'message' => "Account does not have email"
                        ],
                ],Response::HTTP_OK);

            }
        }
        if($google_id)
        {
            $customer = Customer::where('google_id', $google_id)->first();

            if(isset($customer) && !empty($customer))
            {
                if($customer->email && $customer->first_name)
                {
                    $token = $customer->createToken('Login Token')->accessToken;
                    $item = $customer->withAccessToken($token);
                    if($deviceToken)
                    {
                        //Helper::checkCustomerSubcriber($customer,$deviceToken,$deviceType);
                        Helper::checkDevicetoken($customer,$request->device_token,$deviceType);
                    }
                    if($first_app_login)
                    {
                        if(!$item->first_app_login)
                        {
                           // $item->first_app_login =  date('Y-m-d', strtotime($first_app_login));
                            $item->first_app_login =  Carbon::now()->subDay()->format('Y-m-d');
                            $item->save();
                            $check_first_login = true;
                        }
                    }

                    // Update last login
                    $item->last_login = Carbon::now();//->subDay()->format('Y-m-d H:i:s');
                    $item->save();

                    // Rate us pop up not to be shown on first login
                    // $isCheck = Helper::promptReviewMessage($item);
                    $isCheck = !$check_first_login ? Helper::promptReviewMessage($item) : false;
                    return response([
                        'data' => [
                            'isCheck' => $isCheck,
                            'message' => "Login Successfully",
                            'customer'   =>  Helper::formatCustomer($item,$token)
                            ],
                    ],Response::HTTP_OK);
                }
                if($name && $email)
                {
                    if(!$customer->first_name)
                    {
                        $customer->first_name = $name;
                    }
                    if(!$customer->email)
                    {
                        $customer->email = $email;
                    }
                    $customer->status = 1;
                    $customer->save();
                    $this->checkRoleUserCustomer($customer);
                    $token = $customer->createToken('Login Token')->accessToken;
                    $item = $customer->withAccessToken($token);
                    if($deviceToken)
                    {
                        //Helper::checkCustomerSubcriber($customer,$deviceToken,$deviceType);
                        Helper::checkDevicetoken($customer,$request->device_token,$deviceType);
                    }
                    if($first_app_login)
                    {
                        if(!$item->first_app_login)
                        {
                            // $item->first_app_login =  date('Y-m-d', strtotime($first_app_login));
                            $item->first_app_login =  Carbon::now()->subDay()->format('Y-m-d');
                            $item->save();
                            $check_first_login = true;
                        }
                    }
                    // Update last login
                    $item->last_login = Carbon::now();//->subDay()->format('Y-m-d H:i:s');
                    $item->save();

                    // Rate us pop up not to be shown on first login
                    // $isCheck = Helper::promptReviewMessage($item);
                    $isCheck = !$check_first_login ? Helper::promptReviewMessage($item) : false;
                    return response([
                        'data' => [
                            'isCheck' => $isCheck,
                            'message' => "Login Successfully",
                            'customer'   =>  Helper::formatCustomer($item,$token)
                            ],
                    ],Response::HTTP_OK);

                }
                return response([
                    'error' => [
                        'code' => config('constant.status_code.ACCOUNT_WAS_CREATED'),
                        'message' => "Account does not have email"
                        ],
                ],Response::HTTP_OK);
                
            }
            else
            {
                if($email)
                {
                    $customerNew = Customer::where('email', $email)->first();
                    if($customerNew)
                    {
                        if($name)
                        {
                            $customerNew->first_name = $name;
                        }
                        $customerNew->google_id = $google_id;
                        $customerNew->status = 1;
                        $customerNew->country_name = $countryName;
                        $customerNew->save();
                        $this->checkRoleUserCustomer($customerNew);
                        $token = $customerNew->createToken('Login Token')->accessToken;
                        $item = $customerNew->withAccessToken($token);
                        if($deviceToken)
                        {
                            //Helper::checkCustomerSubcriber($customerNew,$deviceToken,$deviceType);
                            Helper::checkDevicetoken($customerNew,$request->device_token,$deviceType);
                        }
                        if($first_app_login)
                        {
                            if(!$item->first_app_login)
                            {
                                // $item->first_app_login =  date('Y-m-d', strtotime($first_app_login));
                                $item->first_app_login =  Carbon::now()->subDay()->format('Y-m-d');
                                $item->save();
                                $check_first_login = true;
                            }
                        }

                        // Update last login
                        $item->last_login = Carbon::now();//->subDay()->format('Y-m-d H:i:s');
                        $item->save();

                        // Rate us pop up not to be shown on first login
                        // $isCheck = Helper::promptReviewMessage($item);
                        $isCheck = !$check_first_login ? Helper::promptReviewMessage($item) : false;
                        return response([
                            'data' => [
                                'isCheck' => $isCheck,
                                'message' => "Login Successfully",
                                'customer'   =>  Helper::formatCustomer($item,$token)
                                ],
                        ],Response::HTTP_OK);
                    }
                    $newCustomer = new Customer();
                    $newCustomer->country_name = $countryName;
                    $newCustomer->google_id = $google_id;
                    $newCustomer->created_at = date('Y-m-d H:i:s', strtotime(Carbon::now()));
                    $newCustomer->updated_at = date('Y-m-d H:i:s', strtotime(Carbon::now()));
                    if($name)
                    {
                        $newCustomer->first_name = $name;
                    }
                    
                    $newCustomer->email = $email;
                    $newCustomer->status = 1;
                    $newCustomer->save();
                    $this->checkRoleUserCustomer($newCustomer);
                    $token = $newCustomer->createToken('Login Token')->accessToken;
                    $item = $newCustomer->withAccessToken($token);
                    if($deviceToken)
                    {
                        //Helper::checkCustomerSubcriber($newCustomer,$deviceToken,$deviceType);
                        Helper::checkDevicetoken($newCustomer,$request->device_token,$deviceType);
                    }
                    if($first_app_login)
                    {
                        if(!$item->first_app_login)
                        {
                            // $item->first_app_login =  date('Y-m-d', strtotime($first_app_login));
                            $item->first_app_login =  Carbon::now()->subDay()->format('Y-m-d');
                            $item->save();
                            $check_first_login = true;
                        }
                    }

                    // Update last login
                    $item->last_login = Carbon::now();//->subDay()->format('Y-m-d H:i:s');
                    $item->save();

                    // Rate us pop up not to be shown on first login
                    // $isCheck = Helper::promptReviewMessage($item);
                    $isCheck = !$check_first_login ? Helper::promptReviewMessage($item) : false;
                    return response([
                            'data' => [
                                'isCheck' => $isCheck,
                                'message' => "Login Successfully",
                                'customer'   =>  Helper::formatCustomer($item,$token)
                                ],
                        ],Response::HTTP_OK);
                }
                return response([
                    'error' => [
                        'code' => config('constant.status_code.ACCOUNT_WAS_CREATED'),
                        'message' => "Account does not have email"
                        ],
                ],Response::HTTP_OK);

            }
        }
        return response([
                    'error' => [
                    'message'   =>  'facebook_id or google_id is required'],
                ],Response::HTTP_OK);
    }
    /**
     * @SWG\Get(
     *   path="/v1/customers/getCustomer",
     *   description="",
     *   summary="",
     *   operationId="api.v1.customers.getCustomer",
     *   produces={"application/json"},
     *   tags={"CUSTOMERS"},
     *   @SWG\Response(response=401, description="unauthorized"),
     *   @SWG\Response(response=200, description="Success"),
     *   security={
     *       {"api_key": {}}
     *   }
     * )
     */
    public function getCustomer(Request $request){
        $customer = $request->user();
        return response([
                        'data' => [
                            'customer'   =>  Helper::formatDateCustomer($customer)
                            ],
                    ],Response::HTTP_OK);
    }
    


    public function activateUser($token)
    {
        $customerResend = CustomerActivation::where('token',$token)->first();
        if($customerResend)
        {
            $this->activationService->activateUser($token);
            flash()->success('Account Verified Successfully!.');
            return redirect('/login');
        }
        flash()->error('Account has been Activated!.');
        return redirect('/');
        
        
    }
    protected function getToken()
    {
        return hash_hmac('sha256', str_random(40), config('app.key'));
    }
    public function getResetPassword($id,$token)
    {
        $resetPassword = ResetPasswordCustomer::where('token',$token)->first();
        if(isset($resetPassword) && !empty($resetPassword))
        {
            return view('customer.passwords.reset',compact('id','token'));
        }
        else
        {
            return redirect('/');
        }
    }
    public function postResetPassword(ResetCompleteRequest $request)
    {
        $allRequest = $request->all();
         $resetPassword = ResetPasswordCustomer::where('token',$allRequest['token_id'])->first();

         if(isset($resetPassword) && !empty($resetPassword))
         {
            $customer = $customer = Customer::where('email',$resetPassword->email)->first();

            $customer->password = Hash::make(hash('sha1', $allRequest['password']));
            $customer->save();
            $resetPassword->delete();
            flash()->success('Password Reset success');
            return redirect('/login');
         }
    }
    public function getAuthetizationToken($data){
        return $data;
        $str = explode(' ', $data);
        if(sizeof($str) != 2){
            return "";
        }
        $token = $str[1];
        return $token;
    }
    public function checkRoleUserCustomer($user)
    {
        $userRole = UserRole::where('user_id',$user->id)->first();
        if(!$userRole)
        {
            $newUserRole = new UserRole();
            $newUserRole->user_id = $user->id;
            $newUserRole->role_id = 2;
            $newUserRole->save();
        }

    }
    public function createDefaultCustomerSettings($customer){

        $customerSettingEmail = new CustomerSetting();
        $customerSettingEmail->user_id = $customer->id;
        $customerSettingEmail->key = 'notificaton_email_when_someone_reply_my_message';
        $customerSettingEmail->value = 1;
        $customerSettingEmail->status = 1;
        $customerSettingEmail->save();

        $customerSettingReview = new CustomerSetting();
        $customerSettingReview->user_id = $customer->id;
        $customerSettingReview->key = 'notificaton_email_when_someone_reply_my_review';
        $customerSettingReview->value = 1;
        $customerSettingReview->status = 1;
        $customerSettingReview->save();


    }
    public function getRealIpAddr()
    {
        $ip = '';
        
        if (isset($_SERVER['HTTP_CLIENT_IP']))
            $ip = $_SERVER['HTTP_CLIENT_IP'];
        else if(isset($_SERVER['HTTP_X_FORWARDED_FOR']))
            $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
        else if(isset($_SERVER['HTTP_X_FORWARDED']))
            $ip = $_SERVER['HTTP_X_FORWARDED'];
        else if(isset($_SERVER['HTTP_FORWARDED_FOR']))
            $ip = $_SERVER['HTTP_FORWARDED_FOR'];
        else if(isset($_SERVER['HTTP_FORWARDED']))
            $ip = $_SERVER['HTTP_FORWARDED'];
        else if(isset($_SERVER['HTTP_X_REAL_IP']))
            $ip = $_SERVER['HTTP_X_REAL_IP'];
        else 
            $ip = $_SERVER['REMOTE_ADDR'];
        return $ip;
    }
}
