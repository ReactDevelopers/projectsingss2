<?php 
	namespace App\Http\Controllers\Front;

	use App\Http\Requests;
	use Illuminate\Support\Facades\DB;
	use App\Models\Proposals;
	use App\Http\Controllers\Controller;
	
	use Illuminate\Support\Facades\Cookie;
	use Illuminate\Validation\Rule;
	use Illuminate\Http\Request;
	use Yajra\Datatables\Html\Builder;
	use Crypt;

	use Voucherify\VoucherifyClient;
    use Voucherify\ClientException;

	use App\Models\Interview as Interview;
	
	class TalentController extends Controller {

		private $jsondata;
		private $redirect;
		private $message;
		private $status; 
		private $prefix;
		private $language;
		private $head_message;

		public function __construct(){
			$this->jsondata    	= [];
			$this->message     	= false;
			$this->head_message = false;
			$this->redirect    	= false;
			$this->status      	= false;
			$this->prefix      	= \DB::getTablePrefix();
			$this->language    	= \App::getLocale();
			\View::share ( 'footer_settings', \Cache::get('configuration') );
		}

		/**
		 * [This method is used for randering view of Personal Information] 
		 * @param  null
		 * @return \Illuminate\Http\Response
		 */
		
		public function profile_step(\Request $request, $step){
			if(strpos(\Request::route()->getPath(), '/edit/')){
				$data['subheader']          = 'talent.includes.top-menu';
				$data['edit_url'] = 'edit/';
			}else{
				$data['edit_url'] 			= '';
				$data['subheader']          = false;
			}

			$data['title']                  = trans('website.W0651');
			$data['header']                 = 'innerheader';
			$data['footer']                 = 'innerfooter';
			$data['view']                   = "talent.profile.{$step}";
			$data['steps']                  = ___get_steps($step);
			$data['user']                   = \Models\Talents::get_user(\Auth::user(),true);
			$data['companydata']			= \DB::table('company_connected_talent')->leftjoin('talent_company','talent_company.talent_company_id','=','company_connected_talent.id_talent_company')->select('company_name','company_website','company_biography','company_logo')->where('id_user','=',\Auth::user()->id_user)->first();
			if(count($data['companydata']) > 0 && $data['companydata']->company_logo == null){
				$data['companydata']->company_logo = asset(sprintf('images/%s',DEFAULT_AVATAR_IMAGE));
			}
			$ownerDetail	= \Models\companyConnectedTalent::where('id_user',\Auth::User()->id_user)->where('user_type','owner')->first();
			if($ownerDetail!=null){
				$data['firm_jurisdiction']		= \Models\FirmJurisdiction::get_firm_jurisdiction($ownerDetail->id_talent_company);
				
			}
			// $data['company_name'] = $data['company_name']->company_name;
			// dd($data['firm_jurisdiction'],$data['user']['skills']);
			$data['skip_url']               = ___editSkipUrl($step,'talent');/*url(sprintf("%s/find-jobs",TALENT_ROLE_TYPE));*/

			/*Check if user needs to enter identification number*/
			$country_id = !empty(\Auth::user()->country) ? \Auth::user()->country : 0;
			$data['country_id'] = $country_id;
			$data['talent_industry_id'] = \Models\TalentIndustries::get_talent_industry_by_userID(\Auth::user()->id_user);

			$data['show_identification_check'] = \Models\Payout_mgmt::talentCheckIdentificationNo($country_id,$data['talent_industry_id']);
			$data['show_identification_check_all'] = \Models\Payout_mgmt::usertalentCheckIdentificationNo($country_id);

			// dd($data['show_identification_check_all']);
 
			$data['payout_management_list'] = \Models\Payout_mgmt::userCheckIdentificationNumber($country_id);
			$data['payout_mgmt_is_registered'] = \Models\Payout_mgmt::userCheckIsRegistered($country_id,$data['talent_industry_id']);
			// dd($data['payout_management_list'],'>>>>>>>>>>',$data['talent_industry_id']);
			if($step == 'three'){
				$data['get_files']          = $data['user']['certificate_attachments'];
				$data['subindustries_name'] = \Models\Listings::industry_subindustry_list(current($data['user']['industry'])['id_industry']);
			}

			if($step == 'five'){
				$data['education_list']         = \Models\Talents::educations(\Auth::user()->id_user);
				$data['work_experience_list']   = \Models\Talents::work_experiences(\Auth::user()->id_user);
				$data['get_files']              = \Models\Talents::get_file(sprintf("user_id = %s AND type = 'certificates' ", \Auth::user()->id_user));                
			}

			return view('talent.profile.index')->with($data);
		}

		
		/**
		 * [This method is used for randering view of Personal Information] 
		 * @param  null
		 * @return \Illuminate\Http\Response
		 */
		
		public function firm_jurisdiction(Request $request){

			$validator = \Validator::make($request->all(), [
				'jurisdiction'              			=> ['required'],
			],[
				'jurisdiction.required'        			=> trans('general.M0643'),
			]);

			if($validator->passes()){
				$data['ownerDetail'] = $ownerDetail	= \Models\companyConnectedTalent::where('id_user',\Auth::User()->id_user)->where('user_type','owner')->first();
				
				$isUpdated      = \Models\FirmJurisdiction::update_jurisdiction($ownerDetail->id_talent_company,$request->all());

				$this->status = true;
				$this->message  = sprintf(ALERT_SUCCESS,trans("general.M0642"));
			}else{
				$this->jsondata = ___error_sanatizer($validator->errors());
				// $this->jsondata = (object)$this->jsondata;
			}

			return response()->json([
				'data'      => $this->jsondata,
				'status'    => $this->status,
				'message'   => $this->message,
				'redirect'  => $this->redirect,
			]);
		}
		/**
		 * [This method is used for randering view of Employer job post] 
		 * @param  null
		 * @return \Illuminate\Http\Response
		 */
		
		public function profile_step_process(Request $request, $step){
			$request->process = "";
			if(!empty($request->process)){
				$request->process = $request->process.'/';
			}

			switch ($step) {
				case 'one':{
					if(!empty($request->birthyear)  && !empty($request->birthmonth) && !empty($request->birthdate)){
						$request->request->add(['birthday'=>sprintf('%s-%s-%s',$request->birthyear, $request->birthmonth, $request->birthdate)]);
					}

					$user = \Models\Talents::get_user(\Auth::user());
					
					$validator = \Validator::make($request->all(), [
						'first_name'                => validation('first_name'),
						'last_name'                 => validation('last_name'),
						'email'                     => ['required','email',Rule::unique('users')->ignore('trashed','status')->where(function($query) use($request){$query->where('id_user','!=',$request->user()->id_user);})],
						'birthday'                  => array_merge(['min_age:14'],validation('birthday')),
						'gender'                    => validation('gender'),
						'mobile'                    => array_merge([Rule::unique('users')->ignore('trashed','status')->where(function($query) use($request){$query->where('id_user','!=',$request->user()->id_user);})],validation('mobile')),
						'address'                   => validation('address'),
						'country'                   => validation('country'),
						'country_code'              => $request->mobile ? array_merge(['required'], validation('country_code')) : validation('country_code'),
						'state'                     => validation('state'),
						'city'                      => validation('city'),
						'postal_code'               => validation('postal_code'),
						'agree'                     => validation('agree'),
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
						'birthday.string'           => trans('general.M0054'),
						'birthday.regex'            => trans('general.M0054'),
						'birthday.min_age'          => trans('general.M0055'),
						'birthday.min_age'          => trans('general.M0055'),
						'birthday.validate_date'    => trans('general.M0506'),
						'gender.string'             => trans('general.M0056'),
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
						'country_code.string'       => trans('general.M0074'),
						'state.integer'             => trans('general.M0060'),
						'city.integer'              => trans('general.M0254'),
						'postal_code.string'        => trans('general.M0061'),
						'agree.required'            => trans('general.M0253'),
					]);

					$data['companydata']			= \DB::table('company_connected_talent')->leftjoin('talent_company','talent_company.talent_company_id','=','company_connected_talent.id_talent_company')->select('company_name','company_website','company_biography','user_type')->where('id_user','=',\Auth::user()->id_user)->first();

					if($request->company_profile=='individual'){

						$data['isOwner'] 		= \Models\companyConnectedTalent::with(['user'])->where('id_user',\Auth::user()->id_user)->where('user_type','owner')->get()->first();
						$data['isOwner'] 		= json_decode(json_encode($data['isOwner']));

						if(!empty($data['isOwner'])){
							$data['connected_user'] = \Models\companyConnectedTalent::with(['user','getProfile'])->where('id_talent_company',$data['isOwner']->id_talent_company)->get();
						}
						// dd($data['companydata'],$data['isOwner'],$data['connected_user']);
						if($request->company_profile=='individual' && !empty($data['connected_user'])){
							$validator->after(function ($validator) use($data) {
								if($data['connected_user']->count()>0) {
									$validator->errors()->add('company_profile', trans('general.M0674'));
								}
							});
						}
					}else if($request->company_profile=='company'){
						$isConnectedUser = \Models\companyConnectedTalent::with(['user','getProfile'])->where('id_user',\Auth::User()->id_user)->where('user_type','user')->count();

						if($isConnectedUser > 0){
							$validator->after(function ($validator) use($data) {
								$validator->errors()->add('company_profile', trans('general.M0660'));
							});
						}
					}
					if($validator->passes()){

						$update = array_intersect_key(json_decode(json_encode($request->all()),true), array_flip(array('first_name', 'last_name', 'email', 'birthday', 'gender', 'mobile', 'address', 'country', 'country_code', 'state', 'city', 'postal_code','company_profile','company_name','show_profile' ) ) );

						___filter_null($update);
						if($update['mobile'] != \Auth::user()->mobile){
							$update['is_mobile_verified'] = DEFAULT_NO_VALUE;
						}
						$code = '';
						if($request->email != $user['email']){
							$code = bcrypt(__random_string());
							$update['remember_token'] = $code;
							$update['is_email_verified'] = DEFAULT_NO_VALUE;
						}


						/*Save First & Last Name in ucfirst*/
						$update['first_name'] = ucfirst(strtolower($request->first_name));
						$update['last_name'] = ucfirst(strtolower($request->last_name));
						$id = \Auth::user()->id_user;
                        $talentcompanydataId = \DB::table('company_connected_talent')->select('id_talent_company')->where('id_user','=',$id)->first();

						if($update['company_profile']=='individual' ){
							\DB::table('company_connected_talent')->where('id_user',\Auth::User()->id_user)->update(['user_type'=>'user']);
						}

						if($update['company_profile']=='company' && !empty($talentcompanydataId)){
							$talentcompanydata['company_name'] 		= $update['company_name'];
							$talentcompanydata['company_website'] 	= $request->company_website;
							$talentcompanydata['company_biography'] = $request->company_biography;
                            $talentcompanydata['created'] 			= date('Y-m-d H:i:s');
                            $talentcompanydata['updated'] 			= date('Y-m-d H:i:s');
                           	
                           	if($request->file('company_logo')){

	                           	$folder = 'uploads/proposals/';
								$uploaded_file = upload_file($request,'company_logo',$folder);
								$talentcompanydata['company_logo'] = $uploaded_file['file_url'];
                           	}



                            $isCompanyUpdated      = \Models\TalentCompany::updateTalentCompany($talentcompanydata,$talentcompanydataId->id_talent_company);
                            // $isCompanyConnectedTalentUpdated = \DB::table('company_connected_talent')->where('id_user','=',$id)->update(['id_talent_company'=>$isTalentCompanydId,'id_user'=>$id,'user_type'=>'owner','updated'=> date('Y-m-d H:i:s'),'created'=> date('Y-m-d H:i:s')]);
						}
						
						if($update['company_profile']=='company' && empty($talentcompanydataId)){
							$talentcompanydata['company_name'] 		= $request->company_name;
							$talentcompanydata['company_website'] 	= $request->company_website;
							$talentcompanydata['company_biography'] = $request->company_biography;
                            $talentcompanydata['created'] 			= date('Y-m-d H:i:s');
                            $talentcompanydata['updated'] 			= date('Y-m-d H:i:s');
                           	
                           	if($request->file('company_logo')){

	                           	$folder = 'uploads/proposals/';
								$uploaded_file = upload_file($request,'company_logo',$folder);
								$talentcompanydata['company_logo'] = $uploaded_file['file_url'];
                           	}

                            $isTalentCompanydId = \Models\TalentCompany::saveTalentCompany($talentcompanydata);
                            $isCreated 			= \Models\companyConnectedTalent::insert([
                            																'id_talent_company'=>$isTalentCompanydId,
                            																'id_user'=>\Auth::user()->id_user,
                            																'user_type'=>'owner',
                            																'updated'=> date('Y-m-d H:i:s'),
                            																'created'=> date('Y-m-d H:i:s')]
                            															);
						}
						/*Update in name field in users table*/
						$update['name'] = $update['first_name'].' '.$update['last_name'];
						$isUpdated      = \Models\Talents::change(\Auth::user()->id_user,$update);


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
							'user_type'         => 'talent',
							'action'            => 'talent-step-one',
							'reference_type'    => 'users',
							'reference_id'      => \Auth::user()->id_user
						]));                    
						$this->redirect = url(sprintf("%s/profile/%sstep/two",TALENT_ROLE_TYPE,$request->process));
					}else{
						$this->jsondata = json_decode(json_encode(___error_sanatizer($validator->errors())),true);
						
						if(!empty($this->jsondata['country_code'])){
							$this->jsondata['mobile'][0] = $this->jsondata['country_code'][0];
							unset($this->jsondata['country_code']);
						}

						$this->jsondata = (object)$this->jsondata;
					}
					break;
				}
				case 'two':{
					$validator = \Validator::make($request->all(), [
						'industry'              			=> validation('industry'),
						'skills'                            => validation('skills'),
						'expertise'                         => validation('expertise'),
						'experience'                        => validation('experience'),
					],[
						'industry.array'        			=> trans('general.M0511'),
						'skills.array'                      => trans('general.M0142'),
						'expertise.string'                  => trans('general.M0066'),
						'experience.numeric'                => trans('general.M0067'),
						'experience.max'                    => trans('general.M0068'),
						'experience.min'                    => trans('general.M0069'),
						'experience.regex'                  => trans('general.M0067'),
					]);

					if($validator->passes()){
						/*REMOVE AND ADD NEWLY SELECTED SKILLS*/
						\Models\Talents::update_industry(\Auth::user()->id_user,$request->industry);    
						
						/*REMOVE AND ADD NEWLY SELECTED SKILLS*/
						\Models\Talents::update_skill(\Auth::user()->id_user,$request->skills);    
						
						/*Check for Identification Number*/
						$identification_no = !empty($request->identification_no) ? $request->identification_no : '';

						$is_register = !empty($request->is_register) ? $request->is_register : 'N';

						$is_identification_no = ($is_register=='Y') ? $request->identification_no : '' ; 
						/*UPDATING PROFILE*/
						\Models\Talents::change(\Auth::user()->id_user,[
							'expertise' => $request->expertise,
							'experience' => $request->experience,
							'identification_no' => $is_identification_no,
							'is_register' => $is_register,
							'updated' => date('Y-m-d H:i:s')]);

						$this->status   = true;
						$this->message  = sprintf(ALERT_SUCCESS,trans("general.M0110"));
						
						/* RECORDING ACTIVITY LOG */
						event(new \App\Events\Activity([
							'user_id'           => \Auth::user()->id_user,
							'user_type'         => 'talent',
							'action'            => 'talent-step-two',
							'reference_type'    => 'users',
							'reference_id'      => \Auth::user()->id_user
						]));                    
						$this->redirect = url(sprintf("%s/profile/%sstep/three",TALENT_ROLE_TYPE,$request->process));
					}else{
						 $this->jsondata = ___error_sanatizer($validator->errors());
					}
					break;
				}
				case 'three':{
					$validator = \Validator::make($request->all(), [
						'industry_id'                       => ['required'],
						'subindustry'                       => validation('subindustry'),
					],[
						'industry_id.required'              => trans('general.M0136'),
						'subindustry.array'                 => trans('general.M0065'),
					]);

					if($validator->passes()){
						/*REMOVE AND ADD NEWLY SELECTED SKILLS*/
						$isSubindustryInserted = \Models\Talents::update_subindustry(\Auth::user()->id_user,$request->subindustry,$request->industry_id);    
						
						if(!empty($isSubindustryInserted)){
							$this->status   = true;
							$this->message  = sprintf(ALERT_SUCCESS,trans("general.M0110"));
							
							/* RECORDING ACTIVITY LOG */
							event(new \App\Events\Activity([
								'user_id'           => \Auth::user()->id_user,
								'user_type'         => 'talent',
								'action'            => 'talent-step-three',
								'reference_type'    => 'users',
								'reference_id'      => \Auth::user()->id_user
							]));                    
							$this->redirect = url(sprintf("%s/profile/%sstep/four",TALENT_ROLE_TYPE,$request->process));
						}else{
							$this->jsondata = (object)['subindustry' => trans('general.M0583')];
						}
					}else{
						$this->jsondata = ___error_sanatizer($validator->errors());
					}
					break;
				}
				case 'four':{
					$validator = \Validator::make($request->all(), [
						'interests'                         => validation('industry'),
						'workrate'                          => validation('workrate'),
						"workrate.0"                        => validation('workrate.0'),
						"workrate.1"                        => validation('workrate.1'),
						"workrate.2"                        => validation('workrate.2'),
						'workrate_information'              => validation('workrate_information'),
					],[
						'interests.array'                   => trans('general.M0512'),
						'workrate.*.numeric'                => trans('general.M0256'),
						'workrate.*.min'                    => trans('general.M0261'),
						'workrate.*.max'                    => trans('general.M0262'),
						"workrate.0.required_with"          => trans('general.M0070'),
						"workrate.1.required_with"          => trans('general.M0070'),
						"workrate.2.required_with"          => trans('general.M0070'),
						'workrate_information.string'       => trans('general.M0071'),
						'workrate_information.regex'        => trans('general.M0071'),
						'workrate_information.max'          => trans('general.M0072'),
						'workrate_information.min'          => trans('general.M0073'),
					]);
					if($validator->passes()){
						/*REMOVE AND ADD NEWLY SELECTED INTERESTS*/
						\Models\Talents::update_interest($request->user()->id_user,$request->interests,$request->workrate,$request->user()->currency);

						/*UPDATING WORK INFORMATION*/
						\Models\Talents::change(\Auth::user()->id_user,['workrate_information' => $request->workrate_information, 'updated' => date('Y-m-d H:i:s')]);
						
						$this->status   = true;
						$this->message  = sprintf(ALERT_SUCCESS,trans("general.M0110"));
						
						/* RECORDING ACTIVITY LOG */
						event(new \App\Events\Activity([
							'user_id'           => \Auth::user()->id_user,
							'user_type'         => 'talent',
							'action'            => 'talent-step-four',
							'reference_type'    => 'users',
							'reference_id'      => \Auth::user()->id_user
						]));                    
						$this->redirect = url(sprintf("%s/profile/%sstep/five",TALENT_ROLE_TYPE,$request->process));
					}else{
						$this->jsondata = ___error_sanatizer($validator->errors());
					}

					break;
				}
				case 'five':{
					break;
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
		 * [This method is used for document Curriculum Vitae ]
		 * @param  Request
		 * @return Json Response
		 */
		
		public function save_document(Request $request){
			$validator = \Validator::make($request->all(), [
				"file"            => validation('document'),
			],[
				'file.validate_file_type'  => trans('general.M0119'),
			]);
			if($validator->passes()){
				$certificates  = \Models\Talents::get_user(\Auth::user())['certificate_attachments'];
				if( count($certificates) < 20){
					$folder = 'uploads/certificates/';
					$uploaded_file = upload_file($request,'file',$folder);
					
					$data = [
						'user_id' => \Auth::user()->id_user,
						'record_id' => \Auth::user()->id_user,
						'reference' => 'users',
						'filename' => $uploaded_file['filename'],
						'extension' => $uploaded_file['extension'],
						'folder' => $folder,
						'type' => 'certificates',
						'size' => $uploaded_file['size'],
						'is_default' => DEFAULT_NO_VALUE,
						'created' => date('Y-m-d H:i:s'),
						'updated' => date('Y-m-d H:i:s'),
					];

					$isInserted = \Models\Talents::create_file($data,true,true);
					
					/* RECORDING ACTIVITY LOG */
					event(new \App\Events\Activity([
						'user_id'           => \Auth::user()->id_user,
						'user_type'         => 'talent',
						'action'            => 'talent-step-three document',
						'reference_type'    => 'users',
						'reference_id'      => \Auth::user()->id_user
					]));
					
					if(!empty($isInserted)){
						if(!empty($isInserted['folder'])){
							$isInserted['file_url'] = url(sprintf("%s/%s",$isInserted['folder'],$isInserted['filename']));
						}
						
						$url_delete = sprintf(
							url('ajax/%s?id_file=%s'),
							DELETE_DOCUMENT,
							$isInserted['id_file']
						);

						$this->jsondata = sprintf(RESUME_TEMPLATE,
							$isInserted['id_file'],
							url(sprintf('/download/file?file_id=%s',___encrypt($isInserted['id_file']))),
							asset('/'),
							substr($uploaded_file['filename'], 0,3),
							$uploaded_file['filename'],
							$url_delete,
							$isInserted['id_file'],
							asset('/')
						);
						
						$this->status = true;
						$this->message  = sprintf(ALERT_SUCCESS,trans("general.M0110"));
					}
				}else{
					$this->jsondata = (object)['file' => trans('general.M0563')];
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

		/**
		 * [This method is used for work experience ]
		 * @param  Request
		 * @return Json Response
		 */

		public function save_work_experience(Request $request){
			$request->request->add(['startdate' => sprintf("%s-%s",$request->joining_year,$request->joining_month)]);

			if($request->is_currently_working == 'yes'){
				$request->request->remove('relieving_month', 'relieving_year');
			}

			if(!empty($request->relieving_year) && !empty($request->relieving_month)){
				$request->request->add(['enddate' => sprintf("%s-%s",$request->relieving_year,$request->relieving_month)]);
			}

			$validator = \Validator::make($request->all(), [
				"jobtitle"                          => validation('jobtitle'),
				"company_name"                      => validation('company_name'),
				"joining_month"                     => validation('joining_month'),
				"joining_year"                      => validation('joining_year'),
				"is_currently_working"              => validation('is_currently_working'),
				"job_type"                          => validation('job_type'),
				"relieving_month"                   => array_merge(['sometimes'],validation('relieving_month')),
				"relieving_year"                    => array_merge(['sometimes'],validation('relieving_year')),
				"country"                           => array_merge(['required'],validation('country')),
				"state"                             => validation('state'),
			],[
				'jobtitle.required'                 => trans('general.M0090'),
				'jobtitle.string'                   => trans('general.M0091'),
				'jobtitle.regex'                    => trans('general.M0091'),
				'jobtitle.max'                      => trans('general.M0092'),
				'jobtitle.min'                      => trans('general.M0093'),
				'company_name.required'             => trans('general.M0023'),
				'joining_month.required'            => trans('general.M0094'),
				'joining_month.string'              => trans('general.M0095'),
				'joining_month.max'                 => trans('general.M0544'),
				'joining_year.required'             => trans('general.M0096'),
				'joining_year.string'               => trans('general.M0097'),
				'joining_year.max'                  => trans('general.M0543'),
				'is_currently_working.required'     => trans('general.M0098'),
				'is_currently_working.string'       => trans('general.M0099'),
				'job_type.required'                 => trans('general.M0100'),
				'job_type.string'                   => trans('general.M0101'),
				'relieving_month.required'          => trans('general.M0102'),
				'relieving_month.string'            => trans('general.M0103'),
				'relieving_month.max'               => trans('general.M0545'),
				'relieving_year.required'           => trans('general.M0104'),
				'relieving_year.string'             => trans('general.M0105'),
                'relieving_year.min'                => trans('general.M0541'),
                'relieving_year.max'                => trans('general.M0542'),
				'country.required'                  => trans('general.M0106'),
				'country.integer'                   => trans('general.M0059'),
				'state.integer'                     => trans('general.M0060'),
				'state.required'                    => trans('general.M0107'),
			]);
			
			$validator->sometimes(['relieving_month','relieving_year'], 'required', function($input){
				return ($input->is_currently_working == DEFAULT_YES_VALUE);
			});
			if($validator->passes()){
				if(!empty($request->startdate) && !empty($request->enddate) && (strtotime($request->startdate) > strtotime($request->enddate))){
                    $this->jsondata = (object)['relieving_month' => trans('general.M0190')];
                }else if(abs($request->joining_month) > 12){
                    $this->jsondata = (object)['relieving_month' => trans('general.M0544')];
                }else if(!empty($request->is_currently_working) && abs($request->relieving_month) > 12){
                    $this->jsondata = (object)['relieving_month' => trans('general.M0545')];
                }else if(!empty($request->is_currently_working) && abs($request->relieving_year) > date('Y')){
                	$this->jsondata = (object)['relieving_month' => trans('general.M0542')];
            	}else if(!empty($request->is_currently_working) && $request->is_currently_working == DEFAULT_NO_VALUE && (strtotime($request->startdate) > strtotime(date('Y-m')) || strtotime($request->enddate) > strtotime(date('Y-m')))){
            		$this->jsondata = (object)['relieving_month' => trans('general.M0557')];
            	}else{
					$update = array_intersect_key(
						json_decode(json_encode($request->all()),true), 
						array_flip(
							array(
								"jobtitle",
								"company_name",
								"joining_month",
								"joining_year",
								"is_currently_working",
								"job_type",
								"relieving_month",
								"relieving_year",
								"country",
								"state",
							)
						)
					);
					___filter_null($update);

					if($request->is_currently_working == DEFAULT_YES_VALUE){
						unset($update['relieving_month']);
						unset($update['relieving_year']);
					}

					if($request->id_experience){
						\Models\Talents::update_experience($request->id_experience,$update);
						$inserted_experience_id = $request->id_experience;
						/* RECORDING ACTIVITY LOG */
						event(new \App\Events\Activity([
							'user_id'           => \Auth::user()->id_user,
							'user_type'         => 'talent',
							'action'            => 'talent-update-step-three-work-experience',
							'reference_type'    => 'users',
							'reference_id'      => \Auth::user()->id_user
						]));                        
					}else{
						$inserted_experience_id = \Models\Talents::add_experience($request->user()->id_user,$update);
						/* RECORDING ACTIVITY LOG */
						event(new \App\Events\Activity([
							'user_id'           => \Auth::user()->id_user,
							'user_type'         => 'talent',
							'action'            => 'talent-step-three-work-experience',
							'reference_type'    => 'users',
							'reference_id'      => \Auth::user()->id_user
						]));
					}

					$experience = \Models\Talents::work_experiences($request->user()->id_user,$inserted_experience_id);
					
					$this->status   = true;
					$this->message  = sprintf(ALERT_SUCCESS,trans("general.M0110"));
					$this->jsondata = \View::make('talent.profile.includes.workexperience')->with(['work_experience_list' => $experience])->render();
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

		/**
		 * [This method is used for education Curriculum Vitae ]
		 * @param  Request
		 * @return Json Response
		 */

		public function save_education(Request $request){
			$validator = \Validator::make($request->all(), [
				"college"                           => validation('college'),
				"degree"                            => validation('degree'),
				"passing_year"                      => validation('passing_year'),
				"area_of_study"                     => validation('area_of_study'),
				// "degree_status"                     => validation('degree_status'),
				// "degree_country"                    => array_merge(['required'],validation('country')),
			],[
				'college.required'                  => trans('general.M0078'),
				'college.string'                    => trans('general.M0079'),
				'college.regex'                     => trans('general.M0079'),
				'college.max'                       => trans('general.M0080'),
				'degree.required'                   => trans('general.M0081'),
				'degree.integer'                    => trans('general.M0082'),
				'passing_year.required'             => trans('general.M0083'),
				'passing_year.integer'              => trans('general.M0084'),
				'area_of_study.required'            => trans('general.M0085'),
				'area_of_study.integer'             => trans('general.M0086'),
				// 'degree_status.required'            => trans('general.M0087'),
				// 'degree_status.integer'             => trans('general.M0088'),
				// 'degree_country.required'           => trans('general.M0089'),
				// 'degree_country.integer'            => trans('general.M0059'),
			]);

			if($validator->passes()){
				$update = array_intersect_key(
					json_decode(json_encode($request->all()),true), 
					array_flip(
						array(
							'college',
							'degree',
							'passing_year',
							'area_of_study',
							// 'degree_status',
							// 'degree_country',
						)
					)
				);
				___filter_null($update);
				
				if($request->id_education){
					\Models\Talents::update_education($request->id_education,$update);
					$inserted_education_id = $request->id_education;
					/* RECORDING ACTIVITY LOG */
					event(new \App\Events\Activity([
						'user_id'           => \Auth::user()->id_user,
						'user_type'         => 'talent',
						'action'            => 'talent-update-step-three-education',
						'reference_type'    => 'users',
						'reference_id'      => \Auth::user()->id_user
					]));
				}else{
					$inserted_education_id = \Models\Talents::add_education($request->user()->id_user,$update);
					/* RECORDING ACTIVITY LOG */
					event(new \App\Events\Activity([
						'user_id'           => \Auth::user()->id_user,
						'user_type'         => 'talent',
						'action'            => 'talent-step-three-education',
						'reference_type'    => 'users',
						'reference_id'      => \Auth::user()->id_user
					]));                    
				}

				$educations = \Models\Talents::educations($request->user()->id_user,$inserted_education_id);

				$this->status   = true;
				$this->message  = sprintf(ALERT_SUCCESS,trans("general.M0110"));
				$this->jsondata = \View::make('talent.profile.includes.education')->with(['education_list' => $educations])->render();
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
		 * [This method is used for randering view of Personal Information] 
		 * @param  null
		 * @return \Illuminate\Http\Response
		 */
		
		public function viewprofile(\Request $request){
			$data['title']                  = trans('website.W0606');
			$data['subheader']              = 'talent.includes.top-menu';
			$data['header']                 = 'innerheader';
			$data['footer']                 = 'innerfooter';
			$data['view']                   = "talent.viewprofile.view";			
			$data['user']                   = \Models\Talents::get_user(\Auth::user());

			$talent_country_id 	 	   = !empty(\Auth::user()->country) ? \Auth::user()->country : 0;
			$talent_industry_id  	   = \Models\TalentIndustries::get_talent_industry_by_userID(\Auth::user()->id_user);

			$data['identification_no_check'] = \Models\Payout_mgmt::talentCheckIdentificationNo($talent_country_id,$talent_industry_id);
			$data['country']= $talent_country_id;
			$data['payout_mgmt_is_registered'] = \Models\Payout_mgmt::userCheckIsRegistered($talent_country_id,$talent_industry_id);

			return view('talent.viewprofile.index')->with($data);
		}
		
		/**
		 * [This method is used for randering view of Availability Setting]
		 * @param  Request
		 * @return \Illuminate\Http\Response
		 */
		
		public function set_availability(Request $request, Builder $htmlBuilder){
			$data['title']                  = trans('website.W0173');
			$data['subheader']              = 'talent.includes.top-menu';
			$data['header']                 = 'innerheader';
			$data['footer']                 = 'innerfooter';
			$data['view']                   = 'talent.availability.setup';

			$data['user']                   = \Models\Talents::get_user(\Auth::user());
			$data['submenu']                = 'availabilities';
			$data['selected_date']          = date('Y-m-d H:i:s');
			$data['skip_url']               = url('talent/availability');
			
			return view('talent.availability.index')->with($data);
		}

		/**
		 * [This method is used for setting user's availability ]
		 * @param  Request
		 * @return Json Response
		 */

		public function save_availability(Request $request){
			if(!empty($request->availability_date)){
				$request->request->add(['availability_date' => ___convert_date($request->availability_date)]);
			}else if(!empty($request->year) && !empty($request->month) && !empty($request->day)){
				$request->request->add(['availability_date' => sprintf('%s-%s-%s',$request->year,$request->month,$request->day)]);
			}

			if(!empty($request->from_time_hour) && !empty($request->from_time_minute) && !empty($request->from_time_meridian)){
				$request->request->add(['from_time' => date('H:i:s',strtotime(sprintf('%s:%s %s',$request->from_time_hour,$request->from_time_minute,$request->from_time_meridian)))]);
			}

			if(!empty($request->to_time_hour) && !empty($request->to_time_minute) && !empty($request->to_time_meridian)){
				$request->request->add(['to_time' => date('H:i:s',strtotime(sprintf('%s:%s %s',$request->to_time_hour,$request->to_time_minute,$request->to_time_meridian)))]);
			}

			if(!empty($request->deadline)){
				$request->request->add(['deadline' => date('Y-m-d',strtotime(str_replace('/', '-', $request->deadline)))]);
			}

			$validate = \Validator::make($request->all(), [
				"availability_type"             => array_merge(['required']),
				"availability_date"             => array_merge(['required'],validation('birthday')),
				"from_time"                     => array_merge(['required'],validation('time')),
				"to_time"                       => array_merge(['required','different:from_time','one_hour_difference:from_time','invalid_time_range:from_time'],validation('time')),
				"repeat"                        => validation('repeat'),
				"deadline"                      => validation('birthday'),
			],[
				'availability_type.required'    => trans('general.M0473'),
				'availability_date.required'    => trans('general.M0155'),
				'availability_date.string'      => trans('general.M0156'),
				'availability_date.regex'       => trans('general.M0156'),
				"from_time.required"            => trans('general.M0159'),
				"from_time.string"              => trans('general.M0160'),
				"from_time.regex"               => trans('general.M0160'),
				"to_time.required"              => trans('general.M0161'),
				"to_time.string"                => trans('general.M0162'),
				"to_time.regex"                 => trans('general.M0162'),
				"to_time.different"             => trans('general.M0222'),
				"to_time.one_hour_difference"   => trans('general.M0223'),
				"to_time.invalid_time_range"    => trans('general.M0224'),
				"repeat.string"                 => trans('general.M0163'),
				'deadline.required'             => trans('general.M0157'),
				'deadline.string'               => trans('general.M0158'),
				'deadline.regex'                => trans('general.M0158'),
			]);

			$validate->after(function ($validate) use($request) {
				if($request->repeat == 'weekly') {
					if(empty($request->availability_day)){
						$validate->errors()->add('availability_day[]', trans('general.M0170'));
					}else if(!is_array($request->availability_day)){
						$validate->errors()->add('availability_day[]', trans('general.M0171'));
					}
				}

				if(!empty($request->availability_date) && !empty($request->from_time)){
					$current_date = ___d(date('Y-m-d H:i:s'));

					if(strtotime("$request->availability_date $request->from_time") < strtotime($current_date)){
						$validate->errors()->add('from_time', trans('general.M0436'));
					}
				}
			});
			
			if($validate->passes()){
				if(empty($request->deadline)){
					$request->request->add(['deadline' => $request->availability_date]);
				}
					
				$valid_employment_types = employment_types('talent_availability','keys');
				
				if(!in_array($request->repeat, $valid_employment_types)){
					$this->message = sprintf(ALERT_DANGER,trans('general.M0169'));
					$this->jsondata = (object)[];
				}else{
					if($request->deadline < $request->availability_date){
						$this->jsondata = (object)[
							'deadline' => trans('general.M0173'),
						];  
					}else{
						$availability_id = NULL;
						
						if(!empty($request->id_availability)){
							$availability_id = $request->id_availability;
						}

						if($request->availability_type == 'busy'){
							$isAvailable = true;
						}else{
							$isAvailable = \Models\Talents::check_availability(\Auth::user()->id_user,$request->availability_date,$request->from_time,$request->to_time,$request->deadline,$request->availability_day,$request->repeat,$availability_id);
						}
						
						if($isAvailable === true){
							$table_talent_availability = DB::table('talent_availability');
							$max_repeat_group = (int)$table_talent_availability->max('repeat_group')+1;
							$data = [];
							if($request->repeat == 'daily' || $request->repeat == 'monthly'){
								$begin = new \DateTime( $request->availability_date );

								$endDate = date('Y-m-d', strtotime("+1 day", strtotime($request->deadline)));

								$end = new \DateTime( $endDate );

								if($request->repeat == 'daily'){
									$repeat_type = '1 day';
								}elseif($request->repeat == 'monthly'){
									$repeat_type = '1 month';
								}
								$interval = \DateInterval::createFromDateString($repeat_type);
								$period = new \DatePeriod($begin, $interval, $end);

								foreach ( $period as $dt ){
									$data[] = [
										'user_id' => \Auth::user()->id_user,
										'availability_type' => $request->availability_type,
										'availability_date' => $dt->format( "Y-m-d" ),
										'from_time' => $request->from_time,
										'to_time' => $request->to_time,
										'repeat' => $request->repeat,
										'deadline' => $request->deadline,
										'repeat_group' => $availability_id ? $availability_id : $max_repeat_group,
										'created' => date('Y-m-d H:i:s'),
										'updated' => date('Y-m-d H:i:s'),
									];
								}
							}elseif($request->repeat == 'weekly'){
								$date = ___days_between($request->availability_date, $request->deadline, $request->availability_day);

								foreach ($date as $d) {
									$data[] = [
										'user_id' => \Auth::user()->id_user,
										'availability_type' => $request->availability_type,
										'availability_date' => $d,
										'from_time' => $request->from_time,
										'to_time' => $request->to_time,
										'repeat' => $request->repeat,
										'deadline' => $request->deadline,
										'repeat_group' => $availability_id ? $availability_id : $max_repeat_group,
										'availability_day' => date('l', strtotime($d)),
										'created' => date('Y-m-d H:i:s'),
										'updated' => date('Y-m-d H:i:s'),
									];
								}
							}

							if(!empty($data)){
								$isInserted = \Models\Talents::setTalentAvailability(\Auth::user()->id_user, $max_repeat_group, $data, $availability_id, $request->availability_date, $request->deadline, $request->availability_type);
							}

							/* RECORDING ACTIVITY LOG */
							event(new \App\Events\Activity([
								'user_id'           => \Auth::user()->id_user,
								'user_type'         => 'talent',
								'action'            => 'talent-set-availability',
								'reference_type'    => 'users',
								'reference_id'      => \Auth::user()->id_user
							]));

							if(!empty($isInserted)){
								$this->status = true;
								$this->message = trans('general.M0000');
								$this->jsondata = ___availability_list($isInserted);
							}

						}else{
							$this->jsondata = (object)[];
							$this->message = sprintf(ALERT_DANGER,trans('general.M0172'));
						}
					}
				}
			}else{
				$this->jsondata = (object)___error_sanatizer($validate->errors());
			}

			return response()->json([
				'data'      => $this->jsondata,
				'status'    => $this->status,
				'message'   => $this->message,
				'redirect'  => $this->redirect,
			]);  
		}

		/**
		 * [This method is used for editing user's availability setting]
		 * @param  Request
		 * @return Json Response
		 */
		
		public function get_availability(Request $request){
			$availability = \Models\Talents::get_availability(\Auth::user()->id_user,$request->id_availability);
			
			if(!empty($availability)){
				$this->jsondata = [
					'id_availability'       => $availability[0]['repeat_group'],
					'year'                  => date('Y',strtotime($availability[0]['availability_date'])),
					'month'                 => date('m',strtotime($availability[0]['availability_date'])),
					'month_txt'             => date('M',strtotime($availability[0]['availability_date'])),
					'day'                   => date('d',strtotime($availability[0]['availability_date'])),
					'from_time_hour'        => date('h',strtotime($availability[0]['from_time'])),
					'from_time_minute'      => date('i',strtotime($availability[0]['from_time'])),
					'from_time_meridian'    => date('A',strtotime($availability[0]['from_time'])),
					'to_time_hour'          => date('h',strtotime($availability[0]['to_time'])),
					'to_time_minute'        => date('i',strtotime($availability[0]['to_time'])),
					'to_time_meridian'      => date('A',strtotime($availability[0]['to_time'])),
					'repeat'                => $availability[0]['repeat'],
					'deadline'              => ___convert_date($availability[0]['deadline'],"JS","d/m/Y"),
					'selected_date'         => ___d($availability[0]['availability_date']),
					'availability_day'      => $availability[0]['availability_day'],
					'availability_type'      => $availability[0]['availability_type'],
				];
			}

			return response()->json([
				'status'    => $this->status,
				'data'      => $this->jsondata,
				'message'   => $this->message
			]);
		}

		/**
		 * [This method is used for randering view of user's availability] 
		 * @param  null
		 * @return \Illuminate\Http\Response
		 */
		
		public function talent_availability(){
			$data['title']                  = trans('website.W0173');
			$data['subheader']              = 'talent.includes.top-menu';
			$data['header']                 = 'innerheader';
			$data['footer']                 = 'innerfooter';
			$data['view']                   = 'talent.availability.view';
			
			$data['user']                   = \Models\Talents::get_user(\Auth::user());
			$data['button']                 = '<a href="'.url('talent/availability/setup').'"><img src="'.asset('images/add.png').'"></a>';
			$data['skip_url']               = url('talent/availability');
			$data['selected_date']          = date('Y-m-d H:i:s');

			return view('talent.availability.index')->with($data);
		}

		/**
		 * [This method is used for getting user's availability] 
		 * @param  Request
		 * @return Json Response
		 */
		
		public function get_talent_availability(Request $request){
			$this->status           = true;
			$availability_calendar  = [];
			$date = $request->date;

			$talent_id         = \Auth::user()->id_user;
			$this->jsondata    = \Models\Talents::get_calendar_availability($talent_id, $date);

			return response()->json([
				'data'      => $this->jsondata,
				'status'    => $this->status,
				'message'   => $this->message,
			]);
		}

		/**
		 * [This method is used for View Review]
		 * @param  Request
		 * @return \Illuminate\Http\Response
		 */
		
		public function view_reviews(Request $request, Builder $htmlBuilder){
			$data['title']                  = trans('website.W0465');
			$data['subheader']              = 'talent.includes.top-menu';
			$data['header']                 = 'innerheader';
			$data['footer']                 = 'innerfooter';
			$data['view']                   = 'talent.review.profile';

			$data['user']                   = \Models\Talents::get_user(\Auth::user());
			$data['submenu']                = 'reviews';

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
				])->where('receiver_id',auth()->user()->id_user)->orderBy('id_review','DESC')->get();

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

			return view('talent.viewprofile.index')->with($data);
		}

		/**
		 * [This method is used for randering view of notification listing] 
		 * @param  null
		 * @return \Illuminate\Http\Response
		 */
		
		public function view_notifications(Request $request, Builder $htmlBuilder){
			$data['title']                  = trans('website.W0466');
			$data['subheader']              = 'talent.includes.top-menu';
			$data['header']                 = 'innerheader';
			$data['footer']                 = 'innerfooter';
			$data['view']                   = 'talent.notification.talent-notifications';

			$data['user']                   = \Models\Talents::get_user(\Auth::user());
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
                            $payload = json_decode($item['notification_response_json'],true);
							if($item['notification'] === 'JOB_UPDATED_BY_EMPLOYER'){
								$html .= '<a href="javascript:void(0);" data-request="mark-read" data-url="'.url('talent/notifications/mark/read?notification_id='.$item['id_notification']).'" class="submenu-block clearfix '.$item['notification_status'].'" data-confirm="true" data-ask="'.sprintf(trans('notification.'.$item->notification),$payload['project_title']).'">';
                            }else{
                                $html .= '<a href="javascript:void(0);" data-request="mark-read" data-url="'.url('talent/notifications/mark/read?notification_id='.$item['id_notification']).'" class="submenu-block clearfix '.$item['notification_status'].'">';
                            }
							$html .= '<span class="submenublock-user"><img src="'.$item->sender_picture.'" /></span>';
							$html .= '<span class="submenublock-info">';
								$html .= '<h4>'.$item->sender_name.'<span>'.___ago($item->created).'</span></h4>';
								if($item->notification === 'JOB_UPDATED_BY_EMPLOYER'){
			                        $html .= '<p>'.sprintf(trans('notification.'.$item->notification),$payload['project_title']).'</p>';
			                    }else if($item->notification === 'JOB_RAISE_DISPUTE_RECEIVED'){
			                        $html .= '<p>'.sprintf(trans('notification.'.$item->notification),sprintf("#%'.0".JOBID_PREFIX."d",$payload['project_id'])).'</p>';
			                    }else{
			                        $html .= '<p>'.trans('notification.'.$item->notification).'</p>';
			                    }
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

			return view('talent.viewprofile.index')->with($data);
		}

		/**
		 * [This method is used for view of change password]
		 * @param  Request
		 * @return \Illuminate\Http\Response
		 */
		
		public function change_password(Request $request){
			$data['title']       = trans('website.W0480');
			$data['subheader']   = 'talent.includes.top-menu';
			$data['header']      = 'innerheader';
			$data['footer']      = 'innerfooter';
			$data['view']        = 'talent.settings.changepassword';

			$data['user']        = \Models\Talents::get_user(\Auth::user());
			
			return view('talent.settings.index')->with($data);
		}  

		/**
		 * [This method is used for handling of change password]
		 * @param  Request
		 * @return Json Response
		 */
		
		public function __change_password(Request $request){
			$old_password = validation('old_password');
			$new_password = validation('old_password');
			unset($old_password[0]);
			unset($new_password[2]);

			$validator = \Validator::make($request->all(), [
				"old_password"              => array_merge(['sometimes'],$old_password),
				"new_password"              => array_merge(['sometimes'],$new_password),
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

			$validator->sometimes(['old_password'], 'required', function($request){
				return (auth()->user()->social_account !== DEFAULT_YES_VALUE);  
			});

			$validator->sometimes(['new_password'], 'different:old_password', function($request){
				return (auth()->user()->social_account !== DEFAULT_YES_VALUE);
			});

			if($validator->passes()){
				if(empty(auth()->user()->email)){
					$this->jsondata = (object)['new_password' => trans('general.M0568')];
				}else{
					$isUpdated      = \Models\Talents::change(\Auth::user()->id_user,[
						'social_account'  => 'changed',
						'password'  	=> bcrypt($request->new_password),
						'api_token'  	=> bcrypt(__random_string()),
						'updated'   	=> date('Y-m-d H:i:s')
					]);
					
					$this->status   = true;
					$this->message  = sprintf(ALERT_SUCCESS,trans("general.M0301"));
					$this->redirect = url('/logout');	   
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

		/**
		 * [This method is used for vieww of setting]
		 * @param  Request
		 * @return \Illuminate\Http\Response
		 */
		public function notificationsettings(Request $request){
			$data['title']                  = trans('website.W0306');
			$data['subheader']              = 'talent.includes.top-menu';
			$data['header']                 = 'innerheader';
			$data['footer']                 = 'innerfooter';
			$data['view']                   = 'talent.settings.notification';

			$data['user']                   = \Models\Talents::get_user(\Auth::user());
			$data['settings']               = \Models\Settings::fetch(\Auth::user()->id_user,\Auth::user()->type);
			$data['industries_name']        = \Cache::get('industries_name');
			$data['subindustries_name']     = \Cache::get('subindustries_name');
			return view('talent.settings.index')->with($data);
		}

		/**
		 * [This method is used for handling of setting]
		 * @param  Request
		 * @return Json Response
		 */
		
		public function __notificationsettings(Request $request){
			$setting 	= \Models\Settings::fetch(auth()->user()->id_user,auth()->user()->type);
            $isUpdated  = \Models\Settings::add(auth()->user()->id_user,$request,$setting);
            
			/* RECORDING ACTIVITY LOG */
			event(new \App\Events\Activity([
				'user_id'           => \Auth::user()->id_user,
				'user_type'         => 'talent',
				'action'            => 'talent-save-settings',
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
		 * [This method is used for randering view social settings] 
		 * @param  null
		 * @return \Illuminate\Http\Response
		 */
		
		public function socialsettings(){
			$data['title']                  = trans('website.W0459');
			$data['subheader']              = 'talent.includes.top-menu';
			$data['header']                 = 'innerheader';
			$data['footer']                 = 'innerfooter';
			
			$data['submenu']                = 'profile';
			$data['view']                   = 'talent.settings.social';
			$data['user']                   = \Models\Talents::get_user(\Auth::user());
			
			return view('talent.settings.index')->with($data);
		}

		/**
		 * [This method is used for randering view social settings] 
		 * @param  null
		 * @return \Illuminate\Http\Response
		 */
		
		public function __socialsettings(Request $request){
			if(!empty($request->socialkey)){
				$socialkey = $request->socialkey;

				$isUpdated = \Models\Talents::change(\Auth::user()->id_user, [$socialkey => NULL, 'updated' => date('Y-m-d H:i:s')]);

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
			$data['title']                  = trans('website.W0713');
			$data['subheader']              = 'talent.includes.top-menu';
			$data['header']                 = 'innerheader';
			$data['footer']                 = 'innerfooter';
			
			$data['submenu']                = 'profile';
			$data['view']                   = 'talent.settings.currency';
			$data['user']                   = \Models\Talents::get_user(\Auth::user());
			$data['currency']               =  json_decode(json_encode(\Models\Currency::getCurrencyList()),true);
			
			return view('talent.settings.index')->with($data);   
		}
		
		/**
		 * [This method is used for configuring payment with paypal]
		 * @param  Request
		 * @return \Illuminate\Http\Response
		 */
		

		public function payments(Request $request){
			$data['title']                  = trans('website.W0374');
			$data['subheader']              = 'talent.includes.top-menu';
			$data['header']                 = 'innerheader';
			$data['footer']                 = 'innerfooter';
			
			$data['submenu']                = 'profile';
			$data['view']                   = 'talent.settings.payments';
			$data['user']                   = \Models\Talents::get_user(\Auth::user());
			$data['currency']               =  json_decode(json_encode(\Models\Currency::getCurrencyList()),true);


			$data['verified_paypal_email'] = false;

			$data['isEmailConfirmed'] = ''; 
			$data['returnMessage']    = '';

			return view('talent.settings.index')->with($data);   
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
		 * [This method is used for Transfer Ownership]
		 * @param  Request
		 * @return \Illuminate\Http\Response
		 */
		

		public function transferownership(Request $request){
		
			$data['title']                  = trans('website.W0977');
			$data['subheader']              = 'talent.includes.top-menu';
			$data['header']                 = 'innerheader';
			$data['footer']                 = 'innerfooter';
			
			$data['submenu']                = 'profile';
	
			if($request->type=='list' && !empty(\Session::get('transferownerships'))){
				$data['view']                   = 'talent.settings.transferownershiplist';
			}else{
				$data['view']                   = 'talent.settings.transferownership';
			}
			$data['user']                   = \Models\Talents::get_user(\Auth::user());
			$data['currency']               =  json_decode(json_encode(\Models\Currency::getCurrencyList()),true);


			$data['verified_paypal_email'] = false;

			$data['isEmailConfirmed'] = ''; 
			$data['returnMessage']    = '';

			return view('talent.settings.index')->with($data);   
		}

		/**
		 * [This method is used for Transfer Ownership List]
		 * @param  Request
		 * @return \Illuminate\Http\Response
		 */
		

		public function __transferownership(Request $request){
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

            $keys = [
                'users.id_user',
                'users.type',
                'users.email',
                'users.gender',
                'users.country',
                'users.expertise',
                'users.created',
                \DB::Raw("
                    IF(
                        {$prefix}files.filename IS NOT NULL,
                        CONCAT('{$base_url}',{$prefix}files.folder,'thumbnail/',{$prefix}files.filename),
                        IF({$prefix}users.social_picture IS NOT NULL OR {$prefix}users.social_picture != '', {$prefix}users.social_picture  ,CONCAT('{$base_url}','images/','".DEFAULT_AVATAR_IMAGE."'))
                    ) as picture
                "),
                \DB::raw("IFNULL(CONCAT({$prefix}users.first_name,' ',{$prefix}users.last_name),{$prefix}users.first_name) as name"),
                \DB::Raw("IF(({$prefix}countries.{$language} != ''),{$prefix}countries.`{$language}`, {$prefix}countries.`en`) as country_name"),
                \DB::Raw("IF(({$prefix}city.{$language} != ''),{$prefix}city.`{$language}`, {$prefix}city.`en`) as city_name"),
            ]; 

            $talent_company_id = \DB::table('company_connected_talent')
				            /*->join('talent_company','talent_company.talent_company_id','=','company_connected_talent.id_talent_company')*/
				            ->where('id_user','=',\Auth::user()->id_user)
				            ->where('company_connected_talent.user_type','=','owner')
				            ->select('id_talent_company')
				            ->first();

			if(!empty($talent_company_id)){          
	        	$var = $talent_company_id->id_talent_company;
	        }
	        else{
	        	$var = 0;
	        }
	            $users = \Models\Talents::select($keys)
	            ->leftJoin('countries as countries','countries.id_country','=','users.country')
	            ->leftJoin('talent_interests','talent_interests.user_id','=','users.id_user')
	            ->leftJoin('city','city.id_city','=','users.city')
	            ->leftjoin('company_connected_talent','company_connected_talent.id_user','=','users.id_user')
	            ->leftjoin('files',function($leftjoin){
	                $leftjoin->on('files.user_id','=','users.id_user');
	                $leftjoin->where('files.type','=',\DB::Raw("'profile'"));
	            })->where('users.company_profile','=','individual')
	            ->where('company_connected_talent.user_type','=','user')
	            ->where('id_talent_company','=',$var);
            /*
            ->where('connected_talent.is_connected','=','1')*/;

            if(!empty(trim($search))){
                $search = trim($search);
                $users->havingRaw("(
                    name LIKE '%$search%'
                )");  
            }

            $users->orderBy('users.id_user','ASC')->groupBy('users.id_user');
               $user = [
                'result'                => $users->limit($limit)->offset($offset)->get(),
                'total'                 => $users->get()->count(),
                'total_filtered_result' => $users->limit($limit)->offset($offset)->get()->count(),
            ];

            if(!empty($user['result']->count())){
                foreach($user['result'] as $keys => $userdata){
                    $html .= \View::make('talent.settings.layouts.main')->with(['talent' => $userdata]);
                }
            }else{
                $html .= '<div class="no-records-found">'.trans('website.W0750').'</div>';
            }

            if($user['total_filtered_result'] == DEFAULT_PAGING_LIMIT){
                $load_more = '<button type="button" class="btn btn-default btn-block btn-lg" data-request="filter-paginate" data-url="'.url(sprintf('/talent/__transferownership')).'" data-target="#article_listing" data-showing="#paginate_showing" data-loadmore="#loadmore" data-form="[role=\'find-jobs\']">'.trans('website.W0254').'</button>';
                $can_load_more = true;
            }else{
                $load_more = '<button type="button" class="btn btn-default btn-block btn-lg hide" data-request="filter-paginate" data-url="'.url(sprintf('/talent/__transferownership')).'" data-target="#article_listing" data-showing="#paginate_showing" data-loadmore="#loadmore" data-form="[role=\'find-jobs\']">'.trans('website.W0254').'</button>';
                $can_load_more = false;
            }
            
            echo json_encode(
                array(
                    "filter_title"      => sprintf(trans('general.M0215'),$user['total']),
                    "paging"            => ($request->page == 1)?false:true,
                    "recordsFiltered"   => $user['total_filtered_result'],
                    "recordsTotal"      => $user['total'],
                    "loadMore"          => $load_more, 
                    "data"              => $html,
                    "can_load_more"     => $can_load_more,
                )
            );
		}

		public function accept_transfer(Request $request){
			if($request->ajax()){
				// $data['event_id'] = $request->event_id;
				return view('talent.settings.transfer-ownership-accept');
				// $data['ret_page'] = !empty($request->ret_page) ? $request->ret_page:'';
			}
		}

		public function post_accept_transfer(Request $request){
			$old_password = validation('old_password');
			unset($old_password[0]);

			$validator = \Validator::make($request->all(), [
				"old_password"              => array_merge(['sometimes'],$old_password),
			],[
				'old_password.required'     => trans('website.W0980'),
				'old_password.old_password' => trans('website.W0981'),
			]);

			$validator->sometimes(['old_password'], 'required', function($request){
				return (auth()->user()->social_account !== DEFAULT_YES_VALUE);  
			});


			if($validator->passes()){
				\Session::set('transferownerships', \Auth::user()->id_user);
				$this->redirect = url(sprintf('%s/settings/transferownership?type=list',TALENT_ROLE_TYPE));
				$this->message 	= trans("website.W0982");
				$this->status 	= true;

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

		public function confirm_transfer(Request $request){
			/*Email to logged in firm user*/
            // $email                  = 'chetandeep@singsys.com';
            $email  				= \Auth::user()->email;
            $emailData              = ___email_settings();
            $emailData['email']     = $email;
            $emailData['name']      = \Auth::user()->name;
	        $emailData['code']      = _get_couponCode();

            $template_name = "send_transfer_ownership_code";

            ___mail_sender($email,'',$template_name,$emailData);
            $insertCode = \DB::table('users')->where('id_user','=',\Auth::user()->id_user)->update(['transfer_ownership_otp'=>$emailData['code']]);
			if($request->ajax()){
				$data['user_id'] = $request->id;
				return view('talent.settings.transferownershipconfirm',$data);
			}
		}

		public function post_confirm_transfer(Request $request){

			$validator = \Validator::make($request->all(), [
				"password"              => ['sometimes'],
			],[
				'password.required'     => trans('website.W0984')
			]);

			$validator->sometimes(['password'], 'required', function($request){
				return (auth()->user()->social_account !== DEFAULT_YES_VALUE);  
			});

			$validator->after(function($v) use($request){
                if(!empty($request->password) && $request->password != \Auth::user()->transfer_ownership_otp){
                	$v->errors()->add('password', trans('website.W0985'));
               	}
            });


			if($validator->passes()){
				$data = \DB::table('users')->where('id_user','=',$request->user_id)->select('email','name')->first();

	            $email                  = $data->email;
		        $emailData              = ___email_settings();
		        $emailData['email']     = $email;
		        $emailData['name']      = $data->name;
		        $code 					= $request->password;
		        $user_id 				= \Auth::user()->id_user;
		        $emailData['link']      = url(sprintf("login?owner=%s&ownershiptoken=%s",$user_id,$code));
		        ___mail_sender($emailData['email'],sprintf("%s %s",$emailData['name'],''),"transfer_ownership_confirmation",$emailData);

	            \Session::forget('transferownerships');
	            $this->redirect = url('/');
				$this->message 	= trans("website.W0990");
				$this->status 	= true;

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

		public function accept_reject_transfer(Request $request){

			$data['header'] = 'innerheader';
            $data['footer'] = 'innerfooter';
            $data['back']   = '';

            $data['title'] = 'Search Job';
            $data['user_id'] = $request->id;
            $owner = \DB::table('company_connected_talent')->where('id_user',\Auth::user()->id_user)->select(['user_type'])->first();
            if($owner->user_type=='owner'){
            	return redirect(sprintf('%s/find-jobs',TALENT_ROLE_TYPE));
            }else{
            	return view('talent.settings.pages.accept_reject_transfer_index')->with($data);
            }
		}

		public function accept_reject_transfer_modal(Request $request,$id){
            if($request->ajax()){
                return view('talent.settings.pages.accept_reject_transfer_index_type',compact('id'));
            }
        
        }

        public function accept_reject_transfer_save(Request $request){
            $redirect = url('/');
            $data = \DB::table('users')->where('id_user',$request->id)->select('email','name','company_name','company_profile')->first();
            $emailData              = ___email_settings();
			$emailData['email']     = $data->email;
			$emailData['name']      = $data->name;
			if($request->confirmation=='accept'){
				$isUpdateUser = \DB::table('company_connected_talent')->where('id_user',$request->id)->update(['user_type'=>'user']);
				$isUpdateUserprofile = \DB::table('users')->where('id_user',$request->id)->update(['company_profile'=>'individual']);
				$isUpdateOwner = \DB::table('company_connected_talent')->where('id_user',\Auth::user()->id_user)->update(['user_type'=>'owner']);
				$isUpdateOwnerProfile = \DB::table('users')->where('id_user',\Auth::user()->id_user)->update(['company_profile'=>'company']);
            	$emailData['confirmation']      = \Auth::user()->name.' has succesfully accepted your transfer ownership request.';
			}
            else{
            	$emailData['confirmation']      = \Auth::user()->name.' has rejected your transfer ownership request.';
            }

			___mail_sender($emailData['email'],'','confirmation_transfer_ownership',$emailData);
			$emailData['email']     = \Auth::user()->email;
			$emailData['name']      = \Auth::user()->name;
			if($request->confirmation=='accept'){
				$emailData['confirmation']      = 'You has succesfully accepted your transfer ownership request.';
			}else{
				$emailData['confirmation']      = 'You have rejected the transfer ownership request.';
			}
			___mail_sender($emailData['email'],'','confirmation_transfer_ownership',$emailData);
			

            return response()->json([
                'data'      => [],
                'status'    => true,
                'message'   => 'You have successfully make your decision.',
                'redirect'  => $redirect,
            ]); 
        
        }

		public function save_verified_paypal_email(Request $request){

			$data['title']                  = trans('website.W0374');
			$data['subheader']              = 'talent.includes.top-menu';
			$data['header']                 = 'innerheader';
			$data['footer']                 = 'innerfooter';
			
			$data['submenu']                = 'profile';
			$data['view']                   = 'talent.settings.payments';
			$data['user']                   = \Models\Talents::get_user(\Auth::user());
			$data['currency']               =  json_decode(json_encode(\Models\Currency::getCurrencyList()),true);

			$isUpdated = \Models\Talents::change(\Auth::user()->id_user,[
				'paypal_id' => \Session::get('user_paypal_email'),
				'paypal_payer_id' => $request->merchantIdInPayPal,
				'updated'   => date('Y-m-d H:i:s')
			]);

			$data['verified_paypal_email'] = true;

			$data['isEmailConfirmed'] = !empty($request->isEmailConfirmed) ? $request->isEmailConfirmed: '' ; 
			$data['returnMessage']    = !empty($request->returnMessage) ? $request->returnMessage: '';

			return view('talent.settings.index')->with($data);
		}


		/**
		 * [This method is used for randering view of finding job] 
		 * @param  null
		 * @return \Illuminate\Http\Response
		 */
		
		public function find_jobs(Request $request){

			$request->request->add(['currency' => \Session::get('site_currency')]);

			$request['currency'] 			= \Session::get('site_currency');
			$data['currency'] 				= \Session::get('site_currency');
			$data['title']                  = trans('website.W0471');
			$data['subheader']              = 'talent.includes.top-menu';
			$data['header']                 = 'innerheader';
			$data['footer']                 = 'innerfooter';
			$data['view']                   = 'talent.findjob.view';

			/*set currency by user location*/
			// $data['user_set_currency']  = ip_info($_SERVER["REMOTE_ADDR"], "Country Code");
			// $data['user_set_currency']  = ip_info('115.249.91.203', "Country Code");
			// \Session::set('site_currency',$data['user_set_currency']);
			\Session::set('site_currency',\Session::get('site_currency'));
			
			$data['user']                   = \Models\Talents::get_user(\Auth::user());
			$data['search']                 = (!empty($request->search))?$request->search:"";
			
			return view('talent.findjob.index')->with($data);
		}

		/**
		 * [This method is used to handle job finding]
		 * @param  Request
		 * @return Json Response
		 */
		
		public function _find_jobs(Request $request){

			$request->request->add(['currency' => \Session::get('site_currency')]);

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

			$projects =  \Models\Projects::talent_jobs(\Auth::user())->proposalStatus(\Auth::user()->id_user);

			if(!empty($request->employment_type_filter)){
				$projects->where('projects.employment',$request->employment_type_filter);
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
				(awarded = '".DEFAULT_NO_VALUE."' AND proposal_status = 'applied')
				OR
				(awarded = '".DEFAULT_NO_VALUE."' AND project_status = 'pending' AND DATE(startdate) >= '".date('Y-m-d')."') 
				OR 
				(proposal_status = 'accepted' AND project_status IN('pending','initiated','closed','completed'))
			)");
			$projects->groupBy(['projects.id_project']);
			$projects->orderByRaw($sort);

			$jobs = [
				'result'                => $projects->limit($limit)->offset($offset)->get(),
				'total'                 => $projects->get()->count(),
				'total_filtered_result' => $projects->limit($limit)->offset($offset)->get()->count(),
			];


			if(!empty($jobs['result']->count())){
				foreach($jobs['result'] as $keys => $project){
					$html .= get_project_template((object)$project,'talent');
				}
			}else{
				$html .= '<div class="no-records-found">'.trans('website.W0750').'</div>';
			}

			if($jobs['total_filtered_result'] == DEFAULT_PAGING_LIMIT){
				$load_more = '<button type="button" class="btn btn-default btn-block btn-lg" data-request="filter-paginate" data-url="'.url(sprintf('%s/_find-jobs',TALENT_ROLE_TYPE)).'" data-target="#job_listing" data-showing="#paginate_showing" data-loadmore="#loadmore" data-form="[role=\'find-jobs\']">'.trans('website.W0254').'</button>';
				$can_load_more = true;
			}else{
				$load_more = '<button type="button" class="btn btn-default btn-block btn-lg hide" data-request="filter-paginate" data-url="'.url(sprintf('%s/_find-jobs',TALENT_ROLE_TYPE)).'" data-target="#job_listing" data-showing="#paginate_showing" data-loadmore="#loadmore" data-form="[role=\'find-jobs\']">'.trans('website.W0254').'</button>';
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

		/**
		 * [This method is used for finding a user's saved,current,scheduled and past job]
		 * @param  Request
		 * @return \Illuminate\Http\Response
		 */
		
		public function myjobs(Request $request, Builder $htmlBuilder, $type = 'scheduled'){
			$request['currency'] = \Session::get('site_currency');
			$data['title']                  = trans('website.W0472');
			$data['subheader']              = 'talent.includes.top-menu';
			$data['header']                 = 'innerheader';
			$data['footer']                 = 'innerfooter';
			$data['view']                   = 'talent.myjob.view';
			$data['user']                   = \Models\Talents::get_user(\Auth::user());

			if ($request->ajax()) {
				$user       = \Auth::user();
				$prefix     = DB::getTablePrefix();
				$projects   = \Models\Projects::talent_jobs($user);

				if($type == 'current'){
					$projects->with([
						'projectlog' => function($q) use($user){
							$q->select('project_id')->totalTiming()->where('talent_id',$user->id_user)->groupBy(['project_id']);
						},
						'proposal' => function($q){
							$q->defaultKeys()->where('talent_proposals.status','accepted')->orderBy('id_proposal','DESC');
						}
					])->whereHas('proposal',function($q) use($user){
						$q->where('talent_proposals.status','accepted');
						$q->where('user_id',$user->id_user);
					})
					->havingRaw("(project_status = 'initiated' OR project_status = 'completed')")
                	->having('is_cancelled','=',DEFAULT_NO_VALUE);
				}else if($type == 'scheduled'){
					$projects->with([
						'proposal' => function($q){
							$q->defaultKeys();
						}
					])->whereHas('proposal',function($q) use($user){
						$q->where('talent_proposals.status','accepted');
						$q->where('user_id',$user->id_user);
					})
					->having('project_status','=','pending')
                	->having('is_cancelled','=',DEFAULT_NO_VALUE);
				}else if($type == 'history'){
					$projects->with([
						'proposal' => function($q){
							$q->defaultKeys();
						}
					])->whereHas('proposal',function($q) use($user){
						$q->where('talent_proposals.status','accepted');
						$q->where('user_id',$user->id_user);
					})->havingRaw("(project_status = 'closed' OR is_cancelled = '".DEFAULT_YES_VALUE."')");
				}

				$projects->orderBy("projects.created","DESC");
				$projects = $projects->groupBy(['projects.id_project'])->get();

				return \Datatables::of($projects)->filter(function ($instance) use ($request) {
					if ($request->has('search')) {
						if(!empty($request->search['value'])){
							$instance->collection = $instance->collection->filter(function ($row) use ($request) {
								return (\Str::contains($row->title, $request->search['value']) || \Str::contains($row->company_name, $request->search['value']) || \Str::contains($row->description, $request->search['value'])) ? true : false;
							});
						} 
					}
				})
				->editColumn('title',function($project){
					return get_myproject_template($project,'talent');
				})
				->make(true);
			}

			$data['html'] = $htmlBuilder
			->parameters([
				"dom" => "<'row' <'col-md-6 table-heading'> <'col-md-3'f> <'col-md-3 filter-option'>> rt <'row'<'col-md-6'i><'col-md-6'p> >",
				"language" => [
					"sInfo" => "Showing _START_ - _END_ out of _TOTAL_ record(s)",
					"sInfoEmpty"=> "No record(s) found. ",
				]
				])
			->addColumn(['data' => 'title', 'name' => 'title', 'title' => '&nbsp;', 'width' => '0', 'searchable' => false, 'orderable' => false]);

			return view('talent.myjob.index')->with($data);
		}

		/**
		 * [This method is used for finding a job in detail]
		 * @param  Request
		 * @return \Illuminate\Http\Response
		 */

		public function project_details(Request $request,Builder $htmlBuilder, $page = 'details'){

			$request->request->add(['currency' => \Session::get('site_currency')]);

			$project_id             = ___decrypt($request->job_id);
			$data['subheader']      = 'talent.includes.top-menu';
			$data['header']         = 'innerheader';
			$data['footer']         = 'innerfooter';
			$data['view']           = "talent.jobdetail.{$page}";
			$data['user']           = \Models\Talents::get_user(\Auth::user());

			$data['submission_fee'] = SUBMISSION_FEE;
			$data['submission_fee_abs'] = abs(SUBMISSION_FEE);
			
			$prefix                 = DB::getTablePrefix();
			$language               = \App::getLocale();
			$user                   = (object)['id_user' => \Auth::user()->id_user];

			$data['companydata']			= \DB::table('company_connected_talent')->leftjoin('talent_company','talent_company.talent_company_id','=','company_connected_talent.id_talent_company')->select('company_name','company_website','company_biography')->where('id_user','=',\Auth::user()->id_user)->first();
			
			$data['project']        = \Models\Projects::defaultKeys()
            ->projectPrice()
            ->projectDescription(true)
            ->companyName()
            ->companyLogo()
            ->isProjectSaved($user->id_user)
			->withCount([
				'reviews' => function($q){
                    $q->where('sender_id',auth()->user()->id_user);
                }
			])
			->with([
				'proposal' => function($q) use($project_id){
					$q->defaultKeys()->where(['talent_proposals.status' => 'accepted'])->with([
						'talent' => function($q){
							$q->defaultKeys();
						}
					]);
				},
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
				'projectlog' => function($q) use($user){
					$q->select('project_id')->totalTiming()->where('talent_id',$user->id_user)->groupBy(['project_id']);
				},
				'employer' => function($q) use($language,$prefix,$user){
					$q->select(
						'id_user',
						'company_name',
						'contact_person_name',
                        'company_website',
                        'company_work_field',
						'company_biography',
						\DB::Raw("YEAR({$prefix}users.created) as member_since"),
						\DB::Raw("IF({$prefix}users.last_name IS NULL,{$prefix}users.first_name, CONCAT({$prefix}users.first_name, ' ',{$prefix}users.last_name)) AS name")
					);

					$q->isTalentSavedEmployer($user->id_user);
					$q->companyLogo();
					$q->country();
					$q->review();
					$q->totalHirings();
					$q->withCount([
						'reviews',
						'projects' => function($q){
							$q->whereNotIn('projects.status',['draft','trashed']);
						}
					]);
					$q->with([
						'transaction' => function($q) use($prefix){
							$q->select(
								'id_transactions',
								'transaction_user_id'
							)->totalPaidByEmployer();
							$q->groupBy('transaction_user_id');
						},
					]);
				},
				'dispute' => function($q){
					$q->defaultKeys();
				},
				'chat' => function($q){
					$q->defaultKeys()->where('sender_id',auth()->user()->id_user);
				}
			])->where('id_project',$project_id)->get()->first();
			
			
			if($page === 'details'){
				$subindustriesID 	= array_column($data['user']['subindustry'], 'id_industry');
				$skillsID 			= array_column($data['user']['skills'], 'id_skill');
				
				$data['project']->similarjobs = \Models\Projects::defaultKeys()
				->projectPrice()
				->companyName()
				->companyLogo()
				->having('project_status','=','pending')
				->where('id_project','!=',$project_id)
				->where(function($q) use($subindustriesID,$skillsID){
					$q->whereHas('subindustries.subindustries',function($q) use($subindustriesID){
						$q->whereIn('subindustry_id',$subindustriesID);
					});
					$q->orWhereHas('skills.skills',function($q) use($skillsID){
						$q->whereIn('skill_id',$skillsID);
					});
				})
				->where('projects.user_id','!=',$data['project']->company_id)
				->where("projects.is_cancelled",DEFAULT_NO_VALUE)
				->whereNotIn('projects.status',['draft','trashed'])
				->orderBy('id_project','DESC')
				->limit(SIMILAR_JOBS_LIMIT)
				->get();

				$data['project']->otherjobs = \Models\Projects::defaultKeys()
				->projectPrice()
				->companyName()
				->companyLogo()
				->where(function($q) use($subindustriesID,$skillsID){
					$q->whereHas('subindustries.subindustries',function($q) use($subindustriesID){
						$q->whereIn('subindustry_id',$subindustriesID);
					});
					$q->orWhereHas('skills.skills',function($q) use($skillsID){
						$q->whereIn('skill_id',$skillsID);
					});
				})
				->having('project_status','=','pending')
				->where('id_project','!=',$project_id)
				->where('projects.user_id','=',$data['project']->company_id)
				->where("projects.is_cancelled",DEFAULT_NO_VALUE)
				->whereNotIn('projects.status',['draft','trashed'])
				->orderBy('id_project','DESC')
				->limit(EMPLOYER_OTHER_JOBS_LIMIT)
				->get();
			}else if($page == 'reviews'){
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

			if(!empty($request->proposal_id)){
				$data['project']->proposal = \Models\Proposals::defaultKeys()->quotedPrice()->where('user_id',auth()->user()->id_user)
				->with([
					'file' => function($q){
						$q->defaultKeys();
					} 
				])
				->where('id_proposal',___decrypt($request->proposal_id))
				->get()
				->first();

			}else{
				$data['project']->proposal = \Models\Proposals::defaultKeys()
				->quotedPrice()
				->with([
					'file' => function($q){
						$q->defaultKeys();
					} 
				])
				->where('user_id',auth()->user()->id_user)
				->where('project_id',$data['project']->id_project)
				->where('status','!=','rejected')
				->get()
				->first();
			}
			
			$data['coupon_detail'] = \Models\Proposals::getCouponDetail($data['project']->id_project, auth()->user()->id_user);
			
			if(!empty($data['project']->proposal)){
				$data['project']->proposal->price_unit = ___cache('currencies')[$data['project']->proposal->price_unit];
			}

			if(!empty($data['project'])){
				$data['title'] = $data['project']->title;
			}else{
				return redirect('talent/find-jobs');
			}
			
			if($page == 'proposal' && $data['project']->status == 'trashed') {
				return redirect('talent/find-jobs');				
			}
			return view('talent.jobdetail.index')->with($data);
		}

		/**
		 * [This method is used for Proposal Submit]
		 * @param  Request
		 * @return Json Response
		 */

		public function submit_proposal(Request $request){
			if(empty($request->project_id)){
				return redirect(sprintf('%s/find-jobs',TALENT_ROLE_TYPE));
			}else{
				$project_id 	= ___decrypt($request->project_id);
				$project 		= \Models\Projects::where('id_project',$project_id)->select(['awarded','employment'])->get()->first();

				if($project->awarded == DEFAULT_NO_VALUE){
					$profileSetupArr = [];
					
					$userInfo = \Models\Talents::get_user(\Auth::user(),true);
					if(empty($request->user()->country)
					 || empty($userInfo['industry'])){
						$this->status = false;
						$this->message = sprintf(ALERT_DANGER,sprintf(trans("website.W0946"), url('talent/profile/step/one')));
					}
					else{
						$industry = $userInfo['industry'][0]['id_industry'];
						$commConfig = \Models\Payout_mgmt::toGetPayoutDetails($userInfo['country'], $industry);
						
						$companyDetail = \Models\companyConnectedTalent::where('id_user',\Auth::user()->id_user)->where('user_type','user')->first();

						if(!empty($companyDetail)){
							$companyOwnerData = \Models\companyConnectedTalent::with(['user'])->where('id_talent_company',$companyDetail->id_talent_company)->where('user_type','owner')->first();

							$userInfo = \Models\Talents::get_user($companyOwnerData->user,true);
							$industry = $userInfo['industry'];
							
							
							if(empty($industry)){
								$this->status = false;
								$this->message = sprintf(ALERT_DANGER,sprintf(trans("website.W0991"), url('talent/settings/payments'), 'www.paypal.com'));
								
							}else{
								$industry = $userInfo['industry'][0]['id_industry'];
								$commConfig = \Models\Payout_mgmt::toGetPayoutDetails($userInfo['country'], $industry);

								if($companyOwnerData->user->is_register == 'Y' && $commConfig['accept_escrow']=='yes' && empty($companyOwnerData->user->paypal_id)){
									$this->status = false;
									$this->message = sprintf(ALERT_DANGER,sprintf(trans("website.W0994"), url('talent/settings/payments'), 'www.paypal.com'));
								}
								elseif($companyOwnerData->user->is_register == 'N' && $commConfig['non_reg_accept_escrow']=='yes' && empty($companyOwnerData->user->paypal_id)){
									$this->status = false;
									$this->message = sprintf(ALERT_DANGER,sprintf(trans("website.W0994"), url('talent/settings/payments'), 'www.paypal.com'));
								}else{
									$this->status = true;
								}
							}
						}else {

							if($request->user()->is_register == 'Y' && $commConfig['accept_escrow']=='yes' && empty($request->user()->paypal_id)){
								$this->status = false;
								$this->message = sprintf(ALERT_DANGER,sprintf(trans("website.W0718"), url('talent/settings/payments'), 'www.paypal.com'));
							}
							elseif($request->user()->is_register == 'N' && $commConfig['non_reg_accept_escrow']=='yes' && empty($request->user()->paypal_id)){
								$this->status = false;
								$this->message = sprintf(ALERT_DANGER,sprintf(trans("website.W0718"), url('talent/settings/payments'), 'www.paypal.com'));
							} else {
								$this->status = true;
							}
						} 

						if($this->status == true) {
							$total_proposals_count 	= \Models\Proposals::select()->where([
								'project_id' 	=> $project_id, 
								'user_id' 		=> \Auth::user()->id_user
							])->get()->count();

							if($total_proposals_count <= TALENT_SUBMIT_PROPOSAL_LIMIT || !empty($request->proposal_id)){
								if(1){
									$validation_comments = validation('description'); unset($validation_comments[0]);
									
									if($project->employment == 'hourly'){
										if(!empty($request->from_time_hour) && !empty($request->from_time_minute) && !empty($request->from_time_meridian)){
											$request->request->add(['from_time' => date('H:i:s',strtotime(sprintf('%s:%s %s',$request->from_time_hour,$request->from_time_minute,$request->from_time_meridian)))]);
										}

										if(!empty($request->to_time_hour) && !empty($request->to_time_minute) && !empty($request->to_time_meridian)){
											$request->request->add(['to_time' => date('H:i:s',strtotime(sprintf('%s:%s %s',$request->to_time_hour,$request->to_time_minute,$request->to_time_meridian)))]);
										}

										$validation 					= [
											"from_time"             	=> array_merge(['required'],validation('time')),
											"to_time"               	=> array_merge(['required','different:from_time','invalid_time_range:from_time'],validation('time')),		
											"quoted_price"          	=> validation('quoted_price'),
											"comments"              	=> $validation_comments,
										];
									}else{
										$validation 					= [
											"quoted_price"          	=> validation('quoted_price'),
											"comments"              	=> $validation_comments,
										];
									}

									$validator = \Validator::make($request->all(), $validation,[
										'quoted_price.required'     	=> trans('general.M0358'),
										'quoted_price.numeric'      	=> trans('general.M0370'),
										'quoted_price.min'          	=> trans('general.M0438'),                            
										'quoted_price.max'          	=> trans('general.M0500'),                            
										'comments.required'         	=> trans('general.M0359'),
										'comments.string'           	=> trans('general.M0360'),
										'comments.regex'            	=> trans('general.M0360'),
										'comments.max'              	=> trans('general.M0361'),
										'comments.min'              	=> trans('general.M0362'),
										"from_time.required"            => trans('general.M0159'),
										"from_time.string"              => trans('general.M0160'),
										"from_time.regex"               => trans('general.M0160'),
										"to_time.required"              => trans('general.M0161'),
										"to_time.string"                => trans('general.M0162'),
										"to_time.regex"                 => trans('general.M0162'),
										"to_time.different"             => trans('general.M0222'),
										"to_time.one_hour_difference"   => trans('general.M0223'),
										"to_time.invalid_time_range"    => trans('general.M0224'),
									]);

									$validator->after(function($v) use($request){
		                                if(!empty($request->input('coupon_code'))){
			                                $apiID  = "c9ce23b8-0c52-4095-b416-d92c49be9c3b";
			                                $apiKey = "4bfd2a38-1c28-41de-aebd-59c3c088b4af";
			                                $client = new VoucherifyClient($apiID, $apiKey);

			                                try{
			                                    $get_voucher = $client->vouchers->get($request->input('coupon_code'));
			                                    $validate_voucher = $client->validations->validateVoucher($request->input('coupon_code'));
			                                }catch(ClientException $exception){
			                                    $v->errors()->add('coupon_code', 'Entered coupon code is invalid');
			                                }

			                                if(!empty($validate_voucher) && $validate_voucher->valid == true){

			                                    try{
			                                        $redeem_voucher = $client->redemptions->redeem($request->input('coupon_code'));
			                                        $request->request->add(['api_coupon_response' => $redeem_voucher]);
			                                        $coupon_code_id = \DB::table('coupon')->select('*')->where('code','=',$request->input('coupon_code'))->first();

			                                        $set_coupon_code_id = $coupon_code_id->id;
			                                        $couponStatus = \Models\Coupon::validateCoupon($set_coupon_code_id, \Auth::user()->id_user);

			                                        $currentTime = strtotime(date('Y-m-d H:i:s'));
			                                        #dd(strtotime($coupon_code_id->start_date) .'>='. $currentTime .'&&'. strtotime($coupon_code_id->expiration_date) .'<='. $currentTime);
			                                        if(strtotime($coupon_code_id->start_date) >= $currentTime || strtotime($coupon_code_id->expiration_date) <= $currentTime){
			                                            $v->errors()->add('coupon_code', 'Entered coupon code is expired.');
			                                        }
			                                        /*elseif($couponStatus){
			                                            $v->errors()->add('coupon_code', 'Entered coupon code is already in use.');
			                                        }*/
			                                        $request->request->add(['coupon_id' => $set_coupon_code_id]);

			                                    }catch(ClientException $exception){
			                                        $v->errors()->add('coupon_code', 'Entered coupon code could not be redeemed');
			                                    }
			                                }
			                                elseif(!empty($validate_voucher) && $validate_voucher->valid == false){
			                                	$v->errors()->add('coupon_code', 'Entered coupon code is expired or invalid');
			                                }
		                            	}
		                            });
									
									if($validator->passes()){
										$working_hours = time_difference($request->from_time, $request->to_time);
										$daily_working_hours 	= sprintf("%s:00:00",___cache('configuration')['daily_working_hours']);
										$working_hours_key 		= 'working_hours';
										
										$request->request->add(['working_hours' => $working_hours]);
										if((empty($request->{$working_hours_key}) || $request->{$working_hours_key} === '00:00') && $project->employment == 'hourly'){
											$this->jsondata = (object)['to_time' => trans('general.M0573')];	
										}else if($project->employment == 'hourly' && strtotime($request->{$working_hours_key}) > strtotime($daily_working_hours)){
											$this->status = false;
											$this->jsondata = (object)['to_time' => sprintf(trans('general.M0524'),substr($daily_working_hours, 0,-3))];
										}else{
											if(empty($request->proposal_id)){

												$insert_proposal = [
													'project_id'        => $project_id,
													'user_id'           => \Auth::user()->id_user,
													'price_unit'    	=> \Auth::user()->currency,
													'quoted_price'      => $request->quoted_price,
													'working_hours'     => $request->working_hours,
													'from_time'     	=> (!empty($request->from_time))?$request->from_time:'00:00:00',
													'to_time'     		=> (!empty($request->to_time))?$request->to_time:'00:00:00',
													'comments'          => $request->comments,
													'status'            => 'applied',
													'created'           => date('Y-m-d H:i:s'),
													'updated'           => date('Y-m-d H:i:s')
												];

												if(!empty($request->coupon_id)){
		                                            $insert_proposal['coupon_id'] = $request->coupon_id;
		                                        }

		                                        /*Check for Admin Manual payout. Add escrow type & pay commision(if present)*/
		                                        $job_industry_id = \Models\ProjectsIndustries::get_industry_by_jobID($project_id);
		                                        $talent_country_id = !empty(\Auth::user()->country) ?\Auth::user()->country:0;
												$payout_det = \Models\Payout_mgmt::toGetPayoutDetails($talent_country_id,$job_industry_id);
												
												if(!empty($payout_det)){
													if(\Auth::user()->is_register == 'Y' && $payout_det['accept_escrow'] == 'yes'){
														$insert_proposal['accept_escrow'] = $payout_det['accept_escrow'];
		                                            	$insert_proposal['pay_commision_percent'] = $payout_det['pay_commision_percent'];	
													}
													elseif(\Auth::user()->is_register == 'N' && $payout_det['non_reg_accept_escrow'] == 'yes'){
														$insert_proposal['accept_escrow'] = $payout_det['non_reg_accept_escrow'];
		                                            	$insert_proposal['pay_commision_percent'] = $payout_det['pay_commision_percent'];	
													}
													else{
														$insert_proposal['accept_escrow'] = 'no';
			                                            $insert_proposal['pay_commision_percent'] = '0.00';
													}
												}
												else{
													$insert_proposal['accept_escrow'] = 'yes';
		                                            $insert_proposal['pay_commision_percent'] = '0.00';
												}

												$proposal = \Models\Proposals::create($insert_proposal);

												/* RECORDING ACTIVITY LOG */
												event(new \App\Events\Activity([
													'user_id'           => \Auth::user()->id_user,
													'user_type'         => 'talent',
													'action'            => 'talent-submit-proposal',
													'reference_type'    => 'projects',
													'reference_id'      => $project_id
												]));										

											}else{

												$update_proposal = [
													'price_unit'    	=> \Auth::user()->currency,
													'working_hours'     => $request->working_hours,
													'quoted_price'      => $request->quoted_price,
													'from_time'     	=> (!empty($request->from_time))?$request->from_time:'00:00:00',
													'to_time'     		=> (!empty($request->to_time))?$request->to_time:'00:00:00',
													'comments'          => $request->comments,
													'status'            => 'applied',
													'edited'           	=> date('Y-m-d H:i:s')
												];

												if(!empty($request->coupon_id)){
		                                            $update_proposal['coupon_id'] = $request->coupon_id;
		                                        }
		                                        else{
		                                            $update_proposal['coupon_id'] = 0;
		                                        }

		                                        /*Check for Admin Manual payout. Add escrow type & pay commision(if present)*/
		                                        $job_industry_id = \Models\ProjectsIndustries::get_industry_by_jobID($project_id);
		                                        $talent_country_id = !empty(\Auth::user()->country) ?\Auth::user()->country:0;
												$payout_det = \Models\Payout_mgmt::toGetPayoutDetails($talent_country_id,$job_industry_id);

												if(!empty($payout_det)){
													if(\Auth::user()->is_register == 'Y' && $payout_det['accept_escrow'] == 'yes'){
														$update_proposal['accept_escrow'] = $payout_det['accept_escrow'];
		                                            	$update_proposal['pay_commision_percent'] = $payout_det['pay_commision_percent'];	
													}elseif(\Auth::user()->is_register == 'N' && $payout_det['non_reg_accept_escrow'] == 'yes'){
														$update_proposal['accept_escrow'] = $payout_det['non_reg_accept_escrow'];
		                                            	$update_proposal['pay_commision_percent'] = $payout_det['pay_commision_percent'];	
													}else{
														$update_proposal['accept_escrow'] = 'no';
			                                            $update_proposal['pay_commision_percent'] = '0.00';
													}
												}else{
													$update_proposal['accept_escrow'] = 'no';
		                                            $update_proposal['pay_commision_percent'] = '0.00';
												}

												\Models\Proposals::where('id_proposal',$request->proposal_id)->update($update_proposal);
												$proposal = (object)['id_proposal' => $request->proposal_id];
											}

											if(!empty($proposal)){
												if(!empty($request->proposal_docs)){
													$isFileUpdated = \Models\File::whereIn('id_file',explode(',', $request->proposal_docs))->update(['record_id' => $proposal->id_proposal, 'updated' => date('Y-m-d H:i:s')]);
												}

												$employer_details = \Models\Projects::where('id_project',$project_id)->select(['user_id','awarded'])->get()->first();

												if($employer_details->awarded = DEFAULT_YES_VALUE){
													\Models\Notifications::where('notification','JOB_UPDATED_BY_EMPLOYER')->where('notify',auth()->user()->id_user)->where('notified_by',$employer_details->user_id)->delete();
												}

												if(empty($request->proposal_id)){
													\Models\Talents::send_chat_request(\Auth::user()->id_user,$employer_details->user_id,$project_id,$proposal->id_proposal);
												}else{
													\Models\Talents::send_chat_request(\Auth::user()->id_user,$employer_details->user_id,$project_id,$proposal->id_proposal,NULL,true);
												}

											}
											
											$this->redirect = url(sprintf('%s/find-jobs/proposal?job_id=%s',TALENT_ROLE_TYPE,___encrypt($project_id)));
											$this->message 	= trans("general.M0368");
											$this->status 	= true;
										}
									}else{
										$this->jsondata = ___error_sanatizer($validator->errors());
										$this->status = false;
									}
								}else{
									$this->status = false;
									$this->message = sprintf(ALERT_DANGER,trans("website.W0256"));                    
								}
							}else{
								$this->status = false;
								$this->message = sprintf(ALERT_DANGER,trans("website.W0687"));                    
							}
						}
						
					}
				}else{
					$this->status = false;
					$this->message = sprintf(ALERT_DANGER,trans("general.M0558"));
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
		 * [This method is used for Document Proposal]
		 * @param  Request
		 * @return Json Response
		 */
		
		public function proposal_document(Request $request){
			$validator = \Validator::make($request->all(), [
				"file"                      => array_merge(validation('document'),['required','max:'.PROPOSAL_DOCUMENT_MAX_SIZE]),
			],[
				'file.required'             => trans('general.M0202'),
				'file.max'                  => trans('general.M0499'),
				'file.validate_file_type'   => trans('general.M0119'),
			]);

			if($validator->passes()){
				$folder = 'uploads/proposals/';
				$uploaded_file = upload_file($request,'file',$folder);
				$data = [
					'user_id'       => $request->user()->id_user,
					'record_id'     => -1,
					'reference'     => 'proposal',
					'filename'      => $uploaded_file['filename'],
					'extension'     => $uploaded_file['extension'],
					'folder'        => $folder,
					'type'          => 'proposal',
					'reference'     => 'proposal',
					'size'          => $uploaded_file['size'],
					'is_default'    => DEFAULT_NO_VALUE,
					'created'       => date('Y-m-d H:i:s'),
					'updated'       => date('Y-m-d H:i:s'),
				];

				$createdfile = \Models\Talents::create_file($data,true,true);
				
				if(!empty($createdfile)){
					/* RECORDING ACTIVITY LOG */
					event(new \App\Events\Activity([
						'user_id'           => \Auth::user()->id_user,
						'user_type'         => 'talent',
						'action'            => 'talent-add-propsal-document',
						'reference_type'    => 'users',
						'reference_id'      => \Auth::user()->id_user
					]));
					
					if(!empty($createdfile['folder'])){
						$createdfile['file_url'] 	= url(sprintf("%s/%s",$createdfile['folder'],$createdfile['filename']));
						$createdfile['extension'] 	= strtoupper($createdfile['extension']);
					}

					$this->jsondata = \View::make('talent.jobdetail.includes.attachment')->with(['file' => $createdfile])->render();
					$this->message  = sprintf(ALERT_SUCCESS,trans("general.M0110"));
					$this->status 	= true;
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

		/**
		 * [This method is used for Active Proposal]
		 * @param  Request
		 * @return \Illuminate\Http\Response
		 */

		public function proposals(Request $request, Builder $htmlBuilder, $type = 'active'){
			$request->request->add(['currency' => \Session::get('site_currency')]);

			if($type == 'active'){
				$proposals = \Models\Proposals::talents($type,\Auth::user()->id_user);
				$data['title']              = str_plural(trans('website.W0475'),count($proposals));
			}else{
				$proposals = \Models\Proposals::talents($type,\Auth::user()->id_user);
				$data['title']              = trans('website.W0689');
			}
			
			$data['subheader']              = 'talent.includes.top-menu';
			$data['header']                 = 'innerheader';
			$data['footer']                 = 'innerfooter';
			$data['view']                   = 'talent.proposals.view';
			$data['user']                   = \Models\Talents::get_user(\Auth::user());
			
			if ($request->ajax()) {

				return \Datatables::of($proposals)
				->editColumn('title',function($proposal){
					return get_talent_proposal_template($proposal);
				})
				->make(true);
			}

			$data['html'] = $htmlBuilder
			->parameters([
				"dom" => "<'row' <'col-md-6 table-heading'> <'col-md-3'> <'col-md-3 filter-option'>> rt <'row'<'col-md-6'i><'col-md-6'p> >",
				"language" => [
					"sInfo" => "Showing _START_ - _END_ out of _TOTAL_ record(s)",
					"sInfoEmpty"=> "No record(s) found. ",
				]
				])
			->addColumn(['data' => 'title', 'name' => 'title', 'title' => '&nbsp;', 'width' => '0', 'searchable' => false, 'orderable' => false]);
			
			return view('talent.proposals.index')->with($data);
		} 

		/**
		 * [This method is used for submitting working hours]
		 * @param  Request
		 * @return Json Response
		 */
		
		public function save_working_hours(Request $request){
			$validator = \Validator::make($request->all(), [
				'project_id' => validation('record_id')
			],[
				'project_id.required' => trans('general.M0121')
			]);

			if($validator->passes()){
				$working_hours_key 		= 'working_hours_'.$request->project_id;
				$project 				= \Models\Projects::defaultKeys()->withCount('dispute')->where('id_project',$request->project_id)->get()->first();
				// dd($project->employment);
				$is_proposal_accepted 	= \Models\Proposals::where('project_id',$request->project_id)->where('user_id',auth()->user()->id_user)->where('status','accepted')->defaultKeys()->orderBy('id_proposal','DESC')->get()->first();
				$total_working_hours 	= \Models\ProjectLogs::where('project_id',$request->project_id)->where('workdate',date('Y-m-d'))->totalTiming()->groupBy(['project_id'])->get()->first();
				$daily_working_hours 	= ($project->employment == 'hourly' ? $is_proposal_accepted->working_hours :sprintf("%s:00:00",___cache('configuration')['daily_working_hours']));

				if(!empty($total_working_hours)){
					$total_working_hours = \DB::Select("SELECT IFNULL(SEC_TO_TIME(TIME_TO_SEC('{$total_working_hours->total_working_hours}') + TIME_TO_SEC('{$request->{$working_hours_key}}')),'00:00:00') as total_working_hours");
					if(!empty($total_working_hours[0])){
						$total_working_hours = $total_working_hours[0];
					}
				}
				if(empty($project)){
					$this->message = trans('general.M0121');
				}else if(empty($request->{$working_hours_key}) || $request->{$working_hours_key} == '00:00'){
					$this->message = trans('general.M0525');
					$this->jsondata = (object)[
						$working_hours_key => trans('general.M0525'),
					];
				}else if(empty(strtotime($request->{$working_hours_key}))){
					$this->message = trans('general.M0523');
					$this->jsondata = (object)[
						$working_hours_key => trans('general.M0523')
					];
				}else if(empty($is_proposal_accepted)){
					$this->message = trans('general.M0521');
					$this->jsondata = (object)[
						$working_hours_key => trans('general.M0524')
					];					
				}else if(strtotime($request->{$working_hours_key}) > strtotime($daily_working_hours)){
					$this->message = trans('general.M0524');
					$this->jsondata = (object)[
						$working_hours_key => trans('general.M0524')
					];					
				}else if((!empty($total_working_hours) && empty(strtotime($total_working_hours->total_working_hours))) || (!empty($total_working_hours->total_working_hours) && (strtotime($total_working_hours->total_working_hours) > strtotime($daily_working_hours)))){
					$this->message = trans('general.M0524');
					$this->jsondata = (object)[
						$working_hours_key => trans('general.M0524')
					];
				}else if($project->project_status != 'initiated'){
					$this->message = trans('general.M0522');
					$this->jsondata = (object)[
						$working_hours_key => trans('general.M0522')
					];					
				}else if($project->status == 'trashed'){
					$this->message = trans('general.M0580');
					$this->jsondata = (object)[
						$working_hours_key => trans('general.M0580')
					];					
				}else if($project->is_cancelled == DEFAULT_YES_VALUE){
					$this->message = trans('general.M0581');
					$this->jsondata = (object)[
						$working_hours_key => trans('general.M0581')
					];					
				}else if(!empty($project->dispute_count)){
					$this->message = trans('general.M0571');
					$this->jsondata = (object)[
						$working_hours_key => trans('general.M0571')
					];				
				}else{
					$insert_data = [	
						'project_id'				=> $request->project_id,	
						'talent_id'					=> auth()->user()->id_user,	
						'employer_id'				=> $project->company_id,	
						'worktime'					=> $request->{$working_hours_key},	
						'workdate'					=> date('Y-m-d'),	
						'created'					=> date('Y-m-d H:i:s'),	
						'updated'					=> date('Y-m-d H:i:s'),	
					];

					$isSaved = \Models\ProjectLogs::save_project_log($insert_data);

					if(!empty($isSaved)){
						$this->status = true;
						$total_working_hours = \Models\ProjectLogs::where('project_id',$request->project_id)->totalTiming()->groupBy(['project_id'])->get()->first();
						
						$this->jsondata = [
							'render' 	=> true,
							'clear' 	=> ["target" => "[name=\"{$working_hours_key}\"]", "value" => '00:00'],
							'target' 	=> "#total_working_hours_{$request->project_id}",
							'html' 		=> ___hours(substr($total_working_hours->total_working_hours, 0, -3)),
						];
					}else{
						$this->message = trans('general.M0356');
					}
				} 
			}else{
				$this->jsondata = ___error_sanatizer($validator->errors());
			}

			return response()->json([
				'data'      => $this->jsondata,
				'status'    => $this->status,
				'nomessage' => true,
				'message'   => $this->message,
				'redirect'  => $this->redirect,
			]);
		}

		/*public function submitted_proposals(){
			$data['subheader']              = 'talent.includes.top-menu';
			$data['header']                 = 'innerheader';
			$data['footer']                 = 'innerfooter';
			$data['view']                   = 'talent.job.submitted-proposals';
			$data['user']                   = \Models\Talents::get_user(\Auth::user());

			$proposals = \Models\Talents::submitted_proposals(\Auth::user()->id_user);
			
			if(!empty($proposals['result'])){
				$data['proposals'] = $proposals['result'];
			}
			
			return view('talent.job.index')->with($data);
		}*/

		/**
		 * [This method is used for finding a saved job]
		 * @param  Request
		 * @return Json Response
		 */
		
		public function save_job(Request $request){
			$validator = \Validator::make($request->all(),[
				'job_id' => validation('job_id')
			],[
				'job_id.integer' => sprintf(trans('general.M0121'),'job_id')
			]);
		   
			if($validator->passes()){
				$isUpdated          = \Models\Talents::save_job(\Auth::user()->id_user,$request->job_id);
				
				/* RECORDING ACTIVITY LOG */
				event(new \App\Events\Activity([
					'user_id'           => \Auth::user()->id_user,
					'user_type'         => 'talent',
					'action'            => 'talent-save-job',
					'reference_type'    => 'users',
					'reference_id'      => \Auth::user()->id_user
				]));                
				
				$this->status   = true;
				$this->message  = sprintf(ALERT_SUCCESS,trans("general.M0218"));               
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
						$html .= '<a href="javascript:void(0);" data-request="inline-ajax" data-url="'.url(sprintf('%s/notifications/mark/read?notification_id=%s',TALENT_ROLE_TYPE,$item['id_notification'])).'" class="submenu-block clearfix '.$item['notification_status'].'">';
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

			return response()->json([
				"recordsTotal"      => intval($notifications['total']),
				"recordsFiltered"   => intval($notifications['total_filtered_result']),
				"loadMore"          => $load_more, 
				"data"              => $html,
			]);
		} 
		
		/**
		 * [This method is used to read marked notification]
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
				/* RECORDING ACTIVITY LOG */
				event(new \App\Events\Activity([
					'user_id'           => \Auth::user()->id_user,
					'user_type'         => 'talent',
					'action'            => 'talent-mark-read-notification',
					'reference_type'    => 'users',
					'reference_id'      => \Auth::user()->id_user
				]));                
			}

			return response()->json([
				'data'      => $this->jsondata,
				'status'    => $this->status,
				'message'   => $this->message,
				'redirect'  => $this->redirect,
			]);
		}

		/**
		 * [This method is used for chat]
		 * @param  Request
		 * @return \Illuminate\Http\Response
		 */
		
		public function chat(Request $request){
			$data['title']          = trans('website.W0477');
			$data['subheader']      = 'talent.includes.top-menu';
			$data['header']         = 'innerheader';
			$data['footer']         = 'innerfooter';
			$data['view']           = 'chat.view';
			$data['user']           = \Models\Talents::get_user(\Auth::user());
			
			return view('chat.index')->with($data);
		}

		/**
		 * [This method is used for initiate chat request]
		 * @param  Request
		 * @return Json Response
		 */
		
		public function initiate_chat_request(Request $request){
			$html = ''; 
			$isRequestSent = \Models\Chats::initiate_chat_request($request->sender,$request->receiver,$request->project_id);
			if(!empty($isRequestSent['status'])){
				$this->status = true;

				if($isRequestSent['chat_initiated'] == 'talent'){
					$html = '<button type="button" class="btn btn-secondary" title="'.trans('job.J0063').'">'.trans('job.J0063').'</button>';
				}else if($isRequestSent['chat_initiated'] == 'employer'){
					$html = '<button type="button" class="btn btn-secondary" data-request="chat-initiate" data-user="'.$request->receiver_id.'" data-url="'.url(sprintf('%s/chat',TALENT_ROLE_TYPE)).'">'.trans('website.W0296').'</button>';
				}else if($isRequestSent['chat_initiated'] == 'employer-accepted'){
					$html = '<button type="button" class="btn btn-secondary" data-request="chat-initiate" data-user="'.$request->receiver_id.'" data-url="'.url(sprintf('%s/chat',TALENT_ROLE_TYPE)).'">'.trans('website.W0296').'</button>';
				}

				$this->jsondata = ['html' => $html];
			}

			return response()->json([
				'data'      => $this->jsondata,
				'status'    => $this->status,
				'message'   => $this->message,
				'redirect'  => $this->redirect,
			]);
		}

		/**
		 * [This method is used for randering view of user's payment] 
		 * @param  null
		 * @return \Illuminate\Http\Response
		 */
		
		public function payment_talent(){
			$data['title']                  = trans('website.W0478');
			$data['subheader']              = 'talent.includes.top-menu';
			$data['header']                 = 'innerheader';
			$data['footer']                 = 'innerfooter';
			$data['view']                   = 'talent.job.payment';

			$data['user']                   = \Models\Talents::get_user(\Auth::user());
			
			return view('talent.profile.index')->with($data);
		}

		/**
		 * [This method is used for payement method]
		 * @param  Request
		 * @return \Illuminate\Http\Response
		 */

		public function payment_method(Request $request){
			$result = \Braintree_Customer::create(array(
				'firstName' => \Auth::user()->first_name,
				'lastName' => \Auth::user()->last_name,
				'company' => '',
				'email' =>\Auth::user()->email,
				'phone' =>\Auth::user()->mobile,
				'fax' => '',
				'website' => ''
			));

			dd($result);
		}

		/**
		 * [This method is used for add card ]
		 * @param  Request
		 * @return \Illuminate\Http\Response
		 */
		
		public function add_card(Request $request){
			$data['title']       = trans('website.W0479');
			$data['subheader']   = 'talent.includes.top-menu';
			$data['header']      = 'innerheader';
			$data['footer']      = 'innerfooter';
			$data['view']        = 'talent.job.add-card';

			$data['user']        = \Models\Talents::get_user(\Auth::user());
			$data['user_card']   = \Models\Payments::get_user_card(\Auth::user()->id_user);
			return view('talent.job.index')->with($data);
		}

		/**
		 * [This method is used for Add payment card]
		 * @param  Request
		 * @return Json Response
		 */
		
		public function add_payment_card(Request $request){
			// dd($request->credit_card['number']);
			$get_user_default_card = self::get_user_default_card(\Auth::user()->id_user);
			$created_date = date('Y-m-d H:i:s');
			$braintree_id = \Auth::user()->braintree_id;
			if(empty($braintree_id))
			{
				$add_customer_result = \Braintree_Customer::create(array(
					'firstName' => \Auth::user()->first_name,
					'lastName' => \Auth::user()->last_name,
					'email' =>\Auth::user()->email,
				));

				\Models\Payments::braintree_response([
					'user_id' => \Auth::user()->id_user,
					'braintree_response_json' => json_encode($add_customer_result),
					'created' => $created_date
				]);
				$braintree_id = $add_customer_result->customer->id;
				$update['braintree_id'] = $add_customer_result->customer->id;
				\Models\Talents::change(\Auth::user()->id_user,$update);
			}

			$add_card_result = \Braintree_CreditCard::create(array(
				'cardholderName' => $request->credit_card['cardholder_name'],
				'customerId' => $braintree_id,
				'expirationDate' => $request->credit_card['expiry_month'] . '/' . $request->credit_card['expiry_year'],
				'number' => $request->credit_card['number'],
				'cvv' => $request->credit_card['cvv']
			));

			\Models\Payments::braintree_response([
				'user_id' => \Auth::user()->id_user,
				'braintree_response_json' => json_encode((array)$add_card_result->creditCard),
				'created' => $created_date
			]);
			if($add_card_result->success){
				$credit_card['user_id']                     = \Auth::user()->id_user;
				$credit_card['type']                        = \Auth::user()->type;
				$credit_card['bin']                         = $add_card_result->creditCard->bin;
				$credit_card['expiration_month']            = $add_card_result->creditCard->expirationMonth;
				$credit_card['expiration_year']             = $add_card_result->creditCard->expirationYear;
				$credit_card['last4']                       = $add_card_result->creditCard->last4;
				$credit_card['card_type']                   = $add_card_result->creditCard->cardType;
				$credit_card['cardholder_name']             = $add_card_result->creditCard->cardholderName;
				$credit_card['commercial']                  = $add_card_result->creditCard->commercial;
				$credit_card['country_of_issuance']         = $add_card_result->creditCard->countryOfIssuance; 
				$credit_card['created_at']                  = $add_card_result->creditCard->createdAt->format('Y-m-d H:i:s');
				$credit_card['customer_id']                 = $add_card_result->creditCard->customerId;
				$credit_card['customer_location']           = $add_card_result->creditCard->customerLocation;
				$credit_card['debit']                       = $add_card_result->creditCard->debit;
				$credit_card['default']                     = $add_card_result->creditCard->default;
				$credit_card['durbin_regulated']            = $add_card_result->creditCard->durbinRegulated;
				$credit_card['expired']                     = $add_card_result->creditCard->expired;
				$credit_card['healthcare']                  = $add_card_result->creditCard->healthcare;
				$credit_card['image_url']                   = $add_card_result->creditCard->imageUrl;
				$credit_card['issuing_bank']                = $add_card_result->creditCard->issuingBank;
				$credit_card['payroll']                     = $add_card_result->creditCard->payroll;
				$credit_card['prepaid']                     = $add_card_result->creditCard->prepaid;
				$credit_card['product_id']                  = $add_card_result->creditCard->productId;
				$credit_card['subscriptions']               = json_encode($add_card_result->creditCard->subscriptions);
				$credit_card['token']                       = $add_card_result->creditCard->token;
				$credit_card['unique_number_identifier']    = $add_card_result->creditCard->uniqueNumberIdentifier;
				$credit_card['updated_at']                  = $add_card_result->creditCard->updatedAt->format('Y-m-d H:i:s');
				$credit_card['venmo_sdk']                   = $add_card_result->creditCard->venmoSdk;
				$credit_card['verifications']               = json_encode($add_card_result->creditCard->verifications);
				$credit_card['billing_address']             = $add_card_result->creditCard->billingAddress;
				$credit_card['expiration_date']             = $add_card_result->creditCard->expirationDate;
				$credit_card['masked_number']               = $add_card_result->creditCard->maskedNumber;
				$credit_card['card_status']                 = 'active';
				$credit_card['updated']                     = $created_date;
				$credit_card['created']                     = $created_date;
				$isInserted = \Models\Payments::save_credit_card($credit_card);
				
				/* RECORDING ACTIVITY LOG */
				event(new \App\Events\Activity([
					'user_id'           => \Auth::user()->id_user,
					'user_type'         => 'talent',
					'action'            => 'talent-add-payment-card',
					'reference_type'    => 'users',
					'reference_id'      => \Auth::user()->id_user
				]));
				
				if($isInserted){
					$url_delete = sprintf(
						url('ajax/%s?card_id=%s'),
						DELETE_CARD,
						$isInserted
					);
					$this->jsondata = sprintf(
						ADD_CARD_TEMPLATE,
						$isInserted,
						$credit_card['image_url'],
						$credit_card['masked_number'],
						$url_delete,
						$isInserted,
						url('/'),
						url('/')
					);
					$this->status   = true;
					$this->message  = sprintf(ALERT_SUCCESS,trans("general.M0110"));
				}else{
				}
				return response()->json([
					'data'      => $this->jsondata,
					'status'    => $this->status,
					'message'   => $this->message,
					'redirect'  => $this->redirect,
				]);
			}
		}

		/**
		 * [This method is used for Apply Job]
		 * @param  Request
		 * @return Json Response
		 */
		
		public function apply_job(Request $request){
			if(!empty($request->project_id)){
				$project_id = ___decrypt($request->project_id);
				$application_data = Proposals::select()->where(['project_id' => $project_id, 'user_id' => $request->user()->id_user, 'type' => 'application'])->get()->count();
				
				if(empty($application_data)){
					$insertArr = [
						'project_id' => $project_id,
						'user_id'    => $request->user()->id_user,
						'type'       => 'application',
						'created'    => date('Y-m-d H:i:s'),
						'updated'    => date('Y-m-d H:i:s')
					];
					
					$proposaldata = Proposals::create($insertArr);
					
					/* RECORDING ACTIVITY LOG */
					event(new \App\Events\Activity([
						'user_id'           => \Auth::user()->id_user,
						'user_type'         => 'talent',
						'action'            => 'talent-apply-job',
						'reference_type'    => 'users',
						'reference_id'      => \Auth::user()->id_user
					]));
					
					$this->status = true;
					$this->message = sprintf(ALERT_SUCCESS,trans("general.M0251"));
					$this->redirect = url(sprintf('%s/find-jobs/job-details?job_id=%s',TALENT_ROLE_TYPE,___encrypt($project_id)));
				}else{
					$this->message = trans("general.M0252");
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
		 * [This method is used for actions]
		 * @param  Request
		 * @return  Json Response
		 */

		public function actions(Request $request,$action){
			$project_id     = (int)___decrypt($request->project_id);
			
			$job_details    = \Models\Projects::findById($project_id,['user_id as company_id','employment','startdate','enddate']);
			$logs           = \Models\ProjectLogs::findById($project_id);
			$already_logged = \Models\ProjectLogs::is_alredy_logged($project_id,\Auth::user()->id_user,$job_details['employment']);
			
			if(empty($job_details)){
				$this->message = trans("general.M0121");
				$this->error = sprintf(trans(sprintf('general.%s',$this->message)),'project_id');
			}else if(strtotime(date('Y-m-d')) < strtotime($job_details['startdate']) && strtotime(date('Y-m-d')) < strtotime($job_details['enddate'])){
				$this->message = trans("general.M0357");
			}else if($action == 'start' && $logs['start'] == 'confirmed'){
				$this->message = trans("general.M0424");
			}else if($action == 'close' && $logs['close'] == 'confirmed'){
				$this->message = trans("general.M0425");
			}else if(!empty($already_logged)){
				$this->message = trans("general.M0425");
			}else{
				if($action == 'start'){
					$isUpdated          = \Models\Projects::change([
						'id_project' => $project_id,
						'project_status' => 'pending'
					],[
						'project_status' => 'initiated',
						'updated' => date('Y-m-d H:i:s')
					]);

					$projectLogData     = [
						"project_id"        =>  $project_id,
						"talent_id"         =>  \Auth::user()->id_user,
						"employer_id"       =>  $job_details['company_id'],
						"start_timestamp"   =>  date('Y-m-d H:i:s'),
						"startdate"         =>  date('Y-m-d H:i:s'),
						"created"           =>  date('Y-m-d H:i:s'),
						"updated"           =>  date('Y-m-d H:i:s'),
					];

					$isSaved            = \Models\ProjectLogs::save_project_log($projectLogData);

					# $isNotified = \Models\Notifications::notify(
					# 	$job_details['company_id'],
					# 	\Auth::user()->id_user,
					# 	'JOB_STARTED_BY_TALENT',
					# 	json_encode([
					# 		"user_id" => (string) $job_details['company_id'],
					# 		"project_id" => (string) $project_id
					# 	])
					# );
				}else if($action == 'close'){
					$isClosed = \Models\ProjectLogs::request_close_job($project_id,\Auth::user()->id_user);

					if(!empty($isClosed)){
						$isNotified = \Models\Notifications::notify(
							$job_details['company_id'],
							\Auth::user()->id_user,
							'JOB_COMPLETED_BY_TALENT',
							json_encode([
								"user_id" => (string) $job_details['company_id'],
								"project_id" => (string) $project_id
							])
						);

					}
				}

				$this->status   = true;
				$this->message  = trans("general.M0283");               
			}

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

		public function project_status(Request $request,$status = 'start'){
			$request->request->add(['currency' => \Session::get('site_currency')]);
			
			$project_id = ___decrypt($request->project_id);
			$project 	= \Models\Projects::defaultKeys()
			->withCount(['dispute'])
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
			}else if($status == 'start' && $project->project_status == 'initiated'){
				$this->message = trans("general.M0559");
			}else if($status == 'start' && (strtotime($project->enddate) < strtotime(date('Y-m-d')))){
				$this->message = trans("general.M0565");
				$this->head_message = trans("general.M0600");
			}else if($status == 'start' && (strtotime($project->startdate) > strtotime(date('Y-m-d')))){
				$this->message = trans("general.M0582");
				$this->head_message = trans("general.M0600");
			}else if($status == 'start' && !empty($project->dispute_count)){
				$this->message = trans("general.M0566");
			}else if($status == 'start' && $project->status == 'trashed'){
				$this->message = trans("general.M0580");
			}else if($status == 'start' && $project->is_cancelled == DEFAULT_YES_VALUE){
				$this->message = trans("general.M0581");
			}else if($status == 'close' && $project->project_status == 'closed'){
				$this->message = trans("general.M0560");
			}else if($status == 'close' && !empty($project->dispute_count)){
				$this->message = trans("general.M0566");
			}/*else if($status == 'close' && empty($project->projectlog)){
				$this->message = trans("general.M0561");
			}*/else{
				switch($status){
					case 'start': {
						$isUpdated          = \Models\Projects::change([
							'id_project' 		=> $project_id,
							'project_status' 	=> 'pending'
						],[
							'project_status'    => 'initiated',
							'updated'           => date('Y-m-d H:i:s')
						]);

						if(!empty($isUpdated)){
							/* RECORDING ACTIVITY LOG */
							event(new \App\Events\Activity([
								'user_id'           => auth()->user()->id_user,
								'user_type'         => 'talent',
								'action'            => 'talent-start-job',
								'reference_type'    => 'projects',
								'reference_id'      => $project_id
							]));

							$isNotified = \Models\Notifications::notify(
                                $project->company_id,
                                $project->proposal->talent->id_user,
                                'JOB_STARTED_BY_TALENT',
                                json_encode([
                                    "employer_id"   => (string) $project->company_id,
                                    "talent_id"     => (string) $project->proposal->talent->id_user,
                                    "project_id"    => (string) $project->id_project
                                ])
                            );

							$this->status = true;
							$this->redirect = url(sprintf('%s/project/details?job_id=%s',TALENT_ROLE_TYPE,___encrypt($project_id)));
						}
						break;
					}
					case 'close': {

						$isUpdated          = \Models\Projects::change([
							'id_project' 		=> $project_id,
							'project_status' 	=> 'initiated'
						],[
							'project_status'    => 'closed',
							'completedate'    	=> date('Y-m-d H:i:s'),
							'updated'           => date('Y-m-d H:i:s')
						]);

						if(!empty($isUpdated)){
							/* RECORDING ACTIVITY LOG */
							event(new \App\Events\Activity([
								'user_id'           => auth()->user()->id_user,
								'user_type'         => 'talent',
								'action'            => 'talent-completed-job',
								'reference_type'    => 'projects',
								'reference_id'      => $project_id
							]));

							$isNotified = \Models\Notifications::notify(
                                $project->company_id,
                                $project->proposal->talent->id_user,
                                'JOB_COMPLETED_BY_TALENT',
                                json_encode([
                                    "employer_id"   => (string) $project->company_id,
                                    "talent_id"     => (string) $project->proposal->talent->id_user,
                                    "project_id"    => (string) $project->id_project
                                ])
                            );

							$this->status = false;
							$this->message = trans("general.M0605");

							$this->redirect = url(sprintf('%s/project/details?job_id=%s',TALENT_ROLE_TYPE,___encrypt($project_id)));
						}
						break;
					}
				}
			}

			return response()->json([
				'data'      	=> $this->jsondata,
				'status'    	=> $this->status,
				'message'   	=> $this->message,
				'head_message'  => $this->head_message,
				'nomessage' 	=> true,
				'redirect'  	=> $this->redirect,
			]);
		}

		/**
		 * [This method is used for report abuse in chat section]
		 * @param  Request
		 * @return Json Response
		 */
		
		public function report_abuse(Request $request){
			$validate = \Validator::make($request->all(), [
				"reason"             => validation('description'),
			],[
				'reason.required'    => trans('general.M0320'),
				'reason.string'      => trans('general.M0321'),
				'reason.regex'       => trans('general.M0321')
			]);

			if($validate->passes()){
				$reason         = (string)$request->reason;
				$sender_id      = (int)\Auth::user()->id_user;
				$receiver_id    = (int)$request->receiver_id;
				
				$isReported = \Models\Abuse::report($sender_id,$receiver_id,$reason);
				
				/* RECORDING ACTIVITY LOG */
				event(new \App\Events\Activity([
					'user_id'           => \Auth::user()->id_user,
					'user_type'         => 'talent',
					'action'            => 'talent-report-abuse',
					'reference_type'    => 'users',
					'reference_id'      => \Auth::user()->id_user
				]));
			}else{
				$this->jsondata = (object)___error_sanatizer($validate->errors());
			}

			return response()->json([
				'data'      => $this->jsondata,
				'status'    => $this->status,
				'message'   => $this->message,
				'redirect'  => $this->redirect,
			]);
		}

		/**
		 * [This method is used for total received amount and total due amount information]
		 * @param  Request
		 * @return Json Response
		 */

		public function wallet(Request $request, Builder $htmlBuilder, $type = 'all'){
			$request->request->add(['currency' => \Session::get('site_currency')]);
			$data['title']                  = trans('website.W0482');
			$data['subheader']              = 'talent.includes.top-menu';
			$data['header']                 = 'innerheader';
			$data['footer']                 = 'innerfooter';
			$data['view']                   = 'talent.wallet.list';

			$data['user']                   = \Models\Talents::get_user(\Auth::user());
			$data['payment_summary']        = \Models\Payments::summary($data['user']['id_user'],'talent');
			
			if ($request->ajax()) {
				$payments = \Models\Payments::listing($data['user']['id_user'],'talent',$type);
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
					if($type == 'all' || $type == 'disputed'){
						$payment->transaction_subtotal = ___calculate_payment($payment->employment,$payment->quoted_price);
					}

					$html = '<div class="content-box-header clearfix">';
						$html .= '<div class="row payment-contentbox">';
							$html .= '<div class="col-md-9 col-sm-8 col-xs-7">';
								$html .= '<div class="contentbox-header-title">';
									$html .= '<h3><a href="'.url(sprintf('%s/project/details?job_id=%s',TALENT_ROLE_TYPE, ___encrypt($payment->transaction_project_id))).'">'.$payment->title.'</a></h3>';
									$html .= '<span class="company-name">'.$payment->company_name.'</span>';
								$html .= '</div>';
							$html .= '</div>';
						$html .= '</div>';
					$html .= '</div>';
					$html .= '<div class="contentbox-minutes clearfix">';
						$html .= '<div class="minutes-left">';
							$html .= '<span>'.trans('website.W0368').'  <strong> '.___d($payment->transaction_date).'</strong></span>';
							$html .= '<span>'.trans('website.W0369').'  <strong> '.___readable($payment->transaction,true).'</strong></span>';
							$html .= '<span>'.trans('website.W0370').'  <strong> '.$payment->currency.___format($payment->transaction_subtotal,true,false).'</strong></span>';
							if($type == 'all'){
								$html .= '<span>'.'Expected Payment Date'.'  <strong> '.___d(date ('Y-m-d',strtotime('+1 day', strtotime($payment->enddate)))).'</strong></span>';
							}
						$html .= '</div>';
					$html .= '</div>';

					return $html;
				})
				->make(true);
			}

			$data['html'] = $htmlBuilder
			->parameters([
				"dom" => "rt <'row'<'col-md-6'i><'col-md-6'p> >",
				"language" => [
					"sInfo" => "Showing _START_ - _END_ out of _TOTAL_ record(s)",
					"sInfoEmpty"=> "No record(s) found.",
				]
				])
			->addColumn(['data' => 'title', 'name' => 'title', 'title' => '&nbsp;', 'width' => '0', 'searchable' => false, 'orderable' => false]);

		 
			return view('talent.wallet.index')->with($data);
		}
		/**
		 * [This method is used for view of Job action]
		 * @param  Request
		 * @return Json Response
		 */
		
		public function jobactions(Request $request){
			$request['currency'] = \Session::get('site_currency');
			$job_id                 = ___decrypt($request->job_id);
			$data['job_details']    = \Models\Talents::get_job(\Auth::user()," id_project = {$job_id} ","single");

			$this->status   = true;
			$this->jsondata = view('talent.job.include.job-detail')->with($data)->render();

			return response()->json([
				'data'      => $this->jsondata,
				'status'    => $this->status,
				'message'   => $this->message,
				'redirect'  => $this->redirect,
			]);            
		}

		/**
		 * [This method is used for Job action]
		 * @param  Request
		 * @return Json Response
		 */
		
		public function job_actions(Request $request){
			$project_id     = ___decrypt($request->job_id);
			$job_detail     = \Models\Projects::talent_actions($project_id);
			
			$this->status   = true;
			$this->jsondata = [
				'html' => view('talent.job.include.job-detail')->with(compact('job_detail'))->render(),
				'receiver_id' => $job_detail['sender_id']
			];

			return response()->json([
				'data'      => $this->jsondata,
				'status'    => $this->status,
				'message'   => $this->message,
				'redirect'  => $this->redirect,
			]);            
		}

		/**
		 * [This method is used for Payout Request]
		 * @param  Request
		 * @return Json Response
		 */

		public function payout_request(Request $request){
			$project_id = !empty($request->job_id) ? ___decrypt($request->job_id) : '';
			$job_detail = \Models\Projects::findById($project_id);
			if(empty($job_detail)){
				$this->message  = trans("general.M0283"); 
			}else{
				$updateData = [
					'request_payout'    => 'yes',
					'updated'           => date('Y-m-d H:i:s')
				];

				$isNotified = \Models\Notifications::notify(
					$job_detail['user_id'],
					\Auth::user()->id_user,
					'JOB_REQUEST_PAYOUT_BY_TALENT',
					json_encode([
						"user_id" => (string) $job_detail['user_id'],
						"project_id" => (string) $project_id
					])
				);

				/* RECORDING ACTIVITY LOG */
				event(new \App\Events\Activity([
					'user_id'           => \Auth::user()->id_user,
					'user_type'         => 'talent',
					'action'            => 'talent-request-payout',
					'reference_type'    => 'users',
					'reference_id'      => \Auth::user()->id_user
				]));
				
				$isUpdated = \Models\ProjectLogs::where(['project_id' => $project_id, 'close' => 'pending'])->update($updateData);
				if($isUpdated){
					$this->status   = true;
					$this->message  = sprintf(ALERT_SUCCESS,trans("general.M0110"));
				}
				
				return response()->json([
					'data'      => $this->jsondata,
					'status'    => $this->status,
					'message'   => $this->message,
					'redirect'  => $this->redirect,
				]);
			}
		}

		/**
		 * [This method is used forJob dispute Detail]
		 * @param  Request
		 * @return \Illuminate\Http\Response
		 */

		public function job_dispute_detail(Request $request){
			$request->request->add(['currency' => \Session::get('site_currency')]);
			$prefix                 = DB::getTablePrefix();
			$data['subheader']      = 'talent.includes.top-menu';
			$data['header']         = 'innerheader';
			$data['footer']         = 'innerfooter';
			$data['view']           = 'talent.jobdetail.disputes';
			$data['user']           = \Models\Talents::get_user(\Auth::user());
			
			$user                   = (object)['id_user' => \Auth::user()->id_user];
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
							$q->defaultKeys();
						}
					]);
				},
				'employer' => function($q) use($prefix,$user){
					$q->select(
						'id_user',
						'company_name',
						'contact_person_name',
                        'company_website',
                        'company_work_field',
						'company_biography',
						\DB::Raw("YEAR({$prefix}users.created) as member_since"),
						\DB::Raw("CONCAT({$prefix}users.first_name, ' ',{$prefix}users.last_name) AS name")
					);

					$q->isTalentSavedEmployer($user->id_user);
					$q->companyLogo();
					$q->country();
					$q->review();
					$q->totalHirings();
					$q->withCount([
						'projects' => function($q){
							$q->whereNotIn('projects.status',['draft','trashed']);
						}
					]);
					$q->with([
						'transaction' => function($q) use($prefix){
							$q->select(
								'id_transactions',
								'transaction_user_id'
							)
							->totalPaidByEmployer()
							->where('transaction_type','debit')
							->where('transaction_status','confirmed')
							->groupBy('transaction_user_id');
						},
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
						}
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
			
			return view('talent.jobdetail.index')->with($data);
		}

		public function networks(Request $request, Builder $htmlBuilder){
			$data['title']          = trans('website.W0730');
			$data['subheader']      = 'talent.includes.top-menu';
			$data['header']         = 'innerheader';
			$data['footer']         = 'innerfooter';
			$data['view']           = 'talent.invite.list';
			$data['user']           = \Models\Talents::get_user(\Auth::user());

			$prefix                 = DB::getTablePrefix();
			$language               = \App::getLocale();
			$user                   = (object)['id_user' => \Auth::user()->id_user];

			if ($request->ajax()) {
				$invite_list = \Models\InviteTalent::select(['id_invite','employer_id','status'])->with([
					'employerDetail' => function($q) use($language,$prefix,$user){
						$q->select(
							'id_user',
							'company_name',
							'contact_person_name',
                            'company_website',
                            'company_work_field',
							'company_biography',
							\DB::Raw("YEAR({$prefix}users.created) as member_since"),
							\DB::Raw("CONCAT({$prefix}users.first_name, ' ',{$prefix}users.last_name) AS name")
						);

						$q->isTalentSavedEmployer($user->id_user);
						$q->companyLogo();
						$q->country();
						$q->withCount('projects');
					}
				])->where('status','accepted')->where('talent_id',\Auth::user()->id_user)->get();
				return \Datatables::of($invite_list)
				->editColumn('invite',function($item){
					$item = (json_decode(json_encode($item),true));
					$html = view('talent.invite.template')->with($item)->render();
					return $html;
				})
				->make(true);
			}

			$data['html'] = $htmlBuilder
			->parameters(["dom" => "<'row' <'col-md-6 table-heading'> <'col-md-3'> <'col-md-3 filter-option'>> rt <'row'<'col-md-6'><'col-md-6'p> >"])
			->addColumn(['data' => 'invite', 'name' => 'invite', 'title' => '&nbsp;', 'width' => '0', 'searchable' => false, 'orderable' => false]);            

			return view('talent.invite.index')->with($data);
		}

		public function mynetworks(Request $request, Builder $htmlBuilder){

			$data['title']          = trans('website.W0730');
			$data['subheader']      = 'talent.includes.top-menu';
			$data['header']         = 'innerheader';
			$data['footer']         = 'innerfooter';
			$data['submenu']        = 'talent.community.submenu';
			$data['view']           = 'talent.community.networks';

			$data['user']           = \Models\Talents::get_user(\Auth::user()); 
			$data['request_list'] 	= \Models\Members::getMemberRequest(\Auth::user()->id_user);

			// $incircle = ($request->input('circle') == 'yes') ? $request->input('circle'):'';
			// $talents = \Models\Talents::get_members($incircle);
			// dd($talents);

			if ($request->ajax()) {
				$incircle = ($request->input('circle') == 'yes') ? $request->input('circle'):'';
				$talents = \Models\Talents::get_members($incircle);

				return \Datatables::of($talents)->filter(function ($instance) use ($request) {
					if ($request->has('search')) {
						if(!empty($request->search['value'])){
							$instance->collection = $instance->collection->filter(function ($row) use ($request) {
								return (\Str::contains($row->name, $request->search['value']) || \Str::contains($row->first_name, $request->search['value']) || \Str::contains($row->last_name, $request->search['value'])) ? true : false;
							});
						} 
					}

				})
				->editColumn('title',function($item){
					$item = json_decode(json_encode($item),true);
					return view('talent.community.member-template',$item)->render();
				})
				->make(true);
			}

			$data['html'] = $htmlBuilder
			->parameters(["dom" => "<'row'<'col-md-4'> <'col-md-8 filter-option'>> t <'row'<'col-md-12'> >"])
			->addColumn(['data' => 'title', 'name' => 'member', 'title' => '&nbsp;', 'width' => '0', 'searchable' => false, 'orderable' => false]);

			return view('talent.community.index')->with($data);
		}

		public function acceptmember(Request $request){

			if($request->status == "rejected"){
				$message = trans('website.W0919');
			}else{
				$message = trans('website.W0920');
				$data = [
					'user_id' 		 =>	$request->member_id,
					'member_id' 	 =>	$request->user_id,
					'note' 			 =>	'',
					'request_status' => 'accepted',
					'created' 		 =>	date('Y-m-d H:i:s'),
					'updated' 		 =>	date('Y-m-d H:i:s'),
				];
				$insertedId = \Models\Members::add_member($data);
			}

			$insertedId = \Models\Members::request_status($request->member_id,$request->user_id,$request->status);

			if($insertedId){
				$this->status   = true;
				$this->message  = sprintf(ALERT_SUCCESS,$message);
				$this->redirect = url(sprintf("%s/network",TALENT_ROLE_TYPE));
			}else{

				$this->status   = false;
				$this->message  = sprintf(ALERT_SUCCESS,trans('website.W0897'));
				
			}

			return response()->json([
				'data'      => $this->jsondata,
				'status'    => $this->status,
				'message'   => $this->message,
				'redirect'  => $this->redirect,
			]);

		}

		public function members(Request $request, Builder $htmlBuilder){

			$data['title']          = trans('website.W0887');
			$data['subheader']      = 'talent.includes.top-menu';
			$data['header']         = 'innerheader';
			$data['footer']         = 'innerfooter';
			$data['submenu']        = 'talent.community.submenu';
			$data['view']           = 'talent.community.members';

			$data['user']           = \Models\Talents::get_user(\Auth::user());  


			if ($request->ajax()) {
				$talents = collect();
				if(!empty($request->search['value']) || $request->input('circle') == 'yes'){
					$incircle = ($request->input('circle') == 'yes') ? $request->input('circle'):'';
					$talents = \Models\Talents::get_members($incircle);
				}

				return \Datatables::of($talents)->filter(function ($instance) use ($request) {
					if ($request->has('search')) {
						if(!empty($request->search['value'])){
							$instance->collection = $instance->collection->filter(function ($row) use ($request) {
								return (\Str::contains($row->name, $request->search['value']) || \Str::contains($row->first_name, $request->search['value']) || \Str::contains($row->last_name, $request->search['value']) || \Str::contains($row->email, $request->search['value'])) ? true : false;
							});
						} 
					}

				})
				->editColumn('title',function($item){
					$item = json_decode(json_encode($item),true);
					return view('talent.community.member-template',$item)->render();
				})
				->make(true);

			}

			$data['html'] = $htmlBuilder
			->parameters(["dom" => "<'row'<'col-md-3'f> <'col-md-8 filter-option'>> t <'row'<'col-md-12'p> >"])
			->addColumn(['data' => 'title', 'name' => 'member', 'title' => '&nbsp;', 'width' => '0', 'searchable' => false, 'orderable' => false]);

			return view('talent.community.index')->with($data);

		}

		public function add_member(Request $request){

			if($request->ajax()){
				$data['talent_id'] = $request->talent_id;
				$data['user_name'] = $request->user_name;
				
				if($request->page == 'addnote'){
					return view('talent.community.add-note',$data);
				}else{
					return view('talent.community.send-invitation',$data);
				}
			}

		}

		public function invite_to_crowbar(Request $request){
			if($request->ajax()){
				return view('talent.community.invite_to_crowbar');
			}
		}

		public function send_invite_to_crowbar(Request $request){

			$validator = \Validator::make($request->all(), [
				'name'                => 'required',
				'email'               => ['required','email']
			],[
				'name.required'       => trans('general.M0618'),
				'email.required'      => trans('general.M0619'),
				'email.email'         => trans('general.M0011')
			]);

			if($validator->passes()){

				$emailData              = ___email_settings();
				$emailData['email']     = $request->input('email');
				$emailData['name']      = $request->input('name');
	            $emailData['link']      = url('/');

				___mail_sender($request->input('email'),'','invite_to_crowbar',$emailData);

				$this->status   = true;
				$this->message  = sprintf(ALERT_SUCCESS, trans('general.M0620'));
				$this->redirect = url(sprintf("%s/network/members",TALENT_ROLE_TYPE));

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

		public function addmember(Request $request){

			$data = [
					'user_id' 		 =>	\Auth::user()->id_user,
					'member_id' 	 =>	$request->talent_id,
					'note' 			 =>	'',
					'request_status' => 'pending',
					'created' 		 =>	date('Y-m-d H:i:s'),
					'updated' 		 =>	date('Y-m-d H:i:s'),
			];

			$insertedId = \Models\Members::add_member($data);

			if($insertedId){
				$this->status   = true;
				$this->message  = sprintf(ALERT_SUCCESS,trans('website.W0918'));
				$this->redirect = url(sprintf("%s/network/members",TALENT_ROLE_TYPE));
			}else{

				$this->status   = false;
				$this->message  = sprintf(ALERT_SUCCESS,trans('website.W0897'));
				
			}

			return response()->json([
				'data'      => $this->jsondata,
				'status'    => $this->status,
				'message'   => $this->message,
				'redirect'  => $this->redirect,
			]);

		}

		public function addmember_note(Request $request){


			$validator = \Validator::make($request->all(), [
				'note'                => 'required',
			],[
				'note.required'       => trans('website.W0898'),
			]);

			if($validator->passes()){

				$data = [
					'user_id' 		 =>	\Auth::user()->id_user,
					'member_id' 	 =>	$request->talent_id,
					'note' 			 =>	$request->note,
					'request_status' => 'pending',
					'created' 		 =>	date('Y-m-d H:i:s'),
					'updated' 		 =>	date('Y-m-d H:i:s'),
				];

				$insertedId = \Models\Members::add_member($data);

				$this->status   = true;
				$this->message  = sprintf(ALERT_SUCCESS,trans('website.W0918'));
				$this->redirect = url(sprintf("%s/network/members",TALENT_ROLE_TYPE));

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

		public function invite_member(Request $request){

			if($request->ajax()){
				$data['event_id'] = $request->event_id;
				$data['ret_page'] = !empty($request->ret_page) ? $request->ret_page:'';
				return view('talent.community.invite-member',$data);
			}

		}

		public function get_user_emails(Request $request){

			$where = 'status = "active"';
			if(!empty($request->search)){
				$where .= " AND first_name LIKE '%{$request->search}%'";
			}
			$emails = \Models\Talents::get_talent_email('array',$where,['email as id', 'first_name as text']);

            return response()->json([
                'results'    => $emails,
                'pagination' => [
                    "more" => true
                ]
            ]);
		}

		public function post_invite_member(Request $request){
				if($request->invite_from == 'from_circle'){
				$validator = \Validator::make($request->all(), [
					'talent_emails'                => 'required',
				],[
					'talent_emails.required'       => 'Please select names',
				]);
			}else{
				$validator = \Validator::make($request->all(), [
					'outside_emails'                => 'required',
				],[
					'outside_emails.required'       => 'Please enter emails',
				]);

				$validator->after(function($v) use($request){
					if(count($request->outside_names) != count($request->outside_emails)){
						$v->errors()->add('outside_emails','Names and Emails are not equal.');
					}
				});
			}

			if($validator->passes()){

				if($request->invite_from == 'from_circle'){
					$get_rsvp_emails = \Models\Events_rsvp::getEmailsById($request->event_id);
					foreach ($request->input('talent_emails') as $key => $value){

						//Check if this member is already invited
						if(!in_array($value, $get_rsvp_emails)){
							$data1 = [
								'event_id'   => $request->event_id,
								'email'  	 =>	$value,
								'status' 	 => 'no',
								'created_at' => date('Y-m-d H:i:s'),
								'updated_at' => date('Y-m-d H:i:s'),
							];

							$insertId = \Models\Events_rsvp::add_rsvp($data1);
							$code = ___encrypt($insertId);

							$talent_name = \Models\Talents::get_talent_name($value);
							if(!empty($talent_name)){
								$show_talent_name = $talent_name['first_name'];
							}

							$emailData              = ___email_settings();
							$emailData['email']     = $value;
							$emailData['name']      = $show_talent_name;
		                    $emailData['link']      = url(sprintf("accept/event?token=%s",$code));

							___mail_sender($value,'',"accept_event",$emailData);

						}else{
							/*As the email is already invite, get its record and send email only*/
							$getRecordId = \Models\Events_rsvp::getRecordByEmail($value,$request->event_id);

							if(!empty($getRecordId)){
								$code1 = ___encrypt($getRecordId->id);

								$show_talent_name1 = '';
								$talent_name1 = \Models\Talents::get_talent_name($value);
								if(!empty($talent_name1)){
									$show_talent_name1 = $talent_name1['first_name'];
								}

								$emailData              = ___email_settings();
								$emailData['email']     = $value;
								$emailData['name']      = $show_talent_name1;
			                    $emailData['link']      = url(sprintf("accept/event?token=%s",$code1));
								___mail_sender($value,'',"accept_event",$emailData);
							}

						}
					}//end foreach

				}else{

					$get_rsvp_emails = \Models\Events_rsvp::getEmailsById($request->event_id);

					foreach ($request->input('outside_emails') as $key1 => $value1){

						//Check if this email is already invited
						if(!in_array($value1, $get_rsvp_emails)){
							$data1 = [
								'event_id'   => $request->event_id,
								'email'  	 =>	$value1,
								'status' 	 => 'no',
								'created_at' => date('Y-m-d H:i:s'),
								'updated_at' => date('Y-m-d H:i:s'),
							];

							$insertId = \Models\Events_rsvp::add_rsvp($data1);
							$code = ___encrypt($insertId);

							// $show_talent_name = explode('@', $value1)[0];
							$show_talent_name = $request->outside_names[$key1];
							$emailData              = ___email_settings();
							$emailData['email']     = $value1;
							$emailData['name']      = $show_talent_name;
		                    $emailData['link']      = url(sprintf("accept/event?token=%s",$code));
		                    
							___mail_sender($value1,'',"accept_event",$emailData);
						}
					}//end foreach

				}//end else

				$this->status   = true;
				$this->message  = sprintf(ALERT_SUCCESS,trans('website.W0917'));

				if(!empty($request->ret_page) && $request->ret_page == 'home'){
					$this->redirect = url("/network/home");
				}else{
					$this->redirect = url(sprintf("%s/network/events",TALENT_ROLE_TYPE));
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

		public function addRsvp(Request $request){

			//check if full is reached
			$count = \Models\Events_rsvp::getGoingCount($request->event_id);
			$get_max_attendee = \Models\Events::getEventById($request->event_id); 
			$max_attendee = (int)$get_max_attendee['maximum_attendees'];

			if($max_attendee == $count){
				$this->status   = false;
				$this->message  = sprintf(ALERT_SUCCESS,trans('website.W0916'));
			}else{

				$count = \Models\Events_rsvp::updateOrAddRsvp($request->event_id,\Auth::user()->email);

				$this->status   = true;
				$this->message  = sprintf(ALERT_SUCCESS,trans('website.W0915'));

			}

			return response()->json([
				'data'      => $this->jsondata,
				'status'    => $this->status,
				'message'   => $this->message,
				'redirect'  => $this->redirect,
			]);

		}

		public function save_fav_event(Request $request){

			$validator = \Validator::make($request->all(),[
				'event_id' => validation('talent_id')
			],[
				'event_id.integer' => trans("general.M0121")
			]);
		   
			if($validator->passes()){
				$isUpdated          = \Models\Events::save_fav_event(\Auth::user()->id_user,$request->event_id);
								
				$this->status   = true;
				$this->message  = sprintf(ALERT_SUCCESS,trans("website.W0910"));         

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

		/*Talent view another talent's profile*/
		public function view_talent_profile(Request $request, $hashid){

			$data['title']          = 'View Talent';
			$data['subheader']      = 'talent.includes.top-menu';
			$data['header']         = 'innerheader';
			$data['footer']         = 'innerfooter';
			$data['submenu']        = 'talent.community.submenu';
			$data['view']           = 'talent.viewtalent.view_talent_profile';

			$data['user']           = \Models\Talents::view_talent_profile(___decrypt($hashid));

			$data['user']['view_talent_first_name']	= $data['user']['first_name'];		
			$data['user']['view_talent_last_name']	= $data['user']['last_name'];		

			$data['user']['first_name'] = \Auth::user()->first_name;
			unset($data['user']['last_name']);

			return view('talent.viewtalent.index')->with($data);
		}


		public function events(Request $request, Builder $htmlBuilder){

			$data['title']          = trans('website.W0888');
			$data['subheader']      = 'talent.includes.top-menu';
			$data['header']         = 'innerheader';
			$data['footer']         = 'innerfooter';
			$data['submenu']        = 'talent.community.submenu';
			$data['view']           = 'talent.community.events';

			$data['user']           = \Models\Talents::get_user(\Auth::user());         

			if ($request->ajax()) {

				$inCheck =  array();
				$inCheck = $request->input('check');
				$inEvent_date = $request->input('event_date');

				$events = \Models\Events::getEventList($inCheck,$inEvent_date);

				return \Datatables::of($events)->filter(function ($instance) use ($request) {
					if ($request->has('search')) {
						if(!empty($request->search['value'])){
							$instance->collection = $instance->collection->filter(function ($row) use ($request) {
								return (\Str::contains($row->event_title, $request->search['value']) || \Str::contains($row->event_description, $request->search['value'])) ? true : false;
							});
						} 
					}

				})
				->editColumn('event',function($event){
					return \View::make('talent.community.event')->with(compact('event'))->render();
				})
				->make(true);
			}

			$data['html'] = $htmlBuilder
			->parameters(["dom" => "<'row' <'col-md-7 filter-option'><'col-md-3 table-heading' f><'col-md-2 post-event'>> t <'row'<'col-md-12 event-custom-paginatiion'p> >"])
			->addColumn(['data' => 'event', 'name' => 'event', 'title' => '&nbsp;', 'width' => '0', 'searchable' => false, 'orderable' => false]);

			return view('talent.community.index')->with($data);
		}

		public function eventDetail(Request $request,$hashid){

			$data['event'] = \Models\Events::getEventDetailById($hashid);
			return view('talent.community.event_detail')->with($data);

		}

		public function post_event(Request $request){

			$data['title']           = trans('website.W0888');
			$data['subheader']       = 'talent.includes.top-menu';
			$data['header']          = 'innerheader';
			$data['footer']          = 'innerfooter';
			$data['submenu']         = 'talent.community.submenu';
			$data['view']            = 'talent.community.post_event';

			$data['user']            = \Models\Talents::get_user(\Auth::user());
			$data['event_list']      = \Models\Events::getEventListById(\Auth::user()->id_user);  

			$data['company_profile'] = !empty($data['user']['company_profile'])?$data['user']['company_profile']:'individual';

			/*Draft Event*/
			$data['eventDetails'] =  \Models\Events::getDraftEvent();

			return view('talent.community.index')->with($data);
		}

		public function save_post_event(Request $request){
		
			if($request->input('event_type') == "live"){

				$validator = \Validator::make($request->all(), [
					'event_name'                => 'required',
					'event_type'                => 'required',
					'event_attendee'            => 'required|numeric',
					'event_desp'               	=> 'required',
					'emails'               		=> 'required',
					'country'        			=> 'required',
					'state'          			=> 'required',
					'city'           			=> 'required',
					'location'       			=> 'required',

				],[
					'event_name.required'       => trans('website.W0873'),
					'event_type.required'       => trans('website.W0874'),
					'event_attendee.required'   => trans('website.W0875'),
					'event_attendee.numeric'    => trans('website.W0876'),
					'event_desp.required'       => trans('website.W0877'),
					'emails.required'           => trans('website.W0878'),
					'country.required'   	 	=> trans('website.W0879'),
					'state.required'     	 	=> trans('website.W0880'),
					'city.required'      	 	=> trans('website.W0881'),
					'location.required'  	 	=> trans('website.W0882'),
				]); 

			}else{

		    	$validator = \Validator::make($request->all(), [
					'event_name'                => 'required',
					'event_type'                => 'required',
					'event_attendee'            => 'required|numeric',
					'event_desp'               	=> 'required',
					'emails'                    => 'required',
					'video_url'           		=> 'required',
				],[
					'event_name.required'       => trans('website.W0873'),
					'event_type.required'       => trans('website.W0874'),
					'event_attendee.required'   => trans('website.W0875'),
					'event_attendee.numeric'    => trans('website.W0876'),
					'event_desp.required'       => trans('website.W0877'),
					'emails.required'           => trans('website.W0878'),
					'video_url.required' 		=> trans('website.W0883'),
				]);

			}

			$validator->after(function() use($request, $validator)  {

			    if( $request->input('visibility') == "circle" AND $request->input('event_fee') == ""){
			        $validator->errors()->add('event_fee', trans('website.W0884'));
			    }

			    if( $request->input('visibility') == "premium" AND $request->input('event_fee') == ""){
			        $validator->errors()->add('event_fee', trans('website.W0884'));
			    }

			    if($request->input('event_fee') != "" && !is_numeric($request->input('event_fee'))){
			        $validator->errors()->add('event_fee', trans('website.W0885'));
			    }

			    if($request->input('event_type') == "virtual" && $request->input('video_url') != ''){
					$is_url = parse_url($request->input('video_url'));

					if(empty($is_url['host']) ){
		        		$validator->errors()->add('video_url',trans('website.W0913'));
					}elseif($is_url['host'] != 'www.youtube.com'){
		        		$validator->errors()->add('video_url',trans('website.W0914'));
					}else{}
			    }
            
			});

			if($validator->passes()){

				//Delete draft
				$idDelete = \Models\Events::deleteEventDraft();

				$event_date = explode('/',$request->input('event_date'));

				$data = [
					'posted_by' => \Auth::user()->id_user,
					'event_title' => $request->input('event_name'),
					'event_description' => $request->input('event_desp'),
					'event_date' => date('Y-m-d', strtotime($event_date[2].'/'.$event_date[1].'/'.$event_date[0])),
					'event_time' => date('H:i:s', strtotime($request->input('time_hour'))),
					'event_type' => $request->input('event_type'),
					'maximum_attendees' => $request->input('event_attendee'),
					'visibility' => $request->input('visibility'),
					'notify_circle' => ($request->input('agree')!='')? $request->input('agree') :'no',
					'is_free' => ($request->input('visibility')!="public"? "no": "yes"), 
					'entry_fee' => $request->input('event_fee'), 
					'user_type' => $request->input('user_type'),
					'status' => 'active', 
					'created' => date('Y-m-d H:i:s'),
					'updated' => date('Y-m-d H:i:s'),
				];

				if($request->input('event_type') == "live"){

					$data['country'] 	= $request->input('country'); 
					$data['state'] 		= $request->input('state'); 
					$data['city'] 		= $request->input('city'); 
					$data['location'] 	= $request->input('location'); 
					$data['video_url'] 	= ''; 

				}else{

					$data['country']   	= 0; 
					$data['state'] 	  	= 0; 
					$data['city'] 	  	= 0; 
					$data['location']   = 0; 
					$data['video_url']  = $request->input('video_url');

				}

				$event_insertId = \Models\Events::addevent($data);

				if($request->attached_doc_id != 0){
					$events = \DB::table('files')
					 		  ->where('id_file','=',$request->attached_doc_id)
					 		  ->update(['record_id'=>$event_insertId]);
				}

				foreach ($request->input('emails') as $key => $value) {
					
					$data1 = [
						'event_id'   => $event_insertId,
						'email'  	 =>	$value,
						'status' 	 => 'no',
						'created_at' => date('Y-m-d H:i:s'),
						'updated_at' => date('Y-m-d H:i:s'),
					];
					$insertId = \Models\Events_rsvp::add_rsvp($data1);
					$code = ___encrypt($insertId);

					$get_name = explode('@', $value);

					$emailData              = ___email_settings();
					$emailData['email']     = $value;
					$emailData['name']      = ucfirst($get_name[0]);
					$emailData['link']      = url(sprintf("accept/event?token=%s",$code));
					$emailData['eventName']      = $request->input('event_name');

					___mail_sender($value,'',"accept_event",$emailData); 

				}

				if($request->input('agree') == "yes"){
					$incircle = "yes";
					$user_members = \Models\Talents::get_members($incircle);
					$user_members = json_decode(json_encode($user_members),true);
					if(!empty($user_members)){
						foreach ($user_members as $key1 => $value1) {
							$emailData              = ___email_settings();
							$emailData['email']     = $value1['email'];
							$emailData['name']      = $value1['name'];
							$emailData['link']      = '';
							$emailData['eventName']      = $request->input('event_name');

							___mail_sender($value1['email'],'',"invite_talent_to_event",$emailData); 
						}
					}
				}

				$this->status   = true;
				$this->message  = sprintf(ALERT_SUCCESS,trans('website.W0886'));
				$this->redirect = url(sprintf("%s/network/post-event",TALENT_ROLE_TYPE));

			}else{

				$this->jsondata = ___error_sanatizer($validator->errors());
			}

			return response()->json([
				'data'      => $this->jsondata,
				'status'    => $this->status,
				'message'   => $this->message,
				'redirect'  => $this->redirect,
				'nomessage' => false,
			]); 

		}

		public function save_post_event_draft(Request $request){

			if($request->input('event_type') == "live"){

				$validator = \Validator::make($request->all(), [
					'event_name'                => 'required',
					'event_type'                => 'required',
					'event_attendee'            => 'numeric',
					'event_desp'               	=> 'string',
					'emails'               		=> 'array',
					'country'        			=> 'numeric',
					'state'          			=> 'numeric',
					'city'           			=> 'numeric',
					'location'       			=> 'string',

				],[
					'event_attendee.numeric'    => trans('website.W0876'),
					'event_name.required'       => trans('website.W0873'),
					'event_type.required'       => trans('website.W0874'),
				]); 

			}else{

		    	$validator = \Validator::make($request->all(), [
					'event_name'                => 'required',
					'event_type'                => 'required',
					'event_attendee'            => 'numeric',
					'event_desp'               	=> 'string',
					'emails'                    => 'array',
					'video_url'           		=> 'string',
				],[
					'event_name.required'       => trans('website.W0873'),
					'event_type.required'       => trans('website.W0874'),
				]);

			}

			$validator->after(function() use($request, $validator)  {

				if($request->input('event_fee') != "" && !is_numeric($request->input('event_fee'))){
			        $validator->errors()->add('event_fee', trans('website.W0885'));
			    }

			    if($request->input('event_type') == "virtual" && $request->input('video_url') != ''){
					$is_url = parse_url($request->input('video_url'));

					if(empty($is_url['host']) ){
		        		$validator->errors()->add('video_url',trans('website.W0913'));
					}elseif($is_url['host'] != 'www.youtube.com'){
		        		$validator->errors()->add('video_url',trans('website.W0914'));
					}else{}
			    }
			});

			if($validator->passes()){

				//Delete draft
				$idDelete = \Models\Events::deleteEventDraft();

				$data = [
					'posted_by' => \Auth::user()->id_user,
					'event_title' => ($request->input('event_name')!='')? $request->input('event_name'):'',
					'event_description' => ($request->input('event_desp') !='')? $request->input('event_desp'):'',
					'event_time' => date('H:i:s', strtotime($request->input('time_hour'))),
					'event_type' => $request->input('event_type'),
					'maximum_attendees'=>($request->input('event_attendee')!='')?$request->input('event_attendee'):'',
					'visibility' => ($request->input('visibility') != '')?$request->input('visibility'):'',
					'notify_circle' => ($request->input('agree')!='')? $request->input('agree') :'no',
					'is_free' => ($request->input('visibility')!="public"? "no": "yes"), 
					'entry_fee' => ($request->input('event_fee')!='')?$request->input('event_fee'):'',
					'user_type' => !empty($request->input('user_type')) ? $request->input('user_type'):'individual', 
					'status' => 'draft',
					'created' => date('Y-m-d H:i:s'),
					'updated' => date('Y-m-d H:i:s'),
				];

				if(!empty($request->input('event_date'))){
					$event_date = explode('/',$request->input('event_date'));
					if(!empty($event_date)){
						$data['event_date'] = date('Y-m-d',strtotime($event_date[2].'/'.$event_date[1].'/'.$event_date[0]));
					}

				}


				if($request->input('event_type') == "live"){

					$data['country'] 	= ($request->input('country')!='')?		$request->input('country'):''; 
					$data['state'] 		= ($request->input('state')!='')?		$request->input('state'):''; 
					$data['city'] 		= ($request->input('city')!='')?		$request->input('city'):''; 
					$data['location'] 	= ($request->input('location')!='')?	$request->input('location'):''; 
					$data['video_url'] 	= ''; 

				}else{

					$data['country']   	= 0; 
					$data['state'] 	  	= 0; 
					$data['city'] 	  	= 0; 
					$data['location']   = 0; 
					$data['video_url']  = ($request->input('video_url')!='')?$request->input('video_url'):'';

				}

				$event_insertId = \Models\Events::addevent($data);

				if($request->attached_doc_id != 0){
					$events = \DB::table('files')
					 		  ->where('id_file','=',$request->attached_doc_id)
					 		  ->update(['record_id'=>$event_insertId]);
				}

				if(!empty($request->input('emails'))){
					foreach ($request->input('emails') as $key => $value) {
						
						$data1 = [
							'event_id'   => $event_insertId,
							'email'  	 =>	$value,
							'status' 	 => 'no',
							'created_at' => date('Y-m-d H:i:s'),
							'updated_at' => date('Y-m-d H:i:s'),
						];

						$emailData              = ___email_settings();
						$emailData['email']     = $value;
						$emailData['name']      = $value;
						$emailData['link']      = '';

						// ___mail_sender($value,'',"update_email_verification",$emailData);
						$insertId = \Models\Events_rsvp::add_rsvp($data1);

					}
				}

				$this->status   = true;
				$this->message  = sprintf(ALERT_SUCCESS,'Event draft saved successfully');
				$this->redirect = url(sprintf("%s/network/post-event",TALENT_ROLE_TYPE));

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

		public function save_event_file(Request $request){

			$this->fileReturnId = 0;

			$validator = \Validator::make($request->all(), [
                "file"                      => ['required','validate_image_type'],
            ],[
                'file.validate_image_type'   => trans('general.M0120'),
            ]);
            

            if($validator->passes()){
                $folder = 'uploads/events/';
                $uploaded_file = upload_file($request,'file',$folder,true);
                $data = [
                    'user_id' => \Auth::user()->id_user,
                    'record_id' => "",
                    'reference' => 'users',
                    'filename' => $uploaded_file['filename'],
                    'extension' => $uploaded_file['extension'],
                    'folder' => $folder,
                    'type' => 'event',
                    'size' => $uploaded_file['size'],
                    'is_default' => DEFAULT_NO_VALUE,
                    'created' => date('Y-m-d H:i:s'),
                    'updated' => date('Y-m-d H:i:s'),
                ];

                $isInserted = \Models\Talents::create_file($data,true,true);

                if(!empty($isInserted)){
                    if(!empty($isInserted['folder'])){
                        $isInserted['file_url'] = url(sprintf("%s/%s",$isInserted['folder'],$isInserted['filename']));
                    }
                    
                    $url_delete = sprintf(
                        url('%s/delete_event_file?id_file=%s'),
                        TALENT_ROLE_TYPE,
                        $isInserted['id_file']
                    );

                    $this->jsondata = sprintf(RESUME_TEMPLATE,
						$isInserted['id_file'],
						url(sprintf('/download/file?file_id=%s',___encrypt($isInserted['id_file']))),
						asset('/'),
						$uploaded_file['filename'],
						$uploaded_file['size'],
						$url_delete,
						$isInserted['id_file'],
						asset('/')
					);
                    
                    $this->status = true;
                    $this->message  = sprintf(ALERT_SUCCESS,trans("general.M0110"));
                    $this->fileReturnId  = $isInserted['id_file'];
                }
            }else{

            	$this->status = false;
                $this->jsondata = ___error_sanatizer($validator->errors());
            }

            return response()->json([
                'data'      	=> $this->jsondata,
                'status'    	=> $this->status,
                'message'   	=> $this->message,
                'redirect'  	=> $this->redirect,
                'fileReturnId'	=> $this->fileReturnId
            ]);
		}

		public function delete_event_file(Request $request){

			$isDeleted = \Models\Talents::delete_file(sprintf(" id_file = %s AND user_id = %s ",$request->id_file, $request->user()->id_user));

            if($isDeleted){
                $this->status = true;
                $this->message  = sprintf(ALERT_SUCCESS,trans("general.M0110"));
            }
            return response()->json([
                'status'    => $this->status,
                'message'   => $this->message
            ]); 

		}

		public function delete_event(Request $request){

			$isDeleted = \Models\Events::updateEventStatusById($request->id_events);

			if($isDeleted){
                $this->status = true;
                $this->message  = sprintf(ALERT_SUCCESS,trans("general.M0110"));
            }
            return response()->json([
                'status'    => $this->status,
                'message'   => $this->message
            ]);

		}

		public function edit_event(Request $request){

			$data['title']     = trans('website.W0888');
			$data['subheader'] = 'talent.includes.top-menu';
			$data['header']    = 'innerheader';
			$data['footer']    = 'innerfooter';
			$data['submenu']   = 'talent.community.submenu';
			$data['view']      = 'talent.community.edit_post_event';

			$data['user']       	= \Models\Talents::get_user(\Auth::user());
			$data['eventDetails'] 	= \Models\Events::getEventById(___decrypt($request->id_events));

			return view('talent.community.index')->with($data);

		}

		public function update_event(Request $request,$hashid){


			if($request->input('event_type') == "live"){

				$validator = \Validator::make($request->all(), [
					'event_name'                => 'required',
					'event_type'                => 'required',
					'event_attendee'            => 'required|numeric',
					'event_desp'               	=> 'required',
					'emails'               		=> 'required',
					'country'        			=> 'required',
					'state'          			=> 'required',
					'city'           			=> 'required',
					'location'       			=> 'required',

				],[
					'event_name.required'       => trans('website.W0873'),
					'event_type.required'       => trans('website.W0874'),
					'event_attendee.required'   => trans('website.W0875'),
					'event_attendee.numeric'    => trans('website.W0876'),
					'event_desp.required'       => trans('website.W0877'),
					'emails.required'           => trans('website.W0878'),
					'country.required'   	 	=> trans('website.W0879'),
					'state.required'     	 	=> trans('website.W0880'),
					'city.required'      	 	=> trans('website.W0881'),
					'location.required'  	 	=> trans('website.W0882'),
				]); 

			}else{

		    	$validator = \Validator::make($request->all(), [
					'event_name'                => 'required',
					'event_type'                => 'required',
					'event_attendee'            => 'required|numeric',
					'event_desp'               	=> 'required',
					'emails'                    => 'required',
					'video_url'           		=> 'required',
				],[
					'event_name.required'       => trans('website.W0873'),
					'event_type.required'       => trans('website.W0874'),
					'event_attendee.required'   => trans('website.W0875'),
					'event_attendee.numeric'    => trans('website.W0876'),
					'event_desp.required'       => trans('website.W0877'),
					'emails.required'           => trans('website.W0878'),
					'video_url.required' 		=> trans('website.W0883'),
				]);

			}

			$validator->after(function() use($request, $validator)  {

			    if( $request->input('visibility') == "circle" AND $request->input('event_fee') == ""){
			        $validator->errors()->add('event_fee', trans('website.W0884'));
			    }

			    if( $request->input('visibility') == "premium" AND $request->input('event_fee') == ""){
			        $validator->errors()->add('event_fee', trans('website.W0884'));
			    }

			    if($request->input('event_fee') != "" && !is_numeric($request->input('event_fee'))){
			        $validator->errors()->add('event_fee', trans('website.W0885'));
			    }

			    if($request->input('event_type') == "virtual" && $request->input('video_url') != ''){
					$is_url = parse_url($request->input('video_url'));

					if(empty($is_url['host']) ){
		        		$validator->errors()->add('video_url',trans('website.W0913'));
					}elseif($is_url['host'] != 'www.youtube.com'){
		        		$validator->errors()->add('video_url',trans('website.W0914'));
					}else{}
			    }
            
			});

			if($validator->passes()){

				$event_date = explode('/',$request->input('event_date'));

				$data = [
					'event_title' => $request->input('event_name'),
					'event_description' => $request->input('event_desp'),
					'event_date' => date('Y-m-d', strtotime($event_date[2].'/'.$event_date[1].'/'.$event_date[0])),
					'event_time' => date('H:i:s', strtotime($request->input('time_hour'))),
					'event_type' => $request->input('event_type'),
					'maximum_attendees' => $request->input('event_attendee'),
					'visibility' => $request->input('visibility'),
					'notify_circle' => ($request->input('agree')!='')? $request->input('agree') :'no',
					'is_free' => ($request->input('visibility')!="public"? "no": "yes"), 
					'entry_fee' => $request->input('event_fee'), 
					'created' => date('Y-m-d H:i:s'),
					'updated' => date('Y-m-d H:i:s'),
				];

				if($request->input('event_type') == "live"){

					$data['country'] 	= $request->input('country'); 
					$data['state'] 		= $request->input('state'); 
					$data['city'] 		= $request->input('city'); 
					$data['location'] 	= $request->input('location'); 
					$data['video_url'] 	= ''; 

				}else{

					$data['country']   	= 0; 
					$data['state'] 	  	= 0; 
					$data['city'] 	  	= 0; 
					$data['location']   = 0; 
					$data['video_url']  = $request->input('video_url');

				}

				$event_insertId = \Models\Events::updateEventById(___decrypt($hashid),$data);

				if($request->attached_doc_id != 0){

					//delete old file
					\Models\File::delete_file(___decrypt($hashid));

					//Update for new file
					$events = \DB::table('files')
					 		  ->where('id_file','=',$request->attached_doc_id)
					 		  ->update(['record_id'=>___decrypt($hashid)]);
				}

				$get_rsvp_emails = \Models\Events_rsvp::getEmailsById(___decrypt($hashid));

				foreach ($request->input('emails') as $key => $value) {

					if(!in_array($value, $get_rsvp_emails)){
						$data1 = [
							'event_id'   => ___decrypt($hashid),
							'email'  	 =>	$value,
							'status' 	 => 'no',
							'created_at' => date('Y-m-d H:i:s'),
							'updated_at' => date('Y-m-d H:i:s'),
						];
						$insertId = \Models\Events_rsvp::add_rsvp($data1);
						$code = ___encrypt($insertId);

						$emailData              = ___email_settings();
						$emailData['email']     = $value;
						$emailData['name']      = $value;
						$emailData['link']      = url(sprintf("accept/event?token=%s",$code));

						___mail_sender($value,'',"accept_event",$emailData);

					}
				}

				// if($request->input('agree') == "yes"){
				// 	$incircle = "yes";
				// 	$user_members = \Models\Talents::get_members($incircle);
				// 	$user_members = json_decode(json_encode($user_members),true);
				// 	if(!empty($user_members)){
				// 		foreach ($user_members as $key1 => $value1) {
				// 			$emailData              = ___email_settings();
				// 			$emailData['email']     = $value1['email'];
				// 			$emailData['name']      = $value1['name'];
				// 			$emailData['link']      = '';

				// 			___mail_sender($value1['email'],'',"update_email_verification",$emailData);
				// 		}
				// 	}
				// }

				$this->status   = true;
				$this->message  = sprintf(ALERT_SUCCESS,trans('website.W0912'));
				$this->redirect = url(sprintf("%s/network/post-event",TALENT_ROLE_TYPE));

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

		/**
		 * [This method is used for document Curriculum Vitae ]
		 * @param  Request
		 * @return Json Response
		 */
		
		public function viewTalentConnect(){
			$data['title']     		= trans('website.W0692');
			$data['subheader'] 		= 'talent.includes.top-menu';
			$data['header']    		= 'innerheader';
			$data['footer']    		= 'innerfooter';
			$data['submenu']   		= 'talent.community.submenu';
			$data['view']      		= 'talent.talent-connect.add';
			
			$data['user']       	= \Models\Talents::get_user(\Auth::user());
			$data['invited_user'] 	= \Models\connectedTalent::where('is_email_sent','0')->where('is_connected','!=','1')->where('send_by',\Auth::user()->id_user)->get();

			$data['isOwner'] 		= \Models\companyConnectedTalent::with(['user'])->where('id_user',\Auth::user()->id_user)->where('user_type','owner')->get()->first();
			$data['isOwner'] 		= json_decode(json_encode($data['isOwner']));

			if(!empty($data['isOwner'])){
				$data['connected_user'] = \Models\companyConnectedTalent::with(['user','company'])
											->where('id_talent_company',$data['isOwner']->id_talent_company)
											->where('user_type','user')
											->get();
				foreach ($data['connected_user'] as $key => &$value) {
					// dd($value->user->id_user);
					$value->user->profile_url = get_file_url(\Models\companyConnectedTalent::get_file(sprintf(" type = 'profile' AND user_id = %s",$value->user->id_user),'single',['filename','folder']));
					
				}
				$data['connected_user'] = json_decode(json_encode($data['connected_user']));
			}else{
				
				$data['connected_user'] = [];
			}
			
			return view('talent.talent-connect.index')->with($data);
		}


		

		/**
		 * [This method is used for storing invited talents data ]
		 * @param  Request
		 * @return Json Response
		 */
		public function storeTalentConnect(Request $request){
			$validator = \Validator::make($request->all(), [
				"name" 				=> validation('name'),
				"email"            	=> validation('email'),
			],[]);

			$validator->after(function ($validator) use($request) {
				$user_id 		= \Models\Users::select('id_user')->where('email',$request->email)->first();
				$ownerDetails 	= \Models\companyConnectedTalent::where('id_user',\Auth::User()->id_user)->first();
				$isAlreadyExist = \Models\companyConnectedTalent::where('id_user',$user_id['id_user'])->where('id_talent_company',$ownerDetails->id_talent_company)->first();

				if(count($isAlreadyExist) > 0){
					$validator->errors()->add('email', trans('general.M0639'));
				}


				$isInvited = \Models\connectedTalent::where('send_to_email',$request->email)->where('is_email_sent','0')->first();
				if(count($isInvited) > 0){
					$validator->errors()->add('email', trans('general.M0640'));
				}

			});

			if($validator->passes()){

				$isAlreadyExist = \Models\connectedTalent::where('send_to_email',$request->email)->where('send_by',\Auth::user()->id_user)->first();
				$data = [
					'send_by' 			=> \Auth::user()->id_user,
					'send_to_name' 		=> $request->name,
					'send_to_email' 	=> $request->email,
					'created_at' 		=> date('Y-m-d H:i:s'),
					'updated_at' 		=> date('Y-m-d H:i:s'),
				];
				if(count($isAlreadyExist) == 0){
					$isInserted = \Models\connectedTalent::create($data);
				}else{
					$isInserted = \Models\connectedTalent::where('send_to_email',$request->email)->where('send_by',\Auth::user()->id_user)->update(['is_email_sent'=>'0']);
					$isInserted = $isAlreadyExist;
				}

				if($isInserted){
					$this->status 		= true;
					$this->jsondata 	= $isInserted;
					$this->message  	= sprintf(ALERT_SUCCESS,trans("general.94208"));
				}else{
					$this->status 		= false;
					$this->message  	= sprintf(ALERT_SUCCESS,trans("general.94209"));
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

		public function sendInviteToTalent(Request $request){
			if(!isset($request->send_to)){
				$this->status   = true;
				$this->message  = sprintf(ALERT_SUCCESS,trans("general.M0641"));
			}else{
				$talent_ids = ($request->send_to);		

				$talent_Data = \Models\connectedTalent::whereIn('id_connect',$talent_ids)->get();
				$talent_Data = json_decode(json_encode($talent_Data),true);


				foreach ($talent_Data as $key => $value) {
					$inviteCode = _get_couponCode('6');
					$emailData              		= ___email_settings();
					$emailData['email']     		= $value['send_to_email'];
					$emailData['name']      		= $value['send_to_name'];
					$emailData['link']      		= url('/');
					$emailData['invited_by']      	= \Auth::user()->name;
					$emailData['invited_code']     	= $inviteCode;


					$isUpdated = \Models\connectedTalent::where('id_connect',$value['id_connect'])->where('send_by',\Auth::User()->id_user)->update(['is_email_sent'=>'1','invite_code'=>$inviteCode]);
					___mail_sender($value['send_to_email'],$value['send_to_name'],"invite_talent",$emailData);
				}
				$this->status = true;
				$this->message = 'Inivitation sent successfully';
				$this->redirect = 'talent-connect';
			}
			return response()->json([
				'data'      => $this->jsondata,
				'status'    => $this->status,
				'message'   => $this->message,
				'redirect'  => $this->redirect,
			]);

		}

		public function removeTalentConnect(Request $request,$talent_id){

			if($talent_id){
				$isDeleted = \Models\connectedTalent::where('id_connect',$talent_id)->delete();
			}
			if($isDeleted){
				$this->status 		= true;
				$this->jsondata 	= '';
				$this->message  	= sprintf(ALERT_SUCCESS,trans("general.94208"));
			}else{
				$this->status 		= false;
				$this->message  	= sprintf(ALERT_SUCCESS,trans("general.94209"));
			}
			return response()->json([
				'data'      => $this->jsondata,
				'status'    => $this->status,
				'message'   => $this->message,
				'redirect'  => $this->redirect,
			]);

		}

		public function validateTalentConnect(Request $request){
			$company_profile = \Auth::user()->company_profile;

			if($company_profile == 'individual'){
				$this->status 		= true;
				$this->jsondata 	= '';
				$this->message  	= sprintf(ALERT_SUCCESS,trans("general.M0644"));
				$this->redirect 	= url('talent/profile/edit/step/one');
			}
			return response()->json([
				'data'      => $this->jsondata,
				'status'    => $this->status,
				'message'   => $this->message,
				'redirect'  => $this->redirect,
			]);

		}


		public function connectWithTalent(Request $request){
			$data['title']                  = trans('website.W0988');
			$data['subheader']              = 'talent.includes.top-menu';
			$data['header']                 = 'innerheader';
			$data['footer']                 = 'innerfooter';
			$data['view']                   = 'talent.talent-connect.connect';

			$data['user']                   = \Models\Talents::get_user(\Auth::user());
			$data['settings']               = \Models\Settings::fetch(\Auth::user()->id_user,\Auth::user()->type);
			$data['industries_name']        = \Cache::get('industries_name');
			$data['subindustries_name']     = \Cache::get('subindustries_name');
			
			return view('talent.settings.index')->with($data);
		}	

		public function connectTalentByInviteCode(Request $request){
			$validator = \Validator::make($request->all(), [
				"invite_code" => validation('invite_code'),
			],[]);

			$validator->after(function ($validator) use($request) {
				// $projectDetail = \Models\Proposals::join('projects','projects.id_project','talent_proposals.project_id')
				// 									->where('talent_proposals.user_id',\Auth::User()->id_user)
				// 									->where('projects.project_status','initiated')
				// 									->count();
				
				// if($projectDetail > 0){
				// 	$validator->errors()->add('invite_code', trans('general.M0658'));
				// }

				$isInviteCode = \Models\connectedTalent::where('invite_code',$request->invite_code)->where('send_to_email',\Auth::User()->email)->first();
				if($request->invite_code != ''){
					if(($isInviteCode)== null){
						$validator->errors()->add('invite_code', trans('general.M0645'));
					}
				}
				$already_connected = \Models\companyConnectedTalent::where('id_user',\Auth::User()->id_user)->first();
				/*if(count($already_connected) > 0){
					$validator->errors()->add('invite_code', trans('general.M0650'));
				}*/
			});

			if($validator->passes()){

				$notice_period_days = \Cache::get('configuration')['notice_period'];

				$curr_date 					= date('Y-m-d');
				$notice_period_end_date 	= \Auth::User()->notice_expired;
				$is_notice_period			= \Auth::User()->is_notice_period;
				
				$already_connected = \Models\companyConnectedTalent::where('id_user',\Auth::User()->id_user)->first();

				if($already_connected == null ){
					$isInviteCode 	= \Models\connectedTalent::where('invite_code',$request->invite_code)->first();

					$companyOwnerId = $isInviteCode->send_by;
					$companyDetail 	= \Models\companyConnectedTalent::where('id_user',$companyOwnerId)->where('user_type','owner')->first();

					$talentData 	= [
						'id_talent_company' => $companyDetail->id_talent_company,
						'id_user'			=> \Auth::user()->id_user,
						'user_type'			=> 'user',
						'created'			=> date('Y-m-d H:i:s'),
						'updated'			=> date('Y-m-d H:i:s'),
					];

					// \Models\companyConnectedTalent::where('id_user',\Auth::user()->id_user)->delete();
					$isInserted = \Models\companyConnectedTalent::insert($talentData);
					
					\Models\connectedTalent::where('invite_code',$request->invite_code)->update(['invite_code' => null]);

					if($isInserted){
						$this->status = true;
						$this->message = 'You are connected successfully.';
						$this->redirect = 'profile/view';
					}
			 	}else{
			 		if($is_notice_period == 'N' ){
						$this->status 		= true;
						$this->jsondata 	= '';
						$this->message  	= sprintf(ALERT_SUCCESS,trans("general.M0661"));
						$this->redirect 	= url('talent/send-unlink-application');
					} else if ($is_notice_period == 'Y' && $notice_period_end_date > $curr_date){

						$this->status 		= true;
						$this->jsondata 	= '';
						$this->message  	= sprintf(ALERT_SUCCESS,trans("general.M0673"));
						$this->redirect 	= url('/');

					} else if($is_notice_period == 'Y' && $notice_period_end_date < $curr_date){
						$isInviteCode 	= \Models\connectedTalent::where('invite_code',$request->invite_code)->first();

						$companyOwnerId = $isInviteCode->send_by;
						$companyDetail 	= \Models\companyConnectedTalent::where('id_user',$companyOwnerId)->where('user_type','owner')->first();

						$talentData 	= [
							'id_talent_company' => $companyDetail->id_talent_company,
							'id_user'			=> \Auth::user()->id_user,
							'user_type'			=> 'user',
							'created'			=> date('Y-m-d H:i:s'),
							'updated'			=> date('Y-m-d H:i:s'),
						];

						\Models\companyConnectedTalent::where('id_user',\Auth::user()->id_user)->delete();

						$isInserted = \Models\companyConnectedTalent::insert($talentData);
						
						\Models\connectedTalent::where('invite_code',$request->invite_code)->update(['invite_code' => null]);

						if($isInserted){
							$this->status = true;
							$this->message = 'You are connected successfully.';
							$this->redirect = 'profile/view';
						}
					}
			 	}
						
			} else{
				$this->jsondata = ___error_sanatizer($validator->errors());
			}
			return response()->json([
				'data'      => $this->jsondata,
				'status'    => $this->status,
				'message'   => $this->message,
				'redirect'  => $this->redirect,
			]);
		}	

		public function unlinkConnectedTalent(Request $request)
		{
			$validator = \Validator::make($request->all(), [],[]);

			$validator->after(function ($validator) use($request) {

				$projectDetail = \Models\Proposals::join('projects','projects.id_project','talent_proposals.project_id')
													->where('talent_proposals.user_id',$request->user_id)
													->where('projects.project_status','initiated')
													->count();
				
				if($projectDetail > 0){
					$validator->errors()->add('user_id', trans('general.M0657'));
				}
			});
			if($validator->passes()){
				$user_id = $request->user_id;
				$response = \Models\companyConnectedTalent::where('id_user',$user_id)->delete();
				if($response){
					$this->status 	= true;
					$this->message 	= 'Talent Unlink successfully.';
					$this->redirect = 'talent-connect';
				}
			} else{
				$this->status 		= true;
				$this->jsondata 	= '';
				$this->message  	= sprintf(ALERT_SUCCESS,trans("general.M0657"));
				$this->redirect 	= url('talent/talent-connect');
			}
			return response()->json([
				'data'      => $this->jsondata,
				'status'    => $this->status,
				'message'   => $this->message,
				'redirect'  => $this->redirect,
			]);

		}	

		public function unlinkFromFirm(Request $request){
			$data['title']                  = 'Send Unlink Request';
			$data['subheader']              = 'talent.includes.top-menu';
			$data['header']                 = 'innerheader';
			$data['footer']                 = 'innerfooter';
			$data['view']                   = 'talent.talent-connect.disconnect-talent';

			$data['user']                   = \Models\Talents::get_user(\Auth::user());
			$data['settings']               = \Models\Settings::fetch(\Auth::user()->id_user,\Auth::user()->type);
			$data['industries_name']        = \Cache::get('industries_name');
			$data['subindustries_name']     = \Cache::get('subindustries_name');
			
			return view('talent.settings.index')->with($data);
		}

		public function sendUnlinkRequest(Request $request)
		{
			$validator = \Validator::make($request->all(), [
				'content' => 'required'
			],[]);
			
			if($validator->passes()){
				$content 			= $request->content;
				$notice_period_days = \Cache::get('configuration')['notice_period'];

				$updateArr = [
					'is_notice_period' => 'Y',
					'notice_expired' => date('Y-m-d', strtotime(date('Y-m-d'). ' + '.$notice_period_days.' days'))
				];

				$companyDetail = \Models\companyConnectedTalent::with('company')->where('id_user',\Auth::User()->id_user)->where('user_type','user')->first();

				$companyOwner = \Models\companyConnectedTalent::with('user')->where('id_talent_company',$companyDetail->id_talent_company)->where('user_type','owner')->first();

				$isUpdated = \Models\Talents::change(\Auth::User()->id_user,$updateArr);
					$emailData = [];
				if($isUpdated){
					$emailData              				= ___email_settings();
					$emailData['email']     				= $companyOwner->user->email;
					$emailData['main_talent_name']  		= $companyOwner->user->name;
					$emailData['connected_talent_name']  	= \Auth::User()->name;
					$emailData['content']  					= $content;
					$emailData['company_name']  			= $companyDetail->company->company_name;
					$emailData['notice_period']  			= $notice_period_days;

					$mail = ___mail_sender($companyOwner->user->email,$companyOwner->user->name,"send_unlink_request",$emailData);

					$mail = ___mail_sender(\Auth::User()->email,$companyOwner->user->name,"unlink_request",$emailData);

					$this->status 		= true;
					$this->jsondata 	= '';
					$this->message  	= sprintf(ALERT_SUCCESS,trans("general.M0251"));
					$this->redirect 	= url('talent/connect-with-talent');
				
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

		public function disconnectedTalentJobs(Request $request, Builder $htmlBuilder){
			$data['title']     		= trans('website.W0996');
			$data['subheader'] 		= 'talent.includes.top-menu';
			$data['header']    		= 'innerheader';
			$data['footer']    		= 'innerfooter';
			$data['submenu']   		= 'talent.community.submenu';
			$data['view']      		= 'talent.talent-connect.job-list';
			$data['backUrl']      	= 'talent/talent-connect';
			$data['user']           = \Models\Talents::get_user(\Auth::user());
			$data['talent_id']		= ___decrypt($request->talent_id);

			$request->request->add(['currency' => \Session::get('site_currency')]);

			if ($request->ajax()) {
				$jobs = \Models\Proposals::with(['project','talent'])
											->whereHas('talent',function($q) use ($data){
												$q->where('id_user',$data['talent_id']);
											})
											->whereHas('project',function($q) use ($data){
												$q->whereIn('project_status',['pending','initiated'])->whereNotIn('status',['trashed']);
											})
											->whereIn('status',['accepted','applied'])
											
											->get();

				return \Datatables::of($jobs)
									->editColumn('title',function($jobs){
										return talent_job_list($jobs);
									})
									->make(true);
			}

			$data['html'] = $htmlBuilder
			->addColumn(['data' => 'title', 'name' => 'title', 'title' => '&nbsp;', 'width' => '0', 'searchable' => false, 'orderable' => false]);
			
			return view('talent.talent-connect.index')->with($data);
		}


		public function connectedTalentList(Request $request, Builder $htmlBuilder){

			$data['title']     		= 'Connected Talent';
			$data['subheader'] 		= 'talent.includes.top-menu';
			$data['header']    		= 'innerheader';
			$data['footer']    		= 'innerfooter';
			$data['submenu']   		= 'talent.community.submenu';
			$data['view']      		= 'talent.talent-connect.job-list';
			$data['backUrl']      	= 'talent/talent-connect';
			$data['user']           = \Models\Talents::get_user(\Auth::user());
			$data['job_id']			= $job_id = ___decrypt($request->job_id);

			$request->request->add(['currency' => \Session::get('site_currency')]);

			if ($request->ajax()) {
				$talentList = \Models\companyConnectedTalent::with(['user'])
											->whereHas('user',function($q) use ($data){
												$q->where('is_notice_period','N');
											})
											->where('id_talent_company',$data['user']['talentCompany'][0]->id_talent_company)
											->where('user_type','user')
											->get();
				foreach ($talentList as $key => $value) {
					$industry = \Models\Talents::industry($value->user->id_user);
					$value->{'industry'} = $industry;
				}

				return \Datatables::of($talentList)
									->editColumn('title',function($talentList) use($job_id){
										return connected_talent_list($talentList,$job_id);
									})
									->make(true);
			}

			$data['html'] = $htmlBuilder
			->parameters([
				"dom" => "<'row' <'col-md-6 table-heading'> <'col-md-3'> <'col-md-3 filter-option'>> rt <'row'<'col-md-6'i><'col-md-6'p> >",
				"language" => [
					"sInfo" => "Showing _START_ - _END_ out of _TOTAL_ record(s)",
					"sInfoEmpty"=> "No record(s) found. ",
				]
			])
			->addColumn(['data' => 'title', 'name' => 'title', 'title' => '&nbsp;', 'width' => '0', 'searchable' => false, 'orderable' => false]);
			
			return view('talent.talent-connect.index')->with($data);
		}

		public function transferJob(Request $request)
		{
			
			$job_id 		= ___decrypt($request->job_id);
			$talent_id 		= ___decrypt($request->talent_id);

			$data['talent']			= \Models\Talents::get_user((object)['id_user' => $talent_id]);
			$data['job_detail']		= \Models\Projects::where('id_project',$job_id)->first();

			$oldTalent = \Models\Proposals::where('project_id',$job_id)->first();

			$isUpdated = \Models\Proposals::where('project_id',$job_id)->update(['user_id' => $talent_id] );

			if($isUpdated){
				$emailData              				= ___email_settings();
				$emailData['email']     				= $data['talent']['email'];
				$emailData['main_talent_name']  		= \Auth::User()->name;
				$emailData['talent_name']  				= $data['talent']['first_name'].' '.$data['talent']['last_name'];
				$emailData['job_name']  				= $data['job_detail']['title'];

				$mail = ___mail_sender($data['talent']['email'],$data['talent']['first_name'],"transfer_job_to",$emailData);
				
				// $mail = ___mail_sender(\Auth::User()->email,$data['talent']['name'],"transfer_job_to",$emailData);

				$this->status 		= true;
				$this->jsondata 	= '';
				$this->message  	= 'Job Transfer successfully.';
				$this->redirect 	= url('talent/disconnected-job-list/'.___encrypt($oldTalent['user_id']));

			}

			return response()->json([
				'data'      => $this->jsondata,
				'status'    => $this->status,
				'message'   => $this->message,
				'redirect'  => $this->redirect,
			]);
		}
	}