<?php

    namespace Models; 

    use Illuminate\Database\Eloquent\Model;
    use Illuminate\Support\Facades\DB;
    use Illuminate\Support\Facades\Crypt;
    use Illuminate\Support\Facades\Mail;

    class Users extends Model{
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
            'api_token',
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
                'users.type',
                'users.expertise',
                'users.gender',
                'users.experience',
                \DB::Raw("YEAR({$prefix}users.created) as member_since"),
            ])->name()->companyLogo();

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
                        CONCAT('{$base_url}',{$prefix}files.folder,{$prefix}files.filename),
                        CONCAT('{$base_url}','images/','".DEFAULT_AVATAR_IMAGE."')
                    ) as company_logo
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
         * [This method is used for change] 
         * @param [Integer]$userID [Used for user id]
         * @param [Varchar]$data[Used for data]
         * @return Boolean
         */ 

        public static function change($userID,$data){
            $isUpdated = false;
            $table_users = DB::table('users');

            if(!empty($data)){
                $table_users->where('id_user','=',$userID);
                $isUpdated = $table_users->update($data); 
            }
            
            return (bool)$isUpdated;
        }

        /**
         * [This method is used to findById] 
         * @param [Integer]$userID [Used for user id]
         * @param [Varchar]$keys[Used for keys]
         * @return Json Response
         */ 

        public static function findByUserId($userID,$keys = []){
            $table_user = DB::table((new static)->getTable());

            if(!empty($keys)){
                $table_user->select($keys);
            }

            return json_decode(
                json_encode(
                    $table_user->where(
                        array(
                            'id_user' => $userID,
                        )
                    )->first()
                ),
                true
            );
        }

        /**
         * [This method is used to findById] 
         * @param [Integer]$userID [Used for user id]
         * @param [Varchar]$keys[Used for keys]
         * @return Json Response
         */ 

        public static function findById($userID,$keys = []){
            $table_user = DB::table((new static)->getTable());

            if(!empty($keys)){
                $table_user->select($keys);
            }

            return json_decode(
                json_encode(
                    $table_user->where(
                        array(
                            'id_user' => $userID,
                        )
                    )->whereIn('type',['talent','employer','none'])->whereNotIn('status',['trashed'])->first()
                ),
                true
            );
        }

        /**
         * [This method is used to get support user] 
         * @param [Integer]$userID [Used for user id]
         * @param [Varchar]$keys[Used for keys]
         * @return Json Response
         */ 

        public static function get_support_user($userID,$keys = ['*']){
            $table_user = DB::table((new static)->getTable());

            if(!empty($keys)){
                $table_user->select($keys);
            }

            $result = json_decode(
                json_encode(
                    $table_user->where(
                        array(
                            'id_user' => $userID,
                        )
                    )->whereIn('type',['support'])->whereNotIn('status',['trashed'])->first()
                ),
                true
            );
            
            $result['sender']                             = trim(sprintf("%s %s",$result['first_name'],$result['last_name']));
            $result['sender_id']                          = $result['id_user'];
            $result['sender_picture']                     = $result['picture'];
            $result['sender_email']                       = ___e($result['email']);
            $result['sender_profile_link']                = url(sprintf('%s/talent/profile?talent_id=%s',EMPLOYER_ROLE_TYPE,___encrypt($result['id_user'])));
            
            return $result;
        }

        /**
         * [This method is used to findByEmail] 
         * @param [Varchar]$email [Used for email]
         * @param [Varchar]$keys[Used for keys]
         * @return Json Response
         */ 

        public static function findByEmail($email,$keys = []){
            $table_user = DB::table((new static)->getTable());

            if(!empty($keys)){
                $table_user->select($keys);
            }

            return json_decode(
                json_encode(
                    $table_user->where(
                        array(
                            'email' => $email,
                        )
                    )->whereNotIn('type',['superadmin','administrator','sub-admin'])->whereNotIn('status',['trashed'])->first()
                ),
                true
            );
        }

        /**
         * [This method is used to findByEmailAnyStatus] 
         * @param [Varchar]$email [Used for email]
         * @param [Varchar]$keys[Used for keys]
         * @return Json Response
         */ 

        public static function findAdminByEmail($email,$keys = []){
            $table_user = DB::table((new static)->getTable());

            if(!empty($keys)){
                $table_user->select($keys);
            }

            return json_decode(
                json_encode(
                    $table_user->where(
                        array(
                            'email' => $email,
                        )
                    )->whereIn('type',['superadmin','administrator','sub-admin'])
                    ->whereNotIn('status',['trashed','suspended'])
                    ->first()
                ),
                true
            );
        }

        /**
         * [This method is used to findByEmailAnyStatus] 
         * @param [Varchar]$email [Used for email]
         * @param [Varchar]$keys[Used for keys]
         * @return Json Response
         */ 

        public static function findByEmailAnyStatus($email,$keys = []){
            $table_user = DB::table((new static)->getTable());

            if(!empty($keys)){
                $table_user->select($keys);
            }

            return json_decode(
                json_encode(
                    $table_user->where(
                        array(
                            'email' => $email,
                        )
                    )->whereNotIn('type',['superadmin','administrator','sub-admin'])
                    ->whereNotIn('status',['trashed','suspended'])
                    ->first()
                ),
                true
            );
        }

        /**
         * [This method is used to findBySocialId ]
         * @param [Varchar]$social_key[Used for Social key]
         * @param [Integer]$social_id [Used for social id]
         * @param [Varchar]$keys [Used for keys]
         * @return Data Response
         */ 

        public static function findBySocialId($social_key,$social_id,$keys = ['*']){
            $table_user = DB::table((new static)->getTable());

            if(!empty($keys)){
                $table_user->select($keys);
            }

            return $table_user->where(
                array(
                    $social_key => $social_id,
                )
            )->whereNotIn('status',['trashed'])->first();
        }

        /**
         * [This method is used to findByToken] 
         * @param [Varchar]$token [Used for token]
         * @param [Varchar]$keys[Used for keys]
         * @return Json Response
         */ 

        public static function findByToken($token,$keys = []){
            $table_user = DB::table((new static)->getTable());

            if(!empty($keys)){
                $table_user->select($keys);
            }

            return json_decode(
                json_encode(
                    $table_user->where(
                        array(
                            'remember_token' => $token,
                        )
                    )->whereNotIn('type',['superadmin','administrator','sub-admin'])->whereNotIn('status',['trashed'])->first()
                ),
                true
            );
        }

        /**
         * [This method is used for authentication handling] 
         * @param [Varchar]$email [Used for email]
         * @param [Varchar]$password[Used for password]
         * @return Data Response
         */ 

        public static function _authenticate($email,$password){
            $table_user = DB::table((new static)->getTable());
            
            $user = $table_user->where(
                array(
                    'email' => $email,
                    'password' => md5($password),
                )
            )->whereNotIn('status',['trashed'])->first();

            if(!empty($user)){
                if($user->status == 'active'){
                    /*\Auth::loginUsingId($user->id_user);*/
                    \Session::put('front_login', [
                        'login_id' => $user->id_user,
                        'name' => $user->name,
                        'email' => $user->email,
                        'type' => $user->type,
                    ]);

                    if($user->type == 'talent'){
                        return [
                            'status' => true,
                            'message' => sprintf(ALERT_SUCCESS,trans(sprintf('general.successfully_loggedin'))),
                            'redirect' => '/talent/dashboard'
                        ];
                    }else if($user->type == 'employer'){
                        return [
                            'status' => true,
                            'message' => sprintf(ALERT_SUCCESS,trans(sprintf('general.successfully_loggedin'))),
                            'redirect' => '/employer/dashboard'
                        ];
                    }else{
                        return [
                            'status' => false,
                            'message' => sprintf(ALERT_DANGER,trans(sprintf('general.invalid_credentials'))),
                            'redirect' => '/login'
                        ];
                    }
                }else{
                    return [
                        'status' => false,
                        'message' => sprintf(ALERT_DANGER,trans(sprintf('general.account_%s',$user->status))),
                        'redirect' => '/login'
                    ];
                }
            }else{
                return [
                    'status' => false,
                    'message' => sprintf(ALERT_DANGER,trans(sprintf('general.invalid_credentials'))),
                    'redirect' => '/login'
                ];
            }
        }

        /**
         * [This method is used for authentication ] 
         * @param [Varchar]$email [Used for email]
         * @param [Varchar]$password[Used for password]
         * @return Data Response
         */ 

        public static function authenticate($email,$password){
            $response = (object)[];
            $table_user = DB::table((new static)->getTable());

            $user = $table_user->where(
                array(
                    'email' => $email,
                    'password' => bcrypt($password),
                )
            )->whereNotIn('status',['trashed'])->first();

            if(!empty($user)){
                if($user->status == 'active'){
                    if($user->type == 'talent'){
                        return [
                            'status' => true,
                            'data' => $user,
                            'message' => 'successfully_loggedin',
                        ];
                    }else if($user->type == 'employer'){
                        return [
                            'status' => true,
                            'data' => $user,
                            'message' => 'successfully_loggedin',
                        ];
                    }else{
                        return [
                            'status' => false,
                            'data' => $response,
                            'message' => 'invalid_credentials',
                        ];
                    }
                }else{
                    return [
                        'status' => false,
                        'data' => $response,
                        'message' => sprintf('account_%s',$user->status),
                    ];
                }
            }else{
                return [
                    'status' => false,
                    'data' => $response,
                    'message' => 'invalid_credentials',
                ];
            }
        }

        /**
         * [This method is used for to get file] 
         * @param [type]$where[Used for where clause]
         * @param [Fetch]$fetch[Used for fetching]
         * @param [Varchar]$keys[Used for keys]
         * @return Data Response
         */ 

        public static function get_file($where = "",$fetch = 'all',$keys = ['*']){
            $table_files = DB::table('files');
            $table_files->select($keys);

            if(!empty($where)){
                $table_files->whereRaw($where);
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
         * [This method is used to get current device] 
         * @param [Integer]$user_id [Used for user id]
         * @param [Varchar]$keys[Used for keys]
         * @return Json Response
         */ 

        public static function get_current_device($user_id,$keys = array()){
            $table_devices = DB::table('devices');
        
            if(!empty($keys)){
                $table_devices->select($keys);
            }

            return json_decode(
                json_encode(
                    $table_devices->where([
                        'user_id' => $user_id,
                        'is_current_device' => 'yes'
                    ])->get()->first()
                ),
                true
            );
        }

        /**
         * [This method is used to getSubscribeList] 
         * @param null
         * @return Data Response
         */ 

        public static function getSubscribeList(){
            \DB::statement(\DB::raw('set @row_number=0'));
            return $detail = DB::table('subscriber')
            ->select([
                \DB::raw('@row_number  := @row_number  + 1 AS row_number'),
                'id_subscriber',
                'email',
                'newsletter_token',
                'status',
                'updated',
                'created',
            ])
            ->get();
        }

        /**
         * [This method is used to getSubscribeByEmail] 
         * @param [Varchar]$email [Used for email]
         * @return Json Response
         */ 

        public static function getSubscribeByEmail($email){
            $detail = DB::table('subscriber')
            ->where('email', $email)
            ->first();

            return json_decode(json_encode($detail), true);
        }

        /**
         * [This method is used to insertSubscribe] 
         * @param [Varchar]$data[Used for data]
         * @return Boolean [true]
         */ 

        public static function insertSubscribe($data){
            DB::table('subscriber')
            ->insert($data);
            return true;
        }

        /**
         * [This method is used to getSubscribeByToken] 
         * @param [Varchar]$token[Used for token]
         * @return Json Response
         */ 

        public static function getSubscribeByToken($token){
            $detail = DB::table('subscriber')
            ->where('newsletter_token', $token)
            ->first();

            return json_decode(json_encode($detail), true);
        }

        /**
         * [This method is used to updateSubscribe] 
         * @param [Integer]$id_subscriber [Used for subscriber id]
         * @param [Varchar]$data[Used for data]
         * @return Data Response
         */ 

        public static function updateSubscribe($id_subscriber, $data){
            $detail = DB::table('subscriber')
            ->where('id_subscriber', $id_subscriber)
            ->update($data);
        }

        /**
         * [This method is used to deleteSubscribe] 
         * @param [Integer]$id_subscriber [Used for subscriber id]
         * @return Data Response
         */ 

        public static function deleteSubscribe($id_subscriber){
            $detail = DB::table('subscriber')
            ->where('id_subscriber', $id_subscriber)
            ->delete();
        }

        /**
         * [This method is used for remove] 
         * @param [Integer]$user_id [Used for user id]
         * @param [Searching]$search[Used for searching]
         * @param [Integer]$employer_id [Used for employer id]
         * @return Data Response
         */ 

        public static function get_my_chat_list($user_id, $search = NULL, $employer_id = NULL){
            $prefix                 = DB::getTablePrefix();
            $base_url               = ___image_base_url();
            
            $table_chat_requests    = DB::table(\DB::Raw("(
                    SELECT * 
                    FROM  {$prefix}chat_requests 
                    WHERE (
                        {$prefix}chat_requests.sender_id = {$user_id}
                        OR 
                        {$prefix}chat_requests.receiver_id = {$user_id}
                    )
                    ORDER BY {$prefix}chat_requests.created DESC
                ) as {$prefix}requests
            "));

            $table_chat_requests->select([
                \DB::Raw("({$prefix}requests.sender_id + {$prefix}requests.sender_id) AS chat_user_id"),
                \DB::Raw("{$user_id} as sender_id"),
                \DB::Raw("
                    IF(
                        ({$user_id} = {$prefix}requests.sender_id),
                        {$prefix}requests.receiver_id,
                        {$prefix}requests.sender_id
                    ) as receiver_id
                "),
                \DB::Raw("
                    IF(
                        ({$user_id} = {$prefix}requests.sender_id),
                        TRIM(CONCAT({$prefix}talent.first_name,' ',{$prefix}talent.last_name)),
                        TRIM(CONCAT({$prefix}employer.first_name,' ',{$prefix}employer.last_name))
                    ) as receiver_name
                "),
                \DB::Raw("
                    IF(
                        ({$user_id} = {$prefix}requests.sender_id),
                        {$prefix}talent.email,
                        {$prefix}employer.email
                    ) as receiver_email
                "),
                \DB::Raw("
                    IF(
                        ({$user_id} = {$prefix}requests.sender_id),
                        {$prefix}talent.chat_status,
                        {$prefix}employer.chat_status
                    ) as status
                "),
                \DB::Raw("
                    IF(
                        ({$user_id} = {$prefix}requests.sender_id),
                        IF(
                            {$prefix}sender_image.filename IS NOT NULL,
                            CONCAT('{$base_url}','/',{$prefix}sender_image.folder,{$prefix}sender_image.filename),
                            CONCAT('{$base_url}','/','images/','".DEFAULT_AVATAR_IMAGE."')
                        ),
                        IF(
                            {$prefix}receiver_image.filename IS NOT NULL,
                            CONCAT('{$base_url}','/',{$prefix}receiver_image.folder,{$prefix}receiver_image.filename),
                            CONCAT('{$base_url}','/','images/','".DEFAULT_AVATAR_IMAGE."')
                        )
                    ) as receiver_picture
                "),
                \DB::Raw(
                    "IF(
                        ({$prefix}requests.request_status = 'pending' AND {$prefix}requests.chat_initiated != 'talent'),
                        'accepted',
                        {$prefix}requests.request_status
                    ) as request_status"
                ),
                \DB::Raw("
                    (
                        IFNULL(
                            (
                                SELECT COUNT(id_chat) FROM {$prefix}chat 
                                WHERE (
                                    (
                                        (
                                            {$prefix}chat.sender_id = {$user_id} 
                                            AND 
                                            receiver_id = IF(
                                                ({$user_id} = {$prefix}requests.sender_id),
                                                {$prefix}requests.receiver_id,
                                                {$prefix}requests.sender_id
                                            ) 
                                        )
                                        OR 
                                        (
                                            {$prefix}chat.sender_id = IF(
                                                ({$user_id} = {$prefix}requests.sender_id),
                                                {$prefix}requests.receiver_id,
                                                {$prefix}requests.sender_id
                                            ) 
                                            AND  
                                            receiver_id = {$user_id}
                                        )
                                    )
                                    AND 
                                    seen_status != 'read'
                                    AND 
                                    delete_sender_status = 'active'
                                )
                            ),
                            0
                        )
                    ) as unread_messages
                "),
                \DB::Raw("
                    (
                        SELECT message FROM {$prefix}chat 
                        WHERE (
                            (
                                {$prefix}chat.sender_id = {$user_id} 
                                AND 
                                receiver_id = IF(
                                    ({$user_id} = {$prefix}requests.sender_id),
                                    {$prefix}requests.receiver_id,
                                    {$prefix}requests.sender_id
                                ) 
                            )
                            OR 
                            (
                                {$prefix}chat.sender_id = IF(
                                    ({$user_id} = {$prefix}requests.sender_id),
                                    {$prefix}requests.receiver_id,
                                    {$prefix}requests.sender_id
                                ) 
                                AND  
                                receiver_id = {$user_id}
                            )
                        )
                        AND 
                        delete_sender_status = 'active'
                        ORDER BY {$prefix}chat.id_chat DESC
                        LIMIT 0,1
                    ) as last_message
                "),
                \DB::Raw("
                    (
                        IFNULL(
                            (
                                SELECT created FROM {$prefix}chat 
                                WHERE (
                                    (
                                        {$prefix}chat.sender_id = {$user_id} 
                                        AND 
                                        receiver_id = IF(
                                            ({$user_id} = {$prefix}requests.sender_id),
                                            {$prefix}requests.receiver_id,
                                            {$prefix}requests.sender_id
                                        ) 
                                    )
                                    OR 
                                    (
                                        {$prefix}chat.sender_id = IF(
                                            ({$user_id} = {$prefix}requests.sender_id),
                                            {$prefix}requests.receiver_id,
                                            {$prefix}requests.sender_id
                                        ) 
                                        AND  
                                        receiver_id = {$user_id}
                                    )
                                )
                                ORDER BY {$prefix}chat.id_chat DESC
                                LIMIT 0,1
                            ),
                            {$prefix}requests.created
                        )
                    ) as timestamp
                "),
                \DB::Raw("
                    (
                        IFNULL(
                            (
                                SELECT created FROM {$prefix}chat 
                                WHERE (
                                    (
                                        {$prefix}chat.sender_id = {$user_id} 
                                        AND 
                                        receiver_id = IF(
                                            ({$user_id} = {$prefix}requests.sender_id),
                                            {$prefix}requests.receiver_id,
                                            {$prefix}requests.sender_id
                                        ) 
                                    )
                                    OR 
                                    (
                                        {$prefix}chat.sender_id = IF(
                                            ({$user_id} = {$prefix}requests.sender_id),
                                            {$prefix}requests.receiver_id,
                                            {$prefix}requests.sender_id
                                        ) 
                                        AND  
                                        receiver_id = {$user_id}
                                    )
                                )
                                ORDER BY {$prefix}chat.id_chat DESC
                                LIMIT 0,1
                            ),
                            {$prefix}requests.created
                        )
                    ) as requested_date
                ")
            ]);

            $table_chat_requests->leftJoin("users as employer","employer.id_user","=","requests.sender_id");
            $table_chat_requests->leftJoin("users as talent","talent.id_user","=","requests.receiver_id");
            $table_chat_requests->leftJoin('files as sender_image',function($leftjoin){
                $leftjoin->on('sender_image.user_id','=','requests.sender_id');
                $leftjoin->on('sender_image.type','=',\DB::Raw('"profile"'));
            });
            $table_chat_requests->leftJoin('files as receiver_image',function($leftjoin){
                $leftjoin->on('receiver_image.user_id','=','requests.receiver_id');
                $leftjoin->on('receiver_image.type','=',\DB::Raw('"profile"'));
            });
            $table_chat_requests->groupBy(["chat_user_id"]);
            $table_chat_requests->orderBy("requests.created","DESC");

            if(!empty($search)){
                $table_chat_requests->having("receiver_name","like","%{$search}%");
            }

            $result = json_decode(json_encode($table_chat_requests->get()),true);
            
            if(!empty($result)){
                array_walk($result, function(&$item) use($user_id){
                    $item['ago']                = ___agoday($item['timestamp']);
                    $item['fulltime']           = ___d($item['timestamp']);
                    $item['timestamp']          = strtotime($item['timestamp']);
                    $item['last_message_code']  = "";

                    if($item['last_message'] == CHAT_EMPLOYER_NEW_REQUEST || $item['last_message'] == CHAT_EMPLOYER_GREETING_MESSAGE || $item['last_message'] == CHAT_TALENT_GREETING_MESSAGE){
                        $item['last_message_code']  = $item['last_message'];
                        $item['last_message']       = trans(sprintf("general.%s",$item['last_message']));
                    }

                    if($user_id != $item['receiver_id']){
                        $item['last_message']       = sprintf("%s: %s",trans('general.M0277'),$item['last_message']);
                    }

                    $item['profile_link']   = "";
                });
            }

            return $result;
        }

        /**
         * [This method is used to getSubscribeUser] 
         * @param [Enum]$type [Used for type]
         * @return Data Respponse
         */ 

        public static function getSubscribeUser($type){
            $prefix             = DB::getTablePrefix();

            $projectProposal    = DB::table('users')
            ->select(
                'users.id_user',
                \DB::Raw('CONCAT('.$prefix.'users.first_name, " ", '.$prefix.'users.last_name ) AS name'),
                'users.email'
            )
            ->where('status', 'active')
            ->where('newsletter_subscribed', 'yes')
            ->where('type', $type)
            ->get();

            return $projectProposal;
        }

        /**
         * [This method is used to getUserNewsletterToken] 
         * @param [Varchar]$token [Used for token]
         * @return Json Response
         */ 

        public static function getUserNewsletterToken($token){
            $detail = DB::table('users')
            ->where('newsletter_token', $token)
            ->first();

            return json_decode(json_encode($detail), true);
        }

        /**
         * [This method is used to getTalentByIndustry] 
        * @param [Integer]$id_industry [Used for Industry id]
         * @return Json Response
         */ 

        public static function getTalentByIndustry($id_industry){
            $prefix = DB::getTablePrefix();
            $current_datetime = date('Y-m-d H:i:s');
            $past_datetime = date('Y-m-d H:i:s', mktime(date('H'),date('i'),date('s'),date('m'),date('d')-7,date('Y')));

            $table_user = DB::table('users')
            ->select(
                \DB::Raw("TRIM(CONCAT({$prefix}users.first_name,' ',{$prefix}users.last_name)) as name"),
                'email',
                'newsletter_token',
                \DB::raw('"0" as job_completion'),
                \DB::raw('(SELECT IFNULL(ROUND((TIMESTAMPDIFF(MINUTE, `startdate`, `enddate`)/60), 0), 0) AS total_hours FROM `'.$prefix.'project_log` AS pl WHERE pl.talent_id = '.$prefix.'users.id_user AND `startdate` >= "'.$past_datetime.'" AND `enddate` <= "'.$current_datetime.'") AS availability_hours'),
                \DB::raw('(SELECT IFNULL(ROUND(AVG(review_average), 1), "0.0") FROM '.$prefix.'reviews AS rev WHERE rev.receiver_id = '.$prefix.'users.id_user) as rating'),
                \DB::raw('(SELECT COUNT(*) FROM '.$prefix.'reviews AS rev WHERE rev.receiver_id = '.$prefix.'users.id_user) as review'),
                \DB::raw('CONCAT(UCASE(LEFT(expertise, 1)),SUBSTRING(expertise, 2)) AS expertise'),
                \DB::raw("(SELECT GROUP_CONCAT(t.skill) FROM {$prefix}talent_skills as t WHERE t.user_id = {$prefix}users.id_user) as skills")
                )
            ->where('type','talent')
            ->where('status','active')
            ->where('newsletter_subscribed','yes')
            ->where('industry',$id_industry)
            ->get()
            ->toArray();

            return json_decode(json_encode($table_user),true);
        }

        /**
         * [This method is used to getEmployerByIndustry] 
         * @param [Integer]$id_industry [Used for Industry id]
         * @return Json Response
         */ 

        public static function getEmployerByIndustry($id_industry){
            $prefix = DB::getTablePrefix();
            $table_user = DB::table('users')
            ->select(
                \DB::Raw("TRIM(CONCAT({$prefix}users.first_name,' ',{$prefix}users.last_name)) as name"),
                'email',
                'newsletter_token'
                )
            ->where('type','employer')
            ->where('status','active')
            ->where('newsletter_subscribed','yes')
            ->where('industry',$id_industry)
            ->get()
            ->toArray();

            return json_decode(json_encode($table_user),true);
        }

        /**
         * [This method is used for listing] 
         * @param [type]$where [Used for where clause]
         * @param [Varchar] $keys[Used for keys]
         * @return Data Response
         */ 

        public static function  listing($where, $keys = ['*']){
            \DB::statement(\DB::raw('set @row_number=0'));

            $table_users = DB::table('users');
            $keys[] = \DB::raw('@row_number  := @row_number  + 1 AS row_number');
            $table_users->select($keys);
            $table_users->where($where);
            $table_users->where('status','!=','trashed');
            $table_users->where('status','!=','suspended');
            $table_users->orderBy('users.id_user','desc');

            return $table_users->get();
        }

        /**
         * [This method is used for listing] 
         * @param [type]$where [Used for where clause]
         * @param [Varchar] $keys[Used for keys]
         * @return Data Response
         */ 

        public static function trashedUserListing($where, $keys = ['*']){
            \DB::statement(\DB::raw('set @row_number=0'));

            $table_users = DB::table('users');
            $keys[] = \DB::raw('@row_number  := @row_number  + 1 AS row_number');
            $table_users->select($keys);
            $table_users->where($where);
            $table_users->orderBy('users.id_user','desc');

            return $table_users->get();
        }

        /**
         * [This method is used to getTalentForNewsLetter] 
         * @param null
         * @return Json Response
         */ 

        public static function getTalentForNewsLetter(){
            $prefix = DB::getTablePrefix();
            $current_datetime = date('Y-m-d H:i:s');
            $past_datetime = date('Y-m-d H:i:s', mktime(date('H'),date('i'),date('s'),date('m'),date('d')-7,date('Y')));

            $table_user = DB::table('users')
            ->select(
                \DB::Raw("TRIM(CONCAT({$prefix}users.first_name,' ',{$prefix}users.last_name)) as name"),
                'email',
                'newsletter_token',
                \DB::raw('"0" as job_completion'),
                \DB::raw('(SELECT IFNULL(ROUND((TIMESTAMPDIFF(MINUTE, `startdate`, `enddate`)/60), 0), 0) AS total_hours FROM `'.$prefix.'project_log` AS pl WHERE pl.talent_id = '.$prefix.'users.id_user AND `startdate` >= "'.$past_datetime.'" AND `enddate` <= "'.$current_datetime.'") AS availability_hours'),
                \DB::raw('(SELECT IFNULL(ROUND(AVG(review_average), 1), "0.0") FROM '.$prefix.'reviews AS rev WHERE rev.receiver_id = '.$prefix.'users.id_user) as rating'),
                \DB::raw('(SELECT COUNT(*) FROM '.$prefix.'reviews AS rev WHERE rev.receiver_id = '.$prefix.'users.id_user) as review'),
                \DB::raw('CONCAT(UCASE(LEFT(expertise, 1)),SUBSTRING(expertise, 2)) AS expertise'),
                \DB::raw("(SELECT GROUP_CONCAT(t.skill) FROM {$prefix}talent_skills as t WHERE t.user_id = {$prefix}users.id_user) as skills")
                )
            ->where('type','talent')
            ->where('status','active')
            ->where('newsletter_subscribed','yes')
            ->where('industry',NULL)
            ->get()
            ->toArray();

            return json_decode(json_encode($table_user),true);
        }

        /**
         * [This method is used to getEmployerForNewsLetter] 
         * @param null
         * @return Json Response
         */ 

        public static function getEmployerForNewsLetter(){
            $prefix = DB::getTablePrefix();
            $table_user = DB::table('users')
            ->select(
                \DB::Raw("TRIM(CONCAT({$prefix}users.first_name,' ',{$prefix}users.last_name)) as name"),
                'email',
                'newsletter_token'
                )
            ->where('type','employer')
            ->where('status','active')
            ->where('newsletter_subscribed','yes')
            ->where('industry',NULL)
            ->get()
            ->toArray();

            return json_decode(json_encode($table_user),true);
        }

        /**
         * [This method is used to get user's rating] 
         * @param [Integer]$user_id [Used for user id]
         * @return Data Response
         */ 

        public static function getUserRating($id_user){
            $prefix = DB::getTablePrefix();
            $table_user = DB::table('users')
            ->select(
                \DB::raw('(SELECT IFNULL(ROUND(AVG(review_average), 1), "0.0") FROM '.$prefix.'reviews AS rev WHERE rev.receiver_id = '.$prefix.'users.id_user) as rating'),
                \DB::raw('(SELECT COUNT(*) FROM '.$prefix.'reviews AS rev WHERE rev.receiver_id = '.$prefix.'users.id_user) as review')
                )
            ->where('id_user',$id_user)
            ->get()
            ->first();

            return $table_user;
        }

        /**
         * [This method is used to getUserForNewsLetter description] 
         * @param [Varchar]$userType[Used for user type]
         * @param [type]$where_data [Used for where clause]
         * @return Data Response
         */ 

        public static function getUserForNewsLetter($userType = 'talent', $where_data = ''){
            $prefix = DB::getTablePrefix();
            $current_datetime = date('Y-m-d H:i:s');
            $past_datetime = date('Y-m-d H:i:s', mktime(date('H'),date('i'),date('s'),date('m'),date('d')-7,date('Y')));

            $table_user = DB::table('users');
            $table_user->select(
                'users.*',
                \DB::Raw("TRIM(CONCAT({$prefix}users.first_name,' ',{$prefix}users.last_name)) as name"),
                'email',
                'newsletter_token',
                \DB::raw('"0" as job_completion'),
                \DB::raw('(SELECT IFNULL(ROUND((TIMESTAMPDIFF(MINUTE, `startdate`, `enddate`)/60), 0), 0) AS total_hours FROM `'.$prefix.'project_log` AS pl WHERE pl.talent_id = '.$prefix.'users.id_user AND `startdate` >= "'.$past_datetime.'" AND `enddate` <= "'.$current_datetime.'") AS availability_hours'),
                \DB::raw('(SELECT IFNULL(ROUND(AVG(review_average), 1), "0.0") FROM '.$prefix.'reviews AS rev WHERE rev.receiver_id = '.$prefix.'users.id_user) as rating'),
                \DB::raw('(SELECT COUNT(*) FROM '.$prefix.'reviews AS rev WHERE rev.receiver_id = '.$prefix.'users.id_user) as review'),
                \DB::raw('CONCAT(UCASE(LEFT(expertise, 1)),SUBSTRING(expertise, 2)) AS expertise'),
                \DB::raw("(SELECT GROUP_CONCAT(t.skill) FROM {$prefix}talent_skills as t WHERE t.user_id = {$prefix}users.id_user) as skills"),
                'currencies.sign AS sign'
                );
            $table_user->leftJoin('currencies', 'users.currency', '=', 'currencies.iso_code');
            $table_user->where('users.type',$userType);
            $table_user->where('users.status','active');
            $table_user->where('users.newsletter_subscribed','yes');
            if(!empty($where_data)){
                $where_data = implode(' AND ', $where_data);
                $table_user->whereRaw($where_data);
            }
            $table_user = $table_user->get()->toArray();

            $table_user = json_decode(json_encode($table_user),true);

            #dd($table_user);
            return $table_user;
        }

        

        /**
         * [This method is used to handle login] 
         * @param [Varchar]$social [Used for social]
         * @return Data Response
         */ 

        public static function __dologin($social){
            
            \Session::put('social',$social);
            $status = false; $message = ""; $redirect = "";

            $field          = ['id_user','type','first_name','last_name','name','email','status'];
            $email          = (!empty($social['social_email']))?$social['social_email']:"";

            if(!empty($social['social_id']) && !empty($social['social_key'])){
                $social_id      = (string) trim($social['social_id']);
                $social_key     = (string) trim($social['social_key']);
            }

            if(!empty($social_key) && !empty($social_id) && !empty($email)){
                $result         = (array) \Models\Users::findByEmail(trim($email),$field);
            }

            if(empty($result) && !empty($social_key) && !empty($social_id)){
                $result         = (array) \Models\Users::findBySocialId($social_key,$social_id,$field);
            }

            /*if(!empty($social['social_company_name'])){
                $social_id      = (string) trim($social['social_company_name']);
            }*/
            
            if(empty($result)){
                $request = [
                    'first_name'    => $social['social_first_name'],
                    'last_name'     => $social['social_last_name'],
                    'email'         => $social['social_email'],
                    'social_id'     => $social['social_id'],
                    'social_key'    => $social['social_key'],
                    'social_picture'    => $social['social_picture'],
                    'company_name'    => !empty($social['social_company_name']) ? $social['social_company_name'] : '',
                ];
                
                if(!empty($social['social_key'])){
                    $validator = self::validate_social_signup($request);
                }else{
                    $validator = self::validate_normal_signup($request);
                }

                if(empty($validator->errors()->all())){
                    if(!empty($email)){
                        $result = (array) \Models\Users::findByEmail($email,$field);
                    }

                    if(!empty($result['email']) && !empty($email) && ($result['email'] != $email)){
                        $message = 'M0039';
                    }else if(!empty($result) && !empty($request['mobile']) && $result['mobile'] != $request['mobile']){
                        $message = 'M0039';
                    }else if(!empty($result['mobile']) && !empty($social_id)){
                        if($result['status'] == 'inactive'){
                            $message = 'M0002';
                        }elseif($result['status'] == 'suspended'){
                            $message = "M0003";
                        }else{
                            $updated_data = array(
                                $social_key     => $social_id,
                                'status'        => 'active'
                            );

                            if(empty($result['email'])){
                                $updated_data['email'] = $email;
                            }

                            \Models\Users::change($result['id_user'],$updated_data);
                            
                            \Auth::loginUsingId($result['id_user']);
                            if(\Auth::user()->type == TALENT_ROLE_TYPE){
                                $redirect = sprintf('/%s/profile/step/one',TALENT_ROLE_TYPE);
                            }elseif(\Auth::user()->type == EMPLOYER_ROLE_TYPE){
                                $redirect = sprintf('/%s/profile/edit/one',EMPLOYER_ROLE_TYPE);
                            }else{
                                $redirect = '/';
                            }

                            $message = 'M0005';
                            $status = true;
                        }
                    }else{
                        $status = true;
                        $redirect = '/signup';
                        $message = 'M0588';
                        \Session::put('social',$request);
                    }
                }else if($message == 'M0039'){
                    
                }
            }else{
                /*if($result['type'] == TALENT_ROLE_TYPE)*/
                if($result['status'] == 'inactive'){
                    $message = trans(sprintf('general.%s',"M0002"));
                }elseif($result['status'] == 'suspended'){
                    $message = trans(sprintf('general.%s',"M0003"));
                }else{
                    $updated_data = array(
                        $social_key     => $social_id,
                        'status'        => 'active'
                    );

                    if(empty($result['email'])){
                        $updated_data['email'] = $email;
                    }

                    \Models\Users::change($result['id_user'],$updated_data);
                    \Auth::loginUsingId($result['id_user']);
                    
                    $message = sprintf(ALERT_SUCCESS,trans(sprintf('general.%s',"M0005")));

                    if(\Auth::user()->type == TALENT_ROLE_TYPE){
                        $redirect = sprintf('/%s/profile/step/one',TALENT_ROLE_TYPE);
                    }elseif(\Auth::user()->type == EMPLOYER_ROLE_TYPE){
                        $redirect = sprintf('/%s/profile/edit/one',EMPLOYER_ROLE_TYPE);
                    }else{
                        $redirect = '/';
                    }
                    $status = true;
                }
            }/*else{
                \Session::forget('social');
                $message = sprintf(ALERT_DANGER,trans(sprintf('general.%s',"M0108")));
            }*/

            return [
                'status' => $status,
                'message' => $message,
                'redirect' => $redirect,
                'validator' => !(empty($validator))?$validator:'',
            ];
        }

        /**
         * [This method is used to validate social signup] 
         * @param Request
         * @return Data Response
         */ 

        public static function validate_social_signup($request){
            $message = false;

            $validate = \Validator::make($request, [
                'first_name'        => validation('first_name'),
                'last_name'         => validation('last_name'),
                'email'             => ['email',\Illuminate\Validation\Rule::unique('users')->ignore('trashed','status')],
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
                'email.unique'              => trans('general.M0012'),
            ]);

            if($validate->passes()){
                
            }

            return $validate;
        }

        public static function postUserRequest($data){
            $table_users = DB::table('user_request');
            $isUpdated = $table_users->insert($data);
            
            return (bool)$isUpdated;
        }

        /**
         * [This method is used to get CountryID of a talent] 
         * @param [Integer]$user_id [Used for user id]
         * @return Data Response
         */ 

        public static function getUserCountrybyID($id_user){
            $prefix = DB::getTablePrefix();
            $country_user = DB::table('users')
            ->select('country')
            ->where('id_user',$id_user)
            ->get()
            ->first();

            if(!empty($country_user->country)){
                return $country_user->country;
            }else{
                return 0;
            }
        }

        public static function emp_suggested_talents($skill_id){

            $prefix   = DB::getTablePrefix();
            $base_url = ___image_base_url();

            $suggested_user = DB::table('talent_skills')
            ->select([
                        'users.name',
                        'users.expertise',
                        \DB::Raw("
                            IF(
                                {$prefix}user_file.filename IS NOT NULL,
                                CONCAT('{$base_url}',{$prefix}user_file.folder,{$prefix}user_file.filename),
                                CONCAT('{$base_url}','images/','".DEFAULT_AVATAR_IMAGE."')
                            ) as user_img
                        ")

                    ])
            ->leftJoin('users','users.id_user','=','talent_skills.user_id')
            ->leftJoin('files as user_file',function($leftjoin){
                $leftjoin->on('user_file.user_id','=','users.id_user');
                $leftjoin->on('user_file.type','=',\DB::Raw('"profile"'));
            })
            ->where('talent_skills.skill_id','=',\DB::Raw("'$skill_id'"))
            ->limit(4)
            ->get();

            if(!empty($suggested_user)){
                return json_decode(json_encode($suggested_user),true);
            }else{
                return [];
            }
        }
    }