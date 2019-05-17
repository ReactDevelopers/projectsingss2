<?php

    namespace App\Http\Controllers\Front;

    use App\Http\Requests;
    use Illuminate\Http\Request;
    use Illuminate\Validation\Rule;
    use Illuminate\Support\Facades\DB;
    use App\Http\Controllers\Controller;
    
    use Auth;
    use File;
    class AjaxController extends Controller{
        
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
        private $prefix;
        private $language;

        public function __construct(Request $request){
            $this->jsondata     = (object)[];
            $this->message      = "M0000";
            $this->status       = false;
            $this->prefix       = \DB::getTablePrefix();
            $this->post         = $request->all();
            $this->language     = \App::getLocale();
        }

        private function populateresponse($data){
            $data['message'] = (!empty($data['message']))?"":$this->message;
            
            if(empty($data['status'])){
                $data['status'] = $this->status;
            }
            
            $data = json_decode(json_encode($data),true);

            array_walk_recursive($data, function(&$item){
                
                if (gettype($item) == 'integer' || gettype($item) == 'float' || gettype($item) == 'NULL'){
                    $item = trim($item);
                }
            });

            if(empty($data['data'])){
                $data['data'] = (object) $data['data'];
            }

            return $data;
        }

        /**
         * [This method is used for Resending Activation link ]
         * @param  null
         * @return \Illuminate\Http\Response
         */
        
        public function resend_activation_link(){
            if(!empty($this->post['email'])){
                $this->post['email'] = base64_decode($this->post['email']);
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
                        
                        $this->message = sprintf(ALERT_SUCCESS,str_replace(['<%s>','\n'], ['',''], trans(sprintf('general.%s','M0021'))));
                    }else if($result['type'] == EMPLOYER_ROLE_TYPE){
                        $emailData              = ___email_settings();
                        $emailData['email']     = $result['email'];
                        $emailData['name']      = $result['first_name'];
                        $emailData['link']      = url(sprintf("activate/account?token=%s",$code));

                        \Models\Employers::change($result['id_user'],['remember_token' => $code,'updated' => date('Y-m-d H:i:s')]);
                        ___mail_sender($result['email'],sprintf("%s %s",$result['first_name'],$result['last_name']),"employer_signup",$emailData);

                        $this->message = sprintf(ALERT_SUCCESS,str_replace(['<%s>','\n'], ['',''], trans(sprintf('general.%s','M0021'))));
                    }else{
                        $this->message = sprintf(ALERT_DANGER,trans(sprintf('general.%s','M0028')));
                    }
                }else{
                    $this->message = sprintf(ALERT_DANGER,trans(sprintf('general.%s','M0028')));
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
         * [This method is used for deletion of user's education ]
         * @param  Request
         * @return Json Response
         */

        public function delete_talent_education(Request $request){
            $isDeleted = \Models\Talents::delete_education(sprintf(" id_education = %s AND user_id = %s ",$request->id_education, $request->user()->id_user));
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
         * [This method is used to edit user's education]
         * @param  Request
         * @return Json Response
         */

        public function edit_talent_education(Request $request){
            $this->jsondata = \Models\Talents::get_education(sprintf(" id_education = %s AND user_id = %s ",$request->id_education, $request->user()->id_user),'single');
            return response()->json([
                'status'    => $this->status,
                'data'      => $this->jsondata,
                'message'   => $this->message
            ]);
        }

        /**
         * [This method is used for user's experience deletion ]
         * @param  Request
         * @return Json Response
         */
        
        public function delete_talent_experience(Request $request){
            $isDeleted = \Models\Talents::delete_experience(sprintf(" id_experience = %s AND user_id = %s ",$request->id_experience, $request->user()->id_user));
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
         * [This method is used to edit user's experience ]
         * @param  Request
         * @return Json Response
         */
        
        public function edit_talent_experience(Request $request){
            $this->jsondata = \Models\Talents::get_experience(sprintf(" id_experience = %s AND user_id = %s ",$this->post['id_experience'], $request->user()->id_user),'single');
            return response()->json([
                'status'    => $this->status,
                'data'      => $this->jsondata,
                'message'   => $this->message
            ]);
        }

        /**
         * [This method is used for document deletion ]
         * @param  Request
         * @return Json Response
         */
        
        public function delete_document(Request $request){
            $isDeleted = \Models\Talents::delete_file(sprintf(" id_file = %s AND user_id = %s ",$this->post['id_file'], $request->user()->id_user));

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
         * [This method is used to crop an image ]
         * @param  Request
         * @return Json Response
         */
        
        public function crop(Request $request){
            $file               = $request->file($request->imagename);
            $folder             = ltrim($request->SGCreator_folder, '/');
            $destination        = public_path($folder);
            $extension          = $file->getClientOriginalExtension();
            $file_size          = get_file_size($file->getClientSize(),'KB');
            $filename           = file_name($file->getClientOriginalName(),'jpeg');

            $crop = new \App\Lib\Crop();
            $crop->initialize(
                array(
                    'src'           => null,
                    'data'          => $request->SGCreator_data,
                    'dst'           => $destination,
                    'file'          => $_FILES[$request->imagename],
                    'targetFile'    => $filename
                )
            );

            $isUploaded = $crop->getMsg();

            if($isUploaded == 'image_uploaded'){
                $data = [
                    'user_id' => empty($request->user_id) ? \Auth::user()->id_user : $request->user_id,
                    'reference' => 'users',
                    'filename' => $filename,
                    'extension' => $extension,
                    'folder' => $folder,
                    'type' => 'profile',
                    'size' => $file_size,
                    'is_default' => DEFAULT_NO_VALUE,
                    'created' => date('Y-m-d H:i:s'),
                    'updated' => date('Y-m-d H:i:s'),
                ];


                if($request->type=='article'){
                    $data['reference'] = 'article';
                    $data['type'] = 'article';
                }
                $isInserted = \Models\Talents::create_file($data,false,true);
                
                if(!empty($isInserted)){
                    if($request->type=='article'){
                        $file_id = $isInserted['id_file'];
                    }
                    else{
                        $file_id = '';
                    }
                    return response()->json([
                        'state'  => true,
                        'message' => trans('website.'.$isUploaded),
                        'result' => asset(sprintf("%s%s",$folder,$filename)),
                        'filename' => $filename,
                        'file_id' => $file_id
                    ]);
                }else{
                    return response()->json([
                        'state'  => false,
                        'message' => trans('website.'.$isUploaded),
                    ]);    
                }
            }else{
                return response()->json([
                    'state'  => false,
                    'message' => trans('website.'.$isUploaded),
                ]);
            }
        } 

        /**
         * [This method is used to validate calender ]
         * @param  Request
         * @return String (Printing a String)
         */
        
        public function validate_calendar(Request $request){
            if(!empty($request->year) && !empty($request->month) && !empty($request->day)){
                $request->request->add(['availability_date' => sprintf('%s-%s-%s',$request->year,$request->month,$request->day)]);
            }

            $validate = \Validator::make($request->all(), [
                "availability_date"             => array_merge(['required'],validation('birthday')),
            ],[
                'availability_date.required'    => trans('general.M0155'),
                'availability_date.string'      => trans('general.M0156'),
                'availability_date.regex'       => trans('general.M0156'),
            ]);

            if(!$validate->fails()){
                if(strtotime(date('Y-m-d')) > strtotime($request->availability_date)){
                    return [
                        'data' => [
                            'selected_date' => ___d($request->availability_date),
                            'dates_html' => "",
                        ],
                        'status' => false,
                        'message' => trans('general.M0174')
                    ];
                }else{
                    $dates_html = "";
                    
                    foreach(range(1,date('t',strtotime($request->availability_date))) as $item){ 
                        $selected = (sprintf('%\'.02d',$item) == date('d',strtotime($request->availability_date)))?'active':'';
                        $checked = (sprintf('%\'.02d',$item) == date('d',strtotime($request->availability_date)))?'checked="checked"':'';

                        $dates_html .= '<label class="btn '.$selected.'"><input type="radio" name="day" data-request="calendar" data-url="'.url('ajax/validate-calendar').'" value="'.sprintf('%\'.02d',$item).'" id="day-'.sprintf('%\'.02d',$item).'" '.$checked.' autocomplete="off"><span class="input-value">'.$item.'</span></label>';
                    }

                    return [
                        'data' => [
                            'selected_date' => ___d($request->availability_date),
                            'dates_html' => $dates_html,
                        ],
                        'status' => true,
                        'message' => trans('general.M0000')
                    ];
                }
            }    
        }

        /**
         * [This method is used for country state listing ]
         * @param  Request
         * @return String (printing string)
         */
        
        public function country_state_list(Request $request){
            if(empty($request->record_id)){
                echo sprintf("<option value=''>%s</option>",sprintf(trans('website.W0056'),trans('website.W0067')));
            }

            $state_list = \Models\Listings::country_state_list($request->record_id);
            if(!empty($state_list)){
                $states = (array)\App\Lib\Dash::combine(
                    $state_list,
                    '{n}.id_state',
                    '{n}.state_name'
                );

                echo ___dropdown_options($states,sprintf(trans('website.W0056'),trans('website.W0067')));
            }else{
                echo sprintf("<option value=''>%s</option>",sprintf(trans('website.W0056'),trans('website.W0067')));
            }
        }

        /**
         * [This method is used for state city listing ]
         * @param  Request
         * @return string (Printing a string)
         */
         
        public function state_city_list(Request $request){
            if(empty($request->record_id)){
                ___dropdown_options(@\Cache::get('cities'),sprintf(trans('website.W0294'),trans('website.W0067')));
            }

            $city_list = \Models\Listings::state_city_list($request->record_id);
            if(!empty($city_list)){
                $cities = (array)\App\Lib\Dash::combine(
                    $city_list,
                    '{n}.id_city',
                    '{n}.city_name'
                );
                
                echo ___dropdown_options($cities,sprintf(trans('website.W0294'),trans('website.W0067')));
            }else{
                echo sprintf("<option value=''>%s</option>",sprintf(trans('website.W0294'),trans('website.W0067')));
            }
        }

        /**
         * [This method is used for subindustry listing of industry]
         * @param  Request
         * @return String (Printing a string)
         */
         

        public function industry_subindustry_list(Request $request){
            if(!empty($request->record_id)){
                $industries = \Models\Listings::industry_subindustry_list($request->record_id);

                if(!empty($industries)){
                    $industries = (array)\App\Lib\Dash::combine(
                        $industries,
                        '{n}.id_industry',
                        '{n}.name'
                    );

                    echo ___dropdown_options($industries,sprintf(trans('website.W0060'),trans('website.W0068')));
                }else{
                    echo sprintf("<option value=''>%s</option>",sprintf(trans('website.W0060'),trans('website.W0068')));
                }
            }else{
                echo sprintf("<option value=''>%s</option>",sprintf(trans('website.W0060'),trans('website.W0068')));
            }
        }

        /**
         * [This method is used for subindustry skill listing]
         * @param  Request
         * @return string (Printing a string)
         */
         
        public function subindustry_skill_list(Request $request){

            if(!empty($request->record_id)){
                $where = "industry_id = " . $request->record_id . " AND skill_status='active'";
                $skill = \Models\Listings::skills('array', ['id_skill','skill_name'], $where);

                if(!empty($skill)){
                    $skill = (array)\App\Lib\Dash::combine(
                        $skill,
                        '{n}.id_skill',
                        '{n}.skill_name'
                    );
                    echo ___dropdown_options($skill,sprintf(trans('website.W0059'),trans('website.W0068')));
                }else{
                    echo sprintf("<option value=''>%s</option>",sprintf(trans('website.W0060'),trans('website.W0068')));
                }
            }else{
                echo sprintf("<option value=''>%s</option>",sprintf(trans('website.W0060'),trans('website.W0068')));
            }
        }

        /**
         * [This method is used for subindustry skills listing]
         * @param  Request
         * @return string (Printing a string)
         */

        public function subindustry_skills_list(Request $request){

            if(!empty($request->record_id)){
                $where = "industry_id = " . $request->record_id . " AND skill_status='active'";
                $skill = \Models\Listings::skills('array', ['id_skill','skill_name'], $where);

                if(!empty($skill)){
                    $skill = (array)\App\Lib\Dash::combine(
                        $skill,
                        '{n}.skill_name',
                        '{n}.skill_name'
                    );
                    echo ___dropdown_options($skill,sprintf(trans('website.W0059'),trans('website.W0068')));
                }else{
                    echo sprintf("<option value=''>%s</option>",sprintf(trans('website.W0060'),trans('website.W0068')));
                }
            }else{
                echo sprintf("<option value=''>%s</option>",sprintf(trans('website.W0060'),trans('website.W0068')));
            }
        } 

        /**
         * [This method is used for city listing]
         * @param  Request
         * @return string (Printing a string)
         */ 
        
        public function city_list(Request $request){
            $html           = "";
            $page           = ($request->page)?$request->page:1;
            $selected_city  = explode(',',$request->selected);

            if(!empty($request->search)){
                $where = "IF(({$this->language} != ''),`{$this->language}`, `en`)  like '%{$request->search}%' ";
            }else{
                $where = " 1 ";
            }

            $cities = \Models\Listings::cities('array',['id_city',\DB::Raw("IF(({$this->language} != ''),`{$this->language}`, `en`) as city_name")],"","",$where,$page);

            if(!empty($cities)){
                array_walk($cities,function($item) use(&$html,$selected_city){
                    $checked = '';
                    if(in_array($item['id_city'],$selected_city)){
                        $checked='checked="true"';
                    }

                    $html .= '<li>
                        <div class="checkbox">                
                            <input data-request="save-city-filter" data-action="filter" type="checkbox" id="city-'.$item['id_city'].'" name="city_filter[]" '.$checked.' value="'.$item['id_city'].'">
                            <label for="city-'.$item['id_city'].'"><span class="check"></span> '.$item['city_name'].'</label>
                        </div>
                    </li>';
                });
            }else{
                $html = '<li>
                    <div class="checkbox">                
                        '.trans('general.M0194').'
                    </div>
                </li>';
            }

            echo $html;
        } 

        /**
         * [This method is used for state listing]
         * @param  Request
         * @return string (Printing a string)
         */ 
        
        public function state_list(Request $request){
            $html           = "";
            if(!empty($request->search)){
                $where = " IF(({$this->language} != ''),`{$this->language}`, `en`) like '%{$request->search}%' ";
            }else{
                $where = " 1 ";
            }

            $cities = \Models\Listings::state_list($where,['id_state',\DB::Raw("IF(({$this->language} != ''),`{$this->language}`, `en`) as state_name")]);

            if(!empty($cities)){
                array_walk($cities,function($item) use(&$html){
                    $html .= '<li>
                        <div class="checkbox">                
                            <input data-action="filter" type="checkbox" id="state-'.$item['id_state'].'" name="state_filter[]" value="'.$item['id_state'].'">
                            <label for="state-'.$item['id_state'].'"><span class="check"></span> '.$item['state_name'].'</label>
                        </div>
                    </li>';
                });
            }else{
                $html = '<li>
                    <div class="checkbox">                
                        '.trans('general.M0194').'
                    </div>
                </li>';
            }

            echo $html;
        }

        /**
         * [This method is used for portfolio deletion]
         * @param  Request
         * @return Json Response
         */ 
        public function delete_portfolio(Request $request){
            $isDeleted = \Models\Portfolio::delete_portfolio($this->post['id_portfolio'], $request->user()->id_user);
            if($isDeleted){
                $this->status = true;
                $this->message  = sprintf(ALERT_SUCCESS,trans("general.M0313"));
            }
            return response()->json([
                'status'    => $this->status,
                'reload'    => true,
                'message'   => $this->message
            ]);            
        }

        /**
         * [This method is used for user's state list]
         * @param  Request
         * @return string(Printing a string)
         */ 

        public function user_state_list(Request $request){
            $html = "";

            $keys = [
                'users.id_user',
            ];

            $talents =  \Models\Employers::get_premium_talents($keys);
            $id_talent = [];
            foreach ($talents as $value) {
                $id_talent[] = $value->id_user;
            }

            if(!empty($request->search)){
                $where = " IF(({$this->prefix}{$this->language} != ''),`{$this->prefix}{$this->language}`, {$this->prefix}`en`) like '%{$request->search}%' ";
            }else{
                $where = " 1 ";
            }
            $location =  \Models\Employers::getUserLocation($id_talent, $where);
            $location_html = '';
            if(!empty($location)){
                foreach ($location as $item) {
                    $location_html .= '<li>
                        <div class="checkbox ">
                            <input data-action="filter" type="checkbox" id="state-'.$item->id_city.'" name="state_filter[]" value="'.$item->id_city.'">
                            <label for="state-'.$item->id_city.'"><span class="check"></span> '.$item->city_name.' ('.$item->num.')</label>
                        </div>
                    </li>';
                }
            }else{
                $location_html = '<li>
                    <div class="checkbox ">
                        '.trans('general.M0194').'
                    </div>
                </li>';
            }

            echo $location_html;
        }

        /**
         * [This method is used for getting user's education]
         * @param  Request
         * @return Json Response
         */ 

        public function get_talent_education(Request $request){

            $this->jsondata = \Models\Talents::get_education(sprintf(" id_education = %s AND user_id = %s ",$request->id_education, $request->id_user),'single');
            return response()->json([
                'data'      => $this->jsondata,
                'message'   => $this->message
            ]);
        }

        /**
         * [This method is used for getting user's experience]
         * @param  Request
         * @return Json Response
         */ 

        public function get_talent_experience(Request $request){

            $this->jsondata = \Models\Talents::get_experience(sprintf(" id_experience = %s AND user_id = %s ",$request->id_experience, $request->id_user),'single');
            return response()->json([
                'data'      => $this->jsondata,
                'message'   => $this->message
            ]);
        }

        /**
         * [This method is used for deletion of user's document]
         * @param  Request
         * @return Json Response
         */ 

        public function delete_user_document(Request $request){
            $isDeleted = \Models\Talents::delete_file(sprintf(" id_file = %s AND user_id = %s ",$request->id_file, $request->id_user));

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
         * [This method is used for country phone codes ajax listing]
         * @param  Request
         * @return Json Response
         */ 

        public function country_phone_codes(Request $request){
            $language = \App::getLocale();

            $page = (!empty($request->page))?$request->page:1;
            $where = 'status = "active"';

            if(!empty($request->search)){
                $where .= " AND (
                    phone_country_code LIKE '%{$request->search}%' 
                    OR 
                    en LIKE '%{$request->search}%'
                )";
            }
            
            $country_phone_codes = \Models\Listings::countries(
                'array',
                [
                    'phone_country_code as id',
                    \DB::Raw("CONCAT(IF(({$language} != ''),`{$language}`, `en`),' (',phone_country_code,')') as text"),
                ],
                $where
            );

            return response()->json([
                'results'    => $country_phone_codes,
                'pagination' => [
                    "more" => true
                ]
            ]);
        }

        /**
         * [This method is used for countries ajax listing]
         * @param  Request
         * @return Json Response
         */ 

        public function countries(Request $request){
            $language = \App::getLocale();
            $where = 'status = "active"';

            if(!empty($request->search)){
                $where .= " AND {$language} LIKE '%{$request->search}%'";
            }

            $countries = \Models\Listings::countries(
                'array',
                [
                    \DB::Raw("IF(({$language} != ''),`{$language}`, `en`) as text"),
                    'id_country as id'
                ],
                $where
            );

            return response()->json([
                'results'    => $countries,
                'pagination' => [
                    "more" => true
                ]
            ]);
        }

        /**
         * [This method is used for randering all the talents] 
         * @param  null
         * @return \Illuminate\Http\Response
         */

        public function talents(Request $request){

            $language = \App::getLocale();
            $where = 'status = "active" and type="talent"';
            if(!empty($request->search)){
                $where .= " AND name LIKE '%{$request->search}%'";
            }

            $talents = \Models\Listings::talents(
                'array',
                [
                    'name as text',
                    'id_user as id'
                ],
                $where
            );

            return response()->json([
                'results'    => $talents,
                'pagination' => [
                    "more" => true
                ]
            ]);
        }

        /**
         * [This method is used for randering all the talents] 
         * @param  null
         * @return \Illuminate\Http\Response
         */

        public function talents_members(Request $request){
            
            $language = \App::getLocale();
            $prefix = DB::getTablePrefix();

            $where = 'status = "active" ';

            if($request->is_edit == 'Y'){
                $groupUser = \Models\GroupMember::getGroupMemberIds($request->id_group);

                if(!empty($groupUser['group_member'])){
                    $where .= ' AND id_user NOT IN ('.$groupUser['group_member'].')';
                }
            }

            if(!empty($request->search)){
                $where .= " AND email LIKE '%{$request->search}%'";
            }

            $talents = \Models\Listings::talents(
                'array',
                [
                    'name as text1',
                    DB::raw("TRIM(CONCAT({$prefix}users.email,' (',{$prefix}users.name,')' )) as text"),
                    'id_user as id'
                ],
                $where
            );

            return response()->json([
                'results'    => $talents,
                'pagination' => [
                    "more" => true
                ]
            ]);
        }

        /**
         * [This method is used for randering all the employers] 
         * @param  null
         * @return \Illuminate\Http\Response
         */

        public function employers(Request $request){

            $language = \App::getLocale();
            $where = 'status = "active" and type="employer"';
            if(!empty($request->search)){
                $where .= " AND name LIKE '%{$request->search}%'";
            }

            $employers = \Models\Listings::employers(
                'array',
                [
                    'name as text',
                    'id_user as id'
                ],
                $where
            );

            return response()->json([
                'results'    => $employers,
                'pagination' => [
                    "more" => true
                ]
            ]);
        }


        /**
         * [This method is used for states ajax listing]
         * @param  Request
         * @return Json Response
         */ 

        public function states(Request $request){
            $language = \App::getLocale();
            $where = 'status = "active"';

            if(!empty($request->search)){
                $where .= " AND {$language} LIKE '%{$request->search}%'";
            }

            if(!empty($request->country)){
                $where .= " AND country_id = $request->country";
            }

            $states = \Models\Listings::states(
                'array',
                [
                    \DB::Raw("IF(({$language} != ''),`{$language}`, `en`) as text"),
                    'id_state as id'
                ],
                $where
            );

            if(empty($request->country)){
                $states = '';
            }

            return response()->json([
                'results'    => $states,
                'pagination' => [
                    "more" => true
                ]
            ]);
        }


        /**
         * [This method is used for cities ajax listing]
         * @param  Request
         * @return Json Response
         */ 

        public function cities(Request $request){
            // dd($request->all());
            $language = \App::getLocale();
            $where = 'status = "active"';

            if(!empty($request->search)){
                $where .= " AND {$language} LIKE '%{$request->search}%'";
            }

            $cities = \Models\Listings::cities(
                'array',
                [
                    \DB::Raw("IF(({$language} != ''),`{$language}`, `en`) as text"),
                    'id_city as id'
                ],
                $request->state,
                '',
                $where
            );

            if(empty($request->state)){
                $cities = '';
            }

            return response()->json([
                'results'    => $cities,
                'pagination' => [
                    "more" => true
                ]
            ]);
        }

        public function delete_availability(Request $request){            
            if(empty($request->id_availability)){
                $this->message = 'M0121';
                $this->error = sprintf(trans(sprintf('general.%s',$this->message)),'id_availability');
            }else{    
                $isInserted = \Models\Talents::delete_availability($request->user()->id_user,$request->id_availability);
                
                /* RECORDING ACTIVITY LOG */
                event(new \App\Events\Activity([
                    'user_id'           => $request->user()->id_user,
                    'user_type'         => 'talent',
                    'action'            => 'talent-delete-availability',
                    'reference_type'    => 'users',
                    'reference_id'      => $request->id_availability
                ]));
                
                $this->status = true;    
            }

            if($isInserted){
                $this->status = true;
                $this->message  = sprintf(ALERT_SUCCESS,trans("general.M0110"));
            }

            return response()->json([
                'status'    => $this->status,
                'message'   => $this->message
            ]);
        }

        /**
         * [This method is used for notification listing]
         * @param  Request
         * @return Json Response
         */
        
        public function notification_count(Request $request){
            if(!file_exists(public_path("uploads/notification/{$request->user_id}.txt"))){
                \File::put(public_path("uploads/notification/{$request->user_id}.txt"),"");
            }

            return [
                'data' => \File::get(public_path("uploads/notification/{$request->user_id}.txt"))
            ];
        }

        /**
         * [This method is used for notification listing]
         * @param  Request
         * @return Json Response
         */
        
        public function notification_list(Request $request){
            $html               = "";

            if(auth()->guard('web')->check()){
                $notifications      = \Models\Notifications::lists(auth()->guard('web')->user()->id_user,1,4);
                $user_type = auth()->user()->type;
                $html_footer  = '<li>';
                    $html_footer .= '<a href="'.url($user_type.'/profile/notifications').'" style="text-align:center;font-size:10px;">';
                    $html_footer .= trans('general.M0290');
                    $html_footer .= '</a>';
                $html_footer .= '</li>';
                
                
                if(!empty($notifications['result'])){
                    foreach($notifications['result'] as $keys => $item){
                        $html .= '<li>';
                                if($item['notification_key'] === 'JOB_UPDATED_BY_EMPLOYER'){
                                    $payload = json_decode(json_encode($item['notification_response_json']),true);
                                    $html .= '<a href="javascript:void(0);" data-request="mark-read" data-url="'.url($user_type.'/notifications/mark/read?notification_id='.$item['id_notification']).'" class="submenu-block clearfix '.$item['notification_status'].'" data-confirm="true" data-ask="'.sprintf($item['notification'],$payload['project_title']).'">';
                                }else{
                                    $html .= '<a href="javascript:void(0);" data-request="mark-read" data-url="'.url($user_type.'/notifications/mark/read?notification_id='.$item['id_notification']).'" class="submenu-block clearfix '.$item['notification_status'].'">';
                                }
                                
                                $html .= '<span class="submenublock-user"><img src="'.$item['sender_picture'].'" /></span>';
                                $html .= '<span class="submenublock-info">';
                                    $html .= '<h4>'.$item['sender_name'].' <span>'.$item['created'].'</span></h4>';
                                    
                                    if($item['notification_key'] === 'JOB_UPDATED_BY_EMPLOYER'){
                                        $html .= '<p>'.sprintf($item['notification'],$payload['project_title']).'</p>';
                                    }else{
                                        $html .= '<p>'.$item['notification'].'</p>';
                                    }
                                $html .= '</span>';
                            $html .= '</a>';
                        $html .= '</li>';
                    }
                }
            }

            if(!$html){
                $html  = '<li class="no-notification-found">';
                    $html .= '<span>';
                    $html .= trans('general.M0291');
                    $html .= '</span>';
                $html .= '</li>';
            }

            return response()->json([
                "data"              => $html.$html_footer,
            ]);
        }

        public function new_expchk(Request $request){

            //Check PayPal mode, and change PayPal url according for Sandbox or Live.
            $PayPal_BASE_URL = PayPal_BASE_URL_SANDBOX;
            if(env('PAYPAL_ENV') == 'sandbox'){
              $PayPal_BASE_URL = PayPal_BASE_URL_SANDBOX;
            }else{
              $PayPal_BASE_URL = PayPal_BASE_URL_LIVE;
            }

            // request http using curl
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $PayPal_BASE_URL . 'oauth2/token');
            curl_setopt($ch, CURLOPT_HEADER, FALSE);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
            curl_setopt($ch, CURLOPT_POST, TRUE);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
            curl_setopt($ch, CURLOPT_USERPWD, env('PAYPAL_CLIENT_ID') . ":" . env('PAYPAL_SECRET'));
            curl_setopt($ch, CURLOPT_POSTFIELDS, "grant_type=client_credentials");
            #curl_setopt($ch, CURLOPT_CAINFO, public_path('api.sandbox.paypal.com_DigicertG2_08202020.pem'));

            $result = curl_exec($ch);  

            if (curl_error($ch)) {
                $error_msg = curl_error($ch);
                dd('error_msg>>> ',$error_msg);
            }

            $json = json_decode($result);
            $accessToken = $json->access_token;

            $payment = \Session::get("payment");

            $currency = !empty($payment['price_unit']) ? $payment['price_unit'] : DEFAULT_CURRENCY;
            unset($payment['price_unit']);

            if (empty($result)) {
                return FALSE;
            } else {
                $json = json_decode($result);

                $price_to_pay = number_format($payment['transaction_total'],2);

                $accessToken = $json->access_token;

                $random_string = __random_string();

                $curl = curl_init();
                $data = '{
                            "intent":"sale",
                            "redirect_urls":{
                                "return_url":"'.url('payment/get-payment').'",
                                "cancel_url":"'.url('payment/cancel-payment').'"
                            },
                            "payer":{
                                "payment_method":"paypal"
                            },
                            "application_context": {
                                "shipping_preference": "NO_SHIPPING"
                            },
                            "transactions":[
                            {
                                "amount":{
                                    "total":"'.$price_to_pay.'",
                                    "currency":"'.$currency.'"
                            },
                            "invoice_number": "CB-INV-'.$random_string.'",
                            "description":"This is the payment transaction description."
                            }
                          ]
                        }';

                curl_setopt($curl, CURLOPT_URL, $PayPal_BASE_URL . 'payments/payment');
                curl_setopt($curl, CURLOPT_POST, true);
                curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);
                curl_setopt($curl, CURLOPT_POSTFIELDS, $data); 
                curl_setopt($curl, CURLOPT_HTTPHEADER, array(
                  "Content-Type: application/json",
                  "Authorization: Bearer ".$accessToken, 
                  "Content-length: ".strlen($data))
                );

                $response1 = curl_exec($curl);
                $result = json_decode($response1);

                \Models\PaypalPayment::paypal_response([
                    'user_id'                   => \Auth::user()->id_user,
                    'response_json'             =>  json_encode($result),
                    'request_type'              => 'Request To PayPal EC. Mode- '.env('PAYPAL_ENV'),
                    'status'                    => 'true',
                    'created'                   => date('Y-m-d H:i:s')
                ]);

                $payment['transaction'] = \Models\Payments::init_employer_payment($payment);

                \Session::set('payment_transaction',$payment['transaction']);

                echo $response1;
            }
        }


        public function execute_expchk(Request $request){

            //Check PayPal mode, and change PayPal url according for Sandbox or Live.
            $PayPal_BASE_URL = PayPal_BASE_URL_SANDBOX;
            if(env('PAYPAL_ENV') == 'sandbox'){
              $PayPal_BASE_URL = PayPal_BASE_URL_SANDBOX;
            }else{
              $PayPal_BASE_URL = PayPal_BASE_URL_LIVE;
            }

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $PayPal_BASE_URL . 'oauth2/token');
            curl_setopt($ch, CURLOPT_HEADER, FALSE);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
            curl_setopt($ch, CURLOPT_POST, TRUE);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
            curl_setopt($ch, CURLOPT_USERPWD, env('PAYPAL_CLIENT_ID') . ":" . env('PAYPAL_SECRET'));
            curl_setopt($ch, CURLOPT_POSTFIELDS, "grant_type=client_credentials");
            $result = curl_exec($ch);            

            $json = json_decode($result);

            $accessToken = $json->access_token;

            $paymentID = $request->paymentID;
            $payerID = $request->payerID;

            $payment = \Session::get("payment");

            $price_to_pay = number_format($payment['transaction_total'],2);
            $currency = !empty($payment['price_unit']) ? $payment['price_unit'] : DEFAULT_CURRENCY;
            unset($payment['price_unit']);

            $curl = curl_init();
            $data = '{
                      "payer_id":"'.$payerID.'",
                      "transactions":[
                        {
                          "amount":{
                            "total":"'.$price_to_pay.'",
                            "currency":"'.$currency.'"
                          }
                        }
                      ]
                    }';

            curl_setopt($curl, CURLOPT_URL, $PayPal_BASE_URL.'payments/payment/'.$paymentID.'/execute/');
            curl_setopt($curl, CURLOPT_POST, true);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);
            curl_setopt($curl, CURLOPT_POSTFIELDS, $data); 
            curl_setopt($curl, CURLOPT_HTTPHEADER, array(
              "Content-Type: application/json",
              "Authorization: Bearer ".$accessToken) 
            );

            $response2 = curl_exec($curl);

            //make arr
            $response_arr = (array)json_decode(json_encode(json_decode($response2), 128));
            //check if payment is completed
            if(!empty($response_arr['transactions'])){
                if($response_arr['transactions'][0]->related_resources[0]->sale->state == 'completed'){

                    //save response
                    \Models\PaypalPayment::paypal_response([
                        'user_id'                   => \Auth::user()->id_user,
                        'response_json'             =>  json_encode(json_decode($response2)),
                        'request_type'              => 'Response From PayPal EC. Mode- '.env('PAYPAL_ENV'),
                        'status'                    => 'true',
                        'created'                   => date('Y-m-d H:i:s')
                    ]);

                    
                    $payment_transaction = \Session::get("payment_transaction");
                    // update to confirm
                    $saleID = $response_arr['transactions'][0]->related_resources[0]->sale->id;

                    $isUpdated = \Models\Payments::update_transaction(
                        $payment_transaction->id_transactions,
                        [
                            'transaction_reference_id' => $saleID, 
                            'transaction_status' => 'confirmed', 
                            'updated' => date('Y-m-d H:i:s')
                        ]
                    );

                    if(!empty($isUpdated)){
                        $isProposalAccepted =  \Models\Employers::accept_proposal(\Auth::user()->id_user,$payment['transaction_project_id'],$payment['transaction_proposal_id']);
                    }

                    /* RECORDING ACTIVITY LOG */
                    event(new \App\Events\Activity([
                        'user_id'           => \Auth::user()->id_user,
                        'user_type'         => 'employer',
                        'action'            => 'employer-payment-complete-job',
                        'reference_type'    => 'projects',
                        'reference_id'      => $payment['transaction_project_id']
                    ]));

                    $this->status = true;
                    $this->message = trans('general.'.$isProposalAccepted['message']);

                    $redirect_url = url(sprintf('%s/project/proposals/talent?proposal_id=%s&project_id=%s',EMPLOYER_ROLE_TYPE,___encrypt($payment['transaction_proposal_id']),___encrypt($payment['transaction_project_id'])));

                    \Session::forget('payment');

                }elseif($response_arr['transactions'][0]->related_resources[0]->sale->state == 'pending'){
                    \Models\PaypalPayment::paypal_response([
                        'user_id'                   => \Auth::user()->id_user,
                        'response_json'             =>  json_encode(json_decode($response2)),
                        'request_type'              => 'Pending Payment. Response From PayPal EC. Mode- '.env('PAYPAL_ENV'),
                        'status'                    => 'false',
                        'created'                   => date('Y-m-d H:i:s')
                    ]);


                    $this->status = false;
                    $this->message = 'Payment processed in pending status.';
                    $redirect_url = '';

                }else{
                    \Models\PaypalPayment::paypal_response([
                        'user_id'                   => \Auth::user()->id_user,
                        'response_json'             =>  json_encode(json_decode($response2)),
                        'request_type'              => 'Response From PayPal EC. Mode- '.env('PAYPAL_ENV'),
                        'status'                    => 'false',
                        'created'                   => date('Y-m-d H:i:s')
                    ]);

                    $this->status = false;
                    $this->message = 'Something went wrong';
                    $redirect_url = '';
                }

            }else{
                \Models\PaypalPayment::paypal_response([
                    'user_id'                   => \Auth::user()->id_user,
                    'response_json'             =>  json_encode(json_decode($response2)),
                    'request_type'              => 'Response From PayPal EC. Mode- '.env('PAYPAL_ENV'),
                    'status'                    => 'false',
                    'created'                   => date('Y-m-d H:i:s')
                ]);


                $this->status = false;
                $this->message = 'Something went wrong';
                $redirect_url = '';
            }

            return response()->json([
                'status'       => $this->status,
                'message'      => $this->message,
                'redirect_url' => $redirect_url,
            ]);

            // echo $response2; 
        }

        //Open page in web view
        public function mobile_open_expchk(Request $request){

            $data['user'] = \Models\Users::findByToken($request->token);
            $data['project_id'] = $project_id  = $project_id      = ___decrypt($request->project_id);
            $data['proposal_id'] = $proposal_id = $proposal_id     = ___decrypt($request->proposal_id);
            $data['transaction_id']  = $transaction_id = ___decrypt($request->transaction_id);
            $data['user_id']  = $user_id = ___decrypt($request->user_id);

            $data['is_payment_already_captured'] = \Models\Payments::is_payment_already_escrowed($project_id);
            
            $get_payment = \DB::table('transactions')
                                ->select('*')
                                ->where('id_transactions','=',$transaction_id)
                                ->first();

            $data['transaction_total'] = $get_payment->transaction_total;
            $data['quoted_price'] = $get_payment->transaction_subtotal;

            $data['title'] = 'Pay via PayPal';
            $currency = \Models\Currency::select(['sign'])->where('iso_code',$request->currency)->first();
            $data['currency']  = $currency['sign'];
            // $data['currency']   = \Models\Currency::getCurrencyByISOCode($request->currency);
            // $data['currency'] = $request->currency;
            return view('front.pages.mobile_paypal')->with($data);
        }

        public function mobile_new_expchk(Request $request){

            //Check PayPal mode, and change PayPal url according for Sandbox or Live.
            $PayPal_BASE_URL = PayPal_BASE_URL_SANDBOX;
            if(env('PAYPAL_ENV') == 'sandbox'){
              $PayPal_BASE_URL = PayPal_BASE_URL_SANDBOX;
            }else{
              $PayPal_BASE_URL = PayPal_BASE_URL_LIVE;
            }

            // request http using curl
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $PayPal_BASE_URL . 'oauth2/token');
            curl_setopt($ch, CURLOPT_HEADER, FALSE);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
            curl_setopt($ch, CURLOPT_POST, TRUE);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
            curl_setopt($ch, CURLOPT_USERPWD, env('PAYPAL_CLIENT_ID') . ":" . env('PAYPAL_SECRET'));
            curl_setopt($ch, CURLOPT_POSTFIELDS, "grant_type=client_credentials");
            $result = curl_exec($ch);            

            $json = json_decode($result);

            $accessToken = $json->access_token;

            $transaction_id  = $request->transaction_id;
            $user_id  = $request->user_id;

            $get_payment = \DB::table('transactions')
                                ->select('*')
                                ->where('id_transactions','=',$transaction_id)
                                ->first();

            $get_payment_price_unit = \DB::table('projects')
                                ->select('price_unit')
                                ->where('id_project','=',$get_payment->transaction_project_id)
                                ->first();

            $currency = !empty($get_payment_price_unit->price_unit) ? $get_payment_price_unit->price_unit : DEFAULT_CURRENCY;


            if (empty($result)) {
                return FALSE;
            } else {
                $json = json_decode($result);

                $price_to_pay = number_format($get_payment->transaction_total,2);

                $accessToken = $json->access_token;

                $random_string = __random_string();

                $curl = curl_init();
                $data = '{
                            "intent":"sale",
                            "redirect_urls":{
                                "return_url":"'.url('payment/get-payment').'",
                                "cancel_url":"'.url('payment/cancel-payment').'"
                            },
                            "payer":{
                                "payment_method":"paypal"
                            },
                            "application_context": {
                                "shipping_preference": "NO_SHIPPING"
                            },
                            "transactions":[
                            {
                                "amount":{
                                    "total":"'.$price_to_pay.'",
                                    "currency":"'.$currency.'"
                            },
                            "invoice_number": "CB-INV-'.$random_string.'",
                            "description":"This is the payment transaction description."
                            }
                          ]
                        }';

                curl_setopt($curl, CURLOPT_URL, $PayPal_BASE_URL . 'payments/payment');
                curl_setopt($curl, CURLOPT_POST, true);
                curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);
                curl_setopt($curl, CURLOPT_POSTFIELDS, $data); 
                curl_setopt($curl, CURLOPT_HTTPHEADER, array(
                  "Content-Type: application/json",
                  "Authorization: Bearer ".$accessToken, 
                  "Content-length: ".strlen($data))
                );

                $response1 = curl_exec($curl);
                $result = json_decode($response1);

                \Models\PaypalPayment::paypal_response([
                    'user_id'       => $user_id,
                    'response_json' => json_encode($result),
                    'request_type'  => 'Request To PayPal EC. Mode- '.env('PAYPAL_ENV'),
                    'status'        => 'true',
                    'created'       => date('Y-m-d H:i:s')
                ]);

                echo $response1;
            }
        }

        public function mobile_execute_expchk(Request $request){

            //Check PayPal mode, and change PayPal url according for Sandbox or Live.
            $PayPal_BASE_URL = PayPal_BASE_URL_SANDBOX;
            if(env('PAYPAL_ENV') == 'sandbox'){
              $PayPal_BASE_URL = PayPal_BASE_URL_SANDBOX;
            }else{
              $PayPal_BASE_URL = PayPal_BASE_URL_LIVE;
            }

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $PayPal_BASE_URL . 'oauth2/token');
            curl_setopt($ch, CURLOPT_HEADER, FALSE);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
            curl_setopt($ch, CURLOPT_POST, TRUE);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
            curl_setopt($ch, CURLOPT_USERPWD, env('PAYPAL_CLIENT_ID') . ":" . env('PAYPAL_SECRET'));
            curl_setopt($ch, CURLOPT_POSTFIELDS, "grant_type=client_credentials");
            $result = curl_exec($ch);            

            $json = json_decode($result);

            $accessToken = $json->access_token;

            $paymentID = $request->paymentID;
            $payerID   = $request->payerID;

            $transaction_id = $request->transaction_id;
            $project_id     = $request->project_id;
            $proposal_id    = $request->proposal_id;
            $user_id        = $request->user_id;

            $get_payment = \DB::table('transactions')
                                ->select('*')
                                ->where('id_transactions','=',$transaction_id)
                                ->first();

            $get_payment_price_unit = \DB::table('projects')
                                ->select('price_unit')
                                ->where('id_project','=',$get_payment->transaction_project_id)
                                ->first();

            $currency = !empty($get_payment_price_unit->price_unit) ? $get_payment_price_unit->price_unit : DEFAULT_CURRENCY;

            $price_to_pay = number_format($get_payment->transaction_total,2);

            $curl = curl_init();
            $data = '{
                      "payer_id":"'.$payerID.'",
                      "transactions":[
                        {
                          "amount":{
                            "total":"'.$price_to_pay.'",
                            "currency":"'.$currency.'"
                          }
                        }
                      ]
                    }';

            curl_setopt($curl, CURLOPT_URL, $PayPal_BASE_URL.'payments/payment/'.$paymentID.'/execute/');
            curl_setopt($curl, CURLOPT_POST, true);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);
            curl_setopt($curl, CURLOPT_POSTFIELDS, $data); 
            curl_setopt($curl, CURLOPT_HTTPHEADER, array(
              "Content-Type: application/json",
              "Authorization: Bearer ".$accessToken) 
            );

            $response2 = curl_exec($curl);

            //make arr
            $response_arr = (array)json_decode(json_encode(json_decode($response2), 128));

            //check if payment is completed
            if(!empty($response_arr['transactions'])){
                if($response_arr['transactions'][0]->related_resources[0]->sale->state == 'completed'){

                    //save response
                    \Models\PaypalPayment::paypal_response([
                        'user_id'                   => $user_id,
                        'response_json'             => json_encode(json_decode($response2)),
                        'request_type'              => '1. Response From PayPal EC. Mode- '.env('PAYPAL_ENV'),
                        'status'                    => 'true',
                        'created'                   => date('Y-m-d H:i:s')
                    ]);
                    
                    // update to confirm
                    $saleID = $response_arr['transactions'][0]->related_resources[0]->sale->id;

                    $isUpdated = \Models\Payments::update_transaction(
                        $transaction_id,
                        [
                            'transaction_reference_id' => $saleID, 
                            'transaction_status' => 'confirmed', 
                            'updated' => date('Y-m-d H:i:s')
                        ]
                    );

                    if(!empty($isUpdated)){
                        $isProposalAccepted = \Models\Employers::accept_proposal($user_id,$project_id,$proposal_id);
                    }

                    $this->status = true;
                    $this->message = trans('general.'.$isProposalAccepted['message']);
                    $redirect_url = url('payment/payment-redirect?status=success');

                }else{
                    \Models\PaypalPayment::paypal_response([
                        'user_id'                   => $user_id,
                        'response_json'             => json_encode(json_decode($response2)),
                        'request_type'              => '2. Response From PayPal EC. Mode- '.env('PAYPAL_ENV'),
                        'status'                    => 'false',
                        'created'                   => date('Y-m-d H:i:s')
                    ]);

                    $this->status = false;
                    $this->message = 'Something went wrong';
                    $redirect_url = url('payment/payment-redirect?status=fail');

                }

            }else{
                \Models\PaypalPayment::paypal_response([
                    'user_id'                   => $user_id,
                    'response_json'             => json_encode(json_decode($response2)),
                    'request_type'              => '3. Response From PayPal EC. Mode- '.env('PAYPAL_ENV'),
                    'status'                    => 'false',
                    'created'                   => date('Y-m-d H:i:s')
                ]);

                $this->status = false;
                $this->message = 'Something went wrong';
                $redirect_url = url('payment/payment-redirect?status=fail');

            }

            return response()->json([
                'status'       => $this->status,
                'message'      => $this->message,
                'redirect_url' => $redirect_url,
            ]);

        }

        /*Redirect to PayPal Success or Fail page*/
        public function mobile_payment_redirect(Request $request){
            $data['title'] = 'Pay via PayPal';
            return view('front.pages.mobile_paypal_redirect')->with($data);
        }

        /*Return from PayPal mobile on-boarding api*/
        public function save_verified_mobile_paypal_email(Request $request){

            $isUpdated = \Models\Talents::change($request->userID,[
                'paypal_id' => $request->pp_email,
                'paypal_payer_id' => $request->merchantIdInPayPal,
                'updated'   => date('Y-m-d H:i:s')
            ]);

            if($isUpdated){
                return redirect('paypal-mobile-email-success'); /*don't change this url*/
            }else{
                return redirect('paypal-mobile-email-error'); /*don't change this url*/
            }

        }

        /* When a Payout(Manual) type has been added by client, and the payment will be accepted manually (i.e. not paid by paypal). */
        public function accept_payout_mgmt(Request $request){

            $payment = \Session::get("payment");
            $payment_transaction = \Models\Payments::init_employer_payment($payment);

            /*Update project payment type to 'Manual' */
            $payment_where = ['id_project' => $payment['transaction_project_id'] ];
            $payment_data  = ['payment_type' => 'manual' ];
            $payment_type_project = \Models\Projects::change($payment_where,$payment_data);

            $isUpdated = \Models\Payments::update_transaction(
                            $payment_transaction->id_transactions,
                            [
                                'transaction_reference_id' => '-', 
                                'transaction_status'       => 'confirmed', 
                                'transaction_type'         => 'manual',
                                'transaction_comment'      => 'This Job\'s payment will be done outside the system.',
                                'updated'                  => date('Y-m-d H:i:s')
                            ]
                        );

            if(!empty($isUpdated)){
                $isProposalAccepted =  \Models\Employers::accept_proposal(\Auth::user()->id_user,$payment['transaction_project_id'],$payment['transaction_proposal_id']);
            }

            $this->status = true;
            $this->message = trans('general.'.$isProposalAccepted['message']);
            $redirect_url = url(sprintf('%s/project/proposals/talent?proposal_id=%s&project_id=%s',EMPLOYER_ROLE_TYPE,___encrypt($payment['transaction_proposal_id']),___encrypt($payment['transaction_project_id'])));

            \Session::forget('payment');

            return response()->json([
                'status'       => $this->status,
                'message'      => $this->message,
                'redirect_url' => $redirect_url,
            ]);

        }
        
    }