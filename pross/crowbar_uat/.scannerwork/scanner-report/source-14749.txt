<?php

    namespace Models; 

    use Illuminate\Database\Eloquent\Model;
    use Illuminate\Support\Facades\DB;
    use Illuminate\Support\Facades\Crypt;
    use Illuminate\Support\Facades\Mail;

    class Projects extends Model{
        protected $table = 'projects';
        protected $primaryKey = 'id_project';

        const CREATED_AT = 'created';
        const UPDATED_AT = 'updated';

        public function __construct(){}

        /**
         * [This method is for relating industries] 
         * @return Boolean
         */

        public function industries(){
            return $this->hasMany('\Models\ProjectsIndustries','project_id','id_project');
        }        
   
        /**
         * [This method is for relating subindustries] 
         * @return Boolean
         */

        public function subindustries(){
            return $this->hasMany('\Models\ProjectsSubindustries','project_id','id_project');
        }         

        /**
         * [This method is for relating skills] 
         * @return Boolean
         */  

        public function skills(){
            return $this->hasMany('\Models\ProjectRequiredSkills','project_id','id_project');
        }              

        /**
         * [This method is for relating employer] 
         * @return Boolean
         */

        public function employer(){
            return $this->hasOne('\Models\Employers','id_user','company_id');
        }

        /**
         * [This method is for relating employer to other jobs] 
         * @return Boolean
         */

        public function similarjobs(){
            return $this->hasMany('\Models\ProjectsSubindustries','project_id','id_project');
        }              
      
        /**
         * [This method is for relating proposal] 
         * @return Boolean
         */

        public function proposals(){
            return $this->hasMany('\Models\Proposals','project_id','id_project');
        }              
      
        /**
         * [This method is for relating reviews] 
         * @return Boolean
         */

        public function reviews(){
            return $this->hasOne('\Models\Reviews','project_id','id_project');
        }               
      
        /**
         * [This method is for relating raise dispute item] 
         * @return Boolean
         */

        public function dispute(){
            return $this->hasOne('\Models\RaiseDispute','project_id','id_project');
        }  

        /**
         * [This method is for relating proposal] 
         * @return Boolean
         */

        public function proposal(){
            return $this->hasOne('\Models\Proposals','project_id','id_project');
        }   

        /**
         * [This method is for relating chat request] 
         * @return Boolean
         */

        public function chat(){
            return $this->hasOne('\Models\ChatRequests','project_id','id_project');
        }  

        /**
         * [This method is for relating proposal to talent] 
         * @return Boolean
         */

        public function talent(){
            return $this->hasOne('\Models\Proposals','project_id','id_project');
        }

        /**
         * [This method is for relating project log] 
         * @return Boolean
         */

        public function projectlog(){
            return $this->hasOne('\Models\ProjectLogs','project_id','id_project');
        }

        /**
         * [This method is for relating project logs] 
         * @return Boolean
         */

        public function projectlogs(){
            return $this->hasMany('\Models\ProjectLogs','project_id','id_project');
        }  

        /**
         * [This method is for relating project logs] 
         * @return Boolean
         */

        public function transaction(){
            return $this->hasOne('\Models\Transactions','transaction_project_id','id_project');
        }           
        
        /**
         * [This method is for finding user by ID] 
         * @return JSON
         */

        public static function findById($project_id,$keys = ['*']){
            $project = DB::table('projects')->select($keys)->where('id_project',$project_id)->get()->first();

            return json_decode(json_encode($project),true);
        }

        /**
         * [This method is for scope for default keys] 
         * @return Boolean
         */

        public function scopeDefaultKeys($query){
            $prefix         = DB::getTablePrefix();
            
            $query->addSelect([
                'projects.id_project',
                'projects.user_id as company_id',
                'projects.title',
                'projects.created',
                'projects.employment',
                'projects.expertise',
                'projects.other_perks',
                'projects.status',
                'projects.awarded',
                'projects.created',
                'projects.startdate',
                'projects.enddate',
                'projects.completedate',
                'projects.closedate',
                'projects.canceldate',
                'projects.updated',
                'projects.status',
                'projects.is_cancelable',
                'projects.is_cancelled',
                'projects.expected_hour',
                \DB::Raw("LPAD({$prefix}projects.id_project, ".JOBID_PREFIX.", '0') as project_display_id"),
            ])
            ->projectStatus()
            ->isDisputable();

            return $query;
        }  

        /**
         * [This method is for scope for project status] 
         * @return Boolean
         */

        public function scopeProjectStatus($query){
            $prefix         = DB::getTablePrefix();
            $current_date   = date('Y-m-d');
            $query->addSelect([
                \DB::Raw("
                    IF(
                        (DATE({$prefix}projects.enddate) < DATE('{$current_date}') && {$prefix}projects.project_status = 'pending'),
                        'pending',
                        IF(
                            DATE({$prefix}projects.enddate) < DATE('{$current_date}'),
                            'closed',
                            IF(
                                ({$prefix}projects.project_status = 'closed' && {$prefix}projects.closedate IS NULL),
                                'completed',
                                {$prefix}projects.project_status
                            )
                        )
                    ) as project_status
                ")
            ]);

            return $query;
        } 

        /**
         * [This method is for scope for closure status] 
         * @return Boolean
         */

        public function scopeProjectClosureStatus($query){
            $prefix         = DB::getTablePrefix();
            $current_date   = date('Y-m-d');
            $query->addSelect([
                \DB::Raw("
                    IF(
                        (
                            DATE(
                                    DATE_ADD({$prefix}projects.closedate, INTERVAL 1 DAY)
                                ) > DATE('{$current_date}') || (cb_projects.project_status = 'closed') 
                        ), 'closed', 'pending'
                    ) as project_closure_status
                ")
            ]);

            return $query;
        } 

        /**
         * [This method is for scope for disputable] 
         * @return Boolean
         */

        public function scopeIsDisputable($query){
            $prefix = DB::getTablePrefix();
            $current_date   = date('Y-m-d H:i:s');

            $query->addSelect([
                \DB::Raw("DATE_ADD({$prefix}projects.completedate, INTERVAL ".RAISE_DISPUTE_DATE_LIMIT." HOUR) as dispute_date"),
                \DB::Raw("
                    IF(
                        ('{$current_date}' <= DATE_ADD({$prefix}projects.completedate, INTERVAL ".RAISE_DISPUTE_DATE_LIMIT." HOUR)),
                        '".DEFAULT_YES_VALUE."',
                        '".DEFAULT_NO_VALUE."'
                    ) as is_disputable
                ")
            ]);

            return $query;
        }  

        /**
         * [This method is for scope for employer disputable] 
         * @return Boolean
         */

        public function scopeIsEmployerDisputable($query){
            $prefix = DB::getTablePrefix();
            $current_date   = date('Y-m-d H:i:s');

            $query->addSelect([
                \DB::Raw("
                    IF(
                        ('{$current_date}' <= DATE_ADD({$prefix}projects.completedate, INTERVAL ".RAISE_DISPUTE_DATE_LIMIT." HOUR)),
                        '".DEFAULT_YES_VALUE."',
                        '".DEFAULT_NO_VALUE."'
                    ) as is_disputable
                ")
            ]);

            return $query;
        }

        /**
         * [This method is for scope for proposal status] 
         * @return Boolean
         */

        public function scopeProposalStatus($query,$talent_id){
            $query->leftjoin('talent_proposals',function($q) use($talent_id){
                $q->on('talent_proposals.user_id','=',\DB::Raw($talent_id));
                $q->on('talent_proposals.project_id','=','projects.id_project');
                $q->on('talent_proposals.status','=',\DB::Raw('"accepted"'));
            })->addSelect([
                'talent_proposals.status as proposal_status'
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
                $leftjoin->on('files.user_id','=','projects.user_id');
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
         * [This method is for scope for company logo] 
         * @return Boolean
         */

        public function scopeCompanyName($query){
            $prefix         = DB::getTablePrefix();
            
            $query->leftJoin('users','users.id_user','=','projects.user_id')->addSelect(["users.company_name as company_name"]);

            return $query;
        }  

        /**
         * [This method is for scope for project description] 
         * @return Boolean
         */

        public function scopeProjectDescription($query, $trim = false){
            $prefix         = DB::getTablePrefix();
            
            if(!empty($trim)){
                $query->leftJoin('project_language', function ($join) {
                    $join->on('projects.id_project', '=', 'project_language.project_id')->where('project_language.language', ___cache('default_language'));
                })->addSelect([
                    \DB::Raw("
                        CONCAT(
                            '<span class=\"job-detail-description\" style=\"font-family:\'Open Sans\';font-size: 12px;\">',
                            CONCAT(
                                TRIM(
                                    TRAILING '<br>' FROM 
                                    TRIM(
                                        TRAILING '<br></span>' FROM
                                        TRIM(
                                            TRAILING '<div><br></div>' 
                                            FROM {$prefix}project_language.description
                                        )
                                    )
                                ),
                                '</span>'
                            ),
                            '</span>'
                        ) as description
                    ")
                ]);
            }else{
                $query->leftJoin('project_language', function ($join) {
                    $join->on('projects.id_project', '=', 'project_language.project_id')->where('project_language.language', ___cache('default_language'));
                })->addSelect([
                    \DB::Raw("
                        CONCAT(
                            '<span class=\"job-detail-description\" style=\"font-family:\'Open Sans\';font-size: 12px;\">',
                            {$prefix}project_language.description,
                            '</span>'
                        ) as description
                    ")
                ]);
            }

            return $query;
        }  

        /**
         * [This method is for scope for project price] 
         * @return Boolean
         */

        public function scopeProjectPrice($query){
            $prefix         = DB::getTablePrefix();
            
            $query->addSelect([
                \DB::Raw('`CONVERT_PRICE`('.$prefix.'projects.price, '.$prefix.'projects.price_unit, "'.request()->currency.'") AS price'),
                \DB::Raw("'".___cache('currencies')[request()->currency]."' as price_unit")
            ]);

            return $query;
        }  

        /**
         * [This method is for scope for saved project by talent] 
         * @return Boolean
         */

        public function scopeIsProjectSaved($query,$talent_id){
            $prefix         = DB::getTablePrefix();
            
            $query->leftJoin('saved_jobs',function($leftjoin) use($talent_id){
                $leftjoin->on('saved_jobs.job_id','=','projects.id_project');
                $leftjoin->on('saved_jobs.user_id','=',\DB::Raw($talent_id));
            })->addSelect([
                \DB::Raw("
                    IF(
                        {$prefix}saved_jobs.id_saved IS NOT NULL,
                        '".DEFAULT_YES_VALUE."',
                        '".DEFAULT_NO_VALUE."'
                    ) as is_saved
                "),
            ]);

            return $query;
        }

        /**
         * [This method is for scope for saved project by talent] 
         * @return Boolean
         */

        public function scopeAcceptedTalentId($query,$project_id){
            $prefix         = DB::getTablePrefix();
            
            $query->leftJoin('talent_proposals as proposals',function($leftjoin) use($project_id){
                $leftjoin->on('proposals.status','=',\DB::Raw("'".'accepted'."'"));
                $leftjoin->on('proposals.project_id','=',\DB::Raw($project_id));
            })->addSelect([
                'proposals.user_id as accepted_talent_id'
            ]);

            return $query;
        }   

        /**
         * [This method is used to findById] 
         * @param [Integer]$project_id [Used for project id]
         * @param [String]$keys[Used for keys]
         * @return Json Response
         */ 

        public static function draft($user_id, $keys = ['*']){
            $language       = \App::getLocale();
            $prefix         = DB::getTablePrefix();
            
            $project = \Models\Projects::select($keys)->projectPrice()
            ->where('user_id',$user_id)
            ->where('status','draft')
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
            ->get()->first();

            return json_decode(json_encode($project),true);
        }

        /**
         * [This method is used for post job in step] 
         * @param [String]$where [Used for where clause]
         * @param [Varchar]$data[Used for data]
         * @return Data Response
         */ 

        public static function postjob($data){
            $projects = DB::table('projects');

            if(!empty($data['id_project'])){
                if(!empty($data['description'])){
                    self::projects_lang($data['id_project'],$data['description'],'update');
                }
                $data['updated'] = date('Y-m-d H:i:s');

                $projects->where('id_project',$data['id_project'])->update($data);
                return $data['id_project'];
            }else{
                $insertId = $projects->insertGetId($data);
                if(!empty($data['description'])){
                    self::projects_lang($insertId,$data['description'],'insert');
                }
                return $insertId;
            }
        }

        public static function projects_lang($project_id,$description,$type="insert"){
            $projectLang = [];
            $allLang = language();

            if(___configuration(['google_translate_enabled'])['google_translate_enabled'] == 'Y'){
                $translator = new \Dedicated\GoogleTranslate\Translator;
                try {
                    $detected_lang = $translator->detect($description);
                }
                catch (\Exception $e) {
                    $detected_lang = false;
                }

                if($detected_lang){
                    if(array_key_exists($detected_lang, $allLang)){
                        $projectLang[] = [
                            'project_id' => $project_id,
                            'language' => $detected_lang,
                            'description' => $description,
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
                                    'project_id' => $project_id,
                                    'language' => \Cache::get('default_language'),
                                    'description' => $convertLang,
                                    'created' => date('Y-m-d H:i:s'),
                                    'updated' => date('Y-m-d H:i:s')
                                ];
                            }
                            else{
                                $projectLang[] = [
                                    'project_id' => $project_id,
                                    'language' => \Cache::get('default_language'),
                                    'description' => $description,
                                    'created' => date('Y-m-d H:i:s'),
                                    'updated' => date('Y-m-d H:i:s')
                                ];
                            }
                        }
                    }
                    else{
                        $projectLang[] = [
                            'project_id' => $project_id,
                            'language' => \Cache::get('default_language'),
                            'description' => $description,
                            'created' => date('Y-m-d H:i:s'),
                            'updated' => date('Y-m-d H:i:s')
                        ];
                    }
                }
                else{
                    $projectLang[] = [
                        'project_id' => $project_id,
                        'language' => \Cache::get('default_language'),
                        'description' => $description,
                        'created' => date('Y-m-d H:i:s'),
                        'updated' => date('Y-m-d H:i:s')
                    ];
                }
            }
            else{
                $projectLang[] = [
                    'project_id' => $project_id,
                    'language' => \Cache::get('default_language'),
                    'description' => $description,
                    'created' => date('Y-m-d H:i:s'),
                    'updated' => date('Y-m-d H:i:s')
                ];
            }

            if(count($projectLang) > 0){
                if($type == 'update'){
                    foreach ($projectLang as $key => $value) {
                        DB::table('project_language')
                        ->where('project_id', '=', $project_id)
                        ->where('language', '=', $value['language'])
                        ->update($value);
                    }
                }else{
                    DB::table('project_language')
                    ->insert($projectLang);
                }
            }
            return true;
        }

        /**
         * [This method is used for saving industry] 
         * @param [String]$where [Used for where clause]
         * @param [Varchar]$data[Used for data]
         * @return Data Response
         */ 

        public static function saveindustry($project_id, $data){
            $projects_industries = DB::table('projects_industries');

            $projects_industries->where('project_id',$project_id)->delete();

            if(!empty($data)){
                $insert = array_map(function($item) use($project_id){
                    return [
                        'project_id'    => $project_id,
                        'industry_id'   => $item
                    ];
                }, $data);

                return $projects_industries->insert($insert);
            }else{
                return true;
            }
        }

        /**
         * [This method is used for saving subindustry] 
         * @param [String]$where [Used for where clause]
         * @param [Varchar]$data[Used for data]
         * @return Data Response
         */ 

        public static function savesubindustry($project_id, $data, $industry_id = NULL){
            $projects_industries = DB::table('projects_subindustries');
            $projects_industries->where('project_id',$project_id)->delete();
            if(!empty($data) && is_array($data)){
                $subindustries_list = array_map(
                    function($i) use($industry_id){
                        if(!in_array(strtolower($i), (array)array_keys(\Cache::get('abusive_words')))){
                            return array(
                                'en'                => substr($i,0,TAG_LENGTH),
                                'parent'            => $industry_id,
                                'created'           => date('Y-m-d H:i:s'),
                                'updated'           => date('Y-m-d H:i:s')
                            ); 
                        }
                    }, 
                    $data
                );
                
                $subindustries_list = array_filter($subindustries_list);

                foreach ($subindustries_list as $key) {
                    $table_industries = DB::table('industries');
                    $inserted_industry = $table_industries->select('id_industry')->where(['en' => $key['en']])->first();
                    if(!empty($inserted_industry->id_industry)){
                        $subindustry_id = $inserted_industry->id_industry;
                    }else{
                        $subindustry_id    = $table_industries->insertGetId($key);
                        \Cache::forget('subindustries_name');
                    }

                    $subindustries[] = [
                        'project_id'            => $project_id,
                        'subindustry_id'        => $subindustry_id
                    ];
                }

                if(count($subindustries_list) == count($data)){
                    if(!empty($subindustries)){
                        return $projects_industries->insert($subindustries);
                    }else{
                        return false;    
                    }
                }else{
                    return false;    
                }
            }else{
                return false;
            }
        }

        /**
         * [This method is used for change] 
         * @param [String]$where [Used for where clause]
         * @param [Varchar]$data[Used for data]
         * @return Data Response
         */ 

        public static function change($where,$data){
            if(empty($where) && empty($data)){
                return false;
            }

            return DB::table('projects')->where($where)->update($data);
        }


        /**
         * [This method is used talent jobs] 
         * @param [type]$user[<description>]
         * @return Builder
         */ 

        public static function talent_jobs($user){
            $prefix         = DB::getTablePrefix();
            $language       = \App::getLocale();
            $base_url       = ___image_base_url();

            $projects = Projects::defaultKeys()
            ->projectPrice()
            ->projectDescription(true)
            ->companyName()
            ->companyLogo()
            ->isProjectSaved($user->id_user)
            ->whereNotIn('projects.status',['draft','trashed']);

            return $projects;
        }

        /**
         * [This method is used to employer jobs] 
         * @param [type]$user[<description>]
         * @return Builder
         */ 

        public static function employer_jobs($user){
            $prefix         = DB::getTablePrefix();
            $language       = \App::getLocale();
            $base_url       = ___image_base_url();

            $projects = Projects::defaultKeys()
            ->projectPrice()
            ->projectDescription(true)
            ->companyName()
            ->companyLogo()
            ->where('projects.user_id',$user->id_user)
            ->whereNotIn('projects.status',['draft','trashed']);

            return $projects;
        }

        /**
         * [This method is used to employer jobs] 
         * @param [type]$user[<description>]
         * @return Builder
         */ 

        public static function public_employer_jobs(){
            $prefix         = DB::getTablePrefix();
            $language       = \App::getLocale();
            $base_url       = ___image_base_url();

            $projects = Projects::defaultKeys()
            ->projectPrice()
            ->projectDescription(true)
            ->companyName()
            ->companyLogo()
            ->whereNotIn('projects.status',['draft','trashed']);

            return $projects;
        }

        /**
         * [This method is used for getting Project List] 
         * @param null
         * @return Data Response
         */ 

        public static function getProjectList(){
            $prefix         = DB::getTablePrefix();
            $language       = \App::getLocale();
            return DB::table('projects as projects')
            ->select([
                'projects.id_project',
                'projects.title',
                \DB::Raw("{$prefix}projects.employment AS employment"),
                \DB::Raw("CONCAT({$prefix}projects.price_unit,{$prefix}projects.price) AS price"),
                \DB::Raw("DATE_FORMAT({$prefix}projects.startdate, '%d %M %Y') AS startdate"),
                \DB::Raw("DATE_FORMAT({$prefix}projects.enddate, '%d %M %Y') AS enddate"),
                \DB::Raw("{$prefix}projects.project_status AS project_status"),
                \DB::Raw("CONCAT({$prefix}users.first_name, ' ',{$prefix}users.last_name) AS name"),
                \DB::Raw("IF(({$prefix}industries.{$language} != ''),{$prefix}industries.`{$language}`, {$prefix}industries.`en`) as industry"),
                \DB::Raw("IF(({$prefix}subindustry.{$language} != ''),{$prefix}subindustry.`{$language}`, {$prefix}subindustry.`en`) as subindustry"),
            ])
            ->leftJoin('users', 'projects.user_id', '=', 'users.id_user')
            ->leftJoin('industries', 'projects.industry', '=', 'industries.id_industry')
            ->leftJoin('industries AS subindustry', 'projects.subindustry', '=', 'subindustry.id_industry')
            ->where('projects.status', 'active')
            ->orderBy('projects.id_project', 'desc')
            ->get();
        }

        /**
         * [This method is used to getProjectDetail] 
         * @param [Integer]$id_project [Used for project Id]
         * @return Json Response
         */ 

        public static function getProjectDetail($id_project){
            $prefix         = DB::getTablePrefix();
            $language       = \App::getLocale();
            $projectDetail = DB::table('projects as projects')
            ->select([
                'projects.id_project',
                'projects.title',
                'projects.user_id as employer_id',
                'projects.description',
                'projects.price_max',
                'projects.expertise',
                'projects.other_perks',
                \DB::Raw("{$prefix}projects.transaction AS transaction"),#ucfirst
                'projects.work_hours',
                \DB::Raw("{$prefix}projects.employment AS employment"),#ucfirst,
                 \DB::Raw("CONCAT({$prefix}projects.price_unit,{$prefix}projects.price) AS price"),
                \DB::Raw("DATE_FORMAT({$prefix}projects.startdate, '%d %M %Y') AS startdate"),
                \DB::Raw("DATE_FORMAT({$prefix}projects.enddate, '%d %M %Y') AS enddate"),
                \DB::Raw("{$prefix}projects.project_status AS project_status"), #ucfirst
                \DB::Raw("CONCAT({$prefix}users.first_name,' ',{$prefix}users.last_name) AS name"),
                'users.company_name',
                \DB::Raw("IF(({$prefix}industries.{$language} != ''),{$prefix}industries.`{$language}`, {$prefix}industries.`en`) as industry"),
                \DB::Raw("IF(({$prefix}subindustry.{$language} != ''),{$prefix}subindustry.`{$language}`, {$prefix}subindustry.`en`) as subindustry"),
                \DB::Raw("IF(({$prefix}city.{$language} != ''),{$prefix}city.{$language}, {$prefix}city.`en`) as city_name")
            ])
            ->leftJoin('users', 'projects.user_id', '=', 'users.id_user')
            ->leftJoin('industries', 'projects.industry', '=', 'industries.id_industry')
            ->leftJoin('industries AS subindustry', 'projects.subindustry', '=', 'subindustry.id_industry')
            ->leftJoin('city', function($join)
            {
                $join->on('city.id_city', '=', 'projects.location')
                     ->where('projects.location', '>', 0);
            })
            ->where('projects.status', 'active')
            ->where('projects.id_project', $id_project)
            ->first();

            return json_decode(json_encode($projectDetail), true);
        }

        /**
         * [This method is used to getProjectSkill] 
         * @param [Integer]$id_project [Used for project Id]
         * @return Data Response
         */ 

        public static function getProjectSkill($id_project){
            $prefix        = DB::getTablePrefix();
            $projectSkill = DB::table('project_required_skills')
            ->select(\DB::Raw('GROUP_CONCAT(`skill` SEPARATOR ", ") AS skill'))
            ->where('project_id', $id_project)
            ->first();

            $projectSkill = json_decode(json_encode($projectSkill), true);
            return $projectSkill['skill'];
        }

        /**
         * [This method is used to getProjectQualification] 
         * @param [Integer]$id_project [Used for project Id]
         * @return Json Response
         */ 

        public static function getProjectQualification($id_project){
            $prefix        = DB::getTablePrefix();
            $projectSkill = DB::table('project_required_qualifications')
            ->select(\DB::Raw('GROUP_CONCAT(`degree_name` SEPARATOR ", ") AS qualification_name'))
            ->leftJoin('degree', 'degree.id_degree', '=', 'project_required_qualifications.qualification')
            ->where('project_id', $id_project)
            ->first();

            $projectSkill = json_decode(json_encode($projectSkill), true);
            return $projectSkill['qualification_name'];
        }

        /**
         * [This method is used to getProjectDescription] 
         * @param [Integer]$id_project [Used for project Id]
         * @return Json Response
         */ 

        public static function getProjectDescription($id_project){
            $prefix       = DB::getTablePrefix();
            $projectLang = DB::table('project_language')
            ->select('language','description')
            ->where('project_id', $id_project)
            ->get();

            return json_decode(json_encode($projectLang), true);
        }

        /**
         * [This method is used to getProjectProposal] 
         * @param [Integer]$id_project [Used for project Id]
         * @return Data Response
         */ 

        public static function getProjectProposal($id_project){
            $prefix             = DB::getTablePrefix();
            \DB::statement(\DB::raw('set @row_number=0'));
            $projectProposal    = DB::table('talent_proposals')
            ->select(
                \DB::raw('@row_number  := @row_number  + 1 AS row_number'),
                'talent_proposals.id_proposal',
                'talent_proposals.quoted_price',
                'talent_proposals.comments',
                'talent_proposals.status',
                \DB::Raw('CONCAT('.$prefix.'users.first_name, " ", '.$prefix.'users.last_name ) AS name')
            )
            ->leftJoin('users', 'users.id_user', '=', 'talent_proposals.user_id')
            ->where('project_id', $id_project)
            ->get();

            return $projectProposal;
        }

        /**
         * [This method is used to getProjectListByIndustry] 
         * @param [Integer]$id_industry [Used for Industry Id]
         * @return Json Response
         */ 

        public static function getProjectListByIndustry($id_industry){
            $prefix         = DB::getTablePrefix();
            $language       = \App::getLocale();
            $projectList = DB::table('projects as projects')
            ->select([
                'projects.id_project',
                'projects.title',
                \DB::Raw('`FIRST`('.$prefix.'projects.employment) AS employment'),
                \DB::Raw('CONCAT('.$prefix.'projects.price_unit,'.$prefix.'projects.price) AS price'),
                \DB::Raw('DATE_FORMAT('.$prefix.'projects.startdate, "%d %M %Y") AS startdate'),
                \DB::Raw('DATE_FORMAT('.$prefix.'projects.enddate, "%d %M %Y") AS enddate'),
                \DB::Raw('`FIRST`('.$prefix.'projects.project_status) AS project_status'),
                \DB::Raw('CONCAT('.$prefix.'users.first_name, " ",'.$prefix.'users.last_name) AS name'),
                'users.company_name',
                \DB::Raw("IF(({$prefix}industries.{$language} != ''),{$prefix}industries.`{$language}`, {$prefix}industries.`en`) as industries_name"),
                \DB::Raw("IF(({$prefix}subindustry.{$language} != ''),{$prefix}subindustry.`{$language}`, {$prefix}subindustry.`en`) as subindustry_name"),
                ])
            ->leftJoin('users', 'projects.user_id', '=', 'users.id_user')
            ->leftJoin('industries', 'projects.industry', '=', 'industries.id_industry')
            ->leftJoin('industries AS subindustry', 'projects.subindustry', '=', 'subindustry.id_industry')
            ->where('projects.status', 'active')
            ->where('projects.industry', $id_industry)
            ->where('projects.project_status', 'pending')
            ->limit(10)
            ->get()
            ->toArray();

            return json_decode(json_encode($projectList), true);
        }

        /**
         * [This method is used for disputables] 
         * @param [Integer]$user_id [Used for user id]
         * @param [Integer]$project_id[project id]
         * @param [Varchar]$project_log[Project log]
         * @return Data Response
         */ 

        public static function is_disputable($user_id, $project_id, $project_log){
            $latest_dispute = \DB::table('project_raised_dispute')->where('project_id',$project_id)->orderBy('created','desc')->get()->first();
            
            $isNotValidDispute = \Models\RaiseDispute::is_valid_dispute($user_id, $project_id);

            if(empty($latest_dispute)){
                if($project_log['start'] == 'confirmed' && !empty($project_log['close']) && $project_log['close'] == 'pending' && !empty($project_log['enddate'])){
                    return DEFAULT_YES_VALUE;
                }else{
                    return DEFAULT_NO_VALUE;
                }
            }else if(!empty($isNotValidDispute)){
                return DEFAULT_YES_VALUE;
            }else{
                return 'already_disputed';
            }
        }

        /**
         * [This method is used for detail] 
         * @param [Integer]$project_id [Used for project Id]
         * @param [Integer]$talent_id[<Used for user's Id>]
         * @return Data Response
         */ 

        public static function detail($project_id,$talent_id){
            if(0){
                /*Check for converted description exist or not*/
                $translator = new \Dedicated\GoogleTranslate\Translator;
                $project_language = DB::table('project_language')
                ->where('project_id', $project_id)
                ->where('language', request()->language)
                ->count();
                if($project_language <= 0){
                    $project_language = DB::table('project_language')
                    ->where('project_id', $project_id)
                    ->where('language', \Cache::get('default_language'))
                    ->get()
                    ->first();

                    if(___configuration(['google_translate_enabled'])['google_translate_enabled'] == 'Y'){
                        try{
                            $convertLang = $translator->setTargetLang(request()->language)->translate($project_language->description);
                        }catch(\Exception $e){
                            $convertLang = false;
                        }

                        if($convertLang){
                            $projectLang = [
                                'project_id' => $project_id,
                                'language' => request()->language,
                                'description' => $convertLang,
                                'created' => date('Y-m-d H:i:s'),
                                'updated' => date('Y-m-d H:i:s')
                            ];
                            DB::table('project_language')
                            ->insert($projectLang);
                        }
                    }
                    else{
                        $projectLang = [
                            'project_id' => $project_id,
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
        }

        /**
         * [This method is used to getProjectListForNewsLetter] 
         * @param null
         * @return Json Response
         */ 
        
        public static function getProjectListForNewsLetter(){
            $prefix         = DB::getTablePrefix();
            $language       = \App::getLocale();
            $projectList = DB::table('projects as projects')
            ->select([
                'projects.id_project',
                'projects.title',
                \DB::Raw('`FIRST`('.$prefix.'projects.employment) AS employment'),
                \DB::Raw('CONCAT('.$prefix.'projects.price_unit,'.$prefix.'projects.price) AS price'),
                \DB::Raw('CONCAT('.$prefix.'projects.price_unit,'.$prefix.'projects.price_max) AS price_max'),
                \DB::Raw('DATE_FORMAT('.$prefix.'projects.startdate, "%d %M %Y") AS startdate'),
                \DB::Raw('DATE_FORMAT('.$prefix.'projects.enddate, "%d %M %Y") AS enddate'),
                \DB::Raw('`FIRST`('.$prefix.'projects.project_status) AS project_status'),
                \DB::Raw('CONCAT('.$prefix.'users.first_name, " ",'.$prefix.'users.last_name) AS name'),
                'users.company_name',
                \DB::Raw("IF(({$prefix}industries.{$language} != ''),{$prefix}industries.`{$language}`, {$prefix}industries.`en`) as industries_name"),
                \DB::Raw("IF(({$prefix}subindustry.{$language} != ''),{$prefix}subindustry.`{$language}`, {$prefix}subindustry.`en`) as subindustry_name"),
                ])
            ->leftJoin('users', 'projects.user_id', '=', 'users.id_user')
            ->leftJoin('industries', 'projects.industry', '=', 'industries.id_industry')
            ->leftJoin('industries AS subindustry', 'projects.subindustry', '=', 'subindustry.id_industry')
            ->where('projects.status', 'active')
            ->where('projects.project_status', 'pending')
            ->limit(10)
            ->get()
            ->toArray();

            return json_decode(json_encode($projectList), true);
        }

        /**
         * [This method is used to close project] 
         * @param null
         * @return Data Response
         */ 

        public static function closeProject(){
            $prefix         = DB::getTablePrefix();
            DB::table('projects as projects')
            ->where('enddate', '<=', date('Y-m-d H:i:s'))
            ->update(['project_status' => 'closed']);
        }

        /**
         * [This method is used for employer's actions] 
         * @param [Integer]$project_id [Used for project Id]
         * @return Data Response
         */ 

        public static function employer_actions($project_id){
            $action                 = 'no';
            $section                = '';
            
            $talent_id              = \App\Models\Proposals::accepted_proposal_id($project_id);
            $project                = \Models\Projects::findById($project_id,['id_project','startdate','enddate','employment','user_id as company_id']);
            $logs                   = \Models\ProjectLogs::findById($project_id);
            $chat                   = \Models\Chats::is_chat_possible_with_talent($project['company_id'],$talent_id);
            $dispute                = \Models\Projects::is_disputable(\Auth::user()->id_user, $project_id,$logs);
            
            if($logs['close'] == 'pending' && !empty($logs['enddate']) && strtotime(date('Y-m-d')) > strtotime($project['enddate'])){
                $action     = 'yes';
                $section    = 'closepending';
            }else if(strtotime(date('Y-m-d')) > strtotime($project['enddate']) && strtotime(date('Y-m-d')) > strtotime($project['startdate'])){
                $action         = 'no';    
                $section        = 'job_expired';    
            }else if(strtotime(date('Y-m-d')) <= strtotime($project['startdate']) && strtotime(date('Y-m-d')) <= strtotime($project['enddate']) && empty($talent_id)){
                if(empty($talent_id)){
                    $action     = 'yes';
                    $section    = 'no_proposal_accepted';        
                }
            } else{
                if(empty($logs['startdate'])){
                    $action     = 'yes';
                    $section    = 'start';
                }else if(!empty($logs['startdate']) && $logs['start'] == 'pending'){
                    $action     = 'yes';
                    $section    = 'startpending';
                }else if(empty($logs['enddate'])){
                    $action     = 'yes';
                    $section    = 'close';
                }else if(!empty($logs['enddate']) && $logs['close'] == 'pending'){
                    $action     = 'yes';
                    $section    = 'closepending';
                }
            }

            if(empty($talent_id)){
                $chat = "";
            }

            return [
                'id_project'            => $project_id,
                'receiver_id'           => (string)$talent_id,
                'sender_id'             => (string)$project['company_id'],
                'jobaction' => [
                    'action'            => $action, 
                    'section'           => $section, 
                    'chat' => [
                        'chataction'    => $chat,
                        'sender_id'     => (string)$talent_id,
                        'receiver_id'   => (string)$project['company_id'],
                    ], 
                    'dispute'           => $dispute,
                    'dispute_detail'    => \Models\RaiseDispute::detail($project['company_id'],$project_id)
                ]
            ];
        }

        /**
         * [This method is used for user's actions] 
         * @param [Integer]$project_id [Used for project Id]
         * @return Data Response
         */ 

        public static function talent_actions($project_id){
            $action                 = 'no';
            $section                = '';

            $project                = \Models\Projects::findById($project_id,['id_project','startdate','enddate','employment','user_id as company_id']);
            $proposal               = \App\Models\Proposals::project_proposal_detail($project_id,\Auth::user()->id_user);
            $logs                   = \Models\ProjectLogs::findById($project_id);
            $chat                   = \Models\Chats::is_chat_possible_with_employer(\Auth::user()->id_user,$project['company_id']);
            $dispute                = \Models\Projects::is_disputable(\Auth::user()->id_user, $project_id,$logs);

            if(strtotime(date('Y-m-d')) <= strtotime($project['startdate']) && strtotime(date('Y-m-d')) <= strtotime($project['enddate']) && (empty($proposal['status']) || $proposal['status'] !== 'accepted') && $project['employment'] != 'fulltime'){
                if(empty($proposal['status'])){
                    $action     = 'yes';
                    if($project['employment'] == 'fulltime'){
                        $section    = 'submit_application';
                    }else{
                        $section    = 'submit_proposal';
                    }
                }else if($proposal['status'] == 'applied'){
                    $action     = 'no';
                    
                    if($project['employment'] == 'fulltime'){
                        $section    = 'application_submitted';
                    }else{
                        $section    = 'proposal_submitted';
                    }
                }else{
                    $action     = 'no';
                    $section    = 'proposal_declined';
                }
            }else if($project['employment'] == 'fulltime'){
                if(empty($proposal['status'])){
                    $action     = 'yes';
                    $section    = 'submit_application';
                }else if($proposal['status'] == 'applied'){
                    $action     = 'no';
                    $section    = 'application_submitted';
                }else{
                    $action     = 'no';
                    $section    = 'proposal_declined';
                }
            }else if(strtotime(date('Y-m-d')) > strtotime($project['enddate']) && strtotime(date('Y-m-d')) > strtotime($project['startdate'])){
                $action         = 'no';    
                $section        = 'job_expired';    
            }else{
                if(empty($logs['startdate'])){
                    $action     = 'yes';
                    $section    = 'start';
                }else if(!empty($logs['startdate']) && $logs['start'] == 'pending'){
                    $action     = 'yes';
                    $section    = 'startpending';
                }else if(empty($logs['enddate'])){
                    $action     = 'yes';
                    $section    = 'close';
                }else if(!empty($logs['enddate']) && $logs['close'] == 'pending'){
                    $action     = 'yes';
                    $section    = 'closepending';
                }
            }

            $talent_id      = \Auth::user()->id_user;
            $employer_id    = $project['company_id'];

            if(empty($proposal)){
                $chat = '';
            }

            return [
                'id_project' => $project_id,
                'receiver_id' => $employer_id,
                'sender_id' => $talent_id,
                'jobaction' => [
                    'action' => $action, 
                    'section' => $section, 
                    'chat' => [
                        'chataction' => $chat,
                        'sender_id' => $talent_id,
                        'receiver_id' => $employer_id,
                    ], 
                    'dispute' => $dispute,
                    'dispute_detail'    => \Models\RaiseDispute::detail($project['company_id'],$project_id)
                ]
            ];
        }

        /**
         * [This method is used to getProjectForNewsLetter] 
         * @param [String] $where_data [Used for where clause]
         * @param [Integer]$user_currency[Used for user currency]
         * @param [String]$currency_sign[Used for Currency Sign]
         * @return Json Response
         */ 

        public static function getProjectForNewsLetter($where_data, $user_currency, $currency_sign){
            $prefix         = DB::getTablePrefix();
            $language       = \App::getLocale();

            if(!empty($where_data)){
                $where_data = implode(' AND ' . $prefix . 'projects.', $where_data);
                $where_data = $prefix . 'projects.' . $where_data;
            }
            else{
                $where_data = '';
            }

            $projectList = DB::table('projects as projects')
            ->select([
                'projects.id_project',
                'projects.title',
                \DB::Raw('`FIRST`('.$prefix.'projects.employment) AS employment'),

                \DB::Raw('CONCAT("",`CONVERT_PRICE`('.$prefix.'projects.price, '.$prefix.'projects.price_unit, "'.$user_currency.'")) AS price'),
                \DB::Raw('CONCAT("",`CONVERT_PRICE`('.$prefix.'projects.price_max, '.$prefix.'projects.price_unit, "'.$user_currency.'")) AS price_max'),

                \DB::Raw('DATE_FORMAT('.$prefix.'projects.startdate, "%d %M %Y") AS startdate'),
                \DB::Raw('DATE_FORMAT('.$prefix.'projects.enddate, "%d %M %Y") AS enddate'),
                \DB::Raw('`FIRST`('.$prefix.'projects.project_status) AS project_status'),
                \DB::Raw('CONCAT('.$prefix.'users.first_name, " ",'.$prefix.'users.last_name) AS name'),
                'users.company_name',
                \DB::Raw("IF(({$prefix}industries.{$language} != ''),{$prefix}industries.`{$language}`, {$prefix}industries.`en`) as industries_name"),
                \DB::Raw("IF(({$prefix}subindustry.{$language} != ''),{$prefix}subindustry.`{$language}`, {$prefix}subindustry.`en`) as subindustry_name")
                ])
            ->leftJoin('users', 'projects.user_id', '=', 'users.id_user')
            ->leftJoin('industries', 'projects.industry', '=', 'industries.id_industry')
            ->leftJoin('industries AS subindustry', 'projects.subindustry', '=', 'subindustry.id_industry')
            ->where('projects.status', 'active')
            ->where('projects.project_status', 'pending');
            if(!empty($where_data)){
                $projectList->whereRaw($where_data);
            }
            $projectList->limit(10);
            $projectList = $projectList->get()->toArray();

            return json_decode(json_encode($projectList), true);
        }

        /**
         * [This method is used for accepted employer proposal] 
         * @param [Integer]$project_id[Project Id]
         * @return Data Response
         */ 

        public static function accepted_proposal_employer($project_id){
            $project = \DB::table('projects')
            ->select('user_id')
            ->where('id_project',$project_id)
            ->get()
            ->first();

            if(!empty($project->user_id)){
                return $project->user_id;
            }else{
                return false;
            }
        }

        /**
         * [This method is used for getting the price unit for a project] 
         * @param [Integer]$project_id[Project Id]
         * @return Data Response
         */ 

        public static function get_project_price_unit($project_id){
            $project = \DB::table('projects')
            ->select('price_unit')
            ->where('id_project',$project_id)
            ->get()
            ->first();

            if(!empty($project->price_unit)){
                return $project->price_unit;
            }else{
                return DEFAULT_CURRENCY;
            }
        }

        /**
         * [This method is used to find a job] 
         * @param [type]$user[<description>]
         * @param [type]$fetch[<description>]
         * @param [type]$where[<description>]
         * @param [type]$page[<description>]
         * @param [type]$sort[<description>]
         * @param [type]$keys[<description>]
         * @param [type]$having [<description>]
         * @param [type]$limit [<description>]
         * @return Data Response
         */ 

        public static function employer_find_job($request, $user,$page=1,$limit = DEFAULT_PAGING_LIMIT){
            $prefix                 = DB::getTablePrefix();
            $language               = \App::getLocale();
            $base_url               = ___image_base_url();
            $page                   = $page;
            $offset                 = ($page-1)*$limit;            
            $search                 = !empty($request->search)? $request->search : '';

            $keys           = [
                'projects.id_project',
                'projects.user_id as company_id',
                'projects.title',
                'project_language.description',
                'users.company_name',
                'projects.created',
                'projects.project_status',
                'projects.price_unit',
                'projects.employment',
                'projects.expertise',
                'projects.other_perks',
                'projects.created',
                \DB::Raw("
                    IF(
                        {$prefix}files.filename IS NOT NULL,
                        CONCAT('{$base_url}','/',{$prefix}files.folder,{$prefix}files.filename),
                        CONCAT('{$base_url}','/','images/','".DEFAULT_AVATAR_IMAGE."')
                    ) as company_logo
                "),
                \DB::Raw('`CONVERT_PRICE`('.$prefix.'projects.price, '.$prefix.'projects.price_unit, "'.request()->currency.'") AS price'),
                \DB::Raw("DATE({$prefix}projects.startdate) as startdate"),
                \DB::Raw("DATE({$prefix}projects.enddate) as enddate"),
                \DB::Raw("IF({$prefix}saved_jobs.id_saved IS NOT NULL,'".DEFAULT_YES_VALUE."','".DEFAULT_NO_VALUE."') as is_saved"),
                \DB::Raw("{$prefix}proposals_listing.status as job_listing_status"),
            ];

            $projects = Projects::select($keys)->with([
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
                'skills.skills' => function($q) use($language,$prefix){
                    $q->select(
                        'id_skill',
                        'skill_name'
                    );
                }
            ])
            ->leftJoin('users as users','users.id_user','=','projects.user_id')
            ->leftJoin('files as files',function($leftjoin){
                $leftjoin->on('files.user_id','=','projects.user_id');
                $leftjoin->on('files.type','=',\DB::Raw('"profile"'));
            })
            ->leftJoin('project_required_skills as skills','skills.project_id','=','projects.id_project')
            
            ->leftJoin('project_log',function($leftjoin) use($prefix){
                $leftjoin->on('project_log.project_id','=','projects.id_project');
                $leftjoin->on(\DB::Raw("DATE({$prefix}project_log.created)"),'=',\DB::Raw("'".date('Y-m-d')."'"));
            })
            ->leftJoin('project_language', function ($join) {
                $join->on('projects.id_project', '=', 'project_language.project_id')->where('project_language.language', ___cache('default_language'));
            });

            if(!empty($request->employment_type_filter)){
                $projects->whereIn('projects.employment',array_filter($request->employment_type_filter));
            }

            if(!empty($request->price_min_filter) && empty($request->price_max_filter)){
                $projects->havingRaw("(price >= $request->price_min_filter)");
            }else if(empty($request->price_min_filter) && !empty($request->price_max_filter)){
                $projects->havingRaw("(price <= $request->price_max_filter )");
            }else if(!empty($request->price_min_filter) && !empty($request->price_max_filter)){
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
                $projects->havingRaw("
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
                ");  
            }            

            $projects->whereRaw("('".date('Y-m-d')."' <= DATE({$prefix}projects.startdate))");
            $projects->whereNotIn("projects.status",['draft','trash']);
            $projects->groupBy(['projects.id_project']);
            $projects->orderByRaw($sort);

            $jobs = [
                'result'                => $projects->limit($limit)->offset($offset)->get(),
                'total'                 => $projects->get()->count(),
                'total_filtered_result' => $projects->limit($limit)->offset($offset)->get()->count(),
            ];            

            return $projects;
        } 

        public static function total_completed_job_by_talent($user_id){
            return self::defaultKeys()->withCount([
                'proposal' => function($q) use($user_id){
                    $q->where('talent_proposals.user_id',$user_id)->where('talent_proposals.status','accepted');
                }
            ])
            ->whereNotIn('projects.status',['draft','trashed'])
            ->having('proposal_count','>',0)
            ->having('project_status','=','closed')
            ->get()->count();
        }       

        public static function total_posted_job_by_employer($user_id){
            return self::whereNotIn('projects.status',['draft','trashed'])
            ->where('user_id',$user_id)
            ->get()
            ->count();
        }

        public static function create_dummp_job($employer_id,$talent_id){
            $project = self::select('id_project')->where('title',JOB_TITLE)->where('talent_id',$talent_id)->where('user_id',$employer_id)->get()->first();

            if(empty($project->id_project)){
                return self::insertGetId([
                    'title' => JOB_TITLE,
                    'user_id' => $employer_id,
                    'talent_id' => $talent_id,
                    'status' => 'trashed',
                    'created' => date('Y-m-d H:i:s'),
                    'updated' => date('Y-m-d H:i:s'),
                ]);
            }else{
                return $project->id_project;
            }
        }       

        public static function is_invitation_sent($employer_id,$talent_id,$project_id){
            $isInvitationSent = \DB::table('invite')->select('id_invite')->where('employer_id',$employer_id)->where('talent_id',$talent_id)->where('project_id',$project_id)->get();

            if(empty($isInvitationSent->count())){
                \DB::table('invite')->insertGetId([
                    'employer_id' => $employer_id,
                    'talent_id' => $talent_id,
                    'project_id' => $project_id,
                    'created' => date('Y-m-d H:i:s'),
                    'updated' => date('Y-m-d H:i:s'),
                ]);
                return false;
            }else{
                return true;
            }
        }

        public static function is_chat_room_created($employer_id,$talent_id){
            return self::select('id_project')->where('title',JOB_TITLE)->where('talent_id',$talent_id)->where('user_id',$employer_id)->get()->count();
        }

        /**
         * [This method is used 4 suggested jobs] 
         * @param [type]$user[<description>]
         * @return Builder
         */ 

        public static function four_suggested_jobs(){
            $prefix         = DB::getTablePrefix();
            $language       = \App::getLocale();
            $base_url       = ___image_base_url();

            /*Get 1 week previous date*/
            $from_date = date("Y-m-d", strtotime("-1 week"));

            $projects = Projects::defaultKeys()
            ->projectPrice()
            ->projectDescription(true)
            ->companyName()
            ->companyLogo()
            ->whereNotIn('projects.status',['draft','trashed'])
            ->where('projects.startdate', '>=', $from_date)
            ->orderBy('projects.id_project', 'DESC')
            ->limit(4)
            ->get();

            return json_decode(json_encode($projects),true);
        }


	}
