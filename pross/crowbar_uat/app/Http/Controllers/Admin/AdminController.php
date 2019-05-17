<?php 
	namespace App\Http\Controllers\Admin;

	use App\Http\Requests;
	use Illuminate\Support\Facades\DB;
	use App\Http\Controllers\Controller;
	
	use Illuminate\Support\Facades\Cookie;
	use Illuminate\Validation\Rule;
	use Illuminate\Http\Request;
	use Yajra\Datatables\Html\Builder;
	use App\Models\Interview as Interview;
	use App\Models\Forum;
	use Auth;
	use Crypt;
	use Illuminate\Pagination\Paginator;
	use Symfony\Component\HttpFoundation\StreamedResponse;

	use Voucherify\VoucherifyClient;
	use Voucherify\ClientException;

	use Illuminate\Support\Facades\Input;
	use Maatwebsite\Excel\Facades\Excel;

	class AdminController extends Controller {

		private $URI_PLACEHOLDER;

		private $jsondata;
		private $redirect;
		private $message;
		private $status;
		private $prefix;

		public function __construct(){
			$this->jsondata     = [];
			$this->message      = false;
			$this->redirect     = false;
			$this->status       = false;
			$this->prefix       = \DB::getTablePrefix();
			$this->URI_PLACEHOLDER = \Config::get('constants.URI_PLACEHOLDER');
		}

		/**
         * [This method is used for randering view of dashboard] 
         * @param  null
         * @return \Illuminate\Http\Response
         */
        
		public function index() {

			$data['page_title'] = 'Dashboard';
			$data['data'] 		= \Models\Administrator::dashboard(\Auth::guard('admin')->user()->type);
			return view(sprintf("%s.%s","backend","dashboard"))->with($data);
		}

		/*Complete Account After created from admin*/

		/**
         * [This method is used for account creation] 
         * @param  Request
         * @return \Illuminate\Http\Response
         */
        
		public function completeAccount(Request $request){
			$data['header']         = 'innerheader';
			$data['footer']         = 'innerfooter';
			$data['token']          = '';
			$data['message']        = '';

			if(!empty($request->token)){
				$data['token']      = $request->token;
				$result = \Models\Administrator::findByToken($request->token,['id_user']);
			}

			if(!empty($result)){
				$data['link_status']    = 'valid';
			}else{
				$data['link_status']    = 'expired';
				$data['message']        = trans('website.W0002');
			}

			return view(sprintf("%s.%s","backend","create-password"))->with($data);
		}

		/**
         * [This method is used for password creation] 
         * @param Request
         * @return \Illuminate\Http\Response
         */
        
		public function createPassword(Request $request){
			$validator = \Validator::make($request->all(), [
				'password'                  => validation('password')
			],[
				'password.required'         => trans('general.M0013'),
				'password.regex'            => trans('general.M0014'),
				'password.string'           => trans('general.M0013'),
				'password.min'              => trans('general.M0014'),
				'password.max'              => trans('general.M0018')
			]);

			if ($validator->passes()) {
				if(!empty($request->token)){
					$result = \Models\Administrator::findByToken($request->token,['id_user']);

					if(!empty($result)){
						$isUpdated = \Models\Users::change($result['id_user'],['password' => bcrypt($request->password),'is_email_verified' => 'yes','status' => 'active','remember_token' => bcrypt(__random_string()) ,'updated' => date('Y-m-d H:i:s')]);

						if(!empty($isUpdated)){
							$request->session()->flash('alert',sprintf(ALERT_SUCCESS,trans("website.W0003")));
							//return redirect()->back()->with(['success' => true]);
							return redirect()->intended(sprintf('/%s/%s',ADMIN_FOLDER,'login'));
						}
					}
					$request->session()->flash('alert',sprintf(ALERT_DANGER,trans("website.W0002")));
				}
				$request->session()->flash('alert',sprintf(ALERT_DANGER,trans("website.W0002")));
			}

			return redirect()->back()->withErrors($validator)->withInput();
		}

        public function forgotpassword(Request $request){
            $data['header']         = 'innerheader';
            $data['footer']         = 'innerfooter';

            return view(sprintf('backend.forgot'))->with($data);       
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
                $result = \Models\Administrator::findByEmail($request->{LOGIN_EMAIL},['id_user','type','email','first_name','last_name','status']);
                if(!empty($result)){
                    $code                   = bcrypt(__random_string());
                    $forgot_otp             = strtoupper(__random_string(6));

                    $isUpdated = \Models\Administrator::change($result['id_user'],[
                    	'remember_token'    => $code,
                        'forgot_otp'        => $forgot_otp,
                        'updated'           => date('Y-m-d H:i:s')
                    ]);

                    if(!empty($isUpdated)){
                        $emailData              = ___email_settings();
                        $emailData['email']     = $result['email'];
                        $emailData['name']      = $result['first_name'];
                        $emailData['link']      = url(sprintf("administrator/reset-password?token=%s",$code));
                        
                        ___mail_sender($result['email'],sprintf("%s %s",$result['first_name'],$result['last_name']),"admin_forgot_password",$emailData);

                        return redirect('administrator/login');
                    }else{
                        $request->session()->flash('alert',sprintf(ALERT_SUCCESS,trans("general.M0029")));
                    }
                }else{
                    $request->session()->flash('alert',sprintf(ALERT_DANGER,trans("general.M0028")));
                }
            }

            return redirect()->back()->withErrors($validator)->withInput();
        }

        public function resetpassword(Request $request){
            $data['header']         = 'innerheader';
            $data['footer']         = 'innerfooter';

            if(!empty($request->token)){
                $data['token']      = $request->token;
                $result = \Models\Administrator::findByToken($request->token,['id_user']);
            }

            if(!empty($result)){
                $data['link_status']    = 'valid';
            }else{
                $data['link_status']    = 'expired';
                $data['message']        = trans('website.W0002');
            }

            return view(sprintf('backend.reset-password'))->with($data);       
        }

		public function _resetPassword(Request $request){
			$validator = \Validator::make($request->all(), [
				'password'                  => validation('password')
			],[
				'password.required'         => trans('general.M0013'),
				'password.regex'            => trans('general.M0014'),
				'password.string'           => trans('general.M0013'),
				'password.min'              => trans('general.M0014'),
				'password.max'              => trans('general.M0018')
			]);

			if ($validator->passes()) {
				if(!empty($request->token)){
					$result = \Models\Administrator::findByToken($request->token,['id_user']);

					if(!empty($result)){
						$isUpdated = \Models\Users::change($result['id_user'],['password' => bcrypt($request->password),'is_email_verified' => 'yes','status' => 'active','remember_token' => bcrypt(__random_string()) ,'updated' => date('Y-m-d H:i:s')]);

						if(!empty($isUpdated)){
							$request->session()->flash('alert',sprintf(ALERT_SUCCESS,trans("website.W0003")));
							//return redirect()->back()->with(['success' => true]);
							return redirect()->intended(sprintf('/%s/%s',ADMIN_FOLDER,'login'));
						}
					}
					$request->session()->flash('alert',sprintf(ALERT_DANGER,trans("website.W0002")));
				}
				$request->session()->flash('alert',sprintf(ALERT_DANGER,trans("website.W0002")));
			}

			return redirect()->back()->withErrors($validator)->withInput();
		}                		

		/**
         * [This method is used for randering view of login] 
         * @param  null
         * @return \Illuminate\Http\Response
         */
        
		public function login(){
			$data['page_title'] = 'Login';

			if (!empty(Cookie::get(LOGIN_REMEMBER))) {
				$email = base64_decode(Cookie::get(LOGIN_EMAIL));
				$password = base64_decode(Cookie::get(LOGIN_PASSWORD));
				$remember = Cookie::get(LOGIN_REMEMBER);

				$data[LOGIN_EMAIL] = $email;
				$data[LOGIN_PASSWORD] = $password;
				$data[LOGIN_REMEMBER] = $remember;
			}else{
				$data[LOGIN_EMAIL] = "";
				$data[LOGIN_PASSWORD] = "";
				$data[LOGIN_REMEMBER] = "";
			}

			return view(sprintf("%s.%s","backend","login"))->with($data);
		}

		/**
         * [This method is used for authentication] 
         * @param  Request
         * @return \Illuminate\Http\Response
         */
        
		public function authenticate(Request $request){
			$validator = \Validator::make($request->all(), [
				LOGIN_EMAIL => 'required|email',
				LOGIN_PASSWORD =>'required'
			],[
				LOGIN_EMAIL.'.required' => 'Please enter email address.',
				LOGIN_EMAIL.'.email' => 'Please enter valid email address.',
				LOGIN_PASSWORD.'.required' => 'Please enter password.'
			]);

			if ($validator->passes()) {
				if(Auth::guard('admin')->attempt(['email' => $request->{LOGIN_EMAIL},'password' => $request->{LOGIN_PASSWORD}],$request->{LOGIN_REMEMBER})){
					if(Auth::guard('admin')->user()->status == 'active'){
						if ($request->{LOGIN_REMEMBER}){
							Cookie::queue(LOGIN_EMAIL, base64_encode($request->{LOGIN_EMAIL}));
							Cookie::queue(LOGIN_PASSWORD, base64_encode($request->{LOGIN_PASSWORD}));
							Cookie::queue(LOGIN_REMEMBER, ($request->{LOGIN_REMEMBER}));
						} else {
							Cookie::queue(LOGIN_EMAIL, '', -100);
							Cookie::queue(LOGIN_PASSWORD, '', -100);
							Cookie::queue(LOGIN_REMEMBER, '', -100);
						}
						return redirect()->intended(sprintf('/%s/%s',ADMIN_FOLDER,'dashboard'));
					}else{
						Auth::guard('admin')->logout();
						$request->session()->flash('alert', sprintf(ALERT_DANGER,'Your account has been blocked, please contact to administrator.'));
						return redirect()->back()->withErrors($validator)->withInput();        
					}
				}else{
					$request->session()->flash('alert', sprintf(ALERT_DANGER,'Email & Password combination is wrong. Try Again.'));
					return redirect()->back()->withErrors($validator)->withInput();    
				}
			}else{
				return redirect()->back()->withErrors($validator)->withInput();
			}
		}

		/**
         * [This method is used for randering view of logout] 
         * @param  null
         * @return \Illuminate\Http\Response
         */
        
		public function getLogout(){
			Auth::guard('admin')->logout();
			return redirect(sprintf('/%s/%s',ADMIN_FOLDER,'login'));
		}

		/**
         * [This method is used for general] 
         * @param Request 
         * @return \Illuminate\Http\Response
         */

		public function general(Request $request, Builder $htmlBuilder){

			$data['page_title'] 		= 'General Settings';
			$settings 					= \Cache::get('configuration');

			$data['setting']            = (object)$settings;
			$data['page']               = (!empty($request->page))?$request->page:"basic";
			$data['subindustries_name'] = (\Cache::get('subindustries_name'));
			$data['url']                = url(sprintf('%s/general', $this->URI_PLACEHOLDER));
			$data['uri_placeholder']    = $this->URI_PLACEHOLDER;
			$data['site_environment']   = inverse_site_envionment($data['setting']->site_environment);
			
			if($request->page == 'countries'){
				if ($request->ajax()) {
					$keys = [
						DB::raw('@row_number  := @row_number  + 1 AS row_number'),
						'countries.*',
						\DB::Raw("IF(( id != ''),`id`, `en`) as id"),
						\DB::Raw("IF(( cz != ''),`cz`, `en`) as cz"),
						\DB::Raw("IF(( ta != ''),`ta`, `en`) as ta"),
						\DB::Raw("IF(( hi != ''),`hi`, `en`) as hi")
					];

					$countries = \Models\Listings::countries("objects",$keys,"status='active'");
					return \Datatables::of($countries)
					/*->filter(function ($instance) use($request){
	                    if ($request->has('search')) {
	                        if(!empty($request->search['value'])){
	                            $instance->collection = $instance->collection->filter(function ($row) use ($request) {
	                                return (
	                                	\Str::contains(strtolower($row->country_name), strtolower($request->search['value'])) 
	                                	|| 
	                                	( strpos(strtolower($row->status), strtolower($request->search['value'])) === 0 ) 
	                                	|| 
	                                	\Str::contains(strtolower($row->phone_country_code), strtolower($request->search['value'])) 
	                                	|| 
	                                	\Str::contains(strtolower($row->iso_code), strtolower($request->search['value']))
                                	) ? true : false;
	                            });
	                        } 
	                    }
	                })*/
					->editColumn('status',function($country){
						return $country->status = ucfirst($country->status);
					})                    
					->editColumn('action',function($country) use($request){
						$html = sprintf('
							<button 
							data-request="inline-form" 
							data-target="#form-content" 
							data-url="%s" 
							class="btn badge bg-black">
							Edit
							</button> ',
							url(sprintf('%s/general/%s/edit?country_id=%s',ADMIN_FOLDER,$request->page,___encrypt($country->id_country))
							)
						);
						
						/*if($country->status == 'Active'){
							$html .= '<a 
							href="javascript:void(0);" 
							data-url="'.url(sprintf('%s/ajax/country/status?country_id=%s&status=inactive',ADMIN_FOLDER,$country->id_country)).'" 
							data-request="ajax-confirm"
							data-ask_title="'.ADMIN_CONFIRM_TITLE.'" 
							data-ask="Do you really want to continue with this action?" 
							class="badge bg-green">Inactive</a>  ';
						}else{
							$html .= '<a 
							href="javascript:void(0);" 
							data-url="'.url(sprintf('%s/ajax/country/status?country_id=%s&status=active',ADMIN_FOLDER,$country->id_country)).'" 
							data-request="ajax-confirm"
							data-ask_title="'.ADMIN_CONFIRM_TITLE.'" 
							data-ask="Do you really want to continue with this action?" 
							class="badge bg-green">Active</a>  ';                        
						}*/

						/*$html .= '<a 
							href="javascript:void(0);" 
							data-url="'.url(sprintf('%s/ajax/country/status?country_id=%s&status=trashed',ADMIN_FOLDER,$country->id_country)).'" 
							data-request="ajax-confirm"
							data-ask_title="'.ADMIN_CONFIRM_TITLE.'" 
							data-ask="Do you really want to continue with this action?" 
							class="badge bg-red">Delete</a>  ';*/
						return $html;
					})
					->make(true);
				}
				$data['html'] = $htmlBuilder
				->addColumn(['data' => 'row_number', 'name' => 'row_number', 'title' => '#','width' => '1'])
				->addColumn(['data' => 'en', 'name' => 'en', 'title' => 'English Country'])
				->addColumn(['data' => 'id', 'name' => 'id', 'title' => 'Indonesia Country'])
				->addColumn(['data' => 'cz', 'name' => 'cz', 'title' => 'Mandarin Country'])
				->addColumn(['data' => 'ta', 'name' => 'ta', 'title' => 'Tamil Country'])
				->addColumn(['data' => 'hi', 'name' => 'hi', 'title' => 'Hindi Country'])
				->addColumn(['data' => 'phone_country_code', 'name' => 'phone_country_code', 'title' => 'Phone Code'])
				->addColumn(['data' => 'iso_code', 'name' => 'iso_code', 'title' => 'Iso Code'])
				// ->addColumn(['data' => 'status', 'name' => 'status', 'title' => 'Status'])
				->addColumn(['data' => 'action', 'name' => 'action', 'title' => 'Actions','searchable' => false, 'orderable' => false, 'width' => '10']);
			}else if($request->page == 'states'){
				if ($request->ajax()) {
					$keys = [
						DB::raw('@row_number  := @row_number  + 1 AS row_number'),
						'state.*',
						\DB::Raw("IF(( {$this->prefix}state.id != ''),{$this->prefix}state.`id`, {$this->prefix}state.`en`) as id"),
						\DB::Raw("IF(( {$this->prefix}state.cz != ''),{$this->prefix}state.`cz`, {$this->prefix}state.`en`) as cz"),
						\DB::Raw("IF(( {$this->prefix}state.ta != ''),{$this->prefix}state.`ta`, {$this->prefix}state.`en`) as ta"),
						\DB::Raw("IF(( {$this->prefix}state.hi != ''),{$this->prefix}state.`hi`, {$this->prefix}state.`en`) as hi"),
						'countries.en as country_name'
					];
					$stateList = \Models\Listings::state_list(
						"
							{$this->prefix}state.status = 'active'
							AND
							{$this->prefix}countries.status = 'active'
						",
						$keys,
						'obj'
					);
					return \Datatables::of($stateList)
					/*->filter(function ($instance) use($request){
	                    if ($request->has('search')) {
	                        if(!empty($request->search['value'])){
	                            $instance->collection = $instance->collection->filter(function ($row) use ($request) {
	                                return (
	                                	\Str::contains(strtolower($row->country_name), strtolower($request->search['value'])) 
	                                	||
	                                	\Str::contains(strtolower($row->state_name), strtolower($request->search['value'])) 
	                                	|| 
	                                	\Str::contains(strtolower($row->iso_code), strtolower($request->search['value']))
	                                	|| 
	                                	( strpos(strtolower($row->status), strtolower($request->search['value'])) === 0 ) 
	                                ) ? true : false;
	                            });
	                        } 
	                    }
	                })*/					
					->editColumn('status',function($stateList){
					return $stateList->status = ucfirst($stateList->status);
					})                    
					->editColumn('action',function($stateList) use($request){
						$html = sprintf('
							<button 
							data-request="inline-form" 
							data-target="#form-content" 
							data-url="%s" 
							class="btn badge bg-black">
							Edit
							</button> ',
							url(sprintf('%s/general/%s/edit?id_state=%s',ADMIN_FOLDER,$request->page,___encrypt($stateList->id_state))
							)
						);
						
						/*if($stateList->status == 'Active'){
							$html .= '<a 
							href="javascript:void(0);" 
							data-url="'.url(sprintf('%s/ajax/state/status?id_state=%s&status=inactive',ADMIN_FOLDER,$stateList->id_state)).'" 
							data-request="ajax-confirm"
							data-ask_title="'.ADMIN_CONFIRM_TITLE.'" 
							data-ask="Do you really want to continue with this action?" 
							class="badge bg-green">Inactive</a>  ';
						}else{
							$html .= '<a 
							href="javascript:void(0);" 
							data-url="'.url(sprintf('%s/ajax/state/status?id_state=%s&status=active',ADMIN_FOLDER,$stateList->id_state)).'" 
							data-request="ajax-confirm"
							data-ask_title="'.ADMIN_CONFIRM_TITLE.'" 
							data-ask="Do you really want to continue with this action?" 
							class="badge bg-green">Active</a>  ';                        
						}

						$html .= '<a 
							href="javascript:void(0);" 
							data-url="'.url(sprintf('%s/ajax/state/status?id_state=%s&status=trashed',ADMIN_FOLDER,$stateList->id_state)).'" 
							data-request="ajax-confirm"
							data-ask_title="'.ADMIN_CONFIRM_TITLE.'" 
							data-ask="Do you really want to continue with this action?" 
							class="badge bg-red">Delete</a>  ';*/
						return $html;
					})
					->make(true);
				}
				$data['html'] = $htmlBuilder
				->addColumn(['data' => 'row_number', 'name' => 'row_number', 'title' => '#','width' => '1'])
				->addColumn(['data' => 'country_name', 'name' => 'country_name', 'title' => 'Country'])
				->addColumn(['data' => 'en', 'name' => 'en', 'title' => 'English State'])
				->addColumn(['data' => 'id', 'name' => 'id', 'title' => 'Indonesia State'])
				->addColumn(['data' => 'cz', 'name' => 'cz', 'title' => 'Mandarin State'])
				->addColumn(['data' => 'ta', 'name' => 'ta', 'title' => 'Tamil State'])
				->addColumn(['data' => 'hi', 'name' => 'hi', 'title' => 'Hindi State'])
				->addColumn(['data' => 'iso_code', 'name' => 'iso_code', 'title' => 'Iso Code'])
				// ->addColumn(['data' => 'status', 'name' => 'status', 'title' => 'Status'])
				->addColumn(['data' => 'action', 'name' => 'action', 'title' => 'Actions','searchable' => false, 'orderable' => false,'width' => 10]);
			}else if($request->page == 'city'){
				if ($request->ajax()) {
					$keys = [
						DB::raw('@row_number  := @row_number  + 1 AS row_number'),
						'city.*',
						\DB::Raw("IF(( {$this->prefix}city.id != ''),{$this->prefix}city.`id`, {$this->prefix}city.`en`) as id"),
						\DB::Raw("IF(( {$this->prefix}city.cz != ''),{$this->prefix}city.`cz`, {$this->prefix}city.`en`) as cz"),
						\DB::Raw("IF(( {$this->prefix}city.ta != ''),{$this->prefix}city.`ta`, {$this->prefix}city.`en`) as ta"),
						\DB::Raw("IF(( {$this->prefix}city.hi != ''),{$this->prefix}city.`hi`, {$this->prefix}city.`en`) as hi"),						
						'state.en as state_name',
						'countries.en as country_name'
					];

					$cityList = \Models\Listings::city_list(
						"
							{$this->prefix}city.status = 'active'
							AND
							{$this->prefix}state.status = 'active'
							AND
							{$this->prefix}countries.status = 'active'
						",
						$keys,
						'obj'
					);
					return \Datatables::of($cityList)
					/*->filter(function ($instance) use($request){
	                    if ($request->has('search')) {
	                        if(!empty($request->search['value'])){
	                            $instance->collection = $instance->collection->filter(function ($row) use ($request) {
	                                return (
	                                	\Str::contains(strtolower($row->country_name), strtolower($request->search['value'])) 
	                                	||
	                                	\Str::contains(strtolower($row->state_name), strtolower($request->search['value'])) 
	                                	|| 
	                                	\Str::contains(strtolower($row->city_name), strtolower($request->search['value']))
	                                	|| 
	                                	( strpos(strtolower($row->status), strtolower($request->search['value'])) === 0 ) 
	                                ) ? true : false;
	                            });
	                        } 
	                    }
	                })*/
					->editColumn('status',function($cityList){
					return $cityList->status = ucfirst($cityList->status);
					})                    
					->editColumn('action',function($cityList) use($request){
						$html = sprintf('
							<button 
							data-request="inline-form" 
							data-target="#form-content" 
							data-url="%s" 
							class="btn badge bg-black">
							Edit
							</button> ',
							url(sprintf('%s/general/%s/edit?id_city=%s',ADMIN_FOLDER,$request->page,___encrypt($cityList->id_city))
							)
						);
						/*if($cityList->status == 'Active'){
							$html .= '<a 
							href="javascript:void(0);" 
							data-url="'.url(sprintf('%s/ajax/city/status?id_city=%s&status=inactive',ADMIN_FOLDER,$cityList->id_city)).'" 
							data-request="ajax-confirm"
							data-ask_title="'.ADMIN_CONFIRM_TITLE.'"  
							data-ask="Do you really want to continue with this action?" 
							class="badge bg-green">Inactive</a>  ';
						}else{
							$html .= '<a 
							href="javascript:void(0);" 
							data-url="'.url(sprintf('%s/ajax/city/status?id_city=%s&status=active',ADMIN_FOLDER,$cityList->id_city)).'" 
							data-request="ajax-confirm"
							data-ask_title="'.ADMIN_CONFIRM_TITLE.'"  
							data-ask="Do you really want to continue with this action?" 
							class="badge bg-green">Active</a>  ';                        
						}

						$html .= '<a 
							href="javascript:void(0);" 
							data-url="'.url(sprintf('%s/ajax/city/status?id_city=%s&status=trashed',ADMIN_FOLDER,$cityList->id_city)).'" 
							data-request="ajax-confirm"
							data-ask_title="'.ADMIN_CONFIRM_TITLE.'" 
							data-ask="Do you really want to continue with this action?" 
							class="badge bg-red">Delete</a>  ';*/
						return $html;
					})
					->make(true);
				}
				$data['html'] = $htmlBuilder
				->addColumn(['data' => 'row_number', 'name' => 'row_number', 'title' => '#','width' => '1'])
				->addColumn(['data' => 'country_name', 'name' => 'country_name', 'title' => 'Country'])
				->addColumn(['data' => 'state_name', 'name' => 'state_name', 'title' => 'State Name'])
				->addColumn(['data' => 'en', 'name' => 'en', 'title' => 'English City'])
				->addColumn(['data' => 'id', 'name' => 'id', 'title' => 'Indonesia City'])
				->addColumn(['data' => 'cz', 'name' => 'cz', 'title' => 'Mandarin City'])
				->addColumn(['data' => 'ta', 'name' => 'ta', 'title' => 'Tamil City'])
				->addColumn(['data' => 'hi', 'name' => 'hi', 'title' => 'Hindi City'])
				// ->addColumn(['data' => 'status', 'name' => 'status', 'title' => 'Status'])
				->addColumn(['data' => 'action', 'name' => 'action', 'title' => 'Actions','searchable' => false, 'orderable' => false,'width' => 10]);
			}else if($request->page == 'industry'){
				if ($request->ajax()) {
					$keys = [
						DB::raw('@row_number  := @row_number  + 1 AS row_number'),
						'industries.id_industry',
						'industries.en',
						\DB::Raw("IF(( {$this->prefix}industries.id != ''),{$this->prefix}industries.`id`, {$this->prefix}industries.`en`) as id"),
						\DB::Raw("IF(( {$this->prefix}industries.cz != ''),{$this->prefix}industries.`cz`, {$this->prefix}industries.`en`) as cz"),
						\DB::Raw("IF(( {$this->prefix}industries.ta != ''),{$this->prefix}industries.`ta`, {$this->prefix}industries.`en`) as ta"),
						\DB::Raw("IF(( {$this->prefix}industries.hi != ''),{$this->prefix}industries.`hi`, {$this->prefix}industries.`en`) as hi"),						
						'industries.status'
					];
					$industryList = \Models\Industries::allindustries("obj"," {$this->prefix}industries.parent = '0' AND {$this->prefix}industries.status != 'trashed'",$keys);
					return \Datatables::of($industryList)
					/*->filter(function ($instance) use($request){
	                    if ($request->has('search')) {
	                        if(!empty($request->search['value'])){
	                            $instance->collection = $instance->collection->filter(function ($row) use ($request) {
	                                return (
	                                	\Str::contains(strtolower($row->name), strtolower($request->search['value']))
	                                	|| 
	                                	( strpos(strtolower($row->status), strtolower($request->search['value'])) === 0 ) 
	                                ) ? true : false;
	                            });
	                        } 
	                    }
	                })*/					
					->editColumn('status',function($industryList){
					return $industryList->status = ucfirst($industryList->status);
					})                    
					->editColumn('action',function($industryList) use($request){
						$html = sprintf('
							<button 
							data-request="inline-form" 
							data-target="#form-content" 
							data-url="%s" 
							class="btn badge bg-black">
							Edit
							</button> ',
							url(sprintf('%s/general/%s/edit?id_industry=%s',ADMIN_FOLDER,$request->page,___encrypt($industryList->id_industry))
							)
						);
						/*if($industryList->status == 'Active'){
							$html .= '<a 
							href="javascript:void(0);" 
							data-url="'.url(sprintf('%s/ajax/industry/status?id_industry=%s&status=inactive',ADMIN_FOLDER,$industryList->id_industry)).'" 
							data-request="ajax-confirm"
							data-ask_title="'.ADMIN_CONFIRM_TITLE.'"  
							data-ask="Do you really want to continue with this action?" 
							class="badge bg-green">Inactive</a>  ';
						}else{
							$html .= '<a 
							href="javascript:void(0);" 
							data-url="'.url(sprintf('%s/ajax/industry/status?id_industry=%s&status=active',ADMIN_FOLDER,$industryList->id_industry)).'" 
							data-request="ajax-confirm"
							data-ask_title="'.ADMIN_CONFIRM_TITLE.'"  
							data-ask="Do you really want to continue with this action?" 
							class="badge bg-green">Active</a>  ';                        
						}

						$html .= '<a 
							href="javascript:void(0);" 
							data-url="'.url(sprintf('%s/ajax/industry/status?id_industry=%s&status=trashed',ADMIN_FOLDER,$industryList->id_industry)).'" 
							data-request="ajax-confirm"
							data-ask_title="'.ADMIN_CONFIRM_TITLE.'"  
							data-ask="Do you really want to continue with this action?" 
							class="badge bg-red">Delete</a>  ';*/
						return $html;
					})
					->make(true);
				}
				$data['html'] = $htmlBuilder
				->addColumn(['data' => 'row_number', 'name' => 'row_number', 'title' => '#','width' => '1'])
				->addColumn(['data' => 'en', 'name' => 'en', 'title' => 'English Industry'])
				->addColumn(['data' => 'id', 'name' => 'id', 'title' => 'Indonesia Industry'])
				->addColumn(['data' => 'cz', 'name' => 'cz', 'title' => 'Mandarin Industry'])
				->addColumn(['data' => 'ta', 'name' => 'ta', 'title' => 'Tamil Industry'])
				->addColumn(['data' => 'hi', 'name' => 'hi', 'title' => 'Hindi Industry'])
				// ->addColumn(['data' => 'status', 'name' => 'status', 'title' => 'Status', 'width' => '10'])
				->addColumn(['data' => 'action', 'name' => 'action', 'title' => 'Actions','searchable' => false, 'orderable' => false, 'width' => '10']);
			}else if($request->page == 'sub_industry'){
				if ($request->ajax()) {                    
					$keys = [
						DB::raw('@row_number  := @row_number  + 1 AS row_number'),
						'industries.id_industry',
						'industries.en',
						\DB::Raw("IF(( {$this->prefix}industries.id != ''),{$this->prefix}industries.`id`, {$this->prefix}industries.`en`) as id"),
						\DB::Raw("IF(( {$this->prefix}industries.cz != ''),{$this->prefix}industries.`cz`, {$this->prefix}industries.`en`) as cz"),
						\DB::Raw("IF(( {$this->prefix}industries.ta != ''),{$this->prefix}industries.`ta`, {$this->prefix}industries.`en`) as ta"),
						\DB::Raw("IF(( {$this->prefix}industries.hi != ''),{$this->prefix}industries.`hi`, {$this->prefix}industries.`en`) as hi"),	
						'parent.en as industry',
						'industries.status'
					];

					$sub_indusrty_list = \Models\Industries::allindustries("obj"," {$this->prefix}industries.parent != '0' AND {$this->prefix}industries.status != 'trashed' ",$keys);
					return \Datatables::of($sub_indusrty_list)
					/*->filter(function ($instance) use($request){
	                    if ($request->has('search')) {
	                        if(!empty($request->search['value'])){
	                            $instance->collection = $instance->collection->filter(function ($row) use ($request) {
	                                return (
	                                	\Str::contains(strtolower($row->industry), strtolower($request->search['value']))
	                                	||
	                                	\Str::contains(strtolower($row->name), strtolower($request->search['value']))
	                                	|| 
	                                	( strpos(strtolower($row->status), strtolower($request->search['value'])) === 0 ) 
	                                ) ? true : false;
	                            });
	                        } 
	                    }
	                })*/
					->editColumn('status',function($sub_indusrty_list){
					return $sub_indusrty_list->status = ucfirst($sub_indusrty_list->status);
					})
					->editColumn('action',function($sub_indusrty_list) use($request){
						$html = sprintf('
							<button 
							data-request="inline-form" 
							data-target="#form-content" 
							data-url="%s" 
							class="btn badge bg-black">
							Edit
							</button> ',
							url(sprintf('%s/general/%s/edit?id_industry=%s',ADMIN_FOLDER,$request->page,___encrypt($sub_indusrty_list->id_industry))
							)
						);
						
						/*if($sub_indusrty_list->status == 'Active'){
							$html .= '<a 
							href="javascript:void(0);" 
							data-url="'.url(sprintf('%s/ajax/industry/status?id_industry=%s&status=inactive',ADMIN_FOLDER,$sub_indusrty_list->id_industry)).'" 
							data-request="ajax-confirm"
							data-ask_title="'.ADMIN_CONFIRM_TITLE.'"  
							data-ask="Do you really want to continue with this action?" 
							class="badge bg-green">Inactive</a>  ';
						}else{
							$html .= '<a 
							href="javascript:void(0);" 
							data-url="'.url(sprintf('%s/ajax/industry/status?id_industry=%s&status=active',ADMIN_FOLDER,$sub_indusrty_list->id_industry)).'" 
							data-request="ajax-confirm"
							data-ask_title="'.ADMIN_CONFIRM_TITLE.'"  
							data-ask="Do you really want to continue with this action?" 
							class="badge bg-green">Active</a>  ';                        
						}

						$html .= '<a 
							href="javascript:void(0);" 
							data-url="'.url(sprintf('%s/ajax/industry/status?id_industry=%s&status=trashed',ADMIN_FOLDER,$sub_indusrty_list->id_industry)).'" 
							data-request="ajax-confirm"
							data-ask_title="'.ADMIN_CONFIRM_TITLE.'"  
							data-ask="Do you really want to continue with this action?" 
							class="badge bg-red">Delete</a>  ';*/
						return $html;
					})
					->make(true);
				}
				$data['html'] = $htmlBuilder
				->addColumn(['data' => 'row_number', 'name' => 'row_number', 'title' => '#','width' => '1'])
				->addColumn(['data' => 'industry', 'name' => 'industry', 'title' => 'Industry'])
				->addColumn(['data' => 'en', 'name' => 'en', 'title' => 'English Industry'])
				->addColumn(['data' => 'id', 'name' => 'id', 'title' => 'Indonesia Industry'])
				->addColumn(['data' => 'cz', 'name' => 'cz', 'title' => 'Mandarin Industry'])
				->addColumn(['data' => 'ta', 'name' => 'ta', 'title' => 'Tamil Industry'])
				->addColumn(['data' => 'hi', 'name' => 'hi', 'title' => 'Hindi Industry'])
				// ->addColumn(['data' => 'status', 'name' => 'status', 'title' => 'Status', 'width' => '10'])
				->addColumn(['data' => 'action', 'name' => 'action', 'title' => 'Actions', 'width' => '150','searchable' => false, 'orderable' => false, 'width' => '10']);
			}else if($request->page == 'abusive_words'){
				if ($request->ajax()) {                    
					$keys = [
						DB::raw('@row_number  := @row_number  + 1 AS row_number'),
						'id_words',
						'abusive_word',
						'status'
					];

					$abusive_word_list = \Models\Listings::abusive_words("obj"," status != 'trashed' ",$keys);
					return \Datatables::of($abusive_word_list)
					/*->filter(function ($instance) use($request){
	                    if ($request->has('search')) {
	                        if(!empty($request->search['value'])){
	                            $instance->collection = $instance->collection->filter(function ($row) use ($request) {
	                                return (
	                                	\Str::contains(strtolower($row->abusive_word), strtolower($request->search['value']))
	                                	|| 
	                                	( strpos(strtolower($row->status), strtolower($request->search['value'])) === 0 ) 
	                                ) ? true : false;
	                            });
	                        } 
	                    }
	                })*/					
					->editColumn('status',function($abusive_word_list){
					return $abusive_word_list->status = ucfirst($abusive_word_list->status);
					})
					->editColumn('action',function($abusive_word_list) use($request){
						$html = sprintf('
							<button 
							data-request="inline-form" 
							data-target="#form-content" 
							data-url="%s" 
							class="btn badge bg-black">
							Edit
							</button> ',
							url(sprintf('%s/general/%s/edit?id_words=%s',ADMIN_FOLDER,$request->page,___encrypt($abusive_word_list->id_words))
							)
						);
						
						/*if($abusive_word_list->status == 'Active'){
							$html .= '<a 
							href="javascript:void(0);" 
							data-url="'.url(sprintf('%s/ajax/abusive-word/status?id_words=%s&status=inactive',ADMIN_FOLDER,$abusive_word_list->id_words)).'" 
							data-request="ajax-confirm"
							data-ask_title="'.ADMIN_CONFIRM_TITLE.'"  
							data-ask="Do you really want to continue with this action?" 
							class="badge bg-green">Inactive</a>  ';
						}else{
							$html .= '<a 
							href="javascript:void(0);" 
							data-url="'.url(sprintf('%s/ajax/abusive-word/status?id_words=%s&status=active',ADMIN_FOLDER,$abusive_word_list->id_words)).'" 
							data-request="ajax-confirm"
							data-ask_title="'.ADMIN_CONFIRM_TITLE.'"  
							data-ask="Do you really want to continue with this action?" 
							class="badge bg-green">Active</a>  ';                        
						}*/

						$html .= '<a 
							href="javascript:void(0);" 
							data-url="'.url(sprintf('%s/ajax/abusive-word/status?id_words=%s&status=trashed',ADMIN_FOLDER,$abusive_word_list->id_words)).'" 
							data-request="ajax-confirm"
							data-ask_title="'.ADMIN_CONFIRM_TITLE.'"  
							data-ask="Do you really want to continue with this action?" 
							class="badge bg-red">Delete</a>  ';
						return $html;
					})
					->make(true);
				}
				$data['html'] = $htmlBuilder
				->addColumn(['data' => 'row_number', 'name' => 'row_number', 'title' => '#','width' => '1'])
				->addColumn(['data' => 'abusive_word', 'name' => 'abusive_word', 'title' => 'Abusive Word'])
				// ->addColumn(['data' => 'status', 'name' => 'status', 'title' => 'Status'])
				->addColumn(['data' => 'action', 'name' => 'action', 'title' => 'Actions', 'width' => '80','searchable' => false, 'orderable' => false]);
			}else if($request->page == 'degree'){
				if ($request->ajax()) {
					$keys = [
						DB::raw('@row_number  := @row_number  + 1 AS row_number'),
						'id_degree',
						'degree_name',
						'degree_status'
					];

					$degree_list = \Models\Listings::degrees("obj",$keys," degree_status != 'trashed' ");
					return \Datatables::of($degree_list)
					/*->filter(function ($instance) use($request){
	                    if ($request->has('search')) {
	                        if(!empty($request->search['value'])){
	                            $instance->collection = $instance->collection->filter(function ($row) use ($request) {
	                                return (
	                                	\Str::contains(strtolower($row->degree_name), strtolower($request->search['value']))
	                                	|| 
	                                	( strpos(strtolower($row->degree_status), strtolower($request->search['value'])) === 0 ) 
	                                ) ? true : false;
	                            });
	                        } 
	                    }
	                })*/
					->editColumn('degree_status',function($degree_list){
					return $degree_list->degree_status = ucfirst($degree_list->degree_status);
					})
					->editColumn('action',function($degree_list) use($request){
						$html = sprintf('
							<button 
							data-request="inline-form" 
							data-target="#form-content" 
							data-url="%s" 
							class="btn badge bg-black">
							Edit
							</button> ',
							url(sprintf('%s/general/%s/edit?id_degree=%s',ADMIN_FOLDER,$request->page,___encrypt($degree_list->id_degree))
							)
						);
						/*if($degree_list->degree_status == 'Active'){
							$html .= '<a 
							href="javascript:void(0);" 
							data-url="'.url(sprintf('%s/ajax/degree/status?id_degree=%s&status=inactive',ADMIN_FOLDER,$degree_list->id_degree)).'" 
							data-request="ajax-confirm"
							data-ask_title="'.ADMIN_CONFIRM_TITLE.'"  
							data-ask="Do you really want to continue with this action?" 
							class="badge bg-green">Inactive</a>  ';
						}else{
							$html .= '<a 
							href="javascript:void(0);" 
							data-url="'.url(sprintf('%s/ajax/degree/status?id_degree=%s&status=active',ADMIN_FOLDER,$degree_list->id_degree)).'" 
							data-request="ajax-confirm"
							data-ask_title="'.ADMIN_CONFIRM_TITLE.'"  
							data-ask="Do you really want to continue with this action?" 
							class="badge bg-green">Active</a>  ';                        
						}

						$html .= '<a 
							href="javascript:void(0);" 
							data-url="'.url(sprintf('%s/ajax/degree/status?id_degree=%s&status=trashed',ADMIN_FOLDER,$degree_list->id_degree)).'" 
							data-request="ajax-confirm"
							data-ask_title="'.ADMIN_CONFIRM_TITLE.'"  
							data-ask="Do you really want to continue with this action?" 
							class="badge bg-red">Delete</a>  ';*/
						return $html;
					})
					->make(true);
				}
				$data['html'] = $htmlBuilder
				->addColumn(['data' => 'row_number', 'name' => 'row_number', 'title' => '#','width' => '1'])
				->addColumn(['data' => 'degree_name', 'name' => 'degree_name', 'title' => 'Degree name'])
				// ->addColumn(['data' => 'degree_status', 'name' => 'degree_status', 'title' => 'Status'])
				->addColumn(['data' => 'action', 'name' => 'action', 'title' => 'Actions', 'width' => '10','searchable' => false, 'orderable' => false]);
			}else if($request->page == 'certificate'){
				if ($request->ajax()) {                    
					$keys = [
						DB::raw('@row_number  := @row_number  + 1 AS row_number'),
						'id_cetificate',
						'certificate_name',
						'certificate_status'
					];

					$certificate_list = \Models\Listings::certificates("obj",$keys," certificate_status != 'trashed' ");
					return \Datatables::of($certificate_list)
					/*->filter(function ($instance) use($request){
	                    if ($request->has('search')) {
	                        if(!empty($request->search['value'])){
	                            $instance->collection = $instance->collection->filter(function ($row) use ($request) {
	                                return (
	                                	\Str::contains(strtolower($row->certificate_name), strtolower($request->search['value']))
	                                	|| 
	                                	( strpos(strtolower($row->certificate_status), strtolower($request->search['value'])) === 0 ) 
	                                ) ? true : false;
	                            });
	                        } 
	                    }
	                })*/					
					->editColumn('certificate_status',function($certificate_list){
					return $certificate_list->certificate_status = ucfirst($certificate_list->certificate_status);
					})
					->editColumn('action',function($certificate_list) use($request){
						$html = sprintf('
							<button 
							data-request="inline-form" 
							data-target="#form-content" 
							data-url="%s" 
							class="btn badge bg-black">
							Edit
							</button> ',
							url(sprintf('%s/general/%s/edit?id_certificate=%s',ADMIN_FOLDER,$request->page,___encrypt($certificate_list->id_cetificate))
							)
						);
						
						/*if($certificate_list->certificate_status == 'Active'){
							$html .= '<a 
							href="javascript:void(0);" 
							data-url="'.url(sprintf('%s/ajax/certificate/status?id_certificate=%s&status=inactive',ADMIN_FOLDER,$certificate_list->id_cetificate)).'" 
							data-request="ajax-confirm"
							data-ask_title="'.ADMIN_CONFIRM_TITLE.'"  
							data-ask="Do you really want to continue with this action?" 
							class="badge bg-green">Inactive</a>  ';
						}else{
							$html .= '<a 
							href="javascript:void(0);" 
							data-url="'.url(sprintf('%s/ajax/certificate/status?id_certificate=%s&status=active',ADMIN_FOLDER,$certificate_list->id_cetificate)).'" 
							data-request="ajax-confirm"
							data-ask_title="'.ADMIN_CONFIRM_TITLE.'"  
							data-ask="Do you really want to continue with this action?" 
							class="badge bg-green">Active</a>  ';                        
						}

						$html .= '<a 
							href="javascript:void(0);" 
							data-url="'.url(sprintf('%s/ajax/certificate/status?id_certificate=%s&status=trashed',ADMIN_FOLDER,$certificate_list->id_cetificate)).'" 
							data-request="ajax-confirm"
							data-ask_title="'.ADMIN_CONFIRM_TITLE.'"  
							data-ask="Do you really want to continue with this action?" 
							class="badge bg-red">Delete</a>  ';*/
						return $html;
					})
					->make(true);
				}
				$data['html'] = $htmlBuilder
				->addColumn(['data' => 'row_number', 'name' => 'row_number', 'title' => '#','width' => '1'])
				->addColumn(['data' => 'certificate_name', 'name' => 'certificate_name', 'title' => 'Certificate name'])
				// ->addColumn(['data' => 'certificate_status', 'name' => 'certificate_status', 'title' => 'Status'])
				->addColumn(['data' => 'action', 'name' => 'action', 'title' => 'Actions', 'width' => '10','searchable' => false, 'orderable' => false]);
			}else if($request->page == 'college'){
				if ($request->ajax()) {                    
					$keys = [
						DB::raw('@row_number  := @row_number  + 1 AS row_number'),
						'id_college',
						'college_name',
						'college_status'
					];

					$college_list = \Models\Listings::colleges("obj",$keys," college_status != 'trashed' ");
					return \Datatables::of($college_list)
					/*->filter(function ($instance) use($request){
	                    if ($request->has('search')) {
	                        if(!empty($request->search['value'])){
	                            $instance->collection = $instance->collection->filter(function ($row) use ($request) {
	                                return (
	                                	\Str::contains(strtolower($row->college_name), strtolower($request->search['value']))
	                                	|| 
	                                	( strpos(strtolower($row->college_status), strtolower($request->search['value'])) === 0 ) 
	                                ) ? true : false;
	                            });
	                        } 
	                    }
	                })*/					
					->editColumn('college_status',function($college_list){
					return $college_list->college_status = ucfirst($college_list->college_status);
					})
					->editColumn('action',function($college_list) use($request){
						$html = sprintf('
							<button 
							data-request="inline-form" 
							data-target="#form-content" 
							data-url="%s" 
							class="btn badge bg-black">
							Edit
							</button> ',
							url(sprintf('%s/general/%s/edit?id_college=%s',ADMIN_FOLDER,$request->page,___encrypt($college_list->id_college))
							)
						);
						
						/*if($college_list->college_status == 'Active'){
							$html .= '<a 
							href="javascript:void(0);" 
							data-url="'.url(sprintf('%s/ajax/college/status?id_college=%s&status=inactive',ADMIN_FOLDER,$college_list->id_college)).'" 
							data-request="ajax-confirm"
							data-ask_title="'.ADMIN_CONFIRM_TITLE.'"  
							data-ask="Do you really want to continue with this action?" 
							class="badge bg-green">Inactive</a>  ';
						}else{
							$html .= '<a 
							href="javascript:void(0);" 
							data-url="'.url(sprintf('%s/ajax/college/status?id_college=%s&status=active',ADMIN_FOLDER,$college_list->id_college)).'" 
							data-request="ajax-confirm"
							data-ask_title="'.ADMIN_CONFIRM_TITLE.'"  
							data-ask="Do you really want to continue with this action?" 
							class="badge bg-green">Active</a>  ';                        
						}

						$html .= '<a 
							href="javascript:void(0);" 
							data-url="'.url(sprintf('%s/ajax/college/status?id_college=%s&status=trashed',ADMIN_FOLDER,$college_list->id_college)).'" 
							data-request="ajax-confirm"
							data-ask_title="'.ADMIN_CONFIRM_TITLE.'"  
							data-ask="Do you really want to continue with this action?" 
							class="badge bg-red">Delete</a>  ';*/
						return $html;
					})
					->make(true);
				}
				$data['html'] = $htmlBuilder
				->addColumn(['data' => 'row_number', 'name' => 'row_number', 'title' => '#','width' => '1'])
				->addColumn(['data' => 'college_name', 'name' => 'college_name', 'title' => 'College name'])
				// ->addColumn(['data' => 'college_status', 'name' => 'college_status', 'title' => 'Status'])
				->addColumn(['data' => 'action', 'name' => 'action', 'title' => 'Actions', 'width' => '10','searchable' => false, 'orderable' => false]);
			}else if($request->page == 'skill'){
				if ($request->ajax()) {
					$keys = [
						DB::raw('@row_number  := @row_number  + 1 AS row_number'),
						'id_skill',
						'industries.en as industry_name',
						'skill_name',
						'skill_status'
					];

					$skill_list = \Models\Listings::getSkillwithIndustry("obj",$keys," skill_status != 'trashed' ");
					return \Datatables::of($skill_list)
					/*->filter(function ($instance) use($request){
	                    if ($request->has('search')) {
	                        if(!empty($request->search['value'])){
	                            $instance->collection = $instance->collection->filter(function ($row) use ($request) {
	                                return (
	                                	\Str::contains(strtolower($row->industry_name), strtolower($request->search['value']))
	                                	||
	                                	\Str::contains(strtolower($row->skill_name), strtolower($request->search['value']))
	                                	|| 
	                                	( strpos(strtolower($row->skill_status), strtolower($request->search['value'])) === 0 ) 
	                                ) ? true : false;
	                            });
	                        } 
	                    }
	                })*/					
					->editColumn('skill_status',function($skill_list){
					return $skill_list->skill_status = ucfirst($skill_list->skill_status);
					})
					->editColumn('action',function($skill_list) use($request){
						$html = sprintf('
							<button 
							data-request="inline-form" 
							data-target="#form-content" 
							data-url="%s" 
							class="btn badge bg-black">
							Edit
							</button> ',
							url(sprintf('%s/general/%s/edit?id_skill=%s',ADMIN_FOLDER,$request->page,___encrypt($skill_list->id_skill))
							)
						);
						
						/*if($skill_list->skill_status == 'Active'){
							$html .= '<a 
							href="javascript:void(0);" 
							data-url="'.url(sprintf('%s/ajax/skill/status?id_skill=%s&status=inactive',ADMIN_FOLDER,$skill_list->id_skill)).'" 
							data-request="ajax-confirm"
							data-ask_title="'.ADMIN_CONFIRM_TITLE.'"  
							data-ask="Do you really want to continue with this action?" 
							class="badge bg-green">Inactive</a>  ';
						}else{
							$html .= '<a 
							href="javascript:void(0);" 
							data-url="'.url(sprintf('%s/ajax/skill/status?id_skill=%s&status=active',ADMIN_FOLDER,$skill_list->id_skill)).'" 
							data-request="ajax-confirm"
							data-ask_title="'.ADMIN_CONFIRM_TITLE.'"  
							data-ask="Do you really want to continue with this action?" 
							class="badge bg-green">Active</a>  ';                        
						}

						$html .= '<a 
							href="javascript:void(0);" 
							data-url="'.url(sprintf('%s/ajax/skill/status?id_skill=%s&status=trashed',ADMIN_FOLDER,$skill_list->id_skill)).'" 
							data-request="ajax-confirm"
							data-ask_title="'.ADMIN_CONFIRM_TITLE.'"  
							data-ask="Do you really want to continue with this action?" 
							class="badge bg-red">Delete</a>  ';*/
						return $html;
					})
					->make(true);
				}
				$data['html'] = $htmlBuilder
				->addColumn(['data' => 'row_number', 'name' => 'row_number', 'title' => '#','width' => '1'])
				->addColumn(['data' => 'industry_name', 'name' => 'industry_name', 'title' => 'Industry'])
				->addColumn(['data' => 'skill_name', 'name' => 'skill_name', 'title' => 'Skill name'])
				// ->addColumn(['data' => 'skill_status', 'name' => 'skill_status', 'title' => 'Status'])
				->addColumn(['data' => 'action', 'name' => 'action', 'title' => 'Actions', 'width' => '10','searchable' => false, 'orderable' => false]);
			}else if($request->page == 'dispute-concern'){
				if ($request->ajax()) {
					$keys = [
						DB::raw('@row_number  := @row_number  + 1 AS row_number'),
						'id_concern',
						'en',
						\DB::Raw("IF((id != ''),`id`, `en`) as id"),
						\DB::Raw("IF((cz != ''),`cz`, `en`) as cz"),
						\DB::Raw("IF((ta != ''),`ta`, `en`) as ta"),
						\DB::Raw("IF((hi != ''),`hi`, `en`) as hi"),	
						'status'
					];

					$dispute_concern = \Models\DisputeConcern::select($keys)->whereNotIn('status',['trashed'])->get();
					return \Datatables::of($dispute_concern)					
					->editColumn('status',function($dispute_concern){
					return $dispute_concern->status = ucfirst($dispute_concern->status);
					})
					->editColumn('action',function($dispute_concern) use($request){
						$html = sprintf('
							<button 
							data-request="inline-form" 
							data-target="#form-content" 
							data-url="%s" 
							class="btn badge bg-black">
							Edit
							</button> ',
							url(sprintf('%s/general/%s/edit?id_concern=%s',ADMIN_FOLDER,$request->page,___encrypt($dispute_concern->id_concern)))
						);
						$html .= '<a 
							href="javascript:void(0);" 
							data-url="'.url(sprintf('%s/ajax/%s/status?id_concern=%s&status=trashed',ADMIN_FOLDER,$request->page,$dispute_concern->id_concern)).'" 
							data-request="ajax-confirm"
							data-ask_title="'.ADMIN_CONFIRM_TITLE.'"  
							data-ask="Do you really want to continue with this action?" 
							class="badge bg-red">Delete</a>  ';
						return $html;
					})
					->make(true);
				}
				$data['html'] = $htmlBuilder
				->addColumn(['data' => 'row_number', 'name' => 'row_number', 'title' => '#','width' => '1'])
				->addColumn(['data' => 'en', 'name' => 'en', 'title' => 'English Concern'])				
				->addColumn(['data' => 'id', 'name' => 'id', 'title' => 'Indonesia Concern'])
				->addColumn(['data' => 'cz', 'name' => 'cz', 'title' => 'Mandarin Concern'])
				->addColumn(['data' => 'ta', 'name' => 'ta', 'title' => 'Tamil Concern'])
				->addColumn(['data' => 'hi', 'name' => 'hi', 'title' => 'Hindi Concern'])
				->addColumn(['data' => 'action', 'name' => 'action', 'title' => 'Actions', 'width' => '80','searchable' => false, 'orderable' => false]);
			}

			return view('backend.pages.settings')->with($data);
		}

		/**
         * [This method is used for adding country] 
         * @param  Request
         * @return Json Response
         */

		public function add_country(Request $request){
			$id_country = $request->country_id ? ___decrypt($request->country_id) : '';
			
			if(!empty($request->action)){
	            $validator = \Validator::make($request->all(), [
	                'iso_code' 				=> 'required',
	                'phone_country_code' 	=> 'required',
	                'en' 					=> 'required'
	            ],[
	            	'iso_code.required'				=> "The Iso Code field is required.",
					'phone_country_code.required'	=> "The Phone Country code field is required.",
					'en.required'					=> "The Country Name field is required."
	            ]);

	            if ($validator->passes()) {
	            	if(!empty($id_country)){
	            		$isInserted 		= \Models\Listings::update_country($id_country,[
	            			'iso_code' 				=> $request->iso_code, 
	            			'phone_country_code' 	=> $request->phone_country_code, 
	            			'en' 					=> $request->en,
	            			'id' 					=> $request->id,
	            			'cz' 					=> $request->cz,
	            			'ta' 					=> $request->ta,
	            			'hi' 					=> $request->hi,
	            			'updated' 				=> date('Y-m-d H:i:s')
	            		]);
	            		$display_message 	= "Country has been updated successfully.";
	            	}else{
	                	$isInserted 		= \Models\Listings::add_country([
	                		'iso_code' 				=> $request->iso_code, 
	                		'phone_country_code' 	=> $request->phone_country_code, 
	            			'en' 					=> $request->en,
	            			'id' 					=> $request->id,
	            			'cz' 					=> $request->cz,
	            			'ta' 					=> $request->ta,
	            			'hi' 					=> $request->hi,
	                		'created' 				=> date('Y-m-d H:i:s'), 
	                		'updated' 				=> date('Y-m-d H:i:s')
	                	]);
	            		$display_message 	= "Country has been saved successfully.";
	            	}

	            	\Cache::forget('countries');
	            	
	                if(!empty($isInserted)){
	                    $this->status 	= true;
	                    $this->message 	= $display_message;
	                    $this->redirect = true;
	                }
	            }else{
					$this->jsondata = ___error_sanatizer($validator->errors()); 
	            }
			}else{
				if(!empty($id_country)){
					$data['country'] = \Models\Listings::countries('single',['id_country','iso_code','phone_country_code','en','id','cz','ta','hi'],'`id_country` = '.$id_country);
				}

				$data['url'] 	= ADMIN_FOLDER;
				$this->jsondata = \View::make('backend.pages.country')->with($data)->render();
				$this->redirect = 'render';
				$this->status 	= true;
			}

			return response()->json([
				'data'      => $this->jsondata,
				'status'    => $this->status,
				'message'   => $this->message,
				'redirect'  => $this->redirect,
			]);
        }

        /**
         * [This method is used for update setting] 
         * @param  Request
         * @return Json Response
         */

		public function update_settings(Request $request){
			$validator = \Validator::make($request->all(), [
				'site_name' 					=> 'required',
				'commission' 					=> 'required|numeric|max:100',
				'paypal_commission' 			=> 'required|numeric|max:100',
				'paypal_commission_flat' 		=> 'required|numeric|max:100',
				'raise_dispute_commission' 		=> 'required|numeric|max:100',
				'cancellation_commission' 		=> 'required|numeric|max:100',
				'site_description' 				=> 'required',
				'site_email' 					=> 'required|email',
				'copyright_text' 				=> 'required',
				'ios_download_app_url' 			=> 'required',
				'android_download_app_url' 		=> 'required',
				'social_facebook_url' 			=> 'required',
				'social_twitter_url' 			=> 'required',
				'social_linkedin_url' 			=> 'required',
				'social_instagram_url' 			=> 'required',
				'social_googleplus_url' 		=> 'required',
				'social_youtube_url' 			=> 'required',
				'smtp_host' 					=> 'required',
				'smtp_username' 				=> 'required',
				'smtp_password' 				=> 'required',
				'smtp_port' 					=> 'required',
				'smtp_mode' 					=> 'required',
				'minimum_profile_percentage'	=> 'required',
				'notice_period' 				=> 'required|numeric|max:100',
			]);

			$validator->after(function ($validator) use($request) {
			    if ($request->commission < 0) {
			        $validator->errors()->add('commission', 'The commission should be greater than zero.');
			    }
			    elseif($request->commission_type == 'per' && $request->commission >= 100){
			        $validator->errors()->add('commission', 'The commission percentage should be less than 100.');
			    }
			});

			if($validator->passes()){

				foreach ($request->except('_token') as $key => $value) {
					\Models\Listings::update_setting($key,$value);
				}

				if(!empty($request->commission) && !empty($request->commission_type)){
					\Models\Listings::alterUserTable($request->commission,$request->commission_type);
				}

				/*$request->session()->flash('alert', sprintf(ALERT_SUCCESS,'Settings has been updated successfully.'));
				return redirect(sprintf('/%s/%s',$this->URI_PLACEHOLDER,'general'));*/
				#$this->redirect = redirect(sprintf('/%s/%s',$this->URI_PLACEHOLDER,'general'));
				$this->redirect = url(sprintf("%s/general",ADMIN_FOLDER));

				$this->status = true;
                $this->message  = 'Settings has been updated successfully.';
			}
			else{
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
         * [This method is used for upload collection] 
         * @param Requestl
         * @return \Illuminate\Http\Response
         */

		public function upload_collection(Request $request) {
			$file = $request->file('file');
			$destination = 'uploads/collection';
			$file->move($destination,$file->getClientOriginalName());
			$content = \File::get(storage_path(sprintf('../public/%s/%s',$destination,$file->getClientOriginalName())));
			
			if(!empty($content)){
				$isUpdated = \Models\Listings::update_setting('postman_collection',$content);
				\Cache::forget('configuration');

				$request->session()->flash('alert', sprintf(ALERT_SUCCESS,'Settings has been updated successfully.'));
			}

			return redirect(sprintf('/%s/%s?page=api',$this->URI_PLACEHOLDER,'general'));
		}

		/**
         * [This method is used for randering emails] 
         * @param  null
         * @return \Illuminate\Http\Response
         */
        
		public function emails(){
			$data['page_title']         = 'Email Messages';
			$data['uri_placeholder']    = $this->URI_PLACEHOLDER;
			$data['url']                = url(sprintf('%s/', $this->URI_PLACEHOLDER));

			return view('backend.emails.list')->with($data);
		}
		
		/**
         * [This method is used for edit email] 
         * @param  Id
         * @return \Illuminate\Http\Response
         */
        
		public function editemail($id_email){
			$data['page_title']         = 'Edit Email Template';
			$data['emails']             = \Models\Listings::emails('first',['*'],"id_email = {$id_email}");
			$data['uri_placeholder']    = $this->URI_PLACEHOLDER;
			$data['url']                = url(sprintf('%s/', $this->URI_PLACEHOLDER));

			return view(sprintf("%s.%s","backend","emails.edit"))->with($data);
		}

		/**
         * [This method is used for update email] 
         * @param Request 
         * @return \Illuminate\Http\Response
         */
        
		public function updateemail(Request $request, $email_id){
			$validator = \Validator::make($request->all(), [
				'subject' => ['required'/*, Rule::unique('emails')->ignore($email_id)*/],
			],[
				'subject.required'=>'Please enter template subject.',
			]);

			if ($validator->passes()) {
				$is_updated = DB::table('emails')
				->where('id_email', ($email_id))
				->update([
					'subject' => $request['subject'],
					'content'=>(string)$request['content']
				]);
				
				$request->session()->flash('success', 'Email template has been updated successfully.');
				return redirect(ADMIN_FOLDER.'/emails');
			} else {
				$this->status = false;  
				return redirect()->back()->withErrors($validator, env('DEFAULT_BACKEND_LAYOUT_FOLDER'))->withInput();              
			} 
		}

		/**
         * [This method is used for generate password] 
         * @param  Request
         * @return \Illuminate\Http\Response
         */

		public function generatePassword(Request $request){
			$password = bcrypt($request->password);
			dd($password);
		}

		/**
         * [This method is used for randering view of front] 
         * @param  null
         * @return \Illuminate\Http\Response
         */

		public function decryptPassword(Request $request){
			$decrypted = Crypt::decrypt($request->password);
			dd($decrypted);
		}

		/*User Management*/

		/**
         * [This method is used for users] 
         * @param  Request
         * @return \Illuminate\Http\Response
         */
        
		public function users(Request $request, Builder $htmlBuilder, $type = 'talent'){
			$data['page'] 				= $type;
			$data['uri_placeholder']    = $this->URI_PLACEHOLDER;
			$data['url']                = url(sprintf('%s/', $this->URI_PLACEHOLDER));
			$data['add_user']           = true;
			$data['userscount'] 		= \Models\Users::listing(['type' => $type])->count();
			
			if ($request->ajax()) {
				return $this->usersAjaxList($type);
			}

			if($request->download && $request->download =='csv'){
				$csvdata = $this->usersAjaxList($type)->getData(true);
				$csvdata = $csvdata['data'];
				$file_name = 'report_'.time().'.csv';
				$headers = [
	                "Content-type" => "application/csv",
	                "Content-Disposition" => "attachment; filename={$file_name}",
	                "Pragma" => "no-cache",
	                "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
	                "Expires" => "0"
	            ];
	            $callback = function() use ($csvdata)
	            {
	                #dd('weqw');
	                $file = fopen('php://output', 'w');
	                fputcsv($file,['Full Name','Email Address','Gender','Mobile','Register with device','Date','Status']);
	                foreach ($csvdata as $cdata) {
	                    fputcsv($file, [$cdata['name'],$cdata['email'],$cdata['gender'],$cdata['mobile'],$cdata['mobile'],$cdata['mobile'],$cdata['status']]);
	                }

	                fclose($file);
	            };

	            $response = new StreamedResponse();
	            $response->setCallback($callback);

	            foreach ($headers as $header_key => $header_val) {
	            	
	            	$response->headers->set($header_key, $header_val);

	            }
	            // @codeCoverageIgnoreEnd
	            $response->send(); 

	            return;
			}

			$htmlBuilder->addColumn(['data' => 'row_number', 'name' => 'row_number', 'title' => '#', 'width' => '1']);
			$htmlBuilder->addColumn(['data' => 'name', 'name' => 'name', 'title' => 'Full Name']);
			$htmlBuilder->addColumn(['data' => 'email', 'name' => 'email', 'title' => 'Email Address']);

			if($type == 'talent' || $type == 'employer'){
				$htmlBuilder->addColumn(['data' => 'gender', 'name' => 'gender', 'title' => 'Gender']);
				$htmlBuilder->addColumn(['data' => 'mobile', 'name' => 'mobile', 'title' => 'Mobile']);
				$htmlBuilder->addColumn(['data' => 'registration_device', 'name' => 'registration_device', 'title' => 'Registered with device']);
			}

			$htmlBuilder->addColumn(['data' => 'created', 'name' => 'created', 'title' => 'Date']);
			$htmlBuilder->addColumn(['data' => 'status', 'name' => 'status', 'title' => 'Status']);
			$htmlBuilder->addColumn(['data' => 'action', 'name' => 'action', 'title' => 'Actions','width' => '130','searchable' => false, 'orderable' => false]);

			$data['html'] = $htmlBuilder;

			return view('backend.talent.list')->with($data);
		}

		public function usersAjaxList($type)
		{
			$users = \Models\Users::listing(['type' => $type]);
				return \Datatables::of($users)
				->editColumn('name',function($item){
					return $this->name = trim(sprintf("%s %s",$item->first_name,$item->last_name));
				})
				->editColumn('gender',function($item){
					if($item->gender){
						return $this->gender = ucfirst($item->gender);
					}else{
						return $this->gender = B_N_A;
					}
				})
				->editColumn('registration_device',function($item){
					if($item->registration_device){
						return $this->gender = ucfirst($item->registration_device);
					}
				})
				->editColumn('mobile',function($item){
					if($item->mobile){
						return $this->mobile = $item->country_code.$item->mobile;
					}else{
						return $this->mobile = B_N_A;
					}
				})
				->editColumn('created',function($item){
					return $this->created = ___d($item->created);
				})
				->editColumn('status',function($item){
					return $this->status = ucfirst($item->status);
				})
				->editColumn('action',function($item){
					$html = '<a href="'.url(sprintf('%s/users/%s/edit?user_id=%s',ADMIN_FOLDER,$item->type,___encrypt($item->id_user))).'" class="badge">View</a> ';

					if($item->status === 'active'){
                        $html .= '<a href="javascript:void(0);" data-url="'.url(sprintf('%s/users/status?id_user=%s&status=inactive',ADMIN_FOLDER,___encrypt($item->id_user))).'" data-request="status" data-ask="Do you really want to continue with this action?" class="badge bg-green" >Inactive</a> ';
                    }else{
                    	$html .= '<a href="javascript:void(0);" data-url="'.url(sprintf('%s/users/status?id_user=%s&status=active',ADMIN_FOLDER,___encrypt($item->id_user))).'" data-request="status" data-ask="Do you really want to continue with this action?" class="badge bg-green" >Active</a> ';
                    }

                    $html .= '<a href="javascript:void(0);" data-target="#hire-me" data-url="'.url(sprintf('%s/users/partial_delete?id_user=%s&status=trashed&type=%s',ADMIN_FOLDER,___encrypt($item->id_user),$item->type)).'" data-request="ajax-modal" class="badge bg-red" >Delete</a>';

					return $html;
				})
				->make(true);
		}

		public function partial_delete_or_suspend(Request $request){
			if($request->ajax()){
				$data['id_user'] = $request->id_user;
				$data['type'] = $request->type;
				$data['status'] = $request->status;
				return view('backend.talent.del_password',$data);
			}
		}

		public function auth_delete_or_suspend_process(Request $request){

			$validator = \Validator::make($request->all(), [
				'password'			=>	['required'],
			],[
				'password.required'	=>	'Please enter password',
			]);

			$validator->after(function() use($request, $validator)  {
				if( !empty($request->input('password'))){
					$result = \Models\Users::findAdminByEmail(\Auth::guard('admin')->user()->email);
                	$match = \Hash::check($request->input('password'), $result['password']);

                	if(!$match){
						$validator->errors()->add('password', 'Entered password does not match.');
                	}
				}
			});

			if($validator->passes()){

				$id_user 		= ___decrypt($request->id_user);
				$user_type 		= $request->type;
				$status 		= $request->status;
				$redirect_page 	= ($status == 'trashed') ? 'users' : 'del-users'; 

				$sepUserInfo = [];
	            if($status == 'restore'){
	            	$userInfo = \Models\Users::findByUserId($id_user);
	            	$sepUserInfo = \Models\Users::findByEmailAnyStatus($userInfo['email']);

	            	if(!empty($sepUserInfo)){
		            	$this->status = true;
			            $this->redirect = url(sprintf("%s/%s/%s",ADMIN_FOLDER,$redirect_page,$user_type));
			            $this->message = 'An account already created with this email, this account can not be restore.';
		            }
		            else{
		            	$status = $status == 'restore' ? 'active' : $status;
	            	
		            	$isUpdated = \Models\Listings::update_user($id_user,array('status' => $status,'updated' => date('Y-m-d H:i:s')));
		            	

						if($isUpdated){
							$this->status = true;
							$this->message = 'Status has been updated successfully.';
							$this->redirect = url(sprintf("%s/%s/%s",ADMIN_FOLDER,$redirect_page,$user_type));
						}else{

							$this->status = true;
							$this->message = 'Status has been updated successfully.';
							$this->redirect = url(sprintf("%s/%s/%s",ADMIN_FOLDER,$redirect_page,$user_type));					
						}

						return response()->json([
							'data'      => $this->jsondata,
							'status'    => $this->status,
							'message'   => $this->message,
							'redirect'  => $this->redirect,
						]);
		            }
	            }
	            else{
	            	$status = $status == 'restore' ? 'active' : $status;
	            	
	            	$isUpdated = \Models\Listings::update_user($id_user,array('status' => $status,'updated' => date('Y-m-d H:i:s')));

	            	if($status == 'active'){
	            		$message = 'User has been updated successfully.';
	            	}
	            	else{
	            		$message = 'User has been deleted successfully.';
	            	}

					if($isUpdated){
						$this->status = true;
						$this->message = $message;
						$this->redirect = url(sprintf("%s/%s/%s",ADMIN_FOLDER,$redirect_page,$user_type));
					}else{

						$this->status = true;
						$this->message = $message;
						$this->redirect = url(sprintf("%s/%s/%s",ADMIN_FOLDER,$redirect_page,$user_type));					
					}
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
         * [This method is used for deleted users] 
         * @param  Request
         * @return \Illuminate\Http\Response
         */
        
		public function del_users(Request $request, Builder $htmlBuilder, $type = 'talent'){
			$data['page'] 				= $type;
			$data['uri_placeholder']    = $this->URI_PLACEHOLDER;
			$data['url']                = url(sprintf('%s/', $this->URI_PLACEHOLDER));
			$data['add_user']           = false;
			$data['userscount'] 		= \Models\Users::listing(['type' => $type])->count();

			if ($request->ajax()) {
				$users = \Models\Users::trashedUserListing(['type' => $type,'status'=>'trashed']);
				return \Datatables::of($users)
				->editColumn('name',function($item){
					return $this->name = trim(sprintf("%s %s",$item->first_name,$item->last_name));
				})
				->editColumn('gender',function($item){
					if($item->gender){
						return $this->gender = ucfirst($item->gender);
					}else{
						return $this->gender = B_N_A;
					}
				})
				->editColumn('registration_device',function($item){
					if($item->registration_device){
						return $this->gender = ucfirst($item->registration_device);
					}
				})
				->editColumn('mobile',function($item){
					if($item->mobile){
						return $this->mobile = $item->country_code.$item->mobile;
					}else{
						return $this->mobile = B_N_A;
					}
				})
				->editColumn('created',function($item){
					return $this->created = ___d($item->created);
				})
				->editColumn('status',function($item){
					return $this->status = ucfirst($item->status);
				})
				->editColumn('action',function($item){
					$html = '';
					// $html = '<a href="'.url(sprintf('%s/users/%s/edit?user_id=%s',ADMIN_FOLDER,$item->type,___encrypt($item->id_user))).'" class="badge">View</a> ';

					if($item->status === 'active'){
                        $html .= '<a href="javascript:void(0);" data-url="'.url(sprintf('%s/users/status?id_user=%s&status=inactive',ADMIN_FOLDER,___encrypt($item->id_user))).'" data-request="status" data-ask="Do you really want to continue with this action?" class="badge bg-green" >Inactive</a> ';
                    }else{
                    	#$html .= '<a href="javascript:void(0);" data-target="#hire-me" data-url="'.url(sprintf('%s/users/status?from=restore&id_user=%s&status=active',ADMIN_FOLDER,___encrypt($item->id_user))).'" data-request="ajax-modal" data-ask="Do you really want to continue with this action?" class="badge bg-green" >Restore</a> ';
                    	$html .= '<a href="javascript:void(0);" data-target="#hire-me" data-url="'.url(sprintf('%s/users/partial_delete?id_user=%s&status=restore&type=%s&from=restore',ADMIN_FOLDER,___encrypt($item->id_user),$item->type)).'" data-request="ajax-modal" data-ask="Do you really want to continue with this action?" class="badge bg-green" >Restore</a> ';
                    }

                    $html .= '<a href="javascript:void(0);" data-target="#hire-me" data-url="'.url(sprintf('%s/users/partial_delete?id_user=%s&status=suspended&type=%s',ADMIN_FOLDER,___encrypt($item->id_user),$item->type)).'" data-request="ajax-modal" class="badge bg-red" >Remove</a>';

					return $html;
				})
				->make(true);
			}

			$htmlBuilder->addColumn(['data' => 'row_number', 'name' => 'row_number', 'title' => '#', 'width' => '1']);
			$htmlBuilder->addColumn(['data' => 'name', 'name' => 'name', 'title' => 'Full Name']);
			$htmlBuilder->addColumn(['data' => 'email', 'name' => 'email', 'title' => 'Email Address']);

			if($type == 'talent' || $type == 'employer'){
				$htmlBuilder->addColumn(['data' => 'gender', 'name' => 'gender', 'title' => 'Gender']);
				$htmlBuilder->addColumn(['data' => 'mobile', 'name' => 'mobile', 'title' => 'Mobile']);
				$htmlBuilder->addColumn(['data' => 'registration_device', 'name' => 'registration_device', 'title' => 'Registered with device']);
			}

			$htmlBuilder->addColumn(['data' => 'created', 'name' => 'created', 'title' => 'Date']);
			$htmlBuilder->addColumn(['data' => 'status', 'name' => 'status', 'title' => 'Status']);
			$htmlBuilder->addColumn(['data' => 'action', 'name' => 'action', 'title' => 'Actions','width' => '130','searchable' => false, 'orderable' => false]);

			$data['html'] = $htmlBuilder;

			return view('backend.talent.list')->with($data);
		}

		/**
         * [This method is used for adding user's] 
         * @param  Request
         * @return \Illuminate\Http\Response
         */

		public function add_talent(Request $request){
			$data['page'] 				= $request->page;
			$data['page_title']         = 'Add Talent';
			$data['uri_placeholder']    = $this->URI_PLACEHOLDER;
			$data['backurl']            = url(sprintf('%s/users/talent', $this->URI_PLACEHOLDER));
			$data['url'] 				= url(sprintf('%s/talent-users/add', $this->URI_PLACEHOLDER));

			return view('backend.talent.add')->with($data);
		}

		/**
         * [This method is used for user's insertion] 
         * @param  Request
         * @return Json Response
         */

		public function insert_talent(Request $request){
			$validator = \Validator::make($request->all(), [
				'first_name'            => validation('first_name'),
				'last_name'             => validation('last_name'),
				'email'                 => ['required','email',Rule::unique('users')->ignore('trashed','status')]
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
			]);

			if ($validator->passes()) {
				$dosignup = \Models\Talents::__dosignup($request);
				$email = $request->email;
				$field    = ['id_user','type','first_name','last_name','name','email','status'];

				if((bool)$dosignup['status']){
					$talent = \Models\Talents::findById($dosignup['signup_user_id'],$field);

					if(!empty($talent) && $talent->status == 'pending'){
						if(!empty($email)){
							$code                   = bcrypt(__random_string());
							$emailData              = ___email_settings();
							$emailData['email']     = $email;
							$emailData['name']      = $request->first_name;
							$emailData['link']      = url(sprintf("create/account?token=%s",$code));

							\Models\Talents::change($dosignup['signup_user_id'],['remember_token' => $code,'updated' => date('Y-m-d H:i:s')]);

							___mail_sender($email,sprintf("%s %s",$request->first_name,$request->last_name),"talent_signup_verification_admin",$emailData);
						}
					}else{
						if(!empty($email)){
							$emailData              = ___email_settings();
							$emailData['email']     = $email;
							$emailData['name']      = $request->first_name;

							___mail_sender($email,sprintf("%s %s",$request->first_name,$request->last_name),"talent_signup_admin",$emailData);
						}
					}

					$this->status = true;
					$this->message = 'User information has been added successfully.';
					$this->redirect = url(sprintf("%s/users/talent",ADMIN_FOLDER));
				}else{
					$this->status = false;
					$this->message = 'Error.';
					$this->redirect = url(sprintf("%s/users/talent",ADMIN_FOLDER));
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
         * [This method is used for edit user's] 
         * @param  Request
         * @return \Illuminate\Http\Response
         */

		public function edit_talent(Request $request, Builder $htmlBuilder, $type = 'talent'){
			$language							= \App::getLocale();
			$id_user 							= ___decrypt($request->user_id);
			$data['page']                       = $request->page;
			$data['page_title']                 = 'Edit Talent';


                
			$profileUrl                    		= (\Models\Talents::get_file(sprintf(" type = 'profile' AND user_id = %s",$id_user),'single',['filename','folder']));
			$data['id_user']                    = $id_user;
			$user 								= (object)['id_user' => $id_user];
			$data['user']                       = (array)\Models\Talents::get_user($user);

			$data['companydata']			= \DB::table('company_connected_talent')->leftjoin('talent_company','talent_company.talent_company_id','=','company_connected_talent.id_talent_company')->select('company_name','company_website','company_biography','user_type')->where('id_user','=',$data['user']['id_user'])->first();

			$data['isOwner'] 		= \Models\companyConnectedTalent::with(['user'])->where('id_user',$data['user']['id_user'])->where('user_type','owner')->get()->first();
			$data['isOwner'] 		= json_decode(json_encode($data['isOwner']));

			if(!empty($data['isOwner'])){
				$data['connected_user'] = \Models\companyConnectedTalent::with(['user','getProfile'])->where('id_talent_company',$data['isOwner']->id_talent_company)->where('user_type','user')->get();
			}/*dd($data['connected_user']);*/
			// dd($data['companydata']);

			if(empty($profileUrl) && empty($data['user']['social_picture'])){
                $data['picture']  = get_file_url($profileUrl);
            }elseif (!empty($profileUrl)) {
                $data['picture']  = get_file_url($profileUrl);
            }elseif (!empty($data['user']['social_picture'])) {
                $data['picture'] = $data['user']['social_picture'];
            }

			$data['url']                        = url(sprintf('%s/users/talent/edit?user_id=%s', ADMIN_FOLDER, ___encrypt($id_user)));
			$data['backurl']            		= url(sprintf('%s/users/talent', $this->URI_PLACEHOLDER));	
			$data['all_skill']                  = \Models\Listings::skills();
			$data['availability']               = \Models\Talents::get_availability($id_user);
			$data['education_list']             = \Models\Talents::educations($id_user);
			$data['get_file']       			= \Models\Portfolio::get_portfolio($id_user);
			
			if(!empty($data['connected_user'])){
				foreach ($data['connected_user'] as $key => $value) {
					$data['get_file_data']       			= \Models\Portfolio::get_portfolio($value['id_user']);
					$data['get_file'] = array_merge($data['get_file'],$data['get_file_data']);
				}
			}

			return view('backend.talent.index')->with($data);
		}

		public function edit_talent_activity_log(Request $request, Builder $htmlBuilder){

			$data['page'] 	= 'activity_log';
			$id_user 	  	= ___decrypt($request->user_id);
			$data['id_user'] = $id_user; 
			$data['picture'] = get_file_url(\Models\Talents::get_file(sprintf(" type = 'profile' AND user_id = %s",$id_user),'single',['filename','folder']));

			$user 			= (object)['id_user' => $id_user];
			$data['user']  	= (array)\Models\Talents::get_user($user);
			$data['url']   	= url(sprintf('%s/users/talent/edit?user_id=%s', ADMIN_FOLDER, ___encrypt($id_user)));

			if($request->ajax()) {
	        	return $this->edit_talentactivity_log_talent_list($request);
	        }

			if($request->download && $request->download =='csv'){

				$csvdata = $this->edit_talentactivity_log_talent_list($request)->getData(true);
				$csvdata = $csvdata['data'];

				$file_name = 'talent_activity_log_'.time().'.csv';

				$headers = [
	                "Content-type" => "application/csv",
	                "Content-Disposition" => "attachment; filename={$file_name}",
	                "Pragma" => "no-cache",
	                "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
	                "Expires" => "0"
	            ];
	            $callback = function() use ($csvdata)
	            {
	                $file = fopen('php://output', 'w');
	                fputcsv($file,['Name','Activity','Reference ID','Reference Type','Reference Name','Date']);

	                foreach ($csvdata as $cdata) {
	                    # code...
	                    fputcsv($file, [$cdata['name'],$cdata['user_activity'],$cdata['reference_id'],$cdata['reference_type'],$cdata['reference_name'],$cdata['created']]);
	                }

	                fclose($file);
	            };

	            $response = new StreamedResponse();
	            $response->setCallback($callback);

	            foreach ($headers as $header_key => $header_val) {
	            	$response->headers->set($header_key, $header_val);
	            }
	            // @codeCoverageIgnoreEnd
	            $response->send(); 
	            return;
			}	        

	        	$data['html'] = $htmlBuilder
				->addColumn(['data' => 'name', 'name' => 'users.name', 'title' => 'Name'])
				->addColumn(['data' => 'user_activity', 'name' => 'activity.user_activity', 'title' => 'Activity','searchable' => false,'orderable' => false])
				->addColumn(['data' => 'reference_id','name' => 'activity.reference_id','title' => 'Reference ID'])
				->addColumn(['data' => 'reference_type','name' => 'activity.reference_type','title' => 'Reference Type'])
				->addColumn(['data' => 'reference_name', 'name' => 'activity.reference_name', 'title' => 'Reference Name','searchable' => false,'orderable'  => false])
				->addColumn(['data' => 'created', 'name' => 'activity.created', 'title' => 'Date']);

			return view('backend.talent.index')->with($data); 
		}

		private function edit_talentactivity_log_talent_list(Request $request){

			$prefix  = DB::getTablePrefix();
        	$project = \Models\ActivityLog::select([
		            'activity.user_id',
		            'activity.action as user_activity',
		            'activity.reference_id',
		            'activity.reference_type',
		            'activity.created',
					\DB::Raw('CONCAT('.$prefix.'users.first_name, " ", '.$prefix.'users.last_name ) AS name'),
					\DB::raw('IF('.$prefix.'activity.reference_type = "projects",'.$prefix.'projects.title,"-") AS reference_name')
		        ])
        		->whereIn('activity.action',['login','talent-submit-proposal','talent-start-job','talent-completed-job','raise-dispute','talent-logout'])
        		->where('user_type','talent')
		        ->leftJoin('users','users.id_user','=','activity.user_id')
		        ->leftJoin('projects','projects.id_project','=','activity.reference_id');

		        if($request->start_date && $request->end_date){
		        	$project->where('activity.created','>=', $request->start_date)
		        			->where('activity.created','<=', $request->end_date);
		        }

		        $project->where('activity.user_id', ___decrypt($request->user_id));
		        $project->orderBy('activity.id_activity', 'DESC');

	        	return \Datatables::eloquent($project)
	        	->editColumn('user_activity',function($item){
	        		return ucfirst(str_replace('-', ' ', $item->user_activity));
	        	})
	        	->editColumn('reference_type',function($item){
	        		return ucfirst($item->reference_type);
	        	})
	        	->editColumn('created',function($item){
	        		return $item->created? date('d F Y', strtotime($item->created)) : '-';
	        	})
	        	->make(true);
        }


		/**
         * [This method is used for update user's] 
         * @param  Request
         * @return Json Response
         */

		public function update_talent(Request $request){
			$request->request->add(['birthday' => date('Y-m-d',strtotime($request->birthday))]);
			$validator = \Validator::make($request->all(), [
				'first_name'                => validation('first_name'),
				'last_name'                 => validation('last_name'),
				'birthday'                  => array_merge(['min_age:14'],validation('birthday')),

				'country_code'              => $request->mobile ? array_merge(['required'], validation('country_code')) : validation('country_code'),
				'mobile'                    => array_merge([Rule::unique('users')->ignore('trashed','status')->where(function($query) use($request){$query->where('id_user','!=',$request->id_user);})],validation('mobile')),
				'postal_code'               => validation('postal_code'),
			],[
				'first_name.required'       => trans('general.M0006'),
				'first_name.regex'          => trans('general.M0007'),
				'first_name.string'         => trans('general.M0007'),
				'first_name.max'            => trans('general.M0020'),
				'last_name.regex'           => trans('general.M0009'),
				'last_name.string'          => trans('general.M0009'),
				'last_name.max'             => trans('general.M0019'),
				'birthday.string'           => trans('general.M0054'),
				'birthday.regex'            => trans('general.M0054'),
				'birthday.min_age'          => trans('general.M0055'),
				'birthday.validate_date'    => trans('general.M0506'),

				'country_code.string'       => trans('general.M0074'),
				'mobile.regex'              => trans('general.M0031'),
				'mobile.string'             => trans('general.M0031'),
				'mobile.min'                => trans('general.M0032'),
				'mobile.max'                => trans('general.M0033'),
				'mobile.unique'             => trans('general.M0197'),

				'postal_code.string'        => trans('general.M0061'),
			]);

			if ($validator->passes()) {
				$is_updated = DB::table('users')
				->where('id_user', $request->id_user)
				->update([
					'first_name'    => $request['first_name'],
					'last_name'     => $request['last_name'],
					'name'          => $request['first_name'] . ' ' . $request['last_name'],
					'birthday'      => $request['birthday'],
					'gender'        => $request['gender'],
					'country_code'  => $request['country_code'],
					'mobile'        => $request['mobile'],
					'other_country_code'  => $request['other_country_code'],
					'other_mobile'  => $request['other_mobile'],
					'address'  => $request['address'],
					'country'  => $request['country'],
					'state'  => $request['state'],
					'city'  => $request['city'],
					'postal_code'  => $request['postal_code'],
				]);

				$this->status = true;
				$this->message = 'User information has been updated successfully.';
				$this->redirect = url(sprintf("%s/users/talent",ADMIN_FOLDER));
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
         * [This method is used for user's status] 
         * @param  Request
         * @return Json Response
         */

		public function users_status(Request $request) {

            $id_user = ___decrypt($request->id_user);
            if(empty($id_user)){return false;}

            $status = strtolower($request->status);
            $from = strtolower($request->from);

            $sepUserInfo = [];
            if($from == 'restore'){
            	$userInfo = \Models\Users::findByUserId($id_user);
            	$sepUserInfo = \Models\Users::findByEmailAnyStatus($userInfo['email']);
            }

            if(!empty($sepUserInfo)){
            	$this->status = true;
	            $this->redirect = 'user-delete';
	            $this->message = 'An account already created with this email, this account can not be restore.';
            }
            else{
            	if($status == 'trashed'){
	                $isUpdated = \Models\Listings::update_user($id_user,array('status' => $status,'updated' => date('Y-m-d H:i:s')));
	            }else{
	                $isUpdated = \Models\Listings::update_user($id_user,array('status' => $status,'updated' => date('Y-m-d H:i:s')));
	            }

	            if($status == 'trashed' || $status == 'inactive'){
	            	\Models\Devices::change($id_user,array('is_current_device' => 'no','updated' => date('Y-m-d H:i:s')));
	            }

	            if(!empty($isUpdated)){
	                $this->status = true;
	                $this->redirect = 'datatable';
	                $this->message = sprintf(ALERT_SUCCESS,'Status has been updated successfully.');
	            }else{
	                $this->status = false;
	                $this->message = sprintf(ALERT_DANGER,'Something wrong, please try again.');
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
         * [This method is used for Education deletion] 
         * @param  Request
         * @return Json Response
         */

		public function delete_education(Request $request){
			$isDeleted = \Models\Talents::delete_education(sprintf(" id_education = %s AND user_id = %s ",$request->id_education, $request->id_user));
			if($isDeleted){
				$this->status = true;
				$this->message  = sprintf(ALERT_SUCCESS,trans("general.M0110"));
			}
			return response()->json([
				'status'    => $this->status,
				'message'   => $this->message
			]);
		}

		/**
         * [This method is used for deleting user's experience] 
         * @param  Request
         * @return Json Response
         */

		public function delete_talent_experience(Request $request){
			$isDeleted = \Models\Talents::delete_experience(sprintf(" id_experience = %s AND user_id = %s ",$request->id_experience, $request->id_user));
			if($isDeleted){
				$this->status = true;
				$this->message  = sprintf(ALERT_SUCCESS,trans("general.M0110"));
			}
			return response()->json([
				'status'    => $this->status,
				'message'   => $this->message
			]);
		}

		/**
         * [This method is used for uploading user's document] 
         * @param  Request
         * @return Json SResponse
         */

		public function user_document_upload(Request $request){
			$validator = \Validator::make($request->all(), [
				"file"            => validation('document'),
			],[
				'file.validate_file_type'  => trans('general.M0119'),
			]);
			if($validator->passes()){
				$folder = 'uploads/certificates/';

				$uploaded_file = upload_file($request,'file',$folder);
				$data = [
					'user_id' => $request->id_user,
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
						$uploaded_file['filename'],
						$uploaded_file['size'],
						$url_delete,
						$isInserted['id_file'],
						asset('/')
					);

					$this->status = true;
					$this->message  = sprintf(ALERT_SUCCESS,trans("general.M0110"));
				}
			}else{
				$this->jsondata = ___error_sanatizer($validator->errors());
			}

			return response()->json([
				'data'      => $this->jsondata,
				'status'    => $this->status,
				'message'   => $this->message
			]);
		}

		/**
         * [This method is used for uploading premium document] 
         * @param  Request
         * @return Json Response
         */

		public function premium_document_upload(Request $request){
			$validator = \Validator::make($request->all(), [
				"file"            => validation('document'),
			],[
				'file.validate_file_type'  => trans('general.M0119'),
			]);
			if($validator->passes()){
				$folder = 'uploads/cv/';

				$uploaded_file = upload_file($request,'file',$folder);
				$data = [
					'user_id' => $request->id_user,
					'reference' => 'users',
					'filename' => $uploaded_file['filename'],
					'extension' => $uploaded_file['extension'],
					'folder' => $folder,
					'type' => 'cv',
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
						url('ajax/%s?id_file=%s'),
						DELETE_DOCUMENT,
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
				}
			}else{
				$this->jsondata = ___error_sanatizer($validator->errors());
			}

			return response()->json([
				'data'      => $this->jsondata,
				'status'    => $this->status,
				'message'   => $this->message
			]);
		}	

		/**
         * [This method is used for adding employer 
         * @param  request
         * @return \Illuminate\Http\Response
         */	

		public function add_employer(Request $request){
			$data['page'] = $request->page;
			$data['page_title']         = 'Add Talent';
			$data['uri_placeholder']    = $this->URI_PLACEHOLDER;
			$data['backurl']            = url(sprintf('%s/users/employer', $this->URI_PLACEHOLDER));
			$data['url'] = url(sprintf('%s/employer-users/add', $this->URI_PLACEHOLDER));

			return view('backend.employer.add')->with($data);
		}

		/**
         * [This method is used for inserting employer] 
         * @param  Request
         * @return Json SResponse
         */

		public function insert_employer(Request $request){

			$validator = \Validator::make($request->all(), [
				'first_name'            => validation('first_name'),
				'last_name'             => validation('last_name'),
				'company_name'          => validation('company_name'),
				'email'                 => ['required','email',Rule::unique('users')->ignore('trashed','status')]
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
			]);

			if ($validator->passes()) {
				$dosignup = \Models\Employers::__dosignup($request);
				$email = $request->email;
				$field    = ['id_user','type','first_name','last_name','name','email','status'];

				if(!empty($dosignup['status'])){
					$talent = \Models\Talents::findById($dosignup['signup_user_id'],$field);

					if(!empty($talent) && $talent->status == 'pending'){
						if(!empty($email)){
							$code                   = bcrypt(__random_string());
							$emailData              = ___email_settings();
							$emailData['email']     = $email;
							$emailData['name']      = $request->first_name;
							$emailData['link']      = url(sprintf("create/account?token=%s",$code));

							\Models\Talents::change($dosignup['signup_user_id'],['remember_token' => $code,'updated' => date('Y-m-d H:i:s')]);

							___mail_sender($email,sprintf("%s %s",$request->first_name,$request->last_name),"employer_signup_admin",$emailData);
						}
					}else{
						if(!empty($email)){
							$emailData              = ___email_settings();
							$emailData['email']     = $email;
							$emailData['name']      = $request->first_name;

							$code                   = bcrypt(__random_string());
							$emailData['link']      = url(sprintf("create/account?token=%s",$code));

							\Models\Talents::change($dosignup['signup_user_id'],['remember_token' => $code,'updated' => date('Y-m-d H:i:s')]);

							___mail_sender($email,sprintf("%s %s",$request->first_name,$request->last_name),"employer_signup_admin",$emailData);
						}
					}

					$this->status = true;
					$this->message = 'User information has been added successfully.';
					$this->redirect = url(sprintf("%s/users/employer",ADMIN_FOLDER));
				}else{
					$this->status = false;
					$this->message = 'Error.';
					$this->redirect = url(sprintf("%s/users/employer",ADMIN_FOLDER));
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
         * [This method is used for employer edit] 
         * @param Request
         * @return \Illuminate\Http\Response
         */

		public function edit_employer(Request $request){
			$id_user 							= ___decrypt($request->user_id);
			$data['page'] 						= $request->page;
			$data['page_title']     			= 'Edit Employer';
			$data['id_user']        			= $id_user;

			// $data['picture']        			= get_file_url(\Models\Talents::get_file(sprintf(" type = 'profile' AND user_id = %s",$id_user),'single',['filename','folder']));
			$profileUrl        					= (\Models\Talents::get_file(sprintf(" type = 'profile' AND user_id = %s",$id_user),'single',['filename','folder']));
			

            
			$data['user']           			= (array)\Models\Employers::findById($id_user);
			
			if(empty($profileUrl) && empty($data['user']['social_picture'])){
                $data['picture']  = get_file_url($profileUrl);
            }elseif (!empty($profileUrl)) {
                $data['picture']  = get_file_url($profileUrl);
            }elseif (!empty($data['user']['social_picture'])) {
                $data['picture'] = $data['user']['social_picture'];
            }
			
			$data['url'] 						= url(sprintf('%s/users/employer/edit?user_id=%s',ADMIN_FOLDER,___encrypt($id_user)));
			$data['backurl']        			= url(sprintf('%s/users/employer', $this->URI_PLACEHOLDER));
			$data['country_phone_codes']        = \Cache::get('country_phone_codes');
			$data['company_work_field_name']    = \Models\Listings::getWorkFieldByID($data['user']['company_work_field']);
			$data['countries']              	= \Models\Listings::getCountry();
			$data['states']                 	= (array)\Models\Listings::getStateByCountryID($data['user']['country']);

			$data['get_files']              	= \Models\Employers::get_file(sprintf("user_id = %s AND type = 'certificates' ", $id_user));

			return view('backend.employer.edit')->with($data);
		}

		public function edit_employer_activity_log(Request $request, Builder $htmlBuilder){

			$id_user 			= ___decrypt($request->user_id);
			$data['page'] 		= 'activity_log';
			$data['page_title'] = 'Edit Employer';
			$data['id_user']    = $id_user;
			$data['picture']    = get_file_url(\Models\Talents::get_file(sprintf(" type = 'profile' AND user_id = %s",$id_user),'single',['filename','folder']));
			$data['user']       = (array)\Models\Employers::findById($id_user);
			$data['url'] 		= url(sprintf('%s/users/employer/edit?user_id=%s',ADMIN_FOLDER,___encrypt($id_user)));
			$data['countries']  = \Models\Listings::getCountry();
			$data['states']     = (array)\Models\Listings::getStateByCountryID($data['user']['country']);
			$data['backurl']    = url(sprintf('%s/users/employer', $this->URI_PLACEHOLDER));

			if($request->ajax()) {
	        	return $this->edit_employeractivity_log_talent_list($request);
	        }

	        if($request->download && $request->download =='csv'){

				$csvdata = $this->edit_employeractivity_log_talent_list($request)->getData(true);
				$csvdata = $csvdata['data'];

				$file_name = 'employer_activity_log_'.time().'.csv';

				$headers = [
	                "Content-type" => "application/csv",
	                "Content-Disposition" => "attachment; filename={$file_name}",
	                "Pragma" => "no-cache",
	                "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
	                "Expires" => "0"
	            ];
	            $callback = function() use ($csvdata)
	            {
	                $file = fopen('php://output', 'w');
	                fputcsv($file,['Name','Activity','Reference ID','Reference Type','Reference Name','Date']);

	                foreach ($csvdata as $cdata) {
	                    # code...
	                    fputcsv($file, [$cdata['name'],$cdata['user_activity'],$cdata['reference_id'],$cdata['reference_type'],$cdata['reference_name'],$cdata['created']]);
	                }

	                fclose($file);
	            };

	            $response = new StreamedResponse();
	            $response->setCallback($callback);

	            foreach ($headers as $header_key => $header_val) {
	            	$response->headers->set($header_key, $header_val);
	            }
	            // @codeCoverageIgnoreEnd
	            $response->send(); 
	            return;
			}	        

			$data['html'] = $htmlBuilder
				->addColumn(['data' => 'name','name' => 'users.name','title' => 'Name'])
				->addColumn(['data' => 'user_activity','name' => 'activity.user_activity','title' => 'Activity','searchable' => false,'orderable'  => false])
				->addColumn(['data' => 'reference_id','name' => 'activity.reference_id','title' => 'Reference ID'])
				->addColumn(['data' => 'reference_type','name' => 'activity.reference_type','title' => 'Reference Type'])
				->addColumn(['data' => 'reference_name','name' => 'activity.reference_name','title' => 'Reference Name','searchable' => false,'orderable'  => false])
				->addColumn(['data' => 'created','name' => 'activity.created','title' => 'Date']);

			return view('backend.employer.edit')->with($data); 
		}

		private function edit_employeractivity_log_talent_list(Request $request){

			$id_user = ___decrypt($request->user_id);

			$prefix  = DB::getTablePrefix();
        	$project = \Models\ActivityLog::select([
		            'activity.user_id',
		            'activity.action as user_activity',
		            'activity.reference_id',
		            'activity.reference_type',
		            'activity.created',
					\DB::Raw('CONCAT('.$prefix.'users.first_name, " ", '.$prefix.'users.last_name ) AS name'),
					\DB::raw('IF('.$prefix.'activity.reference_type = "projects",'.$prefix.'projects.title,"-") AS reference_name')
		        ])
        		->whereIn('activity.action',['login','find-talents','employer-post-job','employer-cancel-job','employer-delete-job','proposal-details','employer-payment-complete-job','employer-close-job','raise-dispute'])
        		->where('user_type','employer')
		        ->leftJoin('users','users.id_user','=','activity.user_id')
		        ->leftJoin('projects','projects.id_project','=','activity.reference_id');

		        if($request->start_date && $request->end_date){
		        	$project->where('activity.created','>=', $request->start_date)
		        			->where('activity.created','<=', $request->end_date);
		        }

		        $project->where('activity.user_id', $id_user);
		        $project->orderBy('activity.id_activity', 'DESC');

	        	return \Datatables::eloquent($project)
	        	->editColumn('user_activity',function($item){
	        		return ucfirst(str_replace('-', ' ', $item->user_activity));
	        	})
	        	->editColumn('reference_type',function($item){
	        		return ucfirst($item->reference_type);
	        	})
	        	->editColumn('created',function($item){
	        		return $item->created? date('d F Y', strtotime($item->created)) : '-';
	        	})
	        	->make(true);
		}
		

		/**
         * [This method is used for updating employer] 
         * @param  Request
         * @return Json SResponse
         */

		public function update_employer(Request $request){

			$validator = \Validator::make($request->all(), [
				'first_name'                => validation('first_name'),
				'last_name'                 => validation('last_name'),
				'birthday'                  => array_merge(['min_age:14'],validation('birthday')),

				'mobile'                    => array_merge([Rule::unique('users')->ignore('trashed','status')->where(function($query) use($request){$query->where('id_user','!=',$request->id_user);})],validation('mobile')),
				'country_code'   			=> $request->mobile ? array_merge(['required'], validation('country_code')) : validation('country_code'),
				'other_mobile'              => array_merge([Rule::unique('users')->ignore('trashed','status')->where(function($query) use($request){$query->where('id_user','!=',$request->id_user);})],validation('mobile'),['different:mobile']),
				'other_country_code'   		=> $request->other_mobile ? array_merge(['required'], validation('country_code')) : validation('country_code'),
				'website'                   => validation('website'),
				'address'                   => validation('address'),
				'country'                   => validation('country'),
				'state'                     => validation('state'),
				'postal_code'               => validation('postal_code'),
			],[
				'first_name.required'       	=> trans('general.M0006'),
				'first_name.regex'          	=> trans('general.M0007'),
				'first_name.string'         	=> trans('general.M0007'),
				'first_name.max'            	=> trans('general.M0020'),
				'last_name.required'        	=> trans('general.M0008'),
				'last_name.regex'           	=> trans('general.M0009'),
				'last_name.string'          	=> trans('general.M0009'),
				'last_name.max'             	=> trans('general.M0019'),
				'birthday.string'           	=> trans('general.M0054'),
				'birthday.regex'            	=> trans('general.M0054'),
				'birthday.min_age'          	=> trans('general.M0055'),
				'birthday.validate_date'    	=> trans('general.M0506'),
				'mobile.required'           	=> trans('general.M0030'),
				'mobile.regex'              	=> trans('general.M0031'),
				'mobile.string'             	=> trans('general.M0031'),
				'mobile.min'                	=> trans('general.M0032'),
				'mobile.max'                	=> trans('general.M0033'),
				'mobile.unique'             	=> trans('general.M0197'),
				'address.string'            	=> trans('general.M0057'),
				'address.regex'             	=> trans('general.M0057'),
				'address.max'               	=> trans('general.M0058'),
				'country.integer'           	=> trans('general.M0059'),
				'state.integer'             	=> trans('general.M0060'),
				'postal_code.string'        	=> trans('general.M0061'),
				'postal_code.regex'         	=> trans('general.M0061'),
				'postal_code.max'           	=> trans('general.M0062'),
				'postal_code.min'           	=> trans('general.M0063'),
                'country_code.required'         => trans('general.M0164'),
                'country_code.string'           => trans('general.M0074'),
                'other_country_code.required'   => trans('general.M0432'),
                'other_country_code.string'     => trans('general.M0074'),				
			]);

			if ($validator->passes()) {
				$is_updated = DB::table('users')
				->where('id_user', $request->id_user)
				->update([
					'first_name'    		=> $request['first_name'],
					'last_name'     		=> $request['last_name'],
					'name'          		=> $request['first_name'] . ' ' . $request['last_name'],
					'country_code'        	=> $request['country_code'],
					'mobile'        		=> $request['mobile'],
					'other_country_code'  	=> $request['other_country_code'],
					'other_mobile'  		=> $request['other_mobile'],
					'address'  				=> $request['address'],
					'country'  				=> $request['country'],
					'state'  				=> $request['state'],
					'postal_code'  			=> $request['postal_code'],
					'website'  				=> $request['website'],
				]);

				$this->status = true;
				$this->message = 'User information has been updated successfully.';
				$this->redirect = url(sprintf("%s/users/employer",ADMIN_FOLDER));
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
         * [This method is used for editing subadmin] 
         * @param  request
         * @return \Illuminate\Http\Response
         */

		public function edit_subadmin(Request $request){
			$data['page'] 						= $request->page;
			$data['page_title']      			= 'Edit Sub Admin';
			$id_user         					= ___decrypt($request->user_id);
			$data['id_user']         			= ___decrypt($request->user_id);
			$data['user']            			= (array)json_decode(json_encode(DB::table('users')
				->where('id_user', $id_user)->get()->first()),true);
			$data['menu_visibility'] 			= \Models\Administrator::getSubAdminPermission($id_user);
			$data['menu_visibility'] 			= $data['menu_visibility']['menu_visibility'];
			$data['uri_placeholder']    		= $this->URI_PLACEHOLDER;
			$data['backurl']        			= url(sprintf('%s/users/sub-admin', $this->URI_PLACEHOLDER));
			$data['url'] 						= url(sprintf('%s/employer-users/'.$id_user.'/edit', $this->URI_PLACEHOLDER));
			$data['menu_visibility'] 			= json_decode($data['menu_visibility']);

			return view('backend.subadmin.edit')->with($data);
		}

		/**
         * [This method is used for updating subadmin] 
         * @param  Request
         * @return Json Response
         */
        
		public function update_subadmin(Request $request){
			$validator = \Validator::make($request->all(), [
				'first_name'                => validation('first_name'),
				'last_name'                 => validation('last_name'),
				'menus'                     => ['required'],
			],[
				'first_name.required'       => trans('general.M0006'),
				'first_name.regex'          => trans('general.M0007'),
				'first_name.string'         => trans('general.M0007'),
				'first_name.max'            => trans('general.M0020'),
				'last_name.required'        => trans('general.M0008'),
				'last_name.regex'           => trans('general.M0009'),
				'last_name.string'          => trans('general.M0009'),
				'last_name.max'             => trans('general.M0019'),
				'menus.required'            => trans('general.select_menu_permission'),
			]);
			if ($validator->passes()) {
				$is_updated = DB::table('users')
				->where('id_user', $request->id_user)
				->update([
					'first_name'    => $request['first_name'],
					'last_name'     => $request['last_name'],
					'name'          => $request['first_name'] . ' ' . $request['last_name'],

				]);

				$permission['id_user'] = $request->id_user;
				$permission['menu_visibility'] = json_encode($request->menus);
				$permission['created'] = date('Y-m-d H:i:s');
				$permission['updated'] = date('Y-m-d H:i:s');
				\Models\Administrator::createSubAdminPermission($request->id_user, $permission);

				$this->status = true;
				$this->message = 'User information has been updated successfully.';
				$this->redirect = url(sprintf('%s/users/sub-admin',ADMIN_FOLDER));
			} else {
				if(empty($request->menus)){
					$validator->getMessageBag()->add('menus_error', trans('general.select_menu_permission'));
				}
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
         * [This method is used for adding sub admin] 
         * @param Request
         * @return \Illuminate\Http\Response
         */
        
		public function add_subadmin(Request $request){
			$data['page'] 			= $request->page;
			$data['page_title']     = 'Add Security Manager';
			$data['url'] 			= url(sprintf('%s/users/sub-admin/add', ADMIN_FOLDER));
			$data['backurl']        = url(sprintf('%s/users/sub-admin', $this->URI_PLACEHOLDER));
			return view('backend.subadmin.add')->with($data);
		}

		/**
         * [This method is used for inserting sub admin] 
         * @param  Request
         * @return Json Response
         */
        
		public function insert_subadmin(Request $request){
			$validator = \Validator::make($request->all(), [
				'first_name'            => validation('first_name'),
				'last_name'             => validation('last_name'),
				'email'                 => ['required','email',Rule::unique('users')->ignore('trashed','status')],
				'menus'             => ['required'],
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
				'menus.required'                    => trans('general.select_menu_permission'),
			]);

			if ($validator->passes()) {
				$dosignup = \Models\Administrator::createSubAdmin($request);
				$email = $request->email;
				$field    = ['id_user','type','first_name','last_name','name','email','status'];

				if((bool)$dosignup['status']){
					$menus = $request->menus;
					array_push($menus, '1');
					// array_push($menus, '90');
					// array_push($menus, '91');
					
					$permission['id_user'] = $dosignup['signup_user_id'];
					$permission['menu_visibility'] = json_encode($menus);
					$permission['created'] = date('Y-m-d H:i:s');
					$permission['updated'] = date('Y-m-d H:i:s');
					\Models\Administrator::createSubAdminPermission($dosignup['signup_user_id'], $permission);

					$talent = \Models\Talents::findById($dosignup['signup_user_id'],$field);

					if(!empty($talent) && $talent->status == 'pending'){
						if(!empty($email)){
							$code                   = bcrypt(__random_string());
							$emailData              = ___email_settings();
							$emailData['email']     = $email;
							$emailData['name']      = $request->first_name;
							$emailData['link']      = url(sprintf("administrator/create-subadmin/account?token=%s",$code));

							\Models\Talents::change($dosignup['signup_user_id'],['remember_token' => $code,'updated' => date('Y-m-d H:i:s')]);

							___mail_sender($email,sprintf("%s %s",$request->first_name,$request->last_name),"subadmin_signup_admin",$emailData);
						}
					}else{
						if(!empty($email)){
							$emailData              = ___email_settings();
							$emailData['email']     = $email;
							$emailData['name']      = $request->first_name;

							___mail_sender($email,sprintf("%s %s",$request->first_name,$request->last_name),"subadmin_signup_admin",$emailData);
						}
					}

					$this->status = true;
					$this->message = 'User information has been added successfully.';
					$this->redirect = url(sprintf('%s/users/sub-admin',ADMIN_FOLDER));
				}else{
					$this->status = false;
					$this->message = 'Error.';
					$this->redirect = url(sprintf('%s/users/sub-admin',ADMIN_FOLDER));
				}
			} else {
				if(empty($request->menus)){
					$validator->getMessageBag()->add('menus_error', trans('general.select_menu_permission'));
				}				
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
         * [This method is used for adding premium] 
         * @param  Request
         * @return \Illuminate\Http\Response
         */
        
		public function add_premium(Request $request){

			$data['page'] = $request->page;
			$data['page_title']         	= 'Add Premium Talent';
			$data['uri_placeholder']    	= $this->URI_PLACEHOLDER;
			$data['industries_name']    	= \Cache::get('industries_name');
            $data['subindustries_name'] 	= \Cache::get('subindustries_name');
			$data['backurl']            	= url(sprintf('%s/users/premium', $this->URI_PLACEHOLDER));
			$data['url'] 					= url(sprintf('%s/premium-users/add', $this->URI_PLACEHOLDER));
			$data['picture'] 				= asset('images/avatar.png');
			$data['countries']              = \Models\Listings::getCountry();
			$data['industries']             = (array)\Models\Listings::getIndustry();

			return view('backend.premium.add')->with($data);
		}

		/**
         * [This method is used for premium insertion] 
         * @param  Request
         * @return Json Response
         */

		public function insert_premium(Request $request){

			$validator = \Validator::make($request->all(), [
				'first_name'            => validation('first_name'),
				'last_name'             => array_merge(validation('last_name'),['required']),
				'email'                 => ['required','email',Rule::unique('users')->ignore('trashed','status')],
				'country'                   => array_merge(validation('country'),['required']),
				'state'                     => array_merge(validation('state'),['required']),
				'city'                     => array_merge(validation('city'),['required']),
				'postal_code'               => array_merge(validation('postal_code'),['required']),

				'industry'              => array_merge(validation('industry'),['required']),
				'subindustry'              => array_merge(validation('industry'),['required']),
				'expertise'              => array_merge(validation('expertise'),['required']),
				'experience'              => array_merge(validation('experience'),['required']),
				"file"            => array_merge(validation('document'),['required']),

			],[
				'file.validate_file_type'  => trans('general.M0119'),
				'first_name.required'       => trans('general.M0006'),
				'first_name.regex'          => trans('general.M0007'),
				'first_name.string'         => trans('general.M0007'),
				'first_name.max'            => trans('general.M0020'),
				'last_name.required'        => trans('general.M0008'),
				'last_name.regex'           => trans('general.M0009'),
				'last_name.string'          => trans('general.M0009'),
				'last_name.max'             => trans('general.M0019'),
				'email.required'                    => trans('general.M0010'),
				'email.email'                       => trans('general.M0011'),
				'email.unique'                      => trans('general.M0012'),
				'country.integer'           => trans('general.M0059'),
				'state.integer'             => trans('general.M0060'),
				'postal_code.string'        => trans('general.M0061'),
				'postal_code.regex'         => trans('general.M0061'),
				'postal_code.max'           => trans('general.M0062'),
				'postal_code.min'           => trans('general.M0063'),

				'industry.required'           => trans('admin.A0052'),
				'subindustry.required'           => trans('admin.sub_industry_required'),
				'expertise.required'           => trans('admin.expertise_required'),
				'experience.required'           => trans('admin.experience_required'),
			]);

			if ($validator->passes()) {
				$is_insert = DB::table('users')
				->insertGetId([
					'first_name'    => $request['first_name'],
					'last_name'     => $request['last_name'],
					'name'          => $request['first_name'] . ' ' . $request['last_name'],
					'country'  => $request['country'],
					'state'  => $request['state'],
					'city'  => $request['city'],
					'postal_code'  => $request['postal_code'],
					'email'  => $request['email'],
					'type'  => 'premium',
					'industry'  => $request['industry'],
					'subindustry'  => $request['subindustry'],
					'expertise'  => $request['expertise'],
					'experience'  => $request['experience'],
					'updated'  => date('Y-m-d H:i:s'),
					'created'  => date('Y-m-d H:i:s'),
				]);

				\Models\Talents::update_skill($is_insert,$request['skill'],$request['subindustry']);

				if($is_insert){
					/*Upload Resume*/
					$folder = 'uploads/certificates/';
					$uploaded_file = upload_file($request,'file',$folder);
					$data = [
						'user_id' => $is_insert,
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
					/*Upload Resume*/

					$this->status = true;
					$this->message = 'User information has been added successfully.';
					$this->redirect = url(sprintf("%s/users/premium",ADMIN_FOLDER));
				}else{
					$this->status = false;
					$this->message = 'Error.';
					$this->redirect = url(sprintf("%s/users/premium",ADMIN_FOLDER));
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
         * [This method is used for premium edit] 
         * @param  Request
         * @return \Illuminate\Http\Response
         */

		public function edit_premium(Request $request, Builder $htmlBuilder, $type = 'talent'){
			$id_user 							= ___decrypt($request->user_id);
			$data['encrypt_user_id']			= $request->user_id;
			$data['page']                       = $request->page;
			$data['page_title']                 = 'Edit Talent';
			$data['picture']                    = get_file_url(\Models\Talents::get_file(sprintf(" type = 'profile' AND user_id = %s",$id_user),'single',['filename','folder']));
			$data['id_user']                    = $id_user;
			$data['user']                       = (array)\Models\Talents::findById($id_user);
			$data['backurl']            		= url(sprintf('%s/users/premium', $this->URI_PLACEHOLDER));
			$data['url']                        = url(sprintf('%s/users/talent/edit?user_id=%s', ADMIN_FOLDER, ___encrypt($id_user)));
			$data['country_phone_codes']        = \Cache::get('country_phone_codes');
			$data['countries']                  = \Models\Listings::getCountry();
			$data['states']                     = (array)\Models\Listings::getStateByCountryID($data['user']['country']);
			$data['cities']                     = (array)\Models\Listings::getCityByStateID($data['user']['state']);
			$data['industries_name']    		= \Cache::get('industries_name');
            $data['subindustries_name'] 		= \Cache::get('subindustries_name');			
			$data['industries']                 = (array)\Models\Listings::getIndustry();
			$data['subindustries']              = (array)\Models\Listings::getSubIndustry($data['user']['industry']);
			$data['interested']                 = \Models\Talents::interested_in($id_user);
			$data['user_certificates']          = \Models\Talents::certificates($id_user);
			$data['user_skill']                 = \Models\Talents::skills($id_user);
			$data['all_skill']                  = \Models\Listings::getSkillByIndustry($data['user']['subindustry']);
			$data['work_experiences']           = \Models\Talents::work_experiences($id_user);
			$data['certificate_attachments']    = \Models\Talents::get_file(sprintf(" user_id = %s AND type = 'cv' ",$id_user),'all',['id_file','filename','folder','size']);
			$data['availability']               = \Models\Talents::get_availability($id_user);
			$data['education_list']             = \Models\Talents::educations($id_user);
			$data['get_files']                  = \Models\Talents::get_file(sprintf("user_id = %s AND type = 'certificates' ", $id_user));
			$data['db_degree']                  = (array)\Models\Listings::getDegree();
			$data['work_experience_list']       = \Models\Talents::work_experiences($id_user);
			$data['certificate_list']           = \Models\Listings::getCertificate();
			$result                             = Interview::getQuestionResponse($id_user);
			$data['questionList']               = $result['questionList'];

			return view('backend.premium.basic')->with($data);
		}

		/**
         * [This method is used for update premium] 
         * @param  Request
         * @return Json Response
         */

		public function update_premium(Request $request){

			$id_user = ___decrypt($request->user_id);
			$validation_mobile = validation('phone_number'); unset($validation_mobile[0]);

			$validator = \Validator::make($request->all(), [
				'first_name'                => validation('first_name'),
				'last_name'                 => validation('last_name'),
				'country'                   => validation('country'),
				'state'                     => validation('state'),
				'postal_code'               => validation('postal_code'),

				'industry'              => array_merge(validation('industry'),['required']),
				'subindustry'              => array_merge(validation('industry'),['required']),
				'expertise'              => array_merge(validation('expertise'),['required']),
				'experience'              => array_merge(validation('experience'),['required']),
			],[
				'first_name.required'       => trans('general.M0006'),
				'first_name.regex'          => trans('general.M0007'),
				'first_name.string'         => trans('general.M0007'),
				'first_name.max'            => trans('general.M0020'),
				'last_name.required'        => trans('general.M0008'),
				'last_name.regex'           => trans('general.M0009'),
				'last_name.string'          => trans('general.M0009'),
				'last_name.max'             => trans('general.M0019'),
				'country.integer'           => trans('general.M0059'),
				'state.integer'             => trans('general.M0060'),
				'postal_code.string'        => trans('general.M0061'),
				'postal_code.regex'         => trans('general.M0061'),
				'postal_code.max'           => trans('general.M0062'),
				'postal_code.min'           => trans('general.M0063'),

				'industry.required'           => trans('admin.A0052'),
				'subindustry.required'           => trans('admin.sub_industry_required'),
				'expertise.required'           => trans('admin.expertise_required'),
				'experience.required'           => trans('admin.experience_required'),
			]);

			if ($validator->passes()) {
				$is_updated = DB::table('users')
				->where('id_user', $id_user)
				->update([
					'first_name'    => $request['first_name'],
					'last_name'     => $request['last_name'],
					'name'          => $request['first_name'] . ' ' . $request['last_name'],
					'country'  => $request['country'],
					'state'  => $request['state'],
					'postal_code'  => $request['postal_code'],
					'website'  => $request['website'],

					'industry'  => $request['industry'],
					'subindustry'  => $request['subindustry'],
					'expertise'  => $request['expertise'],
					'experience'  => $request['experience'],
					'updated'  => date('Y-m-d H:i:s'),
				]);

				\Models\Talents::update_skill($id_user,$request['skill'],$request['subindustry']);

				$this->status = true;
				$this->message = 'User information has been updated successfully.';
				$this->redirect = url(sprintf("%s/users/premium",ADMIN_FOLDER));
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
         * [This method is used for randering view of admin page] 
         * @param  null
         * @return \Illuminate\Http\Response
         */

		public function page(){
			$data['page_title']         = 'Static Pages';
			$data['uri_placeholder']    = $this->URI_PLACEHOLDER;
			$data['url']                = url(sprintf('%s/', $this->URI_PLACEHOLDER));

			return view('backend.page.list')->with($data);
		}

		/**
         * [This method is used for page edit] 
         * @param  Id
         * @return \Illuminate\Http\Response
         */

		public function editpage($id_page){
			$data['page_title']         = 'Edit Statis Page';
			$data['page']             = \Models\Listings::pages('first',['*'],"id = {$id_page}");
			$data['uri_placeholder']    = $this->URI_PLACEHOLDER;
			$data['url']                = url(sprintf('%s/', $this->URI_PLACEHOLDER));

			return view(sprintf("%s.%s","backend","page.edit"))->with($data);
		}

		/**
         * [This method is used for update page] 
         * @param  Request , Id
         * @return \Illuminate\Http\Response
         */

		public function updatepage(Request $request, $id_page){
			$validator = \Validator::make($request->all(), [
				'title' => ['required'],
			],[
				'title.required'=>'Please enter page title.',
			]);

			if ($validator->passes()) {
				$is_updated = DB::table('pages')
				->where('id', ($id_page))
				->update([
					'title' => $request['title'],
					'content'=>(string)$request['content']
				]);

				$request->session()->flash('success', 'Static page has been updated successfully.');
				return redirect(ADMIN_FOLDER.'/pages');
			} else {
				$this->status = false;
				return redirect()->back()->withErrors($validator, env('DEFAULT_BACKEND_LAYOUT_FOLDER'))->withInput();
			}
		}

		/**
         * [This method is used for question listing] 
         * @param  Request
         * @return \Illuminate\Http\Response
         */

		public function questionList(Request $request, Builder $htmlBuilder){
			$data['page_title']         = 'Interview questions list';
			$data['uri_placeholder']    = $this->URI_PLACEHOLDER;
			$data['url']                = url(sprintf('%s/', $this->URI_PLACEHOLDER));
			
			if ($request->ajax()) {
				$questionList = Interview::getQuestionListByType();
				return \Datatables::of($questionList)
				->editColumn('status',function($questionList){
					return ucfirst($questionList->status);
				})                
				->editColumn('action',function($questionList){
					$html = '<a href="'.url(sprintf('%s/interview/question/edit?id_question=%s',ADMIN_FOLDER,$questionList->id)).'"class="badge bg-light-blue">Edit</a>  ';
					if($questionList->status == 'active'){
						$html .= '<a 
						href="javascript:void(0);" 
						data-url="'.url(sprintf('%s/ajax/question/status?id_question=%s&status=inactive',ADMIN_FOLDER,$questionList->id)).'" 
						data-request="status-request" 
						data-ask="Do you really want to continue with this action?" 
						class="badge bg-green">Inactive</a>  ';
					}else{
						$html .= '<a 
						href="javascript:void(0);" 
						data-url="'.url(sprintf('%s/ajax/question/status?id_question=%s&status=active',ADMIN_FOLDER,$questionList->id)).'" 
						data-request="status-request" 
						data-ask="Do you really want to continue with this action?" 
						class="badge bg-green">Active</a>  ';                        
					}

					$html .= '<a 
						href="javascript:void(0);" 
						data-url="'.url(sprintf('%s/ajax/question/status?id_question=%s&status=delete',ADMIN_FOLDER,$questionList->id)).'" 
						data-request="status-request" 
						data-ask="Do you really want to continue with this action?" 
						class="badge bg-red">Delete</a>  ';
					// $html = '<a href="javascript:void(0);"class="badge bg-light-blue">Edit</button>';
					return $html;
				})
				->make(true);
			}

			$data['html'] = $htmlBuilder
			->addColumn(['data' => 'row_number', 'name' => 'row_number', 'title' => '#', 'width' => '1'])
			->addColumn(['data' => 'industry', 'name' => 'industry', 'title' => 'Industry'])
			->addColumn(['data' => 'question', 'name' => 'question', 'title' => 'Question'])
			->addColumn(['data' => 'question_type', 'name' => 'question_type', 'title' => 'Question Type'])
			->addColumn(['data' => 'status', 'name' => 'status', 'title' => 'Status'])
			->addColumn(['data' => 'action', 'name' => 'action', 'title' => 'Actions','width' => '130','searchable' => false, 'orderable' => false]);
			
			return view('backend.interview.question-list')->with($data);
		}

		/**
         * [This method is used for adding interview question] 
         * @param  Request
         * @return \Illuminate\Http\Response
         */

		public function add_interview_question(Request $request){
			$data['page_title']         = 'Add Interview Question';
			$data['question_type']      = json_decode(json_encode(Interview::getQuestionType(),true));
			$data['subindustries_name'] = \Cache::get('subindustries_name');
			$data['backurl']            = url(sprintf('%s/', ADMIN_FOLDER.'/question-list'));

			return view('backend.interview.add-question')->with($data);
		}

		/**
         * [This method is used for adding question] 
         * @param  Request
         * @return Json Response
         */

		public function add_question(Request $request){
			$validator = \Validator::make($request->all(),[
				'id_industry'   => ['required','integer'],
				'question'      => ['required'],
				'question_type' => ['required','integer']
			],[
				'id_industry.required'      => 'The Industry field is required.',
				'id_industry.integer'       => 'The Industry format is invalid.',
				'question.required'         => 'The Question field is required.',
				'question_type.required'    => 'The Question Type field is required.',
				'question_type.integer'     => 'The Question Type format is invalid.',
			]);

			if($validator->passes()){
				$questionData = [
					'id_industry'   => $request->id_industry,
					'question'      => $request->question,
					'status'        => 'active',
					'created'       => date('Y-m-d H:i:s'),
					'updated'       => date('Y-m-d H:i:s')
				];

				$isInserted = Interview::saveQuestion($questionData);
				if($isInserted){
					$question_relationData = [
						'id_question'       => $isInserted, 
						'id_question_type'  => $request->question_type,
					];
					$isInserted = Interview::saveQuestionRelation($question_relationData);
					$this->status   = true;
					$this->message  = 'Question has been submitted successfully.';
					$this->redirect = url(sprintf('%s/question-list',ADMIN_FOLDER));                    
				}
			}else {
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
         * [This method is used for interview question edit] 
         * @param Request
         * @return \Illuminate\Http\Response
         */

		public function edit_interview_question(Request $request){
			$data['page_title']         = 'Add Interview Question';
			$data['question_type']      = json_decode(json_encode(Interview::getQuestionType(),true));
			$data['subindustries_name'] = \Cache::get('subindustries_name');
			$data['question_data']      = json_decode(json_encode(Interview::getQuestionById($request->id_question),true));
			// dd($data['question_data']);
			$data['backurl']            = url(sprintf('%s/', ADMIN_FOLDER.'/question-list'));

			return view('backend.interview.edit-question')->with($data);   
		}

		/**
         * [This method is used for question edit] 
         * @param  Request
         * @return Json Response
         */

		public function edit_question(Request $request){
			$validator = \Validator::make($request->all(),[
				'id_industry'   => ['required','integer'],
				'question'      => ['required'],
				'question_type' => ['required','integer']
			],[
				'id_industry.required'      => 'The Industry field is required.',
				'id_industry.integer'       => 'The Industry format is invalid.',
				'question.required'         => 'The Question field is required.',
				'question_type.required'    => 'The Question Type field is required.',
				'question_type.integer'     => 'The Question Type format is invalid.',
			]);

			if($validator->passes()){
				$questionData = [
					'id_industry'   => $request->id_industry,
					'question'      => $request->question,
					'updated'       => date('Y-m-d H:i:s')
				];

				$isUpdated = Interview::update_question($request->id_question,$questionData);
				if($isUpdated){
					$question_relationData = [ 
						'id_question_type'  => $request->question_type,
					];
					$isUpdated      = Interview::updateQuestionRelation($request->id_question,$question_relationData);
					$this->status   = true;
					$this->message  = 'Question has been updated successfully.';
					$this->redirect = url(sprintf('%s/question-list',ADMIN_FOLDER));                    
				}
			}else {
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
         * [This method is used for message listing] 
         * @param  Request
         * @return \Illuminate\Http\Response
         */

		public function message_list(Request $request){

			switch ($request->messages_status) {
				case 'inbox':
					$data['page_title'] = 'Inbox';
					break;
				case 'closed':
					$data['page_title'] = 'Closed';
					break;
				case 'trashed':
					$data['page_title'] = 'Trashed';
					break;
				default:
					# code...
					break;
			}
			$data['page'] = $request->messages_status;

			$data['uri_placeholder']    = $this->URI_PLACEHOLDER;
			$data['url']                = url(sprintf('%s/', $this->URI_PLACEHOLDER));

			return view('backend.messages.messages-list')->with($data);
		}

		/**
         * [This method is used for detail message] 
         * @param Request
         * @return \Illuminate\Http\Response
         */

		public function message_detail(Request $request){
			$data['id_message'] 		= $request->id_message;
			$data['page_title']         = 'View Message';
			$data['uri_placeholder']    = $this->URI_PLACEHOLDER;
			$data['backurl']            = url(sprintf('%s/', $this->URI_PLACEHOLDER));
			$data['url'] 				= url(sprintf('%s/sub-admin-users/add', $this->URI_PLACEHOLDER));
			$data['message']            = \Models\Administrator::getMessageByID($request->id_message);
			$data['message_replay']     = \Models\Administrator::getMessageReplyByID($request->id_message);
			return view('backend.messages.message-detail')->with($data);
		}

		/**
         * [This method is used for replying message] 
         * @param  Request
         * @return Json SResponse
         */

		public function message_replay(Request $request){
			$validator = \Validator::make($request->all(), [
				'message_content'       => ['required'],
			],[
				'message_content.required' => trans('admin.message_required'),
			]);

			if ($validator->passes()) {
				$message = \Models\Administrator::getMessageByID($request->record_id);
				\Models\Administrator::updateMessage($request->record_id,['message_ticket_status'=>'closed']);

				$data['message_subject'] = $message['message_subject'];
				$data['message_content'] = $request->message_content;
				$data['message_comment'] = $request->message_content;
				$data['sender_type'] = 'admin';
				$data['receiver_type'] = 'talent';
				$data['message_reply_id'] = $message['id_message'];
				$data['message_type'] = $message['message_type'];
				$data['created'] = date('Y-m-d H:i:s');
				$data['updated'] = date('Y-m-d H:i:s');
				\Models\Administrator::addMessage($data);
				$email = $message['sender_email'];

				$emailData                    = ___email_settings();
				$emailData['email']     	  = $email;
				$emailData['name']      	  = $message['message_subject'];
				$emailData['message_reply']   = $request->message_content;
				___mail_sender($email,sprintf("%s %s",$request->first_name,$request->last_name),"admin_contact_reply",$emailData);

				$this->status = true;
				$this->message = 'Message reply successfully submit.';
				$this->redirect = url('administrator/messages/closed');

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
         * [This method is used for message deletion] 
         * @param  Request
         * @return \Illuminate\Http\Response
         */

		public function message_delete(Request $request){
			\Models\Administrator::deleteMessageById($request->id_message);

			$request->session()->flash('success',trans("admin.message_deleted"));
			return redirect()->intended(sprintf('/%s/%s',ADMIN_FOLDER,'messages/inbox'));
		}

		/**
         * [This method is used for raise dispute] 
         * @param  Request
         * @return \Illuminate\Http\Response
         */

		public function raise_dispute(Request $request, Builder $htmlBuilder){
			$disputeList = \Models\RaiseDispute::select([\DB::raw('@row_number  := @row_number  + 1 AS row_number')])
			->defaultKeys()
			->with(['sender' => function($q){
				$q->select('id_user','type')->name();
			},
			'project' => function($q){
				$q->select('id_project','title');
			}
			])->orderBy('created','DESC')->get();

			if ($request->ajax()) {
				$count = 1;
				foreach ($disputeList as &$item) {
					$item->row_number = $count++;
				}
				//raise_dispute

				return \Datatables::of($disputeList)
				->editColumn('sender_name',function($disputeList) {
					return $disputeList->sender_name = ucfirst($disputeList->sender->name);
				})
				->editColumn('sender_type',function($disputeList) {
					return $disputeList->sender_type = ucfirst($disputeList->sender->type);
				})
				->editColumn('created',function($disputeList) {
					return ___d($disputeList->created);
				})
				->editColumn('project_title',function($disputeList) {
					return $disputeList->project_title = ucfirst($disputeList->project->title);
				})
				->editColumn('status',function($disputeList) {
					return $disputeList->status = ucfirst($disputeList->status);
				})
				->editColumn('action',function($disputeList) {
					return '<a href="'.url(ADMIN_FOLDER.'/raise-dispute/detail?dispute_id='.___encrypt($disputeList->id_raised_dispute)).'" class="btn badge">View</a>';
				})
				->make(true);
			}
			
			$data['html'] = $htmlBuilder
			->addColumn(['data' => 'row_number', 'name' => 'row_number', 'title' => '#', 'width' => '1', 'searchable' => false, 'orderable' => false])
			->addColumn(['data' => 'sender_name', 'name' => 'sender_name', 'title' => 'Sender'])
			->addColumn(['data' => 'sender_type', 'name' => 'sender_type', 'title' => 'Sender Type'])
			->addColumn(['data' => 'project_title', 'name' => 'project_title', 'title' => 'Project Title'])
			->addColumn(['data' => 'created', 'name' => 'created', 'title' => 'Dispute Date'])
			->addColumn(['data' => 'status', 'name' => 'status', 'title' => 'Status'])
			->addColumn(['data' => 'action', 'name' => 'action', 'title' => 'Action', 'width' => '1','searchable' => false, 'orderable' => false]);

			return view('backend.raisedispute.list')->with($data);
		}

		/**
         * [This method is used for raise dispute in detail] 
         * @param  Request
         * @return \Illuminate\Http\Response
         */

		public function raise_dispute_detail(Request $request, Builder $htmlBuilder){
			$data['dispute_id']         = ___decrypt($request->dispute_id);

			$request->request->add(['currency'=>DEFAULT_CURRENCY]);
			
			if(empty($data['dispute_id'])){
				return redirect()->back();
			}

			$data['page_title']         = 'View Raise Dispute';
			
			$data['raisedispute']       = \Models\RaiseDispute::defaultKeys()->with([
				'project' =>function($q){
					$q->defaultKeys()->projectDescription()->with([
						'employer' => function($q){
							$q->defaultKeys();
						},
						'industries.industries' => function($q){
							$q->select('id_industry','en as name');
						},
						'subindustries.subindustries' => function($q){
							$q->select('id_industry','en as name');
						},
						'skills.skills' => function($q){
							$q->select('id_skill', 'skill_name');
						},
						'proposal' => function($q){
							$q->defaultKeys()->quotedPrice()->with(['file' => function($q){
								$q->defaultKeys()->where('type','proposal');	
							}])->where('status','accepted');
						}
					]);
				},
				'sender' => function($q){
					$q->defaultKeys();
				},
				'comments' => function($q){
					$q->defaultKeys()->with([
						'files'  => function($q){
                            $q->where('type','disputes');
                        },
						'sender' => function($q){
							$q->defaultKeys();
						}
					]);
				},
				'amount_agreed'=>function($q){
					$q->defaultKeys();
				}
			])->where('id_raised_dispute','=',$data['dispute_id'])->first();
			
			$data['raisedispute'] 			= json_decode(json_encode($data['raisedispute']),true);
			$raise_dispute_index 			= array_search($data['raisedispute']['type'], \Models\Listings::raise_dispute_type_column());
			$data['raisedispute']['step'] 	= (string) $raise_dispute_index+1;
				
			$data['payment'] 				= \Models\Payments::admin_payment_details($data['raisedispute']['project_id']);
			$data['user']               	= \Models\Users::get_support_user(SUPPORT_CHAT_USER_ID);
			$data['page']               	= (!empty($request->page))?$request->page:'detail';
			$data['url']                	= sprintf("%s?dispute_id=%s",$request->url(),$request->dispute_id);
			$data['backurl']            	= url(sprintf('%s/raise-dispute', ADMIN_FOLDER));
			
			if(empty($data['raisedispute'])){
				return redirect(sprintf("%s/raise-dispute",ADMIN_FOLDER));
			}

			if($data['page'] == 'payments'){
				if ($request->ajax()) {
					$disputeList = \Models\Payments::listing($data['raisedispute']['project_id']);
					return \Datatables::of($disputeList)
					->editColumn('transaction_status',function($item){
						return $item->transaction_status = ucfirst($item->transaction_status);
					})
					->editColumn('transaction_type',function($item){
						return $item->transaction_type = ucfirst($item->transaction_type);
					})
					->editColumn('transaction',function($item){
						return $item->transaction = ___readable($item->transaction,true);
					})
					->editColumn('user_type',function($item){
						return $item->user_type = ucfirst($item->user_type);
					})
					->editColumn('transaction_subtotal',function($item){
						return $item->transaction_subtotal = PRICE_UNIT.___format($item->transaction_subtotal);
					})
					->editColumn('transaction_date',function($item){
						return $item->transaction_date = ___d($item->transaction_date);
					}) 
					->editColumn('action',function($item){
						if($item->user_type == 'Employer' && $item->transaction_status == 'Confirmed'){
							return '<button class="btn badge case-resolve bg-green">Escrowed</button>';
						}elseif($item->transaction_status == 'Refunded' || $item->transaction_status == 'Refunded-pending'){
							return '<button class="btn badge case-resolve btn-warning">Refunded</button>';
						}elseif($item->user_type == 'Talent'){
							return '<button class="btn badge case-resolve bg-green">Paid</button>';
						}else{
							return '<button class="btn badge case-resolve bg-red">Failed</button>';
						} 
					})
					->make(true);
				}

				$data['html'] = $htmlBuilder
				->addColumn(['data' => 'row_number', 'name' => 'row_number', 'title' => '#', 'width' => '1', 'searchable' => false, 'orderable' => false])
				->addColumn(['data' => 'transaction_user_name', 'name' => 'transaction_user_name', 'title' => 'User'])
				->addColumn(['data' => 'user_type', 'name' => 'user_type', 'title' => 'User Type'])
				->addColumn(['data' => 'transaction', 'name' => 'transaction', 'title' => 'Payments Type'])
				->addColumn(['data' => 'transaction_status', 'name' => 'transaction_status', 'title' => 'Status'])
				->addColumn(['data' => 'transaction_subtotal', 'name' => 'transaction_subtotal', 'title' => 'Subtotal'])
				->addColumn(['data' => 'transaction_type', 'name' => 'transaction_type', 'title' => 'Type'])
				->addColumn(['data' => 'transaction_date', 'name' => 'transaction_date', 'title' => 'Date'])
				->addColumn(['data' => 'action', 'name' => 'action', 'title' => 'Action', 'width' => '1','searchable' => false, 'orderable' => false]);
			}else if($data['page'] == 'payments-due' || $data['page'] == 'disputed-payment'){
				if ($request->ajax()) {
					if($data['page'] == 'payments-due'){
						$disputeList = \Models\Payments::talent_upcoming_payment($data['raisedispute']['project_id']);
					}else if($data['page'] == 'disputed-payment'){
						$disputeList = \Models\Payments::talent_disputed_payment($data['raisedispute']['project_id']);
					}
					return \Datatables::of($disputeList)
					->editColumn('transaction_date',function($item){
						return $item->transaction_date = ___d($item->transaction_date);
					})
					->editColumn('payment_due',function($item){
						return $item->payment_due = $item->currency.___format($item->quoted_price,true,false);
					})
					// ->editColumn('working_hours',function($item){
					// 	return $item->working_hours = ___convert_time($item->working_hours).' Hrs';
					// })
					->editColumn('action',function($item){
						if($item->transaction_status == 'confirmed'){
							return '<button class="btn badge case-resolve bg-red">Confirmed</button>'; 
						}else if($item->transaction_status == 'disputed'){
							return '<button class="btn badge case-resolve bg-black">Disputed</button>'; 
						}else{
							return '<button class="btn badge case-resolve bg-red">Not Confirmed</button>'; 
						}
					})
					->make(true);
				}

				$data['html'] = $htmlBuilder
				->addColumn(['data' => 'row_number', 'name' => 'row_number', 'title' => '#', 'width' => '1', 'searchable' => false, 'orderable' => false])
				->addColumn(['data' => 'working_hours', 'name' => 'working_hours', 'title' => 'Working Hours', 'width' => '100'])
				->addColumn(['data' => 'payment_due', 'name' => 'payment_due', 'title' => 'Payment Due'])
				->addColumn(['data' => 'action', 'name' => 'action', 'title' => 'Action', 'width' => '1','searchable' => false, 'orderable' => false]);
			}

			return view('backend.raisedispute.view')->with($data);
		}

		/**
         * [This method is used for payment refund] 
         * @param  Request
         * @return Json Response
         */

		public function payment_refund(Request $request){
			$project_id = ___decrypt($request->project_id);
			
			if(empty($project_id)){
				return response()->json([
					'status' => false,
					'message' => sprintf(ALERT_DANGER,'Something wrong, please try again.')
				]); 
			}else{
				$refund_detail = \Models\Payments::employer_refund_detail($project_id);
				
				/* ADDING REFUND TRANSACTION RECORD IN TRANSACTION TABLE */
				$isTransactionInserted = \Models\Payments::refund_transaction(
					$project_id,
					[
						'transaction_user_id'       		=> $refund_detail['refundable_user_id'],
                        'transaction_company_id'    		=> $refund_detail['refundable_user_id'],	
						'transaction_user_type'     		=> $refund_detail['refundable_user_type'],
						'transaction_project_id'    		=> $project_id,
						'transaction_comment'    			=> $refund_detail['refundable_transaction_id'],
						'transaction_proposal_id'   		=> $refund_detail['refundable_proposal_id'],
						'transaction_total'         		=> $refund_detail['refundable_amount'],
						'transaction_subtotal'      		=> $refund_detail['refundable_amount'],
						'raise_dispute_commission'  		=> 0,
						'raise_dispute_commission_type'     => NULL,
						'transaction_type'          		=> 'credit',
						'currency'                  		=> DEFAULT_CURRENCY,
						'transaction_status'        		=> 'refunded-pending',
                        'transaction_done_by'       		=> -1,	
						'transaction_date'          		=> date('Y-m-d',strtotime("+".REFUNDABLE_DATE_MARGIN." days")),
						'updated'                   		=> date('Y-m-d H:i:s'),
                        'created'                   		=> date('Y-m-d H:i:s'),
					]
				);
				
				$dispute_detail 	= \Models\RaiseDispute::where('project_id',$project_id)->where('status','open')->select('id_raised_dispute')->get()->first();
				$isMarkedDispute 	= \Models\RaiseDispute::resolve_raise_dispute($project_id);
				$isProposalDisputed = \Models\Proposals::where('project_id',$project_id)->where('status','accepted')->update(['payment' => 'disputed', 'updated' => date('Y-m-d H:i:s')]);
				$isProjectClosed 	= \Models\Projects::where('id_project',$project_id)->update(['project_status' => 'closed', 'closedate' => date('Y-m-d H:i:s'), 'updated' => date('Y-m-d H:i:s')]);

                $commentArray = [
                    'dispute_id'    => $dispute_detail['id_raised_dispute'],
                    'sender_id'     => \Auth::guard('admin')->user()->id_user,
                    'comment'       => trans('website.W0790'),
                    'type'          => 'receiver-comment',
                    'updated'       => date('Y-m-d H:i:s'),
                    'created'       => date('Y-m-d H:i:s'),
                ];

                $isCommentCreated = \Models\RaiseDisputeComments::submit($commentArray);
				$this->status = true;
				$this->message = sprintf(ALERT_SUCCESS,trans('admin.A0061'));
				$this->redirect = true;
			
				return response()->json([
					'data'      => $this->jsondata,
					'status'    => $this->status,
					'message'   => $this->message,
					'redirect'  => $this->redirect,
				]);
			}
		}

		/**
         * [This method is used for pay payment] 
         * @param  Request
         * @return Json Response
         */

		public function payment_pay(Request $request){
			$project_id = ___decrypt($request->project_id);
			$request['currency'] = \Session::get('site_currency');

			$job_details        = \Models\Projects::defaultKeys()
			->with([
				'proposal' => function($q){
					$q->defaultKeys()->where('talent_proposals.status','accepted')->with([
						'talent' => function($q){
							$q->defaultKeys();
						}
					]);
				}
			])->where(['id_project' => $project_id])->get()->first();
			
			if(empty($project_id)){
				return response()->json([
					'status' => false,
					'message' => sprintf(ALERT_DANGER,'Something wrong, please try again.')
				]); 
			}else{
				$dispute_detail 	= \Models\RaiseDispute::where('project_id',$project_id)->where('status','open')->select('id_raised_dispute')->get()->first();
				$isPayOutDone  		= \Models\Payments::init_talent_payment($project_id,\Auth::guard('admin')->user()->id_user);
                $isMarkedDispute 	= \Models\RaiseDispute::resolve_raise_dispute($project_id);
                $isProposalDisputed = \Models\Proposals::where('project_id',$project_id)->where('status','accepted')->update(['payment' => 'confirmed', 'updated' => date('Y-m-d H:i:s')]);
                
                $commentArray = [
                    'dispute_id'    => $dispute_detail['id_raised_dispute'],
                    'sender_id'     => \Auth::guard('admin')->user()->id_user,
                    'comment'       => trans('website.W0789'),
                    'type'          => 'receiver-comment',
                    'updated'       => date('Y-m-d H:i:s'),
                    'created'       => date('Y-m-d H:i:s'),
                ];


                if(!empty($isPayOutDone) && !empty($isMarkedDispute)){
                	$isProjectClosed 	= \Models\Projects::where('id_project',$project_id)->update(['project_status' => 'closed', 'closedate' => date('Y-m-d H:i:s'), 'updated' => date('Y-m-d H:i:s')]);
                	$isCommentCreated = \Models\RaiseDisputeComments::submit($commentArray);
                    $isNotified = \Models\Notifications::notify(
                        $job_details->proposal->talent->id_user,
                        SUPPORT_CHAT_USER_ID,
                        'JOB_PAYMENT_RELEASED_BY_CROWBAR',
                        json_encode([
                            "talent_id" => (string) $job_details->proposal->talent->id_user,
                            "employer_id" => (string) $job_details->company_id,
                            "project_id" => (string) $project_id,
                            "transaction_id" => (string) $isPayOutDone->id_transactions
                        ])
                    );
						
                    $this->status   = true;
                    $this->message 	= sprintf(ALERT_SUCCESS,trans('admin.A0085'));
                    $this->redirect = true;
                }else{
                	$this->status 	= false;
                    $this->message  = sprintf(ALERT_DANGER,"Something wrong, please try again.");
                }
                
				return response()->json([
					'data'      => $this->jsondata,
					'status'    => $this->status,
					'message'   => $this->message,
					'redirect'  => $this->redirect,
				]);	

				// $data['total_payment_due']  = \Models\Payments::talent_payble_detail($project_id);
			}		
		}

		/**
         * [This method is used for resolving raise dispute] 
         * @param  Request
         * @return Json Response
         */

		public function resolve_raise_dispute(Request $request){
			$project_id = ___decrypt($request->project_id);
			$request['currency'] = \Session::get('site_currency');
			
	        $job_details = \Models\Employers::get_job(" {$this->prefix}projects.id_project = {$project_id} ","single",[
	            'projects.id_project',
	            'projects.user_id',
	            'projects.project_status',
	            \DB::Raw("{$this->prefix}proposals.user_id as accepted_talent_id"),
	        ]);    

			if(empty($project_id)){
				return response()->json([
					'status' => false,
					'message' => sprintf(ALERT_DANGER,'Something wrong, please try again.')
				]); 
			}else{
				/* RESOLVING RAISE DISPUTE */
            	$isDisputeClosed = \Models\RaiseDispute::resolve_raise_dispute($project_id);

					
	            $this->status   = true;
	            $this->message 	= sprintf(ALERT_SUCCESS,trans('admin.A0061'));
	            $this->redirect = "#raise_dispute_buttons";
	        }

			return response()->json([
				'data'      => $this->jsondata,
				'status'    => $this->status,
				'message'   => $this->message,
				'redirect'  => $this->redirect,
			]);				
		}

		/**
         * [This method is used for report abuse] 
         * @param  Request
         * @return \Illuminate\Http\Response
         */

		public function report_abuse(Request $request, Builder $htmlBuilder){
			if ($request->ajax()) {
				$report_abuses = \Models\Abuse::get_all_report_abuses();
				return \Datatables::of($report_abuses)
				->editColumn('sender_name',function($item) {
					return $item->sender_name = $item->sender_name.' ('.ucfirst($item->sender_type).')';
				})
				->editColumn('receiver_name',function($item) {
					return $item->receiver_name = $item->receiver_name.' ('.ucfirst($item->receiver_type).')';
				})
				->editColumn('created',function($item) {
					return $item->created = ___d($item->created);
				})
				->editColumn('type',function($item) {
					if($item->type == 'report-abused'){
						return $item->type = 'Reported';
					}else if($item->type == 'abusive-words'){
						return $item->type = 'Sent abusive words on chat';
					}
				})
				->editColumn('action',function($item) {

					return '<a href="'.url(sprintf('%s/report-abuse/view?id_report=%s&id_user=%s&page=details',ADMIN_FOLDER,___encrypt($item->id_report),___encrypt($item->receiver_id))).'" class="badge">View</a> ';

				})
				->editColumn('status',function($item) {
					return $item->status = ucfirst($item->status);
				})
				->make(true);
			}

			$data['html'] = $htmlBuilder
			->addColumn(['data' => 'row_number', 'name' => 'row_number', 'title' => '#', 'width' => '1'])
			->addColumn(['data' => 'sender_name', 'name' => 'sender_name', 'title' => 'Sender'])
			->addColumn(['data' => 'receiver_name', 'name' => 'receiver_name', 'title' => 'Receiver'])
			->addColumn(['data' => 'no_reported_abuse', 'name' => 'no_reported_abuse', 'title' => 'Total Cases'])
			->addColumn(['data' => 'created', 'name' => 'created', 'title' => 'Date'])
			->addColumn(['data' => 'action', 'name' => 'action', 'title' => 'Action','searchable' => false, 'orderable' => false]);

			return view('backend.reportabuse.list')->with($data);
		}

		/**
         * [This method is used for resolving report abuse] 
         * @param  Request
         * @return Json Response
         */

		public function report_abuse_resolve(Request $request){
			$disputeList = \Models\Abuse::resolve_report_abuse(___decrypt($request->id_report),___decrypt($request->user_id));

			$this->status = true;
			$this->message = sprintf(ALERT_SUCCESS,trans('admin.updated_successfully'));
			
			return response()->json([
				'data'      => $this->jsondata,
				'status'    => $this->status,
				'message'   => $this->message,
				'redirect'  => $this->redirect,
			]);
		}

		/**
         * [This method is used for unlinked reprt abuse] 
         * @param  Request
         * @return Json Response
         */

		public function report_abuse_unlink(Request $request){
			$disputeList = \Models\Abuse::resolve_report_abuse(___decrypt($request->id_report),___decrypt($request->user_id));

			$this->status = true;
			$this->message = sprintf(ALERT_SUCCESS,trans('admin.updated_successfully'));
			
			return response()->json([
				'data'      => $this->jsondata,
				'status'    => $this->status,
				'message'   => $this->message,
				'redirect'  => $this->redirect,
			]);
		}

		/**
         * [This method is used for view reprt abuse] 
         * @param  Request
         * @return Json Response
         */

		public function report_abuse_view(Request $request, Builder $htmlBuilder){

			$data['id_user'] = $id_user = $request->id_user;
			$data['id_report'] = $id_report = $request->id_report;
			$data['page'] = $request->page;

			$data['url'] = url(sprintf('%s/report-abuse/view?id_report=%s&id_user=%s&page=', ADMIN_FOLDER, ___encrypt($id_report), ___encrypt($id_user)));

			$data['user_details'] =(array)\Models\Users::findById(___decrypt($request->id_user));
			$data['picture'] = get_file_url(\Models\Users::get_file(sprintf(" type = 'profile' AND user_id = %s",___decrypt($request->id_user)),'single',['filename','folder']));


			if($request->page == 'details'){
				if ($request->ajax()) {
					$report_abuses = \Models\Abuse::get_all_report_abuse_by_Id(___decrypt($id_user));
					return \Datatables::of($report_abuses)
							->editColumn('sender_name',function($item) {
								return $item->sender_name = $item->sender_name.' ('.ucfirst($item->sender_type).')';
							})
							->editColumn('created',function($item) {
								return $item->created = ___d($item->created);
							})
							->editColumn('type',function($item) {
								if($item->type == 'report-abused'){
									return $item->type = 'Reported';
								}else if($item->type == 'abusive-words'){
									return $item->type = 'Sent abusive words on chat';
								}
							})
							->editColumn('action',function($item) {

								if($item->status == 'open'){
									if($item->type == 'Reported'){
										return '
											<button class="btn badge unlink-chat" data-request="ajax-confirm" data-ask_title="'.ADMIN_CONFIRM_TITLE.'" data-ask="'.trans('admin.report_abuse_unlink_confirm').'" data-url="'.url(ADMIN_FOLDER.'/report-abuse/unlink?id_report='.___encrypt($item->id_report).'&user_id='.___encrypt($item->receiver_id)).'">
											Block Receiver
										</button>';	
									}else{
										return '
											<button class="btn badge unlink-chat" data-request="ajax-confirm" data-ask_title="'.ADMIN_CONFIRM_TITLE.'" data-ask="'.trans('admin.report_abuse_unlink_confirm').'" data-url="'.url(ADMIN_FOLDER.'/report-abuse/unlink?id_report='.___encrypt($item->id_report).'&user_id='.___encrypt($item->sender_id)).'">
											Block Sender
										</button>';	
									}
									
								}else{
									return '<button class="btn badge case-resolve bg-grey">Closed</button>';
								}
							})
							->editColumn('status',function($item) {
								return $item->status = ucfirst($item->status);
							})
							->make(true);
						}

						$data['html'] = $htmlBuilder
						->addColumn(['data' => 'row_number', 'name' => 'row_number', 'title' => '#', 'width' => '1'])
						->addColumn(['data' => 'sender_name', 'name' => 'sender_name', 'title' => 'Sender'])
						->addColumn(['data' => 'message', 'name' => 'message', 'title' => 'Message'])
						->addColumn(['data' => 'type', 'name' => 'type', 'title' => 'Type'])
						->addColumn(['data' => 'created', 'name' => 'created', 'title' => 'Date'])
						->addColumn(['data' => 'status', 'name' => 'status', 'title' => 'Status'])
						->addColumn(['data' => 'action', 'name' => 'action', 'title' => 'Action','searchable' => false, 'orderable' => false]);

			}else{

				$AbuseDetails = \Models\Abuse::getAbuseById(___decrypt($id_report));


				if($data['user_details']['type'] == "employer"){
					$data['company_id'] = $employer_id = $data['user_details']['id_user']; //emp id
					$data['talent_id']  = $talent_id   = $AbuseDetails['sender_id'];
				}else{
					$data['company_id'] = $talent_id   = $AbuseDetails['sender_id']; 
					$data['talent_id']  = $employer_id = $data['user_details']['id_user']; //emp id
				}

				$project_id = \Models\Projects::create_dummp_job($employer_id,$talent_id);

				$data['chat']		= [];
				if(!empty($data['talent_id']) && !empty($data['company_id'])){
					$data['chat'] = \Models\Chats::getmessages($project_id,$data['company_id'],$data['talent_id'],1,0,'up','delete_sender_status',true,true);
				}

			}


			return view('backend.reportabuse.view')->with($data);

		}  

		/**
         * [This method is used for subscribed newsletter] 
         * @param  Request
         * @return \Illuminate\Http\Response
         */       

		public function newsletter_subscribe(Request $request, Builder $htmlBuilder){
			if ($request->ajax()) {
				$subscribeList = \Models\Users::getSubscribeList();
				return \Datatables::of($subscribeList)
				->editColumn('status',function($subscribeList) {
					return $subscribeList->status = ucfirst($subscribeList->status);
				})
				->editColumn('action',function($subscribeList) {
					return '<a href="javascript:;" onclick="unsubscribe('.$subscribeList->id_subscriber.')" class="badge case-resolve bg-red">Unsubscribe</a>';
				})
				->make(true);
			}

			$data['html'] = $htmlBuilder
			->addColumn(['data' => 'row_number', 'name' => 'row_number', 'title' => '#', 'width' => '1'])
			->addColumn(['data' => 'email', 'name' => 'email', 'title' => 'Email'])
			->addColumn(['data' => 'status', 'name' => 'status', 'title' => 'Status', 'width' => '1'])
			->addColumn(['data' => 'action', 'name' => 'action', 'title' => 'Action','searchable' => false, 'width' => '50', 'orderable' => false]);

			return view('backend.newsletter.newsletter-subscribe')->with($data);
		}

		/**
         * [This method is used for unsubscribed newsletter] 
         * @param  Request
         * @return Json Response
         */

		public function newsletter_unsubscribe(Request $request){
			\Models\Users::deleteSubscribe($request->id_subscriber);

			$this->status = true;
			$this->message = 'User has been successfully unsubscribed.';
			return response()->json([
				'message'   => $this->message
			]);
		}

		/**
         * [This method is used for project listing] 
         * @param  Request
         * @return \Illuminate\Http\Response
         */

		/*Job Management*/
		public function project_list(Request $request, Builder $htmlBuilder){
			$type = 'countdata';
			$data['count'] 		=	$this->projectAjaxList($type);
			if ($request->ajax()) {
				return $this->projectAjaxList();
			}

			if($request->download && $request->download =='csv'){
				$projectList =$this->projectAjaxList();
				$csvdata = $projectList->getdata(true);
				$csvdata = $csvdata['data'];
				$file_name = 'report_'.time().'.csv';
				$headers = [
	                "Content-type" => "application/csv",
	                "Content-Disposition" => "attachment; filename={$file_name}",
	                "Pragma" => "no-cache",
	                "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
	                "Expires" => "0"
	            ];
	            $callback = function() use ($csvdata)
	            {
	                #dd('weqw');
	                $file = fopen('php://output', 'w');
	                fputcsv($file,['Job Title','Employer Name','Employment','Start Date','End Date']);
	                foreach ($csvdata as $cdata) {
	                    fputcsv($file, [$cdata['title'],$cdata['name'],$cdata['employment'],$cdata['startdate'],$cdata['enddate']]);
	                }

	                fclose($file);
	            };

	            $response = new StreamedResponse();
	            $response->setCallback($callback);

	            foreach ($headers as $header_key => $header_val) {
	            	
	            	$response->headers->set($header_key, $header_val);

	            }
	            // @codeCoverageIgnoreEnd
	            $response->send(); 

	            return;
			}

			$data['html'] = $htmlBuilder
			->addColumn(['data' => 'row_number', 'name' => 'row_number', 'title' => '#', 'width' => '1', 'searchable' => false, 'orderable' => false])
			->addColumn(['data' => 'title', 'name' => 'title', 'title' => 'Title'])
			->addColumn(['data' => 'name', 'name' => 'name', 'title' => 'Employer Name'])
			->addColumn(['data' => 'employment', 'name' => 'employment', 'title' => 'Employment'])
			->addColumn(['data' => 'startdate', 'name' => 'startdate', 'title' => 'Start Date'])
			->addColumn(['data' => 'enddate', 'name' => 'enddate', 'title' => 'End Date'])
			->addColumn(['data' => 'action', 'name' => 'action', 'title' => 'Action','searchable' => false, 'width'=>"1", 'orderable' => false]);

			return view('backend.project.project-list')->with($data);
		}

		public function projectAjaxList($type =NULL){
			$prefix                 = DB::getTablePrefix();
			\DB::statement(DB::raw('set @row_number=0'));

				$projectList = \Models\Projects::select([
					\DB::raw('@row_number  := @row_number  + 1 AS row_number'),
					'id_project',
					'user_id as company_id',
					'title',
					\DB::Raw("{$this->prefix}projects.employment AS employment"),
					\DB::Raw("CONCAT({$this->prefix}projects.price_unit,{$this->prefix}projects.price) AS price"),
					\DB::Raw("DATE_FORMAT({$this->prefix}projects.startdate, '%d %M %Y') AS startdate"),
					\DB::Raw("DATE_FORMAT({$this->prefix}projects.enddate, '%d %M %Y') AS enddate"),
				])->with([
					'industries.industries' => function($q){
						$q->select('id_industry','en as name');
					},
					'subindustries.subindustries' => function($q){
						$q->select('id_industry','en as name');
					},
					'employer' => function($q) use($prefix){
						$q->select(
							'id_user',
							\DB::Raw("CONCAT({$prefix}users.first_name, ' ',{$prefix}users.last_name) AS name")
						);
					}
				])->whereNotIn('status',['trashed'])->orderBy('id_project','DESC')->get();

				if($type=='countdata'){
					return $projectList->count();
				}

				return \Datatables::of($projectList)
				->editColumn('name',function($projectList){
					return ucfirst($projectList->employer->name);
				})
				->editColumn('employment',function($projectList){
					return ucfirst($projectList->employment);
				})
				->editColumn('action',function($projectList) {
					$html =  '<a href="'.url('administrator/project/detail/'.$projectList->id_project.'?slug=project').'" class="badge case-resolve">View</a> ';
					return $html;
				})
				->make(true);

		}

		/**
         * [This method is used for project deletion] 
         * @param  Request
         * @return Json Response
         */

		public function project_delete(Request $request){

			\Models\Projects::change([
				'id_project' => $request->id_project
			], [
				'status' => 'trash'
			]);

			$this->status = true;
			$this->message = 'Project has been successfully deleted.';
			return response()->json([
				'message'   => $this->message
			]);
		}

		/**
         * [This method is used for project detail] 
         * @param  Request
         * @return \Illuminate\Http\Response
         */

		public function project_detail(Request $request,Builder $htmlBuilder){
			$prefix 		= DB::getTablePrefix();
			if(empty($request->id_project)){
				return redirect()->intended(sprintf('/%s/%s',ADMIN_FOLDER,'project/listing'));
			}

			$data['page'] 								= $request->page;
			$request->request->add(['currency'=>DEFAULT_CURRENCY]);

			$data['project_detail']        = \Models\Projects::defaultKeys()
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
				])->where('id_project',$request->id_project)->first();
			$data['project_detail'] = (json_decode(json_encode($data['project_detail']),true));
			// $data['project_detail']['skill'] 			= \Models\Projects::getProjectSkill($request->id_project);
			// $data['project_detail']['qualification'] 	= \Models\Projects::getProjectQualification($request->id_project);
			$data['url'] 								= url('administrator/project/detail/'.$request->id_project);
			$data['description_lng'] 					= \Models\Projects::getProjectDescription($request->id_project);
			$data['language'] 							= language();
			// $data['project_proposal'] 					= \Models\Projects::getProjectProposal($request->id_project);
			
			if($request->page == 'proposal'){
				if ($request->ajax()) {
					$project_proposal = \Models\Projects::getProjectProposal($request->id_project);
					return \Datatables::of($project_proposal)
					->editColumn('status',function($project_proposal){
						return ucfirst($project_proposal->status);
					})
					->editColumn('action',function($project_proposal){
						return $html = '<a href="'.url(sprintf('%s/project/proposal/detail?id_proposal=%s',ADMIN_FOLDER,___encrypt($project_proposal->id_proposal))).'"class="badge bg-light-blue">Detail</a>  ';
					})
					->make(true);
				}
				$data['html'] = $htmlBuilder
				->addColumn(['data' => 'row_number', 'name' => 'row_number', 'title' => '#','width' => '1'])
				->addColumn(['data' => 'name', 'name' => 'name', 'title' => 'Name'])
				->addColumn(['data' => 'quoted_price', 'name' => 'quoted_price', 'title' => 'Quoted Price'])
				->addColumn(['data' => 'comments', 'name' => 'comments', 'title' => 'Comments'])
				->addColumn(['data' => 'status', 'name' => 'status', 'title' => 'Status'])
				->addColumn(['data' => 'action', 'name' => 'action', 'title' => 'Actions','width' => '80','searchable' => false, 'orderable' => false]);
			}else if($request->page == 'transactions'){
				if ($request->ajax()) {
					$disputeList = \Models\Payments::listing($request->id_project);
					return \Datatables::of($disputeList)
					->editColumn('transaction_status',function($item){
						return $item->transaction_status = ucfirst($item->transaction_status);
					})
					->editColumn('transaction_type',function($item){
						return $item->transaction_type = ucfirst($item->transaction_type);
					})
					->editColumn('transaction',function($item){
						return $item->transaction = ___readable($item->transaction,true);
					})
					->editColumn('user_type',function($item){
						return $item->user_type = ucfirst($item->user_type);
					})
					->editColumn('job_id',function($item){
						return $item->job_id = ucfirst($item->transaction_project_id);
					})
					->editColumn('transaction_reference_id',function($item){
						return ($item->transaction_reference_id?$item->transaction_reference_id:'-');
					})
					->editColumn('transaction_subtotal',function($item){
						return $item->transaction_subtotal = PRICE_UNIT.___format($item->transaction_subtotal);
					})
					->editColumn('transaction_date',function($item){
						return $item->transaction_date = ___d($item->transaction_date);
					}) 
					->editColumn('action',function($item){
						if($item->user_type == 'Employer' && $item->transaction_status == 'Confirmed'){
							return '<button class="btn badge case-resolve bg-green">Escrowed</button>';
						}elseif($item->transaction_status == 'Refunded'){
							return '<button class="btn badge case-resolve btn-warning">Refunded</button>';
						}elseif($item->user_type == 'Talent'){
							return '<button class="btn badge case-resolve bg-green">Paid</button>';
						}else{
							return '<button class="btn badge case-resolve bg-red">Failed</button>';
						} 
					})
					->make(true);
				}

				$data['html'] = $htmlBuilder
				->addColumn(['data' => 'row_number', 'name' => 'row_number', 'title' => '#', 'width' => '1', 'searchable' => false, 'orderable' => false])
				// ->addColumn(['data' => 'id_transactions', 'name' => 'id_transactions', 'title' => 'Invoice ID', 'width' => '20', 'searchable' => false, 'orderable' => false])
				->addColumn(['data' => 'transaction_user_name', 'name' => 'transaction_user_name', 'title' => 'User'])
				->addColumn(['data' => 'user_type', 'name' => 'user_type', 'title' => 'User Type'])
				->addColumn(['data' => 'job_id', 'name' => 'job_id', 'title' => 'Job ID'])
				->addColumn(['data' => 'transaction', 'name' => 'transaction', 'title' => 'Payments Type'])
				->addColumn(['data' => 'transaction_status', 'name' => 'transaction_status', 'title' => 'Status'])
				->addColumn(['data' => 'transaction_reference_id', 'name' => 'transaction_reference_id', 'title' => 'Transaction ID'])
				->addColumn(['data' => 'transaction_subtotal', 'name' => 'transaction_subtotal', 'title' => 'Subtotal'])
				->addColumn(['data' => 'transaction_type', 'name' => 'transaction_type', 'title' => 'Type'])
				->addColumn(['data' => 'transaction_date', 'name' => 'transaction_date', 'title' => 'Date'])
				->addColumn(['data' => 'action', 'name' => 'action', 'title' => 'Payment Status', 'width' => '1','searchable' => false, 'orderable' => false]);
			}else if($request->page == 'chat'){
				$data['talent_id'] 							= \Models\Proposals::accepted_proposal_talent($request->id_project);
				$data['company_id'] 						= $data['project_detail']['company_id'];
				$data['group_id'] 							= $data['project_detail']['chat']['id_chat_request'];
				$data['chat']							 	= [];
				if(!empty($data['talent_id']) && !empty($data['company_id'])){
					$data['chat'] = \Models\Chats::getmessages($data['group_id'],$data['company_id'],$data['talent_id'],1,0,'up','delete_sender_status',true,true);
				}
			}else if($request->page == 'activity_log'){

				$data['id_project'] = $request->id_project; 

				$data['project_title'] 				= $data['project_detail']['title']; 
				$data['project_start_date'] 	 	= $data['project_detail']['startdate'];
				$data['project_employer'] 			= $data['project_detail']['employer'];
				$data['project_received_proposals'] = \Models\ActivityLog::getActivityByProjectId($request->id_project,'talent-submit-proposal',false);
				// dd($data['project_received_proposals']);
				$data['project_accepted_proposal'] 	= \Models\Proposals::accepted_proposal_talent($request->id_project);
				$data['project_payment_complete'] 	= \Models\ActivityLog::getActivityByProjectId($request->id_project,'employer-payment-complete-job',true);
				$data['project_start_talent'] 		= \Models\ActivityLog::getActivityByProjectId($request->id_project,'talent-start-job',true);
				$data['project_complete_talent'] 	= \Models\ActivityLog::getActivityByProjectId($request->id_project,'talent-completed-job',true);
				$data['project_complete_talent_date']= $data['project_detail']['completedate'];
				$data['project_completion_emp'] 	 = \Models\ActivityLog::getActivityByProjectId($request->id_project,'employer-close-job',true);
				$data['project_completion_emp_date'] = $data['project_detail']['closedate'];
				$data['project_raise_dispute'] 	 	 = \Models\ActivityLog::getActivityByProjectId($request->id_project,'raise-dispute',true);
				if(!empty($data['project_raise_dispute'])){
					$project_raise_dispute_details = \Models\RaiseDispute::detail($request->id_project,$data['project_raise_dispute']['user_id']);
					if(!empty($project_raise_dispute_details)){
						$data['project_raise_dispute_id'] = ___encrypt(json_decode(json_encode($project_raise_dispute_details,true))->id_raised_dispute);
					}
				}

				$data['disputeList'] = \Models\Payments::listing($request->id_project);
			}

			return view('backend.project.project-detail')->with($data);
		}

		/**
         * [This method is used for proposals in detail] 
         * @param  Request
         * @return \Illuminate\Http\Response
         */

		public function proposal_detail(Request $request){
			if(empty($request->id_proposal)){
				return redirect()->intended(sprintf('/%s/%s',ADMIN_FOLDER,'project/listing'));
			}
			$id_proposal                = ___decrypt($request->id_proposal);
			$data['project_proposal']   = \Models\Proposals::proposals_detail($id_proposal);
			return view('backend.project.proposal-detail')->with($data);
		}

		/**
         * [This method is used for plan listing] 
         * @param  Request
         * @return \Illuminate\Http\Response
         */

		public function plan_list(Request $request, Builder $htmlBuilder){
			$data['page_title']         = 'Plan list';
			if ($request->ajax()) {
				$planList = \Models\Plan::getPlan();
				return \Datatables::of($planList)
				->editColumn('price',function($planList){
					return $planList->price = PRICE_UNIT.___format($planList->price);
				})
				->editColumn('action',function($planList){
					$html = '<a href="'.url(sprintf('%s/plan/edit?id_plan=%s',ADMIN_FOLDER,___encrypt($planList->id_plan))).'"class="badge bg-light-blue">Edit</a>  ';
					return $html;
				})
				->make(true);
			}

			$data['html'] = $htmlBuilder
			->addColumn(['data' => 'row_number', 'name' => 'row_number', 'title' => '#','width' => '1'])
			->addColumn(['data' => 'name', 'name' => 'name', 'title' => 'Plan name'])
			->addColumn(['data' => 'braintree_plan_id', 'name' => 'braintree_plan_id', 'title' => 'Plan ID','width' => '100'])
			->addColumn(['data' => 'price', 'name' => 'price', 'title' => 'Price','width' => '80'])
			->addColumn(['data' => 'action', 'name' => 'action', 'title' => 'Actions','searchable' => false, 'orderable' => false,'width' => '50']);
			
			return view('backend.plan.plan-list')->with($data);
		}

		/**
         * [This method is used for plan editing] 
         * @param  Request
         * @return \Illuminate\Http\Response
         */

		public function plan_edit(Request $request){
			$data['page_title']         = 'Edit Plan';
			$id_plan                    =  ___decrypt($request->id_plan);
			$data['features']           = \Models\Plan::getFeatures('obj',['id_feature','en as name'],"status = 'active'");
			$data['planData']           = \Models\Plan::getPlanById($id_plan);
			$data['planFeatures']       = explode(',',\Models\Plan::getPlanFeaturesById($id_plan)->feature_ids);
			$data['backurl']            = url(sprintf('%s/', ADMIN_FOLDER.'/plan/list'));
			return view('backend.plan.edit-plan')->with($data);
		}

		/**
         * [This method is used for plan editing] 
         * @param  Request
         * @return Json Response
         */

		public function edit_plan(Request $request){
			$validation = \Validator::make($request->all(),[
				'features'          => validation('features')
			],[
				'features.required' => "Please select any features."
			]);
			if($validation->passes()){
				$id_plan        = ___decrypt($request->id_plan);
				$isUpdated      = \Models\Plan::update_plan_featuers($id_plan,$request->features);
				$this->status   = true;
				$this->message  = 'Plan features has been updated successfully.';
				$this->redirect = url(sprintf('%s/plan/list',ADMIN_FOLDER));
			}else{
				$this->jsondata  = (object)[
					'plan_features' => "Please select any features"
				];
			}

			return response()->json([
				'data'      => $this->jsondata,
				'status'    => $this->status,
				'message'   => $this->message,
				'redirect'  => $this->redirect,
			]);            
		}

		/**
         * [This method is used for question type listing] 
         * @param  Request
         * @return \Illuminate\Http\Response
         */

		public function questionTypeList(Request $request, Builder $htmlBuilder){
			$data['page_title']         = 'Interview questions types';
			
			if ($request->ajax()) {
				$questionTypeList = Interview::getQuestionType('',['active','inactive']);
				return \Datatables::of($questionTypeList)
				->editColumn('status',function($questionTypeList){
					return ucfirst($questionTypeList->status);
				})                
				->editColumn('action',function($questionTypeList){
					$html = '<a href="'.url(sprintf('%s/interview/question-type/edit?id_question_type=%s',ADMIN_FOLDER,___encrypt($questionTypeList->id))).'"class="badge bg-light-blue">Edit</a>  ';
					if($questionTypeList->status == 'active'){
						$html .= '<a 
						href="javascript:void(0);"
						data-request="ajax-confirm" 
						data-ask_title="'.ADMIN_CONFIRM_TITLE.'"                       
						data-url="'.url(sprintf('%s/ajax/question-type/status?id_question_type=%s&status=inactive',ADMIN_FOLDER,___encrypt($questionTypeList->id))).'"
						data-ask="Do you really want to continue with this action?" 
						class="badge bg-green">Inactive</a>  ';
					}else{
						$html .= '<a 
						href="javascript:void(0);"
						data-url="'.url(sprintf('%s/ajax/question-type/status?id_question_type=%s&status=active',ADMIN_FOLDER,___encrypt($questionTypeList->id))).'" 
						data-request="ajax-confirm"
						data-ask_title="'.ADMIN_CONFIRM_TITLE.'" 
						data-ask="Do you really want to continue with this action?" 
						class="badge bg-green">Active</a>  ';                        
					}

					$html .= '<a 
						href="javascript:void(0);"
						data-url="'.url(sprintf('%s/ajax/question-type/status?id_question_type=%s&status=delete',ADMIN_FOLDER,___encrypt($questionTypeList->id))).'" 
						data-request="ajax-confirm"
						data-ask_title="'.ADMIN_CONFIRM_TITLE.'"  
						data-ask="Do you really want to continue with this action?" 
						class="badge bg-red">Delete</a>  ';
					// $html = '<a href="javascript:void(0);"class="badge bg-light-blue">Edit</button>';
					return $html;
				})
				->make(true);
			}

			$data['html'] = $htmlBuilder
			->addColumn(['data' => 'row_number', 'name' => 'row_number', 'title' => '#','width' => '1'])
			->addColumn(['data' => 'question_type', 'name' => 'question_type', 'title' => 'Question Type'])
			->addColumn(['data' => 'status', 'name' => 'status', 'title' => 'Status'])
			->addColumn(['data' => 'action', 'name' => 'action', 'title' => 'Actions','width' => '150','searchable' => false, 'orderable' => false]);
			
			return view('backend.interview.question-type-list')->with($data);
		}

		/**
         * [This method is used for adding interview question type] 
         * @param  Request
         * @return \Illuminate\Http\Response
         */

		public function add_interview_questionType(Request $request){
			$data['page_title']         = 'Add Interview Question';
			$data['backurl']            = url(sprintf('%s/', ADMIN_FOLDER.'/question-type-list'));

			return view('backend.interview.add-question-type')->with($data);
		}

		/**
         * [This method is used for adding question type] 
         * @param  Request
         * @return Json Response
         */

		public function add_questionType(Request $request){
			$validator = \Validator::make($request->all(),[
				'question_type' => validation('question_type'),
			],[
				'question_type.required'    => 'The Question Type field is required.'
			]);

			if($validator->passes()){
				$questionData = [
					'question_type' => $request->question_type,
					'status'        => 'active',
					'created'       => date('Y-m-d H:i:s'),
					'updated'       => date('Y-m-d H:i:s')
				];

				$isInserted = Interview::saveQuestionType($questionData);
				if($isInserted){
					$this->status   = true;
					$this->message  = 'Question has been submitted successfully.';
					$this->redirect = url(sprintf('%s/question-type-list',ADMIN_FOLDER));                    
				}
			}else {
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
         * [This method is used for interview question type editing] 
         * @param  Request
         * @return \Illuminate\Http\Response
         */

		public function edit_interview_questionType(Request $request){
			$id_question_type               = ___decrypt($request->id_question_type);
			$data['page_title']             = 'Add Interview Question';
			$data['question_type_data']     = json_decode(json_encode(Interview::getQuestionType($id_question_type,['active','inactive'],'first'),true));
			$data['backurl']                = url(sprintf('%s/', ADMIN_FOLDER.'/question-type-list'));

			return view('backend.interview.edit-question-type')->with($data);   
		}

		/**
         * [This method is used for question type editing] 
         * @param  Request
         * @return Json Response
         */

		public function edit_questionType(Request $request){
			$validator = \Validator::make($request->all(),[
				'question_type' => ['required']
			],[
				'question_type.required'      => 'The Question Type Name field is required.'
			]);

			if($validator->passes()){
				$id_question_type = ___decrypt($request->id_question_type);
				$questionTypeData = [
					'question_type' => $request->question_type,
					'updated'       => date('Y-m-d H:i:s')
				];
				$isUpdated = Interview::update_question_type($id_question_type,$questionTypeData);
				if($isUpdated){
					$this->status   = true;
					$this->message  = 'Question Type has been updated successfully.';
					$this->redirect = url(sprintf('%s/question-type-list',ADMIN_FOLDER));                    
				}
			}else {
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
         * [This method is used for feature listing] 
         * @param  Request
         * @return \Illuminate\Http\Response
         */

		public function features_list(Request $request, Builder $htmlBuilder){
			$data['page_title']         = 'Features Listings';
			$keys = [
				DB::raw('@row_number  := @row_number  + 1 AS row_number'),
				'id_feature',
				'en',
				DB::raw("IF(`id` != '' ,`id`,`en`) as id"),
				DB::raw("IF(`cz` != '' ,`cz`,`en`) as cz"),
				DB::raw("IF(`ta` != '' ,`ta`,`en`) as ta"),
				DB::raw("IF(`hi` != '' ,`hi`,`en`) as hi"),
				'status'
			];
			if ($request->ajax()) {
				$featuresList = \Models\Plan::getFeatures('obj',$keys,"status != 'trashed'");
				return \Datatables::of($featuresList)
				->editColumn('status',function($featuresList){
					return ucfirst($featuresList->status);
				})                
				->editColumn('action',function($featuresList){
					$html = '<a href="'.url(sprintf('%s/plan/features-list/features/edit?id_feature=%s',ADMIN_FOLDER,___encrypt($featuresList->id_feature))).'"class="badge bg-light-blue">Edit</a>  ';
					if($featuresList->status == 'active'){
						$html .= '<a 
						href="javascript:void(0);"
						data-request="ajax-confirm" 
						data-ask_title="'.ADMIN_CONFIRM_TITLE.'"                       
						data-url="'.url(sprintf('%s/ajax/features/status?id_feature=%s&status=inactive',ADMIN_FOLDER,___encrypt($featuresList->id_feature))).'"
						data-ask="Do you really want to continue with this action?" 
						class="badge bg-green">Inactive</a>  ';
					}else{
						$html .= '<a 
						href="javascript:void(0);"
						data-url="'.url(sprintf('%s/ajax/features/status?id_feature=%s&status=active',ADMIN_FOLDER,___encrypt($featuresList->id_feature))).'" 
						data-request="ajax-confirm"
						data-ask_title="'.ADMIN_CONFIRM_TITLE.'" 
						data-ask="Do you really want to continue with this action?" 
						class="badge bg-green">Active</a>  ';                        
					}

					$html .= '<a 
						href="javascript:void(0);"
						data-url="'.url(sprintf('%s/ajax/features/status?id_feature=%s&status=trashed',ADMIN_FOLDER,___encrypt($featuresList->id_feature))).'" 
						data-request="ajax-confirm"
						data-ask_title="'.ADMIN_CONFIRM_TITLE.'"  
						data-ask="Do you really want to continue with this action?" 
						class="badge bg-red">Delete</a>  ';
					// $html = '<a href="javascript:void(0);"class="badge bg-light-blue">Edit</button>';
					return $html;
				})
				->make(true);
			}

			$data['html'] = $htmlBuilder
			->addColumn(['data' => 'row_number', 'name' => 'row_number', 'title' => '#','width' => '1'])
			->addColumn(['data' => 'en', 'name' => 'en', 'title' => 'English'])
			->addColumn(['data' => 'id', 'name' => 'id', 'title' => 'Indonesia'])
			->addColumn(['data' => 'cz', 'name' => 'cz', 'title' => 'Mandarin'])
			->addColumn(['data' => 'ta', 'name' => 'ta', 'title' => 'Tamil'])
			->addColumn(['data' => 'hi', 'name' => 'hi', 'title' => 'Hindi'])
			->addColumn(['data' => 'status', 'name' => 'status', 'title' => 'Status'])
			->addColumn(['data' => 'action', 'name' => 'action', 'title' => 'Actions','width' => '150','searchable' => false, 'orderable' => false]);
			
			return view('backend.plan.features-list')->with($data);
		}

		/**
         * [This method is used for adding features] 
         * @param  Request
         * @return \Illuminate\Http\Response
         */

		public function features_add(Request $request){
			$data['page_title']             = 'Add Features';
			$data['backurl']                = url(sprintf('%s/', ADMIN_FOLDER.'/plan/features-list'));

			return view('backend.plan.features-add-edit')->with($data);
		}

		/**
         * [This method is used for adding features] 
         * @param  Request
         * @return Json Response
         */

		public function add_feature(Request $request){
			$id_feature = !empty($request->id_feature) ? ___decrypt($request->id_feature) : '';
			$validator = \Validator::make($request->all(),[
				'en'   => validation('admin_feature_name'),
			],[
				'en.required'      => 'Feature name is reqiuired.',
				'en.string'        => 'Feature name format is invalid.'
			]);

			if($validator->passes()){
				if(empty($id_feature)){
					$featureData = [
						'en'          	=> $request->en,
						'id'          	=> $request->id,
						'cz'          	=> $request->cz,
						'ta'          	=> $request->ta,
						'hi'          	=> $request->hi,
						'status'        => 'active',
						'created'       => date('Y-m-d H:i:s'),
						'updated'       => date('Y-m-d H:i:s')
					];
					$isInserted = \Models\Plan::add_feature($featureData);
					$this->message  = 'Feature has been submitted successfully.';
				}else{
					$featureData = [
						'en'          	=> $request->en,
						'id'          	=> $request->id,
						'cz'          	=> $request->cz,
						'ta'          	=> $request->ta,
						'hi'          	=> $request->hi,
						'updated'       => date('Y-m-d H:i:s')
					];
					$isInserted = \Models\Plan::update_feature($id_feature,$featureData);
					$this->message  = 'Feature has been updated successfully.';
				}

				if($isInserted){
					$this->status   = true;
					$this->redirect = url(sprintf('%s/plan/features-list',ADMIN_FOLDER));
				}
			}else {
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
         * [This method is used for edit features] 
         * @param  Request
         * @return \Illuminate\Http\Response
         */

		public function features_edit(Request $request){
			$id_feature               		= ___decrypt($request->id_feature);
			$data['page_title']             = 'Edit Features';
			$data['features_data']     		= json_decode(json_encode(\Models\Plan::getFeatures('single','',"id_feature = {$id_feature}")),true);
			$data['backurl']                = url(sprintf('%s/', ADMIN_FOLDER.'/plan/features-list'));

			return view('backend.plan.features-add-edit')->with($data);
		}

		/**
         * [This method is used for edit features] 
         * @param  Request
         * @return Json Response
         */

		public function edit_features(Request $request){
			$validator = \Validator::make($request->all(),[
				'question_type' => ['required']
			],[
				'question_type.required'      => 'The Question Type Name field is required.'
			]);

			if($validator->passes()){
				$id_question_type = ___decrypt($request->id_question_type);
				$questionTypeData = [
					'question_type' => $request->question_type,
					'updated'       => date('Y-m-d H:i:s')
				];
				$isUpdated = Interview::update_question_type($id_question_type,$questionTypeData);
				if($isUpdated){
					$this->status   = true;
					$this->message  = 'Question Type has been updated successfully.';
					$this->redirect = url(sprintf('%s/question-type-list',ADMIN_FOLDER));                    
				}
			}else {
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
         * [This method is used for adding state] 
         * @param  Request
         * @return Json Response
         */        

		public function add_state(Request $request){
			$id_state = $request->id_state ? ___decrypt($request->id_state) : '';
			if(!empty($request->action)){
				$validator = \Validator::make($request->all(),[
					'country'   => validation('admin_country'),
					'iso_code'  => validation('admin_iso_code'),
					'en'     	=> validation('admin_state_name'),
				],[
					'country.required'   => trans('admin.A0001'),
					'country.integer'    => trans('admin.A0002'),
					'en.required'     => trans('admin.A0003'),
					'en.string'       => trans('admin.A0004'),
					'iso_code.required'  => trans('admin.A0005'),
					'iso_code.string'    => trans('admin.A0006')
				]);

				if($validator->passes()){
					if(!empty($id_state)){
						$stateData = [
							'country_id'    => $request->country,
							'iso_code'      => $request->iso_code,
							'en'    		=> $request->en,
							'id'    		=> $request->id,
							'cz'    		=> $request->cz,
							'ta'    		=> $request->ta,
							'hi'    		=> $request->hi,
							'updated'       => date('Y-m-d H:i:s')
						];

						$isInserted = \Models\Listings::update_state($id_state, $stateData);
						$this->message = trans('admin.A0063');
					}else{
						$stateData = [
							'country_id'    => $request->country,
							'iso_code'      => $request->iso_code,
							'en'    		=> $request->en,
							'id'    		=> $request->id,
							'cz'    		=> $request->cz,
							'ta'    		=> $request->ta,
							'hi'    		=> $request->hi,
							'status'        => 'active',
							'created'       => date('Y-m-d H:i:s'),
							'updated'       => date('Y-m-d H:i:s')
						];

						$isInserted = \Models\Listings::add_state($stateData);
						$this->message      = trans('admin.A0007');
					}
						if($isInserted){
							$this->status       = true;
							$this->redirect     = true;
						}

				}else{
					$this->jsondata = ___error_sanatizer($validator->errors());
				}
			}else{

				if(!empty($id_state)){
					$data['state'] = \Models\Listings::states('single',['id_state','country_id','en','id','cz','ta','hi','iso_code'],'`id_state` = '.$id_state);
				}

				$data['countries']  = \Cache::get('countries');
				$this->jsondata 	= view('backend.pages.state')->with($data)->render();
				$this->redirect 	= 'render';
				$this->status 		= true;
			}
			return response()->json([
				'data'      => $this->jsondata,
				'status'    => $this->status,
				'message'   => $this->message,
				'redirect'  => $this->redirect,
			]);            
		}

		/**
         * [This method is used for adding city] 
         * @param  Request
         * @return Json Response
         */

		public function add_city(Request $request){
			$id_city  = $request->id_city ? ___decrypt($request->id_city) : ''; 
			if(!empty($request->action)){
				$validator = \Validator::make($request->all(),[
					'state'     => validation('admin_state'),
					'en'      	=> validation('admin_city_name'),
				],[
					'state.required'    	=> trans('admin.A0010'),
					'state.integer'     	=> trans('admin.A0011'),
					'en.required'     		=> trans('admin.A0012'),
					'en.string'       		=> trans('admin.A0013'),
				]);

				if($validator->passes()){
					$country = \Models\Listings::state_list("id_state = {$request->state}", ['country_id'],'first');
					
					if(!empty($id_city)){
						$cityData = [
							'country_id'    => $country->country_id,
							'state_id'      => $request->state,
							'en'     		=> $request->en,
							'id'     		=> $request->id,
							'cz'     		=> $request->cz,
							'ta'     		=> $request->ta,
							'hi'     		=> $request->hi,
							'updated'       => date('Y-m-d H:i:s')
						];

						$isInserted = \Models\Listings::update_city($id_city, $cityData);
					}else{
						$cityData = [
							'country_id'    => $country->country_id,
							'state_id'      => $request->state,
							'en'     		=> $request->en,
							'id'     		=> $request->id,
							'cz'     		=> $request->cz,
							'ta'     		=> $request->ta,
							'hi'     		=> $request->hi,
							'status'        => 'active',
							'created'       => date('Y-m-d H:i:s'),
							'updated'       => date('Y-m-d H:i:s')
						];
						
						$isInserted = \Models\Listings::add_city($cityData);
						
					}
					
					if($isInserted){
						$this->status       = true;
						$this->message      = trans('admin.A0014');
						$this->redirect     = true;
					}
				}else{
					$this->jsondata = ___error_sanatizer($validator->errors());
				}
			}else{
				if(!empty($id_city)){
					$data['city'] = \Models\Listings::city_list(" id_city = {$id_city} ", [
						'id_city',
						'state_id',
						'en',
						'id',
						'cz',
						'ta',
						'hi'
					],'single');
				}
				$data['states']		= \Cache::get('states');
				$this->jsondata 	= view('backend.pages.city')->with($data)->render();
				$this->redirect 	= 'render';
				$this->status  		= true;
			}
			return response()->json([
				'data'      => $this->jsondata,
				'status'    => $this->status,
				'message'   => $this->message,
				'redirect'  => $this->redirect,
			]);            
		} 

		/**
         * [This method is used for adding industry] 
         * @param  Request
         * @return Json Response
         */

		public function add_industry(Request $request){
			$id_industry = !empty($request->id_industry) ? ___decrypt($request->id_industry) : '';
			if(!empty($request->action)){
				$validator = \Validator::make($request->all(),[
					'en' => validation('admin_industry_name'),
				],[
					'en.required' => trans('admin.A0015'),
					'en.string'   => trans('admin.A0016')
				]);

				if($validator->passes()){
					if(!empty($id_industry)){
						if(!empty($request->industry_image)){
							$industryData = [
								'en'      	=> $request->en,
								'id'      	=> $request->id,
								'cz'      	=> $request->cz,
								'ta'      	=> $request->ta,
								'hi'      	=> $request->hi,
								'parent'    => 0,
								'image'		=> str_replace('uploads/industry', 'uploads/industry/resize', $request->industry_image),
								'slug'		=> strtolower(str_replace(array('  ', ' '), '-', preg_replace('/[^a-zA-Z0-9 s]/', '', trim($request->en)))),
								'updated'   => date('Y-m-d H:i:s')                    
							];
						}else{
							$industryData = [
								'en'      	=> $request->en,
								'id'      	=> $request->id,
								'cz'      	=> $request->cz,
								'ta'      	=> $request->ta,
								'hi'      	=> $request->hi,
								'parent'    => 0,
								'slug'		=> strtolower(str_replace(array('  ', ' '), '-', preg_replace('/[^a-zA-Z0-9 s]/', '', trim($request->en)))),
								'updated'   => date('Y-m-d H:i:s')                    
							];
						}

						$isInserted = \Models\Industries::update_industry($id_industry,$industryData);
					}else{
						$industryData = [
							'en'      	=> $request->en,
							'id'      	=> $request->id,
							'cz'      	=> $request->cz,
							'ta'      	=> $request->ta,
							'hi'      	=> $request->hi,
							'parent'    => 0,
							'status'    => 'active',
							'image'		=> str_replace('uploads/industry', 'uploads/industry/resize', $request->industry_image),
							'slug'		=> strtolower(str_replace(array('  ', ' '), '-', preg_replace('/[^a-zA-Z0-9 s]/', '', trim($request->en)))),
							'created'   => date('Y-m-d H:i:s'),
							'updated'   => date('Y-m-d H:i:s')                    
						];

						$isInserted = \Models\Industries::add_industry($industryData);

						/*Add Manual Payout for this New inserted Industry for all countries.*/
						$isInserted = \Models\Payout_mgmt::manualPayoutForNewIndustry();	

					}

					\Cache::forget('all_industries_name');
					\Cache::forget('industries_name');
					if($isInserted){
						$this->status       = true;
						$this->message      = trans('admin.A0017');
						$this->redirect 	= url(sprintf('%s/industry',ADMIN_FOLDER));
					}
				}else{
					$this->jsondata = ___error_sanatizer($validator->errors());
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
         * [This method is used for adding sub industry] 
         * @param  Request
         * @return Json Response
         */

		public function add_sub_industry(Request $request){
			$id_industry = $request->id_industry ? ___decrypt($request->id_industry) : '';
			$validator = \Validator::make($request->all(),[
				'industry_parent_id'        => validation('admin_industry'),
				'en'                  		=> validation('admin_industry_name'),
			],[
				'industry_parent_id.required'   => trans('admin.A0022'),
				'industry_parent_id.integer'    => trans('admin.A0023'),
				'en.required'             		=> trans('admin.A0024'),
				'en.string'               		=> trans('admin.A0025')
			]);

			if($validator->passes()){
				if(!empty($id_industry)){
					$industryData = [
						'en'      	=> $request->en,
						'id'      	=> $request->id,
						'cz'      	=> $request->cz,
						'ta'      	=> $request->ta,
						'hi'      	=> $request->hi,
						'parent'    => $request->industry_parent_id,
						'updated'   => date('Y-m-d H:i:s')                    
					];

					$isInserted = \Models\Industries::update_industry($id_industry,$industryData);
				}else{
					$industryData = [
						'en'      	=> $request->en,
						'id'      	=> $request->id,
						'cz'      	=> $request->cz,
						'ta'      	=> $request->ta,
						'hi'      	=> $request->hi,
						'parent'    => $request->industry_parent_id,
						'status'    => 'active',
						'created'   => date('Y-m-d H:i:s'),
						'updated'   => date('Y-m-d H:i:s')                    
					];

					$isInserted = \Models\Industries::add_industry($industryData);
				}
				
				if($isInserted){
					$this->status       = true;
					$this->message      = trans('admin.A0026');
					$this->redirect     = url(sprintf('%s/sub-industry',ADMIN_FOLDER));
				}
			}else{
				$this->jsondata = ___error_sanatizer($validator->errors());
			}

			\Cache::forget('subindustries_name');
			
			return response()->json([
				'data'      => $this->jsondata,
				'status'    => $this->status,
				'message'   => $this->message,
				'redirect'  => $this->redirect,
			]);            
		}

		/**
         * [This method is used for adding abusive word] 
         * @param  Request
         * @return Json Response
         */

		public function add_abusive_words(Request $request){
			$id_words = !empty($request->id_words) ? ___decrypt($request->id_words) : '';
			
			if(!empty($request->action)){
				$validator = \Validator::make($request->all(),[
					'abusive_word'        => validation('admin_abusive_words')
				],[
					'abusive_word.required'   => trans('admin.A0036'),
					'abusive_word.string'     => trans('admin.A0037')
				]);

				if($validator->passes()){
					if(!empty($id_words)){
						$abusive_word = \Models\Listings::abusive_words("single"," id_words != {$id_words} AND abusive_word = '{$request->abusive_word}' AND status != 'trashed' ",['id_words']);
						if(empty($abusive_word)){
							$abusiveWordData = [
								'abusive_word'  => $request->abusive_word,
								'updated'       => date('Y-m-d H:i:s')
							];
							$isInserted = \Models\Listings::update_abusive_words($id_words,$abusiveWordData);
							$this->message      = trans('admin.A0065');
						}else{
							$isInserted = true;
							$this->message      = trans('admin.A0064');
						}
					}else{
						$abusive_word = \Models\Listings::abusive_words("single"," abusive_word = '{$request->abusive_word}'  AND status != 'trashed' ",['id_words','abusive_word']);
						if(empty($abusive_word)){
							$abusiveWordData = [
								'abusive_word'  => $request->abusive_word,
								'status'        => 'active',
								'created'       => date('Y-m-d H:i:s'),
								'updated'       => date('Y-m-d H:i:s')
							];
							$isInserted = \Models\Listings::add_abusive_words($abusiveWordData);
							$this->message      = trans('admin.A0038');
						}else{
							$isInserted = true;
							$this->message      = trans('admin.A0064');
						}
					}
					
					
					if($isInserted){
						$this->status       = true;
						$this->redirect     = true;
					}
				}else{
					$this->jsondata = ___error_sanatizer($validator->errors());
				}
			}else{
				$data = [];
				if(!empty($id_words)){
					$data['abusive'] = \Models\Listings::abusive_words("single"," id_words = {$id_words} ",['id_words','abusive_word']);
				}
				
				$this->jsondata 	= view('backend.pages.abusive_words')->with($data)->render();
				$this->redirect 	= 'render';
				$this->status  		= true;
			}
			return response()->json([
				'data'      => $this->jsondata,
				'status'    => $this->status,
				'message'   => $this->message,
				'redirect'  => $this->redirect,
			]);            
		}
		
		/**
         * [This method is used for adding abusive word] 
         * @param  Request
         * @return Json Response
         */

		public function add_dispute_concern(Request $request){
			$id_concern = !empty($request->id_concern) ? ___decrypt($request->id_concern) : '';
			if(!empty($request->action)){
				$validator = \Validator::make($request->all(),[
					'en'     	=> validation('admin_dispute_concern'),
				],[
					'en.required'     => trans('admin.A0083'),
					'en.string'       => trans('admin.A0084')
				]);

				if($validator->passes()){
					if(!empty($id_concern)){
						$concernData = [
							'en'    		=> $request->en,
							'id'    		=> $request->id,
							'cz'    		=> $request->cz,
							'ta'    		=> $request->ta,
							'hi'    		=> $request->hi,
							'updated'       => date('Y-m-d H:i:s')
						];

						$isInserted = \Models\DisputeConcern::where('id_concern',$id_concern)->update($concernData);
						$this->message = trans('admin.A0063');
					}else{
						$concernData = [
							'en'    		=> $request->en,
							'id'    		=> $request->id,
							'cz'    		=> $request->cz,
							'ta'    		=> $request->ta,
							'hi'    		=> $request->hi,
							'status'        => 'active',
							'created'       => date('Y-m-d H:i:s'),
							'updated'       => date('Y-m-d H:i:s')
						];

						$isInserted = \Models\DisputeConcern::insert($concernData);
						$this->message      = trans('admin.A0007');
					}
						if($isInserted){
							$this->status       = true;
							$this->redirect     = true;
						}

				}else{
					$this->jsondata = ___error_sanatizer($validator->errors());
				}
			}else{
				$data = [];

				if(!empty($id_concern)){
					$data['concern'] = \Models\DisputeConcern::select(['id_concern','en','id','cz','ta','hi'])->where('id_concern',$id_concern)->first();
				}
				$this->jsondata 	= view('backend.pages.dispute-concern')->with($data)->render();
				$this->redirect 	= 'render';
				$this->status  		= true;
			}
			return response()->json([
				'data'      => $this->jsondata,
				'status'    => $this->status,
				'message'   => $this->message,
				'redirect'  => $this->redirect,
			]);            
		}

		/**
         * [This method is used for adding degree] 
         * @param  Request
         * @return Json Response
         */

		public function add_degree(Request $request){
			$id_degree = !empty($request->id_degree) ? ___decrypt($request->id_degree) : '';

			if(!empty($request->action)){
				$validator = \Validator::make($request->all(),[
					'degree'        => validation('admin_degree')
				],[
					'degree.required'   => trans('admin.A0040'),
					'degree.string'     => trans('admin.A0041')
				]);

				if($validator->passes()){
					if(!empty($id_degree)){
						$degree = \Models\Listings::degrees("single",['id_degree','degree_name']," id_degree != {$id_degree} AND degree_name = '{$request->degree}' AND degree_status != 'trashed'");
						if(empty($degree)){
							$degreeData = [
								'degree_name'   => $request->degree,
								'updated'       => date('Y-m-d H:i:s')
							];
							$isInserted = \Models\Listings::update_degree($id_degree,$degreeData);
							$this->message      = trans('admin.A0065');
						}else{
							$isInserted 		= true;
							$this->message      = trans('admin.A0064');
						}
						
					}else{
						$degree = \Models\Listings::degrees("single",['id_degree','degree_name']," degree_name = '{$request->degree}' AND degree_status != 'trashed'");
						if(empty($degree)){
							$degreeData = [
								'degree_name'   => $request->degree,
								'degree_status' => 'active',
								'created'       => date('Y-m-d H:i:s'),
								'updated'       => date('Y-m-d H:i:s')
							];
							$isInserted = \Models\Listings::add_degree($degreeData);
							$this->message      = trans('admin.A0042');
						}else{
							$isInserted 		= true;
							$this->message      = trans('admin.A0064');
						}
						
					}

					if($isInserted){
						$this->status       = true;
						$this->redirect     = true;
					}
				}else{
					$this->jsondata = ___error_sanatizer($validator->errors());
				}
			}else{
				$data = [];
				if(!empty($id_degree)){
					$data['degree'] = \Models\Listings::degrees("single",['id_degree','degree_name']," id_degree = {$id_degree} ");
				}

				$this->jsondata 	= view('backend.pages.degree')->with($data)->render();
				$this->redirect 	= 'render';
				$this->status  		= true;
			}
			return response()->json([
				'data'      => $this->jsondata,
				'status'    => $this->status,
				'message'   => $this->message,
				'redirect'  => $this->redirect,
			]);            
		}

		/**
         * [This method is used for adding certificate] 
         * @param  Request
         * @return Json Response
         */

		public function add_certificate(Request $request){
			$id_certificate = !empty($request->id_certificate) ? ___decrypt($request->id_certificate) : '';

			if(!empty($request->action)){
				$validator = \Validator::make($request->all(),[
					'certificate_name'        => validation('admin_certificate')
				],[
					'certificate.required'   => trans('admin.A0044'),
					'certificate.string'     => trans('admin.A0045')
				]);
				if($validator->passes()){
					if(!empty($id_certificate)){
						$certificate = \Models\Listings::certificates("single",['id_cetificate','certificate_name']," id_cetificate != {$id_certificate} AND certificate_name = '{$request->certificate_name}' AND certificate_status != 'trashed'");
						if(empty($certificate)){
							$certificateData = [
								'certificate_name'      => $request->certificate_name,
								'updated'               => date('Y-m-d H:i:s')
							];
						
							$isInserted = \Models\Listings::update_certificate($id_certificate, $certificateData);
							$this->message      = trans('admin.A0065');
						}else{
							$isInserted = true;
							$this->message      = trans('admin.A0064');
						}
					}else{
						$certificate = \Models\Listings::certificates("single",['id_cetificate','certificate_name'],"certificate_name = '{$request->certificate_name}' AND certificate_status != 'trashed'");
						if(empty($certificate)){
							$certificateData = [
								'certificate_name'      => $request->certificate_name,
								'certificate_status'    => 'active',
								'created'               => date('Y-m-d H:i:s'),
								'updated'               => date('Y-m-d H:i:s')
							];
							$isInserted = \Models\Listings::add_certificate($certificateData);
							$this->message      = trans('admin.A0046');
						}else{
							$isInserted = true;
							$this->message      = trans('admin.A0064');
						}
					}
					
					if($isInserted){
						$this->status       = true;
						$this->redirect     = true;
					}
				}else{
					$this->jsondata = ___error_sanatizer($validator->errors());
				}
			}else{
				$data = [];
				if(!empty($id_certificate)){
					$data['certificate'] = \Models\Listings::certificates("single",['id_cetificate','certificate_name']," id_cetificate = {$id_certificate} ");
				}
				$this->jsondata 	= view('backend.pages.certificate')->with($data)->render();
				$this->redirect 	= 'render';
				$this->status  		= true;
			}
			return response()->json([
				'data'      => $this->jsondata,
				'status'    => $this->status,
				'message'   => $this->message,
				'redirect'  => $this->redirect,
			]);            
		}

		/**
         * [This method is used for adding college] 
         * @param  Request
         * @return Json Response
         */

		public function add_college(Request $request){
			$id_college = !empty($request->id_college) ? ___decrypt($request->id_college) : '';
			$validator = \Validator::make($request->all(),[
				'college_name'        => validation('admin_college')
			],[
				'college.required'   => trans('admin.A0048'),
				'college.string'     => trans('admin.A0049')
			]);

			if($validator->passes()){
				if(!empty($id_college)){
					$college = \Models\Listings::colleges("single",['id_college','college_name']," college_name = '{$request->college_name}' AND id_college != {$id_college} AND college_status != 'trashed' ");
					if(empty($college)){
						if(!empty($request->college_image)){
							$degreeData = [
								'college_name'      => $request->college_name,
								'image'				=> $request->college_image,
								'updated'           => date('Y-m-d H:i:s')
							];
						}else{
							$degreeData = [
								'college_name'      => $request->college_name,
								'updated'           => date('Y-m-d H:i:s')
							];
						}
						$isInserted = \Models\Listings::update_college($id_college,$degreeData);
						$this->message      = trans('admin.A0065');
					}else{
						$isInserted = true;
						$this->message      = trans('admin.A0064');
					}
				}else{
					$college = \Models\Listings::colleges("single",['id_college','college_name']," college_name = '{$request->college_name}' AND college_status != 'trashed' ");
					
					if(empty($college)){
						$degreeData = [
							'college_name'      => $request->college_name,
							'image'				=> $request->college_image,
							'college_status'    => 'active',
							'created'           => date('Y-m-d H:i:s'),
							'updated'           => date('Y-m-d H:i:s')
						];
						$isInserted = \Models\Listings::add_college($degreeData);
						$this->message      = trans('admin.A0050');
					}else{
						$isInserted = true;
						$this->message      = trans('admin.A0064');
					}
				}

				
				if($isInserted){
					$this->status       = true;
					$this->redirect     = url(sprintf('%s/college',ADMIN_FOLDER));
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
         * [This method is used for adding skill] 
         * @param Request
         * @return Json Response
         */
		
		public function add_skill(Request $request){
			$id_skill = !empty($request->id_skill) ? ___decrypt($request->id_skill) : ''; 
			$validator = \Validator::make($request->all(),[
				'skill_name'        => validation('admin_skill')
			],[
				'skill.required'            => trans('admin.A0054'),
				'skill.string'              => trans('admin.A0055')
			]);
			if($validator->passes()){
				if(!empty($id_skill )){
					$skill = \Models\Listings::getSkillwithIndustry("single",['id_skill']," id_skill != {$id_skill} AND {$this->prefix}skill.skill_name = '{$request->skill_name}' AND {$this->prefix}skill.skill_status != 'trashed' ");
					if(empty($skill)){
						$skillData = [
							'skill_name'   => $request->skill_name,
							'updated'      => date('Y-m-d H:i:s')
						];

						$isInserted = \Models\Listings::update_skill($id_skill,$skillData);
						$this->message      = trans('admin.A0065');
					}else{
						$isInserted = true;
						$this->message      = trans('admin.A0064');
					}
				}else{
					$skill = \Models\Listings::getSkillwithIndustry("single",['id_skill']," {$this->prefix}skill.skill_name = '{$request->skill_name}' AND {$this->prefix}skill.skill_status != 'trashed' ");
					if(empty($skill)){
						$skillData = [
							'skill_name'   => $request->skill_name,
							'skill_status' => 'active',
							'created'      => date('Y-m-d H:i:s'),
							'updated'      => date('Y-m-d H:i:s')
						];

						$isInserted = \Models\Listings::add_skill($skillData);
						$this->message      = trans('admin.A0056');							
					}else{
						$isInserted = true;
						$this->message      = trans('admin.A0064');
					}
				}
				
				if($isInserted){
					$this->status       = true;
					$this->redirect     = url(sprintf('%s/skill',ADMIN_FOLDER));
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
         * [This method is used for adding company] 
         * @param  Request
         * @return Json Response
         */

		public function add_company(Request $request){
			$id_company = !empty($request->id_company) ? ___decrypt($request->id_company) : '';
			$validator = \Validator::make($request->all(),[
				'company_name'        => validation('admin_company')
			],[
				'company.required'   => trans('admin.A0048'),
				'company.string'     => trans('admin.A0049')
			]);

			if($validator->passes()){
				if(!empty($id_company)){
					$company = \Models\Listings::companies("single",['id_company','company_name']," company_name = '{$request->company_name}' AND id_company != {$id_company} AND company_status != 'trashed' ");
					if(empty($company)){
						if(!empty($request->company_image)){
							$companyData = [
								'company_name'      => $request->company_name,
								'image'				=> $request->company_image,
								'updated'           => date('Y-m-d H:i:s')
							];
						}else{
							$companyData = [
								'company_name'      => $request->company_name,
								'updated'           => date('Y-m-d H:i:s')
							];
						}
						$isInserted = \Models\Listings::update_company($id_company,$companyData);
						$this->message      = trans('admin.A0065');
					}else{
						$isInserted = true;
						$this->message      = trans('admin.A0064');
					}
				}else{
					$company = \Models\Listings::companies("single",['id_company','company_name']," company_name = '{$request->company_name}' AND company_status != 'trashed' ");
					
					if(empty($company)){
						$companyData = [
							'company_name'      => $request->company_name,
							'image'				=> $request->company_image,
							'company_status'    => 'active',
							'created'           => date('Y-m-d H:i:s'),
							'updated'           => date('Y-m-d H:i:s')
						];
						$isInserted = \Models\Listings::add_company($companyData);
						$this->message      = trans('admin.A0067');
					}else{
						$isInserted = true;
						$this->message      = trans('admin.A0064');
					}
				}

				\Cache::forget('companies');
				\Cache::forget('company_images');
				
				if($isInserted){
					$this->status       = true;
					$this->redirect     = url(sprintf('%s/company',ADMIN_FOLDER));
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
         * [This method is used for employer subscription listing] 
         * @param  Request
         * @return \Illuminate\Http\Response
         */

		/*Subscription List*/
		public function employer_subscription_list(Request $request, Builder $htmlBuilder){
			if ($request->ajax()) {
				$userList = \Models\Employers::getSubscriptionList();
				return \Datatables::of($userList)
				->editColumn('action',function($userList) {
					$actionHTML = '<a href="'.url('administrator/subscription/detail/'.$userList->id_user.'').'" class="badge case-resolve bg-green">Detail</a>';

					if($userList->is_subscribed == 'Yes'){
						$actionHTML .= '<a href="javascript:;" onclick="deleteSubs('.$userList->id_user.')" class="badge case-resolve bg-red">Delete</a>';
					}

					return $actionHTML;
				})
				->make(true);
			}

			$data['html'] = $htmlBuilder
			->parameters([
				"dom" => "<'row' <'col-md-3'f><'col-md-3'><'col-md-6 filter-option'>>rt<'row' <'col-md-6'i><'col-md-6'p>>",
			])
			->addColumn(['data' => 'id_user', 'name' => 'id_user', 'title' => '#', 'width' => '1', 'searchable' => false, 'orderable' => false])
			->addColumn(['data' => 'name', 'name' => 'name', 'title' => 'Employer Name'])
			->addColumn(['data' => 'email', 'name' => 'email', 'title' => 'Email'])
			->addColumn(['data' => 'is_subscribed', 'name' => 'is_subscribed', 'title' => 'Is Subscribed'])
			->addColumn(['data' => 'action', 'name' => 'action', 'title' => 'Action','searchable' => false, 'orderable' => false]);

			return view('backend.subscription.list')->with($data);
		}

		/**
         * [This method is used for Employer subscription in detail] 
         * @param  Request
         * @return \Illuminate\Http\Response
         */

		public function employer_subscription_detail(Request $request, Builder $htmlBuilder){

			$userList = \Models\Employers::getSubscriptionDetail($request->id_user);
			if(empty($userList)){
				return redirect(ADMIN_FOLDER.'/employer/subscription');
			}

			if ($request->ajax()) {

				return \Datatables::of($userList)
				->make(true);
			}

			$data['html'] = $htmlBuilder
			->parameters([
				"dom" => "<'row' <'col-md-3'f><'col-md-3'><'col-md-6 filter-option'>>rt<'row' <'col-md-6'i><'col-md-6'p>>",
			])
			->addColumn(['data' => 'id_subscription', 'name' => 'id_subscription', 'title' => '#', 'width' => '1', 'searchable' => false, 'orderable' => false])
			->addColumn(['data' => 'plan_name', 'name' => 'plan_name', 'title' => 'Plan Name'])
			->addColumn(['data' => 'billingDayOfMonth', 'name' => 'billingDayOfMonth', 'title' => 'Billing Day Of Month'])
			->addColumn(['data' => 'price', 'name' => 'price', 'title' => 'Price($)'])
			->addColumn(['data' => 'nextBillingDate', 'name' => 'nextBillingDate', 'title' => 'Next Bill Date']);

			return view('backend.subscription.list')->with($data);
		}

		/**
         * [This method is used for subscription deletion] 
         * @param  Request
         * @return Json Response
         */

		public function subscription_delete(Request $request){
			$id_user = $request->id_user;

			$userDetail = \Models\Users::findById($id_user);

			if(!empty($userDetail) && !empty($userDetail['braintree_subscription_id'])){
				$result = \Braintree_Subscription::cancel($userDetail['braintree_subscription_id']);

				\Models\Users::change(
                    \Auth::user()->id_user,
                    [
                    'is_subscribed'=>'no',
                    'braintree_subscription_id'=> '',
                    ]
                );

				if($result->success){
					$this->status = true;
					$this->message = 'User subscription has been successfully cancelled.';
					return response()->json([
						'message'   => $this->message
					]);
				}
				else{
					$this->status = false;
					$this->message = 'Error occurred while cancelling.';
					return response()->json([
						'message'   => $this->message
					]);
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
         * [This method is used for subscriber news letter] 
         * @param  Request
         * @return \Illuminate\Http\Response
         */

		public function newsletter_subscriber(Request $request, Builder $htmlBuilder){
			$page = $request->page;
			if(empty($page)){
				$page = 'employer';
			}
			if ($request->ajax()) {
				$subscribeList = \Models\Users::getSubscribeUser($page);
				return \Datatables::of($subscribeList)
				->editColumn('action',function($subscribeList) {
					return '<a href="javascript:;" onclick="unsubscribe('.$subscribeList->id_user.')" class="badge case-resolve bg-red">Unsubscribe</a>';
				})
				->make(true);
			}

			$data['html'] = $htmlBuilder
			->addColumn(['data' => 'id_user', 'name' => 'id_user', 'title' => '#', 'width' => '1'])
			->addColumn(['data' => 'name', 'name' => 'name', 'title' => 'Name'])
			->addColumn(['data' => 'email', 'name' => 'email', 'title' => 'Email', 'width' => '1'])
			->addColumn(['data' => 'action', 'name' => 'action', 'title' => 'Action','searchable' => false, 'width' => '50', 'orderable' => false]);

			return view('backend.newsletter.regis-newsletter-subscriber')->with($data);
		}

		/**
         * [This method is used for newsletter of unsubscribed user's] 
         * @param  Request
         * @return Json Response
         */

		public function newsletter_unsubscribe_user(Request $request){

			\Models\Users::change($request->id_user,['newsletter_subscribed'=>'no']);

			$this->status = true;
			$this->message = 'User has been successfully unsubscribed.';
			return response()->json([
				'message'   => $this->message
			]);
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

            $talent_id         = $request->id_user;
            $this->jsondata    = \Models\Talents::get_calendar_availability($talent_id, $date);

            return response()->json([
                'data'      => $this->jsondata,
                'status'    => $this->status,
                'message'   => $this->message,
            ]);
        }

        /**
         * [This method is used for forum question listing] 
         * @param  Request
         * @return \Illuminate\Http\Response
         */

        public function forum_question_list(Request $request, Builder $htmlBuilder){

			if ($request->ajax()) {
				$questionList = \Models\Forum::getQuestionList();

				return \Datatables::of($questionList)
				->editColumn('action',function($questionList) {
					$actionHtml = '<a href="'.url('administrator/forum/question/detail/' . ___encrypt($questionList->id_question)).'" class="badge">Detail</a> ';
					/*$actionHtml .= '<a href="'.url('administrator/forum/question/reply/' . ___encrypt($questionList->id_question)).'" class="badge">Reply</a> ';*/
					
					/*if($questionList->status == 'Open'){
						$actionHtml .= '<a href="javascript:;" onclick="updateStatus('.$questionList->id_question.')" class="badge bg-green">Close</a> ';
					}else{
						$actionHtml .= '<a href="javascript:;" onclick="updateStatus('.$questionList->id_question.')" class="badge bg-green">Open</a> ';
					}*/

					$actionHtml .= '<a href="javascript:;" onclick="deleteQues('.$questionList->id_question.')" class="badge case-resolve bg-red">Delete</a> ';

					return $actionHtml;
				})
				->make(true);
			}

			$data['html'] = $htmlBuilder
			->addColumn(['data' => 'id_question', 'name' => 'id_question', 'title' => '#', 'width' => '1'])
			->addColumn(['data' => 'question_description', 'name' => 'question_description', 'title' => 'Question'])
			->addColumn(['data' => 'person_name', 'name' => 'person_name', 'title' => 'Name', 'width' => '100'])
			->addColumn(['data' => 'status', 'name' => 'status', 'title' => 'Status', 'width' => '100'])
			->addColumn(['data' => 'created', 'name' => 'created', 'title' => 'Date', 'width' => '100'])
			->addColumn(['data' => 'action', 'name' => 'action', 'title' => 'Action','searchable' => false, 'width' => '100', 'orderable' => false]);

			return view('backend.forum.question-list')->with($data);
		}

		/**
         * [This method is used for updating forum question] 
         * @param  Request
         * @return Json Response
         */

		public function forum_question_update(Request $request){
			$id_question = $request->id_question;
			$status = $request->status;

			$questionDetail = \Models\Forum::getQuestionById($id_question);

			$data = [
                'status'=>'close'
                ];
			if($questionDetail->status == 'pending' || $questionDetail->status == 'close'){
				$data = [
                'status'=>'open'
                ];
                if($questionDetail->status == 'pending'){
                	$data = [
	                'status'=>'open',
	                'approve_date' => date('Y-m-d H:i:s')
	                ];
                }
			}

			if(!empty($data)){
				$result = \Models\Forum::change($id_question, $data);
			}

			if($result){
				$this->status = true;
				$this->message = 'Question has been successfully update.';
				return response()->json([
					'message'   => $this->message
				]);
			}
			else{
				$this->status = false;
				$this->message = 'Error occurred while cancelling.';
				return response()->json([
					'message'   => $this->message
				]);
			}

			return response()->json([
				'data'      => $this->jsondata,
				'status'    => $this->status,
				'message'   => $this->message,
				'redirect'  => $this->redirect,
			]);
		}

		/**
         * [This method is used for updating forum answer] 
         * @param  null
         * @return Json SResponse
         */

		public function forum_answer_update(Request $request){
			$id_answer = $request->id_answer;
			$status = $request->status;

			$questionDetail = \Models\Forum::getAnswerById($id_answer);

			$data = [
                'status'=>'trash'
                ];
			if($questionDetail->status == 'pending' || $questionDetail->status == 'trash'){
				$data = [
                'status'=>'approve'
                ];
                if($questionDetail->status == 'pending'){
                	$data = [
	                'status'=>'approve',
	                'approve_date' => date('Y-m-d H:i:s')
	                ];
                }
			}

			if(!empty($data)){
				$result = \Models\Forum::changeReply($id_answer, $data);
			}

			if($result){
				$this->status = true;
				$this->message = 'Answer has been successfully update.';
				return response()->json([
					'message'   => $this->message
				]);
			}
			else{
				$this->status = false;
				$this->message = 'Error occurred while cancelling.';
				return response()->json([
					'message'   => $this->message
				]);
			}

			return response()->json([
				'data'      => $this->jsondata,
				'status'    => $this->status,
				'message'   => $this->message,
				'redirect'  => $this->redirect,
			]);
		}

		/**
         * [This method is used for forum's question deletion] 
         * @param  Request
         * @return Json Response
         */

		public function forum_question_delete(Request $request){
			$id_question = $request->id_question;

			$result = \Models\Forum::delete_question($id_question);

			if($result){
				$this->status = true;
				$this->message = 'Question has been successfully deleted.';
				return response()->json([
					'message'   => $this->message
				]);
			}
			else{
				$this->status = false;
				$this->message = 'Error occurred while cancelling.';
				return response()->json([
					'message'   => $this->message
				]);
			}

			return response()->json([
				'data'      => $this->jsondata,
				'status'    => $this->status,
				'message'   => $this->message,
				'redirect'  => $this->redirect,
			]);
		}

		/**
         * [This method is used for replying forum's question] 
         * @param  Request
         * @return \Illuminate\Http\Response
         */

		public function forum_question_reply(Request $request){
			$data['id_question'] 	= $request->id_question;
			$id_question 			= ___decrypt($request->id_question);
			$data['ques'] 			= \Models\Forum::getQuestionById($id_question);
			$data['backurl'] 		= url(sprintf('%s/', ADMIN_FOLDER.'/forum/question'));

			return view('backend.forum.question-reply')->with($data);
		}

		/**
         * [This method is used for inserting a forum's question reply] 
         * @param  Request
         * @return Json Response
         */

		public function forum_question_reply_insert(Request $request){
			$id_question = ___decrypt($request->id_question);
			$validator = \Validator::make($request->all(), [
				'answer_description'    => ['required']
			],[
				'answer_description.required' => trans('general.form_reply_required'),
			]);

			if ($validator->passes()) {
				$insertArr = [
				'id_question' => $id_question,
				'id_user' => SUPPORT_CHAT_USER_ID,
				'answer_description' => $request->answer_description,
				'status' => 'approve',
				'approve_date' => date('Y-m-d H:i:s'),
				'created' => date('Y-m-d H:i:s'),
				'updated' => date('Y-m-d H:i:s')
				];
				\Models\Forum::saveAnswer($insertArr);
				$this->status = true;
				$this->message = 'Reply has been added successfully.';
				$this->redirect = url(sprintf("%s/forum/question",ADMIN_FOLDER));
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
         * [This method is used for forum's questions in detail] 
         * @param  null
         * @return \Illuminate\Http\Response
         */

		public function forum_question_detail(Request $request){
			$data['id_question'] 	= $request->id_question;
			$id_question 			= ___decrypt($request->id_question);
			$data['ques']			= \Models\Forum::getQuestionById($id_question);
			$data['backurl']		= url(sprintf('%s/forum/question', $this->URI_PLACEHOLDER));
			$data['answer']			= \Models\Forum::getAnswerByQuesId($id_question);
			#dd($data['answer']);
			return view('backend.forum.question-detail')->with($data);
		}

		/**
         * [This method is used for forum's answers listing] 
         * @param  Request
         * @return \Illuminate\Http\Response
         */

		public function forum_list_answer(Request $request){
			$id_reply = $request->id_reply;
			$id_ques = $request->id_ques;
			$data['answer'] = \Models\Forum::getAnswerByQuesId($id_ques, $id_reply, 'child');
			return view('backend.forum.reply-detail')->with($data);
		}

		/**
         * [This method is used for forum's answers deletion] 
         * @param  Request
         * @return \Illuminate\Http\Response
         */

		public function forum_delete_answer(Request $request){
			$updateArr = [
			'status' => 'trash'
			];
			\Models\Forum::delete_reply($request->id_reply);
		}

		/**
         * [This method is used for adding forum's answer] 
         * @param  Request
         * @return \Illuminate\Http\Response
         */

		public function forum_add_answer(Request $request){
			$id_reply = $request->id_reply;
			$id_ques = $request->id_ques;
			$answer_description = $request->answer_description;

			$insertArr = [
				'id_question' => $id_ques,
				'id_user' => SUPPORT_CHAT_USER_ID,
				'answer_description' => $answer_description,
				'id_parent' => $id_reply,
				'status' => 'approve',
				'approve_date' => date('Y-m-d H:i:s'),
				'created' => date('Y-m-d H:i:s'),
				'updated' => date('Y-m-d H:i:s'),
			];
			\Models\Forum::saveAnswer($insertArr);
		}

		/**
         * [This method is used for html generation] 
         * @param  Id 
         * @return \Illuminate\Http\Response
         */

		public function generateHtml($answer, $html = ''){
            if(!empty($answer)){
                foreach ($answer as $element) {
                	$html .= '<div>
                        <div>'.$element['answer_description'].'</div>
                        <div>
                        <span>'.$element['up_counter'].' ups</span>
                        <span>by '.$element['person_name'].'</span>
                        <span>reply on '.$element['created'].'</span>
                        </div>
                    </div>';

                    if(!empty($element['children'])){
                    	$this->generateHtml($element['children'], $html);
                    }
                }
            }
            return $html;
        }

        /**
         * [This method is used for request payout listing] 
         * @param  Request
         * @return \Illuminate\Http\Response
         */

        public function listRequestPayout(Request $request, Builder $htmlBuilder){
        	if ($request->ajax()) {
				$disputeList = \Models\Payments::payoutList();

				return \Datatables::of($disputeList)
				->editColumn('startdate',function($item){
					return $item->startdate = ___d($item->startdate);
				})
				->editColumn('enddate',function($item){
					return $item->enddate = ___d($item->enddate);
				})
				->editColumn('start',function($item){
					if($item->start == 'pending'){
						return $item->start = '<span class="badge bg-gray">
							<i class="fa fa-times"></i> '.ucfirst($item->start).'
						</span>';
					}elseif($item->start == 'confirmed'){
						return $item->start = '<span class="badge bg-aqua">
							<i class="fa fa-check"></i> '.ucfirst($item->start).'
						</span>';
					}else{
						return $item->start;
					}
				})
				->editColumn('close',function($item){
					if($item->close == 'pending'){
						return $item->close = '<span class="badge bg-gray">
							<i class="fa fa-times"></i> '.ucfirst($item->close).'
						</span>';
					}elseif($item->close == 'confirmed'){
						return '<span class="badge bg-aqua">
							<i class="fa fa-check"></i> '.ucfirst($item->close).'
						</span>';
					}elseif($item->close == 'disputed'){
						return '<span class="badge bg-black">
							<i class="fa fa-times"></i> '.ucfirst($item->close).'
						</span>';
					}else{
						return $item->close;
					}
				})
				->editColumn('payment_due',function($item){
					return $item->payment_due = PRICE_UNIT.___format(___calculate_payment($item->employment,$item->quoted_price));
				})
				->editColumn('working_hours',function($item){
					return $item->working_hours = ___convert_time($item->working_hours).' Hrs';
				})
				->editColumn('action',function($item){
					if($item->close == 'confirmed'){
						return '<button class="btn badge case-resolve bg-red">Confirmed</button>';
					}else if($item->close == 'disputed'){
						return '<button class="btn badge case-resolve bg-black">Disputed</button>';
					}else{
						return '<button class="btn badge case-resolve bg-red">Not Confirmed</button>';
					}
				})
				->make(true);
			}

			$data['html'] = $htmlBuilder
			->addColumn(['data' => 'row_number', 'name' => 'row_number', 'title' => '#', 'width' => '1', 'searchable' => false, 'orderable' => false])
			->addColumn(['data' => 'employer_name', 'name' => 'employer_name', 'title' => 'Employer Name'])
			->addColumn(['data' => 'talent_name', 'name' => 'talent_name', 'title' => 'Talent Name'])
			->addColumn(['data' => 'startdate', 'name' => 'startdate', 'title' => 'Start Date'])
			->addColumn(['data' => 'enddate', 'name' => 'enddate', 'title' => 'End Date'])
			->addColumn(['data' => 'start', 'name' => 'start', 'title' => 'Start Confirm'])
			->addColumn(['data' => 'close', 'name' => 'close', 'title' => 'Close Confirm'])
			->addColumn(['data' => 'working_hours', 'name' => 'working_hours', 'title' => 'Working Hours'])
			->addColumn(['data' => 'payment_due', 'name' => 'payment_due', 'title' => 'Payment Due'])
			->addColumn(['data' => 'action', 'name' => 'action', 'title' => 'Action', 'width' => '1','searchable' => false, 'orderable' => false]);

			return view('backend.payout.request-payout')->with($data);
        }

        /**
         * [This method is used for currency listing] 
         * @param Request
         * @return \Illuminate\Http\Response
         */

        public function currency_list(Request $request, Builder $htmlBuilder){

			if ($request->ajax()) {
				$currencyList = \Models\Currency::getCurrencyList();
				return \Datatables::of($currencyList)
				->editColumn('action',function($currencyList) {
					if($currencyList->default_currency == 'Y'){
						$actionHtml = '<span class="badge bg-grey">Default</span>';
					}else{
						$actionHtml = '<a href="'.url('administrator/currency/edit/' . ___encrypt($currencyList->id)).'" class="badge">Edit</a>';
					}

					// if($currencyList->status == 'active'){
					// 	$actionHtml .= '<a href="javascript:;" onclick="updateStatus('.$currencyList->id.')" class="badge bg-green">Active</a>';
					// }
					// else{
					// 	$actionHtml .= '<a href="javascript:;" onclick="updateStatus('.$currencyList->id.')" class="badge bg-green">Inactive</a>';
					// }

					// $actionHtml .= '<a href="javascript:;" onclick="deleteCurrency('.$currencyList->id.')" class="badge case-resolve bg-red">Delete</a>';

					return $actionHtml;
				})
				->make(true);
			}

			$data['html'] = $htmlBuilder
			->addColumn(['data' => 'id', 'name' => 'id', 'title' => '#', 'width' => '1'])
			->addColumn(['data' => 'country_name', 'name' => 'country_name', 'title' => 'Country'])
			->addColumn(['data' => 'iso_code', 'name' => 'iso_code', 'title' => 'ISO Code'])
			->addColumn(['data' => 'sign', 'name' => 'sign', 'title' => 'Symbol', 'width' => '1'])
			->addColumn(['data' => 'action', 'name' => 'action', 'title' => 'Action','searchable' => false, 'width' => '50', 'orderable' => false]);

			return view('backend.currency.currency-list')->with($data);
		}

		/**
         * [This method is used for adding currency] 
         * @param Request
         * @return \Illuminate\Http\Response
         */

        public function add_currency(Request $request){
			$data['country'] 	= \Models\Listings::countries('array',['id_country','en as country_name']);
			$data['backurl']    = url(sprintf('%s/currency', $this->URI_PLACEHOLDER));
			return view('backend.currency.add-currency')->with($data);
		}

		/**
         * [This method is used for currency insertion] 
         * @param  Request
         * @return Json Response
         */

        public function insert_currency(Request $request){
			$validator = \Validator::make($request->all(), [
				'iso_code'    => ['required'],
				'sign'    => ['required'],
				'id_country'    => ['required']
			],[
				'iso_code.required' => trans('admin.iso_required'),
				'sign.required' => trans('admin.sign_required'),
				'id_country.required' => trans('admin.country_required')
			]);

			if ($validator->passes()) {
				$insertArr = [
				'iso_code' => $request->iso_code,
				'name' => $request->iso_code,
				'id_country' => $request->id_country,
				'sign' => $request->sign,
				'status' => 'active',
				'created' => date('Y-m-d H:i:s'),
				'updated' => date('Y-m-d H:i:s')
				];
				\Models\Currency::saveCurrency($insertArr);
				\Artisan::call('currencyconversion');
				$this->status = true;
				$this->message = 'Currency has been added successfully.';
				$this->redirect = url(sprintf("%s/currency",ADMIN_FOLDER));
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
         * [This method is used for editing currency] 
         * @param Request
         * @return \Illuminate\Http\Response
         */

		public function edit_currency(Request $request){
			$data['id_currency'] 	= $request->id;
			$id_currency 			= ___decrypt($request->id);
			$data['backurl']    	= url(sprintf('%s/currency', $this->URI_PLACEHOLDER));
			$data['curr_detail'] 	= \Models\Currency::getCurrencyById($id_currency);

			$data['country'] = \Models\Listings::countries('array',['id_country','en as country_name']);

			return view('backend.currency.edit-currency')->with($data);
		}

		/**
         * [This method is used for updating currency] 
         * @param  Request
         * @return Json Response
         */
        

        public function update_currency(Request $request){
			$id_currency = ___decrypt($request->id);

			$validator = \Validator::make($request->all(), [
				'iso_code'    => ['required'],
				'sign'    => ['required'],
				'id_country'    => ['required']
			],[
				'iso_code.required' => trans('admin.iso_required'),
				'sign.required' => trans('admin.sign_required'),
				'id_country.required' => trans('admin.country_required')
			]);

			if ($validator->passes()) {
				$insertArr = [
				'iso_code' => $request->iso_code,
				'id_country' => $request->id_country,
				'sign' => $request->sign,
				'updated' => date('Y-m-d H:i:s')
				];
				\Models\Currency::change($id_currency, $insertArr);
				//\Artisan::call('currencyconversion');
				$this->status = true;
				$this->message = 'Currency has been updated successfully.';
				$this->redirect = url(sprintf("%s/currency",ADMIN_FOLDER));
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
         * [This method is used for updating currency status] 
         * @param  Request
         * @return Json Response
         */

		public function update_currency_status(Request $request){
			$id_curr = $request->id_curr;

			$detail = \Models\Currency::getCurrencyById($id_curr);

			if($detail->status == 'active'){
				$updateStatus = 'inactive';
			}
			else{
				$updateStatus = 'active';
			}

			$result = \Models\Currency::change($id_curr,['status'=>$updateStatus]);

			if($result){
				$this->status = true;
				$this->message = 'Currency has been successfully update.';
				return response()->json([
					'message'   => $this->message
				]);
			}
			else{
				$this->status = false;
				$this->message = 'Error occurred.';
				return response()->json([
					'message'   => $this->message
				]);
			}

			return response()->json([
				'data'      => $this->jsondata,
				'status'    => $this->status,
				'message'   => $this->message,
				'redirect'  => $this->redirect,
			]);
		}

		/**
         * [This method is used for currency deletion] 
         * @param  Request
         * @return Json Response
         */

		public function delete_currency(Request $request){
			$id_curr = $request->id_curr;

			$result = \Models\Currency::change($id_curr,['status'=>'deleted']);

			if($result){
				$this->status = true;
				$this->message = 'Currency has been successfully deleted.';
				return response()->json([
					'message'   => $this->message
				]);
			}
			else{
				$this->status = false;
				$this->message = 'Error occurred while deleting.';
				return response()->json([
					'message'   => $this->message
				]);
			}

			return response()->json([
				'data'      => $this->jsondata,
				'status'    => $this->status,
				'message'   => $this->message,
				'redirect'  => $this->redirect,
			]);
		}

		/**
         * [This method is used for banner listing] 
         * @param  Request
         * @return \Illuminate\Http\Response
         */

		/*Banner Section*/
		public function banner_list(Request $request, Builder $htmlBuilder){

			if ($request->ajax()) {
				$bannerList = \Models\Banner::getBanner();
				return \Datatables::of($bannerList)
				->editColumn('action',function($bannerList) {
					$actionHtml = '<a href="'.url("administrator/banner/edit/{$bannerList->banner_section}").'" class="badge">View</a>';
					return $actionHtml;
				})
				->editColumn('updated',function($bannerList) {
					return ___d($bannerList->updated);
				})
				->editColumn('banner_section',function($bannerList) {
					return ___readable($bannerList->banner_section,true);
				})
				->make(true);
			}

			$data['html'] = $htmlBuilder
			->addColumn(['data' => 'row_number', 'name' => 'row_number', 'title' => '#', 'width' => '1'])
			->addColumn(['data' => 'banner_section', 'name' => 'banner_section', 'title' => 'Section'])
			->addColumn(['data' => 'updated', 'name' => 'updated', 'title' => 'Last Updated', 'width' => '120'])
			->addColumn(['data' => 'action', 'name' => 'action', 'title' => 'Action','searchable' => false, 'width' => '1', 'orderable' => false]);

			return view('backend.banner.banner-list')->with($data);
		}

		/**
         * [This method is used for banner edit] 
         * @param  Request
         * @return \Illuminate\Http\Response
         */

		public function banner_edit(Request $request,$section){
			$data['banners'] 			= \Models\Banner::getBannerBySection($section);
			$data['backurl']            = url(sprintf('%s/banner', $this->URI_PLACEHOLDER));

			return view('backend.banner.banner-edit')->with($data);
		}

		/**
         * [This method is used for updating banner] 
         * @param Request
         * @return Json Response
         */

		public function banner_update(Request $request){
			$id_banner = ___decrypt($request->id_banner);
			$pre_banner = \Models\Banner::getBannerById($id_banner);

			if($pre_banner->banner_variable == 'home-page'){
				$validator = \Validator::make($request->all(), [
					#'image_name'    => ['required'],
					'banner_text'    => ['required','min:'.BANNER_TEXT_MIN_LENGTH,'max:'.BANNER_TEXT_MAX_LENGTH]
				],[
					#'image_name.required' => trans('admin.banner_image_required'),
					'banner_text.required' => trans('admin.banner_text_required'),
					'banner_text.min' => trans('admin.banner_text_min'),
					'banner_text.max' => trans('admin.banner_text_max')
				]);
			}
			else{
				/*$validator = \Validator::make($request->all(), [
					'image_name'    => ['required']
				],[
					'image_name.required' => trans('admin.banner_image_required')
				]);*/
				$validator = \Validator::make($request->all(), [

				],[

				]);
			}

			if ($validator->passes()) {
				if(!empty($pre_banner) && !empty($request->image_name)){
					if(!empty($pre_banner->banner_image)){
						@unlink(public_path('uploads/banner/resize/' . $pre_banner->banner_image));
						@unlink(public_path('uploads/banner/thumbnail/' . $pre_banner->banner_image));
						@unlink(public_path('uploads/banner/' . $pre_banner->banner_image));
					}
				}

				$data = [
	                'banner_text' => $request->banner_text,
	                'updated' => date('Y-m-d H:i:s')
	            ];

	            if(!empty($request->image_name)){
	            	$data['banner_image'] = $request->image_name;
	            }

	            $isInserted = \Models\Banner::updateBanner($id_banner, $data);
				$this->status = true;
				$this->message = 'Banner has been updated successfully.';
			} else {
				$errors = json_decode(json_encode($validator->errors()));

				if(!empty($errors->image_name[0])){
					$errors->image_name_one[0] = $errors->image_name[0];
					unset($errors->image_name);
					$this->jsondata = $errors;
				}else{
					$this->jsondata = ___error_sanatizer($validator->errors());
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
         * [This method is used for uploading banner image] 
         * @param  Request
         * @return Json Response
         */

		public function banner_image_upload(Request $request){
			$data['id_banner'] = $request->id_banner;
			$id_banner = ___decrypt($request->id_banner);

			$validator = \Validator::make($request->all(), [
                "file"                      => ['required','validate_banner_type'],
            ],[
                'file.validate_banner_type'   => trans('general.M0497'),
            ]);

            if($validator->passes()){
                $folder = 'uploads/banner/';
                $resize = [
                'width' => BANNER_WIDTH,
                'height' => BANNER_HEIGHT
                ];
                $uploaded_file = upload_file($request,'file',$folder,true, $resize);

                $this->jsondata = [
                'img_html' => sprintf(ADMIN_BANNER_TEMPLATE,
                    asset('uploads/banner/thumbnail/' . $uploaded_file['filename']),
                    asset('/')
                	),
                'image' => $uploaded_file['filename']
                ];

                $this->status = true;
                $this->message  = sprintf(ALERT_SUCCESS,trans("general.M0110"));

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
         * [This method is used for banner image deletion] 
         * @param  Request
         * @return Json Response
         */
        
		public function banner_image_delete(Request $request){
			if(!empty($request->image_name)){
				@unlink(public_path('uploads/banner/resize/' . $request->image_name));
				@unlink(public_path('uploads/banner/thumbnail/' . $request->image_name));
				@unlink(public_path('uploads/banner/' . $request->image_name));
			}
			return response()->json([
                'data'      => [],
            ]);
		}

		/**
         * [This method is used for industry listing] 
         * @param  Request
         * @return \Illuminate\Http\Response
         */

		public function industry_list(Request $request, Builder $htmlBuilder){
			$data['page_title']         = 'Industry list';
			$data['add_url'] 			= url(sprintf('%s/industry/add',ADMIN_FOLDER));
			
			if ($request->ajax()) {
				$keys = [
					DB::raw('@row_number  := @row_number  + 1 AS row_number'),
					'industries.id_industry',
					'industries.en',
					\DB::Raw("IF(( {$this->prefix}industries.id != ''),{$this->prefix}industries.`id`, {$this->prefix}industries.`en`) as id"),
					\DB::Raw("IF(( {$this->prefix}industries.cz != ''),{$this->prefix}industries.`cz`, {$this->prefix}industries.`en`) as cz"),
					\DB::Raw("IF(( {$this->prefix}industries.ta != ''),{$this->prefix}industries.`ta`, {$this->prefix}industries.`en`) as ta"),
					\DB::Raw("IF(( {$this->prefix}industries.hi != ''),{$this->prefix}industries.`hi`, {$this->prefix}industries.`en`) as hi"),						
					'industries.status',
					'industries.is_tagged'
				];
				$industryList = \Models\Industries::allindustries("obj"," {$this->prefix}industries.parent = '0' AND {$this->prefix}industries.status != 'trashed'",$keys);
				return \Datatables::of($industryList)
				/*->filter(function ($instance) use($request){
                    if ($request->has('search')) {
                        if(!empty($request->search['value'])){
                            $instance->collection = $instance->collection->filter(function ($row) use ($request) {
                                return (
                                	\Str::contains(strtolower($row->name), strtolower($request->search['value']))
                                	|| 
                                	( strpos(strtolower($row->status), strtolower($request->search['value'])) === 0 ) 
                                ) ? true : false;
                            });
                        } 
                    }
                })*/
                ->editColumn('is_tagged',function($industryList){
                    if($industryList->is_tagged == DEFAULT_YES_VALUE){
                        return $industryList->is_tagged = '<a href="javascript:;" data-request="favorite" data-url="'.url(sprintf('%s/industry/tag?industry_id=%s',ADMIN_FOLDER,___encrypt($industryList->id_industry))).'"><img src="'.asset('images/star-tagged.png').'"></a>';
                    }else{
                        return $industryList->is_tagged = '<a href="javascript:;" data-request="favorite" data-url="'.url(sprintf('%s/industry/tag?industry_id=%s',ADMIN_FOLDER,___encrypt($industryList->id_industry))).'"><img src="'.asset('images/star-untagged.png').'"></a>';

                    }
                }) 					
				->editColumn('status',function($industryList){
				return $industryList->status = ucfirst($industryList->status);
				})                    
				->editColumn('action',function($industryList) use($request){
					$html = sprintf('
						<a 
						href="%s" 
						class="btn badge bg-black">
						Edit
						</a> ',
						url(
							sprintf(
								'%s/industry/edit?id_industry=%s',
								ADMIN_FOLDER,
								___encrypt($industryList->id_industry)
							)
						)
					);
					/*if($industryList->status == 'Active'){
						$html .= '<a 
						href="javascript:void(0);" 
						data-url="'.url(sprintf('%s/ajax/industry/status?id_industry=%s&status=inactive',ADMIN_FOLDER,$industryList->id_industry)).'" 
						data-request="ajax-confirm"
						data-ask_title="'.ADMIN_CONFIRM_TITLE.'"  
						data-ask="Do you really want to continue with this action?" 
						class="badge bg-green">Inactive</a>  ';
					}else{
						$html .= '<a 
						href="javascript:void(0);" 
						data-url="'.url(sprintf('%s/ajax/industry/status?id_industry=%s&status=active',ADMIN_FOLDER,$industryList->id_industry)).'" 
						data-request="ajax-confirm"
						data-ask_title="'.ADMIN_CONFIRM_TITLE.'"  
						data-ask="Do you really want to continue with this action?" 
						class="badge bg-green">Active</a>  ';                        
					}

					$html .= '<a 
						href="javascript:void(0);" 
						data-url="'.url(sprintf('%s/ajax/industry/status?id_industry=%s&status=trashed',ADMIN_FOLDER,$industryList->id_industry)).'" 
						data-request="ajax-confirm"
						data-ask_title="'.ADMIN_CONFIRM_TITLE.'"  
						data-ask="Do you really want to continue with this action?" 
						class="badge bg-red">Delete</a>  ';*/
					return $html;
				})
				->make(true);
			}
			$data['html'] = $htmlBuilder
			->addColumn(['data' => 'row_number', 'name' => 'row_number', 'title' => '#','width' => '1'])
			->addColumn(['data' => 'is_tagged', 'name' => 'is_tagged', 'title' => '&nbsp;', 'width' => '0', 'searchable' => false, 'orderable' => false])
			->addColumn(['data' => 'en', 'name' => 'en', 'title' => 'English'])
			->addColumn(['data' => 'id', 'name' => 'id', 'title' => 'Indonesia'])
			->addColumn(['data' => 'cz', 'name' => 'cz', 'title' => 'Mandarin'])
			->addColumn(['data' => 'ta', 'name' => 'ta', 'title' => 'Tamil'])
			->addColumn(['data' => 'hi', 'name' => 'hi', 'title' => 'Hindi'])
			// ->addColumn(['data' => 'status', 'name' => 'status', 'title' => 'Status', 'width' => '10'])
			->addColumn(['data' => 'action', 'name' => 'action', 'title' => 'Actions','searchable' => false, 'orderable' => false, 'width' => '10']);
			return view('backend.industry.industry-list')->with($data);
		}

		/**
         * [This method is used for sub industry listing] 
         * @param  Request
         * @return \Illuminate\Http\Response
         */

		public function sub_industry_list(Request $request, Builder $htmlBuilder){
			$data['page_title']         = 'Sub Industry list';
			$data['add_url'] 			= url(sprintf('%s/sub-industry/add',ADMIN_FOLDER));
			if ($request->ajax()) {
				$keys = [
					DB::raw('@row_number  := @row_number  + 1 AS row_number'),
					'industries.id_industry',
					'industries.en',
					\DB::Raw("IF(( {$this->prefix}industries.id != ''),{$this->prefix}industries.`id`, {$this->prefix}industries.`en`) as id"),
					\DB::Raw("IF(( {$this->prefix}industries.cz != ''),{$this->prefix}industries.`cz`, {$this->prefix}industries.`en`) as cz"),
					\DB::Raw("IF(( {$this->prefix}industries.ta != ''),{$this->prefix}industries.`ta`, {$this->prefix}industries.`en`) as ta"),
					\DB::Raw("IF(( {$this->prefix}industries.hi != ''),{$this->prefix}industries.`hi`, {$this->prefix}industries.`en`) as hi"),	
					'parent.en as industry',
					'industries.status'
				];

				$sub_indusrty_list = \Models\Industries::allindustries("obj"," {$this->prefix}industries.parent != '0' AND {$this->prefix}industries.status != 'trashed' ",$keys);
				return \Datatables::of($sub_indusrty_list)
				/*->filter(function ($instance) use($request){
                    if ($request->has('search')) {
                        if(!empty($request->search['value'])){
                            $instance->collection = $instance->collection->filter(function ($row) use ($request) {
                                return (
                                	\Str::contains(strtolower($row->industry), strtolower($request->search['value']))
                                	||
                                	\Str::contains(strtolower($row->name), strtolower($request->search['value']))
                                	|| 
                                	( strpos(strtolower($row->status), strtolower($request->search['value'])) === 0 ) 
                                ) ? true : false;
                            });
                        } 
                    }
                })*/
				->editColumn('status',function($sub_indusrty_list){
				return $sub_indusrty_list->status = ucfirst($sub_indusrty_list->status);
				})
				->editColumn('action',function($sub_indusrty_list) use($request){
					$html = sprintf('
						<a 
						href="%s" 
						class="btn badge bg-black">
						Edit
						</a> ',
						url(sprintf('%s/sub-industry/edit?id_industry=%s',ADMIN_FOLDER,___encrypt($sub_indusrty_list->id_industry))
						)
					);
					
					/*if($sub_indusrty_list->status == 'Active'){
						$html .= '<a 
						href="javascript:void(0);" 
						data-url="'.url(sprintf('%s/ajax/industry/status?id_industry=%s&status=inactive',ADMIN_FOLDER,$sub_indusrty_list->id_industry)).'" 
						data-request="ajax-confirm"
						data-ask_title="'.ADMIN_CONFIRM_TITLE.'"  
						data-ask="Do you really want to continue with this action?" 
						class="badge bg-green">Inactive</a>  ';
					}else{
						$html .= '<a 
						href="javascript:void(0);" 
						data-url="'.url(sprintf('%s/ajax/industry/status?id_industry=%s&status=active',ADMIN_FOLDER,$sub_indusrty_list->id_industry)).'" 
						data-request="ajax-confirm"
						data-ask_title="'.ADMIN_CONFIRM_TITLE.'"  
						data-ask="Do you really want to continue with this action?" 
						class="badge bg-green">Active</a>  ';                        
					}

					$html .= '<a 
						href="javascript:void(0);" 
						data-url="'.url(sprintf('%s/ajax/industry/status?id_industry=%s&status=trashed',ADMIN_FOLDER,$sub_indusrty_list->id_industry)).'" 
						data-request="ajax-confirm"
						data-ask_title="'.ADMIN_CONFIRM_TITLE.'"  
						data-ask="Do you really want to continue with this action?" 
						class="badge bg-red">Delete</a>  ';*/
					return $html;
				})
				->make(true);
			}
			$data['html'] = $htmlBuilder
			->addColumn(['data' => 'row_number', 'name' => 'row_number', 'title' => '#','width' => '1'])
			->addColumn(['data' => 'industry', 'name' => 'industry', 'title' => 'Industry'])
			->addColumn(['data' => 'en', 'name' => 'en', 'title' => 'English'])
			->addColumn(['data' => 'id', 'name' => 'id', 'title' => 'Indonesia'])
			->addColumn(['data' => 'cz', 'name' => 'cz', 'title' => 'Mandarin'])
			->addColumn(['data' => 'ta', 'name' => 'ta', 'title' => 'Tamil'])
			->addColumn(['data' => 'hi', 'name' => 'hi', 'title' => 'Hindi'])
			// ->addColumn(['data' => 'status', 'name' => 'status', 'title' => 'Status', 'width' => '10'])
			->addColumn(['data' => 'action', 'name' => 'action', 'title' => 'Actions', 'width' => '150','searchable' => false, 'orderable' => false, 'width' => '10']);			
			return view('backend.industry.industry-list')->with($data);
		}

		/**
         * [This method is used for Industry add edit ] 
         * @param  null
         * @return \Illuminate\Http\Response
         */

		public function industry_add_edit(Request $request){
			$data 					= [];
			$data['backurl']		= url(sprintf('%s/industry', $this->URI_PLACEHOLDER));
			$id_industry 			= $request->id_industry ? ___decrypt($request->id_industry) : '';
			if(!empty($id_industry)){
				$data['industry'] = \Models\Industries::allindustries("single"," id_industry = {$id_industry} ",[
					'id_industry',
					'en',
					'id',
					'cz',
					'ta',
					'hi',
					'image'
				]);
			}
			return view('backend.industry.industry-add-edit')->with($data);
		}

		/**
         * [This method is used for add edit sub industry] 
         * @param  Request
         * @return \Illuminate\Http\Response
         */

		public function sub_industry_add_edit(Request $request){

			$id_industry 			= !empty($request->id_industry) ? ___decrypt($request->id_industry) : '';
			$data['backurl']		= url(sprintf('%s/sub-industry', $this->URI_PLACEHOLDER));
			if(!empty($id_industry)){
				$data['industry'] = \Models\Industries::allindustries("single"," id_industry = {$id_industry} ",[
					'id_industry',
					'en',
					'id',
					'cz',
					'ta',
					'hi',
					'parent',
					'image'
				]);
			}
			$data['industries'] = \Cache::get('all_industries_name');
			return view('backend.industry.sub-industry-add-edit')->with($data);
		}

		/**
         * [This method is used for uploading Industry image] 
         * @param  Request
         * @return Json Response
         */

		public function industry_image_upload(Request $request){
			
			$validator = \Validator::make($request->all(), [
                "file"                      => ['required','validate_banner_type'],
            ],[
                'file.validate_banner_type'   => trans('general.M0120'),
            ]);

            if($validator->passes()){
                $folder = 'uploads/industry/';
                $resize = [
	                'width' 	=> INDUSTRY_CROP_WIDTH,
	                'height' 	=> INDUSTRY_CROP_HEIGHT
                ];
                $uploaded_file = upload_file($request,'file',$folder,true, $resize);
                
                $this->jsondata = sprintf(INDUSTRY_TEMPLATE,
                    'delete-image',
                    asset(sprintf("%s%s",$folder,$uploaded_file['filename'])),
                    asset(sprintf("%s%s%s",$folder,'resize/',$uploaded_file['filename'])),
                    'industry_image',
                    'delete-image',
                    asset('/'),
                    $folder.$uploaded_file['filename']
                );

                $this->status = true;
                $this->message  = sprintf(ALERT_SUCCESS,trans("general.M0110"));

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
         * [This method is used for Industry tag] 
         * @param  Request
         * @return Json Response
         */

		public function industry_tag(Request $request){
			$industry_id 	= ___decrypt($request->industry_id);
			
			$industry = json_decode(json_encode(\Models\Industries::allindustries("single"," id_industry = {$industry_id} ",[
				'is_tagged'
			])),true);	
			
			$isInserted 	= \Models\Industries::update_industry($industry_id,[
				'is_tagged' => ($industry['is_tagged'] == 'yes') ? 'no' : 'yes',
				'updated'	=> date('Y-m-d H:i:s')
			]);

			$industry = json_decode(json_encode(\Models\Industries::allindustries("single"," id_industry = {$industry_id} ",[
				'is_tagged'
			])),true);
            
            if($industry['is_tagged'] == 'yes'){
                $this->jsondata['html'] = '<img src="'.asset('images/star-tagged.png').'">';
            	$this->message = trans('website.W0617');
            }else{
                $this->jsondata['html'] = '<img src="'.asset('images/star-untagged.png').'">';
            	$this->message = trans('website.W0618');
            }
            
            $this->status  = true;

            return response()->json([
                'data'      => (object)$this->jsondata,
                'status'    => $this->status,
                'message'   => $this->message,
                'redirect'  => $this->redirect,
            ]);            
		}

		/**
         * [This method is used for skill]
         * @param  Request
         * @return \Illuminate\Http\Response
         */

		public function skill(Request $request, Builder $htmlBuilder){
			$data['page_title']         = 'Industry list';
			$data['add_url'] 			= url(sprintf('%s/skill/add',ADMIN_FOLDER));
			
			if ($request->ajax()) {
				$keys = [
					DB::raw('@row_number  := @row_number  + 1 AS row_number'),
					'id_skill',
					'skill_name',
					'skill_status'
				];

				$skill_list = \Models\Listings::getSkillwithIndustry("obj",$keys," skill_status != 'trashed' ");
				return \Datatables::of($skill_list)
				/*->filter(function ($instance) use($request){
                    if ($request->has('search')) {
                        if(!empty($request->search['value'])){
                            $instance->collection = $instance->collection->filter(function ($row) use ($request) {
                                return (
                                	\Str::contains(strtolower($row->industry_name), strtolower($request->search['value']))
                                	||
                                	\Str::contains(strtolower($row->skill_name), strtolower($request->search['value']))
                                	|| 
                                	( strpos(strtolower($row->skill_status), strtolower($request->search['value'])) === 0 ) 
                                ) ? true : false;
                            });
                        } 
                    }
                })*/					
				->editColumn('skill_status',function($skill_list){
				return $skill_list->skill_status = ucfirst($skill_list->skill_status);
				})
				->editColumn('action',function($skill_list) use($request){
					$html = sprintf('
						<a 
						href = "%s" 
						class="btn badge bg-black">
						Edit
						</a> ',
						url(sprintf('%s/skill/edit?id_skill=%s',ADMIN_FOLDER,___encrypt($skill_list->id_skill))
						)
					);
					
					/*if($skill_list->skill_status == 'Active'){
						$html .= '<a 
						href="javascript:void(0);" 
						data-url="'.url(sprintf('%s/ajax/skill/status?id_skill=%s&status=inactive',ADMIN_FOLDER,$skill_list->id_skill)).'" 
						data-request="ajax-confirm"
						data-ask_title="'.ADMIN_CONFIRM_TITLE.'"  
						data-ask="Do you really want to continue with this action?" 
						class="badge bg-green">Inactive</a>  ';
					}else{
						$html .= '<a 
						href="javascript:void(0);" 
						data-url="'.url(sprintf('%s/ajax/skill/status?id_skill=%s&status=active',ADMIN_FOLDER,$skill_list->id_skill)).'" 
						data-request="ajax-confirm"
						data-ask_title="'.ADMIN_CONFIRM_TITLE.'"  
						data-ask="Do you really want to continue with this action?" 
						class="badge bg-green">Active</a>  ';                        
					}

					$html .= '<a 
						href="javascript:void(0);" 
						data-url="'.url(sprintf('%s/ajax/skill/status?id_skill=%s&status=trashed',ADMIN_FOLDER,$skill_list->id_skill)).'" 
						data-request="ajax-confirm"
						data-ask_title="'.ADMIN_CONFIRM_TITLE.'"  
						data-ask="Do you really want to continue with this action?" 
						class="badge bg-red">Delete</a>  ';*/
					return $html;
				})
				->make(true);
			}
			$data['html'] = $htmlBuilder
			->addColumn(['data' => 'row_number', 'name' => 'row_number', 'title' => '#','width' => '1'])
			->addColumn(['data' => 'skill_name', 'name' => 'skill_name', 'title' => 'Skill name'])
			// ->addColumn(['data' => 'skill_status', 'name' => 'skill_status', 'title' => 'Status'])
			->addColumn(['data' => 'action', 'name' => 'action', 'title' => 'Actions', 'width' => '10','searchable' => false, 'orderable' => false]);
			return view('backend.skill.list')->with($data);
		}

		/**
         * [This method is used for adding edited skill] 
         * @param  Request
         * @return \Illuminate\Http\Response
         */

		public function add_edit_skill(Request $request){
			$data['industries_name'] 	= \Cache::get('industries_name');
			$data['subindustries_name'] = \Cache::get('subindustries_name');
			$data['backurl']    		= url(sprintf('%s/skill', $this->URI_PLACEHOLDER));
			$id_skill 	= $request->id_skill ? ___decrypt($request->id_skill) : '';
			
			if(!empty($id_skill)){
				$data['skill'] = \Models\Listings::getSkillwithIndustry("single",['id_skill','skill_name']," id_skill = {$id_skill} ");
			}
			
			return view('backend.skill.add-edit')->with($data);			
		}

		/**
         * [This method is used for college]
         * @param  Request
         * @return \Illuminate\Http\Response
         */

		public function college(Request $request, Builder $htmlBuilder){
			$data['page_title']         = 'College';
			$data['add_url'] 			= url(sprintf('%s/college/add',ADMIN_FOLDER));
			
				if ($request->ajax()) {
					$keys = [
						DB::raw('@row_number  := @row_number  + 1 AS row_number'),
						'id_college',
						'college_name',
						'college_status'
					];

					$college_list = \Models\Listings::colleges("obj",$keys," college_status != 'trashed' ");
					return \Datatables::of($college_list)
					/*->filter(function ($instance) use($request){
	                    if ($request->has('search')) {
	                        if(!empty($request->search['value'])){
	                            $instance->collection = $instance->collection->filter(function ($row) use ($request) {
	                                return (
	                                	\Str::contains(strtolower($row->college_name), strtolower($request->search['value']))
	                                	|| 
	                                	( strpos(strtolower($row->college_status), strtolower($request->search['value'])) === 0 ) 
	                                ) ? true : false;
	                            });
	                        } 
	                    }
	                })*/					
					->editColumn('college_status',function($college_list){
					return $college_list->college_status = ucfirst($college_list->college_status);
					})
					->editColumn('action',function($college_list) use($request){
						$html = sprintf('
							<a 
							href = "%s" 
							class="btn badge bg-black">
							Edit
							</a> ',
							url(sprintf('%s/college/edit?id_college=%s',ADMIN_FOLDER,___encrypt($college_list->id_college)))
						);
						
						/*if($college_list->college_status == 'Active'){
							$html .= '<a 
							href="javascript:void(0);" 
							data-url="'.url(sprintf('%s/ajax/college/status?id_college=%s&status=inactive',ADMIN_FOLDER,$college_list->id_college)).'" 
							data-request="ajax-confirm"
							data-ask_title="'.ADMIN_CONFIRM_TITLE.'"  
							data-ask="Do you really want to continue with this action?" 
							class="badge bg-green">Inactive</a>  ';
						}else{
							$html .= '<a 
							href="javascript:void(0);" 
							data-url="'.url(sprintf('%s/ajax/college/status?id_college=%s&status=active',ADMIN_FOLDER,$college_list->id_college)).'" 
							data-request="ajax-confirm"
							data-ask_title="'.ADMIN_CONFIRM_TITLE.'"  
							data-ask="Do you really want to continue with this action?" 
							class="badge bg-green">Active</a>  ';                        
						}

						$html .= '<a 
							href="javascript:void(0);" 
							data-url="'.url(sprintf('%s/ajax/college/status?id_college=%s&status=trashed',ADMIN_FOLDER,$college_list->id_college)).'" 
							data-request="ajax-confirm"
							data-ask_title="'.ADMIN_CONFIRM_TITLE.'"  
							data-ask="Do you really want to continue with this action?" 
							class="badge bg-red">Delete</a>  ';*/
						return $html;
					})
					->make(true);
				}
				$data['html'] = $htmlBuilder
				->addColumn(['data' => 'row_number', 'name' => 'row_number', 'title' => '#','width' => '1'])
				->addColumn(['data' => 'college_name', 'name' => 'college_name', 'title' => 'College name'])
				// ->addColumn(['data' => 'college_status', 'name' => 'college_status', 'title' => 'Status'])
				->addColumn(['data' => 'action', 'name' => 'action', 'title' => 'Actions', 'width' => '10','searchable' => false, 'orderable' => false]);
			return view('backend.college.list')->with($data);
		}

		/**
         * [This method is used for adding edited college] 
         * @param  Request
         * @return \Illuminate\Http\Response
         */

		public function add_edit_college(Request $request){
			$data['backurl']    		= url(sprintf('%s/college', $this->URI_PLACEHOLDER));
			$id_college 	= $request->id_college ? ___decrypt($request->id_college) : '';
			
			if(!empty($id_college)){
				$data['college'] = \Models\Listings::colleges("single",['id_college','college_name','image']," id_college = {$id_college} ");
			}
			
			return view('backend.college.add-edit')->with($data);			
		}


		/**
         * [This method is used for uploading College image] 
         * @param  Request
         * @return Json Response
         */

		public function college_image_upload(Request $request){
			
			$validator = \Validator::make($request->all(), [
                "file"                      => ['required','validate_banner_type'],
            ],[
                'file.validate_banner_type'   => trans('general.M0120'),
            ]);

            if($validator->passes()){
                $folder = 'uploads/college/';
                $resize = [
	                'width' 	=> COLLEGE_CROP_WIDTH,
	                'height' 	=> COLLEGE_CROP_HEIGHT
                ];
                $uploaded_file = upload_file($request,'file',$folder,true, $resize);
                
                $this->jsondata = sprintf(ADMIN_COLLEGE_IMAGE_TEMPLATE,
                    'delete-image',
                    asset(sprintf("%s%s",$folder,$uploaded_file['filename'])),
                    asset(sprintf("%s%s%s",$folder,'resize/',$uploaded_file['filename'])),
                    'college_image',
                    'delete-image',
                    asset('/'),
                    $folder.$uploaded_file['filename']
                );

                $this->status = true;
                $this->message  = sprintf(ALERT_SUCCESS,trans("general.M0110"));

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
         * [This method is used for college]
         * @param  Request
         * @return \Illuminate\Http\Response
         */

		public function company(Request $request, Builder $htmlBuilder){
			$data['page_title']         = 'Company';
			$data['add_url'] 			= url(sprintf('%s/company/add',ADMIN_FOLDER));
			
				if ($request->ajax()) {
					$keys = [
						DB::raw('@row_number  := @row_number  + 1 AS row_number'),
						'id_company',
						'company_name',
						'company_status'
					];

					$company_list = \Models\Listings::companies("obj",$keys," company_status != 'trashed' ");
					return \Datatables::of($company_list)
					/*->filter(function ($instance) use($request){
	                    if ($request->has('search')) {
	                        if(!empty($request->search['value'])){
	                            $instance->collection = $instance->collection->filter(function ($row) use ($request) {
	                                return (
	                                	\Str::contains(strtolower($row->college_name), strtolower($request->search['value']))
	                                	|| 
	                                	( strpos(strtolower($row->college_status), strtolower($request->search['value'])) === 0 ) 
	                                ) ? true : false;
	                            });
	                        } 
	                    }
	                })*/					
					->editColumn('company_status',function($company_list){
					return $company_list->company_status = ucfirst($company_list->company_status);
					})
					->editColumn('action',function($company_list) use($request){
						$html = sprintf('
							<a 
							href = "%s" 
							class="btn badge bg-black">
							Edit
							</a> ',
							url(sprintf('%s/company/edit?id_company=%s',ADMIN_FOLDER,___encrypt($company_list->id_company)))
						);
						
						/*if($college_list->college_status == 'Active'){
							$html .= '<a 
							href="javascript:void(0);" 
							data-url="'.url(sprintf('%s/ajax/college/status?id_college=%s&status=inactive',ADMIN_FOLDER,$college_list->id_college)).'" 
							data-request="ajax-confirm"
							data-ask_title="'.ADMIN_CONFIRM_TITLE.'"  
							data-ask="Do you really want to continue with this action?" 
							class="badge bg-green">Inactive</a>  ';
						}else{
							$html .= '<a 
							href="javascript:void(0);" 
							data-url="'.url(sprintf('%s/ajax/college/status?id_college=%s&status=active',ADMIN_FOLDER,$college_list->id_college)).'" 
							data-request="ajax-confirm"
							data-ask_title="'.ADMIN_CONFIRM_TITLE.'"  
							data-ask="Do you really want to continue with this action?" 
							class="badge bg-green">Active</a>  ';                        
						}

						$html .= '<a 
							href="javascript:void(0);" 
							data-url="'.url(sprintf('%s/ajax/college/status?id_college=%s&status=trashed',ADMIN_FOLDER,$college_list->id_college)).'" 
							data-request="ajax-confirm"
							data-ask_title="'.ADMIN_CONFIRM_TITLE.'"  
							data-ask="Do you really want to continue with this action?" 
							class="badge bg-red">Delete</a>  ';*/
						return $html;
					})
					->make(true);
				}
				$data['html'] = $htmlBuilder
				->addColumn(['data' => 'row_number', 'name' => 'row_number', 'title' => '#','width' => '1'])
				->addColumn(['data' => 'company_name', 'name' => 'company_name', 'title' => 'Company name'])
				// ->addColumn(['data' => 'college_status', 'name' => 'college_status', 'title' => 'Status'])
				->addColumn(['data' => 'action', 'name' => 'action', 'title' => 'Actions', 'width' => '10','searchable' => false, 'orderable' => false]);
			return view('backend.company.list')->with($data);
		}

		/**
         * [This method is used for adding edited college] 
         * @param  Request
         * @return \Illuminate\Http\Response
         */

		public function add_edit_company(Request $request){
			$data['backurl']    		= url(sprintf('%s/company', $this->URI_PLACEHOLDER));
			$id_company 				= $request->id_company ? ___decrypt($request->id_company) : '';
			
			if(!empty($id_company)){
				$data['company'] = \Models\Listings::companies("single",['id_company','company_name','image']," id_company = {$id_company} ");
			}
			
			return view('backend.company.add-edit')->with($data);			
		}


		/**
         * [This method is used for uploading College image] 
         * @param  Request
         * @return Json Response
         */

		public function company_image_upload(Request $request){
			
			$validator = \Validator::make($request->all(), [
                "file"                      => ['required','validate_banner_type'],
            ],[
                'file.validate_banner_type'   => trans('general.M0120'),
            ]);

            if($validator->passes()){
                $folder = 'uploads/company/';
                $resize = [
	                'width' 	=> COMPANY_CROP_WIDTH,
	                'height' 	=> COMPANY_CROP_HEIGHT
                ];
                $uploaded_file = upload_file($request,'file',$folder,true, $resize);
                
                $this->jsondata = sprintf(ADMIN_COMPANY_IMAGE_TEMPLATE,
                    'delete-image',
                    asset(sprintf("%s%s",$folder,$uploaded_file['filename'])),
                    asset(sprintf("%s%s%s",$folder,'resize/',$uploaded_file['filename'])),
                    'company_image',
                    'delete-image',
                    asset('/'),
                    $folder.$uploaded_file['filename']
                );

                $this->status = true;
                $this->message  = sprintf(ALERT_SUCCESS,trans("general.M0110"));

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
         * [This method is used for contact] 
         * @param  Request
         * @return \Illuminate\Http\Response
         */

		public function contact(){
			$data['title'] = "Contact";
			
			return view('backend.help.contact')->with($data);
		}

        /**
         * [This method is used for handle contact page]
         * @param  Request
         * @return \Illuminate\Http\Response
         */

		public function _contactpage(Request $request){
            $validator = \Validator::make($request->all(), [
                'message'           => validation('message'),
            ],[
                'message.required'          => trans('general.M0034'),
                'message.string'            => trans('general.M0035'),
                'message.max'               => trans('general.M0036'),
            ]);
            
            if ($validator->passes()) {
                
                $configuration      = ___configuration(['site_email','site_name']);
                $message_subject    = 'Contact';
                $message_type       = 'contact-us';
			    
			    $sender_email       = \Auth::guard('admin')->user()->email;
                $sender_name       = \Auth::guard('admin')->user()->first_name.' '.\Auth::guard('admin')->user()->last_name;
                
                $isUpdated = \Models\Messages::compose($sender_name, $sender_email,$request->message,$message_subject,$message_type);

                if(!empty($isUpdated)){
                    $emailData              = ___email_settings();
                    $emailData['email']     = $sender_email;
                    $emailData['name']      = \Auth::guard('admin')->user()->first_name;
                    
                    ___mail_sender($sender_email,$emailData['name'],"user_contact",$emailData);
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
         * [This method is used for view of change password]
         * @param  Request
         * @return \Illuminate\Http\Response
         */
        
        public function change_password(Request $request){
            $data['title']       = trans('website.W0480');
            
            return view('backend.profile.changepassword')->with($data);
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
                "confirm_password"          => validation('new_confirm_password'),
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
                $isUpdated      = \Models\Talents::change(\Auth::user()->id_user,[
                    'password'  => bcrypt($request->new_password),
                    'updated'   => date('Y-m-d H:i:s')
                ]);
                
                $request->session()->flash('alert',sprintf(ALERT_SUCCESS,trans("general.M0301")));
                return redirect(url(sprintf('%s/change-password',ADMIN_FOLDER)));
            }

            return redirect()->back()->withErrors($validator)->withInput(); 
        }

        public function faq(Request $request,$type, Builder $htmlBuilder){
        	if($type == 'topic'){
	        	$data['title']		= 'Topic';
	        	$data['add_url']	= url(sprintf('%s/faq/%s/add',ADMIN_FOLDER,$type));
	        	if($request->ajax()){
	        		DB::statement(DB::raw('set @row_number=0'));
	        		$listing = \Models\Faqs::select([
						DB::raw('@row_number  := @row_number  + 1 AS row_number'),
						'id_faq',
						'status'
					])->with('description')->where('parent','=','0')->where('status','!=','trashed')->get();
					return \Datatables::of($listing)
					->editColumn('title',function($listing){
						return $listing->title = ucfirst($listing->description->title);
					})
					->editColumn('status',function($listing){
						return $listing->status = ucfirst($listing->status);
					})
					->editColumn('action',function($listing) use($type){
						$html = sprintf('
								<a href="%s" class="btn badge bg-black">Edit</a> ',
								url(sprintf('%s/faq/%s/edit?id_faq=%s',ADMIN_FOLDER,$type,___encrypt($listing->id_faq))
							)
						);

						$html .= '<a 
							href="javascript:void(0);" 
							data-url="'.url(sprintf('%s/ajax/faq/status?id_faq=%s&status=trashed',ADMIN_FOLDER,$listing->id_faq)).'" 
							data-request="ajax-confirm"
							data-ask_title="'.ADMIN_CONFIRM_TITLE.'" 
							data-ask="Do you really want to continue with this action?" 
							class="badge bg-red">Delete</a>  ';

						return $html;
					})
					->make(true);
	        	};

				$data['html'] = $htmlBuilder
				->addColumn(['data' => 'row_number', 'name' => 'row_number', 'title' => '#','width' => '1'])
				->addColumn(['data' => 'title', 'name' => 'title', 'title' => 'Topic'])
				->addColumn(['data' => 'status', 'name' => 'status', 'title' => 'Status'])
				->addColumn(['data' => 'action', 'name' => 'action', 'title' => 'Actions', 'width' => '10','searchable' => false, 'orderable' => false]);
	        	return view('backend.faq.topic.list')->with($data);
	        }else if($type == 'category'){
	        	$data['title']		= 'Category';
	        	$data['add_url']	= url(sprintf('%s/faq/%s/add',ADMIN_FOLDER,$type));

	        	if($request->ajax()){
	        		DB::statement(DB::raw('set @row_number=0'));
	        		$listing = \Models\Faqs::select([
						DB::raw('@row_number  := @row_number  + 1 AS row_number'),
						'id_faq',
						'status',
						'parent'
					])->with([
						'postcategory' => function ($q) {
							$q->select('id_faq','parent')
							->with([
								'description' => function($q){
									$q->select('faq_id','title');
								}
							]);
						},
						'description' => function($q){
							$q->select('faq_id','title');
						}
					])
					->where('type','category')->where('status','!=','trashed')
					->get();
					return \Datatables::of($listing)
					->editColumn('title',function($listing){
						return $listing->title = $listing->description->title;
					})
					->editColumn('topic',function($listing){
						return $listing->topic = $listing->postcategory->description->title;
					})
					->editColumn('status',function($listing){
						return $listing->status = ucfirst($listing->status);
					})
					->editColumn('action',function($listing) use($type){
						$html = sprintf('
								<a href="%s" class="btn badge bg-black">Edit</a> ',
								url(sprintf('%s/faq/%s/edit?id_faq=%s',ADMIN_FOLDER,$type,___encrypt($listing->id_faq))
							)
						);

						$html .= '<a 
							href="javascript:void(0);" 
							data-url="'.url(sprintf('%s/ajax/faq/status?id_faq=%s&status=trashed',ADMIN_FOLDER,$listing->id_faq)).'" 
							data-request="ajax-confirm"
							data-ask_title="'.ADMIN_CONFIRM_TITLE.'" 
							data-ask="Do you really want to continue with this action?" 
							class="badge bg-red">Delete</a>  ';						
						return $html;
					})
					->make(true);
	        	};

				$data['html'] = $htmlBuilder
				->addColumn(['data' => 'row_number', 'name' => 'row_number', 'title' => '#','width' => '1'])
				->addColumn(['data' => 'title', 'name' => 'title', 'title' => 'Category'])
				->addColumn(['data' => 'topic', 'name' => 'topic', 'title' => 'Topic'])
				->addColumn(['data' => 'status', 'name' => 'status', 'title' => 'Status'])
				->addColumn(['data' => 'action', 'name' => 'action', 'title' => 'Actions', 'width' => '10','searchable' => false, 'orderable' => false]);
	        	return view('backend.faq.topic.list')->with($data);
	        }else{
	        	$data['title']		= 'Post';
	        	$data['add_url']	= url(sprintf('%s/faq/%s/add',ADMIN_FOLDER,$type));

	        	if($request->ajax()){
	        		DB::statement(DB::raw('set @row_number=0'));
	        		$listing = \Models\Faqs::select([
						DB::raw('@row_number  := @row_number  + 1 AS row_number'),
						'id_faq',
						'status',
						'parent'
					])->with([
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
						},
						'description' => function($q){
							$q->select('faq_id','title');
						}
					])
					->where('type','post')->where('status','!=','trashed')
					->get();

					return \Datatables::of($listing)
					->editColumn('title',function($listing){
						return $listing->title = $listing->description->title;
					})
					->editColumn('category',function($listing){
						return $listing->category = $listing->postcategory->description->title;
					})
					->editColumn('topic',function($listing){
						return $listing->topic = $listing->postcategory->postcategory->description->title;
					})
					->editColumn('status',function($listing){
						return $listing->status = ucfirst($listing->status);
					})
					->editColumn('action',function($listing) use($type){
						$html = sprintf('
								<a href="%s" class="btn badge bg-black">Edit</a> ',
								url(sprintf('%s/faq/%s/edit?id_faq=%s',ADMIN_FOLDER,$type,___encrypt($listing->id_faq))
							)
						);
						$html .= '<a 
							href="javascript:void(0);" 
							data-url="'.url(sprintf('%s/ajax/faq/status?id_faq=%s&status=trashed',ADMIN_FOLDER,$listing->id_faq)).'" 
							data-request="ajax-confirm"
							data-ask_title="'.ADMIN_CONFIRM_TITLE.'" 
							data-ask="Do you really want to continue with this action?" 
							class="badge bg-red">Delete</a>  ';
						return $html;
					})
					->make(true);
	        	};

				$data['html'] = $htmlBuilder
				->addColumn(['data' => 'row_number', 'name' => 'row_number', 'title' => '#','width' => '1'])
				->addColumn(['data' => 'title', 'name' => 'title', 'title' => 'Post'])
				->addColumn(['data' => 'category', 'name' => 'category', 'title' => 'Category'])
				->addColumn(['data' => 'topic', 'name' => 'topic', 'title' => 'Topic'])
				->addColumn(['data' => 'status', 'name' => 'status', 'title' => 'Status'])
				->addColumn(['data' => 'action', 'name' => 'action', 'title' => 'Actions', 'width' => '10','searchable' => false, 'orderable' => false]);
	        	return view('backend.faq.topic.list')->with($data);
	        }
        }

        public function add_edit_faq(Request $request,$type){
			$data['title']		= ucfirst($type);
			$data['backurl']    = url(sprintf('%s/faq/%s', $this->URI_PLACEHOLDER,$type));
			$id_faq 			= $request->id_faq ? ___decrypt($request->id_faq) : '';

			if(!in_array($type,['topic','category','post'])){
				return redirect()->back();
			}

			if($type == 'topic'){
				if(!empty($id_faq)){
					$data['faq'] = \Models\Listings::faq("single",sprintf("{$this->prefix}faq.id_faq = {$id_faq} AND {$this->prefix}faq.type='%s'",$type),['faq.id_faq','faq_language.title']);
				}
			}elseif($type == 'category'){
				if(!empty($id_faq)){
					$data['faq'] = \Models\Listings::faq("single",sprintf("{$this->prefix}faq.id_faq = {$id_faq} AND {$this->prefix}faq.type='%s'",$type),['faq.id_faq','faq.parent','faq_language.title']);
				}
				$data['topic'] = \Models\Listings::faq("array",sprintf("{$this->prefix}faq.status = 'active' AND {$this->prefix}faq.type = 'topic' AND {$this->prefix}faq_language.language = 'en'"),['faq.id_faq','faq_language.title']);
			}else{
				if(!empty($id_faq)){
					$data['faq'] = \Models\Listings::faq("single",sprintf("{$this->prefix}faq.id_faq = {$id_faq} AND {$this->prefix}faq.type='%s'",$type),['faq.id_faq','faq.parent','faq_language.title','faq_language.description']);
				}
				$data['category'] = \Models\Listings::faq("array",sprintf("{$this->prefix}faq.status = 'active' AND {$this->prefix}faq.type = 'category' AND {$this->prefix}faq_language.language = 'en'"),['faq.id_faq','faq_language.title']);
			}
			return view(sprintf('backend.faq.%s.add-edit',$type))->with($data);
        }

        public function _add_edit_faq_topic(Request $request){
			$id_faq = !empty($request->id_faq) ? ___decrypt($request->id_faq) : '';
			$validator = \Validator::make($request->all(),[
				'title'        		=> validation('admin_faq_title')
			],[
				'title.required'   			=> trans('admin.A0068'),
				'title.string'     			=> trans('admin.A0069'),
			]);
			
			if($validator->passes()){
				if(!empty($id_faq)){
					$data['faq'] = \Models\Listings::faq("count",sprintf("{$this->prefix}faq.id_faq = {$id_faq} AND {$this->prefix}faq.type='topic' AND {$this->prefix}faq_language.language ='{$request->language}'"),['faq.id_faq']);
					$faqDetailData = [
						'faq_id'    	=> $id_faq,
						'title'    		=> $request->title,
						'language'  	=> $request->language,
					];

					if(!empty($data['faq'])){
						$faqDetailData['updated']  	= date('Y-m-d H:i:s');
						$isInserted 				= \Models\Listings::update_faq_detail($id_faq,$faqDetailData);
						$this->message      		= trans('admin.A0065');
					}else{
						$faqDetailData['updated']  	= date('Y-m-d H:i:s');
						$faqDetailData['created']  	= date('Y-m-d H:i:s');
						$isInserted 				= \Models\Listings::add_faq_detail($faqDetailData);
						$this->message      		= trans('admin.A0065');
					}
					$this->status       = true;
					$this->redirect     = url(sprintf('%s/faq/topic',ADMIN_FOLDER));
				}else{
					$faqData = [
						'type'      => 'topic',
						'parent'	=> 0,
						'sequence'  => 0,
						'status'  	=> 'Active',
						'created'   => date('Y-m-d H:i:s'),
						'updated'   => date('Y-m-d H:i:s')
					];

					$isInserted = \Models\Listings::add_faq($faqData);
					
					if(!empty($isInserted)){
						$faqDetailData = [
							'faq_id'    	=> $isInserted,
							'title'    		=> $request->title,
							'language'  	=> $request->language,
							'created'   	=> date('Y-m-d H:i:s'),
							'updated'   	=> date('Y-m-d H:i:s')
						];

						$isInserted = \Models\Listings::add_faq_detail($faqDetailData);
					}

					$this->message      = trans('admin.A0078');
					
					if($isInserted){
						$this->status       = true;
						$this->redirect     = url(sprintf('%s/faq/topic',ADMIN_FOLDER));
					}
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

        public function _add_edit_faq_category(Request $request){
			$id_faq = !empty($request->id_faq) ? ___decrypt($request->id_faq) : '';
			$validator = \Validator::make($request->all(),[
				'title'        		=> validation('admin_faq_title'),
				'topic'       		=> validation('admin_faq_topic'),
			],[
				'title.required'   			=> trans('admin.A0068'),
				'title.string'     			=> trans('admin.A0069'),
				'topic.required'     		=> trans('admin.A0074'),
				'topic.required'     		=> trans('admin.A0075')
			]);

			if($validator->passes()){
				if(!empty($id_faq)){
					$data['faq'] = \Models\Listings::faq("count",sprintf("{$this->prefix}faq.id_faq = {$id_faq} AND {$this->prefix}faq.type='category' AND {$this->prefix}faq_language.language ='{$request->language}'"),['faq.id_faq']);
					
					$faqData = [
						'parent' => $request->topic,
						'updated'=> date('Y-m-d H:i:s')
					];

					$faqDetailData = [
						'faq_id'    	=> $id_faq,
						'title'    		=> $request->title,
						'language'  	=> $request->language,
					];


					\Models\Listings::update_faq($id_faq,$faqData);

					if(!empty($data['faq'])){
						$faqDetailData['updated']  	= date('Y-m-d H:i:s');
						$isInserted 				= \Models\Listings::update_faq_detail($id_faq,$faqDetailData);
						$this->message      		= trans('admin.A0065');
					}else{
						$faqDetailData['updated']  	= date('Y-m-d H:i:s');
						$faqDetailData['created']  	= date('Y-m-d H:i:s');
						$isInserted 				= \Models\Listings::add_faq_detail($faqDetailData);
						$this->message      		= trans('admin.A0065');
					}
					$this->status       = true;
					$this->redirect     = url(sprintf('%s/faq/category',ADMIN_FOLDER));
				}else{
					$faqData = [
						'type'      => 'category',
						'parent'	=> $request->topic,
						'sequence'  => 0,
						'status'  	=> 'Active',
						'created'   => date('Y-m-d H:i:s'),
						'updated'   => date('Y-m-d H:i:s')
					];

					$isInserted = \Models\Listings::add_faq($faqData);
					
					if(!empty($isInserted)){
						$faqDetailData = [
							'faq_id'    	=> $isInserted,
							'title'    		=> $request->title,
							'language'  	=> $request->language,
							'created'   	=> date('Y-m-d H:i:s'),
							'updated'   	=> date('Y-m-d H:i:s')
						];

						$isInserted = \Models\Listings::add_faq_detail($faqDetailData);
					}

					$this->message      = trans('admin.A0079');
					
					if($isInserted){
						$this->status       = true;
						$this->redirect     = url(sprintf('%s/faq/category',ADMIN_FOLDER));
					}
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


        private function reportList(Request $request)
        {
        	$project =  \Models\Projects::select([
		            'id_project',
		            'title',
		            'users.name as employer_name',
		            'employment',
		            'startdate',
		            'enddate'
		        ])->where('projects.status','active')
		        ->leftJoin('users','users.id_user','=','projects.user_id')
		        ->leftJoin('transactions',function($q){
		        	$q->on('transaction_project_id','=','projects.id_project')
		        		->where('transaction_user_type','=','talent')
		        		->where('transaction_status','=','confirmed');
		        });

		        if($request->start_date && $request->end_date){

		        	$project->where('enddate','>=', $request->start_date)
		        		->where('enddate','<=', $request->end_date);
		        }

		        if($request->status){
		        	if($request->status =='closed_not_paid'){

		        		$project->where('project_status','closed')
		        			->whereNull('transactions.id_transactions');
		        	}elseif($request->status =='closed_paid'){
		        		
		        		$project->where('project_status','closed')
		        			->whereNotNull('transactions.id_transactions');		        		
		        	}else{
		        		$project->where('project_status',$request->status);	
		        	}
		        }

	        	return \Datatables::eloquent($project)
	        	->editColumn('employment',function($item){
	        		return ucfirst($item->employment);
	        	})
	        	->editColumn('startdate',function($item){
	        		return $item->startdate?date('d F Y', strtotime($item->startdate)):'-';
	        	})
	        	->editColumn('enddate',function($item){
	        		return $item->enddate?date('d F Y', strtotime($item->enddate)):'-';
	        	})
	        	->addColumn('action',function($item){

	        		return '<a href="'.url('administrator/project/detail/'.$item->id_project).'?slug=report" class="badge case-resolve">View</a>';
	        	})->make(true);
        }

        public function jobsReport(Request $request, Builder $htmlBuilder) {

        	if($request->ajax()) {
	        	
	        	return $this->reportList($request);
	        }


			if($request->download && $request->download =='csv'){

				$csvdata = $this->reportList($request)->getData(true);
				$csvdata = $csvdata['data'];
				$file_name = 'report_'.time().'.csv';

				$headers = [
	                "Content-type" => "application/csv",
	                "Content-Disposition" => "attachment; filename={$file_name}",
	                "Pragma" => "no-cache",
	                "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
	                "Expires" => "0"
	            ];
	            $callback = function() use ($csvdata)
	            {
	                #dd('weqw');
	                $file = fopen('php://output', 'w');
	                fputcsv($file,['Employer Name','Title','Employment','Start Date','End date']);

	                foreach ($csvdata as $cdata) {
	                    # code...
	                    fputcsv($file, [$cdata['employer_name'],$cdata['title'],$cdata['employment'],$cdata['startdate'],$cdata['enddate']]);
	                }

	                fclose($file);
	            };

	            $response = new StreamedResponse();
	            $response->setCallback($callback);

	            foreach ($headers as $header_key => $header_val) {
	            	
	            	$response->headers->set($header_key, $header_val);

	            }
	            // @codeCoverageIgnoreEnd
	            $response->send(); 

	            return;
			}

	        

	        $htmlBuilder->addColumn([
	        	'data' => 'employer_name', 
	        	'name' => 'users.name', 
	        	'title' => 'Employer Name'
	        ]);

	        $htmlBuilder->addColumn([
	        	'data' => 'title', 
	        	'name' => 'projects.title', 
	        	'title' => 'Title'
	        ]);

	        $htmlBuilder->addColumn([
	        	'data' => 'employment', 
	        	'name' => 'projects.employment', 
	        	'title' => 'Employment'
	        ]);

	        $htmlBuilder->addColumn([
	        	'data' => 'startdate', 
	        	'name' => 'projects.startdate', 
	        	'title' => 'Start Date'
	        ]);

	        $htmlBuilder->addColumn([
	        	'data' => 'enddate', 
	        	'name' => 'projects.enddate', 
	        	'title' => 'End Date'
	        ]);

	        $htmlBuilder->addColumn([
	            'data'=>'action',
	            'title'=>'Action',
				'orderable'      => false,
				'searchable'     => false,
	        ]);

	        //->parameters(['initComplete'=>'function(){  }'])

        	return view('backend.report.report_list',['html' => $htmlBuilder]);        	
        }

        public function _add_edit_faq_post(Request $request){
			$id_faq = !empty($request->id_faq) ? ___decrypt($request->id_faq) : '';
			$validator = \Validator::make($request->all(),[
				'title'        		=> validation('admin_faq_title'),
				'description'		=> validation('admin_faq_description'),
				'category'			=> validation('admin_faq_category')
			],[
				'title.required'   			=> trans('admin.A0068'),
				'title.string'     			=> trans('admin.A0069'),
				'description.required'     	=> trans('admin.A0070'),
				'description.required'     	=> trans('admin.A0071'),
				'category.required'     	=> trans('admin.A0076'),
				'category.required'     	=> trans('admin.A0077')
			]);
			
			if($validator->passes()){
				if(!empty($id_faq)){
					$data['faq'] = \Models\Listings::faq("count",sprintf("{$this->prefix}faq.id_faq = {$id_faq} AND {$this->prefix}faq.type='post' AND {$this->prefix}faq_language.language ='{$request->language}'"),['faq.id_faq']);

					$faqData = [
						'parent' => $request->category,
						'updated'=> date('Y-m-d H:i:s')
					];

					\Models\Listings::update_faq($id_faq,$faqData);

					$faqDetailData = [
						'faq_id'    	=> $id_faq,
						'title'    		=> $request->title,
						'description'   => $request->description,
						'language'  	=> $request->language,
					];
					if(!empty($data['faq'])){
						$faqDetailData['updated']  	= date('Y-m-d H:i:s');
						$isInserted 				= \Models\Listings::update_faq_detail($id_faq,$faqDetailData);
						$this->message      		= trans('admin.A0065');
					}else{
						$faqDetailData['updated']  	= date('Y-m-d H:i:s');
						$faqDetailData['created']  	= date('Y-m-d H:i:s');
						$isInserted 				= \Models\Listings::add_faq_detail($faqDetailData);
						$this->message      		= trans('admin.A0065');
					}
					$this->status       = true;
					$this->redirect     = url(sprintf('%s/faq/post',ADMIN_FOLDER));
				}else{
					$faqData = [
						'type'      => 'post',
						'parent'	=> $request->category,
						'sequence'  => 0,
						'status'  	=> 'Active',
						'created'   => date('Y-m-d H:i:s'),
						'updated'   => date('Y-m-d H:i:s')
					];

					$isInserted = \Models\Listings::add_faq($faqData);
					
					if(!empty($isInserted)){
						$faqDetailData = [
							'faq_id'    	=> $isInserted,
							'title'    		=> $request->title,
							'description'   => $request->description,
							'language'  	=> $request->language,
							'created'   	=> date('Y-m-d H:i:s'),
							'updated'   	=> date('Y-m-d H:i:s')
						];

						$isInserted = \Models\Listings::add_faq_detail($faqDetailData);
					}

					$this->message      = trans('admin.A0080');
					
					if($isInserted){
						$this->status       = true;
						$this->redirect     = url(sprintf('%s/faq/post',ADMIN_FOLDER));
					}
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
         * [This method is used for work fields]
         * @param  Request
         * @return \Illuminate\Http\Response
         */

		public function workfields(Request $request, Builder $htmlBuilder){
			$data['page_title']         = 'Work Fields';
			$data['add_url'] 			= url(sprintf('%s/workfields/add',ADMIN_FOLDER));
			
				if ($request->ajax()) {
					\DB::statement(DB::raw('set @row_number=0'));
					$keys = [
						\DB::raw('@row_number := @row_number  + 1 AS row_number'),
						'id_workfield',
						'field_name',
						'field_status'
					];

					$workfield_list = \Models\Listings::workfields("obj",$keys," field_status != 'trashed' ");

					return \Datatables::of($workfield_list)
					->editColumn('field_status',function($workfield_list){
						return $workfield_list->field_status = ucfirst($workfield_list->field_status);
					})
					->editColumn('action',function($workfield_list) use($request){
						$html = sprintf('
							<a 
							href="%s" 
							class="btn badge bg-black">
							Edit
							</a> ',
							url(sprintf('%s/workfields/edit?id_workfield=%s',ADMIN_FOLDER,___encrypt($workfield_list->id_workfield)))
						);
						return $html;
					})
					->make(true);
				}
				$data['html'] = $htmlBuilder
				->addColumn(['data' => 'row_number', 'name' => 'row_number', 'title' => '#','width' => '1'])
				->addColumn(['data' => 'field_name', 'name' => 'field_name', 'title' => 'Work Field'])
				->addColumn(['data' => 'action', 'name' => 'action', 'title' => 'Actions', 'width' => '10','searchable' => false, 'orderable' => false]);
			return view('backend.workfield.list')->with($data);
		}   

		/**
         * [This method is used for adding edited college] 
         * @param  Request
         * @return \Illuminate\Http\Response
         */

		public function add_edit_workfield(Request $request){
			$data['backurl']    		= url(sprintf('%s/workfields', $this->URI_PLACEHOLDER));
			$id_workfield 				= $request->id_workfield ? ___decrypt($request->id_workfield) : '';
			
			if(!empty($id_workfield)){
				$data['workfield'] = \Models\Listings::workfields("single",['id_workfield','field_name']," id_workfield = {$id_workfield} ");
			}

			return view('backend.workfield.add-edit')->with($data);			
		}  

		/**
         * [This method is used for adding work field] 
         * @param  Request
         * @return Json Response
         */

		public function add_workfield(Request $request){
			$id_workfield = !empty($request->id_workfield) ? ___decrypt($request->id_workfield) : '';
			$validator = \Validator::make($request->all(),[
				'field_name'        	=> validation('admin_workfield')
			],[
				'field_name.required'   => trans('admin.A0086'),
				'field_name.string'     => trans('admin.A0087')
			]);

			if($validator->passes()){
				if(!empty($id_workfield)){
					$workfields = \Models\Listings::workfields("single",['id_workfield','field_name']," field_name = '{$request->field_name}' AND id_workfield != {$id_workfield} AND field_status != 'trashed' ");
					if(empty($workfields)){
						$workfiledData = [
							'field_name'      	=> $request->field_name,
							'updated'           => date('Y-m-d H:i:s')
						];
						$isInserted = \Models\Listings::update_workfield($id_workfield,$workfiledData);
						$this->message      = trans('admin.A0065');
					}else{
						$isInserted = true;
						$this->message      = trans('admin.A0064');
					}
				}else{
					$workfields = \Models\Listings::workfields("single",['id_workfield','field_name']," field_name = '{$request->field_name}' AND field_status != 'trashed' ");
					
					if(empty($workfields)){
						$workfieldData = [
							'field_name'      	=> $request->field_name,
							'field_status'    	=> 'active',
							'created'           => date('Y-m-d H:i:s'),
							'updated'           => date('Y-m-d H:i:s')
						];
						$isInserted = \Models\Listings::add_workfield($workfieldData);
						$this->message      = trans('admin.A0089');
					}else{
						$isInserted = true;
						$this->message      = trans('admin.A0064');
					}
				}

				\Cache::forget('work_fields');
				
				if($isInserted){
					$this->status       = true;
					$this->redirect     = url(sprintf('%s/workfields',ADMIN_FOLDER));
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
         * [This method is used for listing of transaction] 
         * @param  Request
         * @return Json Response
         */

		public function transactionList(Request $request, Builder $htmlBuilder){

			if($request->ajax()) {
				return $this->transactionReport($request);
			}

			if($request->download && $request->download =='csv'){
				$csvdata = $this->transactionReport($request)->getData(true);
				$csvdata = $csvdata['data'];
				$file_name = 'transaction_log_'.time().'.csv';

					$headers = [
		                "Content-type" => "application/csv",
		                "Content-Disposition" => "attachment; filename={$file_name}",
		                "Pragma" => "no-cache",
		                "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
		                "Expires" => "0"
		            ];
		            $callback = function() use ($csvdata)
		            {
		                // dd('weqw');
		                $file = fopen('php://output', 'w');
		                fputcsv($file,['Talent Name','Employer Name','Job ID','Job Title','Transaction Total','Transaction Subtotal','Currency','Transaction API','Transaction ID','Transaction Comment','Transaction Type','Transaction Status','Transaction Date','Transaction Fee','Cancelation Status']);

		                foreach ($csvdata as $cdata) {
		                    # code...
		                    fputcsv($file, [$cdata['talent_name'],$cdata['employer_name'],$cdata['job_id'],$cdata['job_title'],$cdata['transaction_total'],$cdata['transaction_subtotal'],$cdata['currency'],$cdata['transaction_source'],$cdata['transaction_reference_id'],$cdata['transaction_comment'],$cdata['transaction_type'],$cdata['transaction_status'],$cdata['transaction_date'],$cdata['transaction_paypal_commission'],$cdata['is_cancelled']
		                    	]);
		                }

		                fclose($file);
		            };

		            $response = new StreamedResponse();
		            $response->setCallback($callback);

		            foreach ($headers as $header_key => $header_val) {
		            	$response->headers->set($header_key, $header_val);
		            }
		            // @codeCoverageIgnoreEnd
		            $response->send();
		            return;
			}

			$htmlBuilder->addColumn(['data' => 'talent_name', 'name' => 'users.name', 'title' => 'Talent Name'])
						->addColumn(['data' => 'employer_name', 'name' => 'users.name', 'title' => 'Employer Name'])
						->addColumn(['data' => 'job_id', 'name' => 'job_id', 'title' => 'Job ID', 'searchable' => false])
						->addColumn(['data' => 'job_title', 'name' => 'job_title', 'title' => 'Job Title','searchable' => false])
						->addColumn(['data' => 'transaction_total', 'name' => 'transaction_total', 'title' => 'Transaction Total'])
						->addColumn(['data' => 'transaction_subtotal', 'name' => 'transaction_subtotal', 'title' => 'Transaction Subtotal'])
						->addColumn(['data' => 'currency', 'name' => 'currency', 'title' => 'Currency'])
						->addColumn(['data' => 'transaction_source', 'name' => 'transaction_source', 'title' => 'Transaction API'])
						->addColumn(['data' => 'transaction_reference_id', 'name' => 'transaction_reference_id', 'title' => 'Transaction ID'])
						->addColumn(['data' => 'transaction_comment', 'name' => 'transaction_comment', 'title' => 'Transaction Comment'])
						->addColumn(['data' => 'transaction_type', 'name' => 'transaction_type', 'title' => 'Transaction Type'])
						->addColumn(['data' => 'transaction_status', 'name' => 'transaction_status', 'title' => 'Transaction Status'])
						->addColumn(['data' => 'transaction_date', 'name' => 'transaction_date', 'title' => 'Transaction Date'])
						->addColumn(['data' => 'transaction_paypal_commission', 'name' => 'transaction_paypal_commission', 'title' => 'Transaction Fee'])
						->addColumn(['data' => 'is_cancelled', 'name' => 'is_cancelled', 'title' => 'Cancelation Status'
	        ]);

        	return view('backend.transaction.list',['html' => $htmlBuilder]);
		}

		private function transactionReport(Request $request)
        {

        	$prefix = DB::getTablePrefix();
			$project =  \Models\Transactions::select([
					\DB::Raw("TRIM(IF({$prefix}users.last_name IS NULL, {$prefix}users.first_name, CONCAT({$prefix}users.first_name,' ',{$prefix}users.last_name))) as employer_name"),
					\DB::Raw("TRIM(IF({$prefix}talent.last_name IS NULL, {$prefix}talent.first_name, CONCAT({$prefix}talent.first_name,' ',{$prefix}talent.last_name))) as talent_name"),
					'projects.title as job_title',
					'transactions.transaction_project_id as job_id',
					'transactions.transaction_total as transaction_total',
					'transactions.transaction_subtotal as transaction_subtotal',
					'transactions.currency as currency',
					'transactions.transaction_source as transaction_source',
					'transactions.transaction_reference_id as transaction_reference_id',
					'transactions.transaction_comment as transaction_comment',
					'transactions.transaction_type as transaction_type',
					'transactions.transaction_status as transaction_status',
					'transactions.transaction_date as transaction_date',
					'transactions.transaction_paypal_commission as transaction_paypal_commission',
					'transactions.is_cancelled as is_cancelled',
					DB::raw("DATE_FORMAT({$prefix}transactions.transaction_date, '%Y-%m-%d') as formated_date")
		        ])
		        ->leftJoin('users','users.id_user','=','transactions.transaction_user_id')
		        ->leftJoin('projects','projects.id_project','=','transactions.transaction_project_id')
		        ->leftJoin('talent_proposals','talent_proposals.id_proposal','=','transactions.transaction_proposal_id')
		        ->leftJoin('users as talent','talent.id_user','=','talent_proposals.user_id');


	        if($request->start_date && $request->end_date){
	        	$project->where(DB::raw("DATE_FORMAT({$prefix}transactions.transaction_date, '%Y-%m-%d')"),'>=', $request->start_date)
	        			->where(DB::raw("DATE_FORMAT({$prefix}transactions.transaction_date, '%Y-%m-%d')"),'<=', $request->end_date);
	        }

	        $project->orderBy('id_transactions', 'DESC');
	        // $project->where('transaction_type','=', 'debit');

        	return \Datatables::eloquent($project)
        	->editColumn('talent_name',function($item){
        		if($item->employer_name != $item->talent_name){
    				return $item->talent_name;
        		}else{
    				return $item->talent_name;
        		}
    		})
    		->editColumn('employer_name',function($item){
        		if($item->employer_name == $item->talent_name){
        			return '';
        		}else{
    				return $item->employer_name;
        		}
    		})
        	->editColumn('transaction_source',function($item){
    			return ucfirst($item->transaction_source);
    		})
        	->editColumn('transaction_type',function($item){
    			return ucfirst($item->transaction_type);
    		})->editColumn('transaction_status',function($item){
    			return ucfirst($item->transaction_status);
    		})->editColumn('transaction_reference_id',function($item){
    			return ($item->transaction_reference_id!="" ? $item->transaction_reference_id:'Null');
    		})->editColumn('transaction_comment',function($item){
    			return ($item->transaction_comment!="" ? $item->transaction_comment:'Null');
    		})->editColumn('is_cancelled',function($item){
    			return ucfirst($item->is_cancelled);
    		})->make(true);       	
        }

        /**
         * [This method is used for listing of Coupon] 
         * @param  Request
         * @return Json Response
         */

		public function couponList(Request $request, Builder $htmlBuilder){

			if ($request->ajax()) {
				$coupon = \Models\Coupon::listCoupon();
				return \Datatables::of($coupon)
				->editColumn('start_date',function($item){
					return ___d($item->start_date);
				})
				->editColumn('expiration_date',function($item){
					return ___d($item->expiration_date);
				})
				->editColumn('created',function($item){
					return ___d($item->created);
				})
				->editColumn('status',function($item){
					return ucfirst($item->status);
				})
				->editColumn('action',function($item){
					$html = '';
					$html = '<a href="'.url(sprintf('%s/coupon/view?coupon_id=%s',ADMIN_FOLDER,___encrypt($item->id))).'" class="badge">View</a> ';
					$html .= '<a href="'.url(sprintf('%s/coupon/assign?coupon_id=%s',ADMIN_FOLDER,___encrypt($item->id))).'" class="badge bg-blue">Assign</a> ';
                    $html .= '<a href="javascript:void(0);" data-url="'.url(sprintf('%s/coupon/delete?id=%s',ADMIN_FOLDER,___encrypt($item->id))).'" data-request="status" data-ask="Do you really want to continue with this action?" class="badge bg-red" >Delete</a>';
					return $html;
				})
				->make(true);
			}

			$htmlBuilder->addColumn(['data' => 'row_number', 'name' => 'row_number', 'title' => '#', 'width' => '1']);
			$htmlBuilder->addColumn(['data' => 'code', 'name' => 'code', 'title' => 'Code']);
			$htmlBuilder->addColumn(['data' => 'start_date', 'name' => 'start_date', 'title' => 'Start Date']);
			$htmlBuilder->addColumn(['data' => 'expiration_date', 'name' => 'expiration_date', 'title' => 'Expiration Date']);

			$htmlBuilder->addColumn(['data' => 'created', 'name' => 'created', 'title' => 'Created Date']);
			$htmlBuilder->addColumn(['data' => 'action', 'name' => 'action', 'title' => 'Actions','width' => '130','searchable' => false, 'orderable' => false]);

			$data['html'] = $htmlBuilder;

			return view('backend.coupon.list')->with($data);

		}

		public function addCoupon(Request $request){

			$data['page_title']         = 'Add Coupon';
			$data['uri_placeholder']    = $this->URI_PLACEHOLDER;
			$data['backurl']            = url(sprintf('%s/coupon/list', $this->URI_PLACEHOLDER));
			$data['url'] 				= url(sprintf('%s/coupon/add', $this->URI_PLACEHOLDER));

			return view('backend.coupon.add')->with($data);
		}

		public function insertCoupon(Request $request){

			$validator = \Validator::make($request->all(), [
				'code_prefix'           => ['required',Rule::unique('coupon')],
				'discount'           	=> 'required|numeric|between:0.01,99.99',
				'startdate'             => 'required',
				'enddate'               => 'required'
			],[
				'code_prefix.required'  => 'Please enter prefix code',
				'code_prefix.unique'   	=> 'Entered prefix code already exists',
				'discount.required'  	=> 'Please enter discount',
				'discount.numeric'  	=> 'Discount has to be numeric',
				'discount.between'  	=> 'Discount should be in between 0.01 to 99.99',
				'startdate.required'   	=> 'Please select start date',
				'enddate.required'   	=> 'Please select end date',
			]);

			if ($validator->passes()) {

				$apiID  = "c9ce23b8-0c52-4095-b416-d92c49be9c3b";
				$apiKey = "4bfd2a38-1c28-41de-aebd-59c3c088b4af";

				$client = new VoucherifyClient($apiID, $apiKey);

				$start_date 	 = explode('/', $request->startdate);
				$expiration_date = explode('/', $request->enddate);

				$voucher = [
					'category' =>'Crowbar VOUCHER', 
					'type' => 'DISCOUNT_VOUCHER',
					'start_date'=> date(DATE_ISO8601, strtotime($start_date[2].'-'.$start_date[1].'-'.$start_date[0] .'00:00:00')),
					'expiration_date'=> date(DATE_ISO8601, strtotime($expiration_date[2].'-'.$expiration_date[1].'-'.$expiration_date[0].'23:59:59')),
					'active' => true
				];

				$voucher["discount"]= [
				       	"percent_off"=> number_format($request->discount,1),
				       	"type"=> "PERCENT"
				   	];

				   	$voucher["redemption"]= [
				       	"quantity"=> null
				   	];

				   	$voucher["code_config"]= [
				       	"pattern"=> $request->code_prefix."-#####" 
				   	];

				   	$voucher["metadata"]= [
				       	"test"=> true,
				     	"locale"=> "de-en" 
				   	];

				$this_voucher = $client->vouchers->create($voucher);

				$insertArr = [
					'code'				=> $this_voucher->code,
					'code_prefix'		=> $request->code_prefix,
					'discount'			=> number_format($request->discount,2),
					'start_date'		=> date('Y-m-d',strtotime($start_date[2].'-'.$start_date[1].'-'.$start_date[0])),
					'expiration_date'	=> date('Y-m-d',strtotime($expiration_date[2].'-'.$expiration_date[1].'-'.$expiration_date[0])),
					'status'			=> 'active',
					'created'			=> date('Y-m-d H:i:s'), 
					'updated'			=> date('Y-m-d H:i:s')
				];

				$resp = \DB::table('coupon')->insertGetId($insertArr);

				$this->status = true;
				$this->message = 'Coupon Code has been added successfully.';
				$this->redirect = url(sprintf("%s/coupon/list",ADMIN_FOLDER));
			}else {
				$this->jsondata = ___error_sanatizer($validator->errors());
			}

			return response()->json([
				'data'      => $this->jsondata,
				'status'    => $this->status,
				'message'   => $this->message,
				'redirect'  => $this->redirect,
			]);

		}

		public function assignCoupon(Request $request){

			$data['page_title']         = 'Assign Coupon';
			$data['uri_placeholder']    = $this->URI_PLACEHOLDER;
			$data['backurl']            = url(sprintf('%s/coupon/list', $this->URI_PLACEHOLDER));

			$coupon_code = \DB::table('coupon')
									->select('code')
									->where('id','=',___decrypt($request->coupon_id))
									->first();

			$data['coupon_code'] = $coupon_code->code; 

			return view('backend.coupon.assign')->with($data);

		}

		public function sentCouponCode(Request $request){


			$validator = \Validator::make($request->all(), [
				'email'           => 'required|email',
			],[
				'email.required'  => 'Please enter email',
				'email.email'     => 'Please enter valid email',
			]);

			if ($validator->passes()) {
				$emailData              = ___email_settings();
	            $emailData['email']     = $request->email;
	            $emailData['name']      = '';
	            $emailData['code']      = $request->coupon_code;
	                            
	            ___mail_sender($request->email,'',"send_coupon_code",$emailData);

	            $this->status 	= true;
				$this->message 	= 'Coupon code has been sent to this email.';
				$this->redirect = url(sprintf("%s/coupon/list",ADMIN_FOLDER));

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

		public function couponDelete(Request $request){

			$getCouponCode = \Models\Coupon::getCouponCodeById(___decrypt($request->id));
			
			$apiID  = "c9ce23b8-0c52-4095-b416-d92c49be9c3b";
			$apiKey = "4bfd2a38-1c28-41de-aebd-59c3c088b4af";
			$client = new VoucherifyClient($apiID, $apiKey);

	        try{
				$client->vouchers->delete($getCouponCode['code']);
				$deleteCouponCode = \Models\Coupon::deleteCouponCodeById(___decrypt($request->id));
            }catch(ClientException $exception){
                $this->status = false;
                $this->message = sprintf(ALERT_DANGER,'Something wrong, please try again.');
            }

            if(!empty($deleteCouponCode)){
                $this->status = true;
                $this->redirect = 'datatable';
                $this->message = sprintf(ALERT_SUCCESS,'Coupon code has been deleted successfully.');
            }else{
                $this->status = false;
                $this->message = sprintf(ALERT_DANGER,'Something wrong, please try again.');
            }

			return response()->json([
				'data'      => $this->jsondata,
				'status'    => $this->status,
				'message'   => $this->message,
				'redirect'  => $this->redirect,
			]);         

		}

		public function couponView(Request $request){

			$getCouponCode = ___decrypt($request->coupon_id);

			$data['coupon_code'] = \DB::table('coupon')
									->select('*')
									->where('id','=',___decrypt($request->coupon_id))
									->first();
			return view('backend.coupon.view')->with($data);

		}

		public function listTestimonial(Request $request, Builder $htmlBuilder){

			if ($request->ajax()) {

				$prefix = 	\DB::getTablePrefix();
							\DB::statement(\DB::raw('set @row_number=0'));
				$table_testimonial = \DB::table('testimonial');
				$table_testimonial->select([\DB::raw('@row_number := @row_number  + 1 AS row_number'),'testimonial.*']);
				$testimonial = $table_testimonial->get();
				return \Datatables::of($testimonial)
				->editColumn('created',function($item){
					return ___d($item->created);
				})
				->editColumn('action',function($item){
					$html = '';
					$html = '<a href="'.url(sprintf('%s/testimonial/edit?id=%s',ADMIN_FOLDER,___encrypt($item->id))).'" class="badge">Edit</a> ';
					return $html;
				})
				->make(true);
			}

			$htmlBuilder->addColumn(['data' => 'row_number', 'name' => 'row_number', 'title' => '#', 'width' => '1']);
			$htmlBuilder->addColumn(['data' => 'name', 'name' => 'name', 'title' => 'Name']);
			$htmlBuilder->addColumn(['data' => 'profession', 'name' => 'profession', 'title' => 'Profession']);

			$htmlBuilder->addColumn(['data' => 'created', 'name' => 'created', 'title' => 'Created Date']);
			$htmlBuilder->addColumn(['data' => 'action', 'name' => 'action', 'title' => 'Actions','width' => '130','searchable' => false, 'orderable' => false]);

			$data['html'] = $htmlBuilder;

			return view('backend.testimonial.list')->with($data);

		}

		public function editTestimonial(Request $request){

			$data 			 = [];
			$data['backurl'] = url(sprintf('%s/testimonial', $this->URI_PLACEHOLDER));
			$id 	 		 = $request->id ? ___decrypt($request->id) : '';
			if(!empty($id)){
				$data['testimonial'] = \Models\Testimonial::getTestimonialDetail($id);
			}
			return view('backend.testimonial.edit')->with($data);

		}

		public function updateTestimonial(Request $request){

			$validator = \Validator::make($request->all(), [
				'name'           		=> 'required',
				'profession'     		=> 'required',
				'description'           => 'required'
			],[
				'name.required'  		=> 'Please enter name',
				'profession.required'   => 'Please enter profession',
				'description.required'  => 'Please enter description',
			]);

			if ($validator->passes()) {
				$id = $request->id;
				$updateArr = [
					'name'		  => $request->name,
					'profession'  => $request->profession,
					'description' => $request->description,
					'image' 	  => $request->industry_image,
				];

				$isUpdated = \DB::table('testimonial')
								->where('id','=',$id)
								->update($updateArr);

				if(!empty($isUpdated)){
            		$this->status = true;
					$this->message = 'Testimonial has been updated successfully.';
					$this->redirect = url(sprintf("%s/testimonial",ADMIN_FOLDER));
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

		public function testimonial_image_upload(Request $request){

			$validator = \Validator::make($request->all(), [
                "file"                      => ['required','validate_banner_type'],
            ],[
                'file.validate_banner_type'   => trans('general.M0120'),
            ]);

            if($validator->passes()){
                $folder = 'uploads/testimonial/';
                $resize = [
	                'width' 	=> INDUSTRY_CROP_WIDTH,
	                'height' 	=> INDUSTRY_CROP_HEIGHT
                ];
                $uploaded_file = upload_file($request,'file',$folder,true, $resize);
                
                $this->jsondata = sprintf(INDUSTRY_TEMPLATE,
                    'delete-image',
                    asset(sprintf("%s%s",$folder,$uploaded_file['filename'])),
                    asset(sprintf("%s%s%s",$folder,'resize/',$uploaded_file['filename'])),
                    'industry_image',
                    'delete-image',
                    asset('/'),
                    $folder.$uploaded_file['filename']
                );

                $this->status = true;
                $this->message  = sprintf(ALERT_SUCCESS,trans("general.M0110"));

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

		public function activity_log_talent(Request $request, Builder $htmlBuilder){

			if($request->ajax()) {
	        	return $this->activity_log_talent_list($request);
	        }

			if($request->download && $request->download =='csv'){

				$csvdata = $this->activity_log_talent_list($request)->getData(true);
				$csvdata = $csvdata['data'];
				$file_name = 'talent_activity_log_'.time().'.csv';

				$headers = [
	                "Content-type" => "application/csv",
	                "Content-Disposition" => "attachment; filename={$file_name}",
	                "Pragma" => "no-cache",
	                "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
	                "Expires" => "0"
	            ];
	            $callback = function() use ($csvdata)
	            {
	                $file = fopen('php://output', 'w');
	                fputcsv($file,['Name','Activity','Reference ID','Reference Type','Reference Name','Date']);

	                foreach ($csvdata as $cdata) {
	                    # code...
	                    fputcsv($file, [$cdata['name'],$cdata['user_activity'],$cdata['reference_id'],$cdata['reference_type'],$cdata['reference_name'],$cdata['created']]);
	                }

	                fclose($file);
	            };

	            $response = new StreamedResponse();
	            $response->setCallback($callback);

	            foreach ($headers as $header_key => $header_val) {
	            	$response->headers->set($header_key, $header_val);
	            }
	            // @codeCoverageIgnoreEnd
	            $response->send(); 
	            return;
			}	        

	        $htmlBuilder->addColumn([
	        	'data' 	=> 'name', 
	        	'name' 	=> 'users.name', 
	        	'title' => 'Name'
	        ]);

	        $htmlBuilder->addColumn([
	        	'data' 	=> 'user_activity', 
	        	'name' 	=> 'activity.user_activity', 
	        	'title' => 'Activity',
	        	'searchable' => false,
				'orderable'  => false,	
	        ]);

	        $htmlBuilder->addColumn([
	        	'data' 	=> 'reference_id', 
	        	'name' 	=> 'activity.reference_id', 
	        	'title' => 'Reference ID'
	        ]);

	        $htmlBuilder->addColumn([
	        	'data' 	=> 'reference_type', 
	        	'name' 	=> 'activity.reference_type', 
	        	'title' => 'Reference Type'
	        ]);

	        $htmlBuilder->addColumn([
	        	'data' 	=> 'reference_name', 
	        	'name' 	=> 'activity.reference_name', 
	        	'title' => 'Reference Name',
	        	'searchable' => false,
				'orderable'  => false,
	        ]);

	        $htmlBuilder->addColumn([
	        	'data' 	=> 'created', 
	        	'name' 	=> 'activity.created', 
	        	'title' => 'Date'
	        ]);

	   		// $htmlBuilder->addColumn([
	   		//  'data'		 =>'action',
	   		//  'title'		 =>'Action',
			// 	'orderable'  => false,
			// 	'searchable' => false,
	   		// ]);

        	return view('backend.activity_log.talent.list',['html' => $htmlBuilder]); 
		}

		public function talentCountActivity(Request $request){

			$activities = ['talent-submit-proposal','talent-start-job','talent-completed-job','raise-dispute'];
			$activities_count = array();

			foreach ($activities as $key => $value) {
				$prefix  = DB::getTablePrefix();
	        	$project = \Models\ActivityLog::select([
			            'activity.user_id',
			            'activity.action as user_activity',
			            'activity.reference_id',
			            'activity.reference_type',
			            'activity.created',
						\DB::Raw('CONCAT('.$prefix.'users.first_name, " ", '.$prefix.'users.last_name ) AS name'),
						\DB::raw('IF('.$prefix.'activity.reference_type = "projects",'.$prefix.'projects.title,"-") AS reference_name')
			        ])
	        		->where('activity.action', $value)
	        		->where('user_type','talent')
			        ->leftJoin('users','users.id_user','=','activity.user_id')
			        ->leftJoin('projects','projects.id_project','=','activity.reference_id');
			        if($request->start_date && $request->end_date){
			        	$project->where('activity.created','>=', $request->start_date)
			        			->where('activity.created','<=', $request->end_date);
			        }
			        $project->where('activity.user_id', $request->talent_id);
			        $project->orderBy('activity.id_activity', 'DESC');
			        $activities_count[str_replace('-', '_',$value)] = $project->count();
			}

			return response()->json([
				'activities_count' => $activities_count
			]);
		}

		private function activity_log_talent_list(Request $request){

			$prefix  = DB::getTablePrefix();
        	$project = \Models\ActivityLog::select([
		            'activity.user_id',
		            'activity.action as user_activity',
		            'activity.reference_id',
		            'activity.reference_type',
		            'activity.created',
					\DB::Raw('CONCAT('.$prefix.'users.first_name, " ", '.$prefix.'users.last_name ) AS name'),
					\DB::raw('IF('.$prefix.'activity.reference_type = "projects",'.$prefix.'projects.title,"-") AS reference_name')
		        ])
        		->whereIn('activity.action',['login','talent-submit-proposal','talent-start-job','talent-completed-job','raise-dispute','talent-logout'])
        		->where('user_type','talent')
		        ->leftJoin('users','users.id_user','=','activity.user_id')
		        ->leftJoin('projects','projects.id_project','=','activity.reference_id');

		        if($request->start_date && $request->end_date){
		        	$project->where('activity.created','>=', $request->start_date)
		        			->where('activity.created','<=', $request->end_date);
		        }

		        $project->where('activity.user_id', $request->talent_id);
		        $project->orderBy('activity.id_activity', 'DESC');

	        	return \Datatables::eloquent($project)
	        	->editColumn('user_activity',function($item){
	        		return ucfirst(str_replace('-', ' ', $item->user_activity));
	        	})
	        	->editColumn('reference_type',function($item){
	        		return ucfirst($item->reference_type);
	        	})
	        	->editColumn('created',function($item){
	        		return $item->created? date('d F Y', strtotime($item->created)) : '-';
	        	})
	        	// ->addColumn('action',function($item){
	        	// 	return '<a href="'.url('administrator/project/detail/'.$item->user_id).'" class="badge case-resolve">View</a>';
	        	// })
	        	->make(true);
        }

        public function employerCountActivity(Request $request){

        	$activities = ['employer-post-job','employer-cancel-job','employer-delete-job','employer-payment-complete-job','employer-close-job','raise-dispute'];
			$activities_count = array();

			foreach ($activities as $key => $value) {
				$prefix  = DB::getTablePrefix();
	        	$project = \Models\ActivityLog::select([
			            'activity.user_id',
			            'activity.action as user_activity',
			            'activity.reference_id',
			            'activity.reference_type',
			        ])
	        		->where('activity.action',$value)
	        		->where('user_type','employer')
			        ->leftJoin('users','users.id_user','=','activity.user_id')
			        ->leftJoin('projects','projects.id_project','=','activity.reference_id');

			        if($request->start_date && $request->end_date){
			        	$project->where('activity.created','>=', $request->start_date)
			        			->where('activity.created','<=', $request->end_date);
			        }

			        $project->where('activity.user_id', $request->employer_id);
			        $project->orderBy('activity.id_activity', 'DESC');
			        $activities_count[str_replace('-', '_',$value)] = $project->count();
			}

			return response()->json([
				'activities_count' => $activities_count
			]);

        }

        public function activity_log_employer(Request $request, Builder $htmlBuilder){

			if($request->ajax()) {
	        	return $this->activity_log_employer_list($request);
	        }

			if($request->download && $request->download =='csv'){

				$csvdata = $this->activity_log_employer_list($request)->getData(true);
				$csvdata = $csvdata['data'];
				$file_name = 'employer_activity_log_'.time().'.csv';

				$headers = [
	                "Content-type" => "application/csv",
	                "Content-Disposition" => "attachment; filename={$file_name}",
	                "Pragma" => "no-cache",
	                "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
	                "Expires" => "0"
	            ];
	            $callback = function() use ($csvdata)
	            {
	                $file = fopen('php://output', 'w');
	                fputcsv($file,['Name','Activity','Reference ID','Reference Type','Reference Name','Date']);

	                foreach ($csvdata as $cdata) {
	                    # code...
	                    fputcsv($file, [$cdata['name'],$cdata['user_activity'],$cdata['reference_id'],$cdata['reference_type'],$cdata['reference_name'],$cdata['created']]);
	                }

	                fclose($file);
	            };

	            $response = new StreamedResponse();
	            $response->setCallback($callback);

	            foreach ($headers as $header_key => $header_val) {
	            	$response->headers->set($header_key, $header_val);
	            }
	            // @codeCoverageIgnoreEnd
	            $response->send(); 
	            return;
			}	        

	        $htmlBuilder->addColumn([
	        	'data' 	=> 'name', 
	        	'name' 	=> 'users.name', 
	        	'title' => 'Name'
	        ]);

	        $htmlBuilder->addColumn([
	        	'data' 	=> 'user_activity', 
	        	'name' 	=> 'activity.user_activity', 
	        	'title' => 'Activity',
	        	'searchable' => false,
				'orderable'  => false,
	        ]);

	        $htmlBuilder->addColumn([
	        	'data' 	=> 'reference_id', 
	        	'name' 	=> 'activity.reference_id', 
	        	'title' => 'Reference ID'
	        ]);

	        $htmlBuilder->addColumn([
	        	'data' 	=> 'reference_type', 
	        	'name' 	=> 'activity.reference_type', 
	        	'title' => 'Reference Type'
	        ]);

	        $htmlBuilder->addColumn([
	        	'data' 	=> 'reference_name', 
	        	'name' 	=> 'activity.reference_name', 
	        	'title' => 'Reference Name',
	        	'searchable' => false,
				'orderable'  => false,
	        ]);

	        $htmlBuilder->addColumn([
	        	'data' 	=> 'created', 
	        	'name' 	=> 'activity.created', 
	        	'title' => 'Date'
	        ]);

	   		// $htmlBuilder->addColumn([
	   		//  'data'		 =>'action',
	   		//  'title'		 =>'Action',
			// 	'orderable'  => false,
			// 	'searchable' => false,
	   		// ]);

        	return view('backend.activity_log.employer.list',['html' => $htmlBuilder]); 
		}

		private function activity_log_employer_list(Request $request){

			$prefix  = DB::getTablePrefix();
        	$project = \Models\ActivityLog::select([
		            'activity.user_id',
		            'activity.action as user_activity',
		            'activity.reference_id',
		            'activity.reference_type',
		            'activity.created',
					\DB::Raw('CONCAT('.$prefix.'users.first_name, " ", '.$prefix.'users.last_name ) AS name'),
					\DB::raw('IF('.$prefix.'activity.reference_type = "projects",'.$prefix.'projects.title,"-") AS reference_name')
		        ])
        		->whereIn('activity.action',['login','find-talents','employer-post-job','employer-cancel-job','employer-delete-job','proposal-details','employer-payment-complete-job','employer-close-job','raise-dispute'])
        		->where('user_type','employer')
		        ->leftJoin('users','users.id_user','=','activity.user_id')
		        ->leftJoin('projects','projects.id_project','=','activity.reference_id');

		        if($request->start_date && $request->end_date){
		        	$project->where('activity.created','>=', $request->start_date)
		        			->where('activity.created','<=', $request->end_date);
		        }

		        $project->where('activity.user_id', $request->employer_id);
		        $project->orderBy('activity.id_activity', 'DESC');

	        	return \Datatables::eloquent($project)
	        	->editColumn('user_activity',function($item){
	        		return ucfirst(str_replace('-', ' ', $item->user_activity));
	        	})
	        	->editColumn('reference_type',function($item){
	        		return ucfirst($item->reference_type);
	        	})
	        	->editColumn('created',function($item){
	        		return $item->created? date('d F Y', strtotime($item->created)) : '-';
	        	})
	        	// ->addColumn('action',function($item){
	        	// 	return '<a href="'.url('administrator/project/detail/'.$item->user_id).'" class="badge case-resolve">View</a>';
	        	// })
	        	->make(true);
        }

        public function payout_management_list(Request $request, Builder $htmlBuilder){

			if ($request->ajax()) {

				$payout_management_list = \Models\Payout_mgmt::listPayoutsMgmt();
				
				return \Datatables::of($payout_management_list)
				->editColumn('created',function($item){
					return ___d($item->created);
				})
				->editColumn('action',function($item){
					$html = '';

					// $html .= '<a 
					// 		href="javascript:void(0);" 
					// 		data-url="'.url(sprintf('%s/payout/management/delete?country_id=%s&status=deleted',ADMIN_FOLDER,___encrypt($item->country_id))).'" 
					// 		data-request="ajax-confirm"
					// 		data-ask_title="'.ADMIN_CONFIRM_TITLE.'"  
					// 		data-ask="Do you really want to continue with this action?" 
					// 		class="badge bg-red">Delete</a>  ';

					$html .= '<a href="'.url(sprintf('%s/payout/management/edit?country_id=%s',ADMIN_FOLDER,___encrypt($item->country_id))).'" 
							class="badge bg-black">Edit</a> ';

					$html .= '<a href="'.url(sprintf('%s/payout/management/duplicate?country_id=%s',ADMIN_FOLDER,___encrypt($item->country_id))).'" 
							class="badge">Duplicate</a> ';
							
					return $html;
				})
				->make(true);
			}

			$htmlBuilder->addColumn(['data' => 'row_number', 'name' => 'row_number', 'title' => '#', 'width' => '1']);
			$htmlBuilder->addColumn(['data' => 'country', 'name' => 'country', 'title' => 'Country']);
			// $htmlBuilder->addColumn(['data' => 'industry', 'name' => 'industry', 'title' => 'Profession']);
			// $htmlBuilder->addColumn(['data' => 'accept_escrow', 'name' => 'accept_escrow', 'title' => 'Accept Escrow']);
			// $htmlBuilder->addColumn(['data' => 'pay_commision', 'name' => 'pay_commision', 'title' => 'Pay Commision']);
			// $htmlBuilder->addColumn(['data' => 'pay_commision_percent', 'name' => 'pay_commision_percent', 'title' => 'Pay Commision (in %)']);
			// $htmlBuilder->addColumn(['data' => 'identification_number', 'name' => 'identification_number', 'title' => 'Identification Number']);
			$htmlBuilder->addColumn(['data' => 'status', 'name' => 'status', 'title' => 'Status']);
			$htmlBuilder->addColumn(['data' => 'created', 'name' => 'created', 'title' => 'Created Date']);
			$htmlBuilder->addColumn(['data' => 'action', 'name' => 'action', 'title' => 'Actions','width' => '130','searchable' => false, 'orderable' => false]);

			$data['html'] = $htmlBuilder;

			return view('backend.payout_mgmt.list')->with($data);
		}

		public function show_add_payout_mgmt(Request $request){

			$data['page_title']         = 'Add Payout Management';
			$data['uri_placeholder']    = $this->URI_PLACEHOLDER;
			$data['backurl']            = url(sprintf('%s/payout/management', $this->URI_PLACEHOLDER));
			$data['url'] 				= url(sprintf('%s/payout/management/add', $this->URI_PLACEHOLDER));

			return view('backend.payout_mgmt.add')->with($data);
		}

		public function add_payout_mgmt(Request $request){

			$validator = \Validator::make($request->all(), [
				'country'           	 => 'required',
			],[
				'country.required'  	 => 'Please select country',
			]);

			$validator->after(function ($validator) use($request) {

				$checkPayout = \Models\Payout_mgmt::validatePayoutsMgmt($request->country);
			    if ($checkPayout){
			        $validator->errors()->add('country','A Manual Payout type already exsists for this country.');
			    }

				foreach (___cache('industries_name') as $key => $value) {
					$non_reg_accept_escrow_name = 'non_reg_accept_escrow_'.$key;
					$accept_escrow_name = 'accept_escrow_'.$key;
					$pay_commision_name = 'pay_commision_'.$key;
					$pay_commision_val = 'pay_commision_percent_'.$key;

					$value_pay_commision = $request->$pay_commision_val == 0.00 ? 'no' : 'yes';
				    /*if(($request->$accept_escrow_name == 'yes' || $request->$non_reg_accept_escrow_name == 'yes') && $value_pay_commision == 'no'){ 
				        $validator->errors()->add($pay_commision_val,'The commission cannot be paid if there is no escrow.');
				    }*/
				    /*if($request->$non_reg_accept_escrow_name == 'no' && $value_pay_commision == 'yes'){ 
				        $validator->errors()->add($pay_commision_val,'The commission cannot be paid if there is no escrow.');
				    }*/

				    if(empty($request->$pay_commision_val)){
				    	$validator->errors()->add($pay_commision_val,'Please enter pay commsion %.'); 
				    }
				    if($request->$pay_commision_val >= 100.00){
				    	$validator->errors()->add($pay_commision_val,'Pay commsion cannot be 100% or above.'); 
				    }
				    if(!empty($request->$pay_commision_val) && count(explode('.', $request->$pay_commision_val))>2 ){
				    	$validator->errors()->add($pay_commision_val,'Please enter proper pay commsion %.'); 
				    }
				}

			});

			if ($validator->passes()) {

				foreach(___cache('industries_name') as $key => $value){
					$non_reg_accept_escrow_name1 = 'non_reg_accept_escrow_'.$key;
					$accept_escrow_name1 = 'accept_escrow_'.$key;
					$pay_commision_name1 = 'pay_commision_'.$key;
					$identification_no_name1 = 'identification_no_'.$key;
					$pay_commision_percent1 = 'pay_commision_percent_'.$key;
					$is_registered1 = 'is_registered_'.$key;

					$insertArr[$key] = [
						'country'				=> $request->country,
						'industry'				=> $key,
						'non_reg_accept_escrow'			=> $request->$non_reg_accept_escrow_name1,
						'accept_escrow'			=> $request->$accept_escrow_name1,
						'pay_commision'			=> $request->$pay_commision_percent1 == 0.00 ? 'no' : 'yes',
						'pay_commision_percent' => !empty($request->$pay_commision_percent1)?number_format($request->$pay_commision_percent1,2):'0.00',
						'identification_number'	=> $request->$identification_no_name1,
						'status'				=> 'active',
						'is_registered_show'	=> $request->$is_registered1,
						'created'				=> date('Y-m-d H:i:s'), 
						'updated'				=> date('Y-m-d H:i:s')
					];	
				}

				$resp = \DB::table('payout_mgmt')->insert($insertArr);

				$this->status = true;
				$this->message = 'This Commercial Configuration has been added successfully.';
				$this->redirect = url(sprintf("%s/payout/management",ADMIN_FOLDER));
			}else {
				$this->jsondata = ___error_sanatizer($validator->errors());
			}

			return response()->json([
				'data'      => $this->jsondata,
				'status'    => $this->status,
				'message'   => $this->message,
				'redirect'  => $this->redirect,
			]);
		}

		public function delete_payout_mgmt(Request $request){
			$isUpdated = \Models\Payout_mgmt::deletePayoutsMgmt(___decrypt($request->country_id));

			if($isUpdated){
	                $this->status = true;
	                $this->redirect = 'datatable';
	                $this->message = sprintf(ALERT_SUCCESS,'Status has been updated successfully.');
            }else{
                $this->status = false;
                $this->message = sprintf(ALERT_DANGER,'Something wrong, please try again.');
            }

			return response()->json([
				'data'      => $this->jsondata,
				'status'    => $this->status,
				'message'   => $this->message,
				'redirect'  => $this->redirect,
			]);
		}

		public function show_edit_payout_mgmt(Request $request){
			$data['page_title']      = 'Edit Payout Management';
			$data['uri_placeholder'] = $this->URI_PLACEHOLDER;
			$data['backurl']         = url(sprintf('%s/payout/management', $this->URI_PLACEHOLDER));
			$data['url']  			 = url(sprintf('%s/payout/management/add', $this->URI_PLACEHOLDER));

			$data['payout_det']   = \Models\Payout_mgmt::getPayoutByCountryId(___decrypt($request->country_id));
			$data['country_name'] = \Models\Payout_mgmt::getCountryNameById(___decrypt($request->country_id));
			$data['country_id']   = ___decrypt($request->country_id);
			return view('backend.payout_mgmt.edit')->with($data);
		}

		public function update_payout_mgmt(Request $request,$id){

			$validator = \Validator::make($request->all(), [
				'country'           	 => 'required',
			],[
				'country.required'  	 => 'Please select country',
			]);

			$validator->after(function ($validator) use($request) {

				foreach (___cache('industries_name') as $key => $value) {
					$non_reg_accept_escrow_name = 'non_reg_accept_escrow_'.$key;
					$accept_escrow_name = 'accept_escrow_'.$key;
					$pay_commision_name = 'pay_commision_'.$key;
					$pay_commision_val = 'pay_commision_percent_'.$key;

					$value_pay_commision = $request->$pay_commision_val == 0.00 ? 'no' : 'yes';
				    /*if($request->$accept_escrow_name == 'no' && $value_pay_commision == 'yes'){ 
				        $validator->errors()->add($pay_commision_val,'The commission cannot be paid if there is no escrow.');
				    }*/
				    if($request->$pay_commision_val >= 100.00){
				    	$validator->errors()->add($pay_commision_val,'Pay commsion cannot be 100% or above.'); 
				    }
				    if(empty($request->$pay_commision_val)){
				    	$validator->errors()->add($pay_commision_val,'Please enter pay commsion %.'); 
				    }
				    if(!empty($request->$pay_commision_val) && count(explode('.', $request->$pay_commision_val))>2 ){
				    	$validator->errors()->add($pay_commision_val,'Please enter proper pay commsion %.'); 
				    }
				}

			});

			if ($validator->passes()) {

				foreach(___cache('industries_name') as $key => $value){

					$non_reg_accept_escrow_name1 = 'non_reg_accept_escrow_'.$key;
					$accept_escrow_name1 = 'accept_escrow_'.$key;
					$identification_no_name1 = 'identification_no_'.$key;
					$pay_commision_percent1 = 'pay_commision_percent_'.$key;
					$update_id = 'payout_id_'.$key;
					$is_registered1 = 'is_registered_'.$key;

					$updateArr = [
						'non_reg_accept_escrow'	=> $request->$non_reg_accept_escrow_name1,
						'accept_escrow'			=> $request->$accept_escrow_name1,
						'pay_commision'			=> $request->$pay_commision_percent1 == 0.00 ? 'no' : 'yes',
						'pay_commision_percent' => !empty($request->$pay_commision_percent1)?number_format($request->$pay_commision_percent1,2):'0.00',
						'identification_number'	=> $request->$identification_no_name1,
						'is_registered_show'	=> $request->$is_registered1,
						'updated'				=> date('Y-m-d H:i:s')
					];	

					$update = \DB::table('payout_mgmt')
										->where('id','=',$request->$update_id)
										->update($updateArr);
				}

				$this->status = true;
				$this->message = 'This Commercial Configuration has been updated successfully.';
				$this->redirect = url(sprintf("%s/payout/management",ADMIN_FOLDER));
			}else {
				$this->jsondata = ___error_sanatizer($validator->errors());
			}

			return response()->json([
				'data'      => $this->jsondata,
				'status'    => $this->status,
				'message'   => $this->message,
				'redirect'  => $this->redirect,
			]);
		}

		public function show_duplicate_payout_mgmt(Request $request){

			$data['page_title']      = ' Payout Management';
			$data['uri_placeholder'] = $this->URI_PLACEHOLDER;
			$data['backurl']         = url(sprintf('%s/payout/management', $this->URI_PLACEHOLDER));
			$data['url']  			 = url(sprintf('%s/payout/management/add', $this->URI_PLACEHOLDER));

			$data['country_name'] = \Models\Payout_mgmt::getCountryNameById(___decrypt($request->country_id));
			$data['country_id']   = ___decrypt($request->country_id);

			$data['payout_det']   = \Models\Payout_mgmt::showConfigurationByCountryId(___decrypt($request->country_id));
			// dd($data['payout_det']);

			return view('backend.payout_mgmt.dublicate')->with($data);
		}

		public function add_duplicate_payout_mgmt(Request $request,$old_country_id){

			$validator = \Validator::make($request->all(), [
				'country'           	 => 'required',
			],[
				'country.required'  	 => 'Please select country',
			]);

			$validator->after(function ($validator) use($request) {

				$checkPayout = \Models\Payout_mgmt::validatePayoutsMgmt($request->country);
			    if ($checkPayout){
			        $validator->errors()->add('country','A Manual Payout type already exsists for this country.');
			    }
			});

			if($validator->passes()){

				$checkPayout = \Models\Payout_mgmt::getPayoutMgmtByCountry($old_country_id);
				foreach ($checkPayout as $key => $value) {
					$insertArr[$key] = [
						'country'				=> $request->country,
						'industry'				=> $value['industry'],
						'accept_escrow'			=> $value['accept_escrow'],
						'non_reg_accept_escrow'	=> $value['non_reg_accept_escrow'],
						'pay_commision'			=> $value['pay_commision'] == 0.00 ? 'no' : 'yes',
						'pay_commision_percent' => !empty($value['pay_commision_percent'])?number_format($value['pay_commision_percent'],2):'0.00',
						'identification_number'	=> $value['identification_number'],
						'status'				=> 'active',
						'is_registered_show'	=> $value['is_registered_show'],
						'created'				=> date('Y-m-d H:i:s'), 
						'updated'				=> date('Y-m-d H:i:s')
					];
				}
				
				$resp = \DB::table('payout_mgmt')->insert($insertArr);

				$this->status = true;
				$this->message = 'This Commercial Configuration has been duplicated successfully for selected country.';
				$this->redirect = url(sprintf("%s/payout/management",ADMIN_FOLDER));
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

		public function group_list(Request $request, Builder $htmlBuilder){

			$data['page_title'] = 'Group list';
			$data['add_url'] 	= url(sprintf('%s/group/add',ADMIN_FOLDER));
			
			if ($request->ajax()) {
				$groupList = \Models\Group::getGroupList();
				return \Datatables::of($groupList) 					
				->editColumn('status',function($groupList){
				return $groupList->status = ucfirst($groupList->status);
				})                    
				->editColumn('action',function($groupList) use($request){
					$html = '';
					$html .= sprintf('<a href="%s" class="btn badge bg-black">Edit</a> ',url(sprintf('%s/group/edit?id=%s',ADMIN_FOLDER,___encrypt($groupList->id))));
					$html .= '<a href="javascript:void(0);" data-url="'.url(sprintf('%s/group/delete?id=%s',ADMIN_FOLDER,___encrypt($groupList->id))).'" data-request="status" data-ask="Do you really want to continue with this action?" class="badge bg-red" >Delete</a>';
					return $html;
				})
				->make(true);
			}
			$data['html'] = $htmlBuilder
			->addColumn(['data' => 'row_number', 'name' => 'row_number', 'title' => '#','width' => '1'])
			->addColumn(['data' => 'name', 'name' => 'name', 'title' => 'Group Name'])
			// ->addColumn(['data' => 'status', 'name' => 'status', 'title' => 'Status'])
			->addColumn(['data' => 'created', 'name' => 'created', 'title' => 'Created Date'])
			->addColumn(['data' => 'action', 'name' => 'action', 'title' => 'Actions','searchable' => false, 'orderable' => false, 'width' => '120']);
			return view('backend.group.group-list')->with($data);
		}

		public function addGroup(Request $request){

			$data['page_title']         = 'Add Group';
			$data['uri_placeholder']    = $this->URI_PLACEHOLDER;
			$data['backurl']            = url(sprintf('%s/group/list', $this->URI_PLACEHOLDER));
			$data['url'] 				= url(sprintf('%s/group/add', $this->URI_PLACEHOLDER));

			return view('backend.group.add')->with($data);
		}

		public function insertGroup(Request $request){

			$validator = \Validator::make($request->all(), [
				'name'           => ['required',Rule::unique('group')->ignore('deleted','status')],
				// 'talent_id'		 => ['required','array']
			],[
				'name.required'  	  => 'Please enter group name.',
				'name.unique'    	  => 'Entered group name already exists.',
				'talent_id.required'  => 'Select members',
				'talent_id.array'  	  => 'Select members',
			]);

		    $validator->after(function($v) use($request){

		    	if(empty($request->talent_id) && empty($request->file)){
		    		$v->errors()->add('file', "Please select member(s) or upload file.");
		    	}

		        if(!empty($request->file)){
		            if(!in_array($request->file->getClientOriginalExtension(), ['xls','XLSX'])){
		                $v->errors()->add('file', "The attached file is invalid, file should be in xls format.");
		            }
		        }
		    });

			if ($validator->passes()) {

				$insertArr = [
					'name'				=> $request->name,
					'status'			=> 'active',
					'created'			=> date('Y-m-d H:i:s'), 
					'updated'			=> date('Y-m-d H:i:s')
				];

				$groupInsertedId = \Models\Group::saveGroup($insertArr);

				if(!empty($request->talent_id)){
					foreach ($request->talent_id as $key => $value) {
						$groupArr[$key] = [
							'group_id'			=> $groupInsertedId,
							'user_id'			=> $value,
							'created'			=> date('Y-m-d H:i:s'), 
							'updated'			=> date('Y-m-d H:i:s')
						];
					}
					$membersId = \DB::table('group_member')->insert($groupArr); 
				}

				if(!empty($request->file)){
					$path = Input::file('file')->getRealPath();
		            $data = Excel::load($path, function($reader) {
		            })->get();

		            $groupedArr[]= '';
		            foreach ($data as $key1 => $value1) {

		            	if(!empty($value1['email'])){
			            	$data_email = \Models\Users::select('id_user','email')
			            						->where('email',$value1['email'])
			            						->first();

			            	if(!empty($data_email)){
				            	if(!empty($request->talent_id) && !in_array($data_email['id_user'], $request->talent_id)){

				            		$groupedArr[$key1] = [
										'group_id'			=> $groupInsertedId,
										'user_id'			=> $data_email['id_user'],
										'created'			=> date('Y-m-d H:i:s'), 
										'updated'			=> date('Y-m-d H:i:s')
									];
				            	}else{

				            		$groupedArr[$key1] = [
										'group_id'			=> $groupInsertedId,
										'user_id'			=> $data_email['id_user'],
										'created'			=> date('Y-m-d H:i:s'), 
										'updated'			=> date('Y-m-d H:i:s')
									];

				            	}
			            	}					

		            	}
		            }

					$membersIdExcel = \DB::table('group_member')->insert($groupedArr);
				}

				$this->status = true;
				$this->message = 'Group and its member(s) have been added successfully.';
				$this->redirect = url(sprintf("%s/group/list",ADMIN_FOLDER));
			}else {
				$this->jsondata = ___error_sanatizer($validator->errors());
			}

			return response()->json([
				'data'      => $this->jsondata,
				'status'    => $this->status,
				'message'   => $this->message,
				'redirect'  => $this->redirect,
			]);
		}

		public function groupDelete(Request $request){

			$deleteGroup = \Models\Group::updateStatusGroupById(___decrypt($request->id));

			if(!empty($deleteGroup)){
                $this->status = true;
                $this->redirect = 'datatable';
                $this->message = sprintf(ALERT_SUCCESS,'Group has been deleted successfully.');
            }else{
                $this->status = false;
                $this->message = sprintf(ALERT_DANGER,'Something wrong, please try again.');
            }

			return response()->json([
				'data'      => $this->jsondata,
				'status'    => $this->status,
				'message'   => $this->message,
				'redirect'  => $this->redirect,
			]);
		}

		public function groupDeleteUser(Request $request){
			
			$deleteGroup = \Models\GroupMember::deleteGroupMembers(___decrypt($request->group_id),___decrypt($request->id));

			if(!empty($deleteGroup)){
                $this->status = true;
                $this->redirect = 'datatable';
                $this->message = sprintf(ALERT_SUCCESS,'Group User has been deleted successfully.');
            }else{
                $this->status = false;
                $this->message = sprintf(ALERT_DANGER,'Something wrong, please try again.');
            }

			return response()->json([
				'data'      => $this->jsondata,
				'status'    => $this->status,
				'message'   => $this->message,
				'redirect'  => $this->redirect,
			]);
		}

		public function showEditGroup(Request $request, Builder $htmlBuilder){

			$data['page_title']       = 'Edit Group';
			$data['uri_placeholder']  = $this->URI_PLACEHOLDER;
			$data['backurl']          = url(sprintf('%s/group/list', $this->URI_PLACEHOLDER));
			$data['url'] 			  = url(sprintf('%s/group/add', $this->URI_PLACEHOLDER));

			$data['group_detail'] = \Models\Group::getGroupDetails(___decrypt($request->id));
			$data['group_members'] = \Models\GroupMember::getGroupMembersById(___decrypt($request->id));
			$data['id_group'] = ___decrypt($request->id);

			$data['page_title'] = 'Group list';
			$data['add_url'] 	= url(sprintf('%s/group/add',ADMIN_FOLDER));

			$group_id = ___decrypt($request->id);
			
			if ($request->ajax()) {
				$groupList = \Models\GroupMember::getGroupMembersByIdListing(___decrypt($request->id));
				return \Datatables::of($groupList)                  
				->editColumn('action',function($groupList) use($request){
					$html = '';
					$html .= '<a href="javascript:void(0);" data-url="'.url(sprintf('%s/group/user/delete?id=%s&group_id=%s',ADMIN_FOLDER,___encrypt($groupList->user_id),___encrypt($request->id))).'" data-request="status" data-ask="Do you really want to continue with this action?" class="badge bg-red" >Delete</a>';
					return $html;
				})
				->make(true);
			}
			$data['html'] = $htmlBuilder
			->addColumn(['data' => 'row_number', 'name' => 'row_number', 'title' => '#','width' => '1'])
			->addColumn(['data' => 'text', 'name' => 'text', 'title' => 'Group Name'])
			->addColumn(['data' => 'action', 'name' => 'action', 'title' => 'Actions','searchable' => false, 'orderable' => false, 'width' => '10']);

			return view('backend.group.edit')->with($data);
		}

		public function updateGroup(Request $request,$hashid){

			$validator = \Validator::make($request->all(), [
				'name'           => ['required'],
				// 'talent_id'		 => ['required','array']
			],[
				'name.required'  	  => 'Please enter group name.',
				'talent_id.required'  => 'Select members',
				'talent_id.array'  	  => 'Select members',
			]);
			$validator->after(function ($validator) use($request) {
				$groupName = \Models\Group::checkForGroupName($request->name);
				if(($request->name != $request->hidden_name)){
					if($groupName){
			       		$validator->errors()->add('name', 'Entered Group name already exists.');
					}
				}
			});

			if($validator->passes()){

				$updateArr = [
					'name'				=> $request->name, 
					'updated'			=> date('Y-m-d H:i:s')
				];
				$groupList = \Models\Group::updateGroup($updateArr,___decrypt($hashid));

				/*Delete old members and add new members*/
				#$deleted_membersId = \Models\GroupMember::deleteMembersByGroupId(___decrypt($hashid));
				if(!empty($request->talent_id)){
					foreach ($request->talent_id as $key => $value) {
						$groupArr[$key] = [
							'group_id'			=> ___decrypt($hashid),
							'user_id'			=> $value,
							'created'			=> date('Y-m-d H:i:s'), 
							'updated'			=> date('Y-m-d H:i:s')
						];
					}
					$membersId = \DB::table('group_member')->insert($groupArr);
				}

				if(!empty($request->file)){
					$path = Input::file('file')->getRealPath();
		            $data = Excel::load($path, function($reader) {
		            })->get();

		            foreach ($data as $key1 => $value1) {

		            	if(!empty($value1['email'])){
			            	$data_email = \Models\Users::select('id_user','email')
			            						->where('email',$value1['email'])
			            						->first();
    						if(!empty($data_email)){
				            	if(!empty($request->talent_id) && !in_array($data_email['id_user'], $request->talent_id)){

				            		$groupedArr[$key1] = [
										'group_id'			=> ___decrypt($hashid),
										'user_id'			=> $data_email['id_user'],
										'created'			=> date('Y-m-d H:i:s'), 
										'updated'			=> date('Y-m-d H:i:s')
									];

				            	}
				            	else{
				            		$groupedArr[$key1] = [
										'group_id'			=> ___decrypt($hashid),
										'user_id'			=> $data_email['id_user'],
										'created'			=> date('Y-m-d H:i:s'), 
										'updated'			=> date('Y-m-d H:i:s')
									];
				            	}
				            }
		            	}
		            }

		            $membersIdExcel = \DB::table('group_member')->insert($groupedArr);
		        }

				$this->status = true;
				$this->message = 'Group has been updated successfully.';
				$this->redirect = url(sprintf("%s/group/list",ADMIN_FOLDER));
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
         * [This method is used for forum article listing] 
         * @param  Request
         * @return \Illuminate\Http\Response
         */

        public function articleList(Request $request, Builder $htmlBuilder){

			if ($request->ajax()) {
				$limit 		= $request->length;
				$offset 	= $request->start;
				$keyword 	= empty($request->search['value']) ? null : $request->search['value'];

	        	$articleList = \Models\Article::select('*')
					            ->with(['getArticleUser','getArticleAnwser'])
					            ->orderBy('created','DESC')
					            /*->when($keyword,function($q) use($keyword){
					            	$q->whereHas('getArticleUser',function($q) use($keyword){
					            		$q->where('name','like','%'.$keyword.'%');
					            	});
					            })*/
					            ->get();

				return \Datatables::of($articleList)
				->editColumn('action',function($articleList) {
					$actionHtml = '<a href="'.url('administrator/forum/article/view/' . ___encrypt($articleList->article_id)).'" class="badge">Detail</a> ';
					/*$actionHtml .= '<a href="'.url('administrator/forum/question/reply/' . ___encrypt($articleList->article_id)).'" class="badge">Reply</a> ';*/
					
					/*if($articleList->status == 'Open'){
						$actionHtml .= '<a href="javascript:;" onclick="updateStatus('.$articleList->article_id.')" class="badge bg-green">Close</a> ';
					}else{
						$actionHtml .= '<a href="javascript:;" onclick="updateStatus('.$articleList->article_id.')" class="badge bg-green">Open</a> ';
					}*/

					$actionHtml .= '<a href="javascript:;" data-url="'.url('administrator/forum/article/delete/'.$articleList->article_id).'" data-request="status" data-ask="Do you really want to delete this article?" class="badge case-resolve bg-red">Delete</a> ';

					return $actionHtml;
				})
				->editColumn('user_name',function($articleList) {
					$actionHtml = ucwords(@$articleList->getArticleUser[0]->name);
					return $actionHtml;
				})
				->editColumn('comments',function($articleList) {
					return count($articleList->getArticleAnwser);
				})
				->orderColumn('created','desc')
				->make(true);
			}

			$data['html'] = $htmlBuilder
			->addColumn(['data' => 'article_id', 'name' => 'article_id', 'title' => '#'])
			->addColumn(['data' => 'title', 'name' => 'title', 'title' => 'Title'])
			->addColumn(['data' => 'comments', 'name' => 'comments', 'title' => 'Total Comment'])
			// ->addColumn(['data' => 'description', 'name' => 'description', 'title' => 'Description','width'=>'100'])
			->addColumn(['data' => 'user_name', 'name' => 'user_name', 'title' => 'Posted By'])
			->addColumn(['data' => 'created', 'name' => 'created', 'title' => 'Date'])
			->addColumn(['data' => 'action', 'name' => 'action', 'title' => 'Action','searchable' => false, 'orderable' => false]);

			return view('backend.article.article-list')->with($data);
		}

		public function articleDetail(Request $request){
			$data['article_id'] 	= $request->article_id;
			$article_id 			= ___decrypt($request->article_id);
			$data['article']		= \Models\Article::getArticleDetail($article_id);
			$data['backurl']		= url(sprintf('%s/forum/article', $this->URI_PLACEHOLDER));
			$data['comment']			= \Models\Article::getAnswerByQuesId($article_id);
			
			return view('backend.article.article-view')->with($data);
		}

		public function deleteArticle(Request $request){
			$article_id 			= ($request->article_id);
			$type 					= ($request->type);

			if($type == 'comment'){
				$response 		= \Models\ArticleAnswer::where('id_article_answer',$article_id)->orWhere('id_parent',$article_id)->delete();
				if($response){
					$this->status 	= true;
					$this->redirect = true;
					$this->message 	= 'Comment Deleted successfully.';
				}

			}else if($type == 'article'){
				$response 		= \Models\Article::where('article_id',$article_id)->delete();
				if($response){
					$this->status 	= true;
					$this->redirect = 'datatable';
					$this->message 	= 'Comment Deleted successfully.';
				}
			}
			return response()->json([
				'data'      => $this->jsondata,
				'status'    => $this->status,
				'message'   => $this->message,
				'redirect'  => $this->redirect,
			]);
		}


		public function eventList(Request $request, Builder $htmlBuilder){

			if ($request->ajax()) {
				$limit 		= $request->length;
				$offset 	= $request->start;
				$keyword 	= empty($request->search['value']) ? null : $request->search['value'];

	        	$eventList = \Models\Events::getEventList('','');

				return \Datatables::of($eventList)
				->editColumn('action',function($eventList) {
					$actionHtml = '<a href="'.url('administrator/forum/event/view/' . ___encrypt($eventList->id_events)).'" class="badge">Detail</a> ';

					$actionHtml .= '<a href="javascript:;" data-url="'.url('administrator/forum/event/delete/'.$eventList->id_events).'" data-request="status" data-ask="Do you really want to delete this article?" class="badge case-resolve bg-red">Delete</a> ';

					return $actionHtml;
				})
				->editColumn('user_name',function($eventList) {
					$actionHtml = ucwords(@$eventList->getArticleUser[0]->name);
					return $actionHtml;
				})
				->orderColumn('id_events','desc')
				->make(true);
			}

			$data['html'] = $htmlBuilder
			->addColumn(['data' => 'row_number', 'name' => 'row_number', 'title' => '#'])
			->addColumn(['data' => 'event_title', 'name' => 'event_title', 'title' => 'Title'])
			->addColumn(['data' => 'event_date', 'name' => 'event_date', 'title' => 'Event Date'])
			->addColumn(['data' => 'event_time', 'name' => 'event_time', 'title' => 'Event Time'])
			// ->addColumn(['data' => 'description', 'name' => 'description', 'title' => 'Description','width'=>'100'])
			// ->addColumn(['data' => 'user_name', 'name' => 'user_name', 'title' => 'Posted By'])
			->addColumn(['data' => 'created', 'name' => 'created', 'title' => 'Date'])
			->addColumn(['data' => 'action', 'name' => 'action', 'title' => 'Action','searchable' => false, 'orderable' => false]);

			return view('backend.events.article-list')->with($data);
		}

		public function eventDetail(Request $request){
			$data['event_id'] 		= $request->event_id;
			$event_id 				= ___decrypt($request->event_id);
			$data['event']			= \Models\Events::getEventById($event_id);
			$data['backurl']		= url(sprintf('%s/forum/event', $this->URI_PLACEHOLDER));
			return view('backend.events.article-view')->with($data);
		}

		public function deleteEvent(Request $request){
			$event_id 			= ($request->event_id);	

			$response 		= \Models\Events::where('id_events',$event_id)->delete();
			if($response){
				$this->status 	= true;
				$this->redirect = 'datatable';
				$this->message 	= 'Comment Deleted successfully.';
			}

			return response()->json([
				'data'      => $this->jsondata,
				'status'    => $this->status,
				'message'   => $this->message,
				'redirect'  => $this->redirect,
			]);
		}

	}