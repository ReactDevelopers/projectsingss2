<?php

    namespace App\Http\Controllers\Api;

    use App\Http\Requests;
    use Illuminate\Http\Request;
    use Illuminate\Validation\Rule;
    use Illuminate\Support\Facades\DB;
    use App\Http\Controllers\Controller;

    use Auth;
    use File;
    use Models\Listings;
    use Models\Industries;

    use Voucherify\VoucherifyClient;
    use Voucherify\ClientException;

    class ApiController extends Controller{
        
        /**
         * Create a new controller instance.
         *
         * @return void
         */
        protected $jwt;
        private $post;
        private $token;
        private $status;
        private $jsondata;
        private $status_code;

        public function __construct(Request $request){
            $this->jsondata = (object)[];
            $this->message = "M0000";
            $this->error_code = "no_error_found";
            $this->status = false;
            $this->status_code = 200;

            $json = json_decode(file_get_contents('php://input'),true);
            if(!empty($json)){
                $this->post = $json;
            }else{
                $this->post = $request->all();
            }

            /*RECORDING API REQUEST IN TABLE*/
            \Models\Listings::record_api_request([
                'url' => $request->url(),
                'request' => json_encode($this->post),
                'type' => 'webservice',
                'created' => date('Y-m-d H:i:s')
            ],$request);
        }

        private function populateresponse($data){
            $data['message'] = (!empty($data['message']))?"":$this->message;
            $data['error'] = trans(sprintf("general.%s",$data['message'])); 
            $data['error_code'] = "";

            if(empty($data['status'])){
                $data['status'] = $this->status;
                $data['error_code'] = $this->message;
            }
            
            $data['status_code'] = $this->status_code;
            
            $data = json_decode(json_encode($data),true);

            array_walk_recursive($data, function(&$item){
                
                if (gettype($item) == 'integer' || gettype($item) == 'float' || gettype($item) == 'NULL'){
                    $item = trim($item);
                }
            });

            if(empty($data['data'])){
                $data['data'] = (object) $data['data'];
            }

            $data['message'] = trans('general.'.$data['message']);
            return $data;
        }

        /**
         * [This method is used for general] 
         * @param  Request
         * @return Json Response
         */

        public function general(Request $request){
            $this->status       = true;
            
            $request['currency'] = $request->currency ? $request->currency : \Session::get('site_currency');
            $language           = \App::getLocale();
            $employment_types   = employment_types('talent_personal_information','',$language);
            $job_interests      = job_interests($language);
            $job_types          = job_types('',$language);
            $salary_range       = salary_range();
            $expertise_levels   = expertise_levels('',$language);
            $passing_year       = passing_year();
            $degree_status      = degree_status('',$language);
            $work_rate          = work_rate($language);
            $skills             = Listings::skills('array',['id_skill','skill_name'],"skill_status = 'active'");
            $degrees            = Listings::degrees('array',['id_degree','degree_name'],"degree_status = 'active'");
            $workfields         = Listings::workfields('array',['id_workfield','field_name'],"field_status = 'active'");
            $certificates       = Listings::certificates('array',['id_cetificate','certificate_name'],"certificate_status = 'active'");
            $colleges           = Listings::colleges('array',['id_college','college_name','image'],"college_status = 'active'");
            $companies          = Listings::companies('array',['id_company','company_name','image'],"company_status = 'active'");
            $job_titles         = Listings::job_titles('array',['id_job_title','job_title_name'],"job_title_status = 'active'");
            $industries         = Industries::allindustries("array"," parent = '0' AND status = 'active' ",['id_industry',\DB::Raw("IF(({$language} != ''),`{$language}`, `en`) as name"),'parent'],'');
            $subindustries      = Industries::allindustries("array"," parent != '0' AND status = 'active' ",['id_industry',\DB::Raw("IF(({$language} != ''),`{$language}`, `en`) as name"),'parent']);
            $countries          = Listings::countries('array',['id_country', 'iso_code', 'phone_country_code', \DB::Raw("IF(({$language} != ''),`{$language}`, `en`) as country_name"), 'contains_states'], "status = 'active'");
            $languages          = Listings::languages(['language_name','language_code']);
            $card_type          = Listings::card_type("array","status='active'",['id','type', 'name','image as image_url']);
            $dispute_concern    = \Models\DisputeConcern::select(['id_concern',\DB::Raw("IF(({$language} != ''),`{$language}`, `en`) as concern")])->whereNotIn('status',['trashed'])->get();

            $currencies         = \Models\Currency::getCurrencyList(['currencies.iso_code','currencies.sign','currencies.default_currency']);
            $ios_version        = ___configuration(['ios_version','update_version_message']);
            $android_version    = ___configuration(['android_version','update_version_message']);
            $follow_link        = ___configuration([
                'social_facebook_url',
                'social_twitter_url',
                'social_linkedin_url',
                'social_instagram_url',
                'social_googleplus_url',
            ]);

            $follow_link            = array_map('___http', $follow_link);
            $configuration          = ___configuration(['office_address','contact_number','info_email','is_language_enabled','paypal_commission','paypal_commission_flat']); 
            $range_filter           = range_filter($request->currency);
            $currency_exchange_url  = url('currency-exchange');
            $hire_send_message_to_talent  = trans('website.W0836');


            $this->jsondata = [
                'employment_types'              => $employment_types,
                'job_interests'                 => $job_interests,
                'job_types'                     => $job_types,
                'salary_range'                  => $salary_range,
                'expertise_levels'              => $expertise_levels,
                'countries'                     => $countries,
                'industries'                    => $industries,
                'subindustries'                 => $subindustries,
                'passing_year'                  => $passing_year,
                'degree_status'                 => $degree_status,
                'work_rate'                     => $work_rate,
                'degrees'                       => $degrees,
                'skills'                        => $skills,
                'certificates'                  => $certificates,
                'colleges'                      => $colleges,
                'companies'                     => $companies,
                'dispute_concern'               => $dispute_concern,
                'job_titles'                    => $job_titles,
                'workfields'                    => $workfields,
                'follow_link'                   => $follow_link,
                'staticpages'                   => [
                    'about'                     => 'page/about',
                    'terms-and-conditions'      => 'page/terms-and-conditions',
                    'privacy-policy'            => 'page/privacy-policy',
                    'faq'                       => 'page/community',
                ],
                'ios_version'                   => $ios_version,
                'android_version'               => $android_version,
                'office_address'                => $configuration['office_address'],   
                'contact_number'                => $configuration['contact_number'],   
                'info_email'                    => $configuration['info_email'],   
                'submission_fee'                => SUBMISSION_FEE,   
                'talent_filter'                 => ___filter('talent_sorting_filter'),
                'job_filter'                    => ___filter('job_sorting_filter'),
                'range_filter'                  => $range_filter,
                'proposal_sorting'              => ___filter('proposal_sorting'),
                'proposal_filter'               => ___filter('proposal_filter'),
                'wallet_sorting'                => ___filter('proposal_sorting'),
                'payment_sorting'               => ___filter('proposal_sorting'),
                'premium_filter'                => ___filter('premium_filter'),
                'language'                      => $languages,
                'card_type'                     => $card_type,
                'currency'                      => $currencies,
                'is_language_enabled'           => $configuration['is_language_enabled'],
                'currency_exchange_url'         => $currency_exchange_url,
                'paypal_commission'             => $configuration['paypal_commission'],
                'paypal_commission_flat'        => $configuration['paypal_commission_flat'],
                'hire_send_message_to_talent'   => $hire_send_message_to_talent,
            ]; 

            return response()->json(
                $this->populateresponse([
                    'status' => $this->status,
                    'data' => $this->jsondata
                ])
            );
        }

        /**
         * [This method is used for login] 
         * @param  Request
         * @return Json Response
         */

        public function login(Request $request){
            $request->replace($this->post);

            $validate = \Validator::make($request->all(), [
                'email'    => 'required|email|max:255',
                'password' => 'required',
            ],[
                'email.required'    => 'M0010',
                'email.email'       => 'M0011',
                'password.required' => 'M0013'
            ]);

            if($validate->fails()){
                $this->message = $validate->messages()->first();
            }else {
                $result = \Models\Users::findByEmail($this->post['email'],['id_user','password','type','first_name','last_name','name','email','status','api_token','chat_status','is_subscribed','latitude','longitude','currency','social_account','company_profile','company_name']);
                $match = \Hash::check($this->post['password'], $result['password']);

                /*$token = Auth::attempt(['email' => $this->post['email'], 'password' => $this->post['password'], 'id_user' => $request['id_user']]);*/
                if(!empty($match)){
                    if(!empty($result)){
                        if($result['status'] == 'pending'){
                            $this->message = 'M0046';
                            $this->jsondata = [
                                'type'          => 'confirm',
                                'title'         => 'M0043',
                                'messages'      => 'M0046',
                                'button_one'    => 'M0044',
                                'button_two'    => 'M0045',
                                'token'         => ''
                            ];
                        }else if($result['status'] == 'inactive'){
                            $this->message = 'M0002';
                            $this->jsondata = [
                                'type'          => 'alert',
                                'title'         => 'M0026',
                                'messages'      => 'M0002',
                                'button'        => 'M0027',
                                'token'         => ''
                            ];
                        }else if($result['status'] == 'suspended'){
                            $this->message = 'M0003';
                            $this->jsondata = [
                                'type'          => 'alert',
                                'title'         => 'M0026',
                                'messages'      => 'M0003',
                                'button'        => 'M0027',
                                'token'         => ''
                            ];
                        }else if($result['status'] == 'trashed'){
                            $this->message = 'M0004';
                            $this->jsondata = [
                                'type'          => 'alert',
                                'title'         => 'M0026',
                                'messages'      => 'M0004',
                                'button'        => 'M0027',
                                'token'         => ''
                            ];
                        }else{
                            $device_uuid      = @(string)$this->post['device_uuid'];
                            $device_token     = @(string)$this->post['device_token'];
                            $device_type      = @(string)$this->post['device_type'];
                            $device_name      = @(string)$this->post['device_name'];
                            $latitude         = @(string)$this->post['latitude'];
                            $longitude        = @(string)$this->post['longitude'];
                            $thumb_configured = \Models\ThumbDevices::is_device_configured($result["id_user"],$device_uuid);
                            
                            if(!empty($device_uuid) && $thumb_configured == DEFAULT_NO_VALUE){
                                \Models\ThumbDevices::remove_touch_login($device_uuid);
                            }

                            $this->message    = 'M0000';
                            $this->status     = true;
                            $this->jsondata   = self::__dologin($result,$device_token,$device_type,$device_name,$latitude,$longitude,$device_uuid);
                        }
                    }else{
                        $this->message = 'M0004';
                        $this->jsondata = [
                            'type'          => 'alert',
                            'title'         => 'M0026',
                            'messages'      => 'M0004',
                            'button'        => 'M0027',
                            'token'         => ''
                        ];
                    }
                }else{
                    $this->message = 'M0004';
                    $this->jsondata = [
                        'type'          => 'alert',
                        'title'         => 'M0026',
                        'messages'      => 'M0004',
                        'button'        => 'M0027',
                        'token'         => ''
                    ];
                }
            }

            return response()->json(
                $this->populateresponse([
                    'status' => $this->status,
                    'data' => $this->jsondata
                ])
            );            
        }

        /**
         * [This method is used for logout] 
         * @param  Request
         * @return Json Response
         */

        public function logout(Request $request){
            $request->replace($this->post);

            $device_token   = (string) trim($request->device_token);
            $this->status   = true;
            
            if(!empty($request->device_token)){
                $isDeviceRemoved = \Models\Devices::remove($request->id_user,$request->device_token);
            }

            return response()->json(
                $this->populateresponse([
                    'status' => $this->status,
                    'data' => $this->jsondata
                ])
            );     
        }

        /**
         * [This method is used for touch login] 
         * @param  Request
         * @return Json Response
         */

        public function touch_login(Request $request){
            $request->replace($this->post);
            
            $validator = \Validator::make($request->all(), [
                'device_uuid'           => validation('touch_login'),
                'device_type'           => validation('touch_login'),
            ],[
                'device_uuid.required'  => 'M0429',
                'device_type.required'  => 'M0429',
            ]);

            if($validator->fails()){
                $this->message = $validator->messages()->first();
            }else{
                $device_uuid      = @(string)$this->post['device_uuid'];
                $device_token     = @(string)$this->post['device_token'];
                $device_type      = @(string)$this->post['device_type'];
                $device_name      = @(string)$this->post['device_name'];
                $latitude         = @(string)$this->post['latitude'];
                $longitude        = @(string)$this->post['longitude'];
                
                $isDeviceConfigured =\Models\ThumbDevices::findById($device_uuid, $device_type);
                
                if(!empty($isDeviceConfigured)){
                    $result = \Models\Users::findById($isDeviceConfigured->user_id,['id_user','password','type','first_name','last_name','name','email','status','api_token','chat_status','is_subscribed','latitude','longitude','currency','social_account']);


                    $this->message    = 'M0000';
                    $this->status     = true;
                    $this->jsondata   = self::__dologin($result,$device_token,$device_type,$device_name,$latitude,$longitude,$device_uuid);
                }else{
                    $this->status   = false;
                    $this->message  = 'M0429';
                }

            }

            return response()->json(
                $this->populateresponse([
                    'status'    => $this->status,
                    'data'      => $this->jsondata
                ])
            );          
        }

        /**
         * [This method is used for user's signup] 
         * @param  Request
         * @return Json Response
         */

        public function talentsignup(Request $request){
            $request->replace($this->post);
            // dd($request->all(),'zzz',(string) $request->response['pictureUrl']);
            $newsletter_acceptance  = ($request->newsletter_acceptance)?$request->newsletter_acceptance:'no';
            $field                  = ['id_user','password','type','first_name','last_name','name','email','status','api_token','chat_status','is_subscribed','latitude','longitude','currency','chat_status',\DB::raw("'yes' as agree"),\DB::raw("'{$newsletter_acceptance}' as newsletter_acceptance"),'social_account','social_picture'];
            $email                  = (!empty($this->post['email']))?$this->post['email']:"";
            $social_id              = ''; 
            $social_key             = '';
            
            if(!empty($this->post['social_id']) && !empty($this->post['social_key'])){
                $social_id      = (string) trim($this->post['social_id']);
                $social_key     = (string) trim($this->post['social_key']);
            }

            if(!empty($this->post['device_name']) && !empty($this->post['device_type']) && !empty($this->post['device_token'])){
                $device_name    = (string) trim($this->post['device_name']);
                $device_type    = (string) trim($this->post['device_type']);
                $device_token   = (string) trim($this->post['device_token']);
            }
            
            if(!empty($social_key) && !empty($social_id) && !empty($email)){
                $result         = (array) \Models\Talents::findByEmail(trim($email),$field);
            }

            if(empty($result) && !empty($social_key) && !empty($social_id)){
                $result         = (array) \Models\Talents::findBySocialId($social_key,$social_id,$field);
            }

            if(empty($result)){
                if(!empty($this->post['social_key'])){
                    $this->message = self::validate_social_signup($request);
                }else{
                    $this->message = self::validate_normal_signup($request);
                }

                if($this->message === false){
                    if(!empty($email)){
                        $result = (array) \Models\Talents::findByEmail($email,$field);
                    }

                    if(!empty($result['email']) && !empty($email) && ($result['email'] != $email)){
                        $this->message = 'M0039';
                    }else if(!empty($result) && !empty($this->post['mobile']) && $result['mobile'] != $this->post['mobile']){
                        $this->message = 'M0039';
                    }else if(!empty($result['mobile']) && !empty($social_id)){
                        if($result['status'] == 'inactive'){
                            $this->message = 'M0002';
                        }elseif($result['status'] == 'suspended'){
                            $this->message = "M0003";
                        }else{
                            $updated_data = array(
                                $social_key     => $social_id,
                                'email'         => $email,
                                'status'        => 'active'
                            );

                            \Models\Talents::change($result['id_user'],$updated_data);
                            $this->jsondata = \Models\Talents::findById($result['id_user'],$field);
                            $this->jsondata->action = 'login';

                            $this->message = 'M0000';
                        }
                    }else{
                        /*if(empty($request->first_name)){
                            $this->message = 'M0569';
                        }else*/{
                            if($request->type == NONE_ROLE_TYPE){
                                // dd($request->all(),'zzz',(string) $request->response['pictureUrl']);
                                $dosignup = \Models\Talents::__dosignupnone((object)$request->all());
                                if($request->work_type == 'company'){
                                    $talentcompanydata['company_name'] = $request->company_name;
                                    $talentcompanydata['created'] = date('Y-m-d H:i:s');
                                    $talentcompanydata['updated'] = date('Y-m-d H:i:s');
                                    $isTalentCompanydId      = \Models\TalentCompany::saveTalentCompany($talentcompanydata);
                                    // dd($isTalentCompanydId );
                                    $isCreated = \DB::table('company_connected_talent')->insert(['id_talent_company'=>$isTalentCompanydId,'id_user'=>$dosignup['signup_user_id'],'user_type'=>'owner','updated'=> date('Y-m-d H:i:s'),'created'=> date('Y-m-d H:i:s')]);
                                }
                                //Save Voucherify response
                                if($request->coupon_id != 0){
                                    $coupon_response = [
                                        'user_id'=> $dosignup['signup_user_id'],
                                        'coupon_id'=>$request->coupon_id,
                                        'response_json' => json_encode($request->api_coupon_response),
                                        'created' => date('Y-m-d H:i:s')
                                    ];
                                    $isInserted = \DB::table('api_coupon_response')->insert($coupon_response);
                                }
                            
                                if(!empty($dosignup['status'])){
                                    $this->jsondata = \Models\Talents::findById($dosignup['signup_user_id'],$field);
                                    $this->status = $dosignup['status'];

                                    if(!empty($this->jsondata) && $this->jsondata->status == 'pending'){
                                        $this->jsondata->action = 'signup';
                                        $this->message = $dosignup['message'];
                                        
                                        if(!empty($email)){
                                            $code                   = bcrypt(__random_string());
                                            $emailData              = ___email_settings();
                                            $emailData['email']     = $email;
                                            $emailData['name']      = $this->post['first_name'];
                                            $emailData['link']      = url(sprintf("activate/account?token=%s",$code));
                                            
                                            \Models\Talents::change($this->jsondata->id_user,['remember_token' => $code,'updated' => date('Y-m-d H:i:s')]);

                                            ___mail_sender($email,sprintf("%s %s",$this->post['first_name'],$this->post['last_name']),"talent_signup_verification",$emailData);
                                        }
                                    }else{
                                        $this->message = 'M0000';
                                        $this->jsondata->action = 'login';

                                        /*if(!empty($email)){
                                            $emailData              = ___email_settings();
                                            $emailData['email']     = $email;
                                            $emailData['name']      = $this->post['first_name'];

                                            ___mail_sender($email,sprintf("%s %s",$this->post['first_name'],$this->post['last_name']),"talent_signup",$emailData);
                                        }*/
                                    }
                                }else{
                                    $this->message = $dosignup['message'];
                                }
                            }else if($request->type == EMPLOYER_ROLE_TYPE){
                                $dosignup = \Models\Employers::__dosignupnone((object)$request->all());
                
                                if(!empty($dosignup['status'])){
                                    $field          = ['id_user','type','first_name','last_name','name','email','status','company_name',\DB::raw("'yes' as agree")];
                                    $this->jsondata = \Models\Employers::findById($dosignup['signup_user_id'],$field);
                                    
                                
                                    $this->status = $dosignup['status'];

                                    if(!empty($this->jsondata) && $this->jsondata->status == 'pending'){
                                        $this->jsondata->action = 'signup';
                                        $this->message = $dosignup['message'];
                                        
                                        if(!empty($email)){
                                            $code                   = bcrypt(__random_string());
                                            $emailData              = ___email_settings();
                                            $emailData['email']     = $this->post['email'];
                                            $emailData['name']      = $this->post['first_name'];
                                            $emailData['link']      = url(sprintf("activate/account?token=%s",$code));

                                            \Models\Employers::change($dosignup['signup_user_id'],['remember_token' => $code,'updated' => date('Y-m-d H:i:s')]);
                                            
                                            ___mail_sender($this->post['email'],sprintf("%s %s",$this->post['first_name'],$this->post['last_name']),"employer_signup",$emailData);
                                        }
                                    }else{
                                        $this->message = 'M0000';
                                        $this->jsondata->action = 'login';
                                    }
                                }else{
                                    $this->message = $dosignup['message'];
                                }
                            }else{
                                $this->jsondata->action = 'signup';
                                $this->message = 'M0593';
                            }
                        }
                    }
                }else if($this->message == 'M0039'){
                    $this->jsondata->action = 'signup';
                }else{
                    $this->jsondata->action = 'signup';
                }
            }else/* if($result['type'] == TALENT_ROLE_TYPE)*/{
                if($result['status'] == 'inactive'){
                    $this->message = "M0002";
                }elseif($result['status'] == 'suspended'){
                    $this->message = "M0003";
                }else{
                    $updated_data = array(
                        $social_key     => $social_id,
                        'status'        => 'active'
                    );

                    if(empty($result['email'])){
                        $updated_data['email'] = $email;
                    }

                    \Models\Talents::change($result['id_user'],$updated_data);
                    
                    $this->jsondata = \Models\Talents::findById($result['id_user'],$field);
                    $this->jsondata->action = 'login';
                    $this->message = 'M0000';
                    $this->status = true;
                }
            }/*else{
                $this->message = 'M0108';
            }*/

            if(!empty($this->jsondata->action) && $this->jsondata->action === 'login'){
                $user_rating = \Models\Users::getUserRating($this->jsondata->id_user);
                $device_uuid                                        = @(string)trim($this->post['device_uuid']);
                $this->jsondata->thumb_device_configured            = \Models\ThumbDevices::is_device_configured($this->jsondata->id_user,$device_uuid);
                $this->jsondata->rating                             = $user_rating->rating;
                $this->jsondata->review                             = $user_rating->review;
                
                if(!empty($this->jsondata->currency)){
                    $this->jsondata->currency_sign                      = \Cache::get('currencies')[$this->jsondata->currency];
                }

                // $this->jsondata->picture                            = get_file_url(\Models\Users::get_file(sprintf(" type = 'profile' AND user_id = %s",$this->jsondata->id_user),'single',['filename','folder']));

                $profileUrl = \Models\Users::get_file(sprintf(" type = 'profile' AND user_id = %s",$this->jsondata->id_user),'single',['filename','folder']);


                if(empty($profileUrl) && empty($this->jsondata->social_picture)){
                    $this->jsondata->picture    = get_file_url($profileUrl);
                }elseif (!empty($profileUrl)) {
                    $this->jsondata->picture    = get_file_url($profileUrl);
                }elseif (!empty($this->jsondata->social_picture)) {
                    $this->jsondata->picture    = $this->jsondata->social_picture;
                }


                $this->jsondata->proposal_count                     = \Models\Notifications::unread_notifications($this->jsondata->id_user,'proposals',$this->jsondata->type);
                $this->jsondata->notification_count                 = \Models\Notifications::unread_notifications($this->jsondata->id_user);
                
                /* SENDER INFORMATION */
                $this->jsondata->sender                             = trim(sprintf("%s %s",$this->jsondata->first_name,$this->jsondata->last_name));
                $this->jsondata->sender_id                          = $this->jsondata->id_user;
                $this->jsondata->sender_picture                     = $this->jsondata->picture;
                //$this->jsondata->chat_status                        = $this->jsondata->chat_status;
                $this->jsondata->sender_profile_link                = "";

                if($this->jsondata->type == TALENT_ROLE_TYPE){
                    $this->jsondata->sender_profile_link          = url(sprintf('%s/find-talents/profile?talent_id=%s',EMPLOYER_ROLE_TYPE,___encrypt($this->jsondata->id_user)));
                }

                $percentage =[];
                if($this->jsondata->type == TALENT_ROLE_TYPE){
                    $userData   = \Models\Talents::get_user((object)['id_user' => $this->jsondata->id_user],true);
                    $percentage = (array)\Models\Talents::get_profile_percentage($this->jsondata->id_user);
                }else if($this->jsondata->type == EMPLOYER_ROLE_TYPE){
                    $userData   = \Models\Employers::get_user((object)['id_user' => $this->jsondata->id_user]);
                    $percentage = (array)\Models\Employers::get_profile_percentage($this->jsondata->id_user);
                }
                // dd($percentage);
                // if(!empty($percentage)){
                    $this->jsondata = (object)array_merge($percentage,json_decode(json_encode($this->jsondata),true));
                // }
        
                $api_token = bcrypt(__random_string());
                \Models\Users::change($this->jsondata->id_user,['api_token' => $api_token,'updated' => date('Y-m-d H:i:s')]);
            
                $this->jsondata->_token = $api_token;
                unset($this->jsondata->api_token);
            }
            
            if(!empty($result['id_user']) && !empty($device_token) && !empty($device_name) && !empty($device_type)){
                \Models\Devices::add(
                    $result['id_user'],
                    $device_token,
                    $device_name,
                    $device_type
                );
            }

            if(!empty($this->jsondata->id_user) && (!empty($this->post['latitude']) && !empty($this->post['longitude']))){
                $update = [
                    "latitude"  => $this->post['latitude'],
                    "longitude" => $this->post['longitude']
                ];

                $istalentUpdated = \Models\Talents::change($this->jsondata->id_user,$update);
                
                if(!empty($istalentUpdated)){
                    $this->jsondata->latitude     = $this->post['latitude'];
                    $this->jsondata->longitude    = $this->post['longitude'];
                }
            }            

            /*
                \Models\Talents::record_consumer_activity(array('user_action' => 'login','user_id' => $result['id'],'activity_status' => 'success'));
            */
            
            $jsondata = json_decode(json_encode($this->jsondata),true);
            
            if(!empty($jsondata)){
                $this->jsondata = $jsondata;
            }

            return response()->json(
                $this->populateresponse([
                    'status' => $this->status,
                    'data' => $this->jsondata
                ])
            );            
        }

        /**
         * [This method is used for validating social signup] 
         * @param  Request
         * @return \Illuminate\Http\Response
         */

        public function validate_social_signup($request){
            $message = false;

            $validate = \Validator::make($request->all(), [
                'first_name'        => validation('first_name'),
                'last_name'         => validation('last_name'),
                'email'             => ['required','email',Rule::unique('users')->ignore('trashed','status')],
            ],[
                'first_name.required'       => 'M0006',
                'first_name.regex'          => 'M0007',
                'first_name.string'         => 'M0007',
                'first_name.max'            => 'M0020',
                'last_name.required'        => 'M0008',
                'last_name.regex'           => 'M0009',
                'last_name.string'          => 'M0009',
                'last_name.max'             => 'M0019',
                'email.required'            => 'M0010',
                'email.email'               => 'M0011',
                'email.unique'              => 'M0012',
            ]);

            if($validate->fails()){
                $message = $validate->messages()->first();
            }

            return $message;
        }

        /**
         * [This method is used for validating normal signup] 
         * @param  Request
         * @return \Illuminate\Http\Response
         */

        public function validate_normal_signup($request){
            $message = false;

            $validate = \Validator::make($request->all(), [
                'first_name'        => validation('first_name'),
                'last_name'         => validation('last_name'),
                'email'             => ['required','email',Rule::unique('users')->ignore('trashed','status')],
                'password'          => validation('password'),
                'agree'             => validation('agree'),
                // 'coupon_code'       => validation('coupon_code'),
            ],[
                'first_name.required'       => 'M0006',
                'first_name.regex'          => 'M0007',
                'first_name.string'         => 'M0007',
                'first_name.max'            => 'M0020',
                'last_name.required'        => 'M0008',
                'last_name.regex'           => 'M0009',
                'last_name.string'          => 'M0009',
                'last_name.max'             => 'M0019',
                'email.required'            => 'M0010',
                'email.email'               => 'M0011',
                'email.unique'              => 'M0012',
                'password.required'         => 'M0013',
                'password.regex'            => 'M0014',
                'password.string'           => 'M0013',
                'password.min'              => 'M0014',
                'password.max'              => 'M0018',
                'agree.required'            => 'M0017',
                // 'coupon_code.exists'        => 'M0596',
            ]);


            // $validate->after(function($v) use($request, $validate) {

                // if($request->input('coupon_code') != ""){

                //     $set_coupon_code_id = 0;

                //     $apiID  = "2b259621-848b-40c2-a3d4-3c47874b2fe8";
                //     $apiKey = "6021de2b-b5d5-4ee1-a659-b88c84b26826";
                //     $client = new VoucherifyClient($apiID, $apiKey);

                //     try{
                //         $get_voucher = $client->vouchers->get($request->input('coupon_code'));
                //         $validate_voucher =$client->validations->validateVoucher($request->input('coupon_code'));
                //     }catch(ClientException $exception){
                //         $validate->errors()->add('coupon_code', 'M0597');
                //     }

                //     if(!empty($validate_voucher) && $validate_voucher->valid == true){

                //         try{
                //             $redeem_voucher = $client->redemptions->redeem($request->input('coupon_code'));
                //             $request->request->add(['api_coupon_response' => $redeem_voucher]);
                //             $coupon_code_id = \DB::table('coupon')->select('id')->where('code','=',$request->input('coupon_code'))->first();
                //             $set_coupon_code_id = $coupon_code_id->id;
                //             $request->request->add(['coupon_id' => $set_coupon_code_id]);
                //         }catch(ClientException $exception){
                //             $validate->errors()->add('coupon_code', 'M0598');
                //         }
                //     }
                // }                
            // });   

            if($validate->fails()){
                $message = $validate->messages()->first();
            }

            return $message;
        }

        /**
         * [This method is used for user's signup editing] 
         * @param  Request
         * @return Json Response
         */

        public function edit_talentsignup(Request $request){
            $request->replace($this->post);

            $newsletter_acceptance  = ($request->newsletter_acceptance)?$request->newsletter_acceptance:'no';
            $field          = ['id_user','type','first_name','last_name','name','email','status',\DB::raw("'yes' as agree"),\DB::raw("'{$newsletter_acceptance}' as newsletter_acceptance")];

            $validate = \Validator::make($request->all(), [
                'first_name'        => validation('first_name'),
                'last_name'         => validation('last_name'),
                'email'             => ['required','email',Rule::unique('users')->ignore('trashed','status')->where(function($query) use($request){$query->where('id_user','!=',$request->id_user);})],
                'password'          => validation('password'),
                'agree'             => validation('agree'),
            ],[
                'first_name.required'       => 'M0006',
                'first_name.regex'          => 'M0007',
                'first_name.string'         => 'M0007',
                'first_name.max'            => 'M0020',
                'last_name.required'        => 'M0008',
                'last_name.regex'           => 'M0009',
                'last_name.string'          => 'M0009',
                'last_name.max'             => 'M0019',
                'email.required'            => 'M0010',
                'email.email'               => 'M0011',
                'email.unique'              => 'M0047',
                'password.required'         => 'M0013',
                'password.regex'            => 'M0014',
                'password.string'           => 'M0013',
                'password.min'              => 'M0014',
                'password.max'              => 'M0018',
                'agree.required'            => 'M0017',
            ]);

            if($validate->fails()){
                $this->message = $validate->messages()->first();
            }else{
                $this->jsondata = \Models\Users::findById($request->id_user,['id_user']);
                
                if(!empty($this->jsondata)){
                    $code                   = bcrypt(__random_string());
                    $emailData              = ___email_settings();
                    $emailData['email']     = $this->post['email'];
                    $emailData['name']      = $this->post['first_name'];
                    $emailData['link']      = url(sprintf("activate/account?token=%s",$code));
                    
                    \Models\Users::change($this->jsondata['id_user'],[
                        'first_name'        => $this->post['first_name'],
                        'last_name'         => $this->post['last_name'],
                        'email'             => $this->post['email'],
                        'password'          => bcrypt($this->post['password']),
                        'remember_token'    => $code,
                        'status'            => 'pending',
                        'updated'           => date('Y-m-d H:i:s')
                    ]);

                    /* RECORDING ACTIVITY LOG */
                    event(new \App\Events\Activity([
                        'user_id'           => $request->id_user,
                        'user_type'         => 'talent',
                        'action'            => 'webservice-talent-update-signup',
                        'reference_type'    => 'users',
                        'reference_id'      => $request->id_user
                    ]));

                    $this->jsondata = \Models\Users::findById($request->id_user,$field);
                    
                    if(!empty($this->jsondata)){
                        $this->jsondata['action'] = 'signup';
                    }

                    $this->status = true;
                    $this->message = 'M0021';
                    ___mail_sender($this->post['email'],sprintf("%s %s",$this->post['first_name'],$this->post['last_name']),"talent_signup_verification",$emailData);
                }else{
                    $this->message = 'M0028';
                }
            }
            
            $jsondata = json_decode(json_encode($this->jsondata),true);
            
            if(!empty($jsondata)){
                $this->jsondata = $jsondata;
            }

            return response()->json(
                $this->populateresponse([
                    'status' => $this->status,
                    'data' => $this->jsondata
                ])
            );            
        }

        /**
         * [This method is used to handle user's sign up] 
         * @param Request
         * @return Json Response
         */

        public function _talentsignup(Request $request){
            $request->replace($this->post);

            $validate = \Validator::make($request->all(), [
                'first_name'        => validation('first_name'),
                'last_name'         => validation('last_name'),
                'email'             => ['required','email',Rule::unique('users')->ignore('trashed','status')],
                'password'          => validation('password'),
                'agree'             => validation('agree'),
            ],[
                'first_name.required'       => 'M0006',
                'first_name.regex'          => 'M0007',
                'first_name.string'         => 'M0007',
                'first_name.max'            => 'M0020',
                'last_name.required'        => 'M0008',
                'last_name.regex'           => 'M0009',
                'last_name.string'          => 'M0009',
                'last_name.max'             => 'M0019',
                'email.required'            => 'M0010',
                'email.email'               => 'M0011',
                'email.unique'              => 'M0012',
                'password.required'         => 'M0013',
                'password.regex'            => 'M0014',
                'password.string'           => 'M0013',
                'password.min'              => 'M0014',
                'password.max'              => 'M0018',
                'agree.required'            => 'M0017',
            ]);

            if($validate->fails()){
                $this->message = $validate->messages()->first();
            }else {
                $dosignup = \Models\Talents::__dosignup((object)$request->all());

                /* RECORDING ACTIVITY LOG */
                event(new \App\Events\Activity([
                    'user_id'           => $dosignup['signup_user_id'],
                    'user_type'         => 'talent',
                    'action'            => 'webservice-talent-signup',
                    'reference_type'    => 'users',
                    'reference_id'      => $dosignup['signup_user_id'],
                ]));

                if(!empty($dosignup['status'])){
                    $this->jsondata = [
                        'email' => $this->post['email'],
                        'signup_user_id' => $dosignup['signup_user_id']
                    ];
                    $this->message = $dosignup['message'];
                    $this->status = $dosignup['status'];

                    if(!empty($this->post)){
                        $emailData              = ___email_settings();
                        $emailData['email']     = $this->post['email'];
                        $emailData['name']      = $this->post['first_name'];

                        ___mail_sender($this->post['email'],sprintf("%s %s",$this->post['first_name'],$this->post['last_name']),"talent_signup",$emailData);
                    }
                }else{
                    $this->message = $dosignup['message'];
                }
            }

            return response()->json(
                $this->populateresponse([
                    'status' => $this->status,
                    'data' => $this->jsondata
                ])
            );            
        }

        /**
         * [This method is used for employer signup] 
         * @param  Request
         * @return Json SResponse
         */

        public function employersignup(Request $request){
            $request->replace($this->post);

            $validate = \Validator::make($request->all(), [
                'first_name'        => validation('first_name'),
                'last_name'         => validation('last_name'),
                /*'company_name'      => array_merge(validation('company_name'),[
                    Rule::unique('users')
                    ->ignore('trashed','status')
                ]),*/
                'email'             => ['required','email',Rule::unique('users')->ignore('trashed','status')],
                'password'          => validation('password'),
                'agree'             => validation('agree'),
            ],[
                'first_name.required'       => 'M0006',
                'first_name.regex'          => 'M0007',
                'first_name.string'         => 'M0007',
                'first_name.max'            => 'M0020',
                'last_name.required'        => 'M0008',
                'last_name.regex'           => 'M0009',
                'last_name.string'          => 'M0009',
                'last_name.max'             => 'M0019',
                'company_name.required'     => 'M0023',
                'company_name.regex'        => 'M0024',
                'company_name.string'       => 'M0024',
                'company_name.max'          => 'M0025',
                'company_name.unique'       => 'M0556',
                'email.required'            => 'M0010',
                'email.email'               => 'M0011',
                'email.unique'              => 'M0012',
                'password.required'         => 'M0013',
                'password.regex'            => 'M0014',
                'password.string'           => 'M0013',
                'password.min'              => 'M0014',
                'password.max'              => 'M0018',
                'agree.required'            => 'M0017',
            ]);

            if($validate->fails()){
                $this->message = $validate->messages()->first();
            }else {
                $dosignup = \Models\Employers::__dosignup((object)$request->all());
                
                if(!empty($dosignup['status'])){
                    $newsletter_acceptance = ($request->newsletter_acceptance)?$request->newsletter_acceptance:'no';
                    $field          = ['id_user','type','first_name','last_name','name','email','status','company_name',\DB::raw("'yes' as agree"),\DB::raw("'{$newsletter_acceptance}' as newsletter_acceptance")];
                    $this->jsondata = \Models\Employers::findById($dosignup['signup_user_id'],$field);
                    
                    if(!empty($this->jsondata)){
                        $this->jsondata->action = 'signup';
                    }

                    $this->message = $dosignup['message'];
                    $this->status = $dosignup['status'];

                    if(!empty($this->post)){
                        $code                   = bcrypt(__random_string());
                        $emailData              = ___email_settings();
                        $emailData['email']     = $this->post['email'];
                        $emailData['name']      = $this->post['first_name'];
                        $emailData['link']      = url(sprintf("activate/account?token=%s",$code));
                        
                        \Models\Employers::change($dosignup['signup_user_id'],['remember_token' => $code,'updated' => date('Y-m-d H:i:s')]);

                        /* RECORDING ACTIVITY LOG */
                        event(new \App\Events\Activity([
                            'user_id'           => $dosignup['signup_user_id'],
                            'user_type'         => 'employer',
                            'action'            => 'webservice-employer-signup',
                            'reference_type'    => 'users',
                            'reference_id'      => $dosignup['signup_user_id']
                        ]));

                        ___mail_sender($this->post['email'],sprintf("%s %s",$this->post['first_name'],$this->post['last_name']),"employer_signup",$emailData);
                    }
                }else{
                    $this->message = $dosignup['message'];
                }

                if(!empty($result['id']) && !empty($this->post['device_name']) && !empty($this->post['device_type']) && !empty($this->post['device_token'])){
                    $device_name    = (string) trim($this->post['device_name']);
                    $device_type    = (string) trim($this->post['device_type']);
                    $device_token   = (string) trim($this->post['device_token']);
                 
                    \Models\Devices::add(
                        $result['id'],
                        $device_token,
                        $device_name,
                        $device_type
                    );
                }
            }


            return response()->json(
                $this->populateresponse([
                    'status' => $this->status,
                    'data' => $this->jsondata
                ])
            );            
        }

        /**
         * [This method is used for editing employer signup] 
         * @param  Request
         * @return Json Response
         */

        public function edit_employersignup(Request $request){
            $request->replace($this->post);
                    
            $newsletter_acceptance  = ($request->newsletter_acceptance)?$request->newsletter_acceptance:'no';
            $field                  = ['id_user','type','first_name','last_name','name','email','status','company_name',\DB::raw("'yes' as agree"),\DB::raw("'{$newsletter_acceptance}' as newsletter_acceptance")];

            $validate = \Validator::make($request->all(), [
                'first_name'        => validation('first_name'),
                'last_name'         => validation('last_name'),
                /*'company_name'      => array_merge(validation('company_name'),[
                    Rule::unique('users')
                    ->ignore('trashed','status')
                    ->where(function($query) use($request){
                        if(!empty($request->id_user)){
                            $query->where('id_user','!=',$request->id_user);
                        }
                    })
                ]),*/
                'email'             => ['required','email',Rule::unique('users')->ignore('trashed','status')->where(function($query) use($request){$query->where('id_user','!=',$request->id_user);})],
                'password'          => validation('password'),
                'agree'             => validation('agree'),
            ],[
                'first_name.required'       => 'M0006',
                'first_name.regex'          => 'M0007',
                'first_name.string'         => 'M0007',
                'first_name.max'            => 'M0020',
                'last_name.required'        => 'M0008',
                'last_name.regex'           => 'M0009',
                'last_name.string'          => 'M0009',
                'last_name.max'             => 'M0019',
                'company_name.required'     => 'M0023',
                'company_name.regex'        => 'M0024',
                'company_name.string'       => 'M0024',
                'company_name.max'          => 'M0025',
                'company_name.unique'       => 'M0556',
                'email.required'            => 'M0010',
                'email.email'               => 'M0011',
                'email.unique'              => 'M0012',
                'password.required'         => 'M0013',
                'password.regex'            => 'M0014',
                'password.string'           => 'M0013',
                'password.min'              => 'M0014',
                'password.max'              => 'M0018',
                'agree.required'            => 'M0017',
            ]);

            if($validate->fails()){
                $this->message = $validate->messages()->first();
            }else{
                $this->jsondata = \Models\Users::findById($request->id_user,['id_user']);
                
                if(!empty($this->jsondata)){
                    $code                   = bcrypt(__random_string());
                    $emailData              = ___email_settings();
                    $emailData['email']     = $this->post['email'];
                    $emailData['name']      = $this->post['first_name'];
                    $emailData['link']      = url(sprintf("activate/account?token=%s",$code));
                    
                    \Models\Users::change($this->jsondata['id_user'],[
                        'first_name' => $this->post['first_name'],
                        'last_name' => $this->post['last_name'],
                        'email' => $this->post['email'],
                        'company_name' => ''/*$this->post['company_name']*/,
                        'password' => bcrypt($this->post['password']),
                        'remember_token' => $code,
                        'status' => 'pending',
                        'updated' => date('Y-m-d H:i:s')
                    ]);

                    /* RECORDING ACTIVITY LOG */
                    event(new \App\Events\Activity([
                        'user_id'           => $request->id_user,
                        'user_type'         => 'employer',
                        'action'            => 'webservice-employer-update-signup',
                        'reference_type'    => 'users',
                        'reference_id'      => $request->id_user
                    ]));

                    $this->jsondata = \Models\Users::findById($request->id_user,$field);
                    
                    if(!empty($this->jsondata)){
                        $this->jsondata['action'] = 'signup';
                    }

                    $this->status = true;
                    $this->message = 'M0021';
                    ___mail_sender($this->post['email'],sprintf("%s %s",$this->post['first_name'],$this->post['last_name']),"talent_signup_verification",$emailData);
                }else{
                    $this->message = 'M0028';
                }
            }
            
            $jsondata = json_decode(json_encode($this->jsondata),true);
            
            if(!empty($jsondata)){
                $this->jsondata = $jsondata;
            }

            return response()->json(
                $this->populateresponse([
                    'status' => $this->status,
                    'data' => $this->jsondata
                ])
            );            
        }

        /**
         * [This method is used for forgot password] 
         * @param  Request
         * @return Json Response
         */

        public function forgotpassword(Request $request){
            $request->replace($this->post);

            $validate = \Validator::make($request->all(), [
                'email'             => ['required','email']
            ],[
                'email.required'            => 'M0010',
                'email.email'               => 'M0011',
            ]);

            if($validate->fails()){
                $this->message = $validate->messages()->first();
            }else {
                $result = \Models\Users::findByEmail($this->post['email'],['id_user','email','first_name','last_name','status','type']);
                if(!empty($result)){
                    if($result['status'] == 'pending'){
                        $this->message = 'M0046';
                        $this->jsondata = [
                            'type'          => 'confirm',
                            'title'         => trans('general.M0043'),
                            'messages'      => trans('general.M0046'),
                            'button_one'    => trans('general.M0044'),
                            'button_two'    => trans('general.M0045'),
                            'token'         => ''
                        ];
                    }else if($result['status'] == 'inactive'){
                        $this->message = 'M0002';
                        $this->jsondata = [
                            'type'          => 'alert',
                            'title'         => trans('general.M0026'),
                            'messages'      => trans('general.M0002'),
                            'button'        => trans('general.M0027'),
                            'token'         => ''
                        ];
                    }else if($result['status'] == 'suspended'){
                        $this->message = 'M0003';
                        $this->jsondata = [
                            'type'          => 'alert',
                            'title'         => trans('general.M0026'),
                            'messages'      => trans('general.M0003'),
                            'button'        => trans('general.M0027'),
                            'token'         => ''
                        ];
                    }else {
                        $code                   = bcrypt(__random_string());
                        $forgot_otp             = strtoupper(__random_string(6));

                        $isUpdated = \Models\Users::change($result['id_user'],[
                            'remember_token'    => $code,
                            'forgot_otp'        => $forgot_otp,
                            'social_account'    => 'changed',
                            'updated'           => date('Y-m-d H:i:s')
                        ]);

                        /* RECORDING ACTIVITY LOG */
                        event(new \App\Events\Activity([
                            'user_id'           => $result['id_user'],
                            'user_type'         => $result['type'],
                            'action'            => 'webservice-'.$result['type'].'-forgotpassword',
                            'reference_type'    => 'users',
                            'reference_id'      => $result['id_user']
                        ]));

                        if(!empty($isUpdated)){
                            $emailData              = ___email_settings();
                            $emailData['email']     = $result['email'];
                            $emailData['name']      = $result['first_name'];
                            $emailData['code']      = $forgot_otp;

                            ___mail_sender($result['email'],sprintf("%s %s",$result['first_name'],$result['last_name']),"forgot_password",$emailData);

                            $this->message = 'M0029';
                            $this->status = true;
                            
                            $this->jsondata = [
                                'type'          => 'alert',
                                'title'         => trans('general.M0026'),
                                'messages'      => trans('general.M0029'),
                                'button'        => trans('general.M0027'),
                                'token'         => $code
                            ];
                        }else{
                            $this->message = 'M0029';
                            $this->status = true;

                            $this->jsondata = [
                                'type' => 'alert',
                                'title' => trans('general.M0026'),
                                'messages' => trans('general.M0029'),
                                'button' => 'M0027',
                                'token'         => $code
                            ];
                        }
                    }
                }else{
                    $this->message = 'M0028';        
                    
                    $this->jsondata = [
                        'type'          => 'alert',
                        'title'         => trans('general.M0026'),
                        'messages'      => trans('general.M0028'),
                        'button'        => trans('general.M0027'),
                        'token'         => ""
                    ];            
                }
            }

            return response()->json(
                $this->populateresponse([
                    'status' => $this->status,
                    'data' => $this->jsondata
                ])
            );            
        }

        /**
         * [This method is used for verify forgot password] 
         * @param  Request
         * @return Json Response
         */

        public function verifyforgotpassword(Request $request){
            $request->replace($this->post);

            $validate = \Validator::make($request->all(), [
                'verification_code'             => ['required']
            ],[
                'verification_code.required'    => 'M0504',
            ]);

            if($validate->fails()){
                $this->message = $validate->messages()->first();
            }else {
                $result = \Models\Users::findByToken($this->post['token'],['id_user','email','first_name','last_name','status','type','forgot_otp']);

                if($result['forgot_otp'] != $request->verification_code){
                    $this->message = 'M0505';
                }else if(!empty($result)){
                    if($result['status'] == 'pending'){
                        $this->jsondata = [
                            'type'          => 'confirm',
                            'title'         => 'M0043',
                            'messages'      => 'M0046',
                            'button_one'    => 'M0044',
                            'button_two'    => 'M0045',
                            'token'         => ""
                        ];
                    }else if($result['status'] == 'inactive'){
                        $this->jsondata = [
                            'type'          => 'alert',
                            'title'         => 'M0026',
                            'messages'      => 'M0002',
                            'button'        => 'M0027',
                            'token'         => ""
                        ];
                    }else if($result['status'] == 'suspended'){
                        $this->jsondata = [
                            'type'          => 'alert',
                            'title'         => 'M0026',
                            'messages'      => 'M0003',
                            'button'        => 'M0027',
                            'token'         => ""
                        ];
                    }else {
                        $code                   = bcrypt(__random_string());
                        $forgot_otp             = strtoupper(__random_string(6));

                        $this->status = true;
                        $isUpdated = \Models\Users::change($result['id_user'],[
                            'remember_token'    => $code,
                            'forgot_otp'        => $forgot_otp,
                            'updated'           => date('Y-m-d H:i:s')
                        ]);

                        $this->jsondata = [
                            'type'          => 'alert',
                            'title'         => 'M0026',
                            'messages'      => 'M0029',
                            'button'        => 'M0027',
                            'token'         => $code
                        ];
                    }
                }else{
                    $this->message = 'M0028';        
                    
                    $this->jsondata = [
                        'type'          => 'alert',
                        'title'         => 'M0026',
                        'messages'      => 'M0028',
                        'button'        => 'M0027',
                        'token'         => ""
                    ];            
                }
            }

            return response()->json(
                $this->populateresponse([
                    'status' => $this->status,
                    'data' => $this->jsondata
                ])
            );            
        }

        /**
         * [This method is used for reset password] 
         * @param  Request
         * @return Json Response
         */

        public function resetpassword(Request $request){
            $request->replace($this->post);

            $validate = \Validator::make($request->all(), [
                'token'                     => ['required'],
                'password'                  => validation('password'),
            ],[
                'token.required'            => 'M0121',
                'password.required'         => 'M0013',
                'password.regex'            => 'M0014',
                'password.string'           => 'M0013',
                'password.min'              => 'M0014',
                'password.max'              => 'M0018',
            ]);
            
            if($validate->fails()){
                $this->message = $validate->messages()->first();
            }else {
                if(!empty($request->token)){
                    $result = \Models\Users::findByToken($request->token,['id_user','type']);
                    if(!empty($result)){
                        $isUpdated = \Models\Users::change($result['id_user'],['password' => bcrypt($request->password),'is_email_verified' => 'yes','remember_token' => bcrypt(__random_string()) , 'forgot_otp' => strtoupper(__random_string(6)), 'updated' => date('Y-m-d H:i:s')]);

                        if(!empty($isUpdated)){
                            $this->message  = 'M0507';
                            $this->status   = true;

                            /* RECORDING ACTIVITY LOG */
                            event(new \App\Events\Activity([
                                'user_id'           => $result['id_user'],
                                'user_type'         => $result['type'],
                                'action'            => 'reset-password',
                                'reference_type'    => 'users',
                                'reference_id'      => $result['id_user']
                            ]));
                        }
                    }else{
                        $this->message = 'M0175';
                    }
                }else{
                    $this->message = 'M0175';
                }
            }

            return response()->json(
                $this->populateresponse([
                    'status' => $this->status,
                    'data' => $this->jsondata
                ])
            );            
        }

        /**
         * [This method is used for terms & conditions , privacy & policy]
         * @param  Request
         * @return Json Response
         */

        public function staticpage($slug, Request $request){
            $request->replace($this->post);
            $page = \Models\Pages::single($slug,['id','title','excerpt','content']);

            if(!empty($page)){
                $this->status = true;
                $this->jsondata = [
                    'title' => $page['title'],
                    'content' => file_get_contents(url(sprintf('page/%s?stream=mobile',$slug))),
                ];
            }else if($slug === 'faq' || $slug === 'community'){
                $this->status = true;
                $this->jsondata = [
                    'title' => 'Frequently Asked Questions',
                    'content' => file_get_contents(url(sprintf('page/%s?stream=mobile',$slug))),
                ];
            }else if($slug === 'contact'){
                
                $validate = \Validator::make($request->all(), [
                    'name'              => validation('name'),
                    'email'             => ['required','email'],
                    'phone_number'      => validation('phone_number'),
                    'message'           => validation('message'),
                ],[
                    'name.required'             => 'M0040',
                    'name.regex'                => 'M0041',
                    'name.string'               => 'M0041',
                    'name.max'                  => 'M0042',
                    'email.required'            => 'M0010',
                    'email.email'               => 'M0011',
                    'phone_number.required'     => 'M0439',
                    'phone_number.regex'        => 'M0440',
                    'phone_number.string'       => 'M0440',
                    'phone_number.min'          => 'M0441',
                    'phone_number.max'          => 'M0442',
                    'message.required'          => 'M0034',
                    'message.string'            => 'M0035',
                    'message.max'               => 'M0036',
                ]);

                if($validate->fails()){
                    $this->message = $validate->messages()->first();
                }else{
                    $this->status = true;
                    $configuration      = ___configuration(['site_email','site_name']);
                    $message_subject    = 'Contact';
                    $message_type       = 'contact-us';
                    $user               = \Models\Users::findByEmail($this->post['email'],['id_user','type']);

                    $sender_id          = $sender_type = NULL;
                    $sender_name        = $this->post['name']; 
                    $sender_email       = $this->post['email']; 
                    
                    if(!empty($user)){
                        $sender_id      = $user['id_user'];
                        $sender_type    = $user['type'];
                    }

                    $isUpdated = \Models\Messages::compose($sender_name, $sender_email,$this->post['message'],$message_subject,$message_type);

                    if(!empty($isUpdated)){
                        $emailData              = ___email_settings();
                        $emailData['email']     = $this->post['email'];
                        $emailData['name']      = $this->post['name'];
                        
                        ___mail_sender($this->post['email'],$this->post['name'],"user_contact",$emailData);
                        ___mail_sender($configuration['site_email'],$configuration['site_name'],"admin_contact",$emailData);

                        $this->message = 'M0037';
                        $this->status = true;
                    }else{
                        $this->message = 'M0037';
                        $this->status = true;
                    }
                }
            }

            return response()->json(
                $this->populateresponse([
                    'status' => $this->status,
                    'data' => $this->jsondata
                ])
            );         
        }

        /**
         * [This method is used for randering view of resend link activation] 
         * @param  null
         * @return Json Response
         */

        public function resend_activation_link(){
            if(!empty($this->post['email'])){
                $this->post['email'] = $this->post['email'];
            }

            $validate = \Validator::make($this->post, [
                'email'             => ['required','email']
            ],[
                'email.required'            => 'M0010',
                'email.email'               => 'M0011',
            ]);

            if($validate->fails()){
                $this->message = $validate->messages()->first();
            }else {
                $this->status = true;
                $result = \Models\Users::findByEmail($this->post['email'],['id_user','first_name','last_name','email','type']);

                if(!empty($result)){
                    $code                   = bcrypt(__random_string());
                    
                    if($result['type'] == TALENT_ROLE_TYPE){
                        $emailData              = ___email_settings();
                        $emailData['email']     = $result['email'];
                        $emailData['name']      = $result['first_name'];
                        $emailData['link']      = url(sprintf("activate/account?token=%s",$code));
                        
                        \Models\Talents::change($result['id_user'],['remember_token' => $code,'updated' => date('Y-m-d H:i:s')]);
                        ___mail_sender($result['email'],sprintf("%s %s",$result['first_name'],$result['last_name']),"talent_signup_verification",$emailData);
                        
                            /* RECORDING ACTIVITY LOG */
                            event(new \App\Events\Activity([
                                'user_id'           => $result['id_user'],
                                'user_type'         => 'talent',
                                'action'            => 'webservice-talent-resend-activation-link',
                                'reference_type'    => 'users',
                                'reference_id'      => $result['id_user']
                            ]));

                        $this->message = 'M0021';
                    }else if($result['type'] == EMPLOYER_ROLE_TYPE){
                        $emailData              = ___email_settings();
                        $emailData['email']     = $result['email'];
                        $emailData['name']      = $result['first_name'];
                        $emailData['link']      = url(sprintf("activate/account?token=%s",$code));

                        \Models\Employers::change($result['id_user'],['remember_token' => $code,'updated' => date('Y-m-d H:i:s')]);
                        ___mail_sender($result['email'],sprintf("%s %s",$result['first_name'],$result['last_name']),"employer_signup",$emailData);

                            /* RECORDING ACTIVITY LOG */
                            event(new \App\Events\Activity([
                                'user_id'           => $result['id_user'],
                                'user_type'         => 'employer',
                                'action'            => 'webservice-employer-resend-activation-link',
                                'reference_type'    => 'users',
                                'reference_id'      => $result['id_user']
                            ]));

                        $this->message = 'M0021';
                    }else{
                        $this->message = 'M0028';
                    }
                }else{
                    $this->message = 'M0028';
                }
            }

            return response()->json(
                $this->populateresponse([
                    'status' => $this->status,
                    'data' => $this->jsondata
                ])
            );
        }

        /**
         * [This method is used for randering view of user information] 
         * @param  null
         * @return Json Response
         */

        public function userinfo(){
            
            return response()->json(
                $this->populateresponse([
                    'status' => $this->status,
                    'data' => $this->jsondata
                ])
            );         
        }

        /**
         * [This method is used for randering view of message] 
         * @param  null
         * @return none
         */

        public function subscription_respond(Request $request){
            if(!empty($request->bt_signature) && !empty($request->bt_payload)){
                \Models\Payments::braintree_response([
                    'user_id'                   => '',
                    'braintree_response_json'   => $request->bt_signature,
                    'status'                    => 'true',
                    'type'                      => 'subscription',
                    'created'                   => date('Y-m-d H:i:s')
                ]);
                \Models\Payments::braintree_response([
                    'user_id'                   => '',
                    'braintree_response_json'   => $request->bt_payload,
                    'status'                    => 'true',
                    'type'                      => 'subscription',
                    'created'                   => date('Y-m-d H:i:s')
                ]);

                $webhookNotification = \Braintree_WebhookNotification::parse($request->bt_signature, $request->bt_payload);

                \Models\Payments::braintree_response([
                    'user_id'                   => '',
                    'braintree_response_json'   => json_encode(['webhookNotification'=>$webhookNotification]),
                    'status'                    => 'true',
                    'type'                      => 'subscription',
                    'created'                   => date('Y-m-d H:i:s')
                ]);
                // Example values for webhook notification properties
                $message = $webhookNotification->kind; // "subscription_went_past_due"
                $message .= $webhookNotification->timestamp; // "Sun Jan 1 00:00:00 UTC 2012"

                \Models\Payments::braintree_response([
                    'user_id'                   => '',
                    'braintree_response_json'   => json_encode((array)$message),
                    'status'                    => 'true',
                    'type'                      => 'subscription',
                    'created'                   => date('Y-m-d H:i:s')
                ]);
            }
        }

        /**
         * [This method is used for language change] 
         * @param  Request
         * @return \Illuminate\Http\Response
         */

        public function change_language(Request $request){
            $language = !empty($this->post['language']) ? $this->post['language'] : DEFAULT_LANGUAGE;
            return self::general($request,$language);
        }

        /**
         * [This method is used to send some common features like login , signup] 
         * @param  array $result [user data]
         * @param  array $device_token[used for alert notification purpose]
         * @param  array $device_type[android/iphone]
         * @param  array $device_name[to identify device]
         * @param  array $latitude[to trace location]
         * @param  array $longitude[to trace location]
         * @param  array $device_uuid[used for thumb login]
         * @return \Illuminate\Http\Response
         */

        private function __dologin($result, $device_token, $device_type, $device_name, $latitude, $longitude, $device_uuid){
            $user_rating = \Models\Users::getUserRating($result['id_user']);

            if(!empty($result)){
                $result['review']                           = $user_rating->review;
                $result['rating']                           = $user_rating->rating;
                $result['picture']                          = get_file_url(\Models\Users::get_file(sprintf(" type = 'profile' AND user_id = %s",$result['id_user']),'single',['filename','folder']));
                $result['thumb_device_configured']          = \Models\ThumbDevices::is_device_configured($result["id_user"],$device_uuid);
                $result['currency_sign']                    = \Cache::get('currencies')[$result['currency']];

                /* SENDER INFORMATION */
                $result['sender']                           = trim(sprintf("%s %s",$result['first_name'],$result['last_name']));
                $result['sender_id']                        = $result['id_user'];
                $result['sender_picture']                   = $result['picture'];
                $result['chat_status']                      = $result['chat_status'];
                $result['social_account']                   = $result['social_account'];
                $result['sender_profile_link']              = "";

                if($result['type'] == TALENT_ROLE_TYPE){
                    $result['sender_profile_link']          = url(sprintf('%s/find-talents/profile?talent_id=%s',EMPLOYER_ROLE_TYPE,___encrypt($result['id_user'])));
                }

                $result['notification_count']               = \Models\Notifications::unread_notifications($result['id_user']);
                $result['proposal_count']                   = \Models\Notifications::unread_notifications($result['id_user'],'proposals',$result['type']);
                
                $percentage = [];
                if($result['type'] == TALENT_ROLE_TYPE){
                    $userData   = \Models\Talents::get_user((object)['id_user' =>$result['id_user']],true);
                    $percentage = (array)\Models\Talents::get_profile_percentage($result['id_user']);
                }else if($result['type'] == EMPLOYER_ROLE_TYPE){
                    $userData   = \Models\Employers::get_user((object)['id_user' => $result['id_user']]);
                    $percentage = (array)\Models\Employers::get_profile_percentage($result['id_user']);
                }

                $result = array_merge($percentage,$result);

            
                $result['api_token'] = bcrypt(__random_string());
                \Models\Users::change($result['id_user'],['api_token' => $result['api_token'],'updated' => date('Y-m-d H:i:s')]);
            
                $result['_token'] = $result['api_token'];
                unset($result['api_token']);
            }


            if(!empty($device_token) && !empty($device_name) && !empty($device_type)){
                \Models\Devices::add(
                    $result['id_user'],
                    $device_token,
                    $device_name,
                    $device_type
                );
            }
            
            if(!empty($latitude) && !empty($longitude)){
                $update = [
                    "latitude"  => $latitude,
                    "longitude" => $longitude
                ];

                $istalentUpdated = \Models\Talents::change($result['id_user'],$update);
                if($istalentUpdated){
                    $result['latitude']     = $latitude;
                    $result['longitude']    = $longitude;
                }

                /* RECORDING ACTIVITY LOG */
                event(new \App\Events\Activity([
                    'user_id'           => $result['id_user'],
                    'user_type'         => $result['type'],
                    'action'            => 'webservice-'.$result['type'].'-login',
                    'reference_type'    => 'users',
                    'reference_id'      => $result['id_user']
                ]));

            }

            return $result;
        }
        
        /**
         * [This method is used for country phone codes ajax listing]
         * @param  Request
         * @return Json Response
         */ 
        public function country_phone_codes(Request $request){
            $request->replace($this->post);
            $where          = 'status = "active"';
            $page           = (!empty($request->page))?$request->page:1;
            if(!empty($request->search)){
                $where .= " AND phone_country_code LIKE '%{$request->search}%'";
            }
            
            $country_phone_codes = \Models\Listings::countries(
                'array',
                [
                    'phone_country_code as name',
                    'phone_country_code as id',
                ],
                $where,
                'country_order',
                $page
            );
            $this->status = true;
            
            return response()->json(
                $this->populateresponse([
                    'status' => $this->status,
                    'data' => $country_phone_codes
                ])
            );
        }

        /**
         * [This method is used for countries ajax listing]
         * @param  Request
         * @return Json Response
         */ 

        public function countries(Request $request){
            $language       = \App::getLocale();
            $where          = 'status = "active"';
            $page           = (!empty($request->page))?$request->page:1;
            if(!empty($request->search)){
                $where .= " AND {$language} LIKE '%{$request->search}%'";
            }

            $countries = \Models\Listings::countries(
                'array',
                [
                    \DB::Raw("IF(({$language} != ''),`{$language}`, `en`) as name"),
                    'id_country as id'
                ],
                $where,
                'country_order',
                $page                
            );
            $this->status = true;
            
            return response()->json(
                $this->populateresponse([
                    'status' => $this->status,
                    'data' => $countries
                ])
            );
        }

        /**
         * [This method is used for states ajax listing]
         * @param  Request
         * @return Json Response
         */ 

        public function states(Request $request){
            $request->replace($this->post);
            $language       = \App::getLocale();
            $where          = 'status = "active"';
            $page           = (!empty($request->page))?$request->page:1;
            if(!empty($request->search)){
                $where .= " AND {$language} LIKE '%{$request->search}%'";
            }

            if(!empty($request->country)){
                $where .= " AND country_id = $request->country";
            }

            $states = \Models\Listings::states(
                'array',
                [
                    \DB::Raw("IF(({$language} != ''),`{$language}`, `en`) as name"),
                    'id_state as id'
                ],
                $where,
                'name ASC',
                $page 
            );

            $this->status = true;
            
            return response()->json(
                $this->populateresponse([
                    'status' => $this->status,
                    'data' => $states
                ])
            );
        }

        /**
         * [This method is used for cities ajax listing]
         * @param  Request
         * @return Json Response
         */ 

        public function cities(Request $request){
            $request->replace($this->post);
            $language       = \App::getLocale();
            $where          = 'status = "active"';
            $page           = (!empty($request->page))?$request->page:1;
            if(!empty($request->search)){
                $where .= " AND {$language} LIKE '%{$request->search}%'";
            }

            $cities = \Models\Listings::cities(
                'array',
                [
                    \DB::Raw("IF(({$language} != ''),`{$language}`, `en`) as name"),
                    'id_city as id'
                ],
                $request->state,
                '',
                $where,
                $page,
                DEFAULT_CITY_LIMIT,
                'name'
            );
            
            $this->status = true;
            
            return response()->json(
                $this->populateresponse([
                    'status' => $this->status,
                    'data' => $cities
                ])
            );
        }

        /**
         * [This method is used for user type update (employer or talent)]
         * @param  Request
         * @return Json Response
         */ 

        public function user_type(Request $request){
            $request->replace($this->post);
        
            $validator = \Validator::make($request->all(), [
                'user_id'            => ['required'],
                'user_type'            => ['required']
            ],[
                'user_id.required'         => 'M0646',
                'user_type.required'         => 'M0647'
            ]);

            if($validator->fails()){
                $this->message = $validator->messages()->first();
            }else{
                
                $isUpdatedUserType = \DB::table('users')->where('id_user','=',$request->user_id)->update(['type'=>$request->user_type]);

                $percentage =[];
                if($request->user_type == TALENT_ROLE_TYPE){
                    $userData   = \Models\Talents::get_user((object)['id_user' => $request->user_id],true);
                    $percentage = (array)\Models\Talents::get_profile_percentage($request->user_id);
                }else if($request->user_type == EMPLOYER_ROLE_TYPE){
                    $userData   = \Models\Employers::get_user((object)['id_user' => $request->user_id]);
                    $percentage = (array)\Models\Employers::get_profile_percentage($request->user_id);
                }
                $this->jsondata = $userData;
                // dd($userData,$percentage);
                // dd($percentage);
                // if(!empty($percentage)){
                // $this->jsondata = (object)array_merge($percentage,json_decode(json_encode($this->jsondata),true));
                // $this->jsondata = \Models\Users::getUserRating($request->user_id);
                $this->status   = true;
                $this->message  = 'M0648';
            }

            return response()->json(
                $this->populateresponse([
                    'status' => $this->status,
                    'data' => $this->jsondata
                ])
            );
        }


        /*$id_answer = ___decrypt($request->id_answer);
            $validator = \Validator::make($request->all(), [
                'id_parent'              => ['required'],
                'type'                   => ['required'],
                'answer_description'     => ['required'],
                'id_answer'              => ['required']
            ],[
                'id_parent.required'            => 'M0630',
                'type.required'                 => 'M0626',
                'answer_description.required'   => 'M0629',
                'id_answer.required'            => 'M0625'
            ]);

            if($validator->fails()){
                $this->message = $validator->messages()->first();
            }else{
                $insertArr = [
                    'article_id'  => $id_answer,
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
            }

            return response()->json(
                $this->populateresponse([
                    'data'      => $this->jsondata,
                    'status'    => $this->status
                ])
            );*/

    }
