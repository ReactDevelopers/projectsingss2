<?php

    namespace Models; 

    use Illuminate\Database\Eloquent\Model;
    use Illuminate\Support\Facades\DB;
    use Illuminate\Support\Facades\Crypt;
    use Illuminate\Support\Facades\Mail;

    class Employers extends Model{
        protected $table = 'users';
        protected $primaryKey = 'id_user';
        
        const CREATED_AT = 'created';
        const UPDATED_AT = 'updated';

        protected $fillable = [
            'type',
            'name',
            'first_name',
            'last_name',
            'email',
            'gender',
            'password',
            'status',
            'last_login',
        ];

        protected $hidden = [
            'password', 'remember_token',
        ];

        /**
         * [This method is for scope for default keys] 
         * @return Boolean
         */

        public function scopeDefaultKeys($query){
            $prefix         = DB::getTablePrefix();
            
            $query->addSelect([
                'users.id_user',
            ])->name()->companyLogo();

            return $query;
        }        


        /**
         * [This method is for relating employer to projects] 
         * @return Boolean
         */

        public function projects(){
            return $this->hasMany('\Models\Projects','user_id','id_user');
        }  

        /**
         * [This method is for relating employer to reviews] 
         * @return Boolean
         */

        public function reviews(){
            return $this->hasMany('\Models\Reviews','receiver_id','id_user');
        }  

        /**
         * [This method is for relating employer to trasaction] 
         * @return Boolean
         */

        public function transaction(){
            return $this->hasOne('\Models\Transactions','transaction_user_id','id_user');
        }   

        /**
         * [This method is for relating employer to other jobs] 
         * @return Boolean
         */

        public function otherjobs(){
            return $this->hasMany('\Models\Projects','user_id','id_user');
        }   

        /**
         * [This method is for scope for total reviews] 
         * @return Boolean
         */

        public function scopeReview($query){
            $query->leftjoin('reviews','reviews.receiver_id','=','users.id_user')->addSelect([
                \DB::Raw("COUNT(DISTINCT(id_review)) as total_review"),
                \DB::Raw('IFNULL(ROUND(AVG(review_average), 1), "0.0") as rating')
            ]);

            return $query;
        } 
   
        /**
         * [This method is for scope for total hiring] 
         * @return Boolean
         */

        public function scopeTotalHirings($query){
            $prefix         = \DB::getTablePrefix();
            $query->leftjoin('projects','projects.user_id','=','users.id_user')->leftjoin('talent_proposals',function($q){
                $q->on('talent_proposals.project_id','=','projects.id_project');
                $q->on('talent_proposals.status','=',\DB::Raw('"accepted"'));
            })->addSelect([
                \DB::Raw("COUNT(DISTINCT({$prefix}talent_proposals.id_proposal)) as hirings_count"),
            ]);

            return $query;
        } 

        /**
         * [This method is for scope for city] 
         * @return Boolean
         */

        public function scopeCity($query){
            $prefix         = \DB::getTablePrefix();
            $language       = \App::getLocale();
        
            $query->leftjoin('city','city.id_city','users.city')->addSelect([
                \DB::Raw("IF(({$prefix}city.{$language} != ''),{$prefix}city.`{$language}`, {$prefix}city.`en`) as city_name")
            ]);

            return $query;
        }

        /**
         * [This method is for scope for city] 
         * @return Boolean
         */

        public function scopeCountry($query){
            $language       = \App::getLocale();
        
            $query->leftjoin('countries','countries.id_country','users.country')->addSelect([
                \DB::Raw("IF(({$language} != ''),`{$language}`, `en`) as country_name")
            ]);

            return $query;
        } 

        /**
         * [This method is for scope for company logo] 
         * @return Boolean
         */

        public function scopeCompanyLogo($query){
            $base_url       = ___image_base_url();
            $prefix         = DB::getTablePrefix();
            
            $query->leftJoin('files as files',function($leftjoin){
                $leftjoin->on('files.user_id','=','users.id_user');
                $leftjoin->on('files.type','=',\DB::Raw('"profile"'));
            })->addSelect([
                \DB::Raw("
                     IF(
                        {$prefix}files.filename IS NOT NULL,
                        CONCAT('{$base_url}',{$prefix}files.folder,'thumbnail/',{$prefix}files.filename),
                        IF({$prefix}users.social_picture IS NOT NULL OR {$prefix}users.social_picture != '', {$prefix}users.social_picture  ,CONCAT('{$base_url}','images/','".DEFAULT_AVATAR_IMAGE."'))
                    ) as company_logo
                "),
            ]);

            return $query;
        }  

        /**
         * [This method is for scope for talent saved employer] 
         * @return Boolean
         */

        public function ScopeIsTalentSavedEmployer($query,$talent_id){
            $prefix         = DB::getTablePrefix();
            
            $query->leftJoin('saved_employer',function($leftjoin) use($talent_id){
                $leftjoin->on('saved_employer.employer_id','=','users.id_user');
                $leftjoin->on('saved_employer.user_id','=',\DB::Raw($talent_id));
            })->addSelect([
                \DB::Raw("
                    IF(
                        {$prefix}saved_employer.id_saved IS NOT NULL,
                        '".DEFAULT_YES_VALUE."',
                        '".DEFAULT_NO_VALUE."'
                    ) as is_saved
                "),
            ]);

            return $query;
        } 

        /**
         * [This method is for scope for user name] 
         * @return Boolean
         */

        public function scopeName($query){
            $prefix         = DB::getTablePrefix();
            
            $query->addSelect([
                \DB::Raw("TRIM(IF({$prefix}users.last_name IS NULL, {$prefix}users.first_name, CONCAT({$prefix}users.first_name,' ',{$prefix}users.last_name))) as name")
            ]);

            return $query;
        }

        /**
         * [This method is used to add new] 
         * @param [Varchar]$insert_data[Used to insert data]
         * @return Boolean
         */
        
        public static function add_new($insert_data){
            if(empty($insert_data)){
                return (bool) false;
            }else{
                $insert_data['commission'] = ___cache('configuration')['commission'];
            }

            return self::insertGetId($insert_data);
        }

        /**
         * [This method is used for change] 
         * @param [Integer]$user_id [Used for user id]
         * @param [Varchar]$data[Used for data]
         * @return Boolean
         */

        public static function change($userId,$data){
            $table_user = DB::table((new static)->getTable());
            
            return (bool) $table_user->where('id_user',$userId)->update($data);
        }

        /**
         * [This method is used to findById] 
         * @param [Integer]$user_id [Used for user id]
         * @param [Varchar]$key[Used for Keys]
         * @return Data Response
         */

        public static function findById($userID,$keys = ['*']){
            $table_user = DB::table((new static)->getTable());

            if($index = array_search('status',$keys)){
                unset($keys[$index]);
                $keys[$index] = 'users.status';
            }
            
            if(!empty($keys)){
                $table_user->select($keys);
            }
            $table_user->leftjoin('countries as country_code','country_code.phone_country_code','=','users.country_code');
            $table_user->leftjoin('countries as other_country_code','other_country_code.phone_country_code','=','users.other_country_code');
            $table_user->leftjoin('countries','countries.id_country','=','users.country');
            $table_user->leftjoin('state','state.id_state','=','users.state');
            $table_user->leftjoin('city','city.id_city','=','users.city');

            return $table_user->where(
                array(
                    'id_user' => $userID,
                )
            )->whereNotIn('users.status',['trashed'])->first();
        }

        /**
         * [This method is used to findByEmail ]
         * @param [Varchar]$email[Used for email]
         * @param [Varchar]$device_token[<description>]
         * @return Data Response
         */

        public static function findByEmail($email,$keys = ['*']){
            $table_user = DB::table((new static)->getTable());

            if(!empty($keys)){
                $table_user->select($keys);
            }

            return $table_user->where(
                array(
                    'email' => $email,
                )
            )->whereNotIn('status',['trashed'])->first();
        }

        /**
         * [This method is used for row] 
         * @param [Varchar]$email[Used for email]
         * @return Data Response
         */

        public static function row($email){
            $table_user = DB::table((new static)->getTable());

            return $table_user->where(
                array(
                    'email' => $email,
                )
            )->whereNotIn('status',['trashed'])->first();
        }

        /**
         * [This method is used to create file] 
         * @param [Varchar]$data[Used for data]
         * @return Data Response
         */
        
        public static function create_file($data,$multiple = true, $return = false){
            $table_files = DB::table('files');

            if(empty($multiple)){
                if($table_files->where('user_id',$data['user_id'])->where('type',$data['type'])->get()->count()){
                    $isInserted = $table_files->where('user_id',$data['user_id'])->update($data);
                }else{
                    $isInserted = $table_files->insertGetId($data);
                }
                
                if(!empty($return)){
                    return json_decode(
                        json_encode(
                            $table_files->select(['*','extension as type'])->where('user_id',$data['user_id'])
                            ->whereNotIn('status',['trashed'])
                            ->get()
                            ->first()
                        ),true
                    );
                }else{
                    return 1;
                }
            }else{
                $isInserted = $table_files->insertGetId($data);
                if(!empty($return)){
                    return json_decode(
                        json_encode(
                            $table_files->select(['*','extension as type'])->where('id_file',$isInserted)
                            ->whereNotIn('status',['trashed'])
                            ->get()
                            ->first()
                        ),true
                    );
                }else{
                    return $isInserted;
                }
            }
        }

        /**
         * [This method is used to get file] 
         * @param [String]$where[Used for where clause]
         * @param [Fetch]$fetch[Used for fetching]
         * @param [Varchar]$key[Used for Keys]
         * @return Data Response
         */

        public static function get_file($where = "",$fetch = 'all',$keys = ['*']){
            $table_files = DB::table('files');

            if(!empty($where)){
                $table_files->whereRaw($where);
            }

            if(!empty($keys)){
                $table_files->select($keys);
            }

            if($fetch == 'count'){
                return $table_files->get()->count();
            }else if($fetch == 'single'){
                return (array) $table_files->get()->first();
            }else if($fetch == 'all'){
                return json_decode(json_encode($table_files->get()),true);
            }else{
                return $table_files->get();
            }
        }

        /**
         * [This method is used to update certificate] 
         * @param [Integer]$user_id [Used for user id]
         * @param [Varchar]$certificate[Used for certificate]
         * @return Data Response
         */

        public static function update_certificate($user_id,$certificate){
            $table_employer_certificates = DB::table('employer_certificates');

            $certificates = array_map(
                function($i) use($user_id){ 
                    return array(
                        'certificate' => $i,
                        'user_id' => $user_id
                    ); 
                }, 
                $certificate
            );

            if(!empty($certificates)){
                foreach ($certificates as $key => $value) {
                    $table_certificate = DB::table('certificate');
                    $certificate_count = $table_certificate->where('certificate_name',$value['certificate'])->get();
                    // print_r($certificate_count->count());
                    if(empty($certificate_count->count())){
                        $table_certificate->insert([
                            'certificate_name' => $value['certificate'],
                            'created'          => date('Y-m-d H:i:s'),
                            'updated'          => date('Y-m-d H:i:s'),
                        ]);
                    }
                }
            }
            $table_employer_certificates->where('user_id',$user_id);
            $table_employer_certificates->delete();
            $cache_key  = ['certificates'];
            forget_cache($cache_key);                
            return $table_employer_certificates->insert($certificates);
        }

        /**
         * [This method is used for certificates] 
         * @param [Integer]$user_id [Used for user id]
         * @return Data Response
         */

        public static function certificates($user_id){
            $table_user = DB::table('employer_certificates');

            $data = $table_user->where(
                array(
                    'user_id' => $user_id,
                )
            )->get();
            
            $certificates = (array) json_decode(json_encode($data),true);
            return array_column($certificates, 'certificate');
        }

        /**
         * [This method is used to post job] 
         * @param [Integer]$user_id [Used for user id]
         * @param [Varchar]$data[Used for data]
         * @return Data Response
         */

        public static function post_jobs($user_id,$data){
            $table_projects     = DB::table('projects');
            $table_job_title    = DB::table('job_title');
            $user_info = self::get_user(\Auth::user());

            $data['user_id']    = $user_id;
            $data['created']    = date('Y-m-d H:i:s');
            $data['updated']    = date('Y-m-d H:i:s');
            $data['price_unit'] = $user_info['currency'];
            
            if($job_title_data = $table_job_title->where('job_title_name',$data['title'])->get()->first()){
                $job_title_id = $job_title_data->id_job_title;
            }else{
                $title_array['job_title_name']  = $data['title'];
                $title_array['created']         = date('Y-m-d H:i:s');
                $title_array['updated']         = date('Y-m-d H:i:s');
                $job_title_id = $table_job_title->insertGetId($title_array);
                \Cache::forget('job_titles');
            }
            $isInserted = $table_projects->insertGetId($data);

            if(!empty($isInserted)){
                /*Check for converted description exist or not*/
                $projectLang = [];
                $allLang = language();

                if(___configuration(['google_translate_enabled'])['google_translate_enabled'] == 'Y'){
                    $translator = new \Dedicated\GoogleTranslate\Translator;
                    try {
                        $detected_lang = $translator->detect($data['description']);
                    }
                    catch (\Exception $e) {
                        $detected_lang = false;
                    }

                    if($detected_lang){
                        if(array_key_exists($detected_lang, $allLang)){
                            $projectLang[] = [
                                'project_id' => $isInserted,
                                'language' => $detected_lang,
                                'description' => $data['description'],
                                'created' => date('Y-m-d H:i:s'),
                                'updated' => date('Y-m-d H:i:s')
                            ];
                            if($detected_lang != \Cache::get('default_language')){

                                try {
                                    $convertLang = $translator->setTargetLang(\Cache::get('default_language'))
                                    ->translate($data['description']);
                                }
                                catch (\Exception $e) {
                                    $convertLang = false;
                                }

                                if($convertLang){
                                    $projectLang[] = [
                                        'project_id' => $isInserted,
                                        'language' => \Cache::get('default_language'),
                                        'description' => $convertLang,
                                        'created' => date('Y-m-d H:i:s'),
                                        'updated' => date('Y-m-d H:i:s')
                                    ];
                                }
                                else{
                                    $projectLang[] = [
                                        'project_id' => $isInserted,
                                        'language' => \Cache::get('default_language'),
                                        'description' => $data['description'],
                                        'created' => date('Y-m-d H:i:s'),
                                        'updated' => date('Y-m-d H:i:s')
                                    ];
                                }
                            }
                        }
                        else{
                            $projectLang[] = [
                                'project_id' => $isInserted,
                                'language' => \Cache::get('default_language'),
                                'description' => $data['description'],
                                'created' => date('Y-m-d H:i:s'),
                                'updated' => date('Y-m-d H:i:s')
                            ];
                        }
                    }
                    else{
                        $projectLang[] = [
                            'project_id' => $isInserted,
                            'language' => \Cache::get('default_language'),
                            'description' => $data['description'],
                            'created' => date('Y-m-d H:i:s'),
                            'updated' => date('Y-m-d H:i:s')
                        ];
                    }
                }
                else{
                    $projectLang[] = [
                        'project_id' => $isInserted,
                        'language' => \Cache::get('default_language'),
                        'description' => $data['description'],
                        'created' => date('Y-m-d H:i:s'),
                        'updated' => date('Y-m-d H:i:s')
                    ];
                }

                if(count($projectLang) > 0){
                    DB::table('project_language')
                    ->insert($projectLang);
                }

                return (array) self::get_job(
                    sprintf(" id_project = %s", $isInserted),
                    'single',
                    [
                        'projects.id_project',
                        'projects.employment',
                        'projects.industry',
                        'projects.subindustry',
                        'projects.location',
                        'projects.price',
                        'projects.budget_type',
                        'projects.title',
                        'projects.description',
                        'projects.price_type',
                        'projects.expertise',
                        'projects.budget',
                        'projects.startdate',
                        'projects.enddate'
                    ]
                );
            }else{
                return (array) [];
            }
        }

        /**
         * [This method is used to get job] 
         * @param [String]$where[Used for where clause]
         * @param [fetch]$fetch[Used for fetching]
         * @param [Varchar]$key[Used for Keys]
         * @param [Integer]$page[Used for paging]
         * @param [String]$having[Used for having condition]
         * @param [type]$device_token[<description>]
         * @return Data Response
         */

        public static function get_job($where = "",$fetch = 'all',$keys = ['*'],$page = 0, $having = " 1 ", $limit = DEFAULT_PAGING_LIMIT){
            
            /*Check for converted description exist or not*/
            $translator = new \Dedicated\GoogleTranslate\Translator;
            $project_language = DB::table('project_language')
            ->where('project_id', request()->job_id)
            ->where('language', request()->language)
            ->count();
            if($project_language <= 0){
                
                $project_language = DB::table('project_language')
                ->where('project_id', request()->job_id)
                ->where('language', \Cache::get('default_language'))
                ->get()
                ->first();

                if(!empty($project_language)){
                    if(___configuration(['google_translate_enabled'])['google_translate_enabled'] == 'Y'){
                        try{
                            $convertLang = $translator->setTargetLang(request()->language)->translate($project_language->description);
                        }catch(\Exception $e){
                            $convertLang = false;
                        }

                        if($convertLang){
                            $projectLang = [
                                'project_id' => request()->job_id,
                                'language' => request()->language,
                                'description' => $convertLang,
                                'created' => date('Y-m-d H:i:s'),
                                'updated' => date('Y-m-d H:i:s')
                            ];
                            DB::table('project_language')
                            ->insert($projectLang);
                        }
                        else{
                            $projectLang = [
                                'project_id' => request()->job_id,
                                'language' => request()->language,
                                'description' => $project_language->description,
                                'created' => date('Y-m-d H:i:s'),
                                'updated' => date('Y-m-d H:i:s')
                            ];
                            DB::table('project_language')
                            ->insert($projectLang);
                        }
                    }else{
                        $projectLang = [
                            'project_id' => request()->job_id,
                            'language' => request()->language,
                            'description' => $project_language->description,
                            'created' => date('Y-m-d H:i:s'),
                            'updated' => date('Y-m-d H:i:s')
                        ];
                        DB::table('project_language')
                        ->insert($projectLang);
                    }
                }
            }

            $table_projects = DB::table('projects as projects');
            $prefix = DB::getTablePrefix();
            $offset             = 0;
            if(!empty($page)){
                $offset = ($page - 1)*$limit;
            }

            if($fetch != 'rows' && !empty($limit)){
                $table_projects->offset($offset);
                $table_projects->limit($limit);
            }
            
            if(!empty($keys)){
                $keys[] = \DB::Raw("
                    IF(
                        (('".date('Y-m-d')."' BETWEEN DATE({$prefix}projects.startdate) AND DATE({$prefix}projects.enddate)) AND COUNT({$prefix}proposals.id_proposal) > 0),
                        'yes',
                        'no'
                    ) as is_job_running
                ");

                $keys[] = \DB::Raw("
                    IF(
                        ({$prefix}project_log.startdate IS NULL),
                        'start',
                        IF(
                            ({$prefix}project_log.startdate IS NOT NULL AND {$prefix}project_log.start = 'pending'),
                            'startpending',
                            IF(
                                ({$prefix}project_log.enddate IS NULL),
                                'close',
                                IF(
                                    ({$prefix}project_log.enddate IS NOT NULL AND {$prefix}project_log.close = 'pending'),
                                    'closepending',
                                    'done'
                                )
                            )
                        )
                    ) as job_daily_action
                ");

                $keys[] = \DB::Raw("TRIM(CONCAT({$prefix}talent.first_name,' ',{$prefix}talent.last_name)) as accepted_talent_name");
            }
            
            $table_projects->select($keys);
            $table_projects->addSelect([
                \DB::Raw('`CONVERT_PRICE`('.$prefix.'projects.price, '.$prefix.'projects.price_unit, "'.request()->currency.'") AS price'),
                \DB::Raw('`CONVERT_PRICE`('.$prefix.'projects.price_max, '.$prefix.'projects.price_unit, "'.request()->currency.'") AS price_max'),
                \DB::Raw('`CONVERT_PRICE`('.$prefix.'projects.bonus, '.$prefix.'projects.price_unit, "'.request()->currency.'") AS bonus'),
            ]);

            $table_projects->leftJoin('users as users','users.id_user','=','projects.user_id');
            $table_projects->leftJoin('files as files','files.user_id','=','projects.user_id');
            $table_projects->leftJoin('industries as industry','industry.id_industry','=','projects.industry');
            $table_projects->leftJoin('project_required_skills as skills','skills.project_id','=','projects.id_project');
            $table_projects->leftJoin('project_required_qualifications as qualifications','qualifications.project_id','=','projects.id_project');
            $table_projects->leftJoin('industries as sub_industry','sub_industry.id_industry','=','projects.subindustry');
            $table_projects->leftJoin('talent_proposals as proposals',function($leftjoin){
                $leftjoin->on('proposals.project_id','=','projects.id_project');
                $leftjoin->on('proposals.status','=',\DB::Raw("'accepted'"));
            });
            /*$table_projects->leftJoin('chat_requests as chat_requests',function($leftjoin){
                $leftjoin->on('chat_requests.sender_id','=','proposals.user_id');
                $leftjoin->on('chat_requests.receiver_id','=','projects.user_id');
                $leftjoin->on('chat_requests.request_status','=',\DB::Raw("'accepted'"));
            });*/
            $table_projects->leftJoin('users as talent','talent.id_user','=','proposals.user_id');
            $table_projects->leftJoin('city as city','city.id_city','=','projects.location');
            $table_projects->leftJoin('project_log',function($leftjoin) use($prefix){
                $leftjoin->on('project_log.project_id','=','projects.id_project');
                $leftjoin->on(\DB::Raw("DATE({$prefix}project_log.created)"),'=',\DB::Raw("'".date('Y-m-d')."'"));
            });
            $table_projects->leftJoin('project_language', function ($join) {
                $join->on('projects.id_project', '=', 'project_language.project_id')
                    ->where('project_language.language', request()->language);
            });
            
            $table_projects->groupBy(['projects.id_project']);
            $table_projects->havingRaw($having);

            if(!empty($where)){
                $table_projects->whereRaw($where);
            }
            
            $table_projects->orderBy('projects.created','DESC');

            if($fetch == 'count'){
                return $table_projects->get()->count();
            }else if($fetch == 'single'){
                return (array) $table_projects->get()->first();
            }else if($fetch == 'all'){
                return json_decode(json_encode($table_projects->orderByRaw("{$prefix}projects.created DESC")->get()),true);
            }else if($fetch == 'rows'){
                $total = $table_projects->get()->count();

                $table_projects->offset($offset);
                $table_projects->limit($limit);

                $all_jobs  = json_decode(json_encode($table_projects->get()),true);

                return [
                    'total_result' => $total,
                    'total_filtered_result' => $table_projects->get()->count(),
                    'result' => $all_jobs,
                ];
            }else{
                return $table_projects->get();
            }
        }

        /**
         * [This method is used for signup] 
         * @param [Varchar]$data[Used for data]
         * @return Data Response
         */

        public static function __dosignup($data){
            $token          = bcrypt(__random_string());
            $status         = 'pending';
            
            if(empty($data->password)){
                $data->password = __random_string();
                $status         = 'active';
            }
            
            $insert_data = [
                'type'                          => EMPLOYER_ROLE_TYPE,
                'company_profile'               => (!empty($data->work_type))?$data->work_type:'',
                'company_name'                  => ($data->work_type == 'company')?$data->company_name:'',  
                'name'                          => (string)(!empty($data->name))?$data->name:sprintf("%s %s",(string)$data->first_name,(string)$data->last_name),
                'first_name'                    => (string)ucwords($data->first_name),
                'last_name'                     => (string)ucwords($data->last_name),
                'email'                         => (string)$data->email,
                'picture'                       => (string)(!empty($data->social_picture))?$data->social_picture:DEFAULT_AVATAR_IMAGE,
                'password'                      => bcrypt($data->password),
                'coupon_id'                     => 0,
                'api_token'                     => $token,
                'status'                        => $status,
                'agree'                         => 'yes',
                'percentage_default'            => EMPLOYER_DEFAULT_PROFILE_PERCENTAGE,
                'commission'                    => ___cache('configuration')['commission'],
                'commission_type'               => ___cache('configuration')['commission_type'],
                'newsletter_subscribed'         => (!empty($data->newsletter))?'yes':'no',
                'registration_device'           => !empty($data->device_type) ? $data->device_type : 'website',
                'last_login'                    => date('Y-m-d H:i:s'),
                'updated'                       => date('Y-m-d H:i:s'),
                'created'                       => date('Y-m-d H:i:s'),
            ];

            if(!empty($data->social_key)){
                $insert_data[$data->social_key] = $data->social_id;
                $insert_data['social_account']  = DEFAULT_YES_VALUE;
                $insert_data['social_picture']  = $data->picture;
            }

            $isInserted = self::add_new($insert_data);
            
            if(!empty($isInserted)){
                return [
                    'status' => true,
                    'message' => 'M0021',
                    'signup_user_id' => $isInserted
                ];
            }else{
                return [
                    'status' => false,
                    'message' => 'M0022',
                    'signup_user_id' => false
                ];
            }
        }

        /**
         * [This method is used to get user] 
         * @param [Integer]$user [Used for user]
         * @param [Boolean] $db_flag[Used for to request from database if value is true]
         * @return Data Response
         */
        
        public static function get_user($user, $db_flag = true){
            $prefix = DB::getTablePrefix();
            $language = \App::getLocale();
            
            $keys = array(
                'id_user',
                'type',
                'first_name',
                'last_name',
                'email',
                'gender',
                'mobile',
                'other_mobile',
                'country_code',
                'other_country_code',
                'address',
                'country',
                'state',
                'postal_code',
                'picture',
                'website',
                'other_mobile',
                'company_biography',
                'company_work_field',
                'company_name',
                'company_website',
                'company_profile',
                'contact_person_name',
                'facebook_id',
                'instagram_id',
                'twitter_id',
                'linkedin_id',
                'googleplus_id',
                'is_mobile_verified',
                'chat_status',
                'commission',
                'commission_type',
                'is_subscribed',
                'users.created',
                'currency',
                'social_picture',
                'paypal_id'
            ); 
            
            if(empty($db_flag)){
                $data = array_intersect_key(
                    json_decode(json_encode($user),true), 
                    array_flip($keys)
                );
            }else{
                $keys = array_merge($keys,[
                    \DB::raw('"0" as job_completion'),
                    \DB::raw('"0" as availability_hours'),
                    \DB::Raw("IF(({$prefix}country_code.{$language} != ''),{$prefix}country_code.`{$language}`, {$prefix}country_code.`en`) as country_code_name"),
                    \DB::Raw("IF(({$prefix}other_country_code.{$language} != ''),{$prefix}other_country_code.`{$language}`, {$prefix}other_country_code.`en`) as other_country_code_name"),
                    \DB::Raw('`CONVERT_PRICE`('.$prefix.'users.workrate, '.$prefix.'users.currency, "'.request()->currency.'") AS c_workrate'),
                    \DB::Raw('`CONVERT_PRICE`('.$prefix.'users.workrate_max, '.$prefix.'users.currency, "'.request()->currency.'") AS c_workrate_max'),
                    \DB::Raw("IF(({$prefix}countries.{$language} != ''),{$prefix}countries.`{$language}`, {$prefix}countries.`en`) as country_name"),
                    \DB::Raw("IF(({$prefix}state.{$language} != ''),{$prefix}state.`{$language}`, {$prefix}state.`en`) as state_name"),
                    \DB::Raw("IF(({$prefix}city.{$language} != ''),{$prefix}city.`{$language}`, {$prefix}city.`en`) as city_name"),
                ]);

                $data = json_decode(json_encode(self::findById($user->id_user,$keys)),true);
            }
            if(!empty($data)){


                $data['first_name']                         = ucwords($data['first_name']);
                $data['last_name']                          = ucwords($data['last_name']);
                $data['picture']                            = get_file_url(self::get_file(sprintf(" type = 'profile' AND user_id = %s",$user->id_user),'single',['filename','folder']));
                
                /*$profileUrl = self::get_file(sprintf(" type = 'profile' AND user_id = %s",$user->id_user),'single',['filename','folder']);
                if(empty($profileUrl) && empty($data['social_picture'])){
                    $data['picture']  = get_file_url($profileUrl);
                }elseif (!empty($profileUrl)) {
                    $data['picture']  = get_file_url($profileUrl);
                }elseif (!empty($data['social_picture'])) {
                    $data['picture'] = $data['social_picture'];
                }*/
                $data['certificates']                       = \Models\Employers::certificates($user->id_user);
                                
                $data['notification_count']                 = \Models\Notifications::unread_notifications($data['id_user']);
                $data['proposal_count']                     = \Models\Notifications::unread_notifications($data['id_user'],'proposals',$data['type']);
               
                $reviews = \Models\Reviews::summary($data['id_user']);
                $data = array_merge($data,$reviews);
                
                /*UPDATING PROFILE PERCENTAGE*/
                self::update_profile_percentage($data);
                $data = array_merge(self::get_profile_percentage($user->id_user),$data);

                $data['sender']                             = ucwords(trim(sprintf("%s %s",$data['first_name'],$data['last_name'])));
                $data['field_name']                         = !empty($data['company_work_field']) ? ___cache("skills",$data['company_work_field']) : N_A;
                $data['sender_id']                          = $data['id_user'];
                $data['sender_picture']                     = $data['picture'];
                $data['sender_email']                       = ___e($data['email']);
                $data['sender_profile_link']                = "";
            }
            
            return $data;
        }

        /**
         * [This method is used to get profile percentage] 
         * @param [Integer]$user_id [Used for user id]
         * @return Data Response
         */

        public static function get_profile_percentage($user_id){
            /*\DB::Raw('
                IFNULL(
                    (
                        IFNULL(percentage_default,0)
                        +
                        IFNULL(percentage_step_one,0)
                        +
                        IFNULL(percentage_step_two,0)
                        +
                        IFNULL(percentage_step_three,0)
                    ),
                    0
                ) as profile_percentage_count
            '),
            \DB::Raw('IFNULL(percentage_default,0) as percentage_default'),
            */
            $keys = [
                \DB::Raw('IFNULL(percentage_step_one,0) as profile_percentage_step_one'),
                \DB::Raw('IFNULL(percentage_step_two,0) as profile_percentage_step_two'),
                \DB::Raw('IFNULL(percentage_step_three,0) as profile_percentage_step_three')
            ];
            
            $result = (array) json_decode(json_encode(self::findById($user_id,$keys)),true);

            $result['profile_percentage_step_one']        = (string)(float)($result['profile_percentage_step_one']);
            $result['profile_percentage_step_two']        = (string)(float)($result['profile_percentage_step_two']);
            $result['profile_percentage_step_three']      = '0';
            $result['profile_percentage_count']           = (string)(int)___rounding((float)($result['profile_percentage_step_one']+$result['profile_percentage_step_two']),2);     
            return $result;            

        }

        /**
         * [This method is used to find user's] 
         * @param [Integer]$user [Used for user]
         * @param [Fetch]$page[Used for fetching]
         * @param [Search]$search[Used for searching]
         * @param [String]$having[Used for having condition]
         * @param [Integer]$page[Used for paging]
         * @param [Sort]$sort[Used for sorting]
         * @param [Varchar]$keys[Used for keys]
         * @param [Integer]$limit[Used for limit]
         * @return Data Response
         */

        public static function find_talents($user,$request){
            $prefix                 = DB::getTablePrefix();
            $language               = \App::getLocale();
            $current_datetime       = date('Y-m-d H:i:s');
            $past_datetime          = date('Y-m-d H:i:s', mktime(date('H'),date('i'),date('s'),date('m'),date('d')-7,date('Y')));
            $page                   = (!empty($request->page))?$request->page:1;
            $limit                  = DEFAULT_PAGING_LIMIT;
            $offset                 = ($page-1)*DEFAULT_PAGING_LIMIT;
            $minimum_percentage     = MINIMUM_PERCENTAGE_FOR_SEARCHING;
            $base_url               = ___image_base_url();
            $search                 = !empty($request->search)? $request->search : '';
            $keys = [
                'users.id_user',
                'users.type',
                'users.company_profile',
                'users.gender',
                'users.country',
                'users.expertise',
                'users.created',
                \DB::Raw("IF(
                    {$prefix}saved_talent.id_saved IS NOT NULL,
                    '".DEFAULT_YES_VALUE."',
                    '".DEFAULT_NO_VALUE."'
                ) as is_saved"),
                \DB::Raw("
                    IF(
                        {$prefix}files.filename IS NOT NULL,
                        CONCAT('{$base_url}',{$prefix}files.folder,'thumbnail/',{$prefix}files.filename),
                        IF({$prefix}users.social_picture IS NOT NULL OR {$prefix}users.social_picture != '', {$prefix}users.social_picture  ,CONCAT('{$base_url}','images/','".DEFAULT_AVATAR_IMAGE."'))
                    ) as picture
                "),
                \DB::Raw("GROUP_CONCAT({$prefix}skill.skill_name) as skill_name"),
                \DB::Raw("GROUP_CONCAT(IF((`{$prefix}user_subindustry`.`{$language}` != ''),`{$prefix}user_subindustry`.`{$language}`, `{$prefix}user_subindustry`.`en`)) as subindustry_name"),
                \DB::Raw("GROUP_CONCAT(IF((`{$prefix}user_industry`.`{$language}` != ''),`{$prefix}user_industry`.`{$language}`, `{$prefix}user_industry`.`en`)) as industry_name"),
                \DB::raw("IFNULL(CONCAT({$prefix}users.first_name,' ',{$prefix}users.last_name),{$prefix}users.first_name) as name"),
                \DB::Raw("IF(({$prefix}countries.{$language} != ''),{$prefix}countries.`{$language}`, {$prefix}countries.`en`) as country_name"),
                \DB::Raw("IF(({$prefix}city.{$language} != ''),{$prefix}city.`{$language}`, {$prefix}city.`en`) as city_name"),
                \DB::raw('"0" as job_completion'),
                \DB::raw('"0" as availability_hours'),
                \DB::raw('(SELECT IFNULL(ROUND(AVG(review_average), 1), "0.0") FROM '.$prefix.'reviews AS rev WHERE rev.receiver_id = '.$prefix.'users.id_user LIMIT 1) as rating'),
                \DB::raw('(SELECT COUNT(*) FROM '.$prefix.'reviews AS rev WHERE rev.receiver_id = '.$prefix.'users.id_user LIMIT 1) as review'),
                //\DB::raw('(SELECT IFNULL(ROUND((TIMESTAMPDIFF(MINUTE, `startdate`, `enddate`)/60), 0), 0) AS total_hours FROM `'.$prefix.'project_log` AS pl WHERE pl.talent_id = '.$prefix.'users.id_user AND `startdate` >= "'.$past_datetime.'" AND `enddate` <= "'.$current_datetime.'" LIMIT 1) AS availability_hours'),
                \DB::raw("(SELECT updated FROM {$prefix}viewed_talent WHERE  `employer_id` = {$user->id_user} AND  `talent_id` = {$prefix}users.id_user) as last_viewed"),
            ]; 

            $users = Talents::select($keys)->with([
                'interests' => function($q) use($language,$prefix){
                    $q->select(
                        'user_id',
                        'interest',
                        \DB::Raw("`CONVERT_PRICE`(workrate, currency,'".request()->currency."') AS workrate")
                    );
                },
                'industries.industries' => function($q) use($language,$prefix){
                    $q->select(
                        'id_industry',
                        \DB::Raw("IF(({$language} != ''),`{$language}`, `en`) as name")
                    );
                },
                'skill.skills' => function($q) use($language,$prefix){
                    $q->select(
                        'id_skill',
                        'skill_name'
                    );
                },
                'jurisdiction.juris' => function($q) use($language,$prefix){
                    $q->select(
                        'firm_jurisdiction.country_id as countryid'
                    );
                },
                'getCompany'
            ])
            ->leftJoin('countries as countries','countries.id_country','=','users.country')
            ->leftJoin('talent_interests','talent_interests.user_id','=','users.id_user')
            ->leftJoin('city','city.id_city','=','users.city')
            
            ->leftJoin('talent_skills','talent_skills.user_id','=','users.id_user')
            ->leftJoin('skill','skill.id_skill','=','talent_skills.skill_id')
            
            ->leftJoin('talent_subindustries as talent_subindustry','talent_subindustry.user_id','=','users.id_user')
            ->leftJoin('industries as user_subindustry','user_subindustry.id_industry','=','talent_subindustry.subindustry_id')

            ->leftJoin('talent_industries as talent_industry','talent_industry.user_id','=','users.id_user')
            ->leftJoin('industries as user_industry','user_industry.id_industry','=','talent_industry.industry_id')

            ->leftJoin('saved_talent as saved_talent',function($leftjoin) use($user){
                $leftjoin->on('saved_talent.talent_id','=','users.id_user');
                $leftjoin->on('saved_talent.user_id','=',DB::Raw($user->id_user));
            })->leftjoin('files',function($leftjoin){
                $leftjoin->on('files.user_id','=','users.id_user');
                $leftjoin->where('files.type','=',\DB::Raw("'profile'"));
            });
            // ->leftJoin('firm_jurisdiction','firm_jurisdiction.user_id','=','users.id_user');

            $users->whereRaw("(
                IFNULL(
                    (
                        IFNULL({$prefix}users.percentage_default,0)+
                        IFNULL({$prefix}users.percentage_step_one,0)+
                        IFNULL({$prefix}users.percentage_step_two,0)+
                        IFNULL({$prefix}users.percentage_step_three,0)
                    ),0
                ) >= {$minimum_percentage}
                AND {$prefix}users.status = 'active' 
                AND {$prefix}users.type = 'talent'
                AND ({$prefix}users.first_name IS NOT NULL OR {$prefix}users.first_name != '')
            )");
            
            if(!empty(trim($search))){
                $search = trim($search);
            
                $users->havingRaw("(
                        name LIKE '%$search%' 
                        OR
                        country_name LIKE '%{$search}%'
                        OR
                        skill_name LIKE '%{$search}%'
                        OR
                        industry_name LIKE '%{$search}%'
                        OR
                        subindustry_name LIKE '%{$search}%'

                    )
                ");  
                
               /* $users->when($search,function($q) use($search){
                    $q->whereHas('getCompany',function($q) use($search) {
                        $q->orWhere("company_name","LIKE","%$search%");
                    });  
                });*/
            }

            if($request->location_filter){
                $location_filter = $request->location_filter;
                $users->havingRaw("
                    city_name LIKE '%{$location_filter}%'
                ");
            }

            if(!empty($request->sortby_filter)){
                $users->orderByRaw(sprintf("%s%s",$prefix,___decodefilter($request->sortby_filter)));
            }else{
                $users->orderByRaw("rating DESC");
            }

            if(!empty($request->employment_type_filter)){
                $users->whereHas('interests',function($q) use($request){
                    $q->whereIn('interest',$request->employment_type_filter);    
                });
            }

            if(!empty($request->company_profile_filter)){
                $users->where('company_profile',$request->company_profile_filter);
            }

            if(trim($request->hourly_min_filter) != '' && trim($request->hourly_max_filter) == '' ){
                $users->where("talent_interests.interest","hourly");
                $users->where(\DB::Raw("`CONVERT_PRICE`({$prefix}talent_interests.workrate, {$prefix}users.currency,'".request()->currency."')"),">=",trim($request->hourly_min_filter));
            }else if(trim($request->hourly_min_filter) == '' && trim($request->hourly_max_filter) != '' ){
                $users->where("talent_interests.interest","hourly");
                $users->where(\DB::Raw("`CONVERT_PRICE`({$prefix}talent_interests.workrate, {$prefix}users.currency,'".request()->currency."')"),"<=",trim($request->hourly_max_filter));
            }else if(trim($request->hourly_min_filter) != '' && trim($request->hourly_max_filter) != '' ){
DB::table('users')                ->where("talent_interests.interest","hourly");
                $users->whereRaw("(".\DB::Raw("`CONVERT_PRICE`({$prefix}talent_interests.workrate, {$prefix}users.currency,'".request()->currency."')").">=".trim($request->hourly_min_filter)." AND ".\DB::Raw("`CONVERT_PRICE`({$prefix}talent_interests.workrate, {$prefix}users.currency,'".request()->currency."')")."<=".trim($request->hourly_max_filter).")");
            }

            if(trim($request->monthly_min_filter) != '' && trim($request->monthly_max_filter) == '' ){
                $users->where("talent_interests.interest","monthly");
                $users->where(\DB::Raw("`CONVERT_PRICE`({$prefix}talent_interests.workrate, {$prefix}users.currency,'".request()->currency."')"),">=",trim($request->monthly_min_filter));
            }else if(trim($request->monthly_min_filter) == '' && trim($request->monthly_max_filter) != '' ){
                $users->where("talent_interests.interest","monthly");
                $users->where(\DB::Raw("`CONVERT_PRICE`({$prefix}talent_interests.workrate, {$prefix}users.currency,'".request()->currency."')"),"<=",trim($request->monthly_max_filter));
            }else if(trim($request->monthly_min_filter) != '' && trim($request->monthly_max_filter) != '' ){
                $users->where("talent_interests.interest","monthly");
                $users->whereRaw("(".\DB::Raw("`CONVERT_PRICE`({$prefix}talent_interests.workrate, {$prefix}users.currency,'".request()->currency."')").">=".trim($request->monthly_min_filter)." AND ".\DB::Raw("`CONVERT_PRICE`({$prefix}talent_interests.workrate, {$prefix}users.currency,'".request()->currency."')")."<=".trim($request->monthly_max_filter).")");
            }

            if(trim($request->fixed_min_filter) != '' && trim($request->fixed_max_filter) == '' ){
                $users->where("talent_interests.interest","fixed");
                $users->where(\DB::Raw("`CONVERT_PRICE`({$prefix}talent_interests.workrate, {$prefix}users.currency,'".request()->currency."')"),">=",trim($request->fixed_min_filter));
            }else if(trim($request->fixed_min_filter) == '' && trim($request->fixed_max_filter) != '' ){
                $users->where("talent_interests.interest","fixed");
                $users->where(\DB::Raw("`CONVERT_PRICE`({$prefix}talent_interests.workrate, {$prefix}users.currency,'".request()->currency."')"),"<=",trim($request->fixed_max_filter));
            }else if(trim($request->fixed_min_filter) != '' && trim($request->fixed_max_filter) != '' ){
                $users->where("talent_interests.interest","fixed");
                $users->whereRaw("(".\DB::Raw("`CONVERT_PRICE`({$prefix}talent_interests.workrate, {$prefix}users.currency,'".request()->currency."')").">=".trim($request->fixed_min_filter)." AND ".\DB::Raw("`CONVERT_PRICE`({$prefix}talent_interests.workrate, {$prefix}users.currency,'".request()->currency."')")."<=".trim($request->fixed_max_filter).")");
            }
            if(!empty($request->expertise_filter)){
                $users->when($request->expertise_filter,function($q) use($request){
                    $q->whereIn('expertise',$request->expertise_filter);    
                });
            }

            if(!empty($request->industry_filter)){
                $users->when($request->industry_filter,function($q) use ($request){
                    $q->whereHas('industries.industries',function($q) use($request){
                        $q->whereIn('talent_industries.industry_id',$request->industry_filter);
                    });    
                });
            }

            if(!empty($request->skills_filter)){
                $users->when($request->skills_filter,function($q) use ($request){
                    $q->whereHas('skill.skills',function($q) use($request){
                        $q->whereIn('skill_name',$request->skills_filter);
                    });    
                });
            }
            // dd($users->get());
// dd($request->jurisdiction_filter);
            if(!empty($request->jurisdiction_filter)){
                $users->when($request->jurisdiction_filter,function($q) use ($request){
                    $q->whereHas('jurisdiction.juris',function($q) use($request){
                        $q->whereIn('country_id',$request->jurisdiction_filter);
                    });    
                });
            }

            if(!empty($request->city_filter)){
                $users->whereIn('users.country',$request->city_filter);
            }

            if(!empty($request->saved_talent_filter)){
                $users->when($request->saved_talent_filter,function($q){
                    $q->having('is_saved','=',DEFAULT_YES_VALUE);
                });
            }

            

            $users->groupBy(['users.id_user']);
            $talents = json_decode(json_encode($users->limit($limit)->offset($offset)->get()),true);
            
            array_walk($talents, function(&$item){
                $item['price_unit']         = ___cache('currencies')[request()->currency];
                $item['last_viewed']        = ___ago($item['last_viewed']);
                $item['name']               = ucwords($item['name']);
            });

            $users = [
                'result'                => $talents,
                'total'                 => $users->get()->count(),
                'total_filtered_result' => $users->limit($limit)->offset($offset)->get()->count(),
            ];

            return $users;
        }

        /**
         * [This method is used to update job skills] 
         * @param [Integer]$project_id[Used for project id]
         * @param [Varchar]$skill[Used for skills]
         * @param [Varchar]$subindustry[Used for sub industry]
         * @return Data Response
         */

        public static function update_job_skills($project_id,$skill){
            $table_project_required_skills = DB::table('project_required_skills');
            $table_project_required_skills->where('project_id',$project_id);
            $table_project_required_skills->delete();
            
            if(!empty($skill) && is_array($skill)){
                $skills_list = array_map(
                    function($i){
                        return array(
                            'skill_name'        => substr($i,0,TAG_LENGTH),
                            'created'           => date('Y-m-d H:i:s'),
                            'updated'           => date('Y-m-d H:i:s')
                        ); 
                    }, 
                    $skill
                );

                
                foreach ($skills_list as $key) {
                    $table_skill = DB::table('skill');
                    $inserted_skill = $table_skill->select('id_skill')->where('skill_name',$key['skill_name'])->first();
                    if(!empty($inserted_skill->id_skill)){
                        $skill_id = $inserted_skill->id_skill;
                    }else{
                        $skill_id    = $table_skill->insertGetId($key);
                        \Cache::forget('skills');
                    }

                    $skills[] = [
                        'project_id' => $project_id,
                        'skill_id'   => $skill_id
                    ];
                }
                return $table_project_required_skills->insert($skills);
            }else{
                return true;
            }
        }

        /**
         * [This method is used to update required qualification] 
         * @param [Integer]$project_id[Used for project id]
         * @param [Varchar]$device_token[Used for Qualification]
         * @return Data Response
         */

        public static function update_required_qualification($project_id,$qualification){
            $table_project_required_qualification = DB::table('project_required_qualifications');
            $qualifications = array_map(
                function($i) use($project_id){ 
                    return array(
                        'qualification' =>$i,
                        'project_id' => $project_id
                    ); 
                }, 
                $qualification
            );
            $table_project_required_qualification->where('project_id',$project_id);
            $table_project_required_qualification->delete();

            return $table_project_required_qualification->insert($qualifications);
        }

        /**
         * [This method is used for all proposals] 
         * @param [Integer]$user_id[Used for user id]
         * @param [Search]$search[Used for searching]
         * @param [Integer]$page[Used for paging]
         * @param [Sort]$sort[Used for sorting]
         * @param [Integer]$limit[Used for limit]
         * @return Data Response
         */

        public static function all_proposals($user_id,$search = "",$page = 1,$sort = "projects.id_project DESC", $limit = DEFAULT_PAGING_LIMIT){
            
            $offset = 0;
            $table_projects = DB::table('projects as projects');
            $prefix = DB::getTablePrefix();

            if(empty($keys)){
                $keys = [
                    'projects.id_project',
                    'projects.title',
                    'projects.created',
                    DB::Raw("COUNT({$prefix}proposals.id_proposal) as total_proposals"),
                    DB::Raw("
                        IF(
                            (
                                SELECT COUNT(proposal.id_proposal) FROM {$prefix}talent_proposals as proposal 
                                WHERE proposal.project_id = {$prefix}proposals.project_id
                                AND proposal.status = 'accepted'
                            ),
                            '".DEFAULT_YES_VALUE."',
                            '".DEFAULT_NO_VALUE."'
                        ) as proposal_current_status
                    "),
                ]; 
            }

            $table_projects->select($keys);
            $table_projects->leftJoin('talent_proposals as proposals','proposals.project_id','=','projects.id_project');
            
            $where =" 1 ";

            $table_projects->select($keys);

            if(!empty($search)){
                $search = sprintf(" {$prefix}projects.title LIKE '%%%s%%'",$search);
            }else{
                $search = " 1 ";
            }

            $table_projects->whereRaw(sprintf("%s AND %s",$where,$search));
            $table_projects->where('projects.user_id','=',$user_id);

            if(!empty($sort)){
                $table_projects->orderByRaw("{$prefix}$sort");
            }else{
                $table_projects->orderByRaw("{$prefix}projects.created DESC");
            }

            $table_projects->groupBy(['projects.id_project']);

            $total = $table_projects->get()->count();

            if(!empty($page)){
                $table_projects->limit($limit);
                $table_projects->offset(($page - 1)*$limit);  
            }

            $projects  = json_decode(json_encode($table_projects->get()),true);
            
            $total_filtered_result = $table_projects->get()->count();

            array_walk($projects, function(&$item){
                $item['created'] = ___ago($item['created']);
                if($item['proposal_current_status'] == DEFAULT_YES_VALUE){
                    $item['proposal_status'] = trans('general.M0208');
                }else{
                    $item['proposal_status'] = trans('general.M0209');
                }
            });

            return [
                'total' => $total,
                'result' => $projects,
                'total_filtered_result' => $total_filtered_result,
            ];
        }

        /**
         * [This method is used for project header in detai] 
         * @param [Integer]$user_id[Used for user id]
         * @param [Integer]$projects_id[Used for project id]
         * @return Data Response
         */
        
        public static function project_header_detail($user_id,$project_id){
            $table_projects = DB::table('projects as projects');
            $prefix         = DB::getTablePrefix();
            $language       = \App::getLocale();

            $keys = [
                'projects.id_project',
                'projects.user_id as company_id',
                'projects.title',
                'projects.description',
                'users.company_name',
                'projects.industry',
                'projects.location',
                'projects.created',
                \DB::Raw("IF(({$prefix}industry.`{$language}` != ''),{$prefix}industry.`{$language}`, {$prefix}industry.`en`) as industry_name"),
                #'projects.price',
                #'projects.price_max',
                #'projects.bonus',
                \DB::Raw('`CONVERT_PRICE`('.$prefix.'projects.price, '.$prefix.'projects.price_unit, "'.request()->currency.'") AS price'),
                \DB::Raw('`CONVERT_PRICE`('.$prefix.'projects.price_max, '.$prefix.'projects.price_unit, "'.request()->currency.'") AS price_max'),
                \DB::Raw('`CONVERT_PRICE`('.$prefix.'projects.bonus, '.$prefix.'projects.price_unit, "'.request()->currency.'") AS bonus'),

                'projects.budget_type',
                'projects.price_type',
                'projects.price_unit',
                'projects.employment',
                'projects.expertise',
                'projects.project_status',
                'projects.created',
                'projects.work_hours',
                \DB::Raw("
                    IFNULL(
                        IF(
                            ({$prefix}city.`{$language}` != ''),
                            {$prefix}city.`{$language}`,
                            {$prefix}city.`en`
                        ),
                        ''
                    ) as location_name"
                ),
                \DB::Raw("COUNT({$prefix}proposals.id_proposal) as total_proposals"),
                \DB::Raw("GROUP_CONCAT({$prefix}qualifications.qualification) as required_qualifications"),
                \DB::Raw("DATE({$prefix}projects.startdate) as startdate"),
                \DB::Raw("IF((DATE({$prefix}projects.startdate) < DATE('".date('Y-m-d')."')),'".DEFAULT_YES_VALUE."','".DEFAULT_NO_VALUE."') as is_expired"),
                \DB::Raw("DATE({$prefix}projects.enddate) as enddate"),
                \DB::Raw("GROUP_CONCAT({$prefix}skills.skill) as skills"),   
                \DB::Raw("
                    IF(
                        (
                            SELECT COUNT(proposal.id_proposal) FROM {$prefix}talent_proposals as proposal 
                            WHERE proposal.project_id = {$prefix}proposals.project_id
                            AND proposal.status = 'accepted'
                        ),
                        '".DEFAULT_YES_VALUE."',
                        '".DEFAULT_NO_VALUE."'
                    ) as proposal_current_status
                "),
            ]; 

            $table_projects->select($keys);
            $table_projects->leftJoin('users as users','users.id_user','=','projects.user_id');
            $table_projects->leftJoin('talent_proposals as proposals','proposals.project_id','=','projects.id_project');
            $table_projects->leftJoin('industries as industry','industry.id_industry','=','projects.industry');
            $table_projects->leftJoin('industries as sub_industry','sub_industry.id_industry','=','projects.subindustry');
            $table_projects->leftJoin('project_required_skills as skills','skills.project_id','=','projects.id_project');
            $table_projects->leftJoin('project_required_qualifications as qualifications','qualifications.project_id','=','projects.id_project');            
            $table_projects->leftJoin('city as city','city.id_city','=','projects.location');
            $table_projects->where('projects.user_id','=',$user_id);
            $table_projects->where('projects.id_project','=',$project_id);
            $table_projects->groupBy(['projects.id_project']);

            $project = json_decode(json_encode($table_projects->get()->first()),true);

            if(!empty($project)){
                $project['created'] =  ___ago($project['created']);
                if($project['proposal_current_status'] == DEFAULT_YES_VALUE){
                    $project['proposal_status'] = trans('general.M0208');
                }else{
                    $project['proposal_status'] = trans('general.M0209');
                }
            }

            return $project; 
            
        }

         /**
         * [This method is used for proposal list] 
         * @param [Integer]$user_id[Used for user id]
         * @param [Integer]$projects_id[Used for project id]
         * @param [Fetch]$page[Used for fetching]
         * @return Data Response
         */

        public static function proposal_list($user_id,$project_id,$fetch = 'all'){
            $prefix = DB::getTablePrefix();
            $table_proposals = DB::table('talent_proposals as proposals');


            if($fetch == 'single'){
                $where = ['id_proposal' => $project_id];
            }else{
                $where = ['project_id'  => $project_id];
            }

            $proposals_keys = [
                'proposals.id_proposal',
                'projects.employment',
                'proposals.project_id',
                'proposals.user_id',
                #'proposals.submission_fee',
                #'proposals.quoted_price',
                \DB::Raw('`CONVERT_PRICE`('.$prefix.'proposals.submission_fee, '.$prefix.'proposals.price_unit, "'.request()->currency.'") AS submission_fee'),
                \DB::Raw('`CONVERT_PRICE`('.$prefix.'proposals.quoted_price, '.$prefix.'proposals.price_unit, "'.request()->currency.'") AS quoted_price'),
                'proposals.comments',
                'proposals.status',
                'proposals.created',
                'files.id_file',
                'files.filename',
                DB::Raw("CONCAT({$prefix}files.folder,'',{$prefix}files.filename) as file_url"),
                'files.size',
                'user_profile.filename as picture',
                DB::Raw("CONCAT({$prefix}users.first_name,' ',{$prefix}users.last_name) as name"),
                DB::Raw("'0' as review"),
                DB::Raw("'0.0' as ratings"),
                DB::Raw("IF({$prefix}tagged.id_tagged IS NOT NULL,'".DEFAULT_YES_VALUE."','".DEFAULT_NO_VALUE."') as is_tagged"),
            ];

            $proposals = $table_proposals
            ->select($proposals_keys)
            ->leftjoin('projects','projects.id_project','=','proposals.project_id')
            ->leftjoin('files as files', function($q){
                $q->on('files.record_id','=','proposals.id_proposal');
                $q->where('files.type','=','proposal');
            })
            ->leftJoin('tagged_proposals as tagged',function($leftjoin) use($user_id){
                $leftjoin->on('tagged.proposal_id','=','proposals.id_proposal');
                $leftjoin->on('tagged.employer_id','=',DB::Raw("'{$user_id}'"));
            })
            ->leftjoin('users as users','proposals.user_id','=','users.id_user')
            ->leftjoin('files as user_profile',function($q){
                $q->on('user_profile.user_id','=','users.id_user');
                $q->where('files.type','=','profile');
            })
            ->where($where)
            ->orderByRaw("{$prefix}proposals.status ASC")
            ->groupBy(['user_id']);
            
            if($fetch == 'single'){
                return json_decode(json_encode($proposals->first()),true);
            }elseif($fetch == 'object'){
                return $proposals; 
            }else{
                return $proposals->get();
            }
        }

        /**
         * [This method is used for tagged proposals] 
         * @param [Integer]$user_id[Used for user id]
         * @param [Integer]$projects_id[Used for project id]
         * @param [Integer]$proposal_id[Used for proposal id]
         * @return Json Response
         */

        public static function tagged_proposals($user_id,$project_id,$proposal_id){
            $prefix = DB::getTablePrefix();
            $table_proposals = DB::table('talent_proposals as proposals');
            $proposals_keys = [
                'proposals.id_proposal',
                'projects.user_id as company_id',
                'projects.employment',
                'proposals.project_id',
                'proposals.user_id',
                'proposals.submission_fee',
                'proposals.quoted_price',
                'proposals.comments',
                'proposals.status',
                'proposals.created',
                'files.id_file',
                'files.filename',
                DB::Raw("CONCAT({$prefix}files.folder,'',{$prefix}files.filename) as file_url"),
                'files.size',
                'user_profile.filename as picture',
                DB::Raw("CONCAT({$prefix}users.first_name,' ',{$prefix}users.last_name) as name"),
                DB::Raw("'0' as review"),
                DB::Raw("'0.0' as ratings"),
                DB::Raw("IF({$prefix}tagged.id_tagged IS NOT NULL,'".DEFAULT_YES_VALUE."','".DEFAULT_NO_VALUE."') as is_tagged"),
            ];

            $proposals = $table_proposals
            ->select($proposals_keys)
            ->leftjoin('projects','projects.id_project','=','proposals.project_id')
            ->leftjoin('files as files', function($q){
                $q->on('files.record_id','=','proposals.id_proposal');
                $q->where('files.type','=','proposal');
            })
            ->leftJoin('tagged_proposals as tagged',function($leftjoin) use($user_id){
                $leftjoin->on('tagged.proposal_id','=','proposals.id_proposal');
                $leftjoin->on('tagged.employer_id','=',DB::Raw("'{$user_id}'"));
            })
            ->leftjoin('users as users','proposals.user_id','=','users.id_user')
            ->leftjoin('files as user_profile',function($q){
                $q->on('user_profile.user_id','=','users.id_user');
                $q->where('files.type','=','profile');
            })
            ->where('id_proposal','!=',$proposal_id)
            ->where('project_id','=',$project_id)
            ->whereRaw("{$prefix}tagged.id_tagged IS NOT NULL")
            ->orderByRaw("{$prefix}proposals.status ASC")
            ->limit(TAGGED_PROPOSAL_LIMIT)
            ->groupBy(['user_id'])->get();
            
            return json_decode(json_encode($proposals),true);
        }

        /**
         * [This method is used for proposal listing] 
         * @param [Integer]$user_id[Used for user id]
         * @param [Integer]$projects_id[Used for project id]
         * @param [Integer]$page[Used for paging]
         * @param [Sort]$sort[Used for sorting]
         * @param [Filter]$filter[Used for filtering]
         * @param [Search]$search[Used for searching]
         * @return Data Response
         */

        public static function proposal_listing($user_id, $project_id,$page = 1, $sort = 'status', $filter = NULL, $search = NULL){
            if(empty($page)){ $page = 1; }

            if($page == 1){
                $project = self::project_header_detail($user_id,$project_id);
                $project['created']         = ___ago($project['created']);

                if($project['proposal_current_status'] == DEFAULT_YES_VALUE){
                    $project['proposal_status'] = trans('general.M0208');
                }else{
                    $project['proposal_status'] = trans('general.M0209');
                }
            }else{
                $project = ['id_project' => $project_id, 'created' => '', 'title' => '', 'created' => '', 'employment' => '', 'budget_type' => '', 'total_proposals' => '', 'proposal_current_status' => '', ];
            }

            $proposals = self::proposal_list($user_id,$project_id,'object');

            if(!empty($sort)) {
                $sort = explode(" ", ___decodefilter($sort));

                if(count($sort) == 2){
                    $proposals->orderBy($sort[0],$sort[1]);
                }
            }else{
                $proposals->orderBy('proposals.status','ASC');
            }

            if (!empty('filter')) {
                if($filter == 'tagged_listing'){
                    $proposals->havingRaw('is_tagged = "'.DEFAULT_YES_VALUE.'"');
                } 
            }


            if(!empty($search)){
                $proposals->whereRaw("(name like '%{$search}%' OR comments like '%{$search}%' OR quoted_price like '%{$search}%')");
            } 

            $proposals->limit(DEFAULT_PAGING_LIMIT);
            $proposals->offset(($page - 1)*DEFAULT_PAGING_LIMIT);

            $project['proposals']   = json_decode(json_encode($proposals->get()),true);

            array_walk($project['proposals'],function(&$item) use($project){
                $item['created']        = ___ago($item['created']);
                if($project['employment'] == 'fulltime' && 0){
                    $expected_salary = \Models\Employers::findById(\Auth::user()->id_user,['expected_salary'])->expected_salary;
                    $item['quoted_price'] = (!empty($expected_salary)) ? $expected_salary : '';
                }else{
                    $item['quoted_price']   = (!empty($item['quoted_price'])) ? $item['quoted_price'] :  '';
                }
                $item['price_unit']       = "$";
                $item['file_url']       = asset($item['file_url']);
            });

            return $project;
        }

        /**
         * [This method is used for proposal in detail] 
         * @param [Integer]$user_id[Used for user id]
         * @param [Integer]$projects_id[Used for project id]
         * @return Data Response
         */

        public static function proposal_detail___($user_id,$project_id){
            
            $table_projects = DB::table('projects as projects');
            $prefix = DB::getTablePrefix();

            $keys = [
                'projects.id_project',
                'projects.title',
                'projects.created',
                'projects.created',
                'projects.employment',
                'projects.budget_type',
                DB::Raw("COUNT({$prefix}proposals.id_proposal) as total_proposals"),
                DB::Raw("
                    IF(
                        (
                            SELECT COUNT(proposal.id_proposal) FROM {$prefix}talent_proposals as proposal 
                            WHERE proposal.project_id = {$prefix}proposals.project_id
                            AND proposal.status = 'accepted'
                        ),
                        '".DEFAULT_YES_VALUE."',
                        '".DEFAULT_NO_VALUE."'
                    ) as proposal_current_status
                "),
            ]; 

            $table_projects->select($keys);
            $table_projects->leftJoin('talent_proposals as proposals','proposals.project_id','=','projects.id_project');
            $table_projects->where('projects.user_id','=',$user_id);
            $table_projects->where('projects.id_project','=',$project_id);
            $table_projects->groupBy(['projects.id_project']);

            $project  = (array) $table_projects->get()->first();
            
            if(!empty($project)){
                $table_proposals = DB::table('talent_proposals as proposals');
                $proposals_keys = [
                    'proposals.id_proposal',
                    'proposals.project_id',
                    'proposals.user_id',
                    'proposals.submission_fee',
                    'proposals.quoted_price',
                    'proposals.comments',
                    'proposals.status',
                    'proposals.created',
                    'files.id_file',
                    'files.filename',
                    DB::Raw("CONCAT({$prefix}files.folder,'',{$prefix}files.filename) as file_url"),
                    'files.size',
                    'user_profile.filename as picture',
                    DB::Raw("CONCAT({$prefix}users.first_name,' ',{$prefix}users.last_name) as name"),
                    DB::Raw("'0' as review"),
                    DB::Raw("'0.0' as ratings"),
                    DB::Raw("IF({$prefix}tagged.id_tagged IS NOT NULL,'".DEFAULT_YES_VALUE."','".DEFAULT_NO_VALUE."') as is_tagged"),
                ];

                $project['proposals'] = [];
                $project['proposals'] = json_decode(
                    json_encode(
                        $table_proposals
                        ->leftjoin('files as files', function($q){
                                $q->on('files.record_id','=','proposals.id_proposal');
                                $q->where('files.type','=','proposal');
                            })
                        ->leftJoin('tagged_proposals as tagged',function($leftjoin) use($user_id){
                            $leftjoin->on('tagged.proposal_id','=','proposals.id_proposal');
                            $leftjoin->on('tagged.employer_id','=',DB::Raw("'{$user_id}'"));
                        })
                        ->leftjoin('users as users','proposals.user_id','=','users.id_user')
                        ->leftjoin('files as user_profile',function($q){
                            $q->on('user_profile.user_id','=','users.id_user');
                            $q->where('files.type','=','profile');
                        })
                        ->where('project_id','=',$project_id)
                        ->select($proposals_keys)
                        ->orderByRaw("{$prefix}proposals.status ASC")
                        ->groupBy(['user_id'])
                        ->get()
                    ),
                    true
                );
                
                $project['created'] =  ___ago($project['created']);
                array_walk($project['proposals'],function(&$item) use($project){
                    $item['created']        = ___ago($item['created']);
                    if($project['employment'] == 'fulltime'){
                        $expected_salary = \Models\Employers::findById(\Auth::user()->id_user,['expected_salary'])->expected_salary;
                        $item['quoted_price'] = (!empty($expected_salary)) ? PRICE_UNIT.$expected_salary : '';
                    }else{
                        $item['quoted_price']   = (!empty($item['quoted_price'])) ? PRICE_UNIT.$item['quoted_price'] :  '';
                    }
                    $item['file_url']       = url($item['file_url']);
                });
                if($project['proposal_current_status'] == DEFAULT_YES_VALUE){
                    $project['proposal_status'] = trans('general.M0208');
                }else{
                    $project['proposal_status'] = trans('general.M0209');
                }
                return $project;
            }else{
                return [];
            }
        }

        /**
         * [This method is used for proposal acceptance] 
         * @param [Integer]$user_id[Used for user id]
         * @param [Integer]$projects_id[Used for project id]
         * @param [Integer]$proposal_id[Used for proposal id]
         * @return Data Response
         */

        public static function accept_proposal($user_id,$project_id,$proposal_id){
            $table_projects = DB::table('talent_proposals as proposals');
            $prefix = DB::getTablePrefix();

            $isAlreadyProposalAccepted = json_decode(
                json_encode(
                    $table_projects->select([
                        'projects.user_id',
                        'proposals.status'
                    ])
                    ->leftJoin('projects as projects','projects.id_project','=','proposals.project_id')
                    ->where('projects.user_id',$user_id)
                    ->where('project_id',$project_id)
                    ->where('id_proposal',$proposal_id)
                    ->where('proposals.status','!=','applied')
                    ->get()->first()
                ),
                true
            );
            
            if(empty($isAlreadyProposalAccepted)){

                $proposal_detail    = self::get_proposal($proposal_id,['user_id','from_time','to_time']);
                $project_id         = $proposal_detail['project_id'];
                $project_details    = \Models\Projects::findById($project_id,['user_id as company_id','employment','startdate','enddate']);

                $begin              = new \DateTime($project_details['startdate']);
                $endDate            = date('Y-m-d', strtotime("+1 day", strtotime($project_details['enddate']))); 

                $end = new \DateTime( $endDate );
                $repeat_type = '1 day';
                
                $interval = \DateInterval::createFromDateString($repeat_type);
                $period = new \DatePeriod($begin, $interval, $end);

                $table_talent_availability = DB::table('talent_availability');
                $max_repeat_group = (int)$table_talent_availability->max('repeat_group')+1;

                foreach ( $period as $dt ){
                    $data[] = [
                        'user_id' => $proposal_detail['user_id'],
                        'availability_type' => 'available',
                        'availability_date' => $dt->format( "Y-m-d" ),
                        'from_time' => $proposal_detail['from_time'],
                        'to_time' => $proposal_detail['to_time'],
                        'repeat' => 'daily',
                        'deadline' => $project_details['enddate'],
                        'repeat_group' => $max_repeat_group,
                        'created' => date('Y-m-d H:i:s'),
                        'updated' => date('Y-m-d H:i:s'),
                    ];
                }

                if(!empty($data)){
                    $isInserted = \Models\Talents::setTalentAvailability($proposal_detail['user_id'], $max_repeat_group, $data, NULL, $project_details['startdate'], $project_details['enddate'], 'available');
                }

                if(!empty($proposal_detail)){
                    $table_proposals = DB::table('talent_proposals as proposals');
                    $table_proposals->where('project_id',$project_id);
                    $table_proposals->where('id_proposal',$proposal_id);

                    $isUpdated = $table_proposals->update([
                        'status' => 'accepted',
                        'updated' => date('Y-m-d H:i:s')
                    ]);

                    $isAwarded = \Models\Projects::where('id_project',$project_id)->update(['awarded' => 'yes', 'updated' => date('Y-m-d H:i:s')]);

                    $table_talent_proposals = DB::table('talent_proposals as proposals');
                    $table_talent_proposals->where('project_id',$project_id);
                    $table_talent_proposals->where('id_proposal','!=',$proposal_id); 
                    
                    $isRejected = $table_talent_proposals->update([
                        'status' => 'rejected',
                        'updated' => date('Y-m-d H:i:s')
                    ]);

                    $isDeclinedChatRequest = \Models\ChatRequests::where('sender_id','!=',$proposal_detail['user_id'])
                    ->where('project_id',$project_id)
                    ->update([
                        'chat_initiated' => NULL,
                        'request_status' => 'pending',
                        'updated' => date('Y-m-d H:i:s')
                    ]);

                    $isNotified = \Models\Notifications::notify(
                        $proposal_detail['user_id'],
                        $user_id,
                        'JOB_ACCEPTED_BY_EMPLOYER',
                        json_encode([
                            "user_id" => (string) $user_id,
                            "project_id" => (string) $project_id
                        ])
                    );

                    return [
                        'status' => true,
                        'message' => 'M0211'
                    ];
                }else{
                    return [
                        'status' => false,
                        'message' => 'M0121'
                    ];
                }
            }else{
                if($isAlreadyProposalAccepted['status'] == 'accepted'){
                    return [
                        'status' => false,
                        'message' => 'M0210'
                    ];
                }else if($isAlreadyProposalAccepted['status'] == 'rejected'){
                    return [
                        'status' => false,
                        'message' => 'M0212'
                    ];
                }
            }
        }

        /**
         * [This method is used for declining proposal] 
         * @param [Integer]$user_id[Used for user id]
         * @param [Integer]$projects_id[Used for project id]
         * @param [Integer]$proposal_id[Used for proposal id]
         * @return Data Response
         */

        public static function decline_proposal($user_id,$project_id,$proposal_id){
            $table_projects = DB::table('talent_proposals as proposals');
            $prefix = DB::getTablePrefix();

            $isAlreadyProposalDecline = json_decode(json_encode($table_projects->select(['projects.user_id','proposals.status'])->leftJoin('projects as projects','projects.id_project','=','proposals.project_id')->where('projects.user_id',$user_id)->where('project_id',$project_id)->where('id_proposal',$proposal_id)->where('proposals.status','!=','applied')->get()->first()),true);
            
            if(empty($isAlreadyProposalDecline)){
                $proposal_detail = self::get_proposal($proposal_id,['user_id']);

                if(!empty($proposal_detail)){
                    $table_projects = DB::table('talent_proposals as proposals');
                    $table_projects->where('project_id',$project_id);
                    $table_projects->where('id_proposal',$proposal_id);

                    $isUpdated = $table_projects->update([
                        'status' => 'rejected',
                        'updated' => date('Y-m-d H:i:s')
                    ]);

                    $isDeclinedChatRequest = \Models\ChatRequests::where('receiver_id',$user_id)->where('project_id',$project_id)->update(['chat_initiated' => NULL,'request_status' => 'pending','updated' => date('Y-m-d H:i:s')]);

                    $isNotified = \Models\Notifications::notify(
                        $proposal_detail['user_id'],
                        $user_id,
                        'JOB_REJECTED_BY_EMPLOYER',
                        json_encode([
                            "user_id" => (string) $user_id,
                            "project_id" => (string) $project_id
                        ])
                    );
                    
                    \Models\Notifications::where('notification','JOB_UPDATED_BY_EMPLOYER')->where('notify',$proposal_detail['user_id'])->where('notified_by',$user_id)->delete();

                    return [
                        'status' => true,
                        'message' => 'M0216'
                    ];
                }else{
                    return [
                        'status' => false,
                        'message' => 'M0121'
                    ];
                }
            }else{
                if($isAlreadyProposalDecline['status'] == 'accepted'){
                    return [
                        'status' => false,
                        'message' => 'M0210'
                    ];
                }else if($isAlreadyProposalDecline['status'] == 'rejected'){
                    return [
                        'status' => false,
                        'message' => 'M0212'
                    ];
                }
            }
        }

        /**
         * [This method is used for tag proposal] 
         * @param [Integer]$employer_id[Used for employer id]
         * @param [Integer]$proposal_id[Used for proposal id]
         * @return Data Response
         */

        public static function tag_proposal($employer_id,$proposal_id){
            $table_tagged_proposals = DB::table('tagged_proposals');
            $prefix = DB::getTablePrefix();

            $isAlreadyProposalTagged = $table_tagged_proposals->where('tagged_proposals.employer_id',$employer_id)->where('tagged_proposals.proposal_id',$proposal_id)->get()->count();
            
            if(empty($isAlreadyProposalTagged)){
                $table_tagged_proposals = DB::table('tagged_proposals');
                
                $isTagged = $table_tagged_proposals->insert([
                    'employer_id'   => $employer_id,
                    'proposal_id'   => $proposal_id,
                    'created'       => date('Y-m-d H:i:s'),
                    'updated'       => date('Y-m-d H:i:s')
                ]);

                if(!empty($isTagged)){
                    return [
                        'status' => true,
                        'message' => 'M0315'
                    ];
                }else{
                    return [
                        'status' => true,
                        'message' => 'M0316'
                    ];
                }
            }else{
                $table_tagged_proposals = DB::table('tagged_proposals');
                $isDeleted = $table_tagged_proposals->where('tagged_proposals.employer_id',$employer_id)->where('tagged_proposals.proposal_id',$proposal_id)->delete();
 
                return [
                    'status' => true,
                    'message' => 'M0316'
                ];
            }
        }

        /**
         * [This method is used for employer sidebar widget in details] 
         * @param [Integer]$employer_id[Used for employer id]
         * @return Json response
         */

        public static function employer_sidebar_widget_details($employer_id){
            $table_users = DB::table('users as users');
            $prefix = DB::getTablePrefix();

            $table_users->select([
                DB::Raw("CONCAT({$prefix}users.first_name,' ',{$prefix}users.last_name) as name"),
                'users.company_name',
                \DB::raw("(SELECT COUNT(*) FROM {$prefix}reviews AS reviews WHERE reviews.receiver_id = {$prefix}users.id_user) as review"),
                \DB::raw("(SELECT IFNULL(ROUND(AVG(review_average), 1), '0.0') FROM {$prefix}reviews AS reviews WHERE reviews.receiver_id = {$prefix}users.id_user) as rating"),
                DB::Raw("COUNT({$prefix}projects.id_project) as total_posted_jobs"),
                DB::Raw("COUNT({$prefix}proposals.user_id) as total_hirings"),
                DB::Raw("'0' as total_paid"),
                DB::Raw("`CONVERT_PRICE`((SELECT SUM( transaction_subtotal )
                    FROM {$prefix}transactions
                    WHERE  `transaction_user_id` = {$prefix}projects.`user_id`
                    AND  `transaction_status` =  'confirmed'
                    AND  `transaction_type` =  'debit'),'".\Cache::get('default_currency')."', '".request()->currency."') as total_paid"
                )
            ]);

            $table_users->leftJoin('projects as projects','projects.user_id','=','users.id_user');
            $table_users->leftJoin('talent_proposals as proposals',function($leftjoin){
                $leftjoin->on('proposals.project_id','=','projects.id_project');
                $leftjoin->on('proposals.status','=',DB::Raw('"accepted"'));
            });


            $table_users->where('users.id_user',$employer_id);
            $table_users->groupBy(['users.id_user']);

            return json_decode(json_encode($table_users->get()->first()),true);

        }

        /**
         * [This method is used for employer's other jobs] 
         * @param [Integer]$current_job_id[Used for current job]
         * @param [Integer]$employer_id[Used for employer id]
         * @param [Varchar]$user[Used for user]
         * @return Data Response
         */

        public static function employer_other_jobs($current_job_id,$employer_id,$user=[]){
            $table_projects = DB::table('projects as projects');
            $prefix         = DB::getTablePrefix();
            $current_date   = date('Y-m-d');
            $language       = \App::getLocale();
            $where = " 
                1 AND 
                (
                    (
                        {$prefix}projects.startdate >= '{$current_date}' 
                    )
                    OR 
                    (
                        {$prefix}projects.project_status = 'open'
                        AND 
                        {$prefix}projects.employment = 'fulltime'
                    )
                )
            ";

            $table_projects->select([
                'projects.id_project',
                'projects.title',
                \DB::Raw("IF(({$prefix}industry.{$language} != ''),{$prefix}industry.`{$language}`, {$prefix}industry.`en`) as industry_name"),
                #'projects.price',
                \DB::Raw('`CONVERT_PRICE`('.$prefix.'projects.price, '.$prefix.'projects.price_unit, "'.request()->currency.'") AS price'),
                \DB::Raw('`CONVERT_PRICE`('.$prefix.'projects.price_max, '.$prefix.'projects.price_unit, "'.request()->currency.'") AS price_max'),
                'projects.price_unit',
                'projects.price_type',
                'projects.budget_type',
                'projects.price_unit',
                'projects.created',
                'projects.expertise',
                'projects.employment',
                'users.company_name',
                'users.id_user as company_id',
                \DB::Raw("DATE({$prefix}projects.startdate) as startdate"),
                \DB::Raw("DATE({$prefix}projects.enddate) as enddate"),
                (!empty($user))?\DB::Raw("IF({$prefix}saved_jobs.id_saved IS NOT NULL,'".DEFAULT_YES_VALUE."','".DEFAULT_NO_VALUE."') as is_saved"):\DB::Raw("'".DEFAULT_NO_VALUE."' as is_saved"),
                
            ]);

            $table_projects->leftJoin('industries as industry','industry.id_industry','=','projects.industry');
            $table_projects->leftJoin('users as users','users.id_user','=','projects.user_id');

            if(!empty($user)){
                $table_projects->addSelect(\DB::Raw("{$prefix}proposals_listing.status as job_listing_status"));
                $table_projects->leftjoin('talent_proposals as proposals_listing',function($q) use($user){
                    $q->on('proposals_listing.project_id','=','projects.id_project');
                    /*$q->on('proposals_listing.user_id','=',\DB::Raw($user->id_user));*/
                });
                $table_projects->havingRaw("job_listing_status IS NULL");

                $table_projects->leftJoin('saved_jobs as saved_jobs',function($leftjoin) use($user){
                    $leftjoin->on('saved_jobs.job_id','=','projects.id_project');
                    $leftjoin->where('saved_jobs.user_id','=',$user->id_user);
                });
            }

            $table_projects->where("projects.id_project","!=",$current_job_id);
            $table_projects->where("projects.user_id",$employer_id);
            $table_projects->whereRaw($where);
            
            $table_projects->orderByRaw("RAND()");

            $table_projects->offset(0);
            $table_projects->limit(EMPLOYER_OTHER_JOBS_LIMIT);

            $projects = json_decode(json_encode($table_projects->get()),true);

            array_walk($projects, function(&$item){
                $item['created']        = ___ago($item['created']);
                $item['expertise']      = ucfirst($item['expertise']);
                $item['company_logo']   = get_file_url(\Models\Employers::get_file(sprintf(" type = 'profile' AND user_id = %s",$item['company_id']),'single',['filename','folder']));
                
                if($item['employment'] !== 'fulltime'){
                    $item['timeline']   = ___date_difference($item['startdate'],$item['enddate']);
                    $item['price_type'] = $item['price_type'];
                }else{
                    $item['timeline']   = trans('website.W0039');
                    $item['price_type'] = trans('website.W0039');
                }

                $item['job_type']       = employment_types('post_job',$item['employment']);
                $item['price_unit']     = \Cache::get('currencies')[request()->currency];
                $item['price']          = ___format($item['price'],true,false);
                $item['price_max']      = ___formatblank($item['price_max'],true,false);

            });

            return $projects;
        }

        /**
         * [This method is used for similar jobs] 
         * @param [Integer]$job_id[Used for job id]
         * @param [Integer]$industry_id[Used for Industry id]
         * @param [Varchar]$user[Used for user]
         * @param [Integer]$projects_id[Used for project id]
         * @return Boolean
         */

        public static function similar_jobs($job_id,$industry_id,$user=[],$projects_id=""){
            $table_projects = DB::table('projects as projects');
            $prefix         = DB::getTablePrefix();
            $current_date   = date('Y-m-d');
            $language       = \App::getLocale();
            $where = " 
                1 AND 
                (
                    (
                        {$prefix}projects.startdate >= '{$current_date}' 
                    )
                    OR 
                    (
                        {$prefix}projects.project_status = 'open'
                        AND 
                        {$prefix}projects.employment = 'fulltime'
                    )
                )
            ";

            $table_projects->select([
                'projects.id_project',
                'projects.title',
                \DB::Raw("IF(({$prefix}industry.{$language} != ''),{$prefix}industry.`{$language}`, {$prefix}industry.`en`) as industry_name"),
                #'projects.price',
                \DB::Raw('`CONVERT_PRICE`('.$prefix.'projects.price, '.$prefix.'projects.price_unit, "'.request()->currency.'") AS price'),
                \DB::Raw('`CONVERT_PRICE`('.$prefix.'projects.price_max, '.$prefix.'projects.price_unit, "'.request()->currency.'") AS price_max'),
                'projects.price_unit',
                'projects.price_type',
                'projects.budget_type',
                'projects.created',
                'projects.price_unit',
                'projects.expertise',
                'projects.employment',
                'users.company_name',
                'users.id_user as company_id',
                \DB::Raw("DATE({$prefix}projects.startdate) as startdate"),
                \DB::Raw("DATE({$prefix}projects.enddate) as enddate"),
                (!empty($user))?\DB::Raw("IF({$prefix}saved_jobs.id_saved IS NOT NULL,'".DEFAULT_YES_VALUE."','".DEFAULT_NO_VALUE."') as is_saved"):\DB::Raw("'".DEFAULT_NO_VALUE."' as is_saved"),
            ]);

            $table_projects->leftJoin('industries as industry','industry.id_industry','=','projects.industry');
            $table_projects->leftJoin('users as users','users.id_user','=','projects.user_id');
            
            if(!empty($user)){
                $table_projects->leftJoin('saved_jobs as saved_jobs',function($leftjoin) use($user){
                    $leftjoin->on('saved_jobs.job_id','=','projects.id_project');
                    $leftjoin->where('saved_jobs.user_id','=',$user->id_user);
                });

                $table_projects->addSelect(\DB::Raw("{$prefix}proposals_listing.status as job_listing_status"));
                $table_projects->leftjoin('talent_proposals as proposals_listing',function($q) use($user){
                    $q->on('proposals_listing.project_id','=','projects.id_project');
                    /*$q->on('proposals_listing.user_id','=',\DB::Raw($user->id_user));*/
                });
                $table_projects->havingRaw("job_listing_status IS NULL");
            }

            $table_projects->where("projects.id_project","!=",$job_id);
            $table_projects->where("projects.industry",$industry_id);
            $table_projects->whereRaw($where);
            $table_projects->whereNotIn("projects.id_project", $projects_id);
            $table_projects->orderByRaw("{$prefix}projects.id_project DESC");

            $table_projects->offset(0);
            $table_projects->limit(SIMILAR_JOBS_LIMIT);

            $projects = json_decode(json_encode($table_projects->get()),true);

            array_walk($projects, function(&$item){
                $item['created']        = ___ago($item['created']);
                $item['expertise']      = ucfirst($item['expertise']);
                $item['company_logo']   = get_file_url(\Models\Employers::get_file(sprintf(" type = 'profile' AND user_id = %s",$item['company_id']),'single',['filename','folder']));

                if($item['employment'] !== 'fulltime'){
                    $item['timeline']   = ___date_difference($item['startdate'],$item['enddate']);
                    $item['price_type'] = $item['price_type'];
                }else{
                    $item['timeline']   = trans('website.W0039');
                    $item['price_type'] = trans('website.W0039');
                }

                $item['job_type']       = employment_types('post_job',$item['employment']);
                $item['price_unit']     = \Cache::get('currencies')[request()->currency];
                $item['price']          = ___format($item['price'],true,false);
                $item['price_max']      = ___formatblank($item['price_max'],true,false);
            });

            unset($projects['startdate']);
            unset($projects['enddate']);

            return $projects;
        }

        /**
         * [This method is used to update profile percentage] 
         * @param [Varchar]$user_details [Used for user's detail]
         * @return Boolean
         */

        public static function update_profile_percentage($user_details){
            $percentage = [];

            /*CALCULETING STEP ONE PERCENTAGE*/                
            if($user_details['company_profile'] == 'company'){
                $step_one_percentage = array_intersect_key(
                    $user_details,
                    array_flip(
                        array(
                            'company_profile',
                            'company_name',
                            'contact_person_name',
                            'company_website',
                            'company_work_field',
                            /*'certificates',*/
                            'company_biography'
                        )
                    )
                );
                /*$step_one_percentage['certificates'] = !empty($step_one_percentage['certificates']) ? true : false;*/
                $percentage['percentage_step_one'] = (count(array_filter($step_one_percentage))*EMPLOYER_STEP_ONE_COMPANY_PROFILE_PERCENTAGE_WEIGHTAGE);
            }else{
                $step_one_percentage = array_intersect_key(
                    $user_details,
                    array_flip(
                        array(
                            'company_profile',
                            'company_name',
                            'company_work_field',
                            /*'certificates',*/
                        )
                    )
                );
                /*$step_one_percentage['certificates'] = !empty($step_one_percentage['certificates']) ? true : false;*/
                $percentage['percentage_step_one'] = (count(array_filter($step_one_percentage))*EMPLOYER_STEP_ONE_INDIVIDUAL_PROFILE_PERCENTAGE_WEIGHTAGE);
            }

            
            $step_two_percentage = array_intersect_key(
                $user_details,
                array_flip(
                    array(
                        'first_name',
                        'last_name',
                        'email',
                        'country_code',
                        'mobile',
                        'address',
                        'country',
                        'state',
                        'postal_code',
                    )
                )
            );
            
            /*CALCULETING STEP TWO PERCENTAGE*/
            if($user_details['company_profile'] == 'company'){                
                $percentage['percentage_step_two'] = (count(array_filter($step_two_percentage))*EMPLOYER_STEP_TWO_COMPANY_PROFILE_PERCENTAGE_WEIGHTAGE);
            }else{
                $percentage['percentage_step_two'] = (count(array_filter($step_two_percentage))*EMPLOYER_STEP_TWO_INDIVIDUAL_PROFILE_PERCENTAGE_WEIGHTAGE);
            }

            $step_three_percentage = array_intersect_key(
                $user_details,
                array_flip(
                    array(
                        'facebook_id',
                        'twitter_id',
                        'linkedin_id',
                        'instagram_id',
                        'googleplus_id',
                        'is_mobile_verified',
                    )
                )
            );

            if($step_three_percentage['is_mobile_verified'] !== DEFAULT_YES_VALUE){
                $step_three_percentage['is_mobile_verified'] = "";
            }

            /*CALCULETING STEP THREE PERCENTAGE*/                
            $percentage['percentage_step_three'] = (count(array_filter($step_three_percentage))*EMPLOYER_STEP_THREE_PROFILE_PERCENTAGE_WEIGHTAGE);
            
            self::change($user_details['id_user'],$percentage);
        }

        /**
         * [This method is used to save user's] 
         * @param [Integer]$user_id [Used for user id]
         * @param [Integer]$talent_id [Used for talent id]
         * @return Boolean
         */

        public static function save_talent($user_id, $talent_id){
            $table_saved_talent = DB::table('saved_talent');

            $table_saved_talent->where(['user_id' => $user_id, 'talent_id' => $talent_id]);

            if(!empty($table_saved_talent->get()->count())){
                $isSaved = $table_saved_talent->delete();

                if(!empty($isSaved)){
                    $result = [
                        'action' => 'deleted_saved_talent', /*dont change*/
                        'status' => true
                    ];
                }else{
                    $result = [
                        'action' => 'failed',
                        'status' => false
                    ];
                } 
            }else{
                $data = [
                    "user_id"   => $user_id,
                    "talent_id" => $talent_id,
                    "created"   => date('Y-m-d H:i:s'),
                    "updated"   => date('Y-m-d H:i:s')
                ]; 
                
                $isSaved = $table_saved_talent->insertGetId($data);


                if(!empty($isSaved)){
                    $result = [
                        'action' => 'saved_talent', /*dont change*/
                        'status' => true
                    ];
                }else{
                    $result = [
                        'action' => 'failed',
                        'status' => false
                    ];
                } 
            }

            return $result;
        }

        /**
         * [This method is used to save user's] 
         * @param [Integer]$user_id [Used for user id]
         * @param [Integer]$talent_id [Used for talent id]
         * @return Boolean
         */

        public static function is_talent_saved($user_id, $talent_id){
            $table_saved_talent = DB::table('saved_talent');

            $table_saved_talent->where(['user_id' => $user_id, 'talent_id' => $talent_id]);

            if(!empty($table_saved_talent->get()->count())){
                return true; 
            }else{
                return false;
            }
        }

        /**
         * [This method is used to get chat list] 
         * @param [Integer]$user_id [Used for user id]
         * @param [Search]$search[Used for searching]
         * @param [Integer]$talent_id [Used for talent id]
         * @return Data Response
         */

        public static function get_my_chat_list($user_id,$search = NULL, $talent_id = NULL){
            $table_chat_requests    = DB::table('chat_requests');
            $prefix                 = DB::getTablePrefix();
            $base_url               = ___image_base_url();
            
            $table_chat_requests->select([
                "id_chat_request",
                \DB::Raw("LPAD({$prefix}chat_requests.project_id, ".JOBID_PREFIX.", '0') as project_id"),
                \DB::Raw("{$user_id} as sender_id"),
                'employers.id_user as receiver_id',
                \DB::Raw("TRIM(IF({$prefix}employers.last_name IS NULL, {$prefix}employers.first_name, CONCAT({$prefix}employers.first_name,' ',{$prefix}employers.last_name))) as receiver_name"),
                'employers.email as receiver_email',
                'employers.chat_status as status',
                \DB::Raw("
                    IF(
                        ({$prefix}chat_requests.is_reported = '".DEFAULT_NO_VALUE."'),
                        IF(
                            ({$prefix}chat_requests.request_status = 'pending' AND {$prefix}chat_requests.chat_initiated != 'talent'),
                            'accepted',
                            {$prefix}chat_requests.request_status
                        ),
                        'reported'
                    ) as request_status"
                ),
                \DB::Raw("
                    IF(
                        {$prefix}files.filename IS NOT NULL,
                        CONCAT('{$base_url}','/',{$prefix}files.folder,{$prefix}files.filename),
                        CONCAT('{$base_url}','/','images/','".DEFAULT_AVATAR_IMAGE."')
                    ) as receiver_picture
                "),
                \DB::Raw("
                    (
                        IFNULL(
                            (
                                SELECT COUNT(id_chat) FROM {$prefix}chat 
                                WHERE (
                                    (receiver_id = {$user_id} AND sender_id = {$prefix}employers.id_user)
                                    AND 
                                    seen_status != 'read'
                                    AND 
                                    delete_receiver_status = 'active'
                                ) 
                                AND group_id = id_chat_request
                            ),
                            0
                        )
                    ) as unread_messages
                "),
                \DB::Raw("(
                        SELECT message FROM {$prefix}chat 
                        WHERE (
                            ({$prefix}chat.sender_id = {$user_id} AND receiver_id = {$prefix}employers.id_user)
                            OR 
                            ({$prefix}chat.sender_id = {$prefix}employers.id_user AND  receiver_id = {$user_id})
                        )
                        AND delete_receiver_status = 'active'
                        AND group_id = id_chat_request
                        ORDER BY {$prefix}chat.id_chat DESC
                        LIMIT 0,1
                    ) as last_message
                "),
                \DB::Raw("(
                        SELECT message_type FROM {$prefix}chat 
                        WHERE (
                            ({$prefix}chat.sender_id = {$user_id} AND receiver_id = {$prefix}employers.id_user)
                            OR 
                            ({$prefix}chat.sender_id = {$prefix}employers.id_user AND  receiver_id = {$user_id})
                        )
                        AND delete_receiver_status = 'active'
                        AND group_id = id_chat_request
                        ORDER BY {$prefix}chat.id_chat DESC
                        LIMIT 0,1
                    ) as last_message_type
                "),
                \DB::Raw("
                    (
                        IFNULL(
                            (
                                SELECT created FROM {$prefix}chat 
                                WHERE (
                                    ({$prefix}chat.sender_id = {$user_id} AND receiver_id = {$prefix}employers.id_user)
                                    OR 
                                    ({$prefix}chat.sender_id = {$prefix}employers.id_user AND  receiver_id = {$user_id})
                                )
                                AND group_id = id_chat_request
                                ORDER BY {$prefix}chat.id_chat DESC
                                LIMIT 0,1
                            ),
                            {$prefix}chat_requests.created
                        )
                    ) as timestamp
                "),
                \DB::Raw("
                    (
                        IFNULL(
                            (
                                SELECT created FROM {$prefix}chat 
                                WHERE (
                                    ({$prefix}chat.sender_id = {$user_id} AND receiver_id = {$prefix}employers.id_user)
                                    OR 
                                    ({$prefix}chat.sender_id = {$prefix}employers.id_user AND  receiver_id = {$user_id})
                                )
                                AND group_id = id_chat_request
                                ORDER BY {$prefix}chat.id_chat DESC
                                LIMIT 0,1
                            ),
                            {$prefix}chat_requests.created
                        )
                    ) as requested_date
                ")
            ]);

            $table_chat_requests->leftJoin('users as employers','employers.id_user','=','chat_requests.sender_id');
            $table_chat_requests->leftJoin('files as files',function($leftjoin){
                $leftjoin->on('files.user_id','=','employers.id_user');
                $leftjoin->on('files.type','=',\DB::Raw('"profile"'));
            });
            
            if(!empty($search)){
                $table_chat_requests->having("receiver_name","like","%{$search}%");
            }

            $table_chat_requests->where("receiver_id",$user_id);
            $table_chat_requests->where("is_terminated","no");
            $table_chat_requests->where("employers.status",'active');
            $table_chat_requests->where("request_status",'!=','rejected');
            $table_chat_requests->whereIn("chat_initiated",['talent','employer','employer-accepted']);
            # $table_chat_requests->groupBy(["employers.id_user"]);
            $table_chat_requests->orderBy('timestamp','DESC');

            if(!empty($talent_id)){
                $table_chat_requests->where('sender_id',$talent_id);
                $result = json_decode(json_encode($table_chat_requests->get()),true);
            }else{
                $result = json_decode(json_encode($table_chat_requests->get()),true);
            }

            if(!empty($result)){
                array_walk($result, function(&$item) use($user_id){
                    $item['receiver_email'] = ___e($item['receiver_email']);
                    $item['ago']            = ___agoday($item['timestamp']);
                    $item['fulltime']       = ___d($item['timestamp']);
                    $item['timestamp']      = (string) strtotime($item['timestamp']);
                    $item['last_message_code']  = "";
                    
                    if(empty($item['last_message'])){
                        if($item['request_status'] != 'accepted'){
                            $item['last_message'] = trans('general.M0474');
                            $item['last_message_code'] = 'M0474';
                        }
                    }elseif($item['last_message_type'] == 'image'){
                        $item['last_message'] = trans('website.W0423');
                    }

                    $item['profile_link']   = url(sprintf('%s/find-talents/profile?talent_id=%s',EMPLOYER_ROLE_TYPE,___encrypt($item['receiver_id'])));
                });
            }

            return $result;
        }

        /**
         * [This method is used to get proposal] 
         * @param [Integer]$proposal_id[Used for proposal id]
         * @param [Varchar]$key[Used for Keys]
         * @return Json Response
         */

        public static function get_proposal($proposal_id,$keys = []){
            $prefix          = DB::getTablePrefix();
            $default_currency = \Cache::get('default_currency');

            $table_proposals = DB::table('talent_proposals')
            ->select([
                'talent_proposals.id_proposal',
                'talent_proposals.project_id',
                'talent_proposals.user_id',
                'talent_proposals.price_unit',
                'talent_proposals.accept_escrow',
                'talent_proposals.pay_commision_percent',
                #'submission_fee',
                \DB::Raw("(ABS(HOUR(IFNULL(SEC_TO_TIME(SUM(TIME_TO_SEC({$prefix}talent_proposals.working_hours))),'00:00:00')))+ABS(MINUTE(IFNULL(SEC_TO_TIME(SUM(TIME_TO_SEC({$prefix}talent_proposals.working_hours))),'00:00:00'))/60)+ABS(SECOND(IFNULL(SEC_TO_TIME(SUM(TIME_TO_SEC({$prefix}talent_proposals.working_hours))),'00:00:00'))/3600)) as decimal_working_hours"),
                \DB::Raw('`CONVERT_PRICE`('.$prefix.'talent_proposals.submission_fee, '.$prefix.'talent_proposals.price_unit, "'.request()->currency.'") AS submission_fee'),
                \DB::Raw('`CONVERT_PRICE`('.$prefix.'talent_proposals.quoted_price, '.$prefix.'talent_proposals.price_unit, "'.request()->currency.'") AS quoted_price'),
                \DB::Raw('`CONVERT_PRICE`('.$prefix.'talent_proposals.submission_fee, '.$prefix.'talent_proposals.price_unit, "'.$default_currency.'") AS global_submission_fee'),
                \DB::Raw('`CONVERT_PRICE`('.$prefix.'talent_proposals.quoted_price, '.$prefix.'talent_proposals.price_unit, "'.$default_currency.'") AS global_quoted_price'),
                'talent_proposals.comments',
                'talent_proposals.type',
                'talent_proposals.from_time',
                'talent_proposals.to_time',
                'talent_proposals.status',
                'talent_proposals.created',
                'talent_proposals.updated',
                \DB::Raw("TRIM(IF({$prefix}talent.last_name IS NULL, {$prefix}talent.first_name, CONCAT({$prefix}talent.first_name,' ',{$prefix}talent.last_name))) as talent_name")
            ])
            ->leftjoin('users as talent','talent.id_user','=','talent_proposals.user_id');

            /*if(!empty($keys)){
                $table_proposals->select($keys);                
            }*/

            $table_proposals->where('id_proposal',$proposal_id);
            
            return json_decode(json_encode($table_proposals->get()->first()),true);
        }

        /**
         * [This method is used to update job] 
         * @param [Integer]$project_id[Used for project id]
         * @param [Varchar]$data[Used for data]
         * @return Data Response
         */

        public static function update_job($project_id,$data){
            $table_projects = DB::table('projects');
            $table_projects->where('id_project',$project_id);
            
            return $table_projects->update($data);
        }

        /**
         * [This method is used to find premium user's] 
         * @param [Varchar]$user [Used for user]
         * @param [Fetch]$page[Used for fetching]
         * @param [Search]$search[Used for searching]
         * @param [Integer]$page[Used for paging]
         * @param [Sort]$sort[Used for sorting]
         * @param [Varchar]$key[Used for Keys]
         * @param [Integer]$limit[Used for limit]
         * @return Data Response
         */

        public static function find_premium_talents($user,$fetch = 'all',$search = "",$page = 0, $sort = 'users.name ASC',$keys = NULL, $limit = DEFAULT_PAGING_LIMIT){
            $offset             = 0;
            $table_talents      = DB::table('users as users');
            $prefix             = DB::getTablePrefix();
            $minimum_percentage = MINIMUM_PERCENTAGE_FOR_SEARCHING;
            $language           = \App::getLocale();
            if(empty($keys)){
                $keys = [
                    'users.id_user',
                    'users.type',
                    \DB::raw("CONCAT({$prefix}users.first_name,' ',{$prefix}users.last_name) as name"),
                    'users.gender',
                    'users.country',
                    'users.workrate',
                    \DB::Raw("IF(({$prefix}countries.{$language} != ''),{$prefix}countries.`{$language}`, {$prefix}countries.`en`) as country_name"),
                    \DB::Raw("IF(({$prefix}industries.{$language} != ''),{$prefix}industries.`{$language}`, {$prefix}industries.`en`) as industry_name"),
                    \DB::raw('"0" as job_completion'),
                    \DB::raw('"0" as availability_hours'),
                    \DB::raw('"'.PRICE_UNIT.'" as price_unit'),
                    'users.expertise',
                    \DB::raw("(SELECT GROUP_CONCAT(t.skill) FROM {$prefix}talent_skills as t WHERE t.user_id = {$prefix}users.id_user) as skills"),
                    \DB::Raw("IF(
                        {$prefix}saved_talent.id_saved IS NOT NULL,
                        '".DEFAULT_YES_VALUE."',
                        '".DEFAULT_NO_VALUE."'
                    ) as is_saved"),
                    \DB::raw('"0" as review'),
                    \DB::raw('"0.0" as rating'),
                ];
            }

            $table_talents->select($keys);
            $table_talents->leftJoin('files as files','files.user_id','=','users.id_user');

            $table_talents->leftJoin('industries as industries','industries.id_industry','=','users.industry');
            $table_talents->leftJoin('industries as subindustries','subindustries.id_industry','=','users.subindustry');
            $table_talents->leftJoin('city as city','city.id_city','=','users.city');
            $table_talents->leftJoin('countries as countries','countries.id_country','=','users.country');
            $table_talents->leftJoin('talent_skills as talent_skills','talent_skills.user_id','=','users.id_user');
            $table_talents->leftJoin('talent_interests as talent_interests','talent_interests.user_id','=','users.id_user');
            $table_talents->leftJoin('saved_talent as saved_talent',function($leftjoin) use($user){
                $leftjoin->on('saved_talent.talent_id','=','users.id_user');
                $leftjoin->on('saved_talent.user_id','=',DB::Raw($user->id_user));
            });

            $where ="
                IFNULL(
                    (
                        IFNULL({$prefix}users.percentage_default,0)+
                        IFNULL({$prefix}users.percentage_step_one,0)+
                        IFNULL({$prefix}users.percentage_step_two,0)+
                        IFNULL({$prefix}users.percentage_step_three,0)
                    ),0
                ) >= {$minimum_percentage}
                AND {$prefix}users.status = 'active'
                AND {$prefix}users.type = 'premium'
                AND {$prefix}files.type = 'cv'
                #AND {$prefix}users.agree = '".DEFAULT_YES_VALUE."'
                #AND {$prefix}users.agree_pricing = '".DEFAULT_YES_VALUE."'
                AND ({$prefix}users.first_name IS NOT NULL OR {$prefix}users.first_name != '')
            ";

            $table_talents->select($keys);

            if(empty($search)){
                $search = " 1 ";
            }

            $table_talents->whereRaw(sprintf("%s AND %s",$where,$search));

            if(!empty($sort)){
                $table_talents->orderByRaw("{$prefix}$sort");
            }else{
                $table_talents->orderByRaw("{$prefix}users.name ASC");
            }

            $table_talents->groupBy(['users.id_user']);

            $total = $table_talents->get()->count();

            if(!empty($page)){
                $table_talents->limit($limit);
                $table_talents->offset(($page - 1)*$limit);
            }

            $talents  = json_decode(json_encode($table_talents->get()),true);

            $total_filtered_result = $table_talents->get()->count();

            array_walk($talents, function(&$item){
                $picture = \Models\Talents::get_file(sprintf(" type = 'profile' AND user_id = %s",$item['id_user']),'single',['filename','folder']);

                if(!empty($picture)){
                    $item['picture'] = get_file_url($picture);
                }else{
                    $item['picture'] = "";
                }
            });

            return [
                'total' => $total,
                'result' => $talents,
                'total_filtered_result' => $total_filtered_result,
            ];
        }

        /**
         * [This method is used to get user location] 
         * @param [Integer]$user_id [Used for user id]
         * @param [type]$where[Used for where clause]
         * @return Data Response
         */

        public static function getUserLocation($id_user = array(), $where){
            $prefix     = DB::getTablePrefix();
            $language   = \App::getLocale();
            return DB::table('city')
            ->select(
                DB::raw("COUNT( ".$prefix."users.id_user ) AS num"),
                \DB::Raw("
                    IF(
                        ({$prefix}city.`{$language}` != ''),
                        {$prefix}city.`{$language}`,
                        {$prefix}city.`en`
                    ) as city_name"
                ),                
                'city.id_city')
            ->leftJoin('users as users',function($leftjoin)use($id_user){
                        $leftjoin->on('users.city','=','city.id_city');
                        $leftjoin->whereIn('users.id_user',$id_user);
            })
            ->whereRaw($where)
            ->groupBy('city.id_city')
            ->orderBy('city.city_order','ASC')
            ->get()
            ->toArray();
        }

        /**
         * [This method is used to get premium user's] 
         * @param [Varchar]$key[Used for Keys]
         * @return Data Response
         */ 

        public static function get_premium_talents($keys){
            $offset = 0;
            $table_talents = DB::table('users as users');
            $prefix = DB::getTablePrefix();
            $minimum_percentage = MINIMUM_PERCENTAGE_FOR_SEARCHING;

            $where ="
                (
                    (
                        ".$prefix."users.expected_salary >= ".PERMANENT_SALARY_LOW_FILTER."
                        AND
                        ".$prefix."users.expected_salary <= ".PERMANENT_SALARY_HIGH_FILTER."
                    )
                )
                AND ".$prefix."users.status = 'active'
                AND ".$prefix."users.type = 'premium'
            ";
            $table_talents->whereRaw($where);
            $table_talents->select($keys);

            if(empty($search)){
                $search = " 1 ";
            }

            $table_talents->groupBy(['users.id_user']);
            return $table_talents->get();
        }

        /**
         * [This method is used to getSubscriptionList] 
         * @param null
         * @return Data Response
         */

        public static function getSubscriptionList(){
            $prefix         = DB::getTablePrefix();
            $table_user = DB::table('users');

            $table_user->select([
                'users.id_user',
                \DB::Raw("CONCAT({$prefix}users.first_name, ' ', {$prefix}users.last_name) as name"),
                'users.email',
                \DB::raw("CONCAT(UCASE(LEFT({$prefix}users.is_subscribed, 1)),SUBSTRING({$prefix}users.is_subscribed, 2)) AS is_subscribed"),
            ]);
            $table_user->join('user_subscription','user_subscription.id_user','=','users.id_user');
            $table_user->where('users.type','=','employer');
            $table_user->groupBy(['users.id_user']);

            return $table_user->get();
        }

        /**
         * [This method is used to getSubscriptionDetail] 
         * @param [Integer]$user_id [Used for user id]
         * @return Data Response
         */

        public static function getSubscriptionDetail($id_user){
            $prefix     = DB::getTablePrefix();
            $table_user = DB::table('user_subscription');

            $table_user->select(
                'user_subscription.id_subscription',
                'user_subscription.price',
                'user_subscription.billingDayOfMonth',
                'user_subscription.nextBillAmount',
                \DB::Raw("DATE_FORMAT({$prefix}user_subscription.nextBillingDate, '%d-%m-%Y') as nextBillingDate"),
                'plan.name AS plan_name'
                );
            $table_user->leftJoin('plan','plan.id_plan','=','user_subscription.id_plan');
            $table_user->where('user_subscription.id_user','=',$id_user);

            return $table_user->get();
        }


        /**
         * [This method is used to send chat request] 
         * @param [Integer]$sender_id[Used for Sender id]
         * @param [Integer]$receiver_id[Used for Receiver id]
         * @param [Integer]$project_id[Used for Project id]
         * @param [Varchar]$is_employer_initiated [Used for employer initiated]
         * @return Data Response
         */ 

        public static function send_chat_request($sender_id,$receiver_id,$project_id){

            $table_chat_requests    = DB::table('chat_requests');
            
            $table_chat_requests->where([
                'sender_id' => $sender_id,
                'receiver_id' => $receiver_id,
                'project_id' => $project_id,
            ]);

            if(empty($table_chat_requests->count())){
                $result = $table_chat_requests->insertGetId([
                    'sender_id' => $sender_id,
                    'receiver_id' => $receiver_id,
                    'project_id' => $project_id,
                    'request_status' => 'accepted',
                    'chat_initiated' => 'employer',
                    'created' => date('Y-m-d H:i:s'),
                    'updated' => date('Y-m-d H:i:s'),
                ]);
            }else{
                $result = $table_chat_requests->update([
                    'request_status' => 'accepted',
                    'chat_initiated' => 'employer',
                    'updated' => date('Y-m-d H:i:s'),
                ]);
            }

            return $result;
        }
    }

