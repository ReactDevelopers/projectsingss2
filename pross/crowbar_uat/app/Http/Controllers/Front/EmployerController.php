<?php 
	namespace App\Http\Controllers\Front;

	use App\Http\Requests;
	use Illuminate\Support\Facades\DB;
	use App\Http\Controllers\Controller;
	
	use Illuminate\Support\Facades\Cookie;
	use Illuminate\Validation\Rule;
	use Illuminate\Http\Request;
	use Yajra\Datatables\Html\Builder;
	use Illuminate\Support\Facades\Storage;
	use Srmklive\PayPal\Services\AdaptivePayments;
	use Srmklive\PayPal\Services\ExpressCheckout;	
	use App\Models\Interview as Interview;
	use App\Lib\ExpressCheckoutCustom;

	use Crypt;
	
	class EmployerController extends Controller {

		private $jsondata;
		private $redirect;
		private $message;
		private $status;
		private $prefix;
		private $language;
		private $provider;
		private $head_message;

		public function __construct(){
			$this->jsondata         = [];
			$this->message          = false;
			$this->head_message = false;
			$this->redirect         = false;
			$this->status           = false;
			$this->prefix           = \DB::getTablePrefix();
			$this->language         = \App::getLocale();
			$this->provider 		= new ExpressCheckoutCustom();



			\View::share ( 'footer_settings', \Cache::get('configuration') );
		}



        /**
         * [This method is used for rendering view of Employer] 
         * @param  null
         * @return \Illuminate\Http\Response
         */
        
        public function profile_step(Request $request, $step){
            $data['subheader']              = 'employer/includes/top-menu';
            $data['title']                  = trans('website.W0610');
            $data['header']                 = 'innerheader';
            $data['footer']                 = 'innerfooter';
            $data['view']                   = "employer.profile.{$step}";

            $data['steps']                  = ___get_steps($step);
            $data['user']                   = \Models\Employers::get_user(\Auth::user());
            $data['country_phone_codes']    = \Cache::get('country_phone_codes');
            $data['countries']              = \Cache::get('countries');
            $data['states']                 = \Cache::get('states');
            $data['skip_url']               = ___editSkipUrl($step,'employer');
            return view('employer.profile.index')->with($data);
        }

        /**
         * [This method is used for handling profile setup]
         * @param  Request
         * @return Json Response
         */
        
        public function profile_step_process(Request $request, $step){
        	switch ($step) {
        		case 'one': {
        			$validation_mobile = validation('phone_number'); unset($validation_mobile[0]);
            
		            $validator = \Validator::make($request->all(), [
		                'company_name'                  => validation('company_name'),
		                'contact_person_name'           => validation('contact_person_name'),
		                'company_website'               => validation('website'),
		                // 'company_work_field'         => validation('company_work_field'),
		                'company_work_field'            => validation('company_industry'),
		                /*'certificates'                => validation('certificates'),*/
		                'company_biography'             => validation('company_biography'),
		            ],[
		                'company_name.required'         => trans('general.M0023'),
		                'company_name.regex'            => trans('general.M0024'),
		                'company_name.string'           => trans('general.M0024'),
		                'company_name.max'              => trans('general.M0025'),
		                'contact_person_name.required'  => trans('general.M0040'),
		                'contact_person_name.regex'     => trans('general.M0041'),
		                'contact_person_name.string'    => trans('general.M0041'),
		                'contact_person_name.max'       => trans('general.M0042'),
		                'company_website.string'        => trans('general.M0114'),
		                'company_website.regex'         => trans('general.M0114'),
		                // 'company_work_field.integer' => trans('general.M0115'),
		                'company_work_field.required'   => 'Please select industry',
		                'company_biography.regex'       => trans('general.M0116'),
		                'company_biography.string'      => trans('general.M0116'),
		                'company_biography.max'         => trans('general.M0117'),
		                'company_biography.min'         => trans('general.M0118'),
		            ]);

		            if($validator->passes()){
	                    if(!empty($request->company_website)){
	                        $request->request->add(['company_website'   => ___http($request->company_website)]);
	                        $request->request->add(['website'           => ___http($request->company_website)]);
	                    }

		                $update = array_intersect_key(
		                    json_decode(json_encode($request->all()),true), 
		                    array_flip(
		                        array(
		                            'company_profile',
		                            'company_name',
		                            'contact_person_name',
		                            'website',
		                            'company_website',
		                            'company_work_field',
		                            'company_biography',
		                        )
		                    )
		                );

		                /*
		                *   REPLACING ALL BLANK STRING WITH 
		                *   NULL BECAUSE OF LARAVEL MYSQL 
		                *   DRIVER ASKING FOR INTEGER VALUE 
		                *   FOR INTEGER COLUMN TYPE
		                */
		                ___filter_null($update);
		                
		                $isUpdated      = \Models\Employers::change(\Auth::user()->id_user,$update);
		                
		                /*if(!empty($request->certificates)){
		                    \Models\Employers::update_certificate(\Auth::user()->id_user,$request->certificates);
		                }else{
		                	\Models\Employers::update_certificate(\Auth::user()->id_user,[]);
		                }*/

		                $this->status   = true;
		                $this->message  = sprintf(ALERT_SUCCESS,trans("general.M0110"));
		                
	                    /* RECORDING ACTIVITY LOG */
	                    event(new \App\Events\Activity([
	                        'user_id'           => \Auth::user()->id_user,
	                        'user_type'         => 'employer',
	                        'action'            => 'employer-step-two',
	                        'reference_type'    => 'users',
	                        'reference_id'      => \Auth::user()->id_user
	                    ]));                    
	                    $this->redirect = url(sprintf("%s/profile/edit/two",EMPLOYER_ROLE_TYPE));
		            }else{
		                $this->jsondata = ___error_sanatizer($validator->errors());
		            }
		            break;
        		}
        		case 'two': {
        			$validation_mobile = validation('phone_number'); unset($validation_mobile[0]);
		            $user = \Models\Talents::get_user(\Auth::user());
		            $validator = \Validator::make($request->all(), [
		                'first_name'                => validation('first_name'),
		                'last_name'                 => validation('last_name'),
		                'email'                     => ['required','email',Rule::unique('users')->ignore('trashed','status')->where(function($query) use($request){$query->where('id_user','!=',$request->user()->id_user);})],
		                'mobile'                    => array_merge([Rule::unique('users')->ignore('trashed','status')->where(function($query) use($request){$query->where('id_user','!=',\Auth::user()->id_user);})],validation('mobile')),
		                'country_code'              => $request->mobile ? array_merge(['required'], validation('country_code')) : validation('country_code'),                
		                'other_mobile'              => array_merge([Rule::unique('users')->ignore('trashed','status')->where(function($query) use($request){$query->where('id_user','!=',\Auth::user()->id_user);})],validation('mobile'),['different:mobile']),
		                'other_country_code'        => $request->other_mobile ? array_merge(['required'], validation('country_code')) : validation('country_code'),                
		                /*'website'                   => validation('website'),*/
		                'address'                   => validation('address'),
		                'country'                   => validation('country'),
		                'state'                     => validation('state'),
		                'postal_code'               => validation('postal_code'),
		            ],[
		                'first_name.required'       => trans('general.M0006'),
		                'first_name.regex'          => trans('general.M0007'),
		                'first_name.string'         => trans('general.M0007'),
		                'first_name.max'            => trans('general.M0020'),
		                'last_name.required'        => trans('general.M0008'),
		                'last_name.regex'           => trans('general.M0009'),
		                'last_name.string'          => trans('general.M0009'),
		                'last_name.max'             => trans('general.M0019'),
		                'email.required'            => trans('general.M0010'),
		                'email.email'               => trans('general.M0011'),
		                'email.unique'              => trans('general.M0047'),  
		                'mobile.required'           => trans('general.M0030'),
		                'mobile.regex'              => trans('general.M0031'),
		                'mobile.string'             => trans('general.M0031'),
		                'mobile.min'                => trans('general.M0032'),
		                'mobile.max'                => trans('general.M0033'),
		                'mobile.unique'             => trans('general.M0197'),
		                'address.string'            => trans('general.M0057'),
		                'address.regex'             => trans('general.M0057'),
		                'address.max'               => trans('general.M0058'),                
		                'country.integer'           => trans('general.M0059'),
		                'state.integer'             => trans('general.M0060'),
		                'postal_code.string'        => trans('general.M0061'),
		                'postal_code.regex'         => trans('general.M0061'),
		                'postal_code.max'           => trans('general.M0062'),
		                'postal_code.min'           => trans('general.M0063'),
		                
		                /*USELESS FOR NOW DUE TO DESIGN RESTRICTION*/
		                'country_code.required'         => trans('general.M0164'),
		                'country_code.string'           => trans('general.M0074'),
		                'other_country_code.required'   => trans('general.M0432'),
		                'other_country_code.string'     => trans('general.M0585'),
		                'other_mobile.required'         => trans('general.M0030'),
		                'other_mobile.regex'            => trans('general.M0031'),
		                'other_mobile.string'           => trans('general.M0031'),
		                'other_mobile.min'              => trans('general.M0032'),
		                'other_mobile.max'              => trans('general.M0033'),
		                'other_mobile.unique'           => trans('general.M0197'),
		                'other_mobile.different'        => trans('general.M0127'),
		            ]);

		            if($validator->passes()){
		                $update = array_intersect_key(
		                    json_decode(json_encode($request->all()),true), 
		                    array_flip(
		                        array(
		                            'first_name',
		                            'last_name',
		                            'email',
		                            'mobile',
		                            'other_mobile',
		                            'address',
		                            'country',
		                            'state',
		                            'postal_code',
		                            'country_code',
		                            'other_country_code',
		                        )
		                    )
		                );

		                /*
		                *   REPLACING ALL BLANK STRING WITH 
		                *   NULL BECAUSE OF LARAVEL MYSQL 
		                *   DRIVER ASKING FOR INTEGER VALUE 
		                *   FOR INTEGER COLUMN TYPE
		                */
		                ___filter_null($update);
		                if($update['mobile'] != \Auth::user()->mobile){
		                    $update['is_mobile_verified'] = DEFAULT_NO_VALUE;
		                }

		                if($request->email != $user['email']){
		                    $code = bcrypt(__random_string());
		                    $update['remember_token'] = $code;
		                    $update['is_email_verified'] = DEFAULT_NO_VALUE;
		                }

		                // if(!empty($update['country'])){
		                //     $update['country_code'] = ___get_country_phone_code_by_country($update['country']);
		                // }

		                $isUpdated      = \Models\Employers::change(\Auth::user()->id_user,$update);

		                if($request->email != $user['email']){
		                    if(!empty($request->email)){
		                        $emailData              = ___email_settings();
		                        $emailData['email']     = $request->email;
		                        $emailData['name']      = $request->first_name;
		                        $emailData['link']      = url(sprintf("emailverify/account?token=%s",$code));

		                        ___mail_sender($request->email,sprintf("%s %s",$request->first_name,$request->last_name),"update_email_verification",$emailData);
		                    }
		                }

		                $this->status   = true;
		                $this->message  = sprintf(ALERT_SUCCESS,trans("general.M0110"));
		                
	                    /* RECORDING ACTIVITY LOG */
	                    event(new \App\Events\Activity([
	                        'user_id'           => \Auth::user()->id_user,
	                        'user_type'         => 'employer',
	                        'action'            => 'employer-step-one',
	                        'reference_type'    => 'users',
	                        'reference_id'      => \Auth::user()->id_user
	                    ]));                    
	                    $this->redirect = url(sprintf("%s/profile",EMPLOYER_ROLE_TYPE));
		            }else{
		                $this->jsondata = json_decode(json_encode(___error_sanatizer($validator->errors())),true);
		                
		                if(!empty($this->jsondata['country_code'])){
		                    $this->jsondata['mobile'][0] = $this->jsondata['country_code'][0];
		                    unset($this->jsondata['country_code']);
		                }

		                if(!empty($this->jsondata['other_country_code'])){
		                    $this->jsondata['other_mobile'][0] = $this->jsondata['other_country_code'][0];
		                    unset($this->jsondata['other_country_code']);
		                }                

		                $this->jsondata = (object)$this->jsondata;
		            }
        			break;
        		}
        	}

        	if(!empty($request->redirect)){
        		$this->redirect = urldecode($request->redirect);
        	}

            return response()->json([
                'data'      => $this->jsondata,
                'status'    => $this->status,
                'message'   => $this->message,
                'redirect'  => $this->redirect,
                'nomessage' => true,
            ]);
        }

		/**
		 * [This method is used for rendering view of Employer job post] 
		 * @param  null
		 * @return \Illuminate\Http\Response
		 */
		
		public function job_post(Request $request, $step){
			$request->request->add(['currency' => \Session::get('site_currency')]);
			if(!in_array($step, ['one','two','three','four','five'])){
				return redirect('404');
			}			

			$project_id = !empty($request->job_id) ? ___decrypt($request->job_id) : '';
			
			$language       				= \App::getLocale();
			$prefix         				= \DB::getTablePrefix();
			$data['subheader']              = 'employer/includes/top-menu';
			$data['title']                  = ___title_job_steps($step);
			$data['header']                 = 'innerheader';
			$data['footer']                 = 'innerfooter';
			$data['view']                   = "employer.postjob.{$step}";

			$data['talent_id']				= !empty($request->talent_id) ? $request->talent_id : '';
			$data['steps']                  = ___get_steps($step);
			$data['action']  				= '';
			$data['action_url']  			= '';
			$data['project_id_postfix']  	= '';
			
			if(!empty($project_id)){
				$data['action']  	= 'edit';
				$data['action_url'] = 'edit/';
				$data['project_id_postfix'] = '?job_id='.___encrypt($project_id);
	            $data['project'] 	= \Models\Projects::addSelect(['*'])->projectPrice()
	            ->with([
	                'industries.industries' =>  function($q) use($language, $prefix){
	                    $q->select(
	                        'id_industry',
	                        \DB::Raw("IF(({$language} != ''),`{$language}`, `en`) as name")
	                    );
	                },
	                'subindustries.subindustries' =>  function($q) use($language, $prefix){
	                    $q->select(
	                        'id_industry',
	                        \DB::Raw("IF(({$language} != ''),`{$language}`, `en`) as name")
	                    );
	                },
	                'skills.skills' => function($q){
						$q->select(
							'id_skill',
							'skill_name'
						);
					}
	            ])
	            ->where('user_id',\Auth::user()->id_user)
	            ->where('status','active')
	            ->where('id_project',$project_id)
	            ->get()->first();
	            $data['project'] = json_decode(json_encode($data['project']),true);
	            if($data['project']['awarded'] ==  DEFAULT_YES_VALUE){
	            	return redirect(sprintf('employer/project/details?job_id=%s',$request->job_id));
	            }
			}else{

				$data['project_id_postfix'] 	= !empty($request->talent_id) ? '?talent_id='.$request->talent_id : '';
				$data['project']                = \Models\Projects::draft(\Auth::user()->id_user);
				if(__words_to_number($data['project']['step']) < __words_to_number($step) && $step != 'one'){
					if(!empty($request->talent_id)){
						return redirect(sprintf('%s/hire/talent/one?talent_id=%s',EMPLOYER_ROLE_TYPE,$request->talent_id));
					}elseif(!empty($data['project']['step'])){ 
						return redirect(sprintf('%s/hire/talent/'.$data['project']['step'],EMPLOYER_ROLE_TYPE));
					}else{
						return redirect(sprintf('%s/hire/talent/one',EMPLOYER_ROLE_TYPE));
					}
				}
			}

			$data['user']     			= \Models\Employers::get_user(\Auth::user());
			$data['skip_url'] 			= url(sprintf("%s/find-talents",EMPLOYER_ROLE_TYPE));
			
			if($step == 'five'){
				$data['subindustries_name'] = \Models\Listings::industry_subindustry_list(current($data['project']['industries'])['industries']['id_industry']);
			}

			return view('employer.postjob.index')->with($data);
		}

		public function check_jobpayment_configure(Request $request){
			
			if(empty(\Auth::user()->paypal_payer_id)){
				// $this->redirect = true;
				$this->redirect = url(sprintf('%s/settings/payments',EMPLOYER_ROLE_TYPE));
				$this->status = true;
				$this->message = sprintf(ALERT_DANGER,sprintf(trans("website.W0998"), url('employer/settings/payments'), 'www.paypal.com'));
				return response()->json([
					'data'      => $this->jsondata,
					'status'    => $this->status,
					'message'   => $this->message,
					'nomessage' => false,
					'redirect'  => $this->redirect,
				]);
			}
			else{
				$this->status = false;
				$this->message = sprintf(ALERT_DANGER,sprintf(trans("website.W0996"), url('employer/settings/payments'), 'www.paypal.com'));
				return response()->json([
					'data'      => $this->jsondata,
					'status'    => $this->status,
					'message'   => $this->message,
					'nomessage' => true,
					'redirect'  => $this->redirect,
				]);
			}
		}

		/**
		 * [This method is used for rendering view of Employer job post] 
		 * @param  null
		 * @return \Illuminate\Http\Response
		 */
		
		public function job_post_process(Request $request, $step){
			$request->request->add(['currency' => \Session::get('site_currency')]);
			switch ($step) {
				case 'one':{
					$validator = \Validator::make($request->all(), [
						'title'                             => validation('jobtitle'),
						'description'                       => validation('description'),
						'agree'                             => validation('agree'),
					],[
						'title.required'                    => trans('general.M0090'),
						'title.string'                      => trans('general.M0091'),
						'title.regex'                       => trans('general.M0091'),
						'title.max'                         => trans('general.M0092'),
						'title.min'                         => trans('general.M0093'),
						'description.required'              => trans('general.M0138'),
						'description.string'                => trans('general.M0139'),
						'description.regex'                 => trans('general.M0139'),
						'description.max'                   => trans('general.M0140'),
						'description.min'                   => trans('general.M0141'),
						'agree.required'                    => trans('general.agree_required'),
					]);

					if($validator->passes()){
						$postjob = array_intersect_key(json_decode(json_encode($request->all()),true), array_flip(array('id_project','title','description')));
						if($request->action == 'edit'){
							$postjob['updated'] 	= date('Y-m-d H:i:s');
						}else{
							$postjob['step']    	= 'two';
							$postjob['status']  	= 'draft';
							$postjob['user_id'] 	= \Auth::user()->id_user;
							$postjob['talent_id']   = !empty($request->talent_id) ? ___decrypt($request->talent_id) : '';
							$postjob['updated'] 	= date('Y-m-d H:i:s');
							$postjob['created'] 	= date('Y-m-d H:i:s');
						}

						$isProjectSaved = \Models\Projects::postjob($postjob);

						
						if(!empty($isProjectSaved)){
							$this->status   = true;
							if($request->action == 'edit'){
								$this->redirect = url(sprintf('%s/hire/talent/edit/two?job_id=%s',EMPLOYER_ROLE_TYPE,___encrypt($request->id_project)));
							}else{
								if(!empty($request->talent_id)){
									$this->redirect = url(sprintf('%s/hire/talent/two?talent_id=%s',EMPLOYER_ROLE_TYPE,$request->talent_id));
								}else{
									$this->redirect = url(sprintf('%s/hire/talent/two',EMPLOYER_ROLE_TYPE));
								}
							}
						}else{
							$this->message = trans('general.M0356');
						}
					}else{
						$this->jsondata     = ___error_sanatizer($validator->errors());
					}

					break;
				}

				case 'two':{
					if(!empty($request->industry)){
						$request->request->add(['industry' => array_filter($request->industry)]);
					}

					$validator = \Validator::make($request->all(), [
						'industry'                          => array_merge(['required'],validation('industry')),
						'required_skills'                   => validation('required_skills'),
					],[
						'industry.array'                    => trans('general.M0064'),
						'industry.required'                 => trans('general.M0136'),
						'required_skills.required'          => trans('general.M0137'),
						'required_skills.array'             => trans('general.M0065'),
					]);

					if($validator->passes()){
						$isSkillSaved 			= \Models\Employers::update_job_skills($request->id_project,$request->required_skills);
						$isIndustryAdded        = \Models\Projects::saveindustry($request->id_project,$request->industry);
						$postjob['id_project']  = $request->id_project;
						
						if($request->action == 'edit'){
							$postjob['updated'] 	= date('Y-m-d H:i:s');
						}else{
							$postjob['step']        = 'three';
							$postjob['status']      = 'draft';
							$postjob['user_id']     = \Auth::user()->id_user;
							$postjob['talent_id']   = !empty($request->talent_id) ? ___decrypt($request->talent_id) : '';
							$postjob['created']     = date('Y-m-d H:i:s');
						}
						$postjob['updated']     = date('Y-m-d H:i:s');

						$isProjectSaved = \Models\Projects::postjob($postjob);
						
						if(!empty($isProjectSaved)){
							$this->status   = true;
							if($request->action == 'edit'){
								$this->redirect = url(sprintf('%s/hire/talent/edit/three?job_id=%s',EMPLOYER_ROLE_TYPE,___encrypt($request->id_project)));
							}else{
								if(!empty($request->talent_id)){
									$this->redirect = url(sprintf('%s/hire/talent/three?talent_id=%s',EMPLOYER_ROLE_TYPE,$request->talent_id));
								}else{
									$this->redirect = url(sprintf('%s/hire/talent/three',EMPLOYER_ROLE_TYPE));
								}
							}
						}else{
							$this->message = trans('general.M0356');
						}
					}else{
						$this->jsondata     = ___error_sanatizer($validator->errors());
					}

					break;
				} 

				case 'three':{
					$request->request->add(['price' => (string)current(array_filter($request->price))]);
					$validator = \Validator::make($request->all(), [
						'employment'                        => validation('employment'),
						'price'                          	=> validation('price'),
					],[
						'employment.required'               => trans('general.M0133'),
						'employment.string'                 => trans('general.M0134'),
						'price.required'                    => trans('general.M0228'),
						'price.numeric'                     => trans('general.M0229'),
						'price.max'                         => trans('general.M0231'),
						'price.min'                         => trans('general.M0230'),
					]);

					if($validator->passes()){
						$postjob['price']  		= $request->price;
						$postjob['id_project']  = $request->id_project;
						$postjob['employment']  = $request->employment;
						$postjob['price_unit']  = $request->currency;
						
						if($request->action == 'edit'){
							$postjob['updated']     = date('Y-m-d H:i:s');
						}else{							
							$postjob['step']        = 'four';
							$postjob['status']      = 'draft';
							$postjob['user_id']     = \Auth::user()->id_user;
							$postjob['talent_id']   = !empty($request->talent_id) ? ___decrypt($request->talent_id) : '';
							$postjob['updated']     = date('Y-m-d H:i:s');
							$postjob['created']     = date('Y-m-d H:i:s');
						}

						$isProjectSaved = \Models\Projects::postjob($postjob);
						
						if(!empty($isProjectSaved)){
							$this->status   = true;
							if($request->action == 'edit'){
								$this->redirect = url(sprintf('%s/hire/talent/edit/four?job_id=%s',EMPLOYER_ROLE_TYPE,___encrypt($request->id_project)));
							}else{
								if(!empty($request->talent_id)){
									$this->redirect = url(sprintf('%s/hire/talent/four?talent_id=%s',EMPLOYER_ROLE_TYPE,$request->talent_id));
								}else{
									$this->redirect = url(sprintf('%s/hire/talent/four',EMPLOYER_ROLE_TYPE));
								}
							}
						}else{
							$this->message = trans('general.M0356');
						}
					}else{
						$this->message = sprintf(ALERT_DANGER,current($validator->errors()->all()));
					}

					break;
				} 

				case 'four':{
					if(!empty($request->startdate)){
						$request->request->add(['startdate' => ___convert_date($request->startdate,'MYSQL')]);
					}

					if(!empty($request->enddate)){
						$request->request->add(['enddate' => ___convert_date($request->enddate,'MYSQL')]);
					}
					
					$validator = \Validator::make($request->all(), [
						'startdate'                         => array_merge(['required','validate_date','validate_start_date:'.$request->enddate],validation('birthday')),
						'enddate'                           => array_merge(['required','validate_date','validate_date_type:'.$request->startdate.','.$request->employment],validation('birthday')),
						'expected_hour'                     => ["required_time:{$request->employment}"],
					],[
						'startdate.required'                => trans('general.M0146'),
						'startdate.validate_date'           => trans('general.M0434'),
						'startdate.validate_start_date'    	=> trans('general.M0535'),
						'startdate.string'                  => trans('general.M0147'),
						'startdate.regex'                   => trans('general.M0147'),
						'enddate.required'                  => trans('general.M0148'),
						'enddate.validate_date'             => trans('general.M0435'),
						'enddate.string'                    => trans('general.M0149'),
						'enddate.regex'                     => trans('general.M0149'),
						'enddate.validate_date_type'        => trans('general.M0472'),
						'expected_hour.required_time'       => trans('general.M0584')
					]);

					if($validator->passes()){
						$postjob['id_project']  	= $request->id_project;
						$postjob['startdate'] 		= $request->startdate;
						$postjob['enddate']  		= $request->enddate;
						$postjob['expected_hour']  	= $request->expected_hour;

						$daily_working_hours 	= sprintf("%s:00:00",___cache('configuration')['daily_working_hours']);
						
						if(strtotime($request->expected_hour) > strtotime($daily_working_hours)){
							$this->message = trans('general.M0524');
							$this->jsondata = (object)[
								'expected_hour' => trans('general.M0524')
							];					
						}else{
							if($request->action == 'edit'){
								$postjob['updated']     = date('Y-m-d H:i:s');
								$postjob['created']     = date('Y-m-d H:i:s');
							}else{
								$postjob['step']        = 'five';
								$postjob['status']      = 'draft';
								$postjob['user_id']     = \Auth::user()->id_user;
								$postjob['talent_id']   = !empty($request->talent_id) ? ___decrypt($request->talent_id) : '';
								$postjob['updated']     = date('Y-m-d H:i:s');
								$postjob['created']     = date('Y-m-d H:i:s');
							}

							$isProjectSaved = \Models\Projects::postjob($postjob);
							
							if(!empty($isProjectSaved)){
								$this->status   = true;
								if($request->action == 'edit'){
									$this->redirect = url(sprintf('%s/hire/talent/edit/five?job_id=%s',EMPLOYER_ROLE_TYPE,___encrypt($request->id_project)));
								}else{
									if(!empty($request->talent_id)){
										$this->redirect = url(sprintf('%s/hire/talent/five?talent_id=%s',EMPLOYER_ROLE_TYPE,$request->talent_id));
									}else{
										$this->redirect = url(sprintf('%s/hire/talent/five',EMPLOYER_ROLE_TYPE));
									}
								}
							}else{
								$this->message = trans('general.M0356');
							}
						}
					}else{
						$this->jsondata     = ___error_sanatizer($validator->errors());
					}

					break;
				}

				case 'five':{
					if(!empty(\Auth::user()->company_name)){
						/*$cards      = \Models\PaypalPayment::get_user_card(\Auth::user()->id_user,'','count',['*']);*/
						if(1/*$cards > 0*/){
							$isSubIndustryAdded     = \Models\Projects::savesubindustry($request->id_project,$request->subindustry,$request->industry_id);

							if(!empty($isSubIndustryAdded)){
								$validator = \Validator::make($request->all(), [
									'subindustry'                       => array_merge(['required'],validation('subindustry')),
									'expertise'                         => array_merge(['required'],validation('expertise')),
									'other_perks'                       => validation('other_perks'),
								],[
									'subindustry.array'                 => trans('general.M0346'),
									'subindustry.required'              => trans('general.M0142'),
									'expertise.required'                => trans('general.M0143'),
									'expertise.string'                  => trans('general.M0066'),
									'other_perks.required'              => trans('general.M0567'),
									'other_perks.numeric'              	=> trans('general.M0242'),
									'other_perks.max'                   => trans('general.M0243'),
									'other_perks.min'                   => trans('general.M0244'),
									'other_perks.regex'                 => trans('general.M0242')
								]);

								if($validator->passes()){
									$postjob['expertise']   = $request->expertise;
									$postjob['other_perks'] = $request->other_perks;
									$postjob['id_project']  = $request->id_project;
									
									if($request->action == 'edit'){
										$postjob['updated']     = date('Y-m-d H:i:s');
									}else{
										$postjob['status']      = 'active';
										$postjob['user_id']     = \Auth::user()->id_user;
										$postjob['talent_id']   = !empty($request->talent_id) ? ___decrypt($request->talent_id) : '';
										$postjob['updated']     = date('Y-m-d H:i:s');
										$postjob['created']     = date('Y-m-d H:i:s');
									}

									$isProjectSaved = \Models\Projects::postjob($postjob);

									if($request->action != 'edit'){
										/* RECORDING ACTIVITY LOG */
										event(new \App\Events\Activity([
											'user_id'           => \Auth::user()->id_user,
											'user_type'         => 'employer',
											'action'            => 'employer-post-job',
											'reference_type'    => 'projects',
											'reference_id'      => $isProjectSaved
										]));
									}

									
									if(!empty($isProjectSaved)){
										$this->status   = true;
										if($request->action == 'edit'){
											$this->redirect = url(sprintf('%s/project/details?job_id=%s',EMPLOYER_ROLE_TYPE,___encrypt($request->id_project)));

											$proposals = \Models\Proposals::defaultKeys()->with(['file'])->where('talent_proposals.status','!=','rejected')->where('project_id',$request->id_project)->get();
											
											if(!empty($proposals->count())){
												foreach($proposals as $item){
													$isNotified = \Models\Notifications::notify(
					                                    $item->user_id,
					                                    auth()->user()->id_user,
					                                    'JOB_UPDATED_BY_EMPLOYER',
					                                    json_encode([
					                                        "employer_id"   => (string) auth()->user()->id_user,
					                                        "talent_id"     => (string) $item->user_id,
					                                        "project_id"    => (string) $request->id_project,
					                                        "proposal"    	=> (string) $item->id_proposal,
					                                        "project_title" => (string) sprintf("#%'.0".JOBID_PREFIX."d",$request->id_project)
					                                    ])
					                                );
												}
											}
										}else{
				                            if($postjob['talent_id']){
				                                $isNotified = \Models\Notifications::notify(
				                                    $postjob['talent_id'],
				                                    $postjob['user_id'],
				                                    'JOB_INVITATION_SENT_BY_EMPLOYER',
				                                    json_encode([
				                                        "user_id"       => (string) $postjob['user_id'],
				                                        "talent_id"     => (string) $postjob['talent_id'],
				                                        "project_id"    => (string) $postjob['id_project']
				                                    ])
				                                );
				                            }

											$this->redirect = url(sprintf('%s/my-jobs/submitted',EMPLOYER_ROLE_TYPE));
										}
									}else{
										$this->message = trans('general.M0356');
									}
								}else{
									$this->jsondata     = ___error_sanatizer($validator->errors());
								}
							}else{
								$this->jsondata     = (object)['subindustry' => trans('general.M0583')];
							}
						}else{
							if($request->action == 'edit'){
								$redirect_url  = url(sprintf('%s/hire/talent/edit/five?job_id=%s',EMPLOYER_ROLE_TYPE,___encrypt($request->id_project)));
							}else{
								$redirect_url  = url(sprintf('%s/hire/talent/five',EMPLOYER_ROLE_TYPE));
							}

							$this->status = false;
							$this->message = sprintf(ALERT_DANGER,sprintf(trans("website.W0754"), url('employer/payment/card/manage?redirect='.urlencode($redirect_url))));
						}
					}else{
						if($request->action == 'edit'){
							$redirect_url  = url(sprintf('%s/hire/talent/edit/five?job_id=%s',EMPLOYER_ROLE_TYPE,___encrypt($request->id_project)));
						}else{
							$redirect_url  = url(sprintf('%s/hire/talent/five',EMPLOYER_ROLE_TYPE));
						}

						$this->status = false;
						$this->message = sprintf(ALERT_DANGER,sprintf(trans("website.W0819"), url(sprintf('%s/profile/edit/one?redirect=%s',EMPLOYER_ROLE_TYPE,urlencode($redirect_url)))));
					}

					break;
				}
			}

			return response()->json([
				'nomessage' => true,
				'status'    => $this->status,
				'message'   => $this->message,
				'redirect'  => $this->redirect,
				'data'      => (object)$this->jsondata,
			]);       
		}      

        /**
         * [This method is used for Delete jobs] 
         * @param  Request
         * @return Json Response
         */        

        public function delete_job(Request $request){
            $project_id = ___decrypt($request->job_id);
            if(empty($project_id)){
                $this->message = trans("general.M0121");
            }else{
                $project_detail  = \Models\Projects::defaultKeys()
            	->where('id_project',$project_id)
            	->whereNotIn('projects.status',['draft','trashed'])
            	->first();
            	
                $project_detail = json_decode(json_encode($project_detail),true);
                
                if(!empty($project_detail)){
                    if($project_detail['awarded'] === DEFAULT_NO_VALUE){
                        $postjob = [
                            'id_project' => $project_id,
                            'status'     => 'trashed'
                        ];
                        
                        $isProjectSaved = \Models\Projects::postjob($postjob);

                        /* RECORDING ACTIVITY LOG */
						event(new \App\Events\Activity([
							'user_id'           => \Auth::user()->id_user,
							'user_type'         => 'employer',
							'action'            => 'employer-delete-job',
							'reference_type'    => 'projects',
							'reference_id'      => $project_id
						]));


                        if(!empty($isProjectSaved)){
                            $this->status   = true;
                            $this->message  = trans('general.M0527');
                            $this->redirect = url(sprintf('%s/my-jobs/submitted',EMPLOYER_ROLE_TYPE));
                        }
                    }else{
                        $this->message = trans('general.M0526');
                    }
                }else{
                	$this->message = trans('website.W0684');
                }
            }

            return response()->json([
                'data'      => $this->jsondata,
                'status'    => $this->status,
                'message'   => $this->message,
                'redirect'  => $this->redirect,
            ]);
        }

        /**
         * [This method is used for cancel jobs] 
         * @param  Request
         * @return Json Response
         */        

        public function cancel_job(Request $request){
            $project_id = ___decrypt($request->job_id);
            if(empty($project_id)){
                $this->message = trans("general.M0121");
            }else{
                $project_detail  = \Models\Projects::defaultKeys()
                ->with([
                	'proposal' => function($q){
						$q->defaultKeys()->where('talent_proposals.status','accepted')->with([
							'talent' => function($q){
								$q->defaultKeys()->country()->review()->with([
									'interests'
								]);
							}
						]);
					}
            	])
            	->where('id_project',$project_id)
            	->whereNotIn('projects.status',['draft','trashed'])
            	->first();
            	
                $project_detail = json_decode(json_encode($project_detail),true);
                
                if(!empty($project_detail)){
                    if($project_detail['is_cancelled'] === DEFAULT_YES_VALUE){
                    	$this->message = trans('general.M0574');
                    }else if($project_detail['project_status'] !== 'pending'){
                    	$this->message = trans('general.M0575');
                    }else if($project_detail['awarded'] === DEFAULT_NO_VALUE){
                    	$this->message = trans('general.M0576');
                    }else if($project_detail['is_cancelable'] == DEFAULT_NO_VALUE){
                    	$this->message = trans('general.M0577');
                    }else{
                        $postjob = [
                            'id_project' 	=> $project_id,
                            'canceldate'  	=> date('Y-m-d H:i:s'),
                            'is_cancelled'  => DEFAULT_YES_VALUE
                        ];

                        $isProjectSaved = \Models\Projects::postjob($postjob);

                        /* RECORDING ACTIVITY LOG */
						event(new \App\Events\Activity([
							'user_id'           => \Auth::user()->id_user,
							'user_type'         => 'employer',
							'action'            => 'employer-cancel-job',
							'reference_type'    => 'projects',
							'reference_id'      => $project_id
						]));

                        if(!empty($isProjectSaved)){
                        	$isRefunded = \Models\Payments::cancel_refund($project_detail['id_project'],$project_detail['company_id'],$project_detail['proposal']['id_proposal']);
                        	$isNotified = \Models\Notifications::notify(
                                $project_detail['proposal']['talent']['id_user'],
                                $project_detail['company_id'],
                                'JOB_CANCELLED_BY_EMPLOYER',
                                json_encode([
                                    "employer_id"   => (string) $project_detail['company_id'],
                                    "talent_id"     => (string) $project_detail['proposal']['talent']['id_user'],
                                    "project_id"    => (string) $project_detail['id_project']
                                ])
                            );

                            $this->status   = true;
                            $this->message  = trans('general.M0579');
                            $this->redirect = url(sprintf('%s/my-jobs/completed',EMPLOYER_ROLE_TYPE));
                        }
                    }
                }else{
                	$this->message = trans('website.W0684');
                }
            }

            return response()->json([
                'data'      => $this->jsondata,
                'status'    => $this->status,
                'message'   => $this->message,
                'redirect'  => $this->redirect,
            ]);
        }	

		/**
         * [This method is used for view of change password]
         * @param  Request
         * @return \Illuminate\Http\Response
         */
        
        public function change_password(Request $request){
            $data['title']       = trans('website.W0480');
            $data['subheader']   = 'employer.includes.top-menu';
            $data['header']      = 'innerheader';
            $data['footer']      = 'innerfooter';
            $data['view']        = 'employer.settings.changepassword';

            $data['user']        = \Models\Employers::get_user(\Auth::user());
            
            return view('employer.settings.index')->with($data);
        }  

        /**
         * [This method is used for handling of change password]
         * @param  Request
         * @return Json Response
         */
        
        public function __change_password(Request $request){
            $validator = \Validator::make($request->all(), [
                "old_password"              => validation('old_password'),
                "new_password"              => validation('new_password'),
            ],[
                'old_password.required'     => trans('general.M0292'),
                'old_password.old_password' => trans('general.M0295'),
                'new_password.different'    => trans('general.M0300'),
                'new_password.required'     => trans('general.M0293'),
                'new_password.regex'        => trans('general.M0296'),
                'new_password.max'          => trans('general.M0297'),
                'new_password.min'          => trans('general.M0298'),
                'confirm_password.required' => trans('general.M0294'),
                'confirm_password.same'     => trans('general.M0299'),
            ]);

            if($validator->passes()){
                $isUpdated      = \Models\Employers::change(\Auth::user()->id_user,[
                    'password'  		=> bcrypt($request->new_password),
                    'api_token'  		=> bcrypt(__random_string()),
                    'updated'   		=> date('Y-m-d H:i:s')
                ]);
                
                $this->status   = true;
                $this->message  = sprintf(ALERT_SUCCESS,trans("general.M0301"));
                $this->redirect = url('/logout');
                       
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

        /**
         * [This method is used for vieww of setting]
         * @param  Request
         * @return \Illuminate\Http\Response
         */
        public function notificationsettings(Request $request){
            $data['title']                  = trans('website.W0306');
            $data['subheader']              = 'employer.includes.top-menu';
            $data['header']                 = 'innerheader';
            $data['footer']                 = 'innerfooter';
            $data['view']                   = 'employer.settings.notification';

            $data['user']                   = \Models\Employers::get_user(\Auth::user());
            $data['settings']               = \Models\Settings::fetch(\Auth::user()->id_user,\Auth::user()->type);
            $data['industries_name']        = \Cache::get('industries_name');
            $data['subindustries_name']     = \Cache::get('subindustries_name');
            return view('employer.settings.index')->with($data);
        }

        /**
         * [This method is used for handling of setting]
         * @param  Request
         * @return Json Response
         */
        
        public function __notificationsettings(Request $request){
        	$setting 			= \Models\Settings::fetch(auth()->user()->id_user,auth()->user()->type);
            $isUpdated          = \Models\Settings::add(auth()->user()->id_user,$request,$setting);
            
            /* RECORDING ACTIVITY LOG */
            event(new \App\Events\Activity([
                'user_id'           => \Auth::user()->id_user,
                'user_type'         => 'employer',
                'action'            => 'employer-save-settings',
                'reference_type'    => 'users',
                'reference_id'      => \Auth::user()->id_user
            ]));
            
            $this->status   = true;
            $this->message  = sprintf(ALERT_SUCCESS,trans("general.M0302"));
            $this->redirect = false;
            
            return response()->json([
                'data'      => $this->jsondata,
                'status'    => $this->status,
                'message'   => $this->message,
                'redirect'  => $this->redirect,
            ]);
        }


        /**
         * [This method is used for rendering view social settings] 
         * @param  null
         * @return \Illuminate\Http\Response
         */
        
        public function socialsettings(){
            $data['title']                  = trans('website.W0459');
            $data['subheader']              = 'employer.includes.top-menu';
            $data['header']                 = 'innerheader';
            $data['footer']                 = 'innerfooter';
            
            $data['submenu']                = 'profile';
            $data['view']                   = 'employer.settings.social';
            $data['user']                   = \Models\Employers::get_user(\Auth::user());
            
            return view('employer.settings.index')->with($data);
        }

        /**
         * [This method is used for rendering view social settings] 
         * @param  null
         * @return \Illuminate\Http\Response
         */
        
        public function __socialsettings(Request $request){
            if(!empty($request->socialkey)){
                $socialkey = $request->socialkey;

                $isUpdated = \Models\Employers::change(\Auth::user()->id_user, [$socialkey => NULL, 'updated' => date('Y-m-d H:i:s')]);

                if(!empty($isUpdated)){
                    $this->status = true;
                }
            }

            return response()->json([
                'data'      => $this->jsondata,
                'status'    => $this->status,
                'message'   => $this->message,
                'redirect'  => $this->redirect,
            ]);  
        }

		/**
		 * [This method is used for currency exchange rate]
		 * @param  Request
		 * @return \Illuminate\Http\Response
		 */
		

        public function currency_exchange(Request $request){
            $data['title']                  = trans('website.W0459');
            $data['subheader']              = 'employer.includes.top-menu';
            $data['header']                 = 'innerheader';
            $data['footer']                 = 'innerfooter';
            
            $data['submenu']                = 'profile';
            $data['view']                   = 'employer.settings.currency';
            $data['user']                   = \Models\Employers::get_user(\Auth::user());
            $data['currency']   			=  json_decode(json_encode(\Models\Currency::getCurrencyList()),true);
            
            return view('employer.settings.index')->with($data);   
        }

        /**
         * [This method is used for rendering view invite talent] 
         * @param  null
         * @return \Illuminate\Http\Response
         */
        
        public function invite_talent(){
            $data['title']                  = trans('website.W0692');
            $data['subheader']              = 'employer.includes.top-menu';
            $data['header']                 = 'innerheader';
            $data['footer']                 = 'innerfooter';
            
            $data['submenu']                = 'profile';
            $data['view']                   = 'employer.settings.invite-talent';
            $data['user']                   = \Models\Employers::get_user(\Auth::user());
            
            return view('employer.settings.index')->with($data);
        }

        /**
         * [This method is used for rendering view invite talent] 
         * @param  null
         * @return \Illuminate\Http\Response
         */        

        public function __invitetalent(Request $request){
			$validator = \Validator::make($request->all(), [
					'email'                     => ['required','email']
				],[
					'email.required'            => trans('general.M0010'),
                	'email.email'               => trans('general.M0011'),
                	'email.exists'              	=> trans('general.M0047'),
			]);
			
			$talent = json_decode(json_encode(\Models\Talents::findByEmail($request->email)),true);

			$validator->after(function($validator) use($request, $talent){
				if(empty($talent)){
					$validator->errors()->add('email', trans('general.M0513'));
				}else{
					if($talent['type'] != 'talent'){
						$validator->errors()->add('email', trans('general.M0513'));
					}else{
						$invited_talent = json_decode(json_encode(\Models\InviteTalent::where('talent_id',$talent['id_user'])->where('employer_id',\Auth::user()->id_user)->get()),true);
						if(!empty($invited_talent)){
							$validator->errors()->add('email', trans('general.M0514'));
						}
					}
				}
			});

			if($validator->fails()){
				$this->status = true;
				$this->message = $validator->errors()->first();
			}else{
				$code 					= strtoupper(__random_string(4));
				$emailData              = ___email_settings();
				$emailData['name']      = $request->first_name;
				$emailData['email']     = $request->email;
				$emailData['code']      = $code;

				___mail_sender($request->email,sprintf('%s %s',$talent['first_name'], $talent['last_name']),'invite_talent_event',$emailData);
				
				$invite_talent_array = [
					'employer_id' 	=> \Auth::user()->id_user,
					'talent_id' 	=> $talent['id_user'],
					'code' 			=> $code,
					'status' 		=> 'pending',
					'created' 		=> date('Y-m-d H:i:s'),
					'updated' 		=> date('Y-m-d H:i:s')
				];

				\Models\InviteTalent::insert($invite_talent_array);
				/* RECORDING ACTIVITY LOG */
				event(new \App\Events\Activity([
					'user_id'           => \Auth::user()->id_user,
					'user_type'         => 'employer',
					'action'            => 'invite-talent',
					'reference_type'    => 'talent',
					'reference_id'      => $talent['id_user']
				]));
								
				$this->status   = true;
				$this->message  = sprintf(ALERT_SUCCESS,trans("website.W0693"));               
			}

			return response()->json([
				'data'      => $this->jsondata,
				'status'    => $this->status,
				'message'   => $this->message,
				'redirect'  => $this->redirect,
			]);     	
    	}

		/**
		 * [This method is used for employer jobs]
		 * @param  Request
		 * @return \Illuminate\Http\Response
		 */
		
		public function myjobs(Request $request, Builder $htmlBuilder, $type = 'submitted'){
			$request->request->add(['currency' => \Session::get('site_currency')]);
			$request['language'] = \App::getLocale();
			$data['subheader']          = 'employer/includes/top-menu';
			$data['title']              = trans('website.W0472');
			$data['header']             = 'innerheader';
			$data['footer']             = 'innerfooter';
			$data['view']               = 'employer.myjob.view';
			$data['user']               = \Models\Employers::get_user(\Auth::user());
			$data['type']               = 'my_jobs';
			
			if($request->ajax()){
				$projects = \Models\Projects::employer_jobs(\Auth::user());
				if($type == 'current'){
					$projects->withCount([
						'proposals',
						'proposal' => function($q){
							$q->where('status','accepted');
						}
					])
					->having('proposal_count','>','0')
					->havingRaw("(project_status = 'initiated' OR project_status = 'completed')")
					->having('is_cancelled','=',DEFAULT_NO_VALUE);
				}else if($type == 'scheduled'){
					$projects->withCount([
						'proposals',
						'proposal' => function($q){
							$q->where('status','accepted');
						}
					])
					->having('proposal_count','>','0')
					->having('project_status','=','pending')
					->having('is_cancelled','=',DEFAULT_NO_VALUE);
				}else if($type == 'completed'){
					$projects->withCount([
						'proposals',
						'proposal' => function($q){
							$q->where('status','accepted');
						}
					])
					->having('proposal_count','>','0')
					->havingRaw("(project_status = 'closed' OR is_cancelled = '".DEFAULT_YES_VALUE."' )");
				}else{
					$projects->withCount(['proposals'])->where('awarded','=',DEFAULT_NO_VALUE)
                	->having('is_cancelled','=',DEFAULT_NO_VALUE);
				}

				$projects->orderBy("projects.created","DESC");
				$projects = $projects->groupBy(['projects.id_project'])->get();
				
				return \Datatables::of($projects)->filter(function ($instance) use($request){
					if ($request->has('search')) {
						if(!empty($request->search['value'])){
							$instance->collection = $instance->collection->filter(function ($row) use ($request) {
								return (\Str::contains($row->title, $request->search['value']) || \Str::contains($row->company_name, $request->search['value']) || \Str::contains($row->description, $request->search['value'])) ? true : false;
							});
						} 
					}
				})
				->editColumn('title',function($project){
					return get_myproject_template($project,'employer');
				})
				->make(true);
			}

			$data['html'] = $htmlBuilder
			->parameters([
				"dom" => "<'row' <'col-md-3'f><'col-md-3'><'col-md-6 filter-option'>>rt<'row' <'col-md-6'i><'col-md-6'p>>",
				"language" => [
					"sInfo" => "Showing _START_ - _END_ out of _TOTAL_ record(s)",
					"sInfoEmpty"=> "No record(s) found. ",
				]
			])
			->addColumn(['data' => 'title', 'name' => 'title', 'title' => '&nbsp;', 'width' => '0', 'searchable' => false, 'orderable' => false]);

			return view('employer.myjob.index')->with($data);
		}

		/**
		 * [This method is used for rendering view of profile view] 
		 * @param  null
		 * @return \Illuminate\Http\Response
		 */
		public function view_profile(){
			$data['subheader']              = 'employer.includes.top-menu';
			$data['title']                  = trans('website.W0583');
			$data['header']                 = 'innerheader';
			$data['footer']                 = 'innerfooter';
			$data['view']                   = 'employer.viewprofile.view';

			$data['submenu']                = 'profile';
			$data['user']                   = \Models\Employers::get_user(\Auth::user());
			return view('employer.viewprofile.index')->with($data);
		}

		/**
		 * [This method is used for reviews]
		 * @param  Request
		 * @return \Illuminate\Http\Response
		 */

		public function view_reviews(Request $request, Builder $htmlBuilder){
			$data['subheader']              = 'employer.includes.top-menu';
			$data['title']                  = trans('website.W0584');
			$data['header']                 = 'innerheader';
			$data['footer']                 = 'innerfooter';
			$data['view']                   = 'employer.viewprofile.reviews';

			$data['user']                   = \Models\Employers::get_user(\Auth::user());
			if ($request->ajax()) {
				$reviews    = \Models\Reviews::listing('receiver',$data['user']['id_user']);
				
				return \Datatables::of($reviews)
				->editColumn('review',function($item){
					$html ='<div class="review-content-block clearfix">';
						$html .='<div class="review-content-display"><img src="'.asset($item->receiver_picture).'" /></div>';
						$html .='<div class="review-content-info">';                            
							$html .='<h4>'.$item->sender_name.'</h4>';
							$html .='<div class="rating-review"><span class="rating-block">'.___ratingstar($item->review_average).'</span></div>';
						$html .='</div>';
					$html .='</div>'; 
					$html .='<div class="review-desc"><p>'.$item->description.'</p></div>';
					return $html;
				})
				->make(true);
			}

			$data['html'] = $htmlBuilder
			->parameters(["dom" => "<'row' <'col-md-6 table-heading'> <'col-md-3'> <'col-md-3 filter-option'>> rt <'row'<'col-md-6'><'col-md-6'p> >"])
			->addColumn(['data' => 'review', 'name' => 'review', 'title' => '&nbsp;', 'width' => '0', 'searchable' => false, 'orderable' => false]);            

			return view('employer.viewprofile.index')->with($data);
		}

        /**
         * [This method is used for rendering view of notification] 
         * @param  null
         * @return \Illuminate\Http\Response
         */
        
        public function view_notifications(Request $request, Builder $htmlBuilder){
            $data['subheader']              = 'employer.includes.top-menu';
            $data['title']                  = trans('website.W0585');
            $data['header']                 = 'innerheader';
            $data['footer']                 = 'innerfooter';
            $data['view']                   = 'employer.viewprofile.notification';

            $data['user']                   = \Models\Employers::get_user(\Auth::user());
            $data['submenu']                = 'notifications';
			
			$notifications      			= \Models\Notifications::lists(\Auth::user()->id_user);
			
			if ($request->ajax()) {
				return \Datatables::of($notifications['result_object'])
				->editColumn('notification',function($item){
					$html = ''; $style = '';
					
					if($item->notification_status == 'unread'){
						$style = ' style="background: #f9f9f9;"';
					}

					$html .= '<li class="btn-block notification-item"'.$style.'>';
						$html .= '<a href="javascript:void(0);" data-request="inline-ajax" data-url="'.url(sprintf('%s/notifications/mark/read?notification_id=%s',EMPLOYER_ROLE_TYPE,$item->id_notification)).'" class="submenu-block clearfix '.$item->notification_status.'">';
							$html .= '<span class="submenublock-user"><img src="'.$item->sender_picture.'" /></span>';
							$html .= '<span class="submenublock-info">';
								$html .= '<h4>'.$item->sender_name.' <span>'.___ago($item->created).'</span></h4>';
								$html .= '<p>'.trans('notification.'.$item->notification).'</p>';
							$html .= '</span>';
						$html .= '</a>';
					$html .= '</li>';
					return $html;
				})
				->make(true);
			}
			$data['html'] = $htmlBuilder
			->parameters(["dom" => "<'row' <'col-md-6 table-heading'> <'col-md-3'> <'col-md-3 filter-option'>> rt <'row'<'col-md-6'><'col-md-6'p> >"])
			->addColumn(['data' => 'notification', 'name' => 'notification', 'title' => '&nbsp;', 'width' => '0', 'searchable' => false, 'orderable' => false]);			            
            return view('employer.viewprofile.index')->with($data);
        }


	   /**
		 * [This method is used for rendering view of user's] 
		 * @param  null
		 * @return \Illuminate\Http\Response
		 */

		public function find_talents(Request $request){

			$request->request->add(['currency' => \Session::get('site_currency')]);
			$data['currency'] 				= \Session::get('site_currency');
			$data['subheader']              = 'employer.includes.top-menu';
			$data['title']                  = trans('website.W0586');
			$data['header']                 = 'innerheader';
			$data['footer']                 = 'innerfooter';
			$data['view']                   = 'employer.job.find-talent';

			$data['user']                   = \Models\Employers::get_user(\Auth::user());
			$data['skills']                 = \Cache::get('skills');
			$data['search']                 = (!empty($request->search))?$request->search:"";

			/*set currency by user location*/
			// $data['user_set_currency']  = ip_info($_SERVER["REMOTE_ADDR"], "Country Code");
			// $data['user_set_currency']  = ip_info('115.249.91.203', "Country Code");
			\Session::set('site_currency',\Session::get('site_currency'));
			
			/* RECORDING ACTIVITY LOG */
			event(new \App\Events\Activity([
				'user_id'           => \Auth::user()->id_user,
				'user_type'         => 'employer',
				'action'            => 'find-talents',
				'reference_type'    => 'users',
				'reference_id'      => \Auth::user()->id_user
			]));

			return view('employer.job.template')->with($data);
		}

		/**
		 * [This method is used for filtering user's ]
		 * @param  Request
		 * @return String (Print a string)
		 */
		
		public function _find_talents(Request $request){
			$request->request->add(['currency' => \Session::get('site_currency')]);
			
			$this->status   		= true;
			$html                   = "";
           

			$talents =  \Models\Employers::find_talents(\Auth::user(),$request);
			if(!empty($talents['result'])){
				foreach($talents['result'] as $keys => &$item){
					if(!empty($item['get_company'])){
						$item['talent_company_country'] = \DB::table('firm_jurisdiction as fj')->join('countries as c','c.id_country','fj.country_id')->select(\DB::Raw("IF(({$this->language} != ''),GROUP_CONCAT(`{$this->language}`), GROUP_CONCAT(`en`)) as country_name"))->where('company_id',$item['get_company'][0]['talent_company_id'])->first();
					}else{
						$item['talent_company_country'] =  "N/A";
					}
					$html .= get_talent_template($item);
				}
			}else{
				$html .= '<div class="no-records-found">'.trans('website.W0750').'</div>';
			}

			if($talents['total_filtered_result'] == DEFAULT_PAGING_LIMIT){
				$load_more = '<button type="button" class="btn btn-default btn-block btn-lg" data-request="filter-paginate" data-url="'.url(sprintf('%s/_find-talents',EMPLOYER_ROLE_TYPE)).'" data-target="#talent_listing" data-showing="#paginate_showing" data-loadmore="#loadmore" data-form="[role=\'find-talents\']">'.trans('website.W0254').'</button>';
				$can_load_more = true;
			}else{
				$load_more = '<button type="button" class="btn btn-default btn-block btn-lg hide" data-request="filter-paginate" data-url="'.url(sprintf('%s/_find-talents',EMPLOYER_ROLE_TYPE)).'" data-target="#talent_listing" data-showing="#paginate_showing" data-loadmore="#loadmore" data-form="[role=\'find-talents\']">'.trans('website.W0254').'</button>';
				$can_load_more = false;
			}

			echo json_encode(
				array(
					"filter_title"      => sprintf(trans('general.M0196'),$talents['total_filtered_result']),
					"paging"            => ($request->page == 1)?false:true,
					"recordsFiltered"   => $talents['total_filtered_result'],
					"recordsTotal"      => $talents['total'],
					"loadMore"          => $load_more, 
					"data"              => $html,
					"can_load_more"     => $can_load_more,
				)
			);
		}

		/**
		 * [This method is used for user's profile]
		 * @param  Request
		 * @return \Illuminate\Http\Response
		 */
		
		public function talent_profile(Request $request, Builder $htmlBuilder){
			$request->request->add(['currency' => \Session::get('site_currency')]);

			if(!empty($request->talent_id)){
				$talent_id = ___decrypt($request->talent_id);
			}

			if(empty($talent_id)){
				return redirect(sprintf('%s/find-talents',EMPLOYER_ROLE_TYPE));
			}
			
			$data['title']                  = trans('website.W0587');
			$data['subheader']              = 'employer.includes.top-menu';
			$data['header']                 = 'innerheader';
			$data['footer']                 = 'innerfooter';
			$data['view']                   = 'employer.talent.profile';

			$data['submenu']                = 'profile';
			$data['talent_id']              = ___encrypt($talent_id);
			$data['user']                   = \Models\Employers::get_user(\Auth::user(),true);
			$data['talent']                 = \Models\Talents::get_user((object)['id_user' => $talent_id],true);
			$data['talent']['is_saved'] 	= \Models\Employers::is_talent_saved(\Auth::user()->id_user,$talent_id);

			
			$data['ownerDetail'] = $ownerDetail	= \Models\companyConnectedTalent::where('id_user',$talent_id)->where('user_type','owner')->first();

			if(count($ownerDetail)>0){
				$data['talent']['connectedTalent']	= \Models\companyConnectedTalent::where('id_talent_company',$ownerDetail->id_talent_company)->where('user_type','user')->count();
				$firm_juri	=	\DB::table('firm_jurisdiction as fj')->join('countries as c','c.id_country','fj.country_id')->select(\DB::Raw("IF(({$this->language} != ''),GROUP_CONCAT(`{$this->language}`), GROUP_CONCAT(`en`)) as country_name"))->where('company_id',$ownerDetail->id_talent_company)->first();
				$data['talent']['countries'] = @$firm_juri->country_name;

				if($data['talent']['talentCompany']->company_logo ==  null){
					$data['talent']['talentCompany']->company_logo = asset(sprintf('images/%s',DEFAULT_AVATAR_IMAGE));
				}
				
			}else{
				
				$data['talent']['connectedTalent']	= '0';
			}

			$viewed_talent                  = [
				'employer_id'   => \Auth::user()->id_user,
				'talent_id'     => $talent_id,
				'updated'       => date('Y-m-d h:i:s'),
				'created'       => date('Y-m-d h:i:s')
			];

			$data['last_viewed'] = ___ago(\Models\ViewedTalents::add_viewed_talent($viewed_talent));

			/* RECORDING ACTIVITY LOG */
			event(new \App\Events\Activity([
				'user_id'           => \Auth::user()->id_user,
				'user_type'         => 'employer',
				'action'            => 'talent-profile',
				'reference_type'    => 'talent',
				'reference_id'      => $talent_id
			]));

			if ($request->ajax()) {
				$talent = (object)['id_user' => $talent_id];
				$work_histories = \Models\Projects::talent_jobs($talent)->with(['proposal'])
	            ->whereHas('proposal',function($q) use($talent){
	                $q->where('talent_proposals.status','accepted');
	                $q->where('talent_proposals.user_id',$talent->id_user);
	            })
	            ->having('project_status','=','closed')
	            ->orderBy("projects.created","DESC")
	            ->groupBy(['projects.id_project'])
	            ->get();

				return \Datatables::of($work_histories)->filter(function ($work_history) use($request){
					if ($request->has('search')) {
						if(!empty($request->search['value'])){
							$instance->collection = $instance->collection->filter(function ($row) use ($request) {
								return (\Str::contains($row->title, $request->search['value']) || \Str::contains($row->company_name, $request->search['value']) || \Str::contains($row->quoted_price, $request->search['value'])) ? true : false;
							});
						} 
					}
				})
				->editColumn('title',function($work_history){
					return get_project_small_template($work_history,'employer');
				})
				->make(true);
			}
			$data['completed_jobs'] = true;
			$data['html'] = $htmlBuilder
			->parameters([
				"dom" => "<'row' <'col-md-3'f><'col-md-3'><'col-md-6 filter-option'>>rt<'row' <'col-md-6'i><'col-md-6'p>>",
			])
			->addColumn(['data' => 'title', 'name' => 'title', 'title' => '&nbsp;', 'width' => '0', 'searchable' => false, 'orderable' => false]);

			return view('employer.talent.index')->with($data);
		}

		/**
		 * [This method is used for user's availability]
		 * @param  Request
		 * @return \Illuminate\Http\Response
		 */

		public function talent_availability(Request $request, Builder $htmlBuilder){
			if(!empty($request->talent_id)){
				$talent_id = ___decrypt($request->talent_id);
			}

			if(empty($talent_id)){
				return redirect(sprintf('%s/find-talents',EMPLOYER_ROLE_TYPE));
			}

			$data['title']                  = trans('website.W0588');
			$data['subheader']              = 'employer.includes.top-menu';
			$data['header']                 = 'innerheader';
			$data['footer']                 = 'innerfooter';
			$data['view']                   = 'employer.talent.availability'; 
			$data['talent_id']              = ___encrypt($talent_id);
		
			$data['user']                   = \Models\Employers::get_user(\Auth::user(),true);
			$data['talent']                 = \Models\Talents::get_user((object)['id_user' => $talent_id],true);
			
			$data['ownerDetail'] = $ownerDetail	= \Models\companyConnectedTalent::where('id_user',\Auth::User()->id_user)->where('user_type','owner')->first();

			if($ownerDetail != null){
				$data['talent']['connectedTalent']	= \Models\companyConnectedTalent::where('id_talent_company',$ownerDetail->id_talent_company)->where('user_type','user')->count();
				$data['talent']['firm_juri']	=	\Models\FirmJurisdiction::select('country_id')->where('user_id',$talent_id)->get();
				$countries = [];
				foreach ($data['talent']['firm_juri'] as $key => $value) {
					$countries[] = \Cache::get('countries')[$value->country_id];
				}
				$data['talent']['countries'] = implode(',', $countries);
			}else{
				
				$data['talent']['connectedTalent']	= '0';
			}

			$viewed_talent                  = [
				'employer_id'   => \Auth::user()->id_user,
				'talent_id'     => $talent_id,
				'updated'       => date('Y-m-d h:i:s'),
				'created'       => date('Y-m-d h:i:s')
			];

			$data['last_viewed']            = ___ago(\Models\ViewedTalents::add_viewed_talent($viewed_talent));
			
			/* RECORDING ACTIVITY LOG */
			event(new \App\Events\Activity([
				'user_id'           => \Auth::user()->id_user,
				'user_type'         => 'employer',
				'action'            => 'talent-availability',
				'reference_type'    => 'talent',
				'reference_id'      => $talent_id
			]));

			if ($request->ajax()) {
				$work_histories = \Models\Employers::talent_work_history($talent_id);
				return \Datatables::of($work_histories)->filter(function ($work_history) use($request){
					if ($request->has('search')) {
						if(!empty($request->search['value'])){
							$instance->collection = $instance->collection->filter(function ($row) use ($request) {
								return (\Str::contains($row->title, $request->search['value']) || \Str::contains($row->company_name, $request->search['value']) || \Str::contains($row->quoted_price, $request->search['value'])) ? true : false;
							});
						} 
					}
				})
				->editColumn('title',function($work_history){
					return get_project_template($work_history);
				})
				->make(true);
			}

			$data['html'] = $htmlBuilder
			->parameters([
				"dom" => "<'row' <'col-md-3'f><'col-md-3'><'col-md-6 filter-option'>>rt<'row' <'col-md-6'i><'col-md-6'p>>",
			])
			->addColumn(['data' => 'title', 'name' => 'title', 'title' => '&nbsp;', 'width' => '0', 'searchable' => false, 'orderable' => false]);

			return view('employer.talent.index')->with($data);
		}

		/**
		 * [This method is used for reviewing user's]
		 * @param  Request
		 * @return \Illuminate\Http\Response
		 */
		
		public function talent_reviews(Request $request, Builder $htmlBuilder){
			if(!empty($request->talent_id)){
				$talent_id = ___decrypt($request->talent_id);
			}

			if(empty($talent_id)){
				return redirect(sprintf('%s/find-talents',EMPLOYER_ROLE_TYPE));
			}

			$data['title']                  = trans('website.W0590');
			$data['subheader']              = 'employer.includes.top-menu';
			$data['header']                 = 'innerheader';
			$data['footer']                 = 'innerfooter';
			$data['view']                   = 'employer.talent.reviews';

			$data['submenu']                = 'reviews';
			$data['top_talent_user']        = \Models\Talents::top_talent_user($talent_id);
			$data['talent_id']              = ___encrypt($talent_id);
			$data['user']                   = \Models\Employers::get_user(\Auth::user(),true);
			$data['talent']                 = \Models\Talents::get_user((object)['id_user' => $talent_id],true);

			$data['ownerDetail'] = $ownerDetail	= \Models\companyConnectedTalent::where('id_user',$talent_id)->where('user_type','owner')->first();

			if(count($ownerDetail)>0){
				$data['talent']['connectedTalent']	= \Models\companyConnectedTalent::where('id_talent_company',$ownerDetail->id_talent_company)->where('user_type','user')->count();
				$firm_juri	=	\DB::table('firm_jurisdiction as fj')->join('countries as c','c.id_country','fj.country_id')->select(\DB::Raw("IF(({$this->language} != ''),GROUP_CONCAT(`{$this->language}`), GROUP_CONCAT(`en`)) as country_name"))->where('company_id',$ownerDetail->id_talent_company)->first();
				$data['talent']['countries'] = @$firm_juri->country_name;

				if($data['talent']['talentCompany']->company_logo ==  null){
					$data['talent']['talentCompany']->company_logo = asset(sprintf('images/%s',DEFAULT_AVATAR_IMAGE));
				}
				
			}else{
				
				$data['talent']['connectedTalent']	= '0';
			}


			$viewed_talent                  = [
				'employer_id'   => \Auth::user()->id_user,
				'talent_id'     => $talent_id,
				'updated'       => date('Y-m-d h:i:s'),
				'created'       => date('Y-m-d h:i:s')
			];
						
			$data['last_viewed']            = ___ago(\Models\ViewedTalents::add_viewed_talent($viewed_talent));
			
			$data['isOwner'] 		= \Models\companyConnectedTalent::with(['user'])->where('id_user',$talent_id)->where('user_type','owner')->get()->first();
			$data['isOwner'] 		= json_decode(json_encode($data['isOwner']));

			if(!empty($data['isOwner'])){
				$data['connected_user'] = \Models\companyConnectedTalent::select('id_user')->where('id_talent_company',$data['isOwner']->id_talent_company)->where('user_type','user')->get();
				$data['connected_user'] = json_decode(json_encode($data['connected_user']),true);
			}else{
				$data['connected_user'] = [];
			}
			$talent_ids[] = $talent_id;
			$user_ids = array_column($data['connected_user'], 'id_user');
			$user_ids = array_merge($user_ids,$talent_ids);

			if ($request->ajax()) {
				$reviews    = \Models\Reviews::defaultKeys()->with([
					'sender' => function($q){
						$q->select(
							'id_user'
						)->name()->companyLogo();
					},
					'receiver' => function($q){
						$q->select(
							'id_user'
						)->name()->companyLogo();
					}
				// ])->where('receiver_id',$talent_id)->orderBy('id_review','DESC')->get();
				])->whereIn('receiver_id',$user_ids)->orderBy('id_review','DESC')->get();


				return \Datatables::of($reviews)
				->editColumn('review',function($item){
					$html ='<div class="review-content-block clearfix">';
						$html .='<div class="review-content-display"><img src="'.asset($item->sender->company_logo).'" /></div>';
						$html .='<div class="review-content-info">';                            
							$html .='<h4>'.$item->sender->name.' <span class="pull-right review-figure">'.___ratingstar($item->review_average).'</span></h4>';
							$html .='<p>'.$item->description.'</p>';
							$html .='<span class="review-time">'.trans('general.M0177').' '.___ago($item->created).'</span>';
						$html .='</div>';
					$html .='</div>'; 
					return $html;
				})
				->make(true);
			}

			$data['html'] = $htmlBuilder
			->parameters(["dom" => "<'row' <'col-md-6 table-heading'> <'col-md-3'> <'col-md-3 filter-option'>> rt <'row'<'col-md-6'><'col-md-6'p> >"])
			->addColumn(['data' => 'review', 'name' => 'review', 'title' => '&nbsp;', 'width' => '0', 'searchable' => false, 'orderable' => false]);

			return view('employer.talent.index')->with($data);
		}

		/**
		 * [This method is used for Proposal]
		 * @param  Request
		 * @return \Illuminate\Http\Response
		 */
		
		public function projectproposals(Request $request, Builder $htmlBuilder){
			$data['title']                  = trans('website.W0591');
			$data['subheader']              = 'employer.includes.top-menu';
			$data['header']                 = 'innerheader';
			$data['footer']                 = 'innerfooter';
			$data['view']                   = 'employer.jobdetail.jobs';
			$data['user']                   = \Models\Employers::get_user(\Auth::user());
			
			if($request->ajax()){
				$proposals =  \Models\Projects::defaultKeys()->withCount(['proposals'])
				->where('user_id',\Auth::user()->id_user)
				->whereNotIn('projects.status',['draft','trashed'])
				->where("projects.is_cancelled",DEFAULT_NO_VALUE)->get();
				
				return \Datatables::of($proposals)->filter(function ($instance) use ($request) {
					if ($request->has('search')) {
						if(!empty($request->search['value'])){
							$instance->collection = $instance->collection->filter(function ($row) use ($request) {
								return (\Str::contains(strtolower($row->title), strtolower($request->search['value']))) ? true : false;
							});
						} 
					}

					if ($request->has('sort')) {
						if(!empty($request->sort)){
							$sort = explode(" ", ___decodefilter($request->sort));
							
							if(count($sort) == 2){
								$sort_key = (!empty($sort[0]))?$sort[0]:false;
								if($sort[1] == "ASC"){
									$instance->collection = $instance->collection->sortBy(function ($row) use ($sort, $sort_key) {
										return (!empty($row->$sort_key))? $row->$sort_key: false;
									}); 
								}else if($sort[1] == "DESC"){
									$instance->collection = $instance->collection->sortByDesc(function ($row) use ($sort, $sort_key) {
										return (!empty($row->$sort_key))? $row->$sort_key: false;
									});
								}
							}
						}
					}else{
						$instance->collection = $instance->collection->sortByDesc(function ($row){
							return $row->created;
						});
					}

					if ($request->has('filter')) {
						if($request->filter){
							$instance->collection = $instance->collection->filter(function ($row) use ($request) {
								return ($row->project_status == $request->filter) ? true : false;
							});
						} 
					}
				})
				->editColumn('title',function($project){
					$html ='<div class="content-box all-proposal-box" style="margin-bottom: 0;">';
						$html .='<div class="content-box-header clearfix">';
							$html .='<div class="contentbox-header-title">';
								$html .='<h3><a href="'.url(sprintf('%s/project/proposals/detail?id_project=%s',EMPLOYER_ROLE_TYPE,___encrypt($project->id_project))).'">'.___ellipsis($project->title,50).'</a></h3>';
									$html .='<span class="label-green color-grey pull-right m-t-15">'.trans('website.'.$project->project_status).'</span>';
							$html .='</div>';
							$html .='<div class="contentbox-price-range proposal-activity">';
								$html .='<span><b>'.trans('website.W0712').'</b> '.$project->proposals_count.'</span>';
								$html .='<span><b>'.trans('website.W0711').'</b> '.___d($project->created).'</span>';
							$html .='</div>';
						$html .='</div>';
					$html .='</div>';

					return $html;
				})
				->make(true);
			}

			$data['html'] = $htmlBuilder
			->parameters(["dom" => "<'row' <'col-md-3 col-sm-4 col-xs-12'f><'col-md-3 col-sm-4 col-xs-6 sort-option'><'col-md-3 col-sm-4 col-xs-6 filter-option'>>rt<'row' <'col-md-6'i><'col-md-6'p>>"])
			->addColumn(['data' => 'title', 'name' => 'title', 'title' => '&nbsp;', 'width' => '0', 'searchable' => false, 'orderable' => false]);

			return view('employer.jobdetail.index')->with($data);
		} 

		/**
		 * [This method is used for Proposal in detail]
		 * @param  Request
		 * @return \Illuminate\Http\Response
		 */
		
		public function proposal_details(Request $request){
			$request->request->add(['currency' => \Session::get('site_currency')]);
			$data['subheader']              = 'employer.includes.top-menu';
			$data['header']                 = 'innerheader';
			$data['footer']                 = 'innerfooter';
			$data['view']                   = 'employer.jobdetail.proposal';

			$proposal_id                    = ___decrypt($request->proposal_id);
			$project_id                    	= ___decrypt($request->project_id);

			$data['user']                   = \Models\Employers::get_user(\Auth::user());
			$data['proposal'] 				= \Models\Talents::defaultKeys()
			->country()
			->review()
			->isTalentSavedEmployer(auth()->user()->id_user)
			->isTalentViewedEmployer(auth()->user()->id_user)
			->talentProposals($project_id)
			->talentProject($project_id)
			->where('id_proposal',$proposal_id)
			->groupBy(['users.id_user'])
			->get()
			->first();

			$data['project']                = \Models\Projects::defaultKeys()->withCount([
				'proposal' => function($q) use($proposal_id){
					$q->whereIn('talent_proposals.status',['accepted','rejected'])->where('id_proposal',$proposal_id);
				},
				'proposals' => function($q){
					$q->whereIn('talent_proposals.status',['accepted']);
				},
				'reviews' => function($q){
                    $q->where('sender_id',auth()->user()->id_user);
                }
			])
			->with([
				'proposal' => function($q) use($proposal_id){
					$q->defaultKeys()->convertedQuotedPrice()->where('id_proposal',$proposal_id);
				}
			])
			->projectPrice()
            ->companyName()
            ->companyLogo()
            ->isProjectSaved($request->user()->id_user)
			->where('id_project',$project_id)
			->get()
			->first();

			$data['companydata']			= \DB::table('company_connected_talent')->leftjoin('talent_company','talent_company.talent_company_id','=','company_connected_talent.id_talent_company')->select('company_name','company_website','company_biography')->where('id_user','=',$data['proposal']->id_user)->first();
			// dd($data['project']);
			$data['title']                  = $data['project']->title;
			
			/* RECORDING ACTIVITY LOG */
			event(new \App\Events\Activity([
				'user_id'           => \Auth::user()->id_user,
				'user_type'         => 'employer',
				'action'            => 'proposal-details',
				'reference_type'    => 'proposals',
				'reference_id'      => $proposal_id
			]));
			
			return view('employer.jobdetail.index')->with($data);
		}

		/**
		 * [This method is used for Proposal listing]
		 * @param  Request
		 * @return \Illuminate\Http\Response
		 */
		
		public function proposal_listing(Request $request,Builder $htmlBuilder){
			$request->request->add(['currency' => \Session::get('site_currency')]);
			$data['subheader']              = 'employer.includes.top-menu';
			$data['header']                 = 'innerheader';
			$data['footer']                 = 'innerfooter';
			$data['view']                   = 'employer.jobdetail.proposals';

			$project_id                     = ___decrypt($request->id_project);
			$data['user']                   = \Models\Employers::get_user(\Auth::user());
			$data['project']                = \Models\Projects::select(['id_project','title','employment'])->where('id_project',$project_id)->first();
			$data['title']                  = $data['project']->title;
			$data['back']                  	= url('employer/project/details?job_id='.___encrypt($project_id));
			
			if ($request->ajax()) {
				$proposals = \Models\Talents::defaultKeys()
				->with([
					'chat' => function($q) use($project_id){
						$q->where('project_id',$project_id);
					}
				])
				->country()
				->review()
				->isTalentSavedEmployer(auth()->user()->id_user)
				->isTalentViewedEmployer(auth()->user()->id_user)
				->talentProposals($project_id)
				->talentProject($project_id)
				->whereNotNull('id_proposal')
				->where("projects.is_cancelled",DEFAULT_NO_VALUE)
				->get();
				return \Datatables::of($proposals)
				->filter(function ($instance) use ($request) {
					if ($request->has('sort')) {
						if(!empty($request->sort)){
							$sort = explode(" ", ___decodefilter($request->sort));
							
							if(count($sort) == 2){
								$sort_key = (!empty($sort[0]))?$sort[0]:false;
								if($sort[1] == "ASC"){
									$instance->collection = $instance->collection->sortBy(function ($row) use ($sort, $sort_key) {
										return (!empty($row->$sort_key))? $row->$sort_key: false;
									}); 
								}else if($sort[1] == "DESC"){
									$instance->collection = $instance->collection->sortByDesc(function ($row) use ($sort, $sort_key) {
										return (!empty($row->$sort_key))? $row->$sort_key: false;
									});
								}
							}
						}
					}else{
						$instance->collection = $instance->collection->sortBy(function ($row){
							return $row->status;
						});
					}

					if ($request->has('filter')) {
						if($request->filter == 'tagged_listing'){
							$instance->collection = $instance->collection->filter(function ($row) use ($request) {
								return ($row->is_saved == DEFAULT_YES_VALUE) ? true : false;
							});
						}else if($request->filter == 'applied_proposal'){
							$instance->collection = $instance->collection->filter(function ($row) {
								return ($row->proposal_status == 'applied') ? true : false;
							});
						}else if($request->filter == 'accepted_proposal'){
							$instance->collection = $instance->collection->filter(function ($row) {
								return ($row->proposal_status == 'accepted') ? true : false;
							});
						}else if($request->filter == 'declined_proposal'){
							$instance->collection = $instance->collection->filter(function ($row) {
								return ($row->proposal_status == 'rejected') ? true : false;
							});
						}						 
					}


					if ($request->has('search')) {
						if(!empty($request->search['value'])){
							$instance->collection = $instance->collection->filter(function ($row) use ($request) {
								return (\Str::contains(strtolower($row->name), strtolower($request->search['value'])) || \Str::contains(strtolower($row->comments), strtolower($request->search['value'])) || \Str::contains($row->quoted_price, $request->search['value'])) ? true : false;
							});
						} 
					}
				})
				->editColumn('title',function($proposal){
					$html = view('employer.jobdetail.talent')->with(compact('proposal'))->render();
					return $html;
				})
				->make(true);
			}

			$data['html'] = $htmlBuilder
			->parameters([
				"dom" => "<'row' <'col-md-2'f><'col-md-4 filter-option'>>rt<'row' <'col-md-6'i><'col-md-6'p>>",
				"language" => [
					"sInfo" => "Showing _START_- _END_ out of _TOTAL_ record(s)",
					"sInfoEmpty"=> "No record(s) found.",
				]
			])
			->addColumn(['data' => 'title', 'name' => 'title', 'title' => '&nbsp;']);

			return view('employer.jobdetail.index')->with($data);
		}      

		/**
		 * [This method is used for getting all proposals]
		 * @param  Request
		 * @return String (Print String)
		 */
		
		public function get_all_proposals(Request $request){
			
			$this->status   = true;
			$page           = 0;
			$search         = "";
			$sort           = "";
			$load_more      = "";
			if(!empty($request->page)){
				$page = $request->page;
			}

			if(!empty($request->search)){
				$search = $request->search;
			}
			$html = "";
			
			{
				$html .= '<p class="no-records-found">'.trans('website.W0222').'</p>';
			}

			if($projects['total_filtered_result'] == DEFAULT_PAGING_LIMIT){
				$load_more = '<span class="btn btn-default btn-block btn-lg" data-request="paginate" data-url="'.url(sprintf('employer/get-all-proposals?page=%s&search=%s',($page+1),$request->search)).'" data-target="#proposals_listing" data-showing="#paginate_showing" data-loadmore="#loadmore">'.trans('website.W0254').'</span>';
			}
			if(!empty($projects['result'])){
				$this->jsondata = $projects['result'];
			}
			
			echo json_encode(
				array(
					"recordsTotal"      => $projects['total'],
					"recordsFiltered"   => $projects['total_filtered_result'],
					"loadMore"          => $load_more, 
					"data"              => $html,
				)
			);
		}

		/**
		 * [This method is used for acceptance of proposals]
		 * @param  Request
		 * @return Json Response
		 */
		
		public function proposals_accept(Request $request){
			$request->request->add(['currency' => \Session::get('site_currency')]);
			if(empty($request->project_id)){
				$this->message = 'M0121';
				$this->message = sprintf(trans(sprintf('general.%s',$this->message)),'project_id');   
			}else if(empty($request->proposal_id)){
				$this->message = 'M0121';
				$this->message = sprintf(trans(sprintf('general.%s',$this->message)),'proposal_id');   
			}else{
				$request->project_id = ___decrypt($request->project_id);
				$request->proposal_id = ___decrypt($request->proposal_id);

				$isProposalAccepted =  \Models\Employers::accept_proposal(\Auth::user()->id_user,$request->project_id,$request->proposal_id);
				
				/* RECORDING ACTIVITY LOG */
				event(new \App\Events\Activity([
					'user_id'           => \Auth::user()->id_user,
					'user_type'         => 'employer',
					'action'            => 'proposal-accept',
					'reference_type'    => 'proposal',
					'reference_id'      => $request->proposal_id
				]));
				
				$this->message  = $isProposalAccepted['message'];
				$this->redirect = url(sprintf('%s/proposals/detail?proposal_id=%s',EMPLOYER_ROLE_TYPE,___encrypt($request->proposal_id)));
				if(!empty($isProposalAccepted['status'])){
					$this->status   = true;
				}
			}
			
			return response()->json([
				'data'      => (object)$this->jsondata,
				'status'    => $this->status,
				'message'   => $this->message,
				'redirect'  => $this->redirect,
			]);
		}  

		/**
		 * [This method is used for Tag Proposal]
		 * @param  Request
		 * @return Json Response
		 */
		
		public function proposals_tag(Request $request){
			if(empty($request->proposal_id)){
				$this->message = 'M0121';
				$this->message = sprintf(trans(sprintf('general.%s',$this->message)),'proposal_id');
			}else{
				
				$proposal_id            = ___decrypt($request->proposal_id);
				$isProposalTagged       =  \Models\Employers::tag_proposal(\Auth::user()->id_user,$proposal_id);
								
				/* RECORDING ACTIVITY LOG */
				event(new \App\Events\Activity([
					'user_id'           => \Auth::user()->id_user,
					'user_type'         => 'employer',
					'action'            => 'proposal-tag',
					'reference_type'    => 'proposal',
					'reference_id'      => $proposal_id
				]));
				
				if($isProposalTagged['message'] === 'M0315'){
					$this->jsondata['html'] = '<img src="'.asset('images/star-tagged.png').'">';
				}else{
					$this->jsondata['html'] = '<img src="'.asset('images/star-untagged.png').'">';

				}
				$this->message = trans(sprintf('general.%s',$isProposalTagged['message']));
				$this->status  = true;
			}
			
			return response()->json([
				'data'      => (object)$this->jsondata,
				'status'    => $this->status,
				'message'   => $this->message,
				'redirect'  => $this->redirect,
			]);
		}

		/**
		 * [This method is used for getting user's availabiity]
		 * @param  Request
		 * @return Json Response
		 */
		
		public function get_talent_availability(Request $request){
			$this->status           = true;
			$availability_calendar  = [];

			$availability = [
				'header' => [
					'left' => 'prev,next today',
					'center' => 'title',
					'right' => 'month,agendaWeek,agendaDay'
				],
				'editable' => true,
				'eventLimit' => true, 
				'navLinks' => true,
				'events' => [],
			];
			
			if(!empty($request->talent_id)){
				$talent_id              = ___decrypt($request->talent_id);
				$date = $request->date;

				#$talent_availability    = \Models\Talents::get_availability($talent_id);
				$talent_availability    = \Models\Talents::get_calendar_availability($talent_id, $date);

				/*if(!empty($talent_availability)){
					$get_scalar_availability = ___get_scalar_availability($talent_availability);

					if(!empty($get_scalar_availability)){
						$availability['events'] = $get_scalar_availability;
					}
				}*/
			   
				/* RECORDING ACTIVITY LOG */
				event(new \App\Events\Activity([
					'user_id'           => \Auth::user()->id_user,
					'user_type'         => 'employer',
					'action'            => 'talent-availability',
					'reference_type'    => 'talent',
					'reference_id'      => $talent_id
				]));

			}

			$this->jsondata = $talent_availability;

			return response()->json([
				'data'      => $this->jsondata,
				'status'    => $this->status,
				'message'   => $this->message,
			]);
		}

		/**
		 * [This method is used for declining proposals]
		 * @param  Request
		 * @return Json Response
		 */
		
		public function proposals_decline(Request $request){
			if(empty($request->project_id)){
				$this->message = 'M0121';
				$this->message = $this->error = sprintf(trans(sprintf('general.%s',$this->message)),'project_id');   
			}else if(empty($request->proposal_id)){
				$this->message = 'M0121';
				$this->message = $this->error = sprintf(trans(sprintf('general.%s',$this->message)),'proposal_id');   
			}else{
				$isProposalDeclined =  \Models\Employers::decline_proposal(\Auth::user()->id_user,$request->project_id,$request->proposal_id);
			   
				/* RECORDING ACTIVITY LOG */
				event(new \App\Events\Activity([
					'user_id'           => \Auth::user()->id_user,
					'user_type'         => 'employer',
					'action'            => 'proposal-decline',
					'reference_type'    => 'proposal',
					'reference_id'      => $request->proposal_id
				]));

				$this->message  = trans("general.".$isProposalDeclined['message']);
				$this->redirect = url(sprintf('%s/project/proposals/talent?proposal_id=%s&project_id=%s',EMPLOYER_ROLE_TYPE,___encrypt($request->proposal_id),___encrypt($request->project_id)));
				if(!empty($isProposalDeclined['status'])){
					$this->status   = true;
				}
			}
			
			return response()->json([
				'data'      => (object)$this->jsondata,
				'status'    => $this->status,
				'message'   => $this->message,
				'redirect'  => $this->redirect,
			]);
		}

		/**
		 * [This method is used for getting all jobs]
		 * @param  Request
		 * @return String (Print a string)
		 */
		
		public function get_all_jobs(Request $request){
			$request->request->add(['currency' => \Session::get('site_currency')]);
			$html = '';
			$page = $request->page;
			$search = "";
			$having = " 1 ";
			$current_date = date('Y-m-d');
			$type = $request->type;
			$load_more = "";
			$keys = [
				'projects.id_project',
				'projects.user_id',
				'projects.title',
				'projects.description',
				'users.company_name',
				'projects.expertise',
				'projects.budget',
				'projects.industry',
				'projects.created',
				\DB::Raw("IF(({$this->prefix}industry.{$this->language} != ''),{$this->prefix}industry.`{$this->language}`, {$this->prefix}industry.`en`) as industry_name"),
				'projects.price',
				'projects.price_max',
				'projects.budget_type',
				'projects.price_type',
				'projects.price_unit',
				'projects.bonus',
				'projects.location',
				\DB::Raw("
					IFNULL(
						IF(
							({$this->prefix}city.`{$this->language}` != ''),
							{$this->prefix}city.`{$this->language}`,
							{$this->prefix}city.`en`
						),
						''
					) as location_name"
				),
				'projects.employment',
				\DB::Raw("DATE({$this->prefix}projects.startdate) as startdate"),
				\DB::Raw("DATE({$this->prefix}projects.enddate) as enddate"),
				\DB::Raw("'completed' as type"),
				\DB::Raw("GROUP_CONCAT({$this->prefix}proposals.status) as proposal_status"),
			];            

			if(!empty($request->search)){
				$search = sprintf(" AND %sprojects.title like '%%{$request->search}%%' ",$this->prefix);
			}

			if($request->type == 'my_jobs'){
				$having = " proposal_status LIKE '%accepted%' ";
				$where = sprintf(
					" 
						{$this->prefix}projects.user_id = %s 
						AND 
						{$this->prefix}projects.project_status = '%s' 
						 %s 
					",
					\Auth::user()->id_user,
					'open',
					$search
				);
			}else if($request->type == 'jobs_schedule'){
				$having = " proposal_status LIKE '%accepted%' ";
				$where = sprintf(
					" 
						{$this->prefix}projects.user_id = %s 
						AND 
						{$this->prefix}projects.project_status = '%s'
						%s 
					",
					\Auth::user()->id_user,
					'pending',
					$search
				);
			}else if($request->type == 'jobs_history'){
				$where = sprintf(
					" 
						{$this->prefix}projects.user_id = %s 
						AND 
						{$this->prefix}projects.project_status = '%s'
						%s 
					",
					\Auth::user()->id_user,
					'closed',
					$search
				);  
			}else if($request->type == 'submitted_jobs'){
				$where = sprintf(" %sprojects.user_id = %s %s ",$this->prefix,\Auth::user()->id_user,$search);
			}

			$my_jobs = \Models\Employers::get_job(
				$where,
				'rows',
				$keys,
				$page,
				$having
			);

			if(!empty($my_jobs['result'])){
				array_walk($my_jobs['result'], function(&$item){
					if($item['employment'] !== 'fulltime'){
						$item['timeline'] = sprintf("%s - %s",___d($item['startdate']),___d($item['enddate']));
						$item['price_type'] = job_types($item['price_type']);
					}else{
						$item['price_type'] = trans('website.W0039');
						$item['timeline'] = trans('website.W0039');
					}

					unset($item['startdate']);
					unset($item['enddate']);
				});
			}
			
			if(!empty($my_jobs['result'])){
				foreach($my_jobs['result'] as $keys => $value){
					$html .= '<div class="content-box">';
						$html .= '<div class="content-box-header clearfix">';
							$html .= '<div class="contentbox-header-title">';
								$html .= '<h3><a href="'.url(sprintf('%s/my-jobs/job_details?job_id=%s',EMPLOYER_ROLE_TYPE,___encrypt($value['id_project']))).'">'.$value['title'].'</a></h3>';
								$html .= '<span class="company-name">'.$value['company_name'].'</span>';
							$html .= '</div>';
							$html .= '<div class="contentbox-price-range">';
								$html .= '<span>';
								$html .= $value['price_unit'].$value['price'];
								
								if(!empty($value['price_max'])){
									$html .= ' - '.$value['price_unit'].$value['price_max'];
								}

								$html .= '<span>'.job_types_rates_postfix($value['employment']).'</span>';
								$html .= '</span>';
								$html .= '<small>'.trans('general.'.$value['budget_type']).'</small>';
							$html .= '</div>';
						$html .= '</div>';
						$html .= '<div class="contentbox-minutes clearfix">';
							$html .= '<div class="minutes-left">';
								$html .= '<span>Industry: <strong>'.$value['industry_name'].'</strong></span>';
								
								if($value['employment'] !== 'fulltime'){
									$html .= '<span>Expected Timeline: <strong>'.$value['timeline'].'</strong></span>';
								}else{
									if(!empty($value['bonus'])){
										$html .= '<span>Bonus: <strong>'.$value['price_unit'].___format($value['bonus']).'</strong></span>';
									}

									if(!empty($value['location_name'])){
										$html .= '<span>Location: <strong>'.$value['location_name'].'</strong></span>';
									}
								}

								$html .= '<span>Job Type: <strong>'.employment_types('post_job',$value['employment']).'</strong></span>';
								
								if(!empty($value['expertise'])){
									$html .= '<span>'.trans('website.W0062').': <strong>'.expertise_levels($value['expertise']).'</strong></span>';
								}

							$html .= '</div>';
							$html .= '<div class="minutes-right">';
								$html .= '<span class="posted-time">Posted '.___ago($value['created']).'</span>';
							$html .= '</div>';
						$html .= '</div>';
						$html .= '<div class="content-box-description">';
							if(strlen($value['description']) > READ_MORE_LENGTH){
								$html .= '<p>'.___e(substr($value['description'], 0,READ_MORE_LENGTH)).'..</p>';
							}else{
								$html .= '<p>'.___e($value['description']).'</p>';
							}
						$html .= '</div>';
					$html .= '</div>';
				}
			}else{
				$html .= '<p class="no-records-found">'.trans('website.W0236').'</p>';
			}

			if($my_jobs['total_filtered_result'] == DEFAULT_PAGING_LIMIT){
				$load_more = '<span class="btn btn-default btn-block btn-lg" data-request="paginate" data-url="'.url(sprintf('%s/my-jobs/get_all_jobs?type=%s&page=%s&search=%s',EMPLOYER_ROLE_TYPE,$type,$page+1,$search)).'" data-target="#job_listing" data-showing="#paginate_showing" data-loadmore="#loadmore">'.trans('website.W0254').'</span>';
			}
		
			echo json_encode(
				array(
					"recordsTotal"      => intval($my_jobs['total_result']),
					"recordsFiltered"   => intval($my_jobs['total_filtered_result']),
					"loadMore"          => $load_more, 
					"data"              => $html,
				)
			);            
		}

		public function publicJobDetail(Request $request,$category_id,$job_id){

			$request->request->add(['currency' => \Session::get('site_currency')]);

			$project_id             = ___decrypt($job_id);
			
			$prefix                 = DB::getTablePrefix();
			$language               = \App::getLocale();
			
			$data['project']        = \Models\Projects::public_employer_jobs('detail')
			->with([
				'industries.industries' => function($q) use($language,$prefix){
					$q->select(
						'id_industry',
						\DB::Raw("IF(({$language} != ''),`{$language}`, `en`) as name")
					);
				},
				'subindustries.subindustries' => function($q) use($language,$prefix){
					$q->select(
						'id_industry',
						\DB::Raw("IF(({$language} != ''),`{$language}`, `en`) as name")
					);
				},
				'skills.skills' => function($q){
					$q->select(
						'id_skill',
						'skill_name'
					);
				},
				'proposal' => function($q) use($language){
					$q->defaultKeys()->where('talent_proposals.status','accepted')->with([
						'talent' => function($q) use($language){
							$q->defaultKeys()->review()->with([
								'subindustries.subindustries' => function($q) use($language){
									$q->select(
										'id_industry',
										\DB::Raw("IF(({$language} != ''),`{$language}`, `en`) as name")
									);
								}
							]);
						}
					]);
				},
				'proposals' => function($q){
                        $q->select('id_proposal','user_id','project_id');
                        $q->with([
                            'talent' => function($q){
                                $q->select('id_user')->companyLogo();
                            }
                        ]);
                },
				'dispute' => function($q){
					$q->defaultKeys();
				},
				'projectlog' => function($q){
					$q->select('project_id')->totalTiming()->groupBy(['project_id']);
				},
				'chat' => function($q){
					$q->defaultKeys();
				}
			])->where('id_project',$project_id)->get()->first();

			$data['title'] = $data['project']->title;

			return view('employer.jobdetail.publicJobDetail')->with($data);
			
		}

		/**
		 * [This method is used for finding a job in detail]
		 * @param  Request
		 * @return \Illuminate\Http\Response
		 */

		public function project_details(Request $request,Builder $htmlBuilder, $page = 'details'){
			$request->request->add(['currency' => \Session::get('site_currency')]);

			$project_id             = ___decrypt($request->job_id);
			$data['subheader']      = 'employer.includes.top-menu';
			$data['header']         = 'innerheader';
			$data['footer']         = 'innerfooter';
			$data['view']           = "employer.jobdetail.{$page}";
			$data['user']           = \Models\Talents::get_user(\Auth::user());
			
			$prefix                 = DB::getTablePrefix();
			$language               = \App::getLocale();
			$user                   = (object)['id_user' => \Auth::user()->id_user];
			
			$data['project']        = \Models\Projects::employer_jobs($user,'detail')
			->withCount([
				'dispute',
				'proposals' => function($q){
					$q->where('talent_proposals.status','!=','rejected');	
				},
				'reviews' => function($q){
                    $q->where('sender_id',auth()->user()->id_user);
                }
			])
			->with([
				'industries.industries' => function($q) use($language,$prefix){
					$q->select(
						'id_industry',
						\DB::Raw("IF(({$language} != ''),`{$language}`, `en`) as name"),
						'slug'
					);
				},
				'subindustries.subindustries' => function($q) use($language,$prefix){
					$q->select(
						'id_industry',
						\DB::Raw("IF(({$language} != ''),`{$language}`, `en`) as name")
					);
				},
				'skills.skills' => function($q){
					$q->select(
						'id_skill',
						'skill_name'
					);
				},
				'proposal' => function($q) use($language){
					$q->defaultKeys()->where('talent_proposals.status','accepted')->with([
						'talent' => function($q) use($language){
							$q->defaultKeys()->review()->with([
								'subindustries.subindustries' => function($q) use($language){
									$q->select(
										'id_industry',
										\DB::Raw("IF(({$language} != ''),`{$language}`, `en`) as name")
									);
								}
							]);
						}
					]);
				},
				'proposals' => function($q){
                        $q->select('id_proposal','user_id','project_id');
                        $q->with([
                            'talent' => function($q){
                                $q->select('id_user')->companyLogo();
                            }
                        ]);
                },
				'dispute' => function($q){
					$q->defaultKeys();
				},
				'projectlog' => function($q) use($user){
					$q->select('project_id')->totalTiming()->groupBy(['project_id']);
				},
				'chat' => function($q){
					$q->defaultKeys();
				}
			])->where('id_project',$project_id)->get()->first();
			
			if($page == 'reviews'){   
				if ($request->ajax()) {
					$reviews    = \Models\Reviews::defaultKeys()->with([
						'sender' => function($q){
							$q->select(
								'id_user'
							)->name()->companyLogo();
						},
						'receiver' => function($q){
							$q->select(
								'id_user'
							)->name()->companyLogo();
						}
					])->where('receiver_id',$data['project']->company_id)->orderBy('id_review','DESC')->get();

					return \Datatables::of($reviews)
					->editColumn('review',function($item){
						$html ='<div class="review-content-block clearfix">';
							$html .='<div class="review-content-display"><img src="'.asset($item->sender->company_logo).'" /></div>';
							$html .='<div class="review-content-info">';                            
								$html .='<h4>'.$item->sender->name.' <span class="pull-right review-figure">'.___ratingstar($item->review_average).'</span></h4>';
								$html .='<p>'.$item->description.'</p>';
								$html .='<span class="review-time">'.trans('general.M0177').' '.___ago($item->created).'</span>';
							$html .='</div>';
						$html .='</div>'; 

						return $html;
					})
					->make(true);
				}

				$data['html'] = $htmlBuilder
				->parameters(["dom" => "<'row' <'col-md-6 table-heading'> <'col-md-3'> <'col-md-3 filter-option'>> rt <'row'<'col-md-6'><'col-md-6'p> >"])
				->addColumn(['data' => 'review', 'name' => 'review', 'title' => '&nbsp;', 'width' => '0', 'searchable' => false, 'orderable' => false]);

			}

			$this_project_details = json_decode(json_encode($data['project']),true);
			$data['job_category'] = $this_project_details['industries'][0]['industries']['slug'];

			/*Calculate cancellation price*/
			$commission = ___cache('configuration')['cancellation_commission'];
			$commission_type = ___cache('configuration')['cancellation_commission_type'];

			if($commission_type == 'per'){
			    $calculated_commission=___format(round(((($data['project']->price*$commission)/100)),2)); 
			}else{
			    $calculated_commission = ___format(round(($commission),2));
			}

			$cancellation_amount = $data['project']->price - $calculated_commission;
			$data['cancellation_amount'] = ___format($cancellation_amount,true,true);

			if(empty($data['project'])){
				return redirect(url(sprintf('%s/my-jobs/submitted',EMPLOYER_ROLE_TYPE)));
			}
		
			$data['title'] = $data['project']->title;
			return view('employer.jobdetail.index')->with($data);
		}

		/**
		 * [This method is used for save user's]
		 * @param  Request
		 * @return Json Response
		 */
		
		public function save_talent(Request $request){
			$validator = \Validator::make($request->all(),[
				'talent_id' => validation('talent_id')
			],[
				'talent_id.integer' => trans("general.M0121")
			]);
		   
			if($validator->passes()){
				$isUpdated          = \Models\Employers::save_talent(\Auth::user()->id_user,$request->talent_id);

				if($isUpdated['action'] == 'saved_talent'){
					$this->head_message = trans("general.M0602");
				}else{
					$this->head_message = trans("general.M0603");
				}
				   
				/* RECORDING ACTIVITY LOG */
				event(new \App\Events\Activity([
					'user_id'           => \Auth::user()->id_user,
					'user_type'         => 'employer',
					'action'            => 'save-talent',
					'reference_type'    => 'talent',
					'reference_id'      => $request->talent_id
				]));
								
				$this->status   = true;
				$this->message  = sprintf(ALERT_SUCCESS,trans("general.M0219"));               
			}else{
				$this->jsondata = ___error_sanatizer($validator->errors());
			}

			return response()->json([
				'data'      	=> $this->jsondata,
				'status'    	=> $this->status,
				'message'   	=> $this->message,
				'redirect'  	=> $this->redirect,
				'head_message'  => $this->head_message,
			]);
		}

		/**
		 * [This method is used for chat]
		 * @param  Request
		 * @return Json Response
		 */

		public function chat(Request $request){
			$data['subheader']      = 'employer.includes.top-menu';
			$data['header']         = 'innerheader';
			$data['footer']         = 'innerfooter';
			$data['view']           = 'chat.view';
			$data['title']          = trans('website.W0594');
			$data['user']           = \Models\Employers::get_user(\Auth::user());
			$data['request']    	= $request->all();

			/* RECORDING ACTIVITY LOG */
			event(new \App\Events\Activity([
				'user_id'           => \Auth::user()->id_user,
				'user_type'         => 'employer',
				'action'            => 'chat',
				'reference_type'    => 'users',
				'reference_id'      => \Auth::user()->id_user
			]));

			return view('chat.index')->with($data);
		}

		/**
		 * [This method is used for notification listing]
		 * @param  Request
		 * @return Json Response
		 */

		public function notification_list(Request $request){
		
			$html               = $search = $load_more = "";

			$page               = (!empty($request->page))?$request->page:1;
			$notifications      = \Models\Notifications::lists(\Auth::user()->id_user,$page,DEFAULT_PAGING_LIMIT);

			if(!empty($notifications['result'])){
				foreach($notifications['result'] as $keys => $item){
					$html .= '<li class="btn-block">';
						$html .= '<a href="javascript:void(0);" data-request="inline-ajax" data-url="'.url(sprintf('%s/notifications/mark/read?notification_id=%s',EMPLOYER_ROLE_TYPE,$item['id_notification'])).'" class="submenu-block clearfix '.$item['notification_status'].'">';
							$html .= '<span class="submenublock-user"><img src="'.$item['sender_picture'].'" /></span>';
							$html .= '<span class="submenublock-info">';
								$html .= '<h4>'.$item['sender_name'].' <span>'.$item['created'].'</span></h4>';
								$html .= '<p>'.$item['notification'].'</p>';
							$html .= '</span>';
						$html .= '</a>';
					$html .= '</li>';                
				}
			}else{
				$html .= '<li class="no-records-found">'.trans('general.M0291').'</li>';
			}
			
			if($notifications['total_filtered_result'] == DEFAULT_PAGING_LIMIT){
				$load_more = '<span class="btn btn-default btn-block btn-lg" data-request="paginate" data-url="'.url(sprintf('%s/notifications/list?page=%s',TALENT_ROLE_TYPE,$page+1)).'" data-target="#notification-list" data-showing="#paginate_showing" data-loadmore="#loadmore">'.trans('website.W0254').'</span>';
			}

			/* RECORDING ACTIVITY LOG */
			event(new \App\Events\Activity([
				'user_id'           => \Auth::user()->id_user,
				'user_type'         => 'employer',
				'action'            => 'notification-list',
				'reference_type'    => 'users',
				'reference_id'      => \Auth::user()->id_user
			]));

			return response()->json([
				"recordsTotal"      => intval($notifications['total']),
				"recordsFiltered"   => intval($notifications['total_filtered_result']),
				"loadMore"          => $load_more, 
				"data"              => $html,
			]);
		}

		/**
		 * [This method is used for read marked notification]
		 * @param  Request
		 * @return Json Response
		 */
		
		public function mark_read_notification(Request $request){
			$isMarkedRead = \Models\Notifications::markread($request->notification_id,\Auth::user()->id_user);
			
			if(!empty($isMarkedRead)){
				$this->status = $isMarkedRead['status'];
				$this->jsondata = [
					'total_unread_notifications' => $isMarkedRead['total_unread_notifications']
				];
				$this->redirect = $isMarkedRead['redirect'];
			}

			/* RECORDING ACTIVITY LOG */
			event(new \App\Events\Activity([
				'user_id'           => \Auth::user()->id_user,
				'user_type'         => 'employer',
				'action'            => 'mark-read-notification',
				'reference_type'    => 'users',
				'reference_id'      => \Auth::user()->id_user
			]));
			
			return response()->json([
				'data'      => $this->jsondata,
				'status'    => $this->status,
				'message'   => $this->message,
				'redirect'  => $this->redirect,
			]);
		}


		/**
		 * [This method is used for job start / mark completed]
		 * @param  Request
		 * @return Json Response
		 */

		public function project_status(Request $request,$status = 'close'){

			$request->request->add(['currency' => \Session::get('site_currency')]);
			
			$project_id = ___decrypt($request->project_id);
			$project 	= \Models\Projects::defaultKeys()
			->with([
				'projectlog' => function($q){
					$q->select('project_id')->totalTiming()->where('talent_id',auth()->user()->id_user)->groupBy(['project_id']);
				},
				'proposal' => function($q){
					$q->defaultKeys()->where('talent_proposals.status','accepted');
				}
			])
			->where('id_project',(int)$project_id)
			->get()
			->first();

			if(empty($project)){
				$this->message = trans("general.M0121");
			}else if($status == 'close' && $project->project_status == 'closed' && !empty($project->closedate)){
				$this->message = trans("general.M0562");
			}else{
				switch($status){
					case 'close': {
						$isUpdated          = \Models\Projects::change([
							'id_project' 		=> $project_id,
							'project_status' 	=> 'closed'
						],[
							'closedate'    		=> date('Y-m-d H:i:s'),
							'updated'           => date('Y-m-d H:i:s')
						]);

						if(!empty($isUpdated)){
							/* RECORDING ACTIVITY LOG */
							event(new \App\Events\Activity([
								'user_id'           => auth()->user()->id_user,
								'user_type'         => 'employer',
								'action'            => 'employer-close-job',
								'reference_type'    => 'projects',
								'reference_id'      => $project_id
							]));

							$isNotified = \Models\Notifications::notify(
                                $project->proposal->talent->id_user,
                                $project->company_id,
                                'JOB_COMPLETED_BY_EMPLOYER',
                                json_encode([
                                    "employer_id"   => (string) $project->company_id,
                                    "talent_id"     => (string) $project->proposal->talent->id_user,
                                    "project_id"    => (string) $project->id_project
                                ])
                            );

                            $this->status = false;
							$this->message = trans("general.M0617");

							$this->redirect = url(sprintf('%s/project/details?job_id=%s',EMPLOYER_ROLE_TYPE,___encrypt($project_id)));
						}
						break;
					}
				}
			}

			return response()->json([
				'data'      => $this->jsondata,
				'status'    => $this->status,
				'message'   => $this->message,
				'nomessage' => true,
				'redirect'  => $this->redirect,
			]);
		}

		/**
		 * [This method is used for employer chat request]
		 * @param  Request
		 * @return Json Response
		 */
		
		public function employer_chat_request(Request $request){
			$html           = ''; 
			$sender         = ___decrypt($request->sender);
			$receiver       = ___decrypt($request->receiver);
			$project_id     = ___decrypt($request->project_id);
			
			$isRequestSent  = \Models\Chats::employer_chat_request($sender,$receiver,$project_id);

			/* RECORDING ACTIVITY LOG */
			event(new \App\Events\Activity([
				'user_id'           => $sender,
				'user_type'         => 'employer',
				'action'            => 'employer-chat-request',
				'reference_type'    => 'talent',
				'reference_id'      => $receiver
			]));

			if(!empty($isRequestSent['status'])){
				$this->message = trans('general.M0284');
				$this->status = $isRequestSent['status'];
				$this->redirect = url(sprintf("%s/chat",EMPLOYER_ROLE_TYPE));
			}

			return response()->json([
				'data'      => $this->jsondata,
				'status'    => $this->status,
				'message'   => $this->message,
				'redirect'  => $this->redirect,
			]);
		}

		/**
		 * [This method is used for viewing premium user's]
		 * @param  Request
		 * @return String (Print a string)
		 */

		public function hire_premiums_talents(Request $request){
			$language            = \App::getLocale();
			$request->request->add(['currency' => \Session::get('site_currency')]);

			$data['currency'] = \Session::get('site_currency');
			if(empty(\Auth::guard('web')->check())){
				if($request->stream === 'mobile'){
					$data['header'] = 'mobile/innerheader';
					$data['footer'] = 'mobile/innerfooter';
				}else{
					$data['header'] = 'innerheader';
					$data['footer'] = 'innerfooter';
				}
				$data['banner'] = \Models\Banner::getBannerBySlug('pricing');                
				$data['plan'] = \Models\Plan::getPlanList();

				return view(sprintf('front.pages.pricing'))->with($data);
			}
			
			$data['subheader']          = 'employer.includes.top-menu';
			$data['header']             = 'innerheader';
			$data['footer']             = 'innerfooter';
			$data['title']              = trans('website.W0595');
			$data['view']               = 'employer.premium.hire-talent';

			/*Plans*/
			$data['plan']               = \Models\Plan::getPlanList();

			$data['user']               = \Models\Employers::get_user(\Auth::user());
			$data['user_card']          = \Models\Payments::get_user_card(\Auth::user()->id_user);
			/*$data['plan_details']       = json_decode(json_encode(\Models\Plan::getPlanListing(),true));
			$data['plan']               = \Models\Plan::getPlan();*/
			$keys = [
				'id_feature',
				'status',
				\DB::raw("IF({$language} != '',{$language},en) as name")
			];
			$data['features']           = \Models\Plan::getFeatures('array',$keys);
			$data['industries_name']    = \Cache::get('industries_name');
			$data['subindustries_name'] = \Cache::get('subindustries_name');
			$data['skills']             = \Cache::get('skills');
			$data['search']             = (!empty($request->search))?$request->search:"";

			/* RECORDING ACTIVITY LOG */
			event(new \App\Events\Activity([
				'user_id'           => \Auth::user()->id_user,
				'user_type'         => 'employer',
				'action'            => 'hire-premiums-talents',
				'reference_type'    => 'users',
				'reference_id'      => \Auth::user()->id_user
			]));

			return view('employer.profile.index')->with($data);
		}

		/**
		 * [This method is used for handling premium user's]
		 * @param  Request
		 * @return String (Print a string)
		 */
		
		public function _hire_premium_talents(Request $request){
			$this->status   = true;
			$load_more      = "";
			$search         = " 1 ";
			$html           = "";
			$page           = (!empty($request->page))?$request->page:1;
			$sort           = "";

			if($request->sortby_filter){
				$sort       = ___decodefilter($request->sortby_filter);
			}

			if(!empty($request->employment_type_filter)){
				$search .= sprintf(" AND {$this->prefix}talent_interests.interest IN ('%s') ",implode("','", $request->employment_type_filter));
			}


			$search .= sprintf(" AND (
				(
					{$this->prefix}users.expected_salary >= {$request->permanent_salary_low_filter}
					AND
					{$this->prefix}users.expected_salary <= {$request->permanent_salary_high_filter}
				)
			)");



			if(!empty($request->permanent_salary_low_filter) && !empty($request->permanent_salary_high_filter)){
				$search .= sprintf(" AND {$this->prefix}users.expected_salary >= {$request->permanent_salary_low_filter} AND {$this->prefix}users.expected_salary <= {$request->permanent_salary_high_filter} ");
			}

			if(!empty($request->expertise_filter)){
				$search .= sprintf(" AND {$this->prefix}users.expertise IN ('%s') ", implode("','",$request->expertise_filter));
			}

			if(!empty($request->industry_filter)){
				$search .= sprintf(" AND {$this->prefix}users.industry = {$request->industry_filter} ");
			}

			if(!empty($request->subindustry_filter)){
				$search .= sprintf(" AND {$this->prefix}users.subindustry = {$request->subindustry_filter} ");
			}

			if(!empty($request->skills_filter)){
				$search .= sprintf(" AND {$this->prefix}talent_skills.skill IN ('%s') ",implode("','", $request->skills_filter));
			}

			if(!empty($request->state_filter)){
				$search .= sprintf(" AND {$this->prefix}users.city IN (%s) ",implode(",", $request->state_filter));
			}

			if(!empty($request->search)){
				$search .= sprintf(" AND
					(
						{$this->prefix}users.name like '%%{$request->search}%%'
						OR
						{$this->prefix}talent_skills.skill like '%%{$request->search}%%'
					)
				");
			}

			if(!empty($request->saved_talent_filter)){
				$search .= sprintf(" AND {$this->prefix}saved_talent.id_saved IS NOT NULL");
			}

			if(!empty(trim($request->__search))){
				$search .= sprintf(" AND
					(
						{$this->prefix}users.name like '%%{$request->__search}%%'
						OR
						{$this->prefix}talent_skills.skill like '%%{$request->__search}%%'
					)
				");
			}

			$keys = [
				'users.id_user',
				'users.type',
				\DB::raw("CONCAT(IFNULL({$this->prefix}users.first_name,''),' ',IFNULL({$this->prefix}users.last_name,'')) as name"),
				'users.gender',
				'users.country',
				'users.workrate',
				'users.experience',
				\DB::Raw("IF(({$this->prefix}countries.{$this->language} != ''),{$this->prefix}countries.`{$this->language}`, {$this->prefix}countries.`en`) as country_name"),
				\DB::Raw("
					IF(
						({$this->prefix}city.`{$this->language}` != ''),
						{$this->prefix}city.`{$this->language}`, 
						{$this->prefix}city.`en` 
					) as city_name"
				),
				'files.id_file',
				\DB::Raw("IF(({$this->prefix}industries.{$this->language} != ''),{$this->prefix}industries.`{$this->language}`, {$this->prefix}industries.`en`) as industry_name"),
				\DB::Raw("IF(({$this->prefix}subindustries.{$this->language} != ''),{$this->prefix}subindustries.`{$this->language}`, {$this->prefix}subindustries.`en`) as subindustry"),
				\DB::raw('"0" as job_completion'),
				\DB::raw('"0" as availability_hours'),
				'users.expertise',
				\DB::raw("(SELECT GROUP_CONCAT(t.skill) FROM {$this->prefix}talent_skills as t WHERE t.user_id = {$this->prefix}users.id_user) as skills"),
				\DB::Raw("IF({$this->prefix}saved_talent.id_saved IS NOT NULL,'".DEFAULT_YES_VALUE."','".DEFAULT_NO_VALUE."') as is_saved"),
				\DB::raw('"0.0" as rating'),
				\DB::raw('"0" as review'),
			];

			$talents =  \Models\Employers::find_premium_talents(\Auth::user(),'all',$search,$page,$sort,$keys);

			if(!empty($talents['result'])){
				foreach($talents['result'] as $keys => $item){
					$html .= '<div class="content-box">';
						$html .= '<div class="content-box-header clearfix">';
							$html .= '<div class="contentbox-header-title">';
								$html .= '<div class="talent-header-xs clearfix">';

									if(!empty($item['picture'])){
										$html .= '<div class="talent-display-xs"><img src="'.url($item['picture']).'"></div>';
									}

									$html .= '<div class="talent-details-xs">';
										$html .= '<h4>';
											//$html .= '<a href="'.url("employer/find-talents/profile?talent_id=".___encrypt($item['id_user'])).'">';
											$html .= ___ucfirst($item['name']);
											//$html .= '</a>';

										$html .= '</h4>';
										$html .= '<div class="rating-review">';
											$html .= '<span class="rating-block">';
												$html .= $item['city_name'] . ',';
												$html .= $item['country_name'];
											$html .= '</span>';

										$html .= '</div>';
									$html .= '</div>';
								$html .= '</div>';
							$html .= '</div>';

							$html .= '<div class="contentbox-price-range">';
								$html .= '<a href="'.url("download/file?file_id=".___encrypt($item['id_file'])).'">';
									$html .= '<span>'.trans('website.W0639').'</span>';
								$html .= '</a>';
							$html .= '</div>';

						$html .= '</div>';

						$html .= '<div class="contentbox-minutes clearfix">';
							$html .= '<div class="minutes-left">';

								$html .= '<span>'.trans('website.W0421').': ';
								if(!empty($item['subindustry'])){
									$html .= '<strong>'.$item['subindustry'].',</strong>';
								}
								if(!empty($item['industry_name'])){
									$html .= '<strong>'.$item['industry_name'].'</strong>';
								}
								if(empty($item['subindustry']) && empty($item['industry_name'])){
									$html .= '<strong>'.N_A.'</strong>';
								}
								$html .= '</span>';

								if(!empty($item['expertise'])){
									$html .= '<span>'.trans('website.W0419').': <strong>'.expertise_levels($item['expertise']).'</strong></span>';
								}else{
									$html .= '<span>'.trans('website.W0419').': <strong>'.N_A.'</strong></span>';
								}
								if(!empty($item['experience'])){
									$html .= '<span>'.trans('website.W0420').': <strong>'.$item['experience'].' '.trans('website.W0422').'</strong></span>';
								}else{
									$html .= '<span>'.trans('website.W0420').': <strong>'.N_A.'</strong></span>';
								}
							$html .= '</div>';
							$html .= '<div class="clearfix"></div>';
							$html .= '<div class="row others top-margin-10px bottom-margin-10px">';
								$html .= '<div class="col-md-11 js-example-tags-container">';
									if(!empty($item['skills'])){
										$html .= sprintf("<ul>%s</ul>",___tags(explode(",", $item['skills']),'<li class="tag-selected">%s</li>',' '));
									}else{
										$html .= '<small style="padding: 10px 0;display: block;">'.N_A.'</small>';
									}
								$html .= '</div>';
							$html .= '</div>';
						$html .= '</div>';
					$html .= '</div>';
				}
			}

			if($talents['total_filtered_result'] == DEFAULT_PAGING_LIMIT){
				$load_more = '<button type="button" class="btn btn-default btn-block btn-lg" data-request="filter-paginate" data-url="'.url(sprintf('%s/_hire-premium-talents',EMPLOYER_ROLE_TYPE)).'" data-target="#talent_listing" data-showing="#paginate_showing" data-loadmore="#loadmore" data-form="[role=\'find-talents\']">'.trans('website.W0254').'</button>';
			}else{
				$load_more = '<button type="button" class="btn btn-default btn-block btn-lg hide" data-request="filter-paginate" data-url="'.url(sprintf('%s/_hire-premium-talents',EMPLOYER_ROLE_TYPE)).'" data-target="#talent_listing" data-showing="#paginate_showing" data-loadmore="#loadmore" data-form="[role=\'find-talents\']">'.trans('website.W0254').'</button>';
			}

			echo json_encode(
				array(
					"filter_title"      => sprintf(trans('general.M0196'),$talents['total_filtered_result']),
					"paging"            => ($request->page == 1)?false:true,
					"recordsFiltered"   => $talents['total_filtered_result'],
					"recordsTotal"      => $talents['total'],
					"loadMore"          => $load_more,
					"data"              => $html,
				)
			);
		}

		/**
		 * [This method is used for user's payment]
		 * @param  Request
		 * @return \Illuminate\Http\Response
		 */
		
		public function payment_talent(){
			$data['subheader']              = 'employer.includes.top-menu';
			$data['header']                 = 'innerheader';
			$data['footer']                 = 'innerfooter';
			$data['title']                  = trans('website.W0596');
			$data['view']                   = 'employer.job.payment';
			$data['user']                   = \Models\Employers::get_user(\Auth::user());
			
			/* RECORDING ACTIVITY LOG */
			event(new \App\Events\Activity([
				'user_id'           => \Auth::user()->id_user,
				'user_type'         => 'employer',
				'action'            => 'payment-talent',
				'reference_type'    => 'users',
				'reference_id'      => \Auth::user()->id_user
			]));
			
			return view('employer.job.index')->with($data);
		}

		/**
		 * [This method is used for payment method]
		 * @param  Request
		 * @return Json Response
		 */

		public function payment_method(Request $request){
			$braintree_id = \Auth::user()->braintree_id;
			$created_date = date('Y-m-d H:i:s');
			if(empty($braintree_id)){
				$add_customer_result = \Braintree_Customer::create(array(
					'firstName' => \Auth::user()->first_name,
					'lastName' => \Auth::user()->last_name,
					'company' => '',
					'email' =>\Auth::user()->email,
					'phone' =>\Auth::user()->mobile,
					'fax' => '',
					'website' => ''
				));

				if($add_customer_result->success){
					\Models\Payments::braintree_response([
						'user_id'                   => \Auth::user()->id_user,
						'braintree_response_json'   => json_encode($add_customer_result),
						'status'                    => 'true',
						'created'                   => $created_date
					]);

					$update['braintree_id'] = $add_customer_result->customer->id;
					\Models\Employers::change(\Auth::user()->id_user,$update); 
				}else{
					\Models\Payments::braintree_response([
						'user_id'                   => \Auth::user()->id_user,
						'braintree_response_json'   => json_encode($add_customer_result->message),
						'status'                    => 'false',
						'created'                   => $created_date
					]);

					$this->status = false;
					$this->message = trans('payment.P0020');
					$this->redirect = url(sprintf('%s/payment/start',EMPLOYER_ROLE_TYPE));
					return response()->json([
						'data'      => $this->jsondata,
						'status'    => $this->status,
						'message'   => $this->message,
						'redirect'  => $this->redirect,
					]);                    
				}
			}  
		}

		/**
		 * [This method is used for configuring payment with paypal]
		 * @param  Request
		 * @return \Illuminate\Http\Response
		 */
		

		public function setting_payments(Request $request){ 

			$data['title']                  = trans('website.W0374');
            $data['subheader']              = 'employer.includes.top-menu';
            $data['header']                 = 'innerheader';
            $data['footer']                 = 'innerfooter';
            $data['view']                   = 'employer.settings.payments';

            $data['user']                   = \Models\Employers::get_user(\Auth::user());
            $data['settings']               = \Models\Settings::fetch(\Auth::user()->id_user,\Auth::user()->type);
            $data['verified_paypal_email'] = false;
            $data['returnMessage']    = '';
            // dd($data['user']);
            $data['industries_name']        = \Cache::get('industries_name');
            $data['subindustries_name']     = \Cache::get('subindustries_name');
            return view('employer.settings.index')->with($data); 
		}

		/**
		 * [This method is used for handling of change password]
		 * @param  Request
		 * @return Json Response
		 */
		
		public function __payments(Request $request){

			if($request->user()->paypal_id != $request->paypal_id){
				$validator = \Validator::make($request->all(), [
					"paypal_id"	=> ['required','email',Rule::unique('users')->ignore('trashed','status')->where(function($query) use($request){$query->where('id_user','!=',$request->user()->id_user);})],
				],[
					'paypal_id.required' => trans('general.M0010'),
					'paypal_id.email'	 => trans('general.M0011'),
					'paypal_id.unique'	 => trans('general.M0528'),
				]);

				if($validator->passes()){

					$this->jsondata = validatePayPalEmail2($request['paypal_id']);
					$this->status = true;

					\Session::set('user_paypal_email',$request['paypal_id']);
					
	                /* RECORDING ACTIVITY LOG */
	                event(new \App\Events\Activity([
	                    'user_id'           => $request->user()->id_user,
	                    'user_type'         => 'talent',
	                    'action'            => 'webservice-talent-paypal-configuration',
	                    'reference_type'    => 'notifications',
	                    'reference_id'      => $request->user()->id_user
	                ]));

					$this->redirect = false;
				}else{
					$this->jsondata = ___error_sanatizer($validator->errors());
				}
			}else{
				$this->jsondata = (object)['paypal_id' => trans("general.M0531")];
				$this->message  = trans("general.M0531");
			}

			return response()->json([
				'data'      => $this->jsondata,
				'status'    => $this->status,
				'message'   => $this->message,
				'redirect'  => $this->redirect,
			]);
		}

		/**
		 * [This method is used for add payment card]
		 * @param  Request
		 * @return Json Response
		 */

		public function payment_add_card(Request $request){
			$post = [
				'card_type'   		=> $request->credit_card['card_type'],
				'cardholder_name'   => $request->credit_card['cardholder_name'],
				'expiry_month'      => $request->credit_card['expiry_month'],
				'expiry_year'       => $request->credit_card['expiry_year'],
				'number'            => str_replace(" ","",$request->credit_card['number']),
				'cvv'               => $request->credit_card['cvv'],
				'response'          => $request->response,
				'save_card'         => $request->save_card,
			];

			$request->replace($post);

			$validator = \Validator::make($request->all(), [
				'card_type'  	   => validation('card_type'),
				'cardholder_name'  => validation('name'),
				'expiry_month'     => validation('expiration_month'),
				'expiry_year'      => validation('expiration_year'),
				'number'           => validation('card_number'),
				'cvv'              => validation('cvv')
			],[
                'card_type.required'                        => trans('general.M0547'),
                'card_type.string'                          => trans('general.M0548'),
                'card_type.validate_card_type'              => trans('general.M0549'),
				'cardholder_name.required'                  => trans('general.M0396'),
				'cardholder_name.string'                    => trans('general.M0401'),
				'cardholder_name.regex'                     => trans('general.M0401'),
				'cardholder_name.max'                       => trans('general.M0402'),
				'number.required'                           => trans('general.M0403'),
				'number.string'                             => trans('general.M0403'),
				'number.regex'                              => trans('general.M0403'),
				'number.max'                                => trans('general.M0404'),
				'number.min'                                => trans('general.M0405'),
				'expiry_month.required'                     => trans('general.M0398'),
				'expiry_month.string'                       => trans('general.M0406'),
				'expiry_month.validate_expiry_month'        => trans('general.M0498'),
				'expiry_year.required'                      => trans('general.M0399'),
				'expiry_year.integer'                       => trans('general.M0407'),
				'cvv.required'                              => trans('general.M0400'),
				'cvv.string'                                => trans('general.M0409'),
				'cvv.regex'                                 => trans('general.M0409'),
				'cvv.max'                                   => trans('general.M0410'),
				'cvv.min'                                   => trans('general.M0411'),
			]);
			if($validator->passes()){
                $creditCard = [
                    'card_type'         => $request->card_type,
                    'number'            => $request->number,
                    'expiry_month'      => $request->expiry_month,
                    'expiry_year'       => $request->expiry_year,
                    'cvv'               => $request->cvv,
                    'cardholder_name'   => $request->cardholder_name,
                ];				
				$created_date = date('Y-m-d H:i:s');
				if(empty($request->save_card)){
					$isCardCreated = \Models\PaypalPayment::create_credit_card($creditCard, false);
					if($isCardCreated['status'] == true){
						\Session::set('card_token',$isCardCreated['card']);
						
						/* RECORDING ACTIVITY LOG */
						event(new \App\Events\Activity([
							'user_id'           => \Auth::user()->id_user,
							'user_type'         => 'employer',
							'action'            => 'payment-add-card-without-save',
							'reference_type'    => 'users',
							'reference_id'      => \Auth::user()->id_user
						]));                    
						
						$this->status   = $isCardCreated['status'];
						$this->message  = sprintf(ALERT_DANGER, trans(sprintf('general.%s',$isCardCreated['message'])));
						$this->jsondata = (object)[];
						$this->redirect = true;
					}else{
						$this->message  = sprintf(ALERT_DANGER, trans(sprintf('general.%s',$isCardCreated['message'])));
						$this->jsondata = (object)[];
					}
				}else{
					$isCardCreated = \Models\PaypalPayment::create_credit_card($creditCard, true);
					$this->status   = $isCardCreated['status'];

					if($isCardCreated['status'] == true){
						if($request->response == 'append_payment_details'){
							$this->jsondata = \Models\Payments::get_payment_checkout_html(\Auth::user()->id_user);   
						}else{
							$url_delete = sprintf(
								url('%s/payment/card/delete?card_id=%s'),
								EMPLOYER_ROLE_TYPE,
								$isCardCreated['card']['id_card']
							);

							$this->jsondata = sprintf(
								ADD_CARD_TEMPLATE,
								$isCardCreated['card']['id_card'],
								$isCardCreated['card']['id_card'],
								($isCardCreated['card']['default'] == DEFAULT_YES_VALUE)?'checked="checked"':'',
								$isCardCreated['card']['image_url'],
								wordwrap(sprintf("%s%s",str_repeat(".",strlen($isCardCreated['card']['masked_number'])-4),$isCardCreated['card']['last4']),4,' ',true),
								($isCardCreated['card']['default'] == DEFAULT_YES_VALUE)?trans('website.W0427'):'',
								$url_delete,
								$isCardCreated['card']['id_card'],
								asset('/'),
								trans('general.M0378'),
								asset('/')
							);
						}
					}else{
						$this->message  = sprintf(ALERT_DANGER, trans(sprintf('general.%s',$isCardCreated['message'])));
						$this->jsondata = (object)[];
					}

					/* RECORDING ACTIVITY LOG */
					event(new \App\Events\Activity([
						'user_id'           => \Auth::user()->id_user,
						'user_type'         => 'employer',
						'action'            => 'payment-add-card',
						'reference_type'    => 'users',
						'reference_id'      => \Auth::user()->id_user
					]));
				}

				if(!empty($request->redirect)){
					$this->redirect = $request->redirect;
					$request->session()->flash('alert',sprintf(ALERT_SUCCESS,trans('website.W0791')));
				}

			}else{
				$this->jsondata     = ___error_sanatizer($validator->errors());
			}

			return response()->json([
				'data'      => $this->jsondata,
				'status'    => $this->status,
				'message'   => $this->message,
				'redirect'  => $this->redirect,
			]);
		}

		/**
		 * [This method is used for managing payment card]
		 * @param  Request
		 * @return \Illuminate\Http\Response
		 */

		public function payment_manage_card(Request $request){
			$data['cards']      = \Models\PaypalPayment::get_user_card(\Auth::user()->id_user,'','array',['*',
				 	\DB::raw("CONCAT('".asset('/')."','',{$this->prefix}card_type.image) as image_url"),
				 	\DB::raw("REPLACE(masked_number,'x', '') as last4")
				]);
			$data['title']		= trans('website.W0728');
			if($request->ajax()){
				if(!empty($data['cards']) && empty($request->load)){
					return view('employer.cards.list',$data);
				}else{
					return view('employer.cards.add',$data);
				}
			}else{
				$data['subheader']   = 'employer.includes.top-menu';
				$data['header']      = 'innerheader';
				$data['footer']      = 'innerfooter';
				$data['view']        = 'employer.cards.manage';

				$data['user']        = \Models\Employers::get_user(\Auth::user());
				// dd(\Cache::get('card_type'));
				/* RECORDING ACTIVITY LOG */
				event(new \App\Events\Activity([
					'user_id'           => \Auth::user()->id_user,
					'user_type'         => 'employer',
					'action'            => 'payment-manage-card',
					'reference_type'    => 'users',
					'reference_id'      => \Auth::user()->id_user
				]));
				
				return view('employer.cards.index')->with($data);
			}
		}

		/**
		 * [This method is used for selection of payment card]
		 * @param  Request
		 * @return Json Response
		 */

		public function payment_select_card(Request $request){
			$isMadeDefault  = \Models\PaypalPayment::mark_card_default(\Auth::user()->id_user,$request->card);

			$session        = \Session::get('payment');
			$this->status   = true;
			$this->message  = sprintf(ALERT_SUCCESS,trans("general.M0393"));
			$this->jsondata = \Models\Payments::get_payment_checkout_html(\Auth::user()->id_user);   

			/* RECORDING ACTIVITY LOG */
			event(new \App\Events\Activity([
				'user_id'           => \Auth::user()->id_user,
				'user_type'         => 'employer',
				'action'            => 'payment-select-card',
				'reference_type'    => 'users',
				'reference_id'      => \Auth::user()->id_user
			]));
			
			return response()->json([
				'data'      => $this->jsondata,
				'status'    => $this->status,
				'message'   => $this->message,
			]);
		}

		/**
		 * [This method is used for payment card deletion]
		 * @param  Request
		 * @return Json Response
		 */
		
		public function payment_delete_card(Request $request){
			$data['user_card']  = \Models\PaypalPayment::get_user_card($request->user()->id_user,$request->card_id,'first',['card_token']);
			$isDeleted      	= \Models\PaypalPayment::delete_card($data['user_card']['card_token'],$request->card_id);
			$isMadeDefault  	= \Models\Payments::mark_card_default(\Auth::user()->id_user);

			/* RECORDING ACTIVITY LOG */
			event(new \App\Events\Activity([
				'user_id'           => \Auth::user()->id_user,
				'user_type'         => 'employer',
				'action'            => 'payment-delete-card',
				'reference_type'    => 'users',
				'reference_id'      => \Auth::user()->id_user
			]));

			if($isDeleted){
				$this->status                   = true;
				$this->message                  = sprintf(ALERT_SUCCESS,trans("general.M0110"));
				$this->jsondata                 = \Models\Payments::get_payment_checkout_html(\Auth::user()->id_user);   
			}
			
			return response()->json([
				'data'      => $this->jsondata,
				'status'    => $this->status,
				'message'   => $this->message,
				'redirect'  => $this->redirect,
			]);            
		}

		/**
		 * [This method is used for payment checkout ]
		 * @param  Request
		 * @return \Illuminate\Http\Response
		 */
		
		public function payment_checkout(Request $request){
			$project_id                     = ___decrypt($request->project_id);
			$proposal_id                    = ___decrypt($request->proposal_id);
			
			$is_payment_already_captured = \Models\Payments::is_payment_already_escrowed($project_id);
			
			if(!empty($is_payment_already_captured)){
				$request->session()->flash('alert',sprintf(ALERT_WARNING,trans('general.M0502')));
				return redirect(url(sprintf('%s/project/proposals/talent?proposal_id=%s&project_id=%s',EMPLOYER_ROLE_TYPE,___encrypt($proposal_id),___encrypt($project_id))));
			}

			$request->request->add(['currency' => \Session::get('site_currency')]);
			$default_currency               = \Cache::get('default_currency');

			$data['subheader']              = 'employer.includes.top-menu';
			$data['header']                 = 'innerheader';
			$data['footer']                 = 'innerfooter';
			$data['view']                   = 'employer.payment.checkout';

			$data['user']                   = \Models\Employers::get_user(\Auth::user());
			$data['project']                = \Models\Projects::defaultKeys()->where('id_project',$project_id)->get()->first();
			$data['title']                  = $data['project']->title;
			$data['proposal']               = \Models\Employers::get_proposal($proposal_id,['quoted_price']);
			$data['number_of_days']         = ___get_total_days($data['project']->startdate,$data['project']->enddate);
			$data['default_card_detail']    = \Session::get("card_token");
			$data['back']    				= url(sprintf('%s/project/proposals/talent?proposal_id=%s&project_id=%s',EMPLOYER_ROLE_TYPE,$proposal_id,$project_id));
			
			if(empty($data['default_card_detail'])){
				$data['default_card_detail']    = \Models\PaypalPayment::get_user_default_card(\Auth::user()->id_user,[
	                'id_card',
	                'default',
	                \DB::raw("CONCAT('".asset('/')."','',{$this->prefix}card_type.image) as image_url"),
	                'masked_number',
	                'card_token',
	                'paypal_payer_id',
	                \DB::raw("REPLACE(masked_number,'x', '') as last4")
	            ]);
			}
			if($data['project']['startdate'] >= date('Y-m-d')){
				$is_recurring 		= false;
				$repeat_till_month 	= 0;
				if($data['project']['employment'] == 'hourly'){
					$transaction_sub_total      = $data['proposal']['quoted_price']*$data['proposal']['decimal_working_hours']*$data['number_of_days'];
					$sub_total                  = $data['proposal']['quoted_price']*$data['proposal']['decimal_working_hours']*$data['number_of_days'];
				}else if($data['project']['employment'] == 'monthly'){
					$transaction_sub_total      = ($data['proposal']['quoted_price']);
					$sub_total                  = ($data['proposal']['quoted_price']/MONTH_DAYS)*(($data['number_of_days']));
					$is_recurring 				= ($data['number_of_days'] > MONTH_DAYS) ? true : false;
					$repeat_till_month 			= ($data['number_of_days'])/MONTH_DAYS;
				}else if($data['project']['employment'] == 'fixed'){
					$transaction_sub_total      = $data['proposal']['quoted_price'];
					$sub_total                  = $data['proposal']['quoted_price'];
				}else{
					$request->session()->flash('alert',sprintf(ALERT_WARNING,trans('general.M0371')));
					return redirect(url(sprintf('%s/project/proposals/talent?proposal_id=%s&project_id=%s',EMPLOYER_ROLE_TYPE,___encrypt($proposal_id),___encrypt($project_id))));
				}

				$commission                     			= ___calculate_commission($sub_total,$data['user']['commission'], $data['user']['commission_type']);
				$paypal_commission							= ___calculate_paypal_commission($sub_total);
				$transaction_commission                     = ___calculate_commission($transaction_sub_total,$data['user']['commission'], $data['user']['commission_type']);
				$transaction_paypal_commission				= ___calculate_paypal_commission($transaction_sub_total);

				/*Get price unit for this Job*/
				$price_unit = \Models\Projects::get_project_price_unit($project_id);

				$data['payment']                = [
					'transaction_user_id'       		=> (string) $data['user']['id_user'],
					'transaction_company_id'    		=> (string) $data['user']['id_user'],
					'transaction_user_type'     		=> $data['user']['type'],
					'transaction_project_id'    		=> $project_id,
					'transaction_proposal_id'   		=> $proposal_id,
					'transaction_total'         		=> $sub_total+$commission+$paypal_commission,
					'transaction_subtotal'      		=> $sub_total,
					'transaction_type'          		=> 'debit',
					'transaction_date'          		=> date('Y-m-d H:i:s'),
					'transaction_commission'    		=> $commission,
					'transaction_paypal_commission'		=> $paypal_commission,
					'transaction_is_recurring'			=> $is_recurring,
					'transaction_repeat_till_month'		=> $repeat_till_month,
					'price_unit'						=> $price_unit
				];

				$data['transaction_payment']                = [
					'transaction_user_id'       		=> (string) $data['user']['id_user'],
					'transaction_company_id'    		=> (string) $data['user']['id_user'],
					'transaction_user_type'     		=> $data['user']['type'],
					'transaction_project_id'    		=> $project_id,
					'transaction_proposal_id'   		=> $proposal_id,
					'transaction_total'         		=> $transaction_sub_total+$transaction_commission+$transaction_paypal_commission,
					'transaction_subtotal'      		=> $transaction_sub_total,
					'transaction_type'          		=> 'debit',
					'transaction_date'          		=> date('Y-m-d H:i:s'),
					'transaction_commission'    		=> $transaction_commission,
					'transaction_paypal_commission'		=> $transaction_paypal_commission,
					'transaction_is_recurring'			=> $is_recurring,
					'transaction_repeat_till_month'		=> $repeat_till_month,
					'price_unit'						=> $price_unit
				];

				/*Check if accept escrow is true or false for this proposal*/
				$data['checkPayoutMgmt'] = (bool)0;
				$data_proposal        	 = \Models\Employers::get_proposal($proposal_id);
				$data['checkPayoutMgmt'] = $data_proposal['accept_escrow']=='no' ? true : false;

				/* RECORDING ACTIVITY LOG */
				event(new \App\Events\Activity([
					'user_id'           => \Auth::user()->id_user,
					'user_type'         => 'employer',
					'action'            => 'payment-checkout',
					'reference_type'    => 'users',
					'reference_id'      => \Auth::user()->id_user
				]));
				
				\Session::set('payment',$data['transaction_payment']);
				return view('employer.jobdetail.index')->with($data);
			}else{
				$request->session()->flash('alert',sprintf(ALERT_WARNING,trans('general.M0351')));
				return redirect(url(sprintf('%s/project/proposals/talent?proposal_id=%s&project_id=%s',EMPLOYER_ROLE_TYPE,___encrypt($proposal_id),___encrypt($project_id))));
			}
		}

		/**
		 * [This method is used for final payment and on the same time user's have to enter the card ]
		 * @param  Request
		 * @return \Illuminate\Http\Response
		 */

		public function payment_initiate(Request $request){
			$data['subheader']              = 'employer.includes.top-menu';
			$data['header']                 = 'innerheader';
			$data['footer']                 = 'innerfooter';
			$data['view']                   = 'employer.payment.initiate';
			$data['user']                   = \Models\Employers::get_user(\Auth::user());
			$payment                        = \Session::get("payment");
			
			if(SANDBOX_BRAINTREE_AMOUNT_VALIDATION == 'yes' && $payment['transaction_total'] >= SANDBOX_BRAINTREE_MIN_AMOUNT && $payment['transaction_total'] <= SANDBOX_BRAINTREE_MAX_AMOUNT){
				$request->session()->flash('alert',sprintf(ALERT_WARNING,trans('general.M0481')));
				return redirect(url(sprintf('%s/project/proposals/talent?proposal_id=%s&project_id=%s',EMPLOYER_ROLE_TYPE,___encrypt($payment['transaction_proposal_id']),___encrypt($payment['transaction_project_id']))));
			}

			if(!empty($payment)){

				/* RECORDING ACTIVITY LOG */
				event(new \App\Events\Activity([
					'user_id'           => \Auth::user()->id_user,
					'user_type'         => 'employer',
					'action'            => 'payment-initiate',
					'reference_type'    => 'users',
					'reference_id'      => \Auth::user()->id_user
				]));
				
				if(BYPASS_ESCROW_PAYMENT === true){
					/* BY PASS PAYMENT in LOCAL ENVIRONMENT */
					$payment['redirection'] = url(sprintf("%s/payment/success",EMPLOYER_ROLE_TYPE));
				
				}else if(ESCROW_PAYMENT_TYPE === 'BRAINTREE'){
					$card_details   = \Session::get("card_token");
					
					if(empty($card_details)){
						$card_details = \Models\Payments::get_user_default_card(\Auth::user()->id_user,['token']);
						$card_token     = $card_details['token'];
					}else{
						$card_token     = $card_details['card_token'];
					}
					
					if(!empty($card_details)){
						$result = \Braintree_Transaction::sale([
							'amount' => ___rounding($payment['transaction_total']),
							'merchantAccountId' => env('BRAINTREE_MERCHANT_ACCOUNT_ID'),
							'paymentMethodToken' => $card_token,
							'options' => [
								'submitForSettlement' => true,
							],
						]);

						if(!empty($result->success)){
							$payment['transaction_source'] = 'braintree';
							$payment['transaction_reference_id'] = $result->transaction->id;
							$payment['transaction_status'] = 'confirmed';
						}else{
							$payment['transaction_status'] = 'failed';
						}
						$payment['currency'] = \Cache::get('default_currency');
						
						\Models\Payments::braintree_response([
							'user_id'                   => \Auth::user()->id_user,
							'braintree_response_json'   => json_encode((array)$result->transaction),
							'status'                    => 'false',
							'type'                      => 'sale',
							'created'                   => date('Y-m-d H:i:s')
						]);

						$transaction = \Models\Payments::init_employer_payment($payment);

						if(!empty($result->success)){
							$isProposalAccepted =  \Models\Employers::accept_proposal(\Auth::user()->id_user,$payment['transaction_project_id'],$payment['transaction_proposal_id']);
							$request->session()->flash('alert',sprintf(ALERT_SUCCESS,trans("general.".$isProposalAccepted['message'])));
						
							\Session::forget('payment');
							\Session::forget('card_token');
							return redirect(url(sprintf('%s/project/proposals/talent?proposal_id=%s&project_id=%s',EMPLOYER_ROLE_TYPE,___encrypt($payment['transaction_proposal_id']),___encrypt($payment['transaction_project_id']))));
						}else{
							$request->session()->flash('alert',sprintf(ALERT_DANGER,trans(sprintf("general.%s",$result->errors->deepAll()[0]->code))));
							
							\Session::forget('payment');
							\Session::forget('card_token');
							return redirect(url(sprintf('%s/payment/checkout?project_id=%s&proposal_id=%s',EMPLOYER_ROLE_TYPE,___encrypt($payment['transaction_project_id']),___encrypt($payment['transaction_proposal_id']))));
						}   
					}else{
						$request->session()->flash('alert',sprintf(ALERT_DANGER,trans("general.M0420")));
						
						\Session::forget('payment');
						\Session::forget('card_token');
						return redirect(url(sprintf('%s/payment/checkout?project_id=%s&proposal_id=%s',EMPLOYER_ROLE_TYPE,___encrypt($payment['transaction_project_id']),___encrypt($payment['transaction_proposal_id']))));
					}
				}else if(ESCROW_PAYMENT_TYPE === 'PAYPAL'){
					if($request->payment_type == 'card_payment'){
						$card_details   = \Session::get("card_token");
						if(empty($card_details)){
							$card_details = \Models\PaypalPayment::get_user_default_card(\Auth::user()->id_user,['card_token','paypal_payer_id']);
							$card_details = [
								'card_token' 		=> $card_details['card_token'],
								'paypal_payer_id' 	=> $card_details['paypal_payer_id'],
								'amount' 			=> $payment['transaction_total']
							];
						}else{
							$card_details = [
								'card_token' 		=> $card_details['card_token'],
								'paypal_payer_id' 	=> $card_details['payer_id'],
								'amount' 			=> $payment['transaction_total']
							];
						}
						$payment['transaction'] = \Models\Payments::init_employer_payment($payment,$request->repeat);
						if(!empty($card_details['card_token']) && !empty($card_details['paypal_payer_id']) && !empty($card_details['amount'])){
							$result = \Models\PaypalPayment::payment_checkout($card_details,$payment['transaction_is_recurring'],$payment['transaction_repeat_till_month']);

							if($result['status'] == true){

								if(!empty($result['transaction_type']) && $result['transaction_type'] == "recurring"){
									return redirect($result['redirect_link']);
								}else{
									$paymentData = $result['payment_data']['transactions'][0]->related_resources;

									if(!empty($paymentData)){
										$saleID = $result['payment_data']['transactions'][0]->related_resources[0]->sale->id;

										// /!empty($saleID)
										if(1){

												// if(/*$request->success === TRUE || */app()->environment() !== 'production'){
												$isUpdated = \Models\Payments::update_transaction(
													$payment['transaction']->id_transactions,
													[
														'transaction_reference_id' => $saleID, 
														'transaction_status' => 'confirmed', 
														'updated' => date('Y-m-d H:i:s')
													]
												);

												if(!empty($isUpdated)){
												$isProposalAccepted =  \Models\Employers::accept_proposal(\Auth::user()->id_user,$payment['transaction_project_id'],$payment['transaction_proposal_id']);
												$request->session()->flash('alert',sprintf(ALERT_SUCCESS,trans('general.'.$isProposalAccepted['message'])));
												
												\Session::forget('payment');
												\Session::forget('card_token');
												return redirect(url(sprintf('%s/project/proposals/talent?proposal_id=%s&project_id=%s',EMPLOYER_ROLE_TYPE,___encrypt($payment['transaction_proposal_id']),___encrypt($payment['transaction_project_id']))));
											}else{
												$request->session()->flash('alert',sprintf(ALERT_DANGER,trans('general.'.$isProposalAccepted['message'])));
												
												\Session::forget('payment');
												\Session::forget('card_token');
												return redirect(url(sprintf('%s/payment/checkout?project_id=%s&proposal_id=%s',EMPLOYER_ROLE_TYPE,___encrypt($payment['transaction_project_id']),___encrypt($payment['transaction_proposal_id']))));
											}
										}else{

										}
									}else{
										$request->session()->flash('alert',sprintf(ALERT_DANGER,trans("general.M0323")));
										return redirect(url(sprintf('%s/payment/checkout?project_id=%s&proposal_id=%s',EMPLOYER_ROLE_TYPE,___encrypt($payment['transaction_project_id']),___encrypt($payment['transaction_proposal_id']))));
									}
								}

							}else{
								$isUpdated = \Models\Payments::update_transaction(
									$payment['transaction']->id_transactions,
									[
										'transaction_status' => 'failed', 
										'updated' => date('Y-m-d H:i:s')
									]
								);

								$request->session()->flash('alert',sprintf(ALERT_DANGER,trans("general.M0323")));
								return redirect(url(sprintf('%s/payment/checkout?project_id=%s&proposal_id=%s',EMPLOYER_ROLE_TYPE,___encrypt($payment['transaction_project_id']),___encrypt($payment['transaction_proposal_id']))));
							}
						}else{
							$request->session()->flash('alert',sprintf(ALERT_DANGER,trans("general.M0595")));
							
							\Session::forget('payment');
							\Session::forget('card_token');
							return redirect(url(sprintf('%s/payment/checkout?project_id=%s&proposal_id=%s',EMPLOYER_ROLE_TYPE,___encrypt($payment['transaction_project_id']),___encrypt($payment['transaction_proposal_id']))));						
						}
						/* DO PAYMENT HERE */
						// $provider = \PayPal::setProvider('adaptive_payments');
						// $data = [
						// 	'receivers'  => [[
						// 		'email' => \Cache::get('paypal_adaptive_payment_receiver'),
						// 		'amount' => $payment['amount'],
						// 		'primary' => false,
						// 	]],
						// 	'payer' => 'SENDER', // (Optional) Describes who pays PayPal fees. Allowed values are: 'SENDER', 'PRIMARYRECEIVER', 'EACHRECEIVER' (Default), 'SECONDARYONLY'
						// 	'return_url' => url('payment/success'), 
						// 	'cancel_url' => url('payment/cancel'),
						// ];

						// $result = $provider->createPayRequest($data);
						// $redirect_url = $provider->getRedirectUrl('approved', $result['payKey']);

						// $payment = [
						// 	'redirection' => $redirect_url,
						// ];

						// $data['payment']    = $payment;
						return view('blank')->with($data);
					}else{
						$payment['transaction'] = \Models\Payments::init_employer_payment($payment,$request->repeat);
						\Session::set('payment',$payment);
						$recurring 	= ($request->get('mode') === 'recurring') ? true : false;
				       	$cart 		= \Models\Payments::getCheckoutData($payment,$payment['transaction_is_recurring']);
				       	try {
				           	$response = $this->provider->setExpressCheckout($cart, $payment['transaction_is_recurring']);
				           	return redirect($response['paypal_link']);
				       	} catch (\Exception $e) {
				           	session()->put(['code' => 'danger', 'message' => "Error processing PayPal payment for Order $invoice->id!"]);
				       	}
					}
				}else{
					return redirect()->back();
				}
			}else{
				return redirect()->back();
			}
		}

		/**
		 * [This method is used for requesting payment from escrow ]
		 * @param  Request
		 * @return \Illuminate\Http\Response
		 */
		
		public function payment_callback(Request $request){
			$payment = \Session::get('payment');
			
			if($request->success === TRUE || app()->environment() !== 'production'){
				$isUpdated = \Models\Payments::update_transaction(
					$payment['transaction']->id_transactions,
					[
						'transaction_status' => 'confirmed', 
						'updated' => date('Y-m-d H:i:s')
					]
				);

				if(!empty($isUpdated)){
					$isProposalAccepted =  \Models\Employers::accept_proposal(\Auth::user()->id_user,$payment['transaction_project_id'],$payment['transaction_proposal_id']);
					$request->session()->flash('alert',sprintf(ALERT_SUCCESS,$isProposalAccepted['message']));
					
					\Session::forget('payment');
					\Session::forget('card_token');
					return redirect(url(sprintf('%s/project/proposals/talent?proposal_id=%s&project_id=%s',EMPLOYER_ROLE_TYPE,___encrypt($payment['transaction_proposal_id']),___encrypt($payment['transaction_project_id']))));
				}else{
					$request->session()->flash('alert',sprintf(ALERT_DANGER,$isProposalAccepted['message']));
					
					\Session::forget('payment');
					\Session::forget('card_token');
					return redirect(url(sprintf('%s/payment/checkout?project_id=%s&proposal_id=%s',EMPLOYER_ROLE_TYPE,___encrypt($payment['transaction_project_id']),___encrypt($payment['transaction_proposal_id']))));
				}

			}else{
				$isUpdated = \Models\Payments::update_transaction(
					$payment['transaction']->id_transactions,
					[
						'transaction_status' => 'failed', 
						'updated' => date('Y-m-d H:i:s')
					]
				);

				$request->session()->flash('alert',sprintf(ALERT_DANGER,trans("general.M0323")));
				return redirect(url(sprintf('%s/payment/checkout?project_id=%s&proposal_id=%s',EMPLOYER_ROLE_TYPE,___encrypt($payment['transaction_project_id']),___encrypt($payment['transaction_proposal_id']))));
			}
		}

		public function paypal_payment_billing_success(Request $request){
			$token = $request->get('token');
			$result = \Models\PaypalPayment::execute_billing_agreement(\Auth::user()->id_user,$token);
			$payment = \Session::get('payment');

			if($result['status'] == true){

				$isUpdated = \Models\Payments::update_transactionByIds($payment['transaction_project_id'],$payment['transaction_proposal_id']);
				
				$isProposalAccepted =  \Models\Employers::accept_proposal(\Auth::user()->id_user,$payment['transaction_project_id'],$payment['transaction_proposal_id']);
				$request->session()->flash('alert',sprintf(ALERT_SUCCESS,trans('general.'.$isProposalAccepted['message'])));
				//'Payment successful. Plan implemented by recurring'
				\Session::forget('payment');
				\Session::forget('card_token');
				return redirect(url(sprintf('%s/project/proposals/talent?proposal_id=%s&project_id=%s',EMPLOYER_ROLE_TYPE,___encrypt($payment['transaction_proposal_id']),___encrypt($payment['transaction_project_id']))));
			}else{
				$request->session()->flash('alert',sprintf(ALERT_DANGER,trans('website.W0921')));
				return redirect(url(sprintf('employer/project/proposals/talent?proposal_id=%s&project_id=%s',$payment['transaction_proposal_id'],$payment['transaction_project_id'])));

			}

		}

		public function paypal_payment_billing_cancel(Request $request){

			$payment = \Session::get('payment');
			return redirect(url(sprintf('employer/project/proposals/talent?proposal_id=%s&project_id=%s',$payment['transaction_proposal_id'],$payment['transaction_project_id'])));			

		}

		public function paypal_payment_success(Request $request){
			$payment = \Session::get('payment');
	        $recurring = ($request->get('mode') === 'recurring') ? true : false;
	        $token = $request->get('token');
	        $PayerID = $request->get('PayerID');

	        $cart = \Models\Payments::getCheckoutData($payment,$recurring);

	        // Verify Express Checkout Token
	        $response = $this->provider->getExpressCheckoutDetails($token);
            \Models\PaypalPayment::paypal_response([
                'user_id'                   => \Auth::user()->id_user,
                'response_json'             => json_encode($response),
                'request_type'              => 'get express checkout data',
                'status'                    => 'true',
                'created'                   => date('Y-m-d H:i:s')
            ]);

	        if (in_array(strtoupper($response['ACK']), ['SUCCESS', 'SUCCESSWITHWARNING'])) {
	            if ($recurring === true) {
	                $response = $this->provider->createMonthlySubscription($response['TOKEN'],$cart['total'], $cart['subscription_desc'],'USD',$payment['transaction_repeat_till_month']);
                	$profileesponse = $this->provider->getRecurringPaymentsProfileDetails($response['PROFILEID']);
	                if (!empty($response['PROFILESTATUS']) && in_array($response['PROFILESTATUS'], ['ActiveProfile', 'PendingProfile'])) {
	                    $status = 'Processed';
	                } else {
	                    $status = 'Invalid';
	                }
	            } else {
	                // Perform transaction on PayPal
	                $payment_status = $this->provider->doExpressCheckoutPayment($cart, $token, $PayerID);

		            \Models\PaypalPayment::paypal_response([
		                'user_id'                   => \Auth::user()->id_user,
		                'response_json'             => json_encode($payment_status),
		                'request_type'              => 'accept payment propsal [express checkout]',
		                'status'                    => 'true',
		                'created'                   => date('Y-m-d H:i:s')
		            ]);

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
						$isProposalAccepted =  \Models\Employers::accept_proposal(\Auth::user()->id_user,$payment['transaction_project_id'],$payment['transaction_proposal_id']);
						$request->session()->flash('alert',sprintf(ALERT_SUCCESS,trans('general.'.$isProposalAccepted['message'])));
						
						\Session::forget('payment');
						\Session::forget('card_token');
						return redirect(url(sprintf('%s/project/proposals/talent?proposal_id=%s&project_id=%s',EMPLOYER_ROLE_TYPE,___encrypt($payment['transaction_proposal_id']),___encrypt($payment['transaction_project_id']))));
					}else{
						$request->session()->flash('alert',sprintf(ALERT_DANGER,trans('general.'.$isProposalAccepted['message'])));
						
						\Session::forget('payment');
						\Session::forget('card_token');
						return redirect(url(sprintf('%s/payment/checkout?project_id=%s&proposal_id=%s',EMPLOYER_ROLE_TYPE,___encrypt($payment['transaction_project_id']),___encrypt($payment['transaction_proposal_id']))));
					}
				}else{
					$isUpdated = \Models\Payments::update_transaction(
						$payment['transaction']->id_transactions,
						[
							'transaction_status' => 'failed', 
							'updated' => date('Y-m-d H:i:s')
						]
					);

					$request->session()->flash('alert',sprintf(ALERT_DANGER,trans("general.M0323")));
					return redirect(url(sprintf('%s/payment/checkout?project_id=%s&proposal_id=%s',EMPLOYER_ROLE_TYPE,___encrypt($payment['transaction_project_id']),___encrypt($payment['transaction_proposal_id']))));
				}
	        }
		}

		public function paypal_payment_cancel(Request $request){
			$payment = \Session::get('payment');
			return redirect(url(sprintf('employer/project/proposals/talent?proposal_id=%s&project_id=%s',$payment['transaction_proposal_id'],$payment['transaction_project_id'])));
		}		

		/**
		 * [This method is used for payment]
		 * @param  Request
		 * @return \Illuminate\Http\Response
		 */ 

		public function payments(Request $request, Builder $htmlBuilder, $type = 'all'){
			$request->request->add(['currency' => \Session::get('site_currency')]);
			$data['subheader']              = 'employer.includes.top-menu';
			$data['title']                  = trans('website.W0600');
			$data['header']                 = 'innerheader';
			$data['footer']                 = 'innerfooter';
			$data['view']                   = 'employer.payment.list';

			$data['user']                   = \Models\Employers::get_user(\Auth::user());
			$data['payment_summary']        = \Models\Payments::summary($data['user']['id_user'],'employer');
			
			$count = 0;
			if ($request->ajax()) {
				$payments = \Models\Payments::listing($data['user']['id_user'],'employer',$type);
				return \Datatables::of($payments)->filter(function ($instance) use ($request) {
					if ($request->has('sort')) {
						if(!empty($request->sort)){
							$sort = explode(" ", ___decodefilter($request->sort));

							if(count($sort) == 2){
								if($sort[1] == "ASC"){
									$instance->collection = $instance->collection->sortBy(function ($row) use ($sort) {
										return (!empty($row->$sort[0]))? $row->$sort[0]: false;
									});
								}else if($sort[1] == "DESC"){
									$instance->collection = $instance->collection->sortByDesc(function ($row) use ($sort) {
										return (!empty($row->$sort[0]))? $row->$sort[0]: false;
									});
								}
							}
						}
					}else{
						$instance->collection = $instance->collection->sortBy(function ($row){
							return $row->transaction_status;
						});
					}

					if ($request->has('search')) {
						if(!empty($request->search['value'])){
							$instance->collection = $instance->collection->filter(function ($row) use ($request) {
								return (\Str::contains($row->title, $request->search['value']) || \Str::contains($row->company_name, $request->search['value']) || \Str::contains($row->quoted_price, $request->search['value'])) ? true : false;
							});
						} 
					}
				})
				->editColumn('title',function($payment) use($type){
					if($type == 'all'){
						$payment->transaction_subtotal = ___calculate_payment($payment->employment,$payment->quoted_price);
					}

					$html = '<div class="content-box-header clearfix">';
						$html .= '<div class="row payment-contentbox">';
							$html .= '<div class="col-md-9 col-sm-8 col-xs-7">';
								$html .= '<div class="contentbox-header-title">';
									$html .= '<h3><a href="'.url(sprintf('%s/project/details?job_id=%s',EMPLOYER_ROLE_TYPE, ___encrypt($payment->transaction_project_id))).'">'.$payment->title.'</a></h3>';
									$html .= '<span class="company-name">'.$payment->company_name.'</span>';
								$html .= '</div>';
							$html .= '</div>';
						$html .= '</div>';
					$html .= '</div>';
					$html .= '<div class="contentbox-minutes clearfix">';
						$html .= '<div class="minutes-left">';
							$html .= '<span>'.trans('website.W0368').' : <strong>'.___d($payment->transaction_date).'</strong></span>';
							$html .= '<span>'.trans('website.W0369').'  <strong> '.___readable($payment->transaction,true).'</strong></span>';
							$html .= '<span>'.trans('website.W0726').' : <strong>'.$payment->currency.___format($payment->transaction_subtotal,true,false).'</strong></span>';
						$html .= '</div>';
					$html .= '</div>';

					return $html;
				})
				->make(true);
			} 

			$data['html'] = $htmlBuilder
			->parameters([
				"dom" => "<'row'> rt <'row'<'col-md-6'i><'col-md-6'p> >",
				"language" => [
					"sInfo" => "Showing _START_ - _END_ out of _TOTAL_ record(s)",
					"sInfoEmpty"=> "No record(s) found. ",
				]
			])
			->addColumn(['data' => 'title', 'name' => 'title', 'title' => '&nbsp;', 'width' => '0', 'searchable' => false, 'orderable' => false]);

			return view('employer.payment.index')->with($data);
		}

		/**
		 * [This method is used for user's Portfolio]
		 * @param  Request
		 * @return \Illuminate\Http\Response
		 */ 

		public function talent_portfolio(Request $request, Builder $htmlBuilder){
			if(!empty($request->talent_id)){
				$talent_id = ___decrypt($request->talent_id);
			}

			if(empty($talent_id)){
				return redirect(sprintf('%s/find-talents',EMPLOYER_ROLE_TYPE));
			}

			$data['title']                  = trans('website.W0602');
			$data['subheader']              = 'employer.includes.top-menu';
			$data['header']                 = 'innerheader';
			$data['footer']                 = 'innerfooter';
			$data['view']                   = 'employer.talent.portfolio'; 

			$data['submenu']                = 'portfolio';
			$data['top_talent_user']        = \Models\Talents::top_talent_user($talent_id);
			$data['talent_id']              = ___encrypt($talent_id);
			$data['user']                   = \Models\Employers::get_user(\Auth::user(),true);
			$data['talent']                 = \Models\Talents::get_user((object)['id_user' => $talent_id],true);
			
			$data['ownerDetail'] = $ownerDetail	= \Models\companyConnectedTalent::where('id_user',$talent_id)->where('user_type','owner')->first();

			if(count($ownerDetail)>0){
				$data['talent']['connectedTalent']	= \Models\companyConnectedTalent::where('id_talent_company',$ownerDetail->id_talent_company)->where('user_type','user')->count();
				$firm_juri	=	\DB::table('firm_jurisdiction as fj')->join('countries as c','c.id_country','fj.country_id')->select(\DB::Raw("IF(({$this->language} != ''),GROUP_CONCAT(`{$this->language}`), GROUP_CONCAT(`en`)) as country_name"))->where('company_id',$ownerDetail->id_talent_company)->first();
				$data['talent']['countries'] = @$firm_juri->country_name;

				if($data['talent']['talentCompany']->company_logo ==  null){
					$data['talent']['talentCompany']->company_logo = asset(sprintf('images/%s',DEFAULT_AVATAR_IMAGE));
				}
				
			}else{
				
				$data['talent']['connectedTalent']	= '0';
			}


			$viewed_talent                  = [
				'employer_id'   => \Auth::user()->id_user,
				'talent_id'     => $talent_id,
				'updated'       => date('Y-m-d h:i:s'),
				'created'       => date('Y-m-d h:i:s')
			];
						
			$data['last_viewed']            = ___ago(\Models\ViewedTalents::add_viewed_talent($viewed_talent));
			
			if ($request->ajax()) {

				// $portfolioes = \Models\Portfolio::get_portfolio($talent_id,NULL,'object');
				$data['isOwner'] 		= \Models\companyConnectedTalent::with(['user'])->where('id_user',$talent_id)->where('user_type','owner')->get()->first();
				$data['isOwner'] 		= json_decode(json_encode($data['isOwner']));

				if(!empty($data['isOwner'])){
					$data['connected_user'] = \Models\companyConnectedTalent::select('id_user')->where('id_talent_company',$data['isOwner']->id_talent_company)->where('user_type','user')->get();
					$data['connected_user'] = json_decode(json_encode($data['connected_user']),true);
				}else{
					$data['connected_user'] = [];
				}
				$talent_ids[] = $talent_id;
				$user_ids = array_column($data['connected_user'], 'id_user');
				$user_ids = array_merge($user_ids,$talent_ids);

				$table_portfolio = \DB::table('talent_portfolio as portfolio');
                $keys = [
                    'portfolio.id_portfolio',
                    'portfolio.portfolio',
                    'portfolio.description',
                    'portfolio.created',
                ];

                $table_portfolio->select($keys);
                // $table_portfolio->where('portfolio.user_id', '=', $talent_id);
                $table_portfolio->whereIn('portfolio.user_id', $user_ids);
                $table_portfolio->groupBy(['portfolio.id_portfolio']);
                $table_portfolio->orderBy('portfolio.id_portfolio','DESC');
                
                $result = $table_portfolio->get();
                foreach ($result as &$item) {
                    $table_files = \DB::table('files');
                    $table_files->select(['id_file','filename','folder','extension']);
                    $table_files->where('files.record_id',$item->id_portfolio);
                    $table_files->where('files.type','portfolio');
                    $table_files->orderBy('files.id_file','DESC');

                    $item->file = json_decode(json_encode($table_files->get()),true);
                }

				return \Datatables::of($result)
				->editColumn('portfolio',function($file) use($talent_id){

					if(!empty($file->file)){
					$get_file = $file->file[0];
					$url_delete = '';
						return  sprintf(EMPLOYER_VIEW_PORTFOLIO_TEMPLATE,
	                            $get_file['id_file'],
                            	url(sprintf('/download/file?file_id=%s',___encrypt($get_file['id_file']))),
                            	asset('/'),
                            	$get_file['extension'],
                            	$get_file['filename']
	                        );
					}else{
						return '';
					}

				})
				->make(true);
			}

			$data['html'] = $htmlBuilder
			->parameters([
				"dom" => "<'row' <'col-md-3'f><'col-md-3'><'col-md-6 filter-option'>>rt<'row' <'col-md-6'i><'col-md-6'p>>",
				"bInfo" => false,
			])
			->addColumn(['data' => 'portfolio', 'name' => 'portfolio', 'title' => '&nbsp;']);

			return view('employer.talent.index')->with($data);     
		}

		/**
		 * [This method is used for user's Interview]
		 * @param  Request
		 * @return \Illuminate\Http\Response
		 */ 
		
		public function talent_interview(Request $request){
			if(!empty($request->talent_id)){
				$talent_id = ___decrypt($request->talent_id);
			}

			if(empty($talent_id)){
				return redirect(sprintf('%s/find-talents',EMPLOYER_ROLE_TYPE));
			}

			$data['subheader']              = 'employer.includes.top-menu';
			$data['header']                 = 'innerheader';
			$data['footer']                 = 'innerfooter';
			$data['view']                   = 'employer.interview.view';

			$data['submenu']                = 'interview';
			$data['talent_id']              = ___encrypt($talent_id);
			$data['user']                   = \Models\Employers::get_user(\Auth::user(),true);
			$data['talent']                 = \Models\Talents::get_user((object)['id_user' => $talent_id],true);
			$data['title']                  = sprintf(trans('website.W0603'),sprintf('%s %s',$data['talent']['first_name'],$data['talent']['last_name']));
			$data['country_phone_codes']    = \Cache::get('country_phone_codes');
			$data['countries']              = \Cache::get('countries');
			$data['states']                 = \Cache::get('states');
			$data['degree_name']            = \Cache::get('degree_name');
			$data['top_talent_user']        = \Models\Talents::top_talent_user($talent_id);
			$data['education_list']         = \Models\Talents::educations($talent_id);
			$data['work_experience_list']   = \Models\Talents::work_experiences($talent_id);
			$data['get_files']              = \Models\Talents::get_file(sprintf("user_id = %s AND type = 'certificates' ", $talent_id));

			$data['upgrade_later_url']     = url(sprintf("%s/find-talents",EMPLOYER_ROLE_TYPE));
			$data['upgrade_now_url']     = url(sprintf("%s/hire-premium-talents",EMPLOYER_ROLE_TYPE));

			$viewed_talent                  = [
				'employer_id'   => \Auth::user()->id_user,
				'talent_id'     => $talent_id,
				'updated'       => date('Y-m-d h:i:s'),
				'created'       => date('Y-m-d h:i:s')
			];
						
			$data['last_viewed']            = ___ago(\Models\ViewedTalents::add_viewed_talent($viewed_talent));
			
			$talentAnswerExist = Interview::talentAnswerExist($talent_id);

			if(!empty($talentAnswerExist)){
				$data['talentAnswerExist'] = 1;
			}
			else{
				$data['talentAnswerExist'] = 0;
			}

			/* RECORDING ACTIVITY LOG */
			event(new \App\Events\Activity([
				'user_id'           => \Auth::user()->id_user,
				'user_type'         => 'employer',
				'action'            => 'talent-interview',
				'reference_type'    => 'talent',
				'reference_id'      => $talent_id
			]));


			$result = Interview::getQuestionResponse($talent_id);
			$data['questionList']   = $result['questionList'];
			$data['total']          = $result['total'];
			$data['optain']         = $result['optain'];

			return view('employer.job.index')->with($data);
		}

		/**
		 * [This method is used for plan purchase]
		 * @param  Request
		 * @return \Illuminate\Http\Response
		 */
		
		public function plan_purchase(Request $request){
			$request->request->add(['currency' => \Session::get('site_currency')]);
			$id_plan                    = ___decrypt($request->id_plan);
			$data['id_plan']            = $request->id_plan;

			$data['subheader']          = 'employer.includes.top-menu';
			$data['title']              = trans('website.W0604');
			$data['header']             = 'innerheader';
			$data['footer']             = 'innerfooter';
			$data['view']               = 'employer.payment.plan-checkout';

			/*Plans*/
			$data['plan']               = \Models\Plan::getPlanDetail($id_plan);

			if(empty($data['plan'])){
				return redirect(sprintf("%s/hire-premium-talents",EMPLOYER_ROLE_TYPE));
			}
			$data['plan']                   = json_decode(json_encode($data['plan']), true);
			$data['default_card_detail']    = \Session::get("card_token");
			if(empty($data['default_card_detail'])){
				$data['default_card_detail']    = \Models\Payments::get_user_default_card(\Auth::user()->id_user);
			}

			$data['user']                   = \Models\Employers::get_user(\Auth::user());
			$data['user_card']              = \Models\Payments::get_user_card(\Auth::user()->id_user);

			$data['search']                 = (!empty($request->search))?$request->search:"";

			$data['plan_payment']           = [
				'transaction_user_id'       => (string) $data['user']['id_user'],
				'transaction_user_type'     => $data['user']['type'],
				'transaction_project_id'    => $id_plan,
				'transaction_total'         => $data['plan']['price'],
				'transaction_type'          => 'subscription',
				'transaction_date'          => date('Y-m-d H:i:s')
			];

			/* RECORDING ACTIVITY LOG */
			event(new \App\Events\Activity([
				'user_id'           => \Auth::user()->id_user,
				'user_type'         => 'employer',
				'action'            => 'plan-purchase',
				'reference_type'    => 'plan',
				'reference_id'      => $id_plan
			]));

			\Session::set('plan_payment',$data['plan_payment']);
			return view('employer.profile.index')->with($data);
		}

		/**
		 * [This method is used for final payment and on the same time card also have to enter]
		 * @param  Request
		 * @return \Illuminate\Http\Response
		 */

		public function plan_payment_initiate(Request $request){
			$request->request->add(['currency' => \Session::get('site_currency')]);
			$id_plan         = ___decrypt($request->id_plan);
			$plan               = \Models\Plan::getPlanDetail($id_plan);

			if(empty($plan)){
				return redirect(sprintf("%s/hire-premium-talents",EMPLOYER_ROLE_TYPE));
			}

			$data['subheader']              = 'employer.includes.top-menu';
			$data['header']                 = 'innerheader';
			$data['footer']                 = 'innerfooter';
			$data['view']                   = 'employer.payment.initiate';

			$data['user']                   = \Models\Employers::get_user(\Auth::user());
			$payment                        = \Session::get("plan_payment");
			$card_details                   = \Session::get("card_token");

			if(!empty($payment)){

				if(ESCROW_PAYMENT_TYPE === 'BRAINTREE'){
					if(empty($card_details)){
						$card_details   = \Models\Payments::get_user_default_card(\Auth::user()->id_user,['token']);
						$card_token     = $card_details['token'];
					}else{
						$card_token     = $card_details['card_token'];
					}
					if(!empty($card_token)){
						$result = \Braintree_Subscription::create([
							'planId'                => $plan->braintree_plan_id,
							'merchantAccountId'     => env('BRAINTREE_MERCHANT_ACCOUNT_ID'),
							'paymentMethodToken'    => $card_token
						]);

						$subscriptionData = [
							'id_plan'                   => $id_plan,
							'id_user'                   => \Auth::user()->id_user,
							'balance'                   => $result->subscription->balance,
							'billingDayOfMonth'         => $result->subscription->billingDayOfMonth,
							'currentBillingCycle'       => $result->subscription->currentBillingCycle,
							'daysPastDue'               => $result->subscription->daysPastDue,
							'failureCount'              => $result->subscription->failureCount,
							'firstBillingDate'          => $result->subscription->firstBillingDate->format('Y-m-d H:i:s'),
							'id'                        => $result->subscription->id,
							'merchantAccountId'         => $result->subscription->merchantAccountId,
							'neverExpires'              => $result->subscription->neverExpires,
							'nextBillAmount'            => $result->subscription->nextBillAmount,
							'nextBillingPeriodAmount'   => $result->subscription->nextBillingPeriodAmount,
							'nextBillingDate'           => $result->subscription->nextBillingDate->format('Y-m-d H:i:s'),
							'numberOfBillingCycles'     => $result->subscription->numberOfBillingCycles,
							'paidThroughDate'           => $result->subscription->paidThroughDate->format('Y-m-d H:i:s'),
							'paymentMethodToken'        => $result->subscription->paymentMethodToken,
							'planId'                    => $result->subscription->planId,
							'price'                     => $result->subscription->price,
							'status'                    => $result->subscription->status,
							'trialDuration'             => $result->subscription->trialDuration,
							'trialDurationUnit'         => $result->subscription->trialDurationUnit,
							'trialPeriod'               => $result->subscription->trialPeriod,
							'updated'                   => date('Y-m-d H:i:s'),
							'created'                   => date('Y-m-d H:i:s'),
						];

						\Models\Payments::subscriptionResponse($subscriptionData);

						if(!empty($result->success)){
							$payment['transaction_source']          = 'braintree';
							$payment['transaction_reference_id']    = $result->subscription->transactions[0]->id;
							$payment['transaction_status']          = 'confirmed';
						}else{
							$payment['transaction_status']          = 'failed';
						}

						\Models\Payments::braintree_response([
							'user_id'                   => \Auth::user()->id_user,
							'braintree_response_json'   => json_encode((array)$result->subscription),
							'status'                    => 'false',
							'type'                      => 'sale',
							'created'                   => date('Y-m-d H:i:s')
						]);

						$transaction = \Models\Payments::init_employer_payment(
								$payment,
								$request->repeat
							);

						if(!empty($result->success)){
							\Models\Users::change(
								\Auth::user()->id_user,
								[
								'is_subscribed'=>'yes',
								'braintree_subscription_id'=> $result->subscription->id,
								]
							);
						}

						if(!empty($result->success)){
							$request->session()->flash('alert',sprintf(ALERT_SUCCESS,'Plan has been successfully subscribed'));

							\Session::forget('plan_payment');
							\Session::forget('card_token');
							return redirect(url(sprintf('%s/hire-premium-talents',EMPLOYER_ROLE_TYPE)));
						}else{
							$request->session()->flash('alert',sprintf(ALERT_DANGER,trans(sprintf("general.%s",$result->errors->deepAll()[0]->code))));

							\Session::forget('payment');
							\Session::forget('card_token');
							return redirect(url(sprintf('%s/plan-purchase/'.$request->id_plan,EMPLOYER_ROLE_TYPE)));
						}
					}else{
						$request->session()->flash('alert',sprintf(ALERT_DANGER,trans("general.M0420")));

						\Session::forget('payment');
						\Session::forget('card_token');
						return redirect(url(sprintf('%s/plan-purchase/'.$request->id_plan,EMPLOYER_ROLE_TYPE)));
					}

					/* RECORDING ACTIVITY LOG */
					event(new \App\Events\Activity([
						'user_id'           => \Auth::user()->id_user,
						'user_type'         => 'employer',
						'action'            => 'plan-payment-initiate',
						'reference_type'    => 'plan',
						'reference_id'      => $id_plan
					]));

				}else{
					return redirect()->back();
				}
			}else{
				return redirect()->back();
			}
		}

		/**
		 * [This method is used for requesting payment from Braintree Api]
		 * @param  Request
		 * @return Json Response
		 */

		public function plan_payment_callback(Request $request){
			$payment = \Session::get('payment');

			if($request->success === TRUE || app()->environment() !== 'production'){
				$isUpdated = \Models\Payments::update_transaction(
					$payment['transaction']->id_transactions,
					[
						'transaction_status' => 'confirmed',
						'updated' => date('Y-m-d H:i:s')
					]
				);

				if(!empty($isUpdated)){
					$isProposalAccepted =  \Models\Employers::accept_proposal(\Auth::user()->id_user,$payment['transaction_project_id'],$payment['transaction_proposal_id']);
					$request->session()->flash('alert',sprintf(ALERT_SUCCESS,$isProposalAccepted['message']));

					\Session::forget('payment');
					\Session::forget('card_token');
					return redirect(url(sprintf('%s/project/proposals/talent?proposal_id=%s&project_id=%s',EMPLOYER_ROLE_TYPE,___encrypt($payment['transaction_proposal_id']),___encrypt($payment['transaction_project_id']))));
				}else{
					$request->session()->flash('alert',sprintf(ALERT_DANGER,$isProposalAccepted['message']));

					\Session::forget('payment');
					\Session::forget('card_token');
					return redirect(url(sprintf('%s/payment/checkout?project_id=%s&proposal_id=%s',EMPLOYER_ROLE_TYPE,___encrypt($payment['transaction_project_id']),___encrypt($payment['transaction_proposal_id']))));
				}

			}else{
				$isUpdated = \Models\Payments::update_transaction(
					$payment['transaction']->id_transactions,
					[
						'transaction_status' => 'failed',
						'updated' => date('Y-m-d H:i:s')
					]
				);

				$request->session()->flash('alert',sprintf(ALERT_DANGER,trans("general.M0323")));
				return redirect(url(sprintf('%s/payment/checkout?project_id=%s&proposal_id=%s',EMPLOYER_ROLE_TYPE,___encrypt($payment['transaction_project_id']),___encrypt($payment['transaction_proposal_id']))));
			}
		}

		/**
		 * [This method is used for handling Job action like start job,close job etc]
		 * @param  Request
		 * @return Json Response
		 */

		public function jobactions(Request $request){
			$request->request->add(['currency' => \Session::get('site_currency')]);
			$job_id                 = ___decrypt($request->job_id);
			
			$keys = [
				'projects.id_project',
				'projects.user_id as company_id',
				\DB::Raw("COUNT(DISTINCT({$this->prefix}proposals.id_proposal)) total_accepted_proposal"),
				\DB::Raw("{$this->prefix}proposals.user_id as accepted_talent_id"),
				\DB::Raw("EMPLOYER_JOB_STATUS({$this->prefix}projects.employment,COUNT({$this->prefix}proposals.id_proposal),{$this->prefix}projects.project_status) as job_current_status"),
			];
			
			$data['job_details']    = \Models\Employers::get_job(" id_project = {$job_id} ","single",$keys);

			$this->status   = true;
			$this->jsondata = view('employer.job.includes.job-detail')->with($data)->render();

			return response()->json([
				'data'      => $this->jsondata,
				'status'    => $this->status,
				'message'   => $this->message,
				'redirect'  => $this->redirect,
			]);            
		}

		/**
		 * [This method is used for handling Job action like start job,close job etc]
		 * @param  Request
		 * @return Json Response
		 */

		public function job_actions(Request $request){
			$project_id             = ___decrypt($request->job_id);
			
			$job_detail = \Models\Projects::employer_actions($project_id);
			
			$this->status   = true;
			$this->jsondata = [
				'html' => view('employer.job.includes.job-detail')->with(compact('job_detail'))->render(),
				'receiver_id' => $job_detail['receiver_id'] 
			];

			return response()->json([
				'data'      => $this->jsondata,
				'status'    => $this->status,
				'message'   => $this->message,
				'redirect'  => $this->redirect,
			]);            
		}

		/**
		 * [This method is used for hiring user's]
		 * @param  Request
		 * @return \Illuminate\Http\Response
		 */

		public function hire_talent(Request $request){
			$request->request->add(['currency' => \Session::get('site_currency')]);
			if($request->ajax()){
				$data['talent_id'] = $request->talent_id;
				
				if($request->page != 'existingjob'){
					return view('employer.hire.popup',$data);
				}else{
					$projects 		= \Models\Projects::employer_jobs(\Auth::user());
					$project_lists 	= $projects->withCount(['proposals'])
					->where('awarded','=',DEFAULT_NO_VALUE)
					->having('is_cancelled','=',DEFAULT_NO_VALUE)
					->orderBy("projects.created","DESC")
					->groupBy(['projects.id_project'])
					->get();

					$submitted_jobs = [];
					foreach ($project_lists as $item) {
						$submitted_jobs[$item->id_project] = sprintf("#%'.0".JOBID_PREFIX."d - %s",$item->id_project,$item->title);
					}

					$data['submitted_jobs'] = $submitted_jobs;
					return view('employer.hire.existingjob',$data);
				}
			}
		}

		/**
		 * [This method is used existing job]
		 * @param  Request
		 * @return \Illuminate\Http\Response
		 */

		public function existingjob(Request $request){
			$validator = \Validator::make($request->all(), [
				'job' => validation('record_id')
			],[
				'job.required' => trans('website.W0830')
			]);

			if($validator->passes()){
 				$project_id 	= $request->job;
 				$talent_id 		= ___decrypt($request->talent_id);
 				$employer_id 	= \Auth::user()->id_user;

 				$isRequestSent		= \Models\Chats::employer_chat_request($employer_id,$talent_id,$project_id);
				$isInvitationSent = \Models\Projects::is_invitation_sent($employer_id,$talent_id,$project_id);
		    	$group_id 		= \Models\Chats::getChatRoomGroupId($talent_id,$employer_id,$project_id);
			    
				if(empty($isInvitationSent)){
					$isNotified = \Models\Notifications::notify(
				        $talent_id,
				        $employer_id,
				        'JOB_INVITATION_SENT_BY_EMPLOYER',
				        json_encode([
				            'user_id'    => (string) $employer_id,
				            'talent_id'  => (string) $talent_id,
				            'project_id' => (string) $project_id,
				            'group_id'   => (string) $project_id
				        ])
				    );


				    $isSaved = \Models\Chats::addmessage([
	                    'message'      => trans('website.W0836'),
	                    'sender_id'    => $employer_id,
	                    'receiver_id'  => $talent_id,
	                    'message_type' => 'text',
	                    'group_id'     => $group_id
	                ]);
                }

                $user_details = (array)\Models\Users::findById($talent_id);

                //Send email to talent
                $prefix = DB::getTablePrefix();
                $data['project_detail'] = \Models\Projects::defaultKeys()
				            ->projectDescription()
				            ->companyName()
				            ->companyLogo()
				            ->with([
								'industries.industries' => function($q) use($prefix){
									$q->select(
										'id_industry',
										'en as name'
									);
								},
								'subindustries.subindustries' => function($q) use($prefix){
									$q->select(
										'id_industry',
										'en as name'
									);
								},
								'skills.skills' => function($q){
									$q->select(
										'id_skill',
										'skill_name'
									);
								},
								'employer' => function($q) use($prefix){
									$q->select(
										'id_user',
										'company_name',
										'company_biography',
										\DB::Raw("YEAR({$prefix}users.created) as member_since"),
										\DB::Raw("CONCAT({$prefix}users.first_name, ' ',{$prefix}users.last_name) AS name")
									);
									$q->companyLogo();
									$q->city();
									$q->review();
									$q->totalHirings();
									$q->withCount('projects');
								},
								'chat'
							])->where('id_project', $project_id)->first();

				$project_detail = (json_decode(json_encode($data['project_detail']),true));

				if(!empty($project_detail)){
					$emailData              = ___email_settings();
					$emailData['name']      = $user_details['name'];
					$emailData['email']     = $user_details['email'];

					$emailData['project_type'] = employment_types('post_job',$project_detail['employment']);
					$emailData['industry'] = ___tags(array_column(array_column($project_detail['industries'], 'industries'),'name'),'<span class="small-tags">%s</span>','');

					$emailData['subindustry'] =  ___tags(array_column(array_column($project_detail['subindustries'], 'subindustries'),'name'),'<span class="small-tags">%s</span>','');

					$emailData['required_skills'] =  ___tags(array_column(array_column($project_detail['skills'], 'skills'),'skill_name'),'<span class="small-tags">%s</span><br/>','');

					$emailData['expertise_level'] = !empty($project_detail['expertise']) ? expertise_levels($project_detail['expertise']) : N_A;
					$emailData['timeline'] = ___date_difference($project_detail['startdate'],$project_detail['enddate']);
					$emailData['description'] = strip_tags(nl2br($project_detail['description']));

					___mail_sender($user_details['email'],sprintf('%s %s',$user_details['first_name'], $user_details['last_name']),'existing_job',$emailData);
				}

                $this->status = true;
				$this->message = trans('general.M0590');
				$this->redirect = url(sprintf('%s/chat?receiver_id=%s',EMPLOYER_ROLE_TYPE,___encrypt($group_id)));

			}else{
		        $this->jsondata = ___error_sanatizer($validator->errors());
		    }

		    return response()->json([
                'data'      => $this->jsondata,
                'status'    => $this->status,
                'message'   => $this->message,
                'redirect'  => $this->redirect,
                'nomessage' => true,
            ]);
		}

		public function prepare_message(Request $request){
			$request->request->add(['currency' => \Session::get('site_currency')]);
			if($request->ajax()){
				$data['talent_id'] = $request->talent_id;
				return view('employer.hire.hire_message',$data);
			}

		}

		/**
		 * [This method is used sending message] [Now Post]
		 * @param  Request
		 * @return \Illuminate\Http\Response
		 */

		public function sendmessage(Request $request){

			$validator = \Validator::make($request->all(), [
				'talent_id' 			=> validation('record_id'),
				'send_message' 			=> 'required'
			],[
				'talent_id.required' 	=> trans('general.M0592'), 
				'send_message.required' => trans('general.M0604')
			]);

			if($validator->passes()){

 				$employer_id 			= \Auth::user()->id_user;
 				$talent_id 				= ___decrypt($request->talent_id);
 				$is_chat_room_created 	= \Models\Projects::is_chat_room_created($employer_id,$talent_id);
 				$project_id 			= \Models\Projects::create_dummp_job($employer_id,$talent_id);

			    $isRequestSent 			= \Models\Chats::employer_chat_request($employer_id,$talent_id,$project_id);
			    
			    $isChatStarted 			= \Models\Projects::select('id_project')->where('title',JOB_TITLE)->where('talent_id',$talent_id)->where('user_id',$employer_id)->get();

			    $group_id 			= \Models\Chats::getChatRoomGroupId($talent_id,$employer_id,$project_id);
			    if(empty($is_chat_room_created)){

				    $isSaved = \Models\Chats::addmessage([
	                    'message'      => $request['send_message'],
	                    'sender_id'    => $employer_id,
	                    'receiver_id'  => $talent_id,
	                    'message_type' => 'text',
	                    'group_id'     => $group_id
	                ]);
			    }

                $this->status = true;
				$this->message = trans('general.M0590');
				$this->redirect = url(sprintf('%s/chat?receiver_id=%s',EMPLOYER_ROLE_TYPE,___encrypt($group_id)));

			}else{
		        $this->jsondata = ___error_sanatizer($validator->errors());
		    }

		    return response()->json([
                'data'      => $this->jsondata,
                'status'    => $this->status,
                'message'   => $this->message,
                'redirect'  => $this->redirect,
                'nomessage' => true,
            ]);
		}


		/**
		 * [This method is used for raise job dispute in detail ]
		 * @param  Request
		 * @return \Illuminate\Http\Response
		 */

		public function job_dispute_detail(Request $request){
			$data['subheader']      = 'employer.includes.top-menu';
			$data['header']         = 'innerheader';
			$data['footer']         = 'innerfooter';
			$data['view']           = 'employer.jobdetail.disputes';
			$data['user']           = \Models\Employers::get_user(\Auth::user());
			
			$project_id 			= ___decrypt($request->job_id);
			
			$data['project']        = \Models\Projects::defaultKeys()
			->withCount([
				'reviews' => function($q){
                    $q->where('sender_id',auth()->user()->id_user);
                }
			])
			->with([
				'proposal' => function($q){
					$q->defaultKeys()->where('talent_proposals.status','accepted')->with([
						'talent' => function($q){
							$q->defaultKeys()->country()->review()->with([
								'interests'
							]);
						}
					]);
				},
				'dispute' => function($q){
					$q->defaultKeys()->with([
						'sender' => function($q){
							$q->defaultKeys();
						},
						'concern' => function($q){
							$q->defaultKeys();
						},
						'comments' => function($q){
							$q->defaultKeys()->with([
								'files'  => function($q){
                                    $q->where('type','disputes');
                                },
								'sender' => function($q){
									$q->defaultKeys();
								},
							]);
						}, 
					]);
				}
			])
			->where('id_project',$project_id)
			->get()
			->first();

			if(empty($data['project']->dispute)){
				$data['dispute_reason'] = \Models\DisputeConcern::where('status','active')->get();
			}else{
				$raise_dispute_index 			= array_search($data['project']->dispute->type, \Models\Listings::raise_dispute_type_column());
				$data['project']->dispute->step = (string) $raise_dispute_index+1;
				
				if($data['project']->dispute->last_commented_by == auth()->user()->id_user || $data['project']->dispute->type == 'receiver-final-comment' || $data['project']->dispute->type == 'closed'){
					$data['project']->dispute->can_reply 	= DEFAULT_NO_VALUE;
                	$data['project']->dispute->time_left   	= "";
				}else{
					$data['project']->dispute->can_reply 	= DEFAULT_YES_VALUE;
					$data['project']->dispute->time_left   	= time_difference(date("Y-m-d H:i:s"),date("Y-m-d H:i:s",strtotime($data['project']->dispute->last_updated." +".constant("RAISE_DISPUTE_STEP_".($raise_dispute_index+1)."_HOURS_LIMIT")." hour")));
				}				

                if($data['project']->dispute->time_left === '00:00:00'){
                    $data['project']->dispute->can_reply    = DEFAULT_NO_VALUE;
                }
			}
			
			$data['title'] 			= $data['project']->title;
			
			return view('employer.jobdetail.index')->with($data);
		}

		public function invitation_list(Request $request, Builder $htmlBuilder){
			$data['title']          = 'Invitations';
			$data['subheader']      = 'employer.includes.top-menu';
			$data['header']         = 'innerheader';
			$data['footer']         = 'innerfooter';
			$data['view']           = 'employer.invite.list';
			$data['user']           = \Models\Employers::get_user(\Auth::user());

			if ($request->ajax()) {
				$invite_list = \Models\InviteTalent::select(['id_invite','talent_id','status'])->with([
					'talentDetail' => function($q){
						$q->select('id_user','experience')
						->name()
						->review()
						->companyLogo()
						->country()
						->with('interests')
						->groupBy(['id_user']);
					}
				])->whereNotIn('status',['pending'])->where('employer_id',\Auth::user()->id_user)->get();

				return \Datatables::of($invite_list)
				->editColumn('invite',function($item){
					$item = (json_decode(json_encode($item),true));
					$html = view('employer.invite.template')->with($item)->render();
					return $html;
				})
				->make(true);
			}

			$data['html'] = $htmlBuilder
			->parameters(["dom" => "<'row' <'col-md-6 table-heading'> <'col-md-3'> <'col-md-3 filter-option'>> rt <'row'<'col-md-6'><'col-md-6'p> >"])
			->addColumn(['data' => 'invite', 'name' => 'invite', 'title' => '&nbsp;', 'width' => '0', 'searchable' => false, 'orderable' => false]);            

			return view('employer.invite.index')->with($data);
		}

		public function invitation_status(Request $request){
			$invite_id 	= ___decrypt($request->invite_id);
			$status 	= $request->status;

			if($status == 'accept'){
				$isUpdated = \Models\InviteTalent::where(['id_invite' => $invite_id, 'employer_id' => \Auth::user()->id_user])->update(['status' => 'accepted','updated' => date('Y-m-d H:i:s')]);
				$this->message = trans('website.W0698');	
			}else if($status == 'decline' || $status == 'disconnect'){
				$isUpdated = \Models\InviteTalent::where(['id_invite' => $invite_id, 'employer_id' => \Auth::user()->id_user])->delete();
				$this->message = trans('website.W0705');	
			}

			if(!empty($isUpdated)){
				$this->status = true;	
			}else{
				$this->status = false;
				$this->message = trans('website.W0699');
			}

			return response()->json([
				'status'    => $this->status,
				'message'   => $this->message
			]); 
		}

		/**
		 * [This method is used for document Curriculum Vitae ]
		 * @param  Request
		 * @return Json Response
		 */
		
		public function raise_dispute_document(Request $request){
			$validator = \Validator::make($request->all(), [
				"file"            => validation('document'),
			],[
				'file.validate_file_type'  => trans('general.M0119'),
			]);

			if($validator->passes()){
				$folder = 'uploads/disputes/';
				$uploaded_file = upload_file($request,'file',$folder);
				
				$data = [
					'user_id' => \Auth::user()->id_user,
					'record_id' => NULL,
					'reference' => 'users',
					'filename' => $uploaded_file['filename'],
					'extension' => $uploaded_file['extension'],
					'folder' => $folder,
					'type' => 'disputes',
					'size' => $uploaded_file['size'],
					'is_default' => DEFAULT_NO_VALUE,
					'created' => date('Y-m-d H:i:s'),
					'updated' => date('Y-m-d H:i:s'),
				];

				$isInserted = \Models\Employers::create_file($data,true,true);
				
				if(!empty($isInserted)){
					if(!empty($isInserted['folder'])){
						$isInserted['file_url'] = url(sprintf("%s/%s",$isInserted['folder'],$isInserted['filename']));
					}
					
					$url_delete = sprintf(
						url('ajax/%s?id_file=%s'),
						DELETE_DOCUMENT,
						$isInserted['id_file']
					);

					$this->jsondata = \View::make('employer.jobdetail.includes.attachment')->with(['file' => $isInserted])->render();
					
					$this->status = true;
					$this->message  = sprintf(ALERT_SUCCESS,trans("general.M0589"));
				}
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

		public function getConnectedTalentList(Request $request)
		{
			$talent_id = ___decrypt($request->talent_id);
			
			if(empty($talent_id)){
				return redirect(sprintf('%s/find-talents',EMPLOYER_ROLE_TYPE));
			}

			$data['subheader']              = 'employer.includes.top-menu';
			$data['header']                 = 'innerheader';
			$data['footer']                 = 'innerfooter';
			$data['view']                   = 'employer.talent.connected-talent';

			$data['submenu']                = 'connected-talent';
			$data['title']                	= 'Connected Talent';
			$data['talent_id']              = ___encrypt($talent_id);
		
			$data['talent']                 = \Models\Talents::get_user((object)['id_user' => $talent_id],true);
			
			$data['ownerDetail'] = $ownerDetail	= \Models\companyConnectedTalent::where('id_user',$talent_id)->where('user_type','owner')->first();

			if(count($ownerDetail)>0){
				$data['talent']['connectedTalent']	= \Models\companyConnectedTalent::where('id_talent_company',$ownerDetail->id_talent_company)->where('user_type','user')->count();
				$firm_juri	=	\DB::table('firm_jurisdiction as fj')->join('countries as c','c.id_country','fj.country_id')->select(\DB::Raw("IF(({$this->language} != ''),GROUP_CONCAT(`{$this->language}`), GROUP_CONCAT(`en`)) as country_name"))->where('company_id',$ownerDetail->id_talent_company)->first();
				$data['talent']['countries'] = @$firm_juri->country_name;

				if($data['talent']['talentCompany']->company_logo ==  null){
					$data['talent']['talentCompany']->company_logo = asset(sprintf('images/%s',DEFAULT_AVATAR_IMAGE));
				}
				
			}else{
				
				$data['talent']['connectedTalent']	= '0';
			}

			if($data['talent']['talentCompany']->company_logo == null){
				$data['talent']['talentCompany']->company_logo  = asset(sprintf('images/%s',DEFAULT_AVATAR_IMAGE));
			}
			
			$data['user']                   = \Models\Employers::get_user(\Auth::user(),true);
			$data['country_phone_codes']    = \Cache::get('country_phone_codes');
			$data['countries']              = \Cache::get('countries');
			$data['states']                 = \Cache::get('states');
			$data['degree_name']            = \Cache::get('degree_name');
			$data['top_talent_user']        = \Models\Talents::top_talent_user($talent_id);
			$data['education_list']         = \Models\Talents::educations($talent_id);
			$data['work_experience_list']   = \Models\Talents::work_experiences($talent_id);
			$data['get_files']              = \Models\Talents::get_file(sprintf("user_id = %s AND type = 'certificates' ", $talent_id));

			/* RECORDING ACTIVITY LOG */
			event(new \App\Events\Activity([
				'user_id'           => \Auth::user()->id_user,
				'user_type'         => 'employer',
				'action'            => 'talent-interview',
				'reference_type'    => 'talent',
				'reference_id'      => $talent_id
			]));

			$data['isOwner'] 		= \Models\companyConnectedTalent::with(['user'])->where('id_user',$talent_id)->where('user_type','owner')->get()->first();
			$data['isOwner'] 		= json_decode(json_encode($data['isOwner']));

			if(!empty($data['isOwner'])){
				$data['connected_user'] = \Models\companyConnectedTalent::with(['user','getProfile'])->where('id_talent_company',$data['isOwner']->id_talent_company)->where('user_type','user')->get();

				$data['connected_user'] = json_decode(json_encode($data['connected_user']));
				foreach ($data['connected_user'] as $key => $value) {
					$value->get_profile = get_file_url(json_decode(json_encode($value->get_profile),true));
					$value->industry = \Models\Talents::industry($value->user->id_user);
				}
			}else{
				$data['connected_user'] = [];
			}
			return view('employer.talent.index')->with($data);

		}
	}
