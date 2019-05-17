<?php

    namespace Models; 

    use Illuminate\Database\Eloquent\Model;
    use Illuminate\Support\Facades\DB;
    use Illuminate\Support\Facades\Crypt;
    use Illuminate\Support\Facades\Mail;

    class Listings extends Model{ 

        /**
         * [This method is used for cities] 
         * @param [Fetch]$fetch[Used for fetching]
         * @param [Varchar]$key[Used for Keys]
         * @param [Integer]$state_id[Used for state id ]
         * @param [Integer]$country_id[Used for country id ]
         * @param [String]$where[Used for where clause]
         * @return Json Response
         */ 
               
        public static function cities($fetch = 'array',$keys = array('*'),$state_id = "",$country_id = "",$where="",$page=0,$limit = DEFAULT_CITY_LIMIT,$order_by = NULL){
            $table_cities = DB::table('city');
            
            if(!empty($keys)){
                $table_cities = $table_cities->select($keys); 
            }
            if(!empty($state_id)){
                $table_cities->where('state_id','=',$state_id);
            }

            if(!empty($country_id)){
                $table_cities->where('country_id','=',$country_id);
            }

            if(!empty($where)){
                $table_cities->whereRaw($where);
            }

            if(!empty($order_by)){
                $table_cities->orderBy($order_by,"ASC");
            }

            if(!empty($page)){
                $table_cities->limit($limit);
                $table_cities->offset(($page - 1)*$limit);
            }

            if($fetch === 'array'){
                return json_decode(
                    json_encode(
                        $table_cities->get()
                    ),
                    true
                );
            }else{
                return $table_cities->get();
            }
        }

        /**
         * [This method is used for city list 
         * @param [String]$where[Used for where clause]
         * @param [Varchar]$key[Used for Keys]
         * @param [Fetch]$fetch[Used for fetching]
         * @return Json Response
         */ 

        public static function  city_list($where = "",$keys = array('*'),$fetch="array"){
            $table_cities = DB::table('city');
            DB::statement(DB::raw('set @row_number=0'));
            if(!empty($keys)){
                $table_cities = $table_cities->select($keys); 
            }
        
            $table_cities->whereRaw($where);
            if($fetch == 'obj'){
                $table_cities->leftJoin('countries','countries.id_country','=','city.country_id')
                ->leftJoin('state','state.id_state','=','city.state_id');
                return $table_cities->get();
            }else if($fetch === 'single'){
                return $table_cities->get()->first();
            }else{
                return json_decode(
                    json_encode(
                        $table_cities->get()->toArray()
                    ),
                    true
                );
            }
        }

        /**
         * [This method is used for languages] 
         * @param [Varchar]$key[Used for Keys]
         * @return Json Response
         */ 

        public static function  languages($keys = ['*']){
            $result = json_decode(json_encode(\DB::table('languages')->select($keys)->get()),true);

            return $result;
        }

        /**
         * [This method is used for state list] 
         * @param [String]$where[Used for where clause]
         * @param [Varchar]$key[Used for Keys]
         * @param [Fetch]$fetch[Used for fetching]
         * @return Json Response
         */ 

        public static function  state_list($where = "",$keys = array('*'),$fetch="array"){
            $table_cities = DB::table('state');
            DB::statement(DB::raw('set @row_number=0'));
            if(!empty($keys)){
                $table_cities = $table_cities->select($keys); 
            }
        
            $table_cities->whereRaw($where);
            if($fetch == 'obj'){
                $table_cities->leftJoin('countries','countries.id_country','=','state.country_id');
                return $table_cities->get();
            }else if($fetch == 'first'){
                return $table_cities->first();
            }else{
                return json_decode(
                    json_encode(
                        $table_cities->get()->toArray()
                    ),
                    true
                ); 
            }
        }

        /**
         * [This method is used for industries] 
         * @param [Varchar]$key[Used for Keys]
         * @return Json Response
         */ 

        public static function  industries($keys = array('*')){
            return json_decode(
                json_encode(
                    Industries::with([
                        'children' => function($query) use($keys){
                            /*$query->addSelect($keys);*/
                        }
                    ])->where('parent','0')->select($keys)->get()
                ),
                true
            );
        }

        /**
         * [This method is used for api messages] 
         * @param [Fetch]$fetch[Used for fetching]
         * @param [Varchar]$key[Used for Keys]
         * @param [String]$where[Used for where clause]
         * @return Json Response
         */ 

        public static function  apimessages($fetch = "array", $keys = array('*'),$where = ""){
            $table_api_messages = DB::table('api_messages');

            if(!empty($keys)){
                $table_api_messages->select($keys); 
            }

            if(!empty($where)){
                $table_countries->whereRaw($where); 
            }

            if($fetch === 'array'){
                return json_decode(
                    json_encode(
                        $table_api_messages->get()
                    ),
                    true
                );
            }else{
                return $table_api_messages->get();
            }
        }

        /**
         * [This method is used to add api message] 
         * @param [Varchar]$data[Used for data]
         * @return Boolean
         */ 

        public static function  add_api_message($data){
            $table_api_messages = DB::table('api_messages');

            if(!empty($data)){
                $isInserted = $table_api_messages->insert($data); 
            }

            return (bool)$isInserted;            
        }

        /**
         * [This method is used for record_api_request] 
         * @param [Varchar]$data[Used for data]]
         * @param Request
         * @return Boolean
         */ 

        public static function  record_api_request($data,$request){
            $data['user_id'] = NULL; $data['user_type'] = NULL;
            if(!empty($request->user()->id_user)){
                $data['user_id'] = $request->user()->id_user;
                $data['user_type'] = $request->user()->type;
            }

            $table_api_request = DB::table('api_request');

            if(!empty($data)){
                $isInserted = $table_api_request->insert($data); 
            }

            return (bool)$isInserted; 
            return true;           
        }

        /**
         * [This method is used for twilio_response] 
         * @param [Varchar]$data[Used for data]]
         * @return Boolean
         */ 

        public static function  twilio_response($data){
            $table_api_twilio_response = DB::table('api_twilio_response');

            if(!empty($data)){
                $isInserted = $table_api_twilio_response->insert($data); 
            }

            return (bool)$isInserted;            
        }

        /**
         * [This method is used for countries] 
         * @param [Fetch]$fetch[Used for fetching]
         * @param [Varchar]$key[Used for Keys]
         * @param [String]$where[Used for where clause]
         * @param [String]$order_by[Used for sorting]
         * @return Json Response
         */ 

        public static function  countries($fetch = 'array', $keys = ['*'], $where = "", $order_by = 'country_order',$page=0,$limit = DEFAULT_COUNTRY_LIMIT){
            $table_countries = DB::table('countries');
            DB::statement(DB::raw('set @row_number=0'));
            if(!empty($keys)){
                $table_countries->select($keys); 
            }

            if(!empty($where)){
                $table_countries->whereRaw($where); 
            }
            
            $table_countries->where('status','!=','trashed'); 
            
            if(!empty($page)){
                $table_countries->limit($limit);
                $table_countries->offset(($page - 1)*$limit);
            }

            $table_countries->orderBy($order_by); 

            if($fetch === 'array'){
                return json_decode(
                    json_encode(
                        $table_countries->get()
                    ),
                    true
                );
            }else if($fetch === 'single'){
                return $table_countries->get()->first();
            }else{
                return $table_countries->get();
            }
        }

        /**
         * [This method is used for talents] 
         * @param [Fetch]$fetch[Used for fetching]
         * @param [Varchar]$key[Used for Keys]
         * @param [String]$where[Used for where clause]
         * @param [String]$order_by[Used for sorting]
         * @return Json Response
         */ 

        public static function talents($fetch = 'array', $keys = ['*'], $where = "", $order_by = 'id_user',$page=0,$limit = DEFAULT_COUNTRY_LIMIT){
            $table_users = DB::table('users');
            DB::statement(DB::raw('set @row_number=0'));
            if(!empty($keys)){
                $table_users->select($keys); 
            }

            if(!empty($where)){
                $table_users->whereRaw($where); 
            }
            
            $table_users->where('status','!=','trashed'); 
            
            if(!empty($page)){
                $table_users->limit($limit);
                $table_users->offset(($page - 1)*$limit);
            }

            $table_users->orderBy($order_by); 

            if($fetch === 'array'){
                return json_decode(
                    json_encode(
                        $table_users->get()
                    ),
                    true
                );
            }else if($fetch === 'single'){
                return $table_users->get()->first();
            }else{
                return $table_users->get();
            }
        }

        /**
         * [This method is used for employers] 
         * @param [Fetch]$fetch[Used for fetching]
         * @param [Varchar]$key[Used for Keys]
         * @param [String]$where[Used for where clause]
         * @param [String]$order_by[Used for sorting]
         * @return Json Response
         */ 

        public static function employers($fetch = 'array', $keys = ['*'], $where = "", $order_by = 'id_user',$page=0,$limit = DEFAULT_COUNTRY_LIMIT){
            $table_users = DB::table('users');
            DB::statement(DB::raw('set @row_number=0'));
            if(!empty($keys)){
                $table_users->select($keys); 
            }

            if(!empty($where)){
                $table_users->whereRaw($where); 
            }
            
            $table_users->where('status','!=','trashed'); 
            
            if(!empty($page)){
                $table_users->limit($limit);
                $table_users->offset(($page - 1)*$limit);
            }

            $table_users->orderBy($order_by); 

            if($fetch === 'array'){
                return json_decode(
                    json_encode(
                        $table_users->get()
                    ),
                    true
                );
            }else if($fetch === 'single'){
                return $table_users->get()->first();
            }else{
                return $table_users->get();
            }
        }

        /**
         * [This method is used for states] 
         * @param [Fetch]$fetch[Used for fetching]
         * @param [Varchar]$key[Used for Keys]
         * @param [String]$where[Used for where clause]
         * @param [String]$order_by[Used for sorting]
         * @return Json Response
         */ 

        public static function  states($fetch = 'array', $keys = ['*'], $where = "", $order_by = '',$page=0,$limit = DEFAULT_STATE_LIMIT){
            $table_states = DB::table('state');

            if(!empty($keys)){
                $table_states->select($keys); 
            }

            if(!empty($where)){
                $table_states->whereRaw($where); 
            }
            
            if(!empty($page)){
                $table_states->limit($limit);
                $table_states->offset(($page - 1)*$limit);
            }

            if($order_by){
                $table_states->orderByRaw($order_by); 
            }
            
            if($fetch === 'array'){
                return json_decode(
                    json_encode(
                        $table_states->get()
                    ),
                    true
                );
            }else if($fetch === 'single'){
                return $table_states->get()->first();
            }else{
                return $table_states->get();
            }
        }

        /**
         * [This method is used for country_state list] 
         * @param [Integer]$country_id[Used for country id ]
         * @return Boolean
         */ 

        public static function  country_state_list($country_id){
            $table_states = DB::table('state');
            $language     = \App::getLocale();
            $table_states->select(
                'id_state',
                \DB::Raw("IF(({$language} != ''),`{$language}`, `en`) as state_name")
            ); 

            if(!empty($country_id)){
                $table_states->whereRaw("country_id = {$country_id}"); 
            }
            
            $table_states->orderBy('state_order','ASC'); 

            return json_decode(
                json_encode(
                    $table_states->get()
                ),
                true
            );
        }

        /**
         * [This method is used for state_city list] 
         * @param [Integer]$state_id[Used for state id ]
         * @return Json Response
         */ 

        public static function  state_city_list($state_id){
            $table_city = DB::table('city');
            $language   = \App::getLocale();
            $table_city->select(
                'id_city',
                DB::Raw("IF(({$language} != ''),`{$language}`, `en`) as city_name") 
            ); 

            if(!empty($state_id)){
                $table_city->whereRaw("state_id = {$state_id}"); 
            }
            
            $table_city->orderBy(DB::Raw("IF(({$language} != ''),`{$language}`, `en`)"),'ASC'); 

            return json_decode(
                json_encode(
                    $table_city->get()
                ),
                true
            );
        }

        /**
         * [This method is used for industry_subindustry_list] 
         * @param [Integer]$industry_id[Used for industry id ]
         * @return Json Response
         */ 

        public static function  industry_subindustry_list($industry_id){
            $language           = \App::getLocale();
            $table_industry     = DB::table('industries');
            $table_industry->select(
                'id_industry',
                DB::Raw("IF(({$language} != ''),`{$language}`, `en`) as name")
            ); 

            if(!empty($industry_id)){
                $table_industry->whereRaw(" parent = {$industry_id} AND status = 'active' "); 
            }
            
            $table_industry->orderBy(DB::Raw("IF(({$language} != ''),`{$language}`, `en`)"),'ASC'); 

            return json_decode(
                json_encode(
                    $table_industry->get()
                ),
                true
            );
        }

        /**
         * [This method is used for skills(] 
         * @param [Fetch]$fetch[Used for fetching]
         * @param [Varchar]$key[Used for Keys]
         * @param [String]$where[Used for where clause]
         * @param [String]$order_by[Used for sorting]
         * @return Data Response
         */ 

        public static function  skills($fetch = 'array', $keys = ['*'], $where = "", $order_by = 'skill_name'){
            $table_skills = DB::table('skill');

            if(!empty($keys)){
                $table_skills->select($keys); 
            }

            if(!empty($where)){
                $table_skills->whereRaw($where); 
            }
            
            $table_skills->orderBy($order_by); 

            if($fetch === 'array'){
                return json_decode(
                    json_encode(
                        $table_skills->get()
                    ),
                    true
                );
            }else{
                return $table_skills->get();
            }
        }

        /**
         * [This method is used for certificates] 
         * @param [Fetch]$fetch[Used for fetching]
         * @param [Varchar]$key[Used for Keys]
         * @param [String]$where[Used for where clause]
         * @param [String]$order_by[Used for sorting]
         * @return Data Response
         */ 
        
        public static function  certificates($fetch = 'array', $keys = ['*'], $where = "", $order_by = 'certificate_name'){
            $table_certificate = DB::table('certificate');
            DB::statement(DB::raw('set @row_number=0'));

            if(!empty($keys)){
                $table_certificate->select($keys); 
            }

            if(!empty($where)){
                $table_certificate->whereRaw($where); 
            }
            
            $table_certificate->orderBy($order_by); 

            if($fetch === 'array'){
                return json_decode(
                    json_encode(
                        $table_certificate->get()
                    ),
                    true
                );
            }else if($fetch === 'obj'){
                return $table_certificate->get();
            }else if($fetch === 'single'){
                return $table_certificate->get()->first();
            }else{
                return $table_certificate->get();
            }
        }

        /**
         * [This method is used to add certificate] 
         * @param [Varchar]$data[Used for data]]
         * @return Boolean
         */ 

        public static function  add_certificate($data){
            $table_certificate = DB::table('certificate');

            if(!empty($data)){
                $isInserted = $table_certificate->insert($data); 
            }

            return (bool)$isInserted;
        }     

        /**
         * [This method is used to update certificate] 
         * @param [Integer]$id_certificate[Used for certificate id]
         * @param [Varchar]$data[Used for data]]
         * @return Boolean
         */    

        public static function  update_certificate($id_certificate, $data){
            $table_certificate = DB::table('certificate');

            if(!empty($data)){
                $table_certificate->where('id_cetificate','=',$id_certificate);
                $isUpdated = $table_certificate->update($data); 
            }
            
            return (bool)$isUpdated;
        }         

        /**
         * [This method is used for colleges] 
         * @param [Fetch]$fetch[Used for fetching]
         * @param [Varchar]$key[Used for Keys]
         * @param [String]$where[Used for where clause]
         * @param [String]$order_by[Used for sorting]
         * @return Json Response
         */        
        
        public static function  colleges($fetch = 'array', $keys = ['*'], $where = "", $order_by = 'college_name'){
            $table_college = DB::table('college');
            DB::statement(DB::raw('set @row_number=0'));
            if(!empty($keys)){
                $table_college->select($keys); 
            }

            if(!empty($where)){
                $table_college->whereRaw($where); 
            }
            
            $table_college->orderBy($order_by); 

            if($fetch === 'array'){
                return json_decode(
                    json_encode(
                        $table_college->get()
                    ),
                    true
                );
            }else if($fetch === 'single'){
                return $table_college->get()->first();
            }else{
                return $table_college->get();
            }
        }

        /**
         * [This method is used to add college] 
         * @param [Varchar]$data[Used for data]]
         * @return Boolean
         */ 

        public static function  add_college($data){
            $table_college = DB::table('college');

            if(!empty($data)){
                $isInserted = $table_college->insert($data); 
            }

            return (bool)$isInserted;
        }     

        /**
         * [This method is used to update college] 
         * @param [Integer]$id_college[Used for college id]
         * @param [Varchar]$data[Used for data]
         * @return Boolean
         */    

        public static function  update_college($id_college, $data){
            $table_college = DB::table('college');

            if(!empty($data)){
                $table_college->where('id_college','=',$id_college);
                $isUpdated = $table_college->update($data); 
            }
            
            return (bool)$isUpdated;
        }

        /**
         * [This method is used for company] 
         * @param [Fetch]$fetch[Used for fetching]
         * @param [Varchar]$key[Used for Keys]
         * @param [String]$where[Used for where clause]
         * @param [String]$order_by[Used for sorting]
         * @return Json Response
         */        
        
        public static function  companies($fetch = 'array', $keys = ['*'], $where = "", $order_by = 'company_name'){
            $table_company = DB::table('company');
            DB::statement(DB::raw('set @row_number=0'));
            if(!empty($keys)){
                $table_company->select($keys); 
            }

            if(!empty($where)){
                $table_company->whereRaw($where); 
            }
            
            $table_company->orderBy($order_by); 

            if($fetch === 'array'){
                return json_decode(
                    json_encode(
                        $table_company->get()
                    ),
                    true
                );
            }else if($fetch === 'single'){
                return $table_company->get()->first();
            }else{
                return $table_company->get();
            }
        }

        /**
         * [This method is used to add college] 
         * @param [Varchar]$data[Used for data]]
         * @return Boolean
         */ 

        public static function  add_company($data){
            $table_company = DB::table('company');

            if(!empty($data)){
                $isInserted = $table_company->insert($data); 
            }

            return (bool)$isInserted;
        }     

        /**
         * [This method is used to update college] 
         * @param [Integer]$id_college[Used for college id]
         * @param [Varchar]$data[Used for data]
         * @return Boolean
         */    

        public static function  update_company($id_company, $data){
            $table_company = DB::table('company');

            if(!empty($data)){
                $table_company->where('id_company','=',$id_company);
                $isUpdated = $table_company->update($data); 
            }
            
            return (bool)$isUpdated;
        }   

        /**
         * [This method is used for job titles] 
         * @param [fetch]$fetch[Used for fetching]
         * @param [Varchar]$key[Used for Keys]
         * @param [String]$where[Used for where clause]
         * @param [String]$order_by[Used for sorting]
         * @return Json Response
         */ 

        public static function  job_titles($fetch = 'array', $keys = ['*'], $where = "", $order_by = 'job_title_name'){
            $table_job_title = DB::table('job_title');

            if(!empty($keys)){
                $table_job_title->select($keys); 
            }

            if(!empty($where)){
                $table_job_title->whereRaw($where); 
            }
            
            $table_job_title->orderBy($order_by); 

            if($fetch === 'array'){
                return json_decode(
                    json_encode(
                        $table_job_title->get()
                    ),
                    true
                );
            }else{
                return $table_job_title->get();
            }
        }

        /**
         * [This method is used for degrees] 
         * @param [fetch]$fetch[Used for fetching]
         * @param [Varchar]$key[Used for Keys]
         * @param [String]$where[Used for where clause]
         * @param [String]$order_by[Used for sorting]
         * @return Json Response
         */ 

        public static function  degrees($fetch = 'array', $keys = ['*'], $where = "", $order_by = 'degree_name'){
            $table_degrees = DB::table('degree');
            DB::statement(DB::raw('set @row_number=0'));

            if(!empty($keys)){
                $table_degrees->select($keys); 
            }

            if(!empty($where)){
                $table_degrees->whereRaw($where); 
            }
            
            $table_degrees->orderBy($order_by); 

            if($fetch === 'array'){
                return json_decode(
                    json_encode(
                        $table_degrees->get()
                    ),
                    true
                );
            }else if($fetch === 'single'){
                return $table_degrees->get()->first();
            }else{
                return $table_degrees->get();
            }
        }

        /**
         * [This method is used to add degree] 
         * @param [Varchar]$data[Used for data]
         * @return Boolean
         */ 

        public static function  add_degree($data){
            $table_degree = DB::table('degree');

            if(!empty($data)){
                $isInserted = $table_degree->insert($data); 
                $cache_key  = ['degree_name'];
                forget_cache($cache_key);
            }

            return (bool)$isInserted;
        }

        /**
         * [This method is used for remove] 
         * @param [Integer]$id_degree[Used for degree id ]
         * @param [Varchar]$data[Used for data]
         * @return Boolean
         */ 

        public static function  update_degree($id_degree, $data){
            $table_degree = DB::table('degree');

            if(!empty($data)){
                $table_degree->where('id_degree','=',$id_degree);
                $isUpdated = $table_degree->update($data); 
                $cache_key = ['degree_name'];
                forget_cache($cache_key);
            }
            
            return (bool)$isUpdated;
        }

        /**
         * [This method is used for workfields] 
         * @param [fetch]$fetch[Used for fetching]
         * @param [Varchar]$key[Used for Keys]
         * @param [String]$where[Used for where clause]
         * @param [String]$order_by[Used for sorting]
         * @return Json Response
         */ 

        public static function  workfields($fetch = 'array', $keys = ['*'], $where = "", $order_by = 'field_name'){
            $table_workfields = DB::table('workfield');

            if(!empty($keys)){
                $table_workfields->select($keys); 
            }

            if(!empty($where)){
                $table_workfields->whereRaw($where); 
            }
            
            $table_workfields->orderBy($order_by); 

            if($fetch === 'array'){
                return json_decode(
                    json_encode(
                        $table_workfields->get()
                    ),
                    true
                );
            }else if($fetch === 'single'){
                return $table_workfields->get()->first();
            }else{
                return $table_workfields->get();
            }
        }

        /**
         * [This method is used for abusive word] 
         * @param [fetch]$fetch[Used for fetching]
         * @param [String]$where[Used for where clause]
         * @param [Varchar]$key[Used for Keys]
         * @return Json Response
         */ 

        public static function  abusive_words($fetch="array", $where="",$keys=['*']){
            $table_abusive_words = DB::table('abusive_words');
            DB::statement(DB::raw('set @row_number=0'));
            if(!empty($keys)){
                $table_abusive_words->select($keys); 
            }
            $table_abusive_words->whereRaw($where);
            if($fetch == "array"){
                $result = json_decode(
                    json_encode(
                        $table_abusive_words->get()
                    ),
                    true
                ); 
            }else if($fetch == "obj"){
                $result = $table_abusive_words->get();
            }else if($fetch == "single"){
                $result = $table_abusive_words->get()->first();
            }else{
                $result = $table_abusive_words->get();
            }
            
            return $result;
        }

        /**
         * [This method is used to add abusive words] 
         * @param [Varchar]$data[Used for data]
         * @return Boolean
         */ 

        public static function add_abusive_words($data){
            $table_abusive_words = DB::table('abusive_words');

            if(!empty($data)){
                $isInserted = $table_abusive_words->insert($data);
                $cache_key = ['abusive_words'];
                forget_cache($cache_key);
            }

            return (bool)$isInserted;
        }

        /**
         * [This method is used to update abusive words] 
         * @param [Integer]$id_words[Used for words id ]
         * @param [Varchar]$data[Used for data]
         * @return Boolean
         */ 

        public static function  update_abusive_words($id_words, $data){
            $table_abusive_words = DB::table('abusive_words');

            if(!empty($data)){
                $table_abusive_words->where('id_words','=',$id_words);
                $isUpdated = $table_abusive_words->update($data);
                $cache_key = ['abusive_words'];
                forget_cache($cache_key);
            }
            
            return (bool)$isUpdated;
        }      

        /**
         * [This method is used for adding country] 
         * @param [Varchar]$data[Used for data]
         * @return Boolean
         */   

        public static function  add_country($data){
            $table_countries = DB::table('countries');

            if(!empty($data)){
                $isInserted = $table_countries->insert($data);
                $cache_key = ['country_name','phone_codes','countries','country_phone_codes'];
                forget_cache($cache_key);
            }

            return (bool)$isInserted;
        }

        /**
         * [This method is used to update country] 
         * @param [Integer]$country_id[Used for country id ]
         * @param [Varchar]$data[Used for data]
         * @return Boolean
         */ 

        public static function  update_country($country_id, $data){
            $table_countries = DB::table('countries');
            if(!empty($data)){
                $table_countries->where('id_country','=',$country_id);
                $isUpdated = $table_countries->update($data);
                $cache_key = ['country_name','phone_codes','countries','country_phone_codes'];
                forget_cache($cache_key);
            }
            return (bool)$isUpdated;
        }

        /**
         * [This method is used to add state] 
         * @param [Varchar]$data[Used for data]
         * @return Boolean
         */ 

        public static function  add_state($data){
            $table_state = DB::table('state');

            if(!empty($data)){
                $isInserted = $table_state->insert($data); 
                $cache_key = ['state_name','states'];
                forget_cache($cache_key);                
            }

            return (bool)$isInserted;
        }

        /**
         * [This method is used to update state] 
         * @param [Integer]$state_id[Used for state id ]
         * @param [Varchar]$data[Used for data]
         * @return Boolean
         */ 

        public static function  update_state($state_id, $data){
            $table_state = DB::table('state');

            if(!empty($data)){
                $table_state->where('id_state','=',$state_id);
                $isUpdated = $table_state->update($data);
                $cache_key = ['state_name','states'];
                forget_cache($cache_key);                 
            }
            
            return (bool)$isUpdated;
        }

        /**
         * [This method is used to add city] 
         * @param [Varchar]$data[Used for data]
         * @return Boolean
         */ 

        public static function  add_city($data){
            $table_city = DB::table('city');

            if(!empty($data)){
                $isInserted = $table_city->insert($data); 
            }

            return (bool)$isInserted;
        }

        /**
         * [This method is used update city] 
         * @param [Integer]$city_id[Used for city id ]
         * @param [Varchar]$data[Used for data]
         * @return Boolean
         */ 

        public static function  update_city($city_id, $data){
            $table_city = DB::table('city');

            if(!empty($data)){
                $table_city->where('id_city','=',$city_id);
                $isUpdated = $table_city->update($data); 
            }
            
            return (bool)$isUpdated;
        }  

        /**
         * [This method is used to update setting] 
         * @param [Varchar]$key[Used for Keys]
         * @param [Integer]$value[Used for value]
         * @return Boolean
         */               

        public static function update_setting($key, $value){
            $isUpdated = DB::table('config')->where('key', $key)->update(['value' => $value]);
            return (bool)$isUpdated;
        }

        /**
         * [This method is used for emails] 
         * @param [fetch]$fetch[Used for fetching]
         * @param [Varchar]$key[Used for Keys]
         * @param [String]$where[Used for where clause]
         * @param [String]$order_by[Used for sorting]
         * @return Boolean
         */ 
        
        public static function  emails($fetch = 'array', $keys = array('*'),$where = "", $order_by = 'id_email'){
            $table_emails = DB::table('emails');

            if(!empty($keys)){
                $table_emails->select($keys); 
            }

            if(!empty($where)){
                $table_emails->whereRaw($where); 
            }
            
            $table_emails->orderBy($order_by); 

            if($fetch === 'array'){
                return json_decode(
                    json_encode(
                        $table_emails->get()
                    ),
                    true
                );
            }else if($fetch === 'first'){
                return json_decode(
                    json_encode(
                        $table_emails->first()
                    ),
                    true
                );
            }else{
                return $table_emails->get();
            }
        }

        /**
         * [This method is used for project status] 
         * @param null
         * @return Json Response
         */ 

        public static function  project_status_column(){
            $prefix = DB::getTablePrefix();
            $result = DB::select("
                SELECT REPLACE(REPLACE(REPLACE(COLUMN_TYPE,'enum(',''),')',''),'\'','') as column_type
                FROM `information_schema`.`COLUMNS` 
                WHERE TABLE_NAME = '{$prefix}projects' AND TABLE_SCHEMA = '".env("DB_DATABASE")."'
                AND COLUMN_NAME = 'project_status' 
            ");

            if(!empty($result[0])){
                return explode(",",json_decode(json_encode($result[0]->column_type),true));
            }else{
                return [];
            }
        }

        /**
         * [This method is used to raise dispute type column] 
         * @param null
         * @return Json Response
         */ 

        public static function  raise_dispute_type_column(){
            $prefix = DB::getTablePrefix();
            $result = DB::select("
                SELECT REPLACE(REPLACE(REPLACE(COLUMN_TYPE,'enum(',''),')',''),'\'','') as column_type
                FROM `information_schema`.`COLUMNS` 
                WHERE TABLE_NAME = '{$prefix}projects_dispute' AND TABLE_SCHEMA = '".env("DB_DATABASE")."'
                AND COLUMN_NAME = 'type' 
            ");

            if(!empty($result[0])){
                return explode(",",json_decode(json_encode($result[0]->column_type),true));
            }else{
                return [];
            }
        }

        /**
         * [This method is used for users] 
         * @param [fetch]$fetch[Used for fetching]
         * @param [Varchar]$key[Used for Keys]
         * @param [String]$where[Used for where clause]
         * @param [String]$order_by[Used for sorting]
         * @return Json Response
         */ 

        public static function  users($fetch = 'array', $keys = array('*'),$where = "", $order_by = 'id_user'){
            $table_users = DB::table('users');

            if(!empty($keys)){
                $table_users->select($keys);
            }

            if(!empty($where)){
                $table_users->whereRaw($where);
            }

            $table_users->orderBy($order_by);

            if($fetch === 'array'){
                return json_decode(
                    json_encode(
                        $table_users->get()
                    ),
                    true
                );
            }else if($fetch === 'first'){
                return json_decode(
                    json_encode(
                        $table_users->first()
                    ),
                    true
                );
            }else{
                return $table_users->get();
            }
        }

        /**
         * [This method is used to update user] 
         * @param [Integer]$user_id [Used for user id]
         * @param [Varchar]$data[Used for data]
         * @return Boolean
         */ 

        public static function  update_user($id_user, $data){
            $table_users = DB::table('users');

            if(!empty($data)){
                $table_users->where('id_user','=',$id_user);
                $isUpdated = $table_users->update($data);
            }

            return (bool)$isUpdated;
        }

        /**
         * [This method is used to getCountry] 
         * @param null
         * @return Data Response
         */ 

        public static function getCountry(){
            $language     = \App::getLocale();
            return DB::table('countries')
                ->select(
                    'id_country',
                    DB::Raw("IF(({$language} != ''),`{$language}`, `en`) as country_name"),
                    'phone_country_code'
                )
                ->where('status','=','active')
                ->orderBy('country_name','ASC')
                ->get()
                ->toArray();
        }

        /**
         * [This method is used to getStateByCountryID] 
         * @param [Integer]$id_country[Used for country id]
         * @return Data Response
         */ 

        public static function getStateByCountryID($id_country){
            $language = \App::getLocale();
            return DB::table('state')
                ->select(
                    'id_state',
                    DB::Raw("IF(({$language} != ''),`{$language}`, `en`) as state_name")
                )
                ->where('country_id',$id_country)
                ->orderBy('state_order','ASC')
                ->get()
                ->toArray();
        }

        /**
         * [This method is used to getCityByStateID] 
         * @param [Integer]$id_state[Used for state id]
         * @return Data Response
         */ 

        public static function getCityByStateID($id_state){
            $language     = \App::getLocale();
            return DB::table('city')
                ->select(
                    'id_city',
                    DB::Raw("IF(({$language} != ''),`{$language}`, `en`) as city_name")
                )
                ->where('state_id',$id_state)
                ->orderBy('city_order','ASC')
                ->get()
                ->toArray();
        }

        /**
         * [This method is used for getting Industry] 
         * @param null
         * @return Data Response
         */ 

        public static function getIndustry(){
            $language     = \App::getLocale();
            return DB::table('industries')
                ->select(
                    'id_industry',
                    DB::Raw("IF(({$language} != ''),`{$language}`, `en`) as name")
                )
                ->where('status','active')
                ->orderBy('industries_order','ASC')
                ->get()
                ->toArray();
        }

        /**
         * [This method is used for remove] 
         * @param [Integer]$id_industry[Used for industry id]
         * @return Data Response
         */ 

        public static function getSubIndustry($id_industry){
            $language     = \App::getLocale();
            return DB::table('industries')
                ->select('id_industry',
                    DB::Raw("IF(({$language} != ''),`{$language}`, `en`) as name")
                )
                ->where('parent', $id_industry)
                ->where('status','active')
                ->orderBy('industries_order','ASC')
                ->get()
                ->toArray();
        }

        /**
         * [This method is used for remove] 
         * @param [Integer]$user_id [Used for user id]
         * @return Data Response
         */ 

        public static function getTalentSkill($id_user){
            return DB::table('talent_skills')
                ->select('skill')
                ->where('user_id', $id_user)
                ->orderBy('skill','ASC')
                ->get()
                ->toArray();
        }

        /**
         * [This method is used to getTalentCertificates] 
         * @param [Integer]$user_id [Used for user id]
         * @return Data Response
         */ 

        public static function getTalentCertificates($id_user){
            return DB::table('talent_certificates')
                ->select('certificate')
                ->where('user_id', $id_user)
                ->orderBy('certificate','ASC')
                ->get()
                ->toArray();
        }

        /**
         * [This method is used to getDegree] 
         * @param null
         * @return Data Response
         */ 

        public static function getDegree(){
            return DB::table('degree')
                ->select('id_degree','degree_name')
                ->where('degree_status', 'active')
                ->orderBy('degree_name','ASC')
                ->get()
                ->toArray();
        }

        /**
         * [This method is used for remove] 
         * @param [Integer]$id_industry[Used for industry id]
         * @return Json Response
         */ 

        public static function getSkillByIndustry($id_industry){
            $skills = DB::table('skill')
            ->select('id_skill','skill_name')
            ->where('skill_status', 'active')
            ->where('industry_id', $id_industry)
            ->orderBy('skill_name','ASC')
            ->get();

            return json_decode(json_encode($skills),true);
        }

        /**
         * [This method is used to getCertificate] 
         * @param null
         * @return Json Response
         */ 

        public static function getCertificate(){
            $certificate = DB::table('certificate')
            ->select('id_cetificate','certificate_name')
            ->where('certificate_status', 'active')
            ->orderBy('certificate_name','ASC')
            ->get();

            return json_decode(json_encode($certificate),true);
        }

        /**
         * [This method is used to getUserCertificate] 
         * @param [Integer]$user_id [Used for user id]
         * @return Json Response
         */ 

        public static function getUserCertificate($id_user){
            $certificate = DB::table('talent_certificates')
            ->where('user_id', $id_user)
            ->orderBy('certificate','ASC')
            ->get();

            return json_decode(json_encode($certificate),true);
        }

        /**
         * [This method is used to getWorkFieldByID] 
         * @param [Integer]$id_workfield[Used for work field id]
         * @return Json Response
         */ 

        public static function getWorkFieldByID($id_workfield){
            $workfield = DB::table('workfield')
            ->where('id_workfield', $id_workfield)
            ->get()
            ->first();

            return json_decode(json_encode($workfield),true);
        }

        /**
         * [This method is used for pages] 
         * @param [fetch]$fetch[Used for fetching]
         * @param [Varchar]$key[Used for Keys]
         * @param [String]$where[Used for where clause]
         * @param [String]$order_by[Used for sorting]
         * @return Json Response
         */ 

        public static function  pages($fetch = 'array', $keys = array('*'),$where = "", $order_by = 'id'){
            $table_pages = DB::table('pages');

            if(!empty($keys)){
                $table_pages->select($keys);
            }

            if(!empty($where)){
                $table_pages->whereRaw($where);
            }

            $table_pages->orderBy($order_by);

            if($fetch === 'array'){
                return json_decode(
                    json_encode(
                        $table_pages->get()
                    ),
                    true
                );
            }else if($fetch === 'first'){
                return json_decode(
                    json_encode(
                        $table_pages->first()
                    ),
                    true
                );
            }else{
                return $table_pages->get();
            }
        }

        /**
         * [This method is used for messages] 
         * @param [fetch]$fetch[Used for fetching]
         * @param [Varchar]$key[Used for Keys]
         * @param [String]$where[Used for where clause]
         * @param [String]$order_by[Used for sorting]
         * @return Json Response
         */ 

        public static function  messages($fetch = 'array', $keys = array('*'),$where = "", $order_by = 'id_message'){
            $table_messages = DB::table('messages');

            if(!empty($keys)){
                $table_messages->select($keys);
            }

            if(!empty($where)){
                $table_messages->whereRaw($where);
            }

            $table_messages->orderBy($order_by);

            if($fetch === 'array'){
                return json_decode(
                    json_encode(
                        $table_messages->get()
                    ),
                    true
                );
            }else if($fetch === 'first'){
                return json_decode(
                    json_encode(
                        $table_messages->first()
                    ),
                    true
                );
            }else{
                return $table_messages->get();
            }
        }

        /**
         * [This method is used to get Skill with Industry] 
         * @param [fetch]$fetch[Used for fetching]
         * @param [Varchar]$key[Used for Keys]
         * @param [String]$where[Used for where clause]
         * @param [String]$order_by[Used for sorting]
         * @return Json Response
         */ 

        public static function getSkillwithIndustry($fetch = 'array', $keys = ['*'], $where = "", $order_by = 'skill_name'){
            $table_skills = DB::table('skill');
            DB::statement(DB::raw('set @row_number=0'));
            if(!empty($keys)){
                $table_skills->select($keys); 
            }

            if(!empty($where)){
                $table_skills->whereRaw($where); 
            }
            
            $table_skills->orderBy($order_by);
            if($fetch === 'array'){
                return json_decode(
                    json_encode(
                        $table_skills->get()
                    ),
                    true
                );
            }else if($fetch === 'single'){
                return $table_skills->get()->first();
            }else{
                return $table_skills->get();
            }
        }

        /**
         * [This method is used for adding skill] 
         * @param [Varchar]$data[Used for data]
         * @return Boolean
         */ 

        public static function  add_skill($data){
            $table_skill = DB::table('skill');

            if(!empty($data)){
                $isInserted = $table_skill->insert($data); 
            }

            return (bool)$isInserted;
        }

        /**
         * [This method is used to update skill] 
         * @param [Integer]$skill_id[Used for skill id]
         * @param [Varchar]$data[Used for data]
         * @return Boolean
         */ 

        public static function  update_skill($skill_id, $data){
            $table_skill = DB::table('skill');

            if(!empty($data)){
                $table_skill->where('id_skill','=',$skill_id);
                $isUpdated = $table_skill->update($data); 
            }
            
            return (bool)$isUpdated;
        }

        /**
         * /
         * [This method is used to alter user table]
         * @param  [Interger or double] $commission [Numeric value for commission set by admin]
         * @param  [string] $commission_type [commission type set by admin]
         * @return  none
         */
        
        public static function alterUserTable($commission, $commission_type){
            $prefix = DB::getTablePrefix();

            DB::table('users')->update([
                'commission' => $commission,
                'commission_type' => $commission_type
            ]);

            DB::statement("
                ALTER TABLE `".$prefix."users` CHANGE `commission` `commission` DOUBLE(8,2) NULL DEFAULT '".addslashes($commission)."', CHANGE `commission_type` `commission_type` ENUM('flat','per') CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '".addslashes($commission_type)."'
            ");

        }

        /**
         * [This method is used to getConvertPrice] 
         * @param [Integer]$price [Used for price]
         * @param [Integer]$currency[Used for currency]
         * @return Json Response
         */ 

        public static function getConvertPrice($price, $currency){
            $result = DB::select("SELECT
                `CONVERT_PRICE`(".$price.", '".\Cache::get('default_currency')."', '".$currency."') AS max_price
            ");
            $result = json_decode(json_encode($result), true);

            if(!empty($result)){
                return $result[0]['max_price'];
            }
            else{
                return $price;
            }
        }

        /**
         * [This method is used to get Max project price, talent price or premium talent price]
         * @param  [string] $type [refer to the table]
         * @return Data Response
         */
        
        public static function getMaxPrice($type){
            $prefix = DB::getTablePrefix();
            if($type == 'project'){
                $where = "
                    (
                        (
                            {$this->prefix}projects.project_status = 'pending'
                            OR
                            {$this->prefix}projects.employment = 'fulltime'
                        )
                    )
                ";

                return DB::table('projects')
                ->whereRaw("({$prefix}projects.employment = 'fulltime' OR '".date('Y-m-d')."' <= DATE({$prefix}projects.startdate))")
                ->whereRaw($where)
                ->max('price');
            }
            else{
                $result = [
                    'expected_salary' => DB::table('users')->where('type', $type)->where('status', 'active')->max('expected_salary'),
                    'workrate' => DB::table('users')->where('type', $type)->where('status', 'active')->max('workrate'),
                    'workrate_max' => DB::table('users')->where('type', $type)->where('status', 'active')->max('workrate_max')
                ];
                return $result;
            }
        }

        /**
         * [This method is used histories activity] 
         * @param [String]$where[Used for where clause]
         * @param [String]$order_by[Used for sorting]
         * @param [String]$order[Used for where order]
         * @return Data Response
         */ 

        public static function activity_histories($where = "1",$order_by = 'id_activity',$order = 'ASC'){
            $prefix             = DB::getTablePrefix();
            $table_activity     = \DB::table('activity');
            $table_activity->select([
                \DB::raw("COALESCE({$prefix}users.id_user) user_id"),
                \DB::raw("COALESCE({$prefix}users.name) name"),
                \DB::raw("COALESCE({$prefix}users.email) email"),
                'user_type',
                'activity.updated',
                'activity.action as activity'
            ])
            ->leftJoin('users','users.id_user','activity.user_id')
            ->whereRaw($where)
            ->where('activity_status','=','success')
            ->orderBy($order_by,$order);
            if(!empty($count)){
                return $table_activity->get()->count();
            }else{
                return $table_activity->get();
            }
        }

        /**
         * [This method is used for faq topic] 
         * @param [String]$fetch[Used for return data type]
         * @param [String]$where[Used for where clause]
         * @param [array]$keys[select keys]
         * @param [String]$order_by[Used for sorting]
         * @param [String]$order[Used for where order]
         * @return Data Response
         */

        public static function faq($fetch="obj",$where="1",$keys=['*'],$order_by="sequence",$order="ASC"){
            $table_faq = \Models\Faqs::select($keys)
            ->leftJoin('faq_language','faq_language.faq_id','=','faq.id_faq')
            ->categoryTopic()
            ->whereRaw($where)
            ->orderBy($order_by,$order);
            
            if ($fetch == 'array') {
                return json_decode(json_encode($table_faq->get()),true);
            }else if($fetch == 'single'){
                return $table_faq->get()->first();
            }else if($fetch == 'count'){
                return $table_faq->get()->count();
            }else{
                return $table_faq->get(); 
            }
        }

        public static function add_faq($data){
            if(!empty($data)){
                return $table_faq = \DB::table('faq')
                ->insertGetId($data);
            }else{
                return bool(false);   
            }
        }

        public static function update_faq($faq_id,$data){
            if(!empty($data)){
                return $table_faq = \DB::table('faq')
                ->where('id_faq',$faq_id)
                ->update($data);
            }else{
                return bool(false);   
            }
        }        

        public static function add_faq_detail($data){
            if(!empty($data)){
                return $table_faq = \DB::table('faq_language')
                ->insertGetId($data);
            }else{
                return bool(false);   
            }
        } 
        public static function update_faq_detail($faq_id,$data){
            if(!empty($data)){
                return $table_faq = \DB::table('faq_language')
                ->where('faq_id',$faq_id)
                ->update($data);
            }else{
                return bool(false);   
            }
        }

        public static function card_type($fetch="array",$where="1",$keys=['*']){
            $card_type = \DB::table('card_type')->select($keys)->whereRaw($where);
            if($fetch == 'array'){
                $card_type_array = json_decode(json_encode($card_type->get()),true);
                array_walk($card_type_array,function(&$item){
                    if(!empty($item['image_url'])){
                        $item['image_url'] = asset($item['image_url']);
                    }
                });
                return $card_type_array;
            }else if($fetch == 'first'){
                return $card_type->get()->first();
            }else{
                return $card_type->get();
            }
        }

        /**
         * [This method is used to add college] 
         * @param [Varchar]$data[Used for data]]
         * @return Boolean
         */ 

        public static function  add_workfield($data){
            $table_workfield = DB::table('workfield');

            if(!empty($data)){
                $isInserted = $table_workfield->insert($data); 
            }

            return (bool)$isInserted;
        }     

        /**
         * [This method is used to update college] 
         * @param [Integer]$id_college[Used for college id]
         * @param [Varchar]$data[Used for data]
         * @return Boolean
         */    

        public static function  update_workfield($id_workfield, $data){
            $table_workfield = DB::table('workfield');

            if(!empty($data)){
                $table_workfield->where('id_workfield','=',$id_workfield);
                $isUpdated = $table_workfield->update($data); 
            }
            
            return (bool)$isUpdated;
        }

        /**
         * [This method is used talent jobs] 
         * @param [type]$user[<description>]
         * @return Builder
         */ 

        public static function search_jobs(){
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

    }

