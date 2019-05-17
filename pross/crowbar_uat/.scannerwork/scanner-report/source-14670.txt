<?php

    namespace App\Http\Controllers\Front;

    use Ramsey\Laravel\OAuth2\Instagram\Facades\Instagram;
    use SammyK\LaravelFacebookSdk\LaravelFacebookSdk;
    use Artesaos\LinkedIn\Facades\LinkedIn;
    use Thujohn\Twitter\Facades\Twitter;
    use Vinkla\Facebook\Facades\Facebook;
    use Illuminate\Support\Facades\Crypt;
    use Illuminate\Support\Facades\Input;
    use Illuminate\Support\Facades\DB;
    use App\Http\Controllers\Session;
    use Illuminate\Validation\Rule;
    use Illuminate\Http\Request;
    use Jenssegers\Agent\Agent;
    use Yajra\Datatables\Html\Builder;
    use Illuminate\Support\Facades\Storage;
    use Srmklive\PayPal\Services\AdaptivePayments;
    use Srmklive\PayPal\Services\ExpressCheckout;

    use Twilio;
    use Cookie;
    use Auth;
    use Exception;
     
    use Models\Users;
    use Models\Talents;
    use Models\Employers;
    use Models\Faqs;

    use App\Http\Controllers\Controller;

    use Voucherify\VoucherifyClient;
    use Voucherify\ClientException;

    class FrontController extends Controller{

        private $provider;

        public function __construct(){
            $this->jsondata     = [];
            $this->message      = false;
            $this->redirect     = false;
            $this->status       = false;
            $this->provider     = new ExpressCheckout();
            $this->prefix       = \DB::getTablePrefix();
            \View::share ( 'footer_settings', \Cache::get('configuration') );
        }

        public function _404(){
            $data['header'] = 'innerheader';
            $data['footer'] = 'innerfooter';
            return view('front.pages.signup')->with($data);
        }

        public function index(Request $request){
            if (!empty(\Auth::guard('web')->check())) {

                if($request->back == 'forum'){
                    return redirect('page/community');
                }else if(\Auth::guard('web')->user()->type == 'employer'){
                    return redirect(sprintf('%s/find-talents',EMPLOYER_ROLE_TYPE));
                }else if(\Auth::guard('web')->user()->type == 'talent'){
                    return redirect(sprintf('%s/find-jobs',TALENT_ROLE_TYPE));
                }elseif(\Auth::guard('web')->user()->type == 'none'){
                    return redirect(sprintf('select-profile'));
                }
                
                return redirect('/');
            }
            
            $data['header']                     = 'header';
            $data['footer']                     = 'footer';
            $language                           = \App::getLocale();
            $data['banner']['home']             = \Models\Banner::getBannerBySection('home');
            $data['banner']['how-it-works']     = \Models\Banner::getBannerBySection('how-it-works');
            $data['tagged_industries']          = \Models\Industries::lists("is_tagged = 'yes' AND parent = 0 ",['id_industry',
                \DB::Raw("IF(({$language} != ''),`{$language}`, `en`) as name"),
                'image']);

            // $data['testimonials'] = \Models\Testimonial::getFrontList();

            if(!empty($request->token)){
                $data['token']      = $request->token;
                $result = \Models\Users::findByToken($request->token,['email','first_name','last_name','company_name','type','company_profile']);
                
                \Session::forget('_old_input');
                if(empty(\Session::get('_old_input'))){
                    \Session::set('_old_input.first_name',$result['first_name']);
                    \Session::set('_old_input.last_name',$result['last_name']);
                    \Session::set('_old_input.company_name',$result['company_name']);
                    \Session::set('_old_input.work_type',$result['company_profile']);
                    \Session::set('_old_input.email',$result['email']);
                }
            }else{
                // \Session::forget('_old_input');
            }

            $profession = \DB::table('industries')
                            ->select('id_industry','en')
                            ->where('parent','=',0)
                            ->get();

            $data['professions'] = json_decode(json_encode($profession),true);

            return view('front.home')->with($data);
        }

        public function tempUserData(Request $request){
            $validator = \Validator::make($request->all(), [
                'temp_name'             => validation('name'),
                'temp_email'       => ['required','email'],
                'temp_professions' => 'required',
                'temp_company'     => 'required',
                'temp_contact'     => 'required',
            ],[
                'temp_name.required'            => 'The name field is required.',
                'temp_email.required'           => 'The email field is required.',
                'temp_professions.required'     => 'The professions field is required.',
                'temp_company.required'         => 'The company field is required.',
                'temp_contact.required'         => 'The contact field is required.',
                'password.regex'                => trans('general.password_regex'),
                'password.digits_between'       => trans('general.password_regex'),
            ]);

            if ($validator->passes()) {

                \Models\Users::postUserRequest([
                    'name' => $request->temp_name,
                    'email' => $request->temp_email,
                    'types_of_professionals' => implode(',', $request->temp_professions),
                    'profession' => '',
                    'company' => $request->temp_company,
                    'contact' => $request->temp_contact,
                ]);

                $this->status   = true;
                $this->message  = '2popup';
            }
            else{
                $this->jsondata = json_decode(json_encode(___error_sanatizer($validator->errors())),true);
            }

            return response()->json([
                'data'      => $this->jsondata,
                'status'    => $this->status,
                'message'   => $this->message,
                'redirect'  => $this->redirect,
                'nomessage' => true,
            ]);
        }

        public function openTempUserData(Request $request){

            $data['header'] = 'innerheader';
            $data['footer'] = 'innerfooter';
            $data['back']   = '';

            $data['title'] = 'Coming Soon';

            return view('front.temp_data')->with($data);

        }

        /**
         * [This method is used for login]
         * @param  Request
         * @return \Illuminate\Http\Response
         */
        
        public function login(Request $request){
            if (!empty(\Auth::guard('web')->check())) {
                if($request->back == 'forum'){
                    return redirect('page/community');
                }else if(\Auth::guard('web')->user()->type == 'talent' && !empty($request->owner)){
                    $redirect = url('talent/accept-reject-transfer-ownership?id='.$request->get('owner'));
                    return redirect($redirect);
                }else if(\Auth::guard('web')->user()->type == 'employer'){
                    return redirect(sprintf('%s/find-talents',EMPLOYER_ROLE_TYPE));
                }else if(\Auth::guard('web')->user()->type == 'talent'){
                    return redirect(sprintf('%s/find-jobs',TALENT_ROLE_TYPE));
                }
                
                return redirect('/');
            }

            $data['header'] = 'innerheader';
            $data['footer'] = 'innerfooter';
            $data['back']   = '';

            if(!empty($request->back)){
                $data['back'] = $request->back;
            }

            if (!empty(Cookie::get(LOGIN_REMEMBER))) {
                $email      = base64_decode(Cookie::get(LOGIN_EMAIL));
                $password   = base64_decode(Cookie::get(LOGIN_PASSWORD));
                $remember   = Cookie::get(LOGIN_REMEMBER);

                $data[LOGIN_EMAIL]      = $email;
                $data[LOGIN_PASSWORD]   = $password;
                $data[LOGIN_REMEMBER]   = $remember;
            }else{
                $data[LOGIN_EMAIL]      = "";
                $data[LOGIN_PASSWORD]   = "";
                $data[LOGIN_REMEMBER]   = "";
            }

            if(!empty($request->token) && (!empty($request->action) && $request->action == 'edit')){
                $data['token']      = $request->token;
                $result = \Models\Users::findByToken($request->token,['email','first_name','last_name']);

                if(empty(\Session::get('_old_input'))){
                    \Session::set('_old_input.first_name',$result['first_name']);
                    \Session::set('_old_input.last_name',$result['last_name']);
                    \Session::set('_old_input.email',$result['email']);
                }
            }else{
                /*\Session::forget('_old_input');*/
            }

            if($request->ownershiptoken){
                $data['ownershiptoken'] = $request->ownershiptoken;
                $data['owner'] = $request->owner;
            } 
            if($request->talent_id){
                $data['talent_id'] = $request->talent_id;
            }
            return view('front.pages.login')->with($data);
        }


        public function hello_pp(Request $request){

            $curl = curl_init();

            curl_setopt_array($curl, array(
              CURLOPT_URL => "http://sandbox.paypal.com/",
              CURLOPT_RETURNTRANSFER => true,
              CURLOPT_ENCODING => "",
              CURLOPT_MAXREDIRS => 10,
              CURLOPT_TIMEOUT => 30,
              CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
              CURLOPT_CUSTOMREQUEST => "GET",
              CURLOPT_HTTPHEADER => array(
                "cache-control: no-cache",
                "postman-token: e378af2a-6655-c4c0-7df0-e2eddb734770"
              ),
            ));

            $response = curl_exec($curl);
            $err = curl_error($curl);

            curl_close($curl);

            if ($err) {
              dd( "cURL Error #:", $err);
            } else {
              dd("response>>>> ", $response);
            }

        }

        /**
         * [This method is used for Authentication]
         * @param  Request
         * @return \Illuminate\Http\Response
         */
        
        public function authenticate(Request $request){
            $validator = \Validator::make($request->all(), [
                LOGIN_EMAIL         => 'required|email',
                LOGIN_PASSWORD      => 'required',
            ],[
                LOGIN_EMAIL.".required"         => 'The email address is required.',        
                LOGIN_EMAIL.".email"            => 'The email address must be a valid email address.',        
                LOGIN_PASSWORD.".required"      => 'The password is required.',
            ]);
            
            $validator->after(function($validator) use ($request){});

            if ($validator->passes()) {
                if ($request->{LOGIN_REMEMBER}){
                    Cookie::queue(LOGIN_EMAIL, base64_encode($request->{LOGIN_EMAIL}));
                    Cookie::queue(LOGIN_PASSWORD, base64_encode($request->{LOGIN_PASSWORD}));
                    Cookie::queue(LOGIN_REMEMBER, ($request->{LOGIN_REMEMBER}));
                } else {
                    Cookie::queue(LOGIN_EMAIL, '', -100);
                    Cookie::queue(LOGIN_PASSWORD, '', -100);
                    Cookie::queue(LOGIN_REMEMBER, '', -100);
                }
                

                $result = \Models\Users::findByEmailAnyStatus($request->{LOGIN_EMAIL},['id_user','password','type','status']);
                $match = \Hash::check($request->{LOGIN_PASSWORD}, $result['password']);
                
                /*Auth::attempt(['email' => $request->{LOGIN_EMAIL}, 'password' => $request->{LOGIN_PASSWORD}, 'type' => 'talent', 'status' => 'active'], $request->{LOGIN_REMEMBER}) || Auth::attempt(['email' => $request->{LOGIN_EMAIL}, 'password' => $request->{LOGIN_PASSWORD}, 'type' => 'employer'], $request->{LOGIN_REMEMBER})*/
                if(!empty($match)) {
                    if($result['status'] == 'pending'){
                        $request->session()->flash(
                            'alert',
                            sprintf(
                                ALERT_INFO,
                                sprintf(
                                    trans("website.W0006"),
                                    '<span class="resend-link"><a href="javascript:;" data-request="inline-ajax" data-target=".message" data-url="'.url('/ajax/resend_activation_link?email='.base64_encode($request->{LOGIN_EMAIL})).'">resend verification link</a></span>'
                                )
                            )
                        );
                        return redirect()->back();
                    }else if($result['status'] == 'inactive'){
                        $request->session()->flash('alert',sprintf(ALERT_WARNING,trans("general.M0002")));
                    }else if($result['status'] == 'suspended'){
                        $request->session()->flash('alert',sprintf(ALERT_WARNING,trans("general.M0003")));
                        
                    }else if($result['status'] == 'trashed'){
                        $request->session()->flash('alert',sprintf(ALERT_WARNING,trans("general.M0621")));
                        
                    }else {
                        \Auth::loginUsingId($result['id_user'], $request->{LOGIN_REMEMBER});

                        $updateArr = array(
                            'is_interview_popup_appeared' => 'no',
                            'last_login' => date('Y-m-d H:i:s')
                        );
                        \Models\Talents::change(Auth::user()->id_user, $updateArr);
                        if($request->get('talent_id')){
                            if(Auth::user()->type == TALENT_ROLE_TYPE){
                                $redirect = 'talent/view/'.$request->talent_id;
                            }elseif(Auth::user()->type == EMPLOYER_ROLE_TYPE){
                                $redirect = 'employer/find-talents/profile?talent_id='.$request->talent_id;
                            }
                        }else{

                            \Session::forget('social');
                            $redirect_url = '/';
                            if(!empty($request->back) && $request->back == 'forum'){
                                return redirect('community/forum');
                            }elseif(!empty($request->back) && $request->back == 'pricing'){
                                return redirect('page/pricing');
                            }

                            if(Auth::user()->type == TALENT_ROLE_TYPE){
                                // dd($request->all(),'zzz');
                                $profile_percentage = \Models\Talents::get_profile_percentage(Auth::user()->id_user);
                                
                                if($profile_percentage['profile_percentage_count'] < \Cache::get('configuration')['minimum_profile_percentage']){
                                    $redirect = sprintf('%s/profile/step/one',TALENT_ROLE_TYPE);
                                }else{
                                    $redirect = sprintf('%s/find-jobs',TALENT_ROLE_TYPE);
                                }
                                if(!empty($request->get('owner'))){
                                    $transfer_ownership_otp = \DB::table('users')->select('transfer_ownership_otp')->where('id_user','=',$request->get('owner'))->first();
                                    $redirect = 'talent/accept-reject-transfer-ownership?id='.$request->get('owner');
                                    // dd($transfer_ownership_otp,$request->get('ownershiptoken'),$transfer_ownership_otp->transfer_ownership_otp,$redirect);
                                    if(!empty($request->get('ownershiptoken')) && $request->get('ownershiptoken')==$transfer_ownership_otp->transfer_ownership_otp){
                                        $redirect = 'talent/accept-reject-transfer-ownership?id='.$request->get('owner');
                                        // dd($redirect);
                                    }
                                }
                            }else if(Auth::user()->type == EMPLOYER_ROLE_TYPE){
                                $redirect = sprintf('%s/find-talents',EMPLOYER_ROLE_TYPE);
                            }else if(Auth::user()->type == 'none'){
                                $redirect = 'select-profile';
                            }else{
                                $redirect = '/login';
                            }
                        }

                        /* RECORDING ACTIVITY LOG */
                        event(new \App\Events\Activity([
                            'user_id' => $request->user()->id_user,
                            'user_type' => $request->user()->type,
                            'action' => 'login',
                            'reference_type' => 'users',
                            'reference_id' => $request->user()->id_user
                        ]));

                        \Session::set('site_currency',$request->user()->currency);

                        return redirect($redirect);    
                    }

                    return redirect()->back();    
                }else{
                    $request->session()->flash('alert',sprintf(ALERT_DANGER,trans("general.M0004")));
                    return redirect()->back()->withErrors($validator)->withInput();
                }
            }else{
                return redirect()->back()->withErrors($validator)->withInput();
            }
        }

        /**
         * [This method is used for signup]
         * @param  null
         * @return \Illuminate\Http\Response
         */
        
        public function signup(){
            $data['header'] = 'innerheader';
            $data['footer'] = 'innerfooter';
            
            if (!empty(\Auth::guard('web')->check())) {
                if(\Auth::guard('web')->user()->type == 'employer'){
                    return redirect(sprintf('%s/find-talents',EMPLOYER_ROLE_TYPE));
                }else if(\Auth::guard('web')->user()->type == 'talent'){
                    return redirect(sprintf('%s/find-jobs',TALENT_ROLE_TYPE));
                }
                
                return redirect('/');
            }
            $data['social'] = \Session::get('social');
            // dd($data['social']);

            return view('front.pages.signup')->with($data);
        }

        /**
         * [This method is used for handling employer signup]
         * @param  Request
         * @return \Illuminate\Http\Response
         */
        
        public function __signupemployer(Request $request){
            $social = \Session::get('social');
            $token_user = [];
            if(!empty($request->remember_token)){
                $token_user = \Models\Users::findByToken($request->remember_token,['id_user']);
            } 

            $validation_password            = validation('password');
            $validation_password[0]         = 'sometimes';

            if(empty(\Session::get('social'))){
                $validation_email = [
                    'required',
                    'email',
                    Rule::unique('users')->ignore('trashed','status')
                ];
            }else{
                $validation_email = [
                    'required',
                    'email'
                ];
            }

            $validator = \Validator::make($request->all(), [
                'first_name'            => validation('first_name'),
                'last_name'             => validation('last_name'),
                /*'company_name'          => array_merge(validation('company_name'),[
                    Rule::unique('users')
                    ->ignore('trashed','status')
                    ->where(function($query) use($token_user){
                        if(!empty($token_user['id_user'])){
                            $query->where('id_user','!=',$token_user['id_user']);
                        }
                    })
                ]),*/
                'email'                 => $validation_email,
                'password'              => $validation_password,
            ],[
                'password.regex'                => trans('general.password_regex'),
                'password.digits_between'       => trans('general.password_regex'),
            ]);
            
            $validator->sometimes(['password'], 'required', function($input){
                return empty($input->social_agree);
            });

            $validator->after(function() use($request, $validator){

                if($request['work_type'] == 'company' && empty($request['company_name'])){
                    $validator->errors()->add('company_name',trans('general.M0624'));
                }

            });

            if ($validator->passes()) {
                if(!empty($request->remember_token)){
                    $result = \Models\Users::findByToken($request->remember_token,['id_user']);

                    if(!empty($result)){
                        $email = $request->email;
                        $code  = bcrypt(__random_string());

                        \Models\Users::change($result['id_user'],[
                            'first_name' => $request->first_name,
                            'last_name' => $request->last_name,
                            'email' => $request->email,
                            'password' => bcrypt($request->password),
                            'company_name' => '',
                            'remember_token' => $code,
                            'agree' => 'yes',
                            'newsletter_subscribed' => (!empty($request->newsletter))?'yes':'no',
                            'updated' => date('Y-m-d H:i:s')
                        ]);
                        
                        if(!empty($email)){
                            $emailData              = ___email_settings();
                            $emailData['email']     = $email;
                            $emailData['name']      = $request->first_name;
                            $emailData['link']      = url(sprintf("activate/account?token=%s",$code));

                            ___mail_sender($email,sprintf("%s %s",$request->first_name,$request->last_name),"employer_signup",$emailData);
                        }
                        
                        /* RECORDING ACTIVITY LOG */
                        event(new \App\Events\Activity([
                            'user_id' => $result['id_user'],
                            'user_type' => 'employer',
                            'action' => 'signup-updated',
                            'reference_type' => 'users',
                            'reference_id' => $result['id_user']
                        ]));
                        
                        return redirect(sprintf('/%s/signup/success?token='.($code),EMPLOYER_ROLE_TYPE));
                    }else{
                        $request->session()->flash('alert',strip_tags(trans(sprintf('website.%s','W0002'))));
                        return redirect(sprintf('/signup/%s/',EMPLOYER_ROLE_TYPE));
                    }
                }else{
                    $field          = ['id_user','type','first_name','last_name','name','email','status'];
                    $email          = (!empty($request->email))?$request->email:"";

                    if(!empty($social['social_key']) && !empty($social['social_id']) && !empty($email)){
                        $result         = (array) \Models\Talents::findByEmail(trim($email),$field);
                    }

                    if(empty($result) && !empty($social['social_key']) && !empty($social['social_id'])){
                        $result         = (array) \Models\Talents::findBySocialId($social['social_key'],$social['social_id'],$field);
                    }

                    if(empty($result)){
                        $dosignup = Employers::__dosignup($request);

                        if(!empty($dosignup['status'])){
                            \Session::forget('social');
                            $employer = \Models\Employers::findById($dosignup['signup_user_id'],$field);

                            if(!empty($employer) && $employer->status == 'pending'){
                                if(!empty($email)){
                                    $code                   = bcrypt(__random_string());
                                    $emailData              = ___email_settings();
                                    $emailData['email']     = $email;
                                    $emailData['name']      = $request->first_name;
                                    $emailData['link']      = url(sprintf("activate/account?token=%s",$code));
                                    \Models\Employers::change($dosignup['signup_user_id'],['remember_token' => $code,'updated' => date('Y-m-d H:i:s')]);
                                    ___mail_sender($email,sprintf("%s %s",$request->first_name,$request->last_name),"employer_signup",$emailData);
                                    
                                    $result = \Models\Users::findById($dosignup['signup_user_id'],['remember_token']);
                                }

                                /* RECORDING ACTIVITY LOG */
                                event(new \App\Events\Activity([
                                    'user_id' => $dosignup['signup_user_id'],
                                    'user_type' => 'employer',
                                    'action' => 'signup',
                                    'reference_type' => 'users',
                                    'reference_id' => $dosignup['signup_user_id']
                                ]));

                                return redirect(sprintf('/%s/signup/success?token='.($result['remember_token']),EMPLOYER_ROLE_TYPE));
                            }else{
                                /*if(!empty($email)){
                                    $emailData              = ___email_settings();
                                    $emailData['email']     = $email;
                                    $emailData['name']      = $request->first_name;
                                    ___mail_sender($email,sprintf("%s %s",$request->first_name,$request->last_name),"talent_signup",$emailData);

                                    if($request->newsletter){
                                        self::newsletter_subscription($email,$dosignup['signup_user_id'],'talent');
                                    }
                                }*/
                                \Auth::loginUsingId($dosignup['signup_user_id']);
                                \Session::forget('social');
                                return redirect(sprintf('/%s/profile/edit/one',EMPLOYER_ROLE_TYPE));
                            }
                        }else{
                            $request->session()->flash('alert',trans(sprintf('general.%s',$dosignup['message'])));
                            return redirect()->back()->withErrors($validator);
                        }
                    }else{
                        if($result['status'] == 'inactive'){
                            $validator->errors()->add('alert', sprintf(ALERT_DANGER,trans('general.M0002')));
                        }elseif($result['status'] == 'suspended'){
                            $validator->errors()->add('alert', sprintf(ALERT_DANGER,trans('general.M0003')));
                        }else{
                            $updated_data = array(
                                $social['social_key']       => $social['social_id'],
                                'email'                     => $email,
                                'status'                    => 'active'
                            );

                            \Session::forget('social');
                            \Models\Talents::change($result['id_user'],$updated_data);
                            \Auth::loginUsingId($result['id_user']);
                            
                            /* RECORDING ACTIVITY LOG */
                            event(new \App\Events\Activity([
                                'user_id' => $result['id_user'],
                                'user_type' => 'employer',
                                'action' => 'social-signup',
                                'reference_type' => 'users',
                                'reference_id' => $result['id_user']
                            ]));

                            return redirect(sprintf('/%s/profile/edit/one',EMPLOYER_ROLE_TYPE));
                        }
                    }
                }
            }else{
                return redirect()->back()->withErrors($validator)->withInput();
            }
        }
        
        /**
         * [This method is used for user's signup]
         * @param  Request
         * @return \Illuminate\Http\Response
         */
        
        public function signuptalent(Request $request){
            $field          = ['id_user','type','first_name','last_name','name','email','status'];
            if(!empty(\Session::get('social_login_without_type'))){
                $request = \Session::get('social_login_without_type');
                $dosignup = \Models\Talents::__dosignup((object)$request);
                            
                if(!empty($dosignup['status'])){
                    $talent = \Models\Talents::findById($dosignup['signup_user_id'],$field);
                    if(!empty($talent) && $talent->status == 'pending'){
                        
                        if(!empty($email)){
                            $code                   = bcrypt(__random_string());
                            $emailData              = ___email_settings();
                            $emailData['email']     = $email;
                            $emailData['name']      = $request['first_name'];
                            $emailData['link']      = url(sprintf("activate/account?token=%s",$code));
                            
                            \Models\Talents::change($talent->id_user,['remember_token' => $code,'updated' => date('Y-m-d H:i:s')]);

                            ___mail_sender($email,sprintf("%s %s",$request['first_name'],$request['last_name']),"talent_signup_verification",$emailData);
                        }
                        $message    = $dosignup['message'];
                    }else{
                        if(!empty($email)){
                            $emailData              = ___email_settings();
                            $emailData['email']     = $email;
                            $emailData['name']      = $request['first_name'];

                            ___mail_sender($email,sprintf("%s %s",$request['first_name'],$request['last_name']),"talent_signup",$emailData);
                        }

                        \Auth::loginUsingId($dosignup['signup_user_id']);
                        $redirect = sprintf('/%s/profile/step/one',TALENT_ROLE_TYPE);
                        return redirect($redirect);
                    }
                }else{
                    $request->session()->flash('alert',sprintf(ALERT_DANGER,$dosignup['message']));
                    return redirect('/');
                }
            }else{
                return redirect('/');
            }
        }

        /**
         * [This method is used for user's signup popup to select user type(Talent or Employer)]
         * @param  Request
         * @return \Illuminate\Http\Response
         */

        public function selectType(Request $request){

            if($request->ajax()){
                return view('front.pages.select_user_type');
            }
        }

        /**
         * [This method is used for employer signup]
         * @param  Request
         * @return \Illuminate\Http\Response
         */
        
        public function signupemployer(Request $request){
            $field          = ['id_user','type','first_name','last_name','name','email','status'];
            if(!empty(\Session::get('social_login_without_type'))){
                $session    = \Session::get('social_login_without_type');
                $dosignup   = Employers::__dosignup((object)$session);
                            
                if(!empty($dosignup['status'])){
                    $employer = \Models\Employers::findById($dosignup['signup_user_id'],$field);
                    if(!empty($employer) && $employer->status == 'pending'){
                        $code                   = bcrypt(__random_string());
                        $emailData              = ___email_settings();
                        $emailData['email']     = $employer->email;
                        $emailData['name']      = $session['first_name'];
                        $emailData['link']      = url(sprintf("activate/account?token=%s",$code));
                        
                        \Models\Employers::change($dosignup['signup_user_id'],['remember_token' => $code,'updated' => date('Y-m-d H:i:s')]);
                        ___mail_sender($employer->email,sprintf("%s %s",$session['first_name'],$session['last_name']),"employer_signup",$emailData);
                        return redirect('/');
                    }else{
                        if(!empty($employer->email)){
                            $emailData              = ___email_settings();
                            $emailData['email']     = $employer->email;
                            $emailData['name']      = $session['first_name'];

                            ___mail_sender($employer->email,sprintf("%s %s",$session['first_name'],$session['last_name']),"employer_social_signup",$emailData);
                        }

                        \Auth::loginUsingId($dosignup['signup_user_id']);
                        $redirect = sprintf('/%s/profile/edit/one',EMPLOYER_ROLE_TYPE);
                        return redirect($redirect);
                    }
                }else{
                    $request->session()->flash('alert',sprintf(ALERT_DANGER,$dosignup['message']));
                    return redirect('/');
                }
            }else{
                return redirect('/');
            }
        }
        
        /**
         * [This method is used for handling user's signup]
         * @param  Request
         * @return \Illuminate\Http\Response
         */
        
        /**
         * [This method is used for handling user's signup]
         * @param  Request
         * @return \Illuminate\Http\Response
         */
        
        public function __signupnone(Request $request){
            $social = \Session::get('social');
            if(!empty($social)){
                if(empty($request->social_agree)){
                    \Session::put('social.social_agree','');
                }else{
                    \Session::put('social.social_agree','agree');
                }
            }

            $validation_password            = validation('password');
            $validation_password[0]         = 'sometimes';

            if(empty(\Session::get('social'))){
                $validation_email = [
                    'required',
                    'email',
                    Rule::unique('users')->ignore('trashed','status')
                ];
            }else{
                $validation_email = [
                    'required',
                    'email'
                ];
            }

            $validator = \Validator::make($request->all(), [
                'first_name'               => validation('first_name'),
                'last_name'                => validation('last_name'),
                'email'                    => $validation_email,
                'password'                 => $validation_password,
            ],[
                'first_name.required'               => trans('general.M0006'),
                'first_name.regex'                  => trans('general.M0007'),
                'first_name.string'                 => trans('general.M0007'),
                'first_name.max'                    => trans('general.M0020'),
                'last_name.required'                => trans('general.M0008'),
                'last_name.regex'                   => trans('general.M0009'),
                'last_name.string'                  => trans('general.M0009'),
                'last_name.max'                     => trans('general.M0019'),
                'email.required'                    => trans('general.M0010'),
                'email.email'                       => trans('general.M0011'),
                'email.unique'                      => trans('general.M0012'),
                'password.required'                 => trans('general.M0013'),
                'password.regex'                    => trans('general.M0014'),
                'password.string'                   => trans('general.M0013'),
                'password.min'                      => trans('general.M0014'),
                'password.max'                      => trans('general.M0018'),
            ]);
            
            $validator->sometimes(['password'], 'required', function($input){
                return empty($input->social_agree);
            });

            $validator->after(function() use($request, $validator){

                if($request['work_type'] == 'company' && empty($request['company_name'])){
                    $validator->errors()->add('company_name',trans('general.M0624'));
                }

            });

            if ($validator->passes()) {
                if(!empty($request->remember_token)){
                    $result = \Models\Users::findByToken($request->remember_token,['id_user']);

                    if(!empty($result)){
                        $email = $request->email;
                        $code  = bcrypt(__random_string());

                        \Models\Users::change($result['id_user'],[
                            'first_name' => $request->first_name,
                            'last_name' => $request->last_name,
                            'email' => $request->email,
                            'password' => bcrypt($request->password),
                            'remember_token' => $code,
                            'agree' => 'yes',
                            'newsletter_subscribed' => (!empty($request->newsletter))?'yes':'no',
                            'updated' => date('Y-m-d H:i:s')
                        ]);
                        
                        if(!empty($email)){
                            $emailData              = ___email_settings();
                            $emailData['email']     = $email;
                            $emailData['name']      = $request->first_name;
                            $emailData['link']      = url(sprintf("activate/account?token=%s",$code));

                            ___mail_sender($email,sprintf("%s %s",$request->first_name,$request->last_name),"talent_signup_verification",$emailData);

                            if($request->newsletter){
                                self::newsletter_subscription($email,$result['id_user'],'none');
                            }
                        }
            
                        /* RECORDING ACTIVITY LOG */
                        event(new \App\Events\Activity([
                            'user_id' => $result['id_user'],
                            'user_type' => 'none',
                            'action' => 'signup-update',
                            'reference_type' => 'users',
                            'reference_id' => $result['id_user']
                        ]));
                        return redirect(sprintf('/%s/signup/success?token='.($code),NONE_ROLE_TYPE));
                    }else{
                        $request->session()->flash('alert',sprintf(ALERT_DANGER,trans(sprintf('website.%s','W0002'))));
                        return redirect(sprintf('/signup/%s/',NONE_ROLE_TYPE));
                    }
                }else{
                    $field          = ['id_user','type','first_name','last_name','name','email','status'];
                    $email          = (!empty($request->email))?$request->email:"";

                    if(!empty($social['social_key']) && !empty($social['social_id']) && !empty($email)){
                        $result         = (array) \Models\Talents::findByEmail(trim($email),$field);
                    }

                    if(empty($result) && !empty($social['social_key']) && !empty($social['social_id'])){
                        $result         = (array) \Models\Talents::findBySocialId($social['social_key'],$social['social_id'],$field);
                    }

                    if(empty($result)){
                        $dosignup = Talents::__dosignupnone($request);
                        if($request->work_type == 'company'){
                            $talentcompanydata['company_name'] = $request->company_name;
                            $talentcompanydata['created'] = date('Y-m-d H:i:s');
                            $talentcompanydata['updated'] = date('Y-m-d H:i:s');
                            $isTalentCompanydId      = \Models\TalentCompany::saveTalentCompany($talentcompanydata);
                            $isCreated = \DB::table('company_connected_talent')->insert(['id_talent_company'=>$isTalentCompanydId,'id_user'=>$dosignup['signup_user_id'],'user_type'=>'owner','updated'=> date('Y-m-d H:i:s'),'created'=> date('Y-m-d H:i:s')]);
                        }

                        if(!empty($dosignup['status'])){
                            \Session::forget('social');
                            $talent = \Models\Talents::findById($dosignup['signup_user_id'],$field);

                            if(!empty($talent) && $talent->status == 'pending'){
                                if(!empty($email)){
                                    $code                   = bcrypt(__random_string());
                                    $emailData              = ___email_settings();
                                    $emailData['email']     = $email;
                                    $emailData['name']      = $request->first_name;
                                    $emailData['link']      = url(sprintf("activate/account?token=%s",$code));
                                    
                                    \Models\Talents::change($dosignup['signup_user_id'],['remember_token' => $code,'updated' => date('Y-m-d H:i:s')]);

                                    ___mail_sender($email,sprintf("%s %s",$request->first_name,$request->last_name),"talent_signup_verification",$emailData);

                                    if($request->newsletter){
                                        self::newsletter_subscription($email,$dosignup['signup_user_id'],'talent');
                                    }
                                }

                                $result = \Models\Users::findById($dosignup['signup_user_id'],['remember_token']);

                                /* RECORDING ACTIVITY LOG */
                                event(new \App\Events\Activity([
                                    'user_id' => $dosignup['signup_user_id'],
                                    'user_type' => 'none',
                                    'action' => 'signup',
                                    'reference_type' => 'users',
                                    'reference_id' => $dosignup['signup_user_id']
                                ]));
                                \Session::forget('_old_input');
                                return redirect(sprintf('/%s/signup/success?token='.($result['remember_token']),NONE_ROLE_TYPE));
                            }else{
                                /*if(!empty($email)){
                                    $emailData              = ___email_settings();
                                    $emailData['email']     = $email;
                                    $emailData['name']      = $request->first_name;
                                    ___mail_sender($email,sprintf("%s %s",$request->first_name,$request->last_name),"talent_signup",$emailData);

                                    if($request->newsletter){
                                        self::newsletter_subscription($email,$dosignup['signup_user_id'],'talent');
                                    }
                                }*/
                                \Auth::loginUsingId($dosignup['signup_user_id']);
                                \Session::forget('social');
                                // return redirect(sprintf('/%s/profile/step/one',NONE_ROLE_TYPE));
                                return redirect(sprintf('select-profile'));
                            }
                        }else{
                            $request->session()->flash('alert',trans(sprintf('general.%s',$dosignup['message'])));
                            return redirect()->back()->withErrors($validator);
                        }
                    }else{
                        if($result['status'] == 'inactive'){
                            $validator->errors()->add('alert', sprintf(ALERT_DANGER,trans('general.M0002')));
                        }elseif($result['status'] == 'suspended'){
                            $validator->errors()->add('alert', sprintf(ALERT_DANGER,trans('general.M0003')));
                        }else{
                            $updated_data = array(
                                $social['social_key']       => $social['social_id'],
                                'email'                     => $email,
                                'status'                    => 'active'
                            );

                            \Session::forget('social');
                            \Models\Talents::change($result['id_user'],$updated_data);
                            \Auth::loginUsingId($result['id_user']);
                            
                            /* RECORDING ACTIVITY LOG */
                            event(new \App\Events\Activity([
                                'user_id' => $result['id_user'],
                                'user_type' => 'none',
                                'action' => 'social-signup',
                                'reference_type' => 'users',
                                'reference_id' => $result['id_user']
                            ]));
                            return redirect(sprintf('/%s/profile/step/one',NONE_ROLE_TYPE));
                        }
                    }
                }
            }else{
                return redirect()->back()->withInput()->withErrors($validator);
            }
        }

        public function __signuptalent(Request $request){
            $social = \Session::get('social');
            if(!empty($social)){
                if(empty($request->social_agree)){
                    \Session::put('social.social_agree','');
                }else{
                    \Session::put('social.social_agree','agree');
                }
            }

            $validation_password            = validation('password');
            $validation_password[0]         = 'sometimes';

            if(empty(\Session::get('social'))){
                $validation_email = [
                    'required',
                    'email',
                    Rule::unique('users')->ignore('trashed','status')
                ];
            }else{
                $validation_email = [
                    'required',
                    'email'
                ];
            }

            $validator = \Validator::make($request->all(), [
                'first_name'               => validation('first_name'),
                'last_name'                => validation('last_name'),
                'email'                    => $validation_email,
                'password'                 => $validation_password,
            ],[
                'first_name.required'               => trans('general.M0006'),
                'first_name.regex'                  => trans('general.M0007'),
                'first_name.string'                 => trans('general.M0007'),
                'first_name.max'                    => trans('general.M0020'),
                'last_name.required'                => trans('general.M0008'),
                'last_name.regex'                   => trans('general.M0009'),
                'last_name.string'                  => trans('general.M0009'),
                'last_name.max'                     => trans('general.M0019'),
                'email.required'                    => trans('general.M0010'),
                'email.email'                       => trans('general.M0011'),
                'email.unique'                      => trans('general.M0012'),
                'password.required'                 => trans('general.M0013'),
                'password.regex'                    => trans('general.M0014'),
                'password.string'                   => trans('general.M0013'),
                'password.min'                      => trans('general.M0014'),
                'password.max'                      => trans('general.M0018'),
            ]);
            
            $validator->sometimes(['password'], 'required', function($input){
                return empty($input->social_agree);
            });

            $validator->after(function() use($request, $validator){

                if($request['work_type'] == 'company' && empty($request['company_name'])){
                    $validator->errors()->add('company_name',trans('general.M0624'));
                }

            });

            if ($validator->passes()) {
                if(!empty($request->remember_token)){
                    $result = \Models\Users::findByToken($request->remember_token,['id_user']);

                    if(!empty($result)){
                        $email = $request->email;
                        $code  = bcrypt(__random_string());

                        \Models\Users::change($result['id_user'],[
                            'first_name' => $request->first_name,
                            'last_name' => $request->last_name,
                            'email' => $request->email,
                            'password' => bcrypt($request->password),
                            'remember_token' => $code,
                            'agree' => 'yes',
                            'newsletter_subscribed' => (!empty($request->newsletter))?'yes':'no',
                            'updated' => date('Y-m-d H:i:s')
                        ]);
                        
                        if(!empty($email)){
                            $emailData              = ___email_settings();
                            $emailData['email']     = $email;
                            $emailData['name']      = $request->first_name;
                            $emailData['link']      = url(sprintf("activate/account?token=%s",$code));

                            ___mail_sender($email,sprintf("%s %s",$request->first_name,$request->last_name),"talent_signup_verification",$emailData);

                            if($request->newsletter){
                                self::newsletter_subscription($email,$result['id_user'],'talent');
                            }
                        }
            
                        /* RECORDING ACTIVITY LOG */
                        event(new \App\Events\Activity([
                            'user_id' => $result['id_user'],
                            'user_type' => 'talent',
                            'action' => 'signup-update',
                            'reference_type' => 'users',
                            'reference_id' => $result['id_user']
                        ]));
                        return redirect(sprintf('/%s/signup/success?token='.($code),TALENT_ROLE_TYPE));
                    }else{
                        $request->session()->flash('alert',sprintf(ALERT_DANGER,trans(sprintf('website.%s','W0002'))));
                        return redirect(sprintf('/signup/%s/',TALENT_ROLE_TYPE));
                    }
                }else{
                    $field          = ['id_user','type','first_name','last_name','name','email','status'];
                    $email          = (!empty($request->email))?$request->email:"";

                    if(!empty($social['social_key']) && !empty($social['social_id']) && !empty($email)){
                        $result         = (array) \Models\Talents::findByEmail(trim($email),$field);
                    }

                    if(empty($result) && !empty($social['social_key']) && !empty($social['social_id'])){
                        $result         = (array) \Models\Talents::findBySocialId($social['social_key'],$social['social_id'],$field);
                    }

                    if(empty($result)){
                        $dosignup = Talents::__dosignup($request);

                        if(!empty($dosignup['status'])){
                            \Session::forget('social');
                            $talent = \Models\Talents::findById($dosignup['signup_user_id'],$field);

                            if(!empty($talent) && $talent->status == 'pending'){
                                if(!empty($email)){
                                    $code                   = bcrypt(__random_string());
                                    $emailData              = ___email_settings();
                                    $emailData['email']     = $email;
                                    $emailData['name']      = $request->first_name;
                                    $emailData['link']      = url(sprintf("activate/account?token=%s",$code));
                                    
                                    \Models\Talents::change($dosignup['signup_user_id'],['remember_token' => $code,'updated' => date('Y-m-d H:i:s')]);

                                    ___mail_sender($email,sprintf("%s %s",$request->first_name,$request->last_name),"talent_signup_verification",$emailData);

                                    if($request->newsletter){
                                        self::newsletter_subscription($email,$dosignup['signup_user_id'],'talent');
                                    }
                                }

                                $result = \Models\Users::findById($dosignup['signup_user_id'],['remember_token']);
                                
                                /* RECORDING ACTIVITY LOG */
                                event(new \App\Events\Activity([
                                    'user_id' => $dosignup['signup_user_id'],
                                    'user_type' => 'talent',
                                    'action' => 'signup',
                                    'reference_type' => 'users',
                                    'reference_id' => $dosignup['signup_user_id']
                                ]));

                                return redirect(sprintf('/%s/signup/success?token='.($result['remember_token']),TALENT_ROLE_TYPE));
                            }else{
                                /*if(!empty($email)){
                                    $emailData              = ___email_settings();
                                    $emailData['email']     = $email;
                                    $emailData['name']      = $request->first_name;
                                    ___mail_sender($email,sprintf("%s %s",$request->first_name,$request->last_name),"talent_signup",$emailData);

                                    if($request->newsletter){
                                        self::newsletter_subscription($email,$dosignup['signup_user_id'],'talent');
                                    }
                                }*/
                                \Auth::loginUsingId($dosignup['signup_user_id']);
                                \Session::forget('social');
                                return redirect(sprintf('/%s/profile/step/one',TALENT_ROLE_TYPE));
                            }
                        }else{
                            $request->session()->flash('alert',trans(sprintf('general.%s',$dosignup['message'])));
                            return redirect()->back()->withErrors($validator);
                        }
                    }else{
                        if($result['status'] == 'inactive'){
                            $validator->errors()->add('alert', sprintf(ALERT_DANGER,trans('general.M0002')));
                        }elseif($result['status'] == 'suspended'){
                            $validator->errors()->add('alert', sprintf(ALERT_DANGER,trans('general.M0003')));
                        }else{
                            $updated_data = array(
                                $social['social_key']       => $social['social_id'],
                                'email'                     => $email,
                                'status'                    => 'active'
                            );

                            \Session::forget('social');
                            \Models\Talents::change($result['id_user'],$updated_data);
                            \Auth::loginUsingId($result['id_user']);
                            
                            /* RECORDING ACTIVITY LOG */
                            event(new \App\Events\Activity([
                                'user_id' => $result['id_user'],
                                'user_type' => 'talent',
                                'action' => 'social-signup',
                                'reference_type' => 'users',
                                'reference_id' => $result['id_user']
                            ]));
                            return redirect(sprintf('/%s/profile/step/one',TALENT_ROLE_TYPE));
                        }
                    }
                }
            }else{
                return redirect()->back()->withInput()->withErrors($validator);
            }
        }

        /**
         * [This method is used for handling edited user's signup]
         * @param  Request
         * @return \Illuminate\Http\Response
         */
        
        public function __editsignupnone(Request $request){
            $data['header']         = 'innerheader';
            $data['footer']         = 'innerfooter';

            if(!empty($request->token)){
                $data['token']      = $request->token;
                $result = \Models\Users::findByToken($request->token,['email']);
            }

            if(!empty($result)){
                $data['email'] = $result['email'];
            }else{
                $data['alert'] = trans('website.W0002');
                $data['email'] = false;
            }

            return view(sprintf('front.pages.sign-edit-talent'))->with($data);       
        }

        /**
         * [This method is used for handling edited user's signup]
         * @param  Request
         * @return \Illuminate\Http\Response
         */
        
        public function __editsignuptalent(Request $request){
            $data['header']         = 'innerheader';
            $data['footer']         = 'innerfooter';

            if(!empty($request->token)){
                $data['token']      = $request->token;
                $result = \Models\Users::findByToken($request->token,['email']);
            }

            if(!empty($result)){
                $data['email'] = $result['email'];
            }else{
                $data['alert'] = trans('website.W0002');
                $data['email'] = false;
            }

            return view(sprintf('front.pages.sign-edit-talent'))->with($data);       
        }

        /**
         * [This method is used to handle edited employer signup]
         * @param  Request
         * @return \Illuminate\Http\Response
         */
        
        public function __editsignupemployer(Request $request){
            $data['header']         = 'innerheader';
            $data['footer']         = 'innerfooter';

            if(!empty($request->token)){
                $data['token']      = $request->token;
                $result = \Models\Users::findByToken($request->token,['email']);
            }

            if(!empty($result)){
                $data['email'] = $result['email'];
            }else{
                $data['alert'] = trans('website.W0002');
                $data['email'] = false;
            }

            return view(sprintf('front.pages.sign-edit-employer'))->with($data);       
        }

        /**
         * [This method is used to handle social signup]
         * @param  Request
         * @return \Illuminate\Http\Response
         */
        
        public function social_signup(Request $request){
            if($request->ajax()){
                return view('front.pages.social',[]);
            }    
        }
        
        /**
         * [This method is used for login with favebook]
         * @param  Request
         * @return \Illuminate\Http\Response
         */
        
        public function facebook(LaravelFacebookSdk $fb, Request $request){
            $login_url = $fb->getLoginUrl(['email']);

            if(!empty($request->type)){
                \Session::put(['redirect_section' => $request->type]);
            }
            /* REDIRCTING FOR AUTHENTICATION */
            return redirect( $login_url );
        }

        /**
         * [This method is used for handle facebook response]
         * @param  Request
         * @return \Illuminate\Http\Response
         */
        
        public function facebook_callback(Request $request, LaravelFacebookSdk $fb){
            $redirect = '/signup/talent';

            if(\Auth::guard('web')->user()){
                if(\Auth::guard('web')->user()->type == 'employer'){
                    $redirect = '/employer/settings/social';
                }elseif(\Auth::guard('web')->user()->type == 'talent'){
                    $redirect = '/talent/settings/social';
                }
            }
        
            try {
                $token = $fb->getAccessTokenFromRedirect();
            } catch (Facebook\Exceptions\FacebookSDKException $e) {
                return redirect($redirect);
            }
            if (! $token) {
                $helper = $fb->getRedirectLoginHelper();

                if (! $helper->getError()) {
                    abort(403, 'Unauthorized action.');
                }

                return redirect($redirect);
                /*
                *   $helper->getError()
                *   $helper->getErrorCode()
                *   $helper->getErrorReason()
                *   $helper->getErrorDescription()
                */
            }
            if (! $token->isLongLived()) {
                $oauth_client = $fb->getOAuth2Client();

                try {
                    $token = $oauth_client->getLongLivedAccessToken($token);
                } catch (Facebook\Exceptions\FacebookSDKException $e) {
                    $request->session()->flash('alert',sprintf(ALERT_DANGER,$e->getMessage()));
                    return redirect($redirect);
                }
            }

            $fb->setDefaultAccessToken($token);
            \Session::put('fb_user_access_token', (string) $token);

            try {
                $response = $fb->get('/me?fields=id,first_name,last_name,name,gender,email,picture.type(large)');
            } catch (Facebook\Exceptions\FacebookSDKException $e) {
                $request->session()->flash('alert',sprintf(ALERT_DANGER,$e->getMessage()));
            }

            // Convert the response to a `Facebook/GraphNodes/GraphUser` collection
            $facebook       = $response->getGraphUser();
            if(\Auth::user()){
                $request->request->add(['facebook_id'=>$facebook['id']]);
                $validator = \Validator::make($request->all(), [
                    'facebook_id'    => [Rule::unique('users')->ignore('trashed','status')->where(function($query) use($request){$query->where('id_user','!=',\Auth::user()->id_user);})],
                ],[
                    sprintf('%s.unique','facebook_id')   => trans('general.M0126'),
                ]);

                if($validator->passes()){
                    $update             =   ['facebook_id' => $facebook['id']];
                    $isUpdated          = \Models\Talents::change(\Auth::user()->id_user,$update);
                    
                    /* RECORDING ACTIVITY LOG */
                    event(new \App\Events\Activity([
                        'user_id'           => \Auth::user()->id_user,
                        'user_type'         => 'employer',
                        'action'            => 'connected-facebook-account',
                        'reference_type'    => 'users',
                        'reference_id'      => \Auth::user()->id_user
                    ]));

                    \Session::set('site_currency',\Auth::user()->currency);
                    return redirect($redirect);
                }else{
                    $request->session()->flash('alert',sprintf(ALERT_DANGER,$validator->errors()->first()));
                    return redirect($redirect);
                }
            }else{
                /*if(empty($facebook['first_name'])){
                    $request->session()->flash('alert',trans('general.M0569'));
                    return redirect('/');
                }else*/{
                    $facebook['email']              = !empty($facebook['email'])?$facebook['email']:'';
                    $facebook['name']               = !empty($facebook['name'])?$facebook['name']:'';
                    $facebook['first_name']         = !empty($facebook['first_name'])?$facebook['first_name']:'';
                    $facebook['last_name']          = !empty($facebook['last_name'])?$facebook['last_name']:'';
                    $facebook['gender']             = !empty($facebook['gender'])?$facebook['gender']:'';
                    $facebook['picture']['url']     = !empty($facebook['picture']['url'])?$facebook['picture']['url']:'';

                    $dologin = Users::__dologin([
                        'social_agree'      => (string) 'agree',
                        'social_key'        => (string) 'facebook_id',
                        'social_id'         => (string) $facebook['id'],
                        'social_email'      => (string) $facebook['email'],
                        'social_name'       => (string) $facebook['name'],
                        'social_first_name' => (string) $facebook['first_name'],
                        'social_last_name'  => (string) $facebook['last_name'],
                        'social_gender'     => (string) $facebook['gender'], 
                        'social_picture'    => (string) $facebook['picture']['url'],
                        'social_country'    => (string) "",
                    ]);

                    $request->session()->flash('alert',sprintf(ALERT_DANGER,$dologin['message']));
                    if(!empty($dologin['status'])){
                        return redirect($dologin['redirect']);
                    }else{
                        return redirect('/login');
                    }   
                }
            }
        }
        
        /**
         * [This method is used for login with linkedin]
         * @param  Request
         * @return \Illuminate\Http\Response
         */
        
        public function linkedin(Request $request){
            $redirect = '/signup/talent';

            if(\Auth::guard('web')->user()){
                if(\Auth::guard('web')->user()->type == 'employer'){
                    $redirect = '/employer/settings/social';
                }elseif(\Auth::guard('web')->user()->type == 'talent'){
                    $redirect = '/talent/settings/social';
                }elseif(\Auth::guard('web')->user()->type == 'none'){
                    $redirect = 'select-profile';
                }
            }
            if (LinkedIn::isAuthenticated()) {
                $linkedin = LinkedIn::get('v1/people/~:(id,email-address,first-name,last-name,location,positions,num-connections,picture-url,specialties,public-profile-url)');
                // dd($linkedin,'zzz',$linkedin['positions']['values'][0]['company']['name'],json_decode(json_encode($linkedin['positions']['values'],true)));

                if(\Auth::user()){
                    $request->request->add(['linkedin_id'=>$linkedin['id']]);
                    $validator = \Validator::make($request->all(), [
                        'linkedin_id'    => [Rule::unique('users')->ignore('trashed','status')->where(function($query) use($request){$query->where('id_user','!=',\Auth::user()->id_user);})],
                    ],[
                        sprintf('%s.unique','linkedin_id')   => trans('general.M0126'),
                    ]);

                    if($validator->passes()){
                        $update             =   ['linkedin_id' => $linkedin['id'], 'updated' => date('Y-m-d H:i:s')];
                        $isUpdated          = \Models\Talents::change(\Auth::user()->id_user,$update);
                            
                        /* RECORDING ACTIVITY LOG */
                        event(new \App\Events\Activity([
                            'user_id'           => \Auth::user()->id_user,
                            'user_type'         => 'employer',
                            'action'            => 'connected-linkedin-account',
                            'reference_type'    => 'users',
                            'reference_id'      => \Auth::user()->id_user
                        ]));

                        \Session::set('site_currency',\Auth::user()->currency);
                        return redirect($redirect);
                    }else{
                        $request->session()->flash('alert',sprintf(ALERT_DANGER,$validator->errors()->first()));
                        return redirect($redirect);
                    }
                }else{
                    /*if(empty($linkedin['firstName'])){
                        $request->session()->flash('alert',trans('general.M0569'));
                        return redirect('/');
                    }else*/{
                        $linkedin['emailAddress']                   = (!empty($linkedin['emailAddress']))?$linkedin['emailAddress']:'';
                        $linkedin['firstName']                      = (!empty($linkedin['firstName']))?$linkedin['firstName']:'';
                        $linkedin['lastName']                       = (!empty($linkedin['lastName']))?$linkedin['lastName']:'';
                        $linkedin['publicProfileUrl']               = (!empty($linkedin['publicProfileUrl']))?$linkedin['publicProfileUrl']:'';
                        $linkedin['location']['country']['code']    = (!empty($linkedin['location']['country']['code']))?$linkedin['location']['country']['code']:'';

                        $linkedin['company_name']                   = (!empty($linkedin['positions']['values'][0]['company']['name']))?$linkedin['positions']['values'][0]['company']['name']:'';
                        
                        $dologin = Users::__dologin([
                            'social_agree'      => (string) 'agree',
                            'social_key'        => (string) 'linkedin_id',
                            'social_id'         => (string) $linkedin['id'],
                            'social_email'      => (string) $linkedin['emailAddress'],
                            'social_name'       => (string) trim(sprintf("%s %s",$linkedin['firstName'],$linkedin['lastName'])),
                            'social_first_name' => (string) $linkedin['firstName'],
                            'social_last_name'  => (string) $linkedin['lastName'],
                            // 'social_picture'    => @(string) $linkedin['publicProfileUrl'],
                            'social_picture'    => isset($linkedin['pictureUrl']) ? (string) $linkedin['pictureUrl'] : "",
                            'social_country'    => (string) $linkedin['location']['country']['code'],
                            'social_gender'     => (string) "", 
                            'social_company_name'    => (string) isset($linkedin['company_name']) ? (string) $linkedin['company_name'] : "",
                        ]);  
                        
                        $request->session()->flash('alert',sprintf(ALERT_DANGER,$dologin['message']));
                        if(!empty($dologin['status'])){
                            return redirect($dologin['redirect']);
                        }else{
                            return redirect('/login');
                        }      
                    }
                }
            }elseif (LinkedIn::hasError()) {
                $request->session()->flash('alert',sprintf(ALERT_DANGER,trans('general.instagram_cancel_request')));
                return redirect('/');
            }

            /* REDIRCTING FOR AUTHENTICATION */
            return redirect(LinkedIn::getLoginUrl(['rw_groups', 'r_contactinfo', 'r_fullprofile', 'w_messages','r_emailaddress']));
        }
        
        /**
         * [This method is used for login with instagram]
         * @param  Request
         * @return \Illuminate\Http\Response
         */
        
        public function instagram(Request $request){
            if(!empty($request->type)){
                \Session::put(['redirect_section' => $request->type]);
            }
            
            $authUrl = Instagram::authorize(['/user/self'], function ($url, $provider) use ($request) {
                $request->session()->put('instagramState', $provider->getState());
                return $url;
            });

            /* REDIRCTING FOR AUTHENTICATION */
            return redirect()->away($authUrl);
        }

         /**
         * [This method is used for handle instagram response]
         * @param  Request
         * @return \Illuminate\Http\Response
         */
        
        public function instagram_callback(Request $request){
            $redirect = '/signup/talent';

            if(\Auth::guard('web')->user()){
                if(\Auth::guard('web')->user()->type == 'employer'){
                    $redirect = '/employer/settings/social';
                }elseif(\Auth::guard('web')->user()->type == 'talent'){
                    $redirect = '/talent/settings/social';
                }
            }
        
            if (!$request->has('state') || $request->state !== $request->session()->get('instagramState')) {
                abort(400, 'Invalid state');
            }

            if (!$request->has('code')) {
                abort(400, 'Authorization code not available');
            }

            $token = Instagram::getAccessToken('authorization_code', [
                'code' => $request->code,
            ]);

            $request->session()->put('instagramToken', $token);


            $instagramToken = $request->session()->get('instagramToken');

            $instagram = Instagram::getResourceOwner($instagramToken);
            
            if(\Auth::user()){
                $request->request->add(['instagram_id'=>$instagram->getId()]);
                $validator = \Validator::make($request->all(), [
                    'instagram_id'    => [Rule::unique('users')->ignore('trashed','status')->where(function($query) use($request){$query->where('id_user','!=',\Auth::user()->id_user);})],
                ],[
                    sprintf('%s.unique','instagram_id')   => trans('general.M0126'),
                ]);

                if($validator->passes()){
                    $update             =   ['instagram_id' => $instagram->getId()];
                    $isUpdated          = \Models\Talents::change(\Auth::user()->id_user,$update);                    

                    /* RECORDING ACTIVITY LOG */
                    event(new \App\Events\Activity([
                        'user_id' => \Auth::user()->id_user,
                        'user_type' => 'employer',
                        'action' => 'connected-instagram-account',
                        'reference_type' => 'users',
                        'reference_id' => \Auth::user()->id_user
                    ]));
                    
                    \Session::set('site_currency',\Auth::user()->currency);
                    return redirect($redirect);
                }else{
                    $request->session()->flash('alert',sprintf(ALERT_DANGER,$validator->errors()->first()));
                    return redirect($redirect);
                }
            }else{
                /*if(empty($instagram->getName())){
                    $request->session()->flash('alert',trans('general.M0569'));
                    return redirect('/');
                }else*/{
                    $dologin = Users::__dologin([
                        'social_agree'      => (string) 'agree',
                        'social_key'        => (string) 'instagram_id',
                        'social_id'         => (string) $instagram->getId(),
                        'social_email'      => (string) "",
                        'social_name'       => (string) $instagram->getName(),
                        'social_first_name' => (string) ___firstname($instagram->getName()),
                        'social_last_name'  => (string) ___lastname($instagram->getName()),
                        'social_picture'    => (string) $instagram->getImageurl(),
                        'social_country'    => (string) "",
                        'social_gender'     => (string) "", 
                    ]);

                    $request->session()->flash('alert',sprintf(ALERT_DANGER,$dologin['message']));
                    if(!empty($dologin['status'])){
                        return redirect($dologin['redirect']);
                    }else{
                        return redirect('/login');
                    }   
                }
            }
        }

        /**
         * [This method is used for login with twitter]
         * @param  Request
         * @return \Illuminate\Http\Response
         */
        
        public function twitter(Request $request){
            // your SIGN IN WITH TWITTER  button should point to this route
            if(!empty($request->type)){
                \Session::put(['redirect_section' => $request->type]);
            }  
            $sign_in_twitter = true;
            $force_login = false;
            
            // Make sure we make this request w/o tokens, overwrite the default values in case of login.
            Twitter::reconfig(['token' => '', 'secret' => '']);
            $token = Twitter::getRequestToken(url('/login/twitter/callback'));

            if (isset($token['oauth_token_secret'])){
                $url = Twitter::getAuthorizeURL($token, $sign_in_twitter, $force_login);

                \Session::put('oauth_state', 'start');
                \Session::put('oauth_request_token', $token['oauth_token']);
                \Session::put('oauth_request_token_secret', $token['oauth_token_secret']);
                return redirect($url);
            }

            /* REDIRCTING FOR AUTHENTICATION */
            return redirect('/');
        }

        /**
         * [This method is used for handle twitter response]
         * @param  Request
         * @return \Illuminate\Http\Response
         */
        
        public function twitter_callback(Request $request){
            $redirect = '/';

            if(\Auth::guard('web')->user()){
                if(\Auth::guard('web')->user()->type == 'employer'){
                    $redirect = '/employer/settings/social';
                }elseif(\Auth::guard('web')->user()->type == 'talent'){
                    $redirect = '/talent/settings/social';
                }
            }
            if(!empty($request->denied)){
                return redirect($redirect);
            }else{
                if (\Session::has('oauth_request_token')){
                    $request_token = [
                        'token'  => \Session::get('oauth_request_token'),
                        'secret' => \Session::get('oauth_request_token_secret'),
                    ];

                    Twitter::reconfig($request_token);

                    $oauth_verifier = false;

                    if (Input::has('oauth_verifier')){
                        $oauth_verifier = Input::get('oauth_verifier');
                    }

                    // getAccessToken() will reset the token for you
                    $token = Twitter::getAccessToken($oauth_verifier);

                    if (!isset($token['oauth_token_secret'])){
                        return redirect('/login/twitter')->with('flash_error', 'We could not log you in on Twitter.');
                    }

                    $twitter = Twitter::getCredentials(['include_email' => 'true']);
                    if (is_object($twitter) && !isset($twitter->error)){
                        \Session::put('access_token', $token);
                        
                        if(\Auth::user()){
                            $request->request->add(['twitter_id'=>$twitter->id_str]);
                            $validator = \Validator::make($request->all(), [
                                'twitter_id'    => [Rule::unique('users')->ignore('trashed','status')->where(function($query) use($request){$query->where('id_user','!=',\Auth::user()->id_user);})],
                            ],[
                                sprintf('%s.unique','twitter_id')   => trans('general.M0126'),
                            ]);

                            if($validator->passes()){
                                $update             =   ['twitter_id' => $twitter->id_str];
                                $isUpdated          = \Models\Users::change(\Auth::user()->id_user,$update);

                                /* RECORDING ACTIVITY LOG */
                                event(new \App\Events\Activity([
                                    'user_id'           => \Auth::user()->id_user,
                                    'user_type'         => 'employer',
                                    'action'            => 'connected-twitter-account',
                                    'reference_type'    => 'users',
                                    'reference_id'      => \Auth::user()->id_user
                                ]));

                                \Session::set('site_currency',\Auth::user()->currency);
                                return redirect($redirect);
                            }else{
                                $request->session()->flash('alert',sprintf(ALERT_DANGER,$validator->errors()->first()));
                                return redirect($redirect);
                            }
                        }else{  
                            if(empty($twitter->name)){
                                $request->session()->flash('alert',trans('general.M0569'));
                                return redirect('/');
                            }else{
                                $dologin = Users::__dologin([
                                    'social_agree'          => (string) 'agree',
                                    'social_key'            => (string) 'twitter_id',
                                    'social_id'             => (string) $twitter->id_str,
                                    'social_name'           => (string) $twitter->name,
                                    'social_first_name'     => (string) ___firstname($twitter->name),
                                    'social_last_name'      => (string) ___lastname($twitter->name),
                                    'social_picture'        => (string) $twitter->profile_image_url_https,
                                    'social_email'          => (string) "",
                                    'social_gender'         => (string) "",
                                    'social_country'        => (string) "",
                                ]);       

                                if($dologin['message'] != 'M0588'){
                                    $request->session()->flash('alert',sprintf(ALERT_DANGER,$dologin['message']));
                                }else{
                                    $request->session()->flash('select_type',true);
                                }

                                if(!empty($dologin['status'])){
                                    return redirect($dologin['redirect']);
                                }else{
                                    return redirect('/login');
                                }   
                            }
                        }
                    }else{
                        $request->session()->flash('alert',sprintf(ALERT_DANGER,trans(sprintf('general.something_wrong'))));
                        return redirect('/');
                    }
                }
            }
        }

        /**
         * [This method is used for login with googleplus]
         * @param  Request
         * @return \Illuminate\Http\Response
         */
        public function googleplus(Request $request)  {
            $redirect = '/signup/talent';

            if(\Auth::guard('web')->user()){
                if(\Auth::guard('web')->user()->type == 'employer'){
                    $redirect = '/employer/settings/social';
                }elseif(\Auth::guard('web')->user()->type == 'talent'){
                    $redirect = '/talent/settings/social';
                }
            }
        
            $google_redirect_url = asset('login/googleplus');
            $gClient = new \Google_Client();
            $gClient->setApplicationName(config('services.google.app_name'));
            $gClient->setClientId(config('services.google.client_id'));
            $gClient->setClientSecret(config('services.google.client_secret'));
            $gClient->setRedirectUri($google_redirect_url);
            $gClient->setDeveloperKey(config('services.google.api_key'));
            $gClient->setScopes(array(
                'https://www.googleapis.com/auth/plus.me',
                'https://www.googleapis.com/auth/userinfo.email',
                'https://www.googleapis.com/auth/userinfo.profile',
            ));
            
            $google_oauthV2 = new \Google_Service_Oauth2($gClient);
            
            if ($request->get('code')){
                $gClient->authenticate($request->get('code'));
                $request->session()->put('token', $gClient->getAccessToken());
            }

            if ($request->session()->get('token')){
                $gClient->setAccessToken($request->session()->get('token'));
            }
            
            if ($gClient->getAccessToken()){
                //For logged in user, get details from google using access token
                $guser = $google_oauthV2->userinfo->get();
                if(\Auth::user()){
                    $request->request->add(['googleplus_id'=>$guser->id]);
                    $validator = \Validator::make($request->all(), [
                        'googleplus_id'    => [Rule::unique('users')->ignore('trashed','status')->where(function($query) use($request){$query->where('id_user','!=',\Auth::user()->id_user);})],
                    ],[
                        sprintf('%s.unique','googleplus_id')   => trans('general.M0126'),
                    ]);

                    if($validator->passes()){
                        $update             =   ['googleplus_id' => $guser->id];
                        $isUpdated          = \Models\Talents::change(\Auth::user()->id_user,$update);

                        /* RECORDING ACTIVITY LOG */
                        event(new \App\Events\Activity([
                            'user_id'           => \Auth::user()->id_user,
                            'user_type'         => 'employer',
                            'action'            => 'connected-google-plus-account',
                            'reference_type'    => 'users',
                            'reference_id'      => \Auth::user()->id_user
                        ]));
                        
                        \Session::set('site_currency',\Auth::user()->currency);
                        return redirect($redirect);
                    }else{
                        $request->session()->flash('alert',sprintf(ALERT_DANGER,$validator->errors()->first()));
                        return redirect($redirect);
                    }
                }                  
            } else{
                //For Guest user, get google login url
                $request->session()->flash('alert',sprintf(ALERT_DANGER,trans('general.M0175')));
                $authUrl = $gClient->createAuthUrl();
                return redirect()->to($authUrl);
            }
        }

        /**
         * [This method is used for terms & conditions , privacy & policy]
         * @param  Request
         * @return \Illuminate\Http\Response
         */
        
        public function staticpage($slug, Request $request, Builder $htmlBuilder){
            if($request->stream === 'mobile'){
                $data['header'] = 'mobile/innerheader';
                $data['footer'] = 'mobile/innerfooter';
            }else{
                $data['header'] = 'innerheader';
                $data['footer'] = 'innerfooter';
            }
            if($slug == 'about' || $slug == 'contact'){
                $data['page'] = \Models\Pages::single($slug,['id','title','excerpt','content']);
                if($slug === 'about'){
                    $data['subpage'] = \Models\Pages::single('history',['id','title','excerpt','content']);
                }
            }else if($slug == 'community'){
                $data['latest_question'] = \Models\Forum::latestQuestion();

                if ($request->ajax()) {
                    $question = \Models\Forum::getQuestionFront();
                    return \Datatables::of($question)
                    ->editColumn('question_description',function($question){
                        if(!empty($question->filename)){
                            $profilePic = asset($question->filename);
                        }
                        else{
                            $profilePic = asset('images/sdf.png');
                        }
                        $html = '<span>
                                    <a href="'.url('community/forum/question/' . ___encrypt($question->id_question)).'">
                                        <span class="question-wrap">
                                            <h5>'.$question->question_description.'</h5>
                                            <span class="question-author">
                                                <span class="flex-cell">
                                                    <img src="'.$profilePic.'" alt="image" class="question-author-image">
                                                    <span class="question-author-action">
                                                        <h4>'.$question->person_name.'</h4>
                                                        <span>'.___ago($question->approve_date).'</span>
                                                    </span>
                                                </span>
                                                <span class="count-wrap">
                                                    <h6 class="reply-counts">Total replies ('.$question->total_reply.')</h6>
                                                </span>
                                            </span>
                                        </span>
                                    </a>
                                </span>';

                        return $html;
                    })
                    ->make(true);
                }

                $data['html'] = $htmlBuilder
                ->parameters(["dom" => "rt <'row'<'col-md-6'i><'col-md-6'p> >"])
                ->addColumn(['data' => 'question_description', 'name' => 'question_description', 'title' => '&nbsp;', 'width' => '0', 'searchable' => false, 'orderable' => false]);

                return view(sprintf('front.pages.questions'))->with($data);
            }else if($slug == 'pricing'){
                return redirect('pricing_page');
                // $data['plan'] = \Models\Plan::getPlanList();
                // return view(sprintf('front.pages.%s',$slug))->with($data);
            }else if($slug === 'how-it-works'){
                $data['banner']['how-it-works']         = \Models\Banner::getBannerBySection('how-it-works');
                $data['banner']['employer']             = \Models\Banner::getBannerBySection('employer');
                $data['banner']['talent']               = \Models\Banner::getBannerBySection('talent');
            }else if($slug === 'faq'){
                $data['banner']['help-center']         = \Models\Banner::getBannerBySection('help-center');
                $data['faq'] = Faqs::select([
                    'id_faq',
                    'parent'
                ])
                ->with([
                    'description' =>function($q){
                        $q->select('faq_id','title');
                    },
                    'topicCategory' => function($q){
                        $q->select('parent','id_faq')
                        ->with([
                            'description' => function($q){
                                $q->select('faq_id','title');
                            },
                            'categoryPost' => function($q){
                                $q->select('parent','id_faq')
                                ->with([
                                    'description' => function($q){
                                        $q->select('faq_id','title');
                                    }
                                ])->whereHas('description',function($q){
                                    if(!empty(request()->search)){
                                        $q->where('title','LIKE','%'.request()->search.'%');
                                    }
                                });    
                            }
                        ]);
                    },
                ])
                ->whereHas('topicCategory.categoryPost.description',function($q){
                    if(!empty(request()->search)){
                        $q->where('title','LIKE','%'.request()->search.'%');
                    }
                })
                ->where('parent','0')
                ->where('status','!=','trashed')
                ->get();

                $data['faq'] = (json_decode(json_encode($data['faq']),true));
                return view('front.pages.faq')->with($data);
            }else if($slug === 'faq-detail'){
                $faq_id = !empty($request->faq) ? ___decrypt($request->faq) : '';
                
                if(!empty($faq_id)){
                    $data['faq'] = Faqs::select([
                        'id_faq',
                        'parent'
                    ])->with([
                        'description' => function($q){
                            $q->select('faq_id','title','description');
                        },
                        'postcategory' => function ($q) {
                            $q->select('id_faq','parent')
                            ->with([
                                'description' => function($q){
                                    $q->select('faq_id','title');
                                },
                                'postcategory' => function ($q) {
                                    $q->select('id_faq','parent')
                                    ->with([
                                        'description' => function($q){
                                            $q->select('faq_id','title');
                                        }
                                    ]);
                                }
                            ]);
                        }
                    ])
                    ->withCount('faqResponse')
                    ->withCount(['faqResponseCount' => function($q){
                            $q->where('response','like');
                        }]
                    )
                    ->with(['faqResponseByIp' => function($q){
                            $q->select('faq_id','response')
                            ->where('ip_address',request()->ip());
                        }]
                    )
                    ->where('id_faq',$faq_id)->first();

                    $data['related_faq'] = Faqs::select([
                        'id_faq',
                        'parent'
                    ])->with([
                        'description' => function($q){
                            $q->select('faq_id','title');
                        }
                    ])->where('type','=','post')->where('id_faq','!=',$faq_id)->where('parent','=',$data['faq']['parent'])->get();
                    $data['faq'] = json_decode(json_encode($data['faq']),true);
                    $data['related_faq'] = json_decode(json_encode($data['related_faq']),true);
                }
                if(empty($data['faq'])){
                    return redirect()->back();
                }

                return view('front.pages.faq-detail')->with($data);
            }else{
                $data['page'] = \Models\Pages::single($slug,['id','title','excerpt','content']);
                return view(sprintf('front.pages.static',$slug))->with($data);       
            }
            
            return view(sprintf('front.pages.%s',$slug))->with($data);       
        }

        /**
         * [This method is used for handle contact page]
         * @param  Request
         * @return \Illuminate\Http\Response
         */
        
        public function _contactpage(Request $request){
            $validator = \Validator::make($request->all(), [
                'name'              => validation('name'),
                'email'             => ['required','email'],
                'phone_number'      => validation('phone_number'),
                'country_code'      => $request->phone_number ? array_merge(['required'], validation('country_code')) : validation('country_code'),
                'message'           => validation('message'),
            ],[
                'name.required'             => trans('general.M0040'),
                'name.regex'                => trans('general.M0041'),
                'name.string'               => trans('general.M0041'),
                'name.max'                  => trans('general.M0042'),
                'email.required'            => trans('general.M0010'),
                'email.email'               => trans('general.M0011'),
                'phone_number.required'     => trans('general.M0030'),
                'phone_number.regex'        => trans('general.M0031'),
                'phone_number.string'       => trans('general.M0031'),
                'phone_number.min'          => trans('general.M0032'),
                'phone_number.max'          => trans('general.M0033'),
                'message.required'          => trans('general.M0034'),
                'message.string'            => trans('general.M0035'),
                'message.max'               => trans('general.M0036'),
                'country_code.string'       => trans('general.M0074'),
            ]);
            
            if ($validator->passes()) {
                
                $configuration      = ___configuration(['site_email','site_name']);
                $message_subject    = 'Contact';
                $message_type       = 'contact-us';

                $sender_email       = $request->email;  
                $sender_name       = $request->name;
                
                $isUpdated = \Models\Messages::composeSecond($sender_name, $sender_email,$request->message,$message_subject,$message_type,$request->country_code,$request->phone_number);

                if(!empty($isUpdated)){
                    $emailData              = ___email_settings();
                    $emailData['email']     = $request->email;
                    $emailData['name']      = $request->name;
                    
                    ___mail_sender($request->email,$request->name,"user_contact",$emailData);
                    ___mail_sender($configuration['site_email'],$configuration['site_name'],"admin_contact",$emailData);
                    $request->session()->flash('alert',sprintf(ALERT_SUCCESS,trans("general.M0037")));
                }else{
                    $request->session()->flash('alert',sprintf(ALERT_SUCCESS,trans("general.M0037")));
                }

                return redirect()->back();
            }

            return redirect()->back()->withErrors($validator)->withInput(); 
        }

        /**
         * [This method is used to reset password]
         * @param  Request
         * @return \Illuminate\Http\Response
         */
        
        public function resetpassword(Request $request){
            $data['header']         = 'innerheader';
            $data['footer']         = 'innerfooter';
            $data['token']          = '';
            $data['message']        = '';
            
            if(!empty($request->token)){
                $data['token']      = $request->token;
                $result = \Models\Users::findByToken($request->token,['id_user']);
            }

            if(!empty($result)){
                $data['link_status']    = 'valid';
            }else{
                $data['link_status']    = 'expired';
                $data['message']        = trans('website.W0002');
            }
            
            return view(sprintf('front.pages.reset'))->with($data);       
        }

        /**
         * [This method is used to handle reset password]
         * @param  Request
         * @return \Illuminate\Http\Response
         */
        
        public function _resetpassword(Request $request){

            $result = \Models\Users::findByToken($request->token,['id_user','type','password']);

            $validator = \Validator::make($request->all(), [
                'password'         => array_merge(['match_old_password:'.$result['password']],validation('password')),
            ],[
                'password.required'           => trans('general.M0013'),
                'password.match_old_password' => trans('general.M0601'),
                'password.regex'              => trans('general.M0014'),
                'password.string'             => trans('general.M0013'),
                'password.min'                => trans('general.M0014'),
                'password.max'                => trans('general.M0018'),
            ]);
            
            if ($validator->passes()) {
                if(!empty($request->token)){
                    $result = \Models\Users::findByToken($request->token,['id_user','type']);

                    if(!empty($result)){
                        $isUpdated = \Models\Users::change($result['id_user'],['password' => bcrypt($request->password),'is_email_verified' => 'yes','remember_token' => bcrypt(__random_string()) ,'updated' => date('Y-m-d H:i:s')]);

                        if(!empty($isUpdated)){
                            $request->session()->flash('alert',sprintf(ALERT_SUCCESS,trans("website.W0003")));

                            /* RECORDING ACTIVITY LOG */
                            event(new \App\Events\Activity([
                                'user_id'           => $result['id_user'],
                                'user_type'         => $result['type'],
                                'action'            => 'reset-password',
                                'reference_type'    => 'users',
                                'reference_id'      => $result['id_user']
                            ]));

                            return redirect()->back()->with(['success' => true]);
                        }
                    }
                    $request->session()->flash('alert',sprintf(ALERT_DANGER,trans("website.W0002")));
                }
                $request->session()->flash('alert',sprintf(ALERT_DANGER,trans("website.W0002")));
            }

            return redirect()->back()->withErrors($validator)->withInput();
        }

        /**
         * [This method is used for forgot password]
         * @param  Request
         * @return \Illuminate\Http\Response
         */
        
        public function forgotpassword(Request $request){
            $data['header']         = 'innerheader';
            $data['footer']         = 'innerfooter';

            return view(sprintf('front.pages.forgot'))->with($data);       
        }

        /**
         * [This method is used to handle forgot password]
         * @param  Request
         * @return \Illuminate\Http\Response
         */
        
        public function _forgotpassword(Request $request){
            $validator = \Validator::make($request->all(), [
                LOGIN_EMAIL                 => ['required','email'],
            ],[
                LOGIN_EMAIL.'.required'     => trans('general.M0010'),
                LOGIN_EMAIL.'.email'        => trans('general.M0011'),
            ]);
            
            if ($validator->passes()) {
                $result = \Models\Users::findByEmail($request->{LOGIN_EMAIL},['id_user','type','email','first_name','last_name','status']);
                
                if(!empty($result)){
                    if($result['status'] == 'pending'){
                        $request->session()->flash(
                            'alert',
                            sprintf(
                                ALERT_INFO,
                                sprintf(
                                    trans("website.W0006"),
                                    '<span class="resend-link"><a href="javascript:;" data-request="inline-ajax" data-target=".message" data-url="'.url('/ajax/resend_activation_link?email='.base64_encode($request->{LOGIN_EMAIL})).'">resend verification link</a></span>'
                                )
                            )
                        ); 
                        return redirect()->back();
                    }else if($result['status'] == 'inactive'){
                        $request->session()->flash('alert',sprintf(ALERT_WARNING,trans("general.M0002")));
                    }else if($result['status'] == 'suspended'){
                        $request->session()->flash('alert',sprintf(ALERT_WARNING,trans("general.M0003")));
                    }else {
                        $code                   = bcrypt(__random_string());
                        $forgot_otp             = strtoupper(__random_string(6));

                        $isUpdated = \Models\Users::change($result['id_user'],[
                            'remember_token'    => $code,
                            'forgot_otp'        => $forgot_otp,
                            'social_account'    => 'changed',
                            'updated'           => date('Y-m-d H:i:s')
                        ]);

                        if(!empty($isUpdated)){
                            $emailData              = ___email_settings();
                            $emailData['email']     = $result['email'];
                            $emailData['name']      = $result['first_name'];
                            $emailData['code']      = $forgot_otp;
                            
                            ___mail_sender($result['email'],sprintf("%s %s",$result['first_name'],$result['last_name']),"forgot_password",$emailData);
                            
                            /* RECORDING ACTIVITY LOG */
                            event(new \App\Events\Activity([
                                'user_id'           => $result['id_user'],
                                'user_type'         => $result['type'],
                                'action'            => 'forgot-password-verify',
                                'reference_type'    => 'users',
                                'reference_id'      => $result['id_user']
                            ]));

                            return redirect(sprintf('forgot/password/verify?token=%s',$code));
                        }else{
                            $request->session()->flash('alert',sprintf(ALERT_SUCCESS,trans("general.M0029")));
                        }
                    }
                }else{
                    $request->session()->flash('alert',sprintf(ALERT_DANGER,trans("general.M0028")));
                }
            }

            return redirect()->back()->withErrors($validator)->withInput();
        }

        /**
         * [This method is used for rendering verify forgot password page]
         * @param  Request
         * @return \Illuminate\Http\Response
         */
        
        public function verifyforgotpassword(Request $request){
            $data['header']         = 'innerheader';
            $data['footer']         = 'innerfooter';
            $data['token']          = $request->token;
            return view(sprintf('front.pages.forgot-verify'))->with($data);       
        }


        /**
         * [This method is used to verify forgot password]
         * @param  Request
         * @return \Illuminate\Http\Response
         */
        
        public function _verifyforgotpassword(Request $request){
            $validator = \Validator::make($request->all(), [
                'verification_code'             => ['required'],
            ],[
                'verification_code.required'    => trans('general.M0504'),
            ]);
            
            if ($validator->passes()) {
                $result = \Models\Users::findByToken($request->token,['id_user','type','email','first_name','last_name','status','forgot_otp']);
                
                if($result['forgot_otp'] != $request->verification_code){
                    $request->session()->flash('alert',sprintf(ALERT_WARNING,trans("general.M0505")));
                }else if(!empty($result)){
                    if($result['status'] == 'pending'){
                        $request->session()->flash(
                            'alert',
                            sprintf(
                                ALERT_INFO,
                                sprintf(
                                    trans("website.W0006"),
                                    '<span class="resend-link"><a href="javascript:;" data-request="inline-ajax" data-target=".message" data-url="'.url('/ajax/resend_activation_link?email='.base64_encode($request->{LOGIN_EMAIL})).'">resend verification link</a></span>'
                                )
                            )
                        ); 
                        return redirect()->back();
                    }else if($result['status'] == 'inactive'){
                        $request->session()->flash('alert',sprintf(ALERT_WARNING,trans("general.M0002")));
                    }else if($result['status'] == 'suspended'){
                        $request->session()->flash('alert',sprintf(ALERT_WARNING,trans("general.M0003")));
                    }else {
                        $code                   = bcrypt(__random_string());
                        $forgot_otp             = strtoupper(__random_string(6));

                        $isUpdated = \Models\Users::change($result['id_user'],[
                            'remember_token'    => $code,
                            'forgot_otp'        => $forgot_otp,
                            'updated'           => date('Y-m-d H:i:s')
                        ]);

                        return redirect(sprintf('/reset/password?token=%s',$code));
                    }
                }else{
                    $request->session()->flash('alert',sprintf(ALERT_DANGER,trans("general.M0028")));
                }
            }

            return redirect()->back()->withErrors($validator)->withInput();
        } 

        /**
         * [This method is used to activate an account]
         * @param  Request
         * @return \Illuminate\Http\Response
         */
        
        public function activateaccount(Request $request){
            $data['header'] = 'innerheader';
            $data['footer'] = 'innerfooter';
            $data['agent']  = new Agent();
            
            if(!empty($request->token)){
                $result = \Models\Users::findByToken($request->token,['id_user','status']);
            }

            if(!empty($result)){
                if($result['status'] != 'active'){
                    $isUpdated = \Models\Users::change($result['id_user'],['remember_token' => bcrypt(__random_string()),'status' => 'active', 'is_email_verified' => 'yes', 'updated' => date('Y-m-d H:i:s')]);
                    
                    if(!empty($isUpdated)){
                        /* RECORDING ACTIVITY LOG */
                        event(new \App\Events\Activity([
                            'user_id'           => $result['id_user'],
                            'user_type'         => 'employer',
                            'action'            => 'connected-linkedin-account',
                            'reference_type'    => 'users',
                            'reference_id'      => $result['id_user'] 
                        ]));
                        
                        $data['message'] = trans('website.W0001');
                    }else{
                        $data['message'] = trans('website.W0002');
                    }
                }else{
                    $data['message'] = trans('website.W0749');
                }
            }else{
                $data['message'] = trans('website.W0002');
            }

            return view(sprintf('front.pages.activate'))->with($data);       
        }

        /**
         * [This method is used to activate an account]
         * @param  Request
         * @return \Illuminate\Http\Response
         */
        
        public function acceptevent(Request $request){

            $data['header'] = 'innerheader';
            $data['footer'] = 'innerfooter';
            $data['agent']  = new Agent();

            $isUpdated = \Models\Events_rsvp::accept_event(___decrypt($request->token));          

            if(!empty($isUpdated)){
                $data['message'] = 'Event successfully accepted.';
            }else{
                $data['message'] = 'Invalid request.';
            }

            return view(sprintf('front.pages.activate'))->with($data); 

        }

        /**
         * [This method is used to download postman collection]
         * @param  null
         * @return \Illuminate\Http\Response
         */
        
        public function download_postman_collection(){
            return \Response::download(public_path('/uploads/collection/'.'CrowBar.postman_collection.json'));
        }

        /**
         * [This method is used to download file]
         * @param  Request
         * @return \Illuminate\Http\Response
         */
        
        public function download_file(Request $request){
            $get_file = \Models\Talents::get_file(
                sprintf('id_file = "%s"',___decrypt($request->file_id)),
                'single',
                [
                    \DB::Raw("CONCAT(folder,'',filename) as file_url"),
                ]
            );
            
            if(!empty($get_file['file_url']) && file_exists(public_path($get_file['file_url']))){
                return \Response::download(public_path($get_file['file_url']));
            }else{
                return redirect('/');
            }
        }

        /**
         * [This method is used for redirecting]
         * @param  Request
         * @return \Illuminate\Http\Response
         */
        
        public function redirect(Request $request) {
            $agent  = new Agent();
            
            if($agent->isMobile()){
                return redirect('crowbar://');
            }else{
                return redirect('/');
            }
        }

        /**
         * [This method is used for language change]
         * @param  Request
         * @return \Illuminate\Http\Response
         */
        
        public function change_language(Request $request) {
            \Artisan::call('cache:clear');
            
            $previous_locale = \App::getLocale();
            if(!empty($request->language)){
                \App::setLocale($request->language);
            }
            
            $previous_url   = \URL::previous();
            $is_home        = array_search($previous_locale, explode("/", $previous_url));
            
            if(empty($is_home)){
                return redirect("{$previous_url}{$request->language}");
            }else{
                return redirect(str_replace("/$previous_locale", "/$request->language", $previous_url));
            }
        }

        /**
         * [This method is used for currency change]
         * @param  Request
         * @return \Illuminate\Http\Response
         */
        
        public function change_currency(Request $request) {
            \Session::set('site_currency', $request->currency);
            
            if(!empty($request->currency)){
                if(Auth::guard('web')->check()){
                    $updateArr = [
                        'currency' => $request->currency
                    ];
                    \Models\Users::change(Auth::user()->id_user, $updateArr);
                    if(Auth::user()->type == 'talent'){
                        \Models\Talents::update_interest_currency(Auth::user()->id_user,$request->currency);
                    }
                }
            }
            
            return redirect()->back();
        }

        /**
         * [This method is used for desktop notification]
         * @param  Request
         * @return \Illuminate\Http\Response
         */
        
        public function mark_read_desktop(Request $request) {
            if(!empty($request->notification_id)){
                \Models\Notifications::mark_read_desktop($request->notification_id);
            }
        }

         /**
         * [This method is used for Account completion]
         * @param  Request
         * @return \Illuminate\Http\Response
         */

        /*Complete Account After created from admin*/
        public function completeAccount(Request $request){
            $data['header']         = 'innerheader';
            $data['footer']         = 'innerfooter';
            $data['token']          = '';
            $data['message']        = '';
            $data['agent']          = new Agent();

            if(!empty($request->token)){
                $data['token']      = $request->token;
                $result = \Models\Users::findByToken($request->token,['id_user']);
            }

            if(!empty($result)){
                $data['link_status']    = 'valid';
            }else{
                $data['link_status']    = 'expired';
                $data['message']        = trans('website.W0002');
            }

            return view(sprintf('front.pages.complete-account'))->with($data);
        }

        /**
         * [This method is used for password creation]
         * @param  Request
         * @return \Illuminate\Http\Response
         */
        
        public function createPassword(Request $request){
            $validator = \Validator::make($request->all(), [
                'password'                  => validation('password'),
            ],[
                'password.required'         => trans('general.M0013'),
                'password.regex'            => trans('general.M0014'),
                'password.string'           => trans('general.M0013'),
                'password.min'              => trans('general.M0014'),
                'password.max'              => trans('general.M0018'),
            ]);

            if ($validator->passes()) {
                if(!empty($request->token)){
                    $result = \Models\Users::findByToken($request->token,['id_user']);

                    if(!empty($result)){
                        $isUpdated = \Models\Users::change($result['id_user'],['password' => bcrypt($request->password),'is_email_verified' => 'yes','status' => 'active','remember_token' => bcrypt(__random_string()) ,'updated' => date('Y-m-d H:i:s')]);

                        if(!empty($isUpdated)){
                            $request->session()->flash('alert',sprintf(ALERT_SUCCESS,trans("website.W0003")));
                            return redirect()->back()->with(['success' => true]);
                        }
                    }
                    $request->session()->flash('alert',sprintf(ALERT_DANGER,trans("website.W0002")));
                }
                $request->session()->flash('alert',sprintf(ALERT_DANGER,trans("website.W0002")));
            }

            return redirect()->back()->withErrors($validator)->withInput();
        }

        /**
         * [This method is used to subscribed news letter]
         * @param  Request
         * @return Json Response
         */
        
        public function subscribedNewsLetter(Request $request){
            $validator = \Validator::make($request->all(), [
                'email' => ['required','email',Rule::unique('subscriber')->ignore('pending','status')]
            ],[
                'email.required' => trans('general.M0010'),
                'email.email' => trans('general.M0011'),
                'email.unique' => trans('general.M0012'),
            ]);

            if ($validator->passes()) {
                $subscribeDetail = \Models\Users::getSubscribeByEmail($request->email);

                if(empty($subscribeDetail)){
                    $email = $request->email;
                    $code = bcrypt(__random_string());
                    $newsletterData = [
                        'email'             => $request->email, 
                        'remember_token'    => $code, 
                        'updated'           => date('Y-m-d H:i:s'), 
                        'created'           => date('Y-m-d H:i:s')
                    ];                    
                    \Models\Users::insertSubscribe($newsletterData);
                }else{
                    $email = $subscribeDetail['email'];
                    $code = $subscribeDetail['remember_token'];
                }

                if(!empty($email)){
                    $emailData              = ___email_settings();
                    $emailData['email']     = $request->email;
                    $emailData['link']      = url(sprintf("confirm-newsletter?token=%s",$code));

                    ___mail_sender($email,'',"newsletter_subscription",$emailData);

                    $this->status = true;
                    $this->message = 'You have successfully subscribed. Please check your email';
                    $this->redirect = url('');
                }else{
                    $this->status = false;
                    $this->message = 'Error.';
                    $this->redirect = url('');
                }
            } else {
                $this->jsondata = ___error_sanatizer($validator->errors());
            }

            return response()->json([
                'data'      => $this->jsondata,
                'status'    => $this->status,
                'message'   => $this->message,
                'redirect'  => $this->redirect,
            ]);
        }

        /**
         * [This method is used to confirm news letter ]
         * @param  Request
         * @return \Illuminate\Http\Response
         */
        
        public function confirmNewsLetter(Request $request){
            $data['header']         = 'innerheader';
            $data['footer']         = 'innerfooter';

            $subscribeDetail = \Models\Users::getSubscribeByToken($request->token);
            $data['agent']  = new Agent();

            if(empty($subscribeDetail)){
                $data['link_status'] = 'expired';
                $data['message']     = trans('website.W0002');
            }else{
                \Models\Users::updateSubscribe($subscribeDetail['id_subscriber'], ['status'=>'active','newsletter_token'=>'']);
                $data['link_status'] = 'valid';
                $data['message']     = trans('website.W0426');
            }

            return view(sprintf('front.pages.confirm-newsletter'))->with($data);
        }

        /**
         * [This method is used for news letter subscription]
         * @param  User_id , User_type , Email
         * @return \Illuminate\Http\Response
         */
        
        public function newsletter_subscription($email,$user_id,$user_type){
            $code = bcrypt(__random_string());
            $newsletterData = [
                'email'             => $email, 
                'newsletter_token'    => $code,
                'user_id'           => $user_id,
                'user_type'         => $user_type,
                'updated'           => date('Y-m-d H:i:s'), 
                'created'           => date('Y-m-d H:i:s')
            ];

            \Models\Users::insertSubscribe($newsletterData);
            if(!empty($email)){
                $emailData              = ___email_settings();
                $emailData['email']     = $email;
                $emailData['link']      = url(sprintf("confirm-newsletter?token=%s",$code));
                if($user_type == 'talent'){
                    $template_name = "newsletter_subscription_".$user_type;
                }else if($user_type == 'talent'){
                    $template_name = "newsletter_subscription_".$user_type;
                }else{
                    $template_name = "newsletter_subscription";
                }
                ___mail_sender($email,'',$template_name,$emailData);
                return true;
            }else{
                return false;
            }
        }

        public function fetchPlan(){

            $plans = \Braintree_Plan::all();
            
        }

        /**
         * [This method is used to unsubscribe news letter ]
         * @param  Request
         * @return \Illuminate\Http\Response
         */
        
        public function newsletter_unsubscribe(Request $request){
            $data['header']         = 'innerheader';
            $data['footer']         = 'innerfooter';

            $subscribeDetail = \Models\Users::getUserNewsletterToken($request->token);
            $data['agent']  = new Agent();

            if(empty($subscribeDetail)){
                $data['link_status'] = 'expired';
                $data['message']     = trans('website.W0436');
            }else{
                \Models\Users::change($subscribeDetail['id_user'], ['newsletter_subscribed'=>'no','newsletter_token'=>'']);
                $data['link_status'] = 'valid';
                $data['message']     = trans('website.W0435');
            }

            return view(sprintf('front.pages.confirm-newsletter'))->with($data);
        }

        /**
         * [This method is used sending news letter to employer]
         * @param  null
         * @return \Illuminate\Http\Response
         */
        
        public function sendNewsletterToEmployer(){
            $employerList = \Models\Users::getUserForNewsLetter('employer');

            foreach ($employerList as $emp) {
                $where = [];
                if(!empty($emp['industry'])){
                    $where[] = 'industry = ' . $emp['industry'];
                }
                if(!empty($emp['subindustry'])){
                    $where[] = 'subindustry = ' . $emp['subindustry'];
                }

                $talentList = \Models\Users::getUserForNewsLetter('talent', $where);

                $htmlLetter = '';
                if(!empty($talentList)){
                    foreach ($talentList as $list) {
                        if(empty($list['expertise'])){
                            $expertise = 'NA';
                        }
                        else{
                            $expertise = $list['expertise'];
                        }
                        if(empty($list['skills'])){
                            $skills = 'NA';
                        }
                        else{
                            $skills = $list['skills'];
                        }
                        if(!empty($list['availability_hours'])){
                            $availability_hours = sprintf(trans('general.M0180'),$list['availability_hours']);
                        }else{
                            $availability_hours = N_A;
                        }

                        $htmlLetter .= sprintf(EMPLOYER_NEWSLETTER_TEMPLATE, $list['name'], $list['rating'], $list['review'], $list['job_completion'], $availability_hours,$expertise,$skills);
                    }

                    /*Email to employer*/
                    $email                  = $emp['email'];
                    $emailData              = ___email_settings();
                    $emailData['email']     = $email;
                    $emailData['name']      = $emp['name'];
                    $emailData['table']     = $htmlLetter;
                    $emailData['unsubscribe']      = url(sprintf("newsletter/unsubscribe/%s",$emp['newsletter_token']));

                    $template_name = "weekly_newsletter_employer";

                    ___mail_sender($email,'',$template_name,$emailData);
                }
            }
        }

        /**
         * [This method is used to send news letter to user's]
         * @param  null
         * @return \Illuminate\Http\Response
         */
        
        public function sendNewsletterToTalent(){
            $talentList = \Models\Users::getUserForNewsLetter();

            foreach ($talentList as $emp) {
                $where = [];
                if(!empty($emp['city'])){
                    $where[] = 'location = ' . $emp['city'];
                }
                if(!empty($emp['industry'])){
                    $where[] = 'industry = ' . $emp['industry'];
                }
                if(!empty($emp['subindustry'])){
                    $where[] = 'subindustry = ' . $emp['subindustry'];
                }

                $currency = \Cache::get('default_currency');
                if(!empty($emp['currency'])){
                    $currency = $emp['currency'];
                }

                $projectList = \Models\Projects::getProjectForNewsLetter($where, $currency, $emp['sign']);

                $htmlLetter = '';
                if(!empty($projectList)){
                    foreach ($projectList as $list) {

                        if($list['price_max'] !== NULL){
                            $price  = ___format($list['price_max'], true, true) .' - '. ___format($list['price'], true, true) . ' price range';
                        }
                        else{
                            $price  = ___format($list['price'], true, true) . ' price';
                        }
                        $htmlLetter .= sprintf(TALENT_NEWSLETTER_TEMPLATE, $list['title'], $list['company_name'], $price .' '. $list['employment'], $list['industries_name']);
                    }
                    /*Email to talent*/
                    $email                  = $emp['email'];
                    $emailData              = ___email_settings();
                    $emailData['email']     = $email;
                    $emailData['name']      = $emp['name'];
                    $emailData['table']     = $htmlLetter;
                    $emailData['unsubscribe']      = url(sprintf("newsletter/unsubscribe/%s",$emp['newsletter_token']));

                    $template_name = "weekly_newsletter_talent";

                    ___mail_sender($email,'',$template_name,$emailData);
                }
            }
        }

        /**
         * [This method is used for share ]
         * @param  Request
         * @return String (Print string)
         */
        
        public function share(Request $request){
            $request['currency'] = 'IDR';
            $result = \Models\Listings::getConvertPrice();
            dd($result);
            $locale = \App::getLocale();
            dd($locale);
            #echo \Share::page('http://jorenvanhocht.be')->facebook();
            echo \Share::page('http://jorenvanhocht.be', 'Share title')
            ->facebook()
            ->twitter()
            ->googlePlus()
            ->linkedin('Extra linkedin summary can be passed here');
        }

        /**
         * [This method is used to complete email account]
         * @param  Request
         * @return \Illuminate\Http\Response
         */
        
        public function completeAccountEmail(Request $request){
            $data['header']         = 'innerheader';
            $data['footer']         = 'innerfooter';
            $data['token']          = '';
            $data['message']        = '';
            $data['agent']  = new Agent();

            if(!empty($request->token)){
                $data['token']      = $request->token;
                $result = \Models\Users::findByToken($request->token,['id_user']);
            }

            if(!empty($result)){
                \Models\Users::change($result['id_user'], ['is_email_verified'=>'yes', 'remember_token' => '']);
                $data['link_status'] = 'valid';
                $data['message']     = trans('website.W0447');
            }else{
                $data['link_status']    = 'expired';
                $data['message']        = trans('website.W0002');
            }

            return view(sprintf('front.pages.confirm-newsletter'))->with($data);
        }

        /**
         * [This method is used to add community forum question]
         * @param  Request
         * @return Json Response
         */
        
        public function community_forum_add_question(Request $request){
            $validator = \Validator::make($request->all(), [
                'question_description'            => ['required'],
                'type'                            => ['required']
            ],[
                'question_description.required'   => trans('general.question_required'),
                'type.required'                   => 'Please select question type.'
            ]);

            if ($validator->passes()) {
                $insertArr = [
                    'id_user'              => \Auth::user()->id_user,
                    'question_description' => $request->question_description,
                    'status'               => 'open',
                    'type'                 => $request->type,
                    'approve_date'         => date('Y-m-d H:i:s'),
                    'created'              => date('Y-m-d H:i:s'),
                    'updated'              => date('Y-m-d H:i:s')
                ];

                \Models\Forum::saveQuestion($insertArr);

                $this->status = true;
                $this->message = 'Your question has been successfully added.';
                $this->redirect = url('network/community/forum');
            } else {
                $this->jsondata = ___error_sanatizer($validator->errors());
            }

            return response()->json([
                'data'      => $this->jsondata,
                'status'    => $this->status,
                'message'   => $this->message,
                'redirect'  => $this->redirect,
            ]);
        }

        /**
         * [This method is used to add community forum answer]
         * @param  Request
         * @return Json Response
         */
        
        public function community_forum_add_answer(Request $request){
            $id_question = ___decrypt($request->id_question);
            $validator = \Validator::make($request->all(), [
                'answer_description'            => ['required']
            ],[
                'answer_description.required'   => trans('general.answer_required')
            ]);

            if($validator->passes()){
                $insertArr = [
                    'id_user'            => \Auth::user()->id_user,
                    'id_question'        => $id_question,
                    'answer_description' => $request->answer_description,
                    'id_parent'          => $request->id_parent,
                    'status'             => 'approve',
                    'type'               => $request->type,
                    'created'            => date('Y-m-d H:i:s'),
                    'approve_date'       => date('Y-m-d H:i:s'),
                    'updated'            => date('Y-m-d H:i:s')
                ];
                \Models\Forum::saveAnswer($insertArr);

                $this->status = true;
                $this->message = 'Your answer has been successfully added.';
                $this->redirect = url('network/community/forum/question/' . $request->id_question);
            }else{
                $this->jsondata = ___error_sanatizer($validator->errors());
            }

            return response()->json([
                'data'      => $this->jsondata,
                'status'    => $this->status,
                'message'   => $this->message,
                'redirect'  => $this->redirect,
            ]);
        }

        public function community_forum_upvote(Request $request){

            if(!empty($request->answer_id)){

                $cb_forum_answer_vote = DB::table('forum_answer_vote');
                $cb_forum_answer_vote->select('vote');
                $cb_forum_answer_vote->where(['user_id' => \Auth::user()->id_user, 'forum_answer_id' => $request->answer_id]);

                if(!empty($cb_forum_answer_vote->get()->count())){
                    $cb_forum_answer_vote = $cb_forum_answer_vote->first();
                    if($cb_forum_answer_vote->vote == 'downvote'){
                        $isUpdated = \DB::table('forum_answer_vote')
                                    ->where(['user_id' => \Auth::user()->id_user, 
                                            'forum_answer_id' => $request->answer_id
                                            ])
                                    ->update(['vote'=>'upvote']);
                    }else{
                        $isUpdated = \DB::table('forum_answer_vote')
                                    ->where(['user_id' => \Auth::user()->id_user, 
                                            'forum_answer_id' => $request->answer_id
                                            ])
                                    ->delete();
                    }
                }else{
                    $insertArr = [
                        'forum_answer_id' => $request->answer_id,
                        'user_id'         => \Auth::user()->id_user,
                        'vote'            => 'upvote',
                        'created'         => date('Y-m-d H:i:s'),
                        'updated'         => date('Y-m-d H:i:s'),
                    ];

                    $isUpdated = \DB::table('forum_answer_vote')->insert($insertArr);                
                }

                $this->status   = true;
                $this->message  = sprintf(ALERT_SUCCESS,trans("website.W0910"));         

            }else{
                $this->status   = true;
                $this->message  = 'Something went wrong.';
            }

            /*Send vote count in the end*/
            $this->jsondata = \Models\Forum_answer_vote::count_votes($request->answer_id);

            return response()->json([
                'data'      => $this->jsondata,
                'status'    => $this->status,
                'message'   => $this->message,
                'redirect'  => $this->redirect,
                'answer_id' => $request->answer_id
            ]);
        }

        public function community_forum_downvote(Request $request){

            if(!empty($request->answer_id)){

                $cb_forum_answer_vote = DB::table('forum_answer_vote');
                $cb_forum_answer_vote->select('vote');
                $cb_forum_answer_vote->where(['user_id' => \Auth::user()->id_user, 'forum_answer_id' => $request->answer_id]);

                if(!empty($cb_forum_answer_vote->get()->count())){

                    $cb_forum_answer_vote = $cb_forum_answer_vote->first();
                    if($cb_forum_answer_vote->vote == 'upvote'){
                        $isUpdated = \DB::table('forum_answer_vote')
                                    ->where(['user_id' => \Auth::user()->id_user, 
                                            'forum_answer_id' => $request->answer_id
                                            ])
                                    ->update(['vote'=>'downvote']);
                    }else{
                        $isUpdated = \DB::table('forum_answer_vote')
                                    ->where(['user_id' => \Auth::user()->id_user, 
                                            'forum_answer_id' => $request->answer_id
                                            ])
                                    ->delete();
                    }

                }else{
                    $insertArr = [
                        'forum_answer_id' => $request->answer_id,
                        'user_id'         => \Auth::user()->id_user,
                        'vote'            => 'downvote',
                        'created'         => date('Y-m-d H:i:s'),
                        'updated'         => date('Y-m-d H:i:s'),
                    ];

                    $isUpdated = \DB::table('forum_answer_vote')->insert($insertArr);                
                }

                $this->status   = true;
                $this->message  = sprintf(ALERT_SUCCESS,trans("website.W0910"));         

            }else{
                $this->status   = true;
                $this->message  = 'Something went wrong.';
            }

            /*Send vote count in the end*/
            $this->jsondata = \Models\Forum_answer_vote::count_votes($request->answer_id);

            return response()->json([
                'data'      => $this->jsondata,
                'status'    => $this->status,
                'message'   => $this->message,
                'redirect'  => $this->redirect,
                'answer_id' => $request->answer_id
            ]);
        }

        public function mark_event_favorite(Request $request){

            if(!empty($request['event_id'])){
                $isUpdated = \Models\Events::save_fav_event(\Auth::user()->id_user,$request['event_id']);
                if($isUpdated['status']){
                    $this->status   = true;
                    $this->message  = 'Action successful.';
                    $this->jsondata =  'Event Bookmarked';              
                }else{
                    $this->status   = false;
                    $this->message  = 'Something went wrong.';
                    $this->jsondata = [];
                }
            }else{
                $this->status   = false;
                $this->message  = 'Something went wrong.';
                $this->jsondata = [];
            }

            return response()->json([
                'data'    => $this->jsondata,
                'status'  => $this->status,
                'message' => $this->message,
                'event_id'=> $request['event_id']
            ]);

        }

        /**
         * [This method is used for community forum question]
         * @param  Request
         * @return \Illuminate\Http\Response
         */
        
        public function community_forum_question(Request $request){
            $data['id_question'] = $request->id_question;
            $id_question = ___decrypt($request->id_question);
            $data['header'] = 'innerheader';
            $data['footer'] = 'innerfooter';

            if(!empty(\Auth::user()) && \Auth::user()->type == "employer"){
                $data['subheader'] = 'employer.includes.top-menu';
            }elseif(!empty(\Auth::user()) && \Auth::user()->type == "talent"){
                $data['subheader'] = 'talent.includes.top-menu';
            }else{
                $data['subheader'] = 'talent.includes.top-menu';
            }

            if(!empty(\Auth::user())){
                $data['user'] = \Models\Talents::get_user(\Auth::user(),true);
                $data['company_profile'] = !empty($data['user']['company_profile'])?$data['user']['company_profile']:'individual';
                $data['extends'] = 'layouts.talent.main';
            }else{
                $data['user'] = [];
                $data['user']['first_name'] = '';
                $data['company_profile'] = 'individual';
                $data['extends'] = 'layouts.front.main';
            }

            $data['related_question'] = \Models\Forum::latestQuestion();
            $data['question'] = \Models\Forum::getQuestionFront($id_question);
            // dd($data['question']);

            if(empty($data['question'])){
                return redirect(url('page/community'));
            }

            $data['orderBy'] = $orderBy = !empty($request->order) ? $request->order : '';

            $data['answer']             = \Models\Forum::getAnswerFrontByQuesId($id_question,$orderBy);

            foreach ($data['answer'] as $key => $value) {
                $filename['folder']         = $value->folder;
                $filename['filename']       = $value->filename;
                $value->filename            = get_file_url($filename);

                if(!empty($value->has_child_answer)){
                    foreach ($value->has_child_answer as $ckey => $cvalue) {
                        $filename['folder']         = $cvalue->folder;
                        $filename['filename']       = $cvalue->filename;
                        $cvalue->filename            = get_file_url($filename);
                    }
                }

            }
            $data['answer'] = json_decode(json_encode($data['answer']), true);
            // dd($data['answer']);

            return view(sprintf('front.pages.question-detail'))->with($data);
        }

        /**
         * [This method is used for community forum]
         * @param  Request
         * @return \Illuminate\Http\Response
         */
        
        public function community_forum(Request $request, Builder $htmlBuilder){
            $data['header'] = 'innerheader';
            $data['footer'] = 'innerfooter';
            $data['latest_question'] = \Models\Forum::latestQuestion();

            if(!empty(\Auth::user()) && \Auth::user()->type == "employer"){
                $data['subheader'] = 'employer.includes.top-menu';
            }elseif(!empty(\Auth::user()) && \Auth::user()->type == "talent"){
                $data['subheader'] = 'talent.includes.top-menu';
            }else{
                $data['subheader'] = 'talent.includes.top-menu';
            }

            if(!empty(\Auth::user())){
                $data['user'] = \Models\Talents::get_user(\Auth::user(),true);
                $data['extends'] = 'layouts.talent.main';
            }else{
                $data['user'] = [];
                $data['user']['first_name'] = '';
                $data['extends'] = 'layouts.front.main';
            }

            $search_ques = !empty($request->search_question) ? $request->search_question:'';

            if ($request->ajax()) {
                $question = \Models\Forum::getQuestionFront();

                return \Datatables::of($question)->filter(function ($instance) use ($request,$search_ques) {
                    if ($request->has('search') || !empty($search_ques) ){
                        if(!empty($request->search['value']) || !empty($search_ques) ){
                            $instance->collection = $instance->collection->filter(function ($row) use ($request,$search_ques) {
                                return (\Str::contains($row->question_description, $request->search['value']) || \Str::contains(strtolower($row->question_description), $request->search['value']) || \Str::contains($row->question_description, $search_ques) || \Str::contains(strtolower($row->question_description), $search_ques) ) ? true : false;
                            });
                        } 
                    }
                })
                ->editColumn('question_description',function($question){
                    /*get linkedin pofile url*/
                    $filedata['filename'] = $question->filename;
                    $filedata['folder'] = $question->folder;
                    $profilePic = get_file_url($filedata);

                    // if(!empty($question->filename)){
                    //     $profilePic = asset($question->filename);
                    // }else{
                    //     $profilePic = asset('images/sdf.png');
                    // }

                    if($question->type == 'individual'){
                        $person_or_cmp_name = $question->person_name;
                    }else{
                        $person_or_cmp_name = $question->firm_name;
                    }

                    if($question->is_following == 1){
                        $is_following = 'active';
                        $follow_text = 'Following';
                    }else{
                        $is_following = '';
                        $follow_text = 'Follow';
                    }

                    /*Show follow/following button if other user*/
                    if(!empty(\Auth::user()) && $question->id_user != \Auth::user()->id_user){
                        $follow_html = '<a href="javascript:void(0);" class="follow-icon follow_user_'.$question->id_user.' '.$is_following.'" data-request="home-follow-user" data-user_id="'.$question->id_user.'" data-url="'.url(sprintf('/mynetworks/community/follow-user?user_id=%s',$question->id_user)).'" >'.$follow_text.'</a>';
                    }else{ /*A user can't follow himself*/
                        $follow_html = '';
                    }

                    $html = '<div class="question-listing">
                                <a class="follow-link" href="'.url('/network/community/forum/question/'.___encrypt($question->id_question)).'">
                                    <span class="question-wrap">
                                        <h5>'.$question->question_description.'</h5>

                                        <span class="question-author question-author-wrapper">
                                            <span class="flex-cell">
                                                <img src="'.$profilePic.'" alt="image" class="question-author-image">
                                                    <span class="question-author-action">
                                                        <h4>'.$person_or_cmp_name.'</h4>
                                                        <span>'.___ago($question->approve_date).'</span>

                                                        <span>'.$follow_html.'</span>
                                                    </span>


                                                    <span class="count-wrap">
                                                        <h6 class="reply-counts">'.$question->total_reply.' replies</h6>
                                                    </span>
                                            </span>
                                            <div class="dropdown socialShareDropdown">
                                                <a href="javascript:void(0);" data-toggle="dropdown" aria-expanded="false">'.trans("website.W0908").'</a>
                                                <ul class="dropdown-menu">
                                                    <li>
                                                        <a href="javascript:void(0);" class="linkdin_icon">
                                                            <script src="//platform.linkedin.com/in.js" type="text/javascript"> lang: en_US</script>
                                                            <script type="IN/Share" data-url="'.url("/network/community/forum/question/".$question->id_question).'"></script>
                                                            <img src="'.asset('images/linkedin.png').'">
                                                        </a>
                                                    </li>
                                                    <li>
                                                        <a class="fb_icon" href="https://www.facebook.com/sharer/sharer.php?u='.url('/network/community/forum/question/'.$question->id_question).'" target="_blank">
                                                            <img src="'.asset('images/facebook.png').'">
                                                        </a>
                                                    </li>
                                                    <li>
                                                        <a href="https://web.whatsapp.com/send?text='.url('/network/community/forum/question/').$question->id_question.'" target="_blank" id="whatsapp_link" data-action="share/whatsapp/share"><img src="'.asset('images/whatsapp-logo.png').'"></a>
                                                    </li>
                                                </ul>
                                            </div>
                                        </span>
                                    </span>
                                </a>
                            </div>';

                    return $html;
                })
                ->make(true);
            }

            $data['html'] = $htmlBuilder
            ->parameters(["dom" => "<'row'<'col-md-9'><'col-md-3 below-search-question'>rt <'row'<'col-md-6'><'col-md-6'p> >"])
            ->addColumn(['data' => 'question_description', 'name' => 'question_description', 'title' => '&nbsp;', 'width' => '0', 'searchable' => false, 'orderable' => false]);

            return view(sprintf('front.pages.questions'))->with($data);
        }

        /**
         * [This method is used for community forum]
         * @param  Request
         * @return \Illuminate\Http\Response
         */
        
        public function community_forum_ques_ask(Request $request, Builder $htmlBuilder){

            $data['header'] = 'innerheader';
            $data['footer'] = 'innerfooter';

            $data['title']  = 'Ask a question';

            $data['latest_question'] = \Models\Forum::latestQuestion();

            $data['subheader'] = 'talent.includes.top-menu';
            $data['user']      = \Models\Talents::get_user(\Auth::user(),true);

            $data['company_profile'] = !empty($data['user']['company_profile'])?$data['user']['company_profile']:'individual';       

            return view(sprintf('front.pages.questions-ask'))->with($data);
        }

        /**
         * [This method is used to answer forum list]
         * @param  Request
         * @return \Illuminate\Http\Response
         */
        
        public function forum_list_answer(Request $request){
            $id_reply = $request->id_reply;
            $id_ques = ___decrypt($request->id_question);
            $data['answer'] = \Models\Forum::getAnswerByQuesId($id_ques, $id_reply, 'child', 'front');

            return view('front.pages.load-answer')->with($data);
        }

        /**
         * [This method is used for google translate]
         * @param  Request
         * @return \Illuminate\Http\Response
         */
        
        public function googleTranslate(Request $request){
            dd(___configuration(['google_translate_enabled'])['google_translate_enabled']);
            $translator = new \Dedicated\GoogleTranslate\Translator;
            dd(\Cache::get('languages'));
            #dd(\Cache::get('default_language'));
            /*$result = $translator->setSourceLang('en')
                     ->setTargetLang('ru')
                     ->translate('Hello World');

            $result = $translator->setTargetLang('ru')
                     ->translate('Hello World');*/
            $result = '';
            try {
                $result = $translator->detect('洗腦');
            }
            catch (Exception $e) {
                $result = false;
            }

            dd($result); // "Привет мир"
        }

        public function currency_exchange(Request $request){
            $data['header']     = 'header';
            $data['footer']     = 'footer';
            $data['currency']   =  json_decode(json_encode(\Models\Currency::getCurrencyList()),true);
            return view(sprintf('front.pages.currency-exchange'))->with($data);
        }

        public function faq_response(Request $request){
            if(!in_array($request->response, ['like','dislike'])){
                $this->message = trans('website.W0684');
            }else{
                $responseArray = [
                    'faq_id'        => ___decrypt($request->faq_id),
                    'ip_address'    => $request->ip(),
                    'response'      => $request->response,
                    'created'       => date('Y-m-d H:i:s'),
                    'updated'       => date('Y-m-d H:i:s')
                ];

                $this->status       = true;
                $this->message      = trans('website.W0685');
                $response           = \Models\Faq_response::add($responseArray);
                $this->jsondata     = sprintf('%s out of %s helpful votes',$response['liked_response'],$response['total_response']);
            }

            return response()->json([
                'data'      => $this->jsondata,
                'status'    => $this->status,
                'message'   => $this->message
            ]);
        }

        public function paypal_express_checkout(Request $request){
            $data['user']                   = Users::findByToken($request->token);
            if(!empty($data['user'])){
                $project_id                     = ___decrypt($request->project_id);
                $proposal_id                    = ___decrypt($request->proposal_id);
                $data['project']                = \Models\Projects::defaultKeys()->where('id_project',$project_id)->get()->first();
                $data['proposal']               = \Models\Employers::get_proposal($proposal_id,['quoted_price']);
                if(!empty($data['project']) && !empty($data['proposal'])){
                    $data['number_of_days']         = ___get_total_days($data['project']->startdate,$data['project']->enddate);
                    $is_recurring       = false;
                    $repeat_till_month  = 0;

                    if($data['project']['employment'] == 'hourly'){
                        $sub_total                  = $data['proposal']['global_quoted_price']*$data['proposal']['decimal_working_hours']*$data['number_of_days'];
                    }else if($data['project']['employment'] == 'monthly'){
                        $sub_total                  = ($data['proposal']['global_quoted_price']);
                        $is_recurring               = ($data['number_of_days'] > MONTH_DAYS) ? true : false;
                        $repeat_till_month          = ($data['number_of_days'] - MONTH_DAYS)/MONTH_DAYS;                        
                    }else if($data['project']['employment'] == 'fixed'){
                        $sub_total                  = $data['proposal']['global_quoted_price'];
                    }else{
                        $request->session()->flash('alert',sprintf(ALERT_WARNING,trans('general.M0371')));
                        return redirect(url(sprintf('%s/project/proposals/talent?proposal_id=%s&project_id=%s',EMPLOYER_ROLE_TYPE,___encrypt($proposal_id),___encrypt($project_id))));
                    }

                    $commission                     = ___calculate_commission($sub_total,$data['user']['commission'], $data['user']['commission_type']);
                    $paypal_commission              = ___calculate_paypal_commission($sub_total);
                    $payment = $data['payment']                = [
                        'transaction_user_id'               => (string) $data['user']['id_user'],
                        'transaction_company_id'            => (string) $data['user']['id_user'],
                        'transaction_user_type'             => $data['user']['type'],
                        'transaction_project_id'            => $project_id,
                        'transaction_proposal_id'           => $proposal_id,
                        'transaction_total'                 => $sub_total+$commission+$paypal_commission,
                        'transaction_subtotal'              => $sub_total,
                        'transaction_type'                  => 'debit',
                        'transaction_date'                  => date('Y-m-d H:i:s'),
                        'transaction_commission'            => $commission,
                        'transaction_paypal_commission'     => $paypal_commission,
                        'transaction_is_recurring'          => $is_recurring,
                        'transaction_repeat_till_month'     => $repeat_till_month
                    ];
                    \Session::set('payment',$data['payment']);
                    $payment['transaction'] = \Models\Payments::init_employer_payment($payment,$request->repeat);
                    \Session::set('payment',$payment);
                    $recurring  = ($request->get('mode') === 'recurring') ? true : false;
                    $cart       = \Models\Payments::getCheckoutData($payment,$payment['transaction_is_recurring']);
                    $cart['return_url'] = url(sprintf("paypal-express-checkout-callback"));
                    $cart['cancel_url'] = url(sprintf("paypal-express-checkout-cancel-callback"));
                    try {
                        $response = $this->provider->setExpressCheckout($cart,$payment['transaction_is_recurring']);
                        return redirect($response['paypal_link']);
                    } catch (\Exception $e) {
                        session()->put(['code' => 'danger', 'message' => "Error processing PayPal payment for Order $invoice->id!"]);
                    }
                }else{
                    return redirect(url('404'));
                }
            }else{
                return redirect(url('404'));
            }
        }

        public function paypal_payment_callback(Request $request){
            $payment = \Session::get('payment');
            $recurring = ($request->get('mode') === 'recurring') ? true : false;
            $token = $request->get('token');
            $PayerID = $request->get('PayerID');

            $cart = \Models\Payments::getCheckoutData($payment,$recurring);

            // Verify Express Checkout Token
            $response = $this->provider->getExpressCheckoutDetails($token);
            if (in_array(strtoupper($response['ACK']), ['SUCCESS', 'SUCCESSWITHWARNING'])) {
                if ($recurring === true) {
                    $response = $this->provider->createMonthlySubscription($response['TOKEN'], $cart['total'], $cart['subscription_desc']);
                    $profileesponse = $this->provider->getRecurringPaymentsProfileDetails($response['PROFILEID']);
                    if (!empty($response['PROFILESTATUS']) && in_array($response['PROFILESTATUS'], ['ActiveProfile', 'PendingProfile'])) {
                        $status = 'Processed';
                    } else {
                        $status = 'Invalid';
                    }
                } else {
                    // Perform transaction on PayPal
                    $payment_status = $this->provider->doExpressCheckoutPayment($cart, $token, $PayerID);
                    $status = $payment_status['PAYMENTINFO_0_PAYMENTSTATUS'];
                }
                if($status == 'Completed' || $status == 'Processed'){
                    // if(/*$request->success === TRUE || */app()->environment() !== 'production'){
                    $isUpdated = \Models\Payments::update_transaction(
                        $payment['transaction']->id_transactions,
                        [
                            'transaction_reference_id' => !empty($payment_status['PAYMENTINFO_0_TRANSACTIONID'])?$payment_status['PAYMENTINFO_0_TRANSACTIONID']:$profileesponse['PROFILEID'],
                            'transaction_status' => 'confirmed', 
                            'updated' => date('Y-m-d H:i:s')
                        ]
                    );

                    if(!empty($isUpdated)){
                        $isProposalAccepted =  \Models\Employers::accept_proposal($payment['transaction_user_id'],$payment['transaction_project_id'],$payment['transaction_proposal_id']);
                        
                        \Session::forget('payment');
                        \Session::forget('card_token');
                        return redirect(url(sprintf('paypal-payment-success')));
                    }else{                        
                        \Session::forget('payment');
                        \Session::forget('card_token');
                        return redirect(url(sprintf('paypal-payment-success')));
                    }
                }else{
                    $isUpdated = \Models\Payments::update_transaction(
                        $payment['transaction']->id_transactions,
                        [
                            'transaction_status' => 'failed', 
                            'updated' => date('Y-m-d H:i:s')
                        ]
                    );
                    return redirect(url(sprintf('paypal-payment-success?error=true&short_message="%s&long_message=%s',$paypal_array['get_express_array']['L_SHORTMESSAGE0'],$paypal_array['get_express_array']['L_LONGMESSAGE0'])));
                }
            }
        } 

        public function paypal_payment_cancel_callback(Request $request){
            return redirect(url(sprintf('paypal-payment-cancel')));
        }

        public function paypal_payment_success(Request $request)
        {
            echo "success";
        }

        public function paypal_payment_cancel(Request $request)
        {
            echo "cancel";
        }

        public function recurrsive_success_url(Request $request){

            $token       = $request->get('token');
            $project_id  = $request->get('project_id');
            $proposal_id = $request->get('proposal_id');
            $user_id     = $request->get('user_id');

            $result = \Models\PaypalPayment::execute_billing_agreement($user_id,$token);

            if($result['status'] == true){

                $isUpdated = \Models\Payments::update_transactionByIds($project_id,$proposal_id);
                $isProposalAccepted =  \Models\Employers::accept_proposal($user_id,$project_id,$proposal_id);

                return [
                    'status' => true,
                    'message' => trans('general.'.$isProposalAccepted['message'])
                ];

            }else{

                return [
                    'status' => false,
                    'message' => trans('website.W0921')
                ];

            }


        }
        public function recurrsive_cancel_url(Request $request){
            return [
                    'status' => false,
                    'message' => trans('website.W0921')
                ];
        }

                /**
         * [This method is used for community forum]
         * @param  Request
         * @return \Illuminate\Http\Response
         */
        
        public function community_home(Request $request){

            $data['header'] = 'innerheader';
            $data['footer'] = 'innerfooter';

            if(!empty(\Auth::user()) && \Auth::user()->type == "employer"){
                $data['subheader'] = 'employer.includes.top-menu';
            }elseif(!empty(\Auth::user()) && \Auth::user()->type == "talent"){
                $data['subheader'] = 'talent.includes.top-menu';
            }else{
                $data['subheader'] = 'talent.includes.top-menu';
            }

            $data['view']      = 'front.home.home';

            $data['get_group'] = !empty($request->group) ? ___decrypt($request->group) : 0;

            if(!empty(\Auth::user())){
                $data['user'] = \Models\Talents::get_user(\Auth::user(),true);
                $data['extends'] = 'layouts.talent.main';
            }else{
                $data['user'] = [];
                $data['user']['first_name'] = '';
                $data['extends'] = 'layouts.front.main';
            }

            $data['suggestion_right_txt'] = 'Suggested jobs';

            if(!empty(\Auth::user()) && \Auth::user()->type == "talent"){
                $request->request->add(['currency' => \Session::get('site_currency')]);
                $data['suggested_jobs'] = \Models\Projects::four_suggested_jobs();
                // dd($data['suggested_jobs']);
                $data['suggestion_right_txt'] = 'Suggested Jobs';
            }elseif(!empty(\Auth::user()) && \Auth::user()->type == "employer") {
                $data['user'] = \Models\Employers::get_user(\Auth::user());
                $skill_id = 1;
                if(!empty($data['user']['company_work_field'])){
                    $skill_id = \Models\Skills::getSkillIdByName($data['user']['company_work_field']);
                }
                $data['suggested_talents'] = \Models\Users::emp_suggested_talents($skill_id);
                $data['suggestion_right_txt'] = 'Suggested talents';
            }else{
                $data['suggested_jobs'] = [];
                $data['suggested_talents'] = [];
            }

            if(!empty(\Auth::user())){
                $data['groups'] = \Models\GroupMember::getGroupMemberList(\Auth::user()->id_user);
            }else{
                $data['groups'] = [];
            }
            // dd($data);
            /*This is for the selected group from URL*/
            $data['get_group_id'] = !empty($request->group) ? ___decrypt($request->group) :'';

            return view(sprintf('front.home.index'))->with($data);
        }

        public function _community_home(Request $request){
            
            $this->status           = true;
            $base_url               = ___image_base_url();
            $prefix                 = DB::getTablePrefix();
            $html                   = "";
            $page                   = (!empty($request->page))?$request->page:1;

            $language               = \App::getLocale();

            $user_id                = !empty(\Auth::user()->id_user) ? \Auth::user()->id_user : 0;
            $prefix                 = DB::getTablePrefix();

            /* ------ NEW QUERY -----*/

            $limit   = 1;
            $offset  = ($page-1)*1;

            $forum   = collect();
            $article = collect();
            $events  = collect();

            $search = !empty($request->search) ? $request->search :'';
            // $option = !empty($request->listing_radio) ? $request->listing_radio :'';

            $group_id = !empty($request->get_group_id) ? $request->get_group_id :'';

            $group_members = [];
            if(!empty($group_id)){ 
                $group_members = \Models\GroupMember::getGroupMembersById($group_id);
                $group_members = array_column($group_members, 'user_id');
            }

            /*If hyperlinks on home page ever change, use following code*/

            // if(!empty($option)){
            //     switch ($option) {
            //         case 'forum':
            //             $forum = \Models\Forum::getAll($search, $limit, $offset, $group_members); 
            //             break;
            //         case 'event':
            //             $events = \Models\Events::getAll($search, $limit, $offset, $group_members);
            //             break;  
            //         case 'article':
            //             $article = \Models\Article::getAll($search, $limit, $offset, $group_members);
            //             break;
            //         default:
            //             $forum   = \Models\Forum::getAll($search, $limit, $offset, $group_members); 
            //             $article = \Models\Article::getAll($search, $limit, $offset, $group_members);
            //             $events  = \Models\Events::getAll($search, $limit, $offset, $group_members);
            //             break;
            //     }
            // }else{
                $forum   = \Models\Forum::getAll($search, $limit, $offset, $group_members); 
                $article = \Models\Article::getAll($search, $limit, $offset, $group_members);
                $events  = \Models\Events::getAll($search, $limit, $offset, $group_members);
            // }

            $collection = new \Illuminate\Support\Collection();
            $sortedCollection = $collection->merge($forum)->merge($article)->merge($events)->sortBy('created');

            $sortedCollection = json_decode(json_encode($sortedCollection),true);
            $sortedCollection = array_reverse($sortedCollection);
            $list_data = [
                'result'                => $sortedCollection,
                'total'                 => count($sortedCollection),
                'total_filtered_result' => $forum->count() + $article->count() + $events->count(),
            ];

            /* ------ /NEW QUERY -----*/

            if(!empty($list_data['result'])){
                foreach($list_data['result'] as $keys => $data){
                    if($data['list_type'] == 'article'){
                        $getdata['article'] = \Models\Article::getHomeArticleDetail($data['article_id']);
                        $getdata['article_last_comment'] = \Models\Article::getLastComment($data['article_id']);
                        
                        /*get linkedin pofile url*/
                        $filedata['filename'] = $getdata['article']['user_img'];
                        $filedata['folder'] = $getdata['article']['folder'];
                        $getdata['article']['user_img'] = get_file_url($filedata);

                        $html .= \View::make('front.home.layouts.article')->with($getdata);
                    }elseif(!empty(\Auth::user()) && \Auth::user()->type == "talent" && $data['list_type'] == 'event'){
                        $getdata['event'] = \Models\Events::getHomeEventDetail($data['id_events']);
                        $getdata['userDetails'] = \Models\Events::userDetailsForEvent($data['id_events']);
                        
                        $filedata['filename'] = $getdata['userDetails']['filename'];
                        $filedata['folder'] = $getdata['userDetails']['folder'];
                        $getdata['userDetails']['user_img'] = get_file_url($filedata);

                        $html .= \View::make('front.home.layouts.events')->with($getdata);
                    }elseif($data['list_type'] == 'question'){
                        $getdata['question'] = \Models\Forum::getHomeQuestionFront($data['id_question']);

                        /*get linkedin pofile url*/
                        $filedata['filename'] = $getdata['question']['filename'];
                        $filedata['folder'] = $getdata['question']['folder'];
                        $getdata['question']['filename'] = get_file_url($filedata);

                        $html .= \View::make('front.home.layouts.question')->with($getdata);
                    }else{
                        $html .= '';
                    }
                }
            }else{
                $html .= '<div class="no-records-found">'.trans('website.W0750').'</div>';
            }
            
            if($list_data['total_filtered_result'] == 3 || $list_data['total_filtered_result'] == 2){
                $load_more = '<button type="button" class="btn btn-default btn-block btn-lg" data-request="filter-paginate" data-url="'.url(sprintf('/_mynetworks/_home')).'" data-target="#home_listing" data-showing="#paginate_showing" data-loadmore="#loadmore" data-form="[role=\'find-jobs\']">'.trans('website.W0254').'</button>';
                $can_load_more = true;
            }else{
                $load_more = '<button type="button" class="btn btn-default btn-block btn-lg hide" data-request="filter-paginate" data-url="'.url(sprintf('/_mynetworks/_home')).'" data-target="#home_listing" data-showing="#paginate_showing" data-loadmore="#loadmore" data-form="[role=\'find-jobs\']">'.trans('website.W0254').'</button>';
                $can_load_more = false;
            }

            echo json_encode(
                array(
                    "filter_title"      => sprintf(trans('general.M0215'),$list_data['total']),
                    "paging"            => ($request->page == 1)?false:true,
                    "recordsFiltered"   => $list_data['total_filtered_result'],
                    "recordsTotal"      => $list_data['total'],
                    "loadMore"          => $load_more, 
                    "data"              => $html,
                    "can_load_more"     => $can_load_more,
                )
            );            
        }
        
        public function search_jobs(Request $request){

            $data['header'] = 'innerheader';
            $data['footer'] = 'innerfooter';
            $data['back']   = '';

            $data['title'] = 'Search Job';
            $data['view']  = 'front.pages.search_job_view';

            $getIndustryId =  \Models\Industries::getIndustryIdByName($request->industry);
            $request->request->add(['industry' => $getIndustryId]);

            return view('front.pages.search_job_index')->with($data);

        }

        public function _search_jobs(Request $request){

            $request['currency']    = \Session::get('site_currency');
            $request['language']    = \App::getLocale();
            
            $this->status           = true;
            $prefix                 = DB::getTablePrefix();
            $html                   = "";
            $page                   = (!empty($request->page))?$request->page:1;
            $limit                  = DEFAULT_PAGING_LIMIT;
            $offset                 = ($page-1)*DEFAULT_PAGING_LIMIT;
            $search                 = !empty($request->search)? $request->search : '';
            $language               = \App::getLocale();
            
            if(empty($request->sortby_filter)){
                $sort = "FIELD(project_status,'pending','initiated','closed'), {$prefix}projects.id_project DESC";
            }else{
                $sort = sprintf("%s%s",$prefix,___decodefilter($request->sortby_filter));
            }

            $projects =  \Models\Listings::search_jobs();
            
            if(!empty($request->employment_type_filter)){
                $projects->whereIn('projects.employment',array_filter($request->employment_type_filter));
            }

            if($request->price_min_filter != '' && $request->price_max_filter == ''){
                $projects->havingRaw("(price >= $request->price_min_filter)");
            }else if($request->price_min_filter == '' && $request->price_max_filter != ''){
                $projects->havingRaw("(price <= $request->price_max_filter )");
            }else if($request->price_min_filter != '' && $request->price_max_filter != ''){
                $projects->havingRaw("(price >= $request->price_min_filter AND price <= $request->price_max_filter )");
            }

            if(!empty($request->industry_filter)){
                $projects->when($request->industry_filter,function($q) use ($request){
                    $q->whereHas('industries.industries',function($q) use($request){
                        $q->whereIn('projects_industries.industry_id',$request->industry_filter);
                    });    
                });
            }

            if(!empty($request->skills_filter)){
                $projects->when($request->skills_filter,function($q) use ($request){
                    $q->whereHas('skills.skills',function($q) use($request){
                        $q->whereIn('project_required_skills.skill_id',$request->skills_filter);
                    });    
                });
            }

            if(!empty($request->startdate_filter) && empty($request->enddate_filter)){
                $projects->when($request->startdate_filter,function($q) use ($request,$prefix){
                    $q->whereRaw(sprintf("(DATE({$prefix}projects.startdate) >= '%s')",___convert_date($request->startdate_filter,'MYSQL')));    
                });
            }else if(empty($request->startdate_filter) && !empty($request->enddate_filter)){
                $projects->when($request->startdate_filter,function($q) use ($request,$prefix){
                    $q->whereRaw(sprintf("(DATE({$prefix}projects.enddate) >= '%s')",___convert_date($request->endate_filter,'MYSQL')));    
                });
            }else if(!empty($request->startdate_filter) && !empty($request->enddate_filter)){
                $projects->when($request->startdate_filter,function($q) use ($request,$prefix){
                    $q->whereRaw(sprintf("(DATE({$prefix}projects.startdate) >= '%s' AND DATE({$prefix}projects.enddate) <= '%s')",___convert_date($request->startdate_filter,'MYSQL'),___convert_date($request->enddate_filter,'MYSQL')));    
                });
            }

            if(!empty($request->expertise_filter)){
                $projects->when($request->expertise_filter,function($q) use ($request,$prefix){
                    $q->whereIn("projects.expertise",$request->expertise_filter);
                });
            }

            if(!empty(trim($search))){
                $search = trim($search);
                $projects->havingRaw("(
                    title LIKE '%$search%' 
                    OR
                    description LIKE '%$search%' 
                    OR
                    company_name LIKE '%$search%' 
                    OR
                    expertise LIKE '%$search%' 
                    OR
                    employment LIKE '%$search%' 
                    OR
                    other_perks LIKE '%$search%' 
                    OR
                    price LIKE '%$search%' 
                    OR
                    description LIKE '%$search%' 
                    OR
                    description LIKE '%$search%'
                )");  
            }            

            $projects->where("projects.is_cancelled",DEFAULT_NO_VALUE);
            $projects->whereNotIn("projects.status",['draft','trash']);
            $projects->havingRaw("(
                (awarded = '".DEFAULT_NO_VALUE."')
                OR
                (awarded = '".DEFAULT_NO_VALUE."' AND project_status = 'pending' AND DATE(startdate) >= '".date('Y-m-d')."') 
                OR 
                (project_status IN('pending','initiated','closed','completed'))
            )");
            $projects->groupBy(['projects.id_project']);
            $projects->orderByRaw($sort);

            $jobs = [
                'result'                => $projects->limit($limit)->offset($offset)->get(),
                'total'                 => $projects->get()->count(),
                'total_filtered_result' => $projects->limit($limit)->offset($offset)->get()->count(),
            ];

            // dd($jobs['result']);

            if(!empty($jobs['result']->count())){
                foreach($jobs['result'] as $keys => $project){
                    $html .= get_project_template((object)$project,'talent');
                }
            }else{
                $html .= '<div class="no-records-found">'.trans('website.W0750').'</div>';
            }

            if($jobs['total_filtered_result'] == DEFAULT_PAGING_LIMIT){
                $load_more = '<button type="button" class="btn btn-default btn-block btn-lg" data-request="filter-paginate" data-url="'.url('_search-job').'" data-target="#job_listing" data-showing="#paginate_showing" data-loadmore="#loadmore" data-form="[role=\'find-jobs\']">'.trans('website.W0254').'</button>';
                $can_load_more = true;
            }else{
                $load_more = '<button type="button" class="btn btn-default btn-block btn-lg hide" data-request="filter-paginate" data-url="'.url('_search-job').'" data-target="#job_listing" data-showing="#paginate_showing" data-loadmore="#loadmore" data-form="[role=\'find-jobs\']">'.trans('website.W0254').'</button>';
                $can_load_more = false;
            }
            
            echo json_encode(
                array(
                    "filter_title"      => sprintf(trans('general.M0215'),$jobs['total']),
                    "paging"            => ($request->page == 1)?false:true,
                    "recordsFiltered"   => $jobs['total_filtered_result'],
                    "recordsTotal"      => $jobs['total'],
                    "loadMore"          => $load_more, 
                    "data"              => $html,
                    "can_load_more"     => $can_load_more,        
                )
            );  

        }

        /*url=firstname-lastname/RandomUniqueNumber*/
        public function show_talent_profile(Request $request,$name,$user_id){

            $data['title']          = 'Talent Profile' ;
            $data['header']         = 'innerheader';
            $data['footer']         = 'innerfooter';
            $data['view']           = 'front.pages.talent_profile.view';
            $data['user']           = \Models\Talents::view_talent_profile(___decrypt($user_id));

            return view('front.pages.talent_profile.index')->with($data);
        }

        public function select_profile(Request $request){

            $data['header'] = 'innerheader';
            $data['footer'] = 'innerfooter';
            $data['back']   = '';

            $data['title'] = 'Search Job';
           
            return view('front.pages.user_type_index')->with($data);
        
        }

        public function select_profile_modal(Request $request){

            if($request->ajax()){
                return view('front.pages.select_user_type');
            }
        
        }

        public function select_profile_save(Request $request){
            $redirect = '';
            \DB::table('users')->where('id_user', Auth::user()->id_user)->update(['type'=>$request->type]);
            if($request->type == TALENT_ROLE_TYPE){
                $profile_percentage = \Models\Talents::get_profile_percentage(Auth::user()->id_user);
                
                if($profile_percentage['profile_percentage_count'] < \Cache::get('configuration')['minimum_profile_percentage']){
                    $redirect = sprintf('%s/profile/step/one',TALENT_ROLE_TYPE);
                }else{
                    $redirect = sprintf('%s/find-jobs',TALENT_ROLE_TYPE);
                }
            }
            if($request->type == EMPLOYER_ROLE_TYPE){
                $redirect = sprintf('%s/find-talents',EMPLOYER_ROLE_TYPE);
            }
            $msg = ($request->type=='talent') ? 'Professionals' : 'Client';
            return response()->json([
                'data'      => [],
                'status'    => true,
                'message'   => 'You are successfully registered as '.$msg.'.',
                'redirect'  => $redirect,
            ]); 
        
        }

        /**
         * [This method is used for randering view of finding job] 
         * @param  null
         * @return \Illuminate\Http\Response
         */
        
        public function community_article(Request $request){

            $request->request->add(['currency' => \Session::get('site_currency')]);

            $request['currency']            = \Session::get('site_currency');
            $data['currency']               = \Session::get('site_currency');
            $data['title']                  = trans('website.W0969');
            $data['header']                 = 'innerheader';
            $data['footer']                 = 'innerfooter';
            $data['view']                   = 'front.article.view';

            if(!empty(\Auth::user()) && \Auth::user()->type == "employer"){
                $data['subheader'] = 'employer.includes.top-menu';
            }elseif(!empty(\Auth::user()) && \Auth::user()->type == "talent"){
                $data['subheader'] = 'talent.includes.top-menu';
            }else{
                $data['subheader'] = 'talent.includes.top-menu';
            }

            if(!empty(\Auth::user())){
                $data['user'] = \Models\Talents::get_user(\Auth::user(),true);
                $data['extends'] = 'layouts.talent.main';
            }else{
                $data['user'] = [];
                $data['user']['first_name'] = '';
                $data['extends'] = 'layouts.front.main';
            }

            $data['search']          = (!empty($request->search_article))?$request->search_article:"";
            $data['related_article'] = \Models\Article::related_article();
            
            return view('front.article.list')->with($data);
        }

        /**
         * [This method is used to handle job finding]
         * @param  Request
         * @return Json Response
         */
        
        public function _community_article(Request $request){
            
            $this->status           = true;
            $prefix                 = DB::getTablePrefix();
            $html                   = "";
            $page                   = (!empty($request->page))?$request->page:1;
            $limit                  = DEFAULT_PAGING_LIMIT;
            $offset                 = ($page-1)*DEFAULT_PAGING_LIMIT;
            $search                 = !empty($request->search)? $request->search : '';
            $language               = \App::getLocale();

            $base_url               = ___image_base_url();
            $data['search']         = (!empty($request->search))?$request->search:"";

            $user_id                = !empty(\Auth::user()->id_user) ? \Auth::user()->id_user : 0;

            $article = \Models\Article::leftjoin('users','users.id_user','=','article.id_user')
                        ->leftJoin('files as user_profile',function($leftjoin){
                            $leftjoin->on('user_profile.user_id','=','article.id_user');
                            $leftjoin->on('user_profile.type','=',\DB::Raw('"profile"'));
                        })
                        ->leftJoin('files as article_img',function($leftjoin){
                            $leftjoin->on('article_img.record_id','=','article.article_id');
                            $leftjoin->on('article_img.type','=',\DB::Raw('"article"'));
                        })
                        ->select('article.article_id',
                                'article.id_user',
                                'article.title',
                                'article.description',
                                'users.name',
                                'article.updated as updated_at',
                                \DB::raw('(SELECT COUNT(id_article_answer) FROM '.$prefix.'article_answer WHERE '.$prefix.'article_answer.article_id = '.$prefix.'article.article_id and  '.$prefix.'article_answer.id_parent ="0") AS total_reply'),
                                

                                \DB::Raw("
                                    IF(
                                         {$prefix}user_profile.filename IS NOT NULL,
                                         CONCAT('{$base_url}',{$prefix}user_profile.folder,'thumbnail/',{$prefix}user_profile.filename),(
                                         IF({$prefix}users.social_picture IS NOT NULL OR {$prefix}users.social_picture != '', {$prefix}users.social_picture  ,CONCAT('{$base_url}','images/','".DEFAULT_AVATAR_IMAGE."')))
                                     ) as user_img
                                "),


                                \DB::Raw("
                                        IF(
                                            {$prefix}article_img.filename IS NOT NULL,
                                            CONCAT('{$base_url}',{$prefix}article_img.folder,{$prefix}article_img.filename),
                                            'none'
                                        ) as article_img
                                    "),

                                \DB::Raw("(SELECT count(*) FROM {$prefix}network_user_save WHERE {$prefix}network_user_save.save_user_id = {$prefix}article.id_user and {$prefix}network_user_save.user_id='".$user_id."' and {$prefix}network_user_save.section='user') AS is_following"),

                                'article.type',
                                \DB::Raw("IF(({$prefix}article.type = 'firm'),{$prefix}users.company_name, 'N/A') as firm_name")
                        );

            if(!empty(trim($search))){
                $search = trim($search);
                $article->havingRaw("(
                    title LIKE '%$search%' 
                    OR
                    description LIKE '%$search%'
                )");  
            }

            $article->orderBy('article.article_id','DESC');
            // dd(json_decode(json_encode($article->limit($limit)->offset($offset)->get()),true));

            $article->groupBy('article.article_id')->orderBy('article.article_id', 'ASC');
               $articles = [
                'result'                => $article->limit($limit)->offset($offset)->get(),
                'total'                 => $article->get()->count(),
                'total_filtered_result' => $article->limit($limit)->offset($offset)->get()->count(),
            ];

            if(!empty($articles['result']->count())){
                foreach($articles['result'] as $keys => $articlesdata){
                    $html .= \View::make('front.article.layouts.main')->with(['article' => $articlesdata]);
                }
            }else{
                $html .= '<div class="no-records-found">'.trans('website.W0750').'</div>';
            }

            if($articles['total_filtered_result'] == DEFAULT_PAGING_LIMIT){
                $load_more = '<button type="button" class="btn btn-default btn-block btn-lg" data-request="filter-paginate" data-url="'.url(sprintf('/_mynetworks/_article')).'" data-target="#article_listing" data-showing="#paginate_showing" data-loadmore="#loadmore" data-form="[role=\'find-jobs\']">'.trans('website.W0254').'</button>';
                $can_load_more = true;
            }else{
                $load_more = '<button type="button" class="btn btn-default btn-block btn-lg hide" data-request="filter-paginate" data-url="'.url(sprintf('/_mynetworks/_article')).'" data-target="#article_listing" data-showing="#paginate_showing" data-loadmore="#loadmore" data-form="[role=\'find-jobs\']">'.trans('website.W0254').'</button>';
                $can_load_more = false;
            }
            
            echo json_encode(
                array(
                    "filter_title"      => sprintf(trans('general.M0215'),$articles['total']),
                    "paging"            => ($request->page == 1)?false:true,
                    "recordsFiltered"   => $articles['total_filtered_result'],
                    "recordsTotal"      => $articles['total'],
                    "loadMore"          => $load_more, 
                    "data"              => $html,
                    "can_load_more"     => $can_load_more,
                )
            );            
        }

        /**
         * [This method is used for rendering view of Talent Article add] 
         * @param  null
         * @return \Illuminate\Http\Response
         */
        
        public function articles_add(Request $request){

            $data['header'] = 'innerheader';
            $data['footer'] = 'innerfooter';
            $data['latest_question'] = \Models\Forum::latestQuestion();

            $data['view']      = "front.article.add";

            if(!empty(\Auth::user()) && \Auth::user()->type == "employer"){
                $data['subheader'] = 'employer.includes.top-menu';
            }elseif(!empty(\Auth::user()) && \Auth::user()->type == "talent"){
                $data['subheader'] = 'talent.includes.top-menu';
            }else{
                $data['subheader'] = 'talent.includes.top-menu';
            }

            if(!empty(\Auth::user())){
                $data['user'] = \Models\Talents::get_user(\Auth::user(),true);
                $data['company_profile'] = !empty($data['user']['company_profile'])?$data['user']['company_profile']:'individual';
                $data['extends'] = 'layouts.talent.main';
            }else{
                $data['user'] = [];
                $data['user']['first_name'] = '';
                $data['company_profile'] = 'individual';
                $data['extends'] = 'layouts.front.main';
            }

            return view('front.article.index')->with($data);
        }

        /**
         * [This method is used for showing article detail page] 
         * @param  null
         * @return \Illuminate\Http\Response
         */
        
        public function show_article_details(Request $request, $hashid){

            $data['header'] = 'innerheader';
            $data['footer'] = 'innerfooter';

            $data['page_title'] = 'Article details';
            $data['view']       = 'front.article.detail';

            if(!empty(\Auth::user()) && \Auth::user()->type == "employer"){
                $data['subheader'] = 'employer.includes.top-menu';
            }elseif(!empty(\Auth::user()) && \Auth::user()->type == "talent"){
                $data['subheader'] = 'talent.includes.top-menu';
            }else{
                $data['subheader'] = 'talent.includes.top-menu';
            }

            if(!empty(\Auth::user())){
                $data['user'] = \Models\Talents::get_user(\Auth::user(),true);
                $data['company_profile'] = !empty($data['user']['company_profile'])?$data['user']['company_profile']:'individual';
                $data['extends'] = 'layouts.talent.main';
            }else{
                $data['user'] = [];
                $data['user']['first_name'] = '';
                $data['company_profile'] = 'individual';
                $data['extends'] = 'layouts.front.main';
            }

            /*For article view*/
            $article_view = \Models\Article::countView(___decrypt($hashid));

            $data['id_article'] = ___decrypt($hashid);

            $id_article = ___decrypt($hashid);
            $data['related_article'] = \Models\Article::related_article();

            $data['article'] = \Models\Article::getArticleDetail($id_article);
            // dd($data['article']);

            $data['answer'] = \Models\Article::getAnswerByQuesId($id_article);

            foreach ($data['answer'] as $key => $value) {
                $filename['folder']         = $value->folder;
                $filename['filename']       = $value->filename;
                $value->filename            = get_file_url($filename);

                if(!empty($value->has_child_answer)){
                    foreach ($value->has_child_answer as $ckey => $cvalue) {
                        $filename['folder']         = $cvalue->folder;
                        $filename['filename']       = $cvalue->filename;
                        $cvalue->filename            = get_file_url($filename);
                    }
                }

            }
            
            $data['answer'] = json_decode(json_encode($data['answer']), true);
            // dd($data['answer']);

            return view('front.article.index')->with($data);
        }

        /**
         * [This method is used to article forum answer]
         * @param  Request
         * @return Json Response
         */
        
        public function community_article_add_answer(Request $request, $id){

            $validator = \Validator::make($request->all(), [
                'answer_description'            => ['required']
            ],[
                'answer_description.required'   => 'Enter your comment.'
            ]);

            if($validator->passes()){

                $insertArr = [
                    'article_id'  => $id,
                    'user_id'     => \Auth::user()->id_user,
                    'id_parent'   => $request->id_parent,
                    'answer_desp' => $request->answer_description,
                    'type'        => $request->type,
                    'created'     => date('Y-m-d H:i:s'),
                    'updated'     => date('Y-m-d H:i:s')
                ];
                \Models\Article::saveComment($insertArr);

                $this->status = true;
                $this->message = 'Your comment has been successfully added.';
                $this->redirect = url('network/article/detail/'.___encrypt($id));
            }else{
                $this->jsondata = ___error_sanatizer($validator->errors());
            }

            return response()->json([
                'data'      => $this->jsondata,
                'status'    => $this->status,
                'message'   => $this->message,
                'redirect'  => $this->redirect,
            ]);
        }

        /*Follow user developed by 455*/
        public function followUser(Request $request){

            if(!empty($request['user_id'])){
                $isUpdated = \Models\Article::question_save_user(\Auth::user()->id_user,$request['user_id']);
                if($isUpdated['status']){
                    $this->status   = true;
                    $this->message  = 'Action successful.';
                    $this->jsondata =  $isUpdated['send_text'];              
                }else{
                    $this->status   = false;
                    $this->message  = 'Something went wrong.';
                    $this->jsondata = [];
                }
            }else{
                $this->status   = false;
                $this->message  = 'Something went wrong.';
                $this->jsondata = [];
            }

            return response()->json([
                'data'    => $this->jsondata,
                'status'  => $this->status,
                'message' => $this->message,
                'user_id' => $request['user_id']
            ]);
        }

        /*Follow post developed by 455*/
        public function followPost(Request $request){

            if(!empty($request['post_id'])){
                $isUpdated = \Models\Article::follow_this_article(\Auth::user()->id_user,$request->all());
                if($isUpdated['status']){
                    $this->status   = true;
                    $this->message  = 'Action successful.';
                    $this->jsondata =  $isUpdated['send_text'];              
                }else{
                    $this->status   = false;
                    $this->message  = 'Something went wrong.';
                    $this->jsondata = [];
                }
            }else{
                $this->status   = false;
                $this->message  = 'Something went wrong.';
                $this->jsondata = [];
            }

            return response()->json([
                'data'    => $this->jsondata,
                'status'  => $this->status,
                'message' => $this->message,
            ]);
        }

        /**
         * [This method is used to article add submit]
         * @param  Request
         * @return Json Response
         */
        
        public function articles_add_submit(Request $request){
            $validator = \Validator::make($request->all(), [
                'title'            => ['required'],
                'description'      => ['required']
            ],[
                'title.required'         => trans('general.title_required'),
                'description.required'   => trans('general.description_required')
            ]);

            $validator->after(function($validator) use ($request){

                $desp = strip_tags($request->description); 
                $count = str_word_count($desp);
                if($count > 5000){
                    $validator->errors()->add('description', 'Description cannot be more than 5000 words.' );
                }
            });

            if($validator->passes()){

                $insertArr = [
                    'id_user'     => \Auth::user()->id_user,
                    'title'       => $request->title,
                    'description' => $request->description,
                    'type'        => $request->type,
                    'created'     => date('Y-m-d H:i:s'),
                    'updated'     => date('Y-m-d H:i:s')
                ];
                $article_data = \Models\Article::saveArticle($insertArr);

                if(!empty($request->file_id)){
                    $updateArr = [
                        'record_id'          => $article_data,
                        'updated'            => date('Y-m-d H:i:s')
                    ];
                    $file_data = \Models\File::update_file([$request->file_id],$updateArr);
                }

                $this->status = true;
                $this->message = 'Your article has been successfully added.';
                $this->redirect = url('network/article');
            }else{
                $this->jsondata = ___error_sanatizer($validator->errors());
            }

            return response()->json([
                'data'      => $this->jsondata,
                'status'    => $this->status,
                'message'   => $this->message,
                'redirect'  => $this->redirect,
            ]);
        }

    }