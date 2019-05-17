<?php

	namespace Models;

	use Illuminate\Database\Eloquent\Model;
	use Illuminate\Support\Facades\DB;
    use Illuminate\Support\Facades\Crypt;
    use Illuminate\Support\Facades\Mail;

	class Administrator extends Model
	{
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

        /**
         * [This method is used to add new] 
         * @param [Varchar]$insert_data[Used for inserting a data]
         * @return Boolean
         */
        

        public static function add_new($insert_data){
            if(empty($insert_data)){
                return (bool) false;
            }else{
                $insert_data['commission'] = \Cache::get('commission');
            }

            return self::insertGetId($insert_data);
        }

        /**
         * [This method is used for sub admin creation] 
         * @param [Varchar]$data[Used for data]
         * @return Data Response 
         */

	    public static function createSubAdmin($data){
	        $status         = 'pending';
	        $token          = bcrypt(__random_string());

	        $insert_data = [
                'type'                          => SUB_ADMIN_ROLE_TYPE,
                'name'                          => (string)sprintf("%s %s",$data->first_name,$data->last_name),
                'first_name'                    => (string)$data->first_name,
                'last_name'                     => (string)$data->last_name,
                'email'                         => (string)$data->email,
                'picture'                       => (string)(!empty($data->social_picture))?$data->social_picture:DEFAULT_AVATAR_IMAGE,
                'password'                      => bcrypt($data->password),
                'status'                        => $status,
                'api_token'                     => $token,
                'agree'                         => 'yes',
                'newsletter_subscribed'         => (!empty($data->newsletter))?'yes':'no',
                'remember_token'                => __random_string(),
                'percentage_default'            => TALENT_DEFAULT_PROFILE_PERCENTAGE,
                'last_login'                    => date('Y-m-d H:i:s'),
                'updated'                       => date('Y-m-d H:i:s'),
                'created'                       => date('Y-m-d H:i:s'),
            ];

	        $isInserted = self::add_new($insert_data);

	        if(!empty($isInserted)){
	            return [
	                'status' => true,
	                'message' => 'M0021',
	                'signup_user_id' => $isInserted,
	            ];
	        }else{
	            return [
	                'status' => false,
	                'message' => 'M0022',
	                'signup_user_id' => false,
	            ];
	        }
	    }

        /**
         * [This method is used for dashboard] 
         * @param [Enum] $user_type [Used for user type]
         * @return Data Response
         */
        

	    public static function dashboard($user_type){
            $prefix             = DB::getTablePrefix();
            $defaultCurrency    = \Models\Currency::getDefaultCurrency();

            if($user_type == 'superadmin'){
                $data = [
                    "{$user_type}" => [
                        'recent_projects'    => \DB::table('projects')->select(['projects.title', \DB::Raw('`CONVERT_PRICE`('.$prefix.'projects.price, '.$prefix.'projects.price_unit, "'.$defaultCurrency->iso_code.'") AS price')])->where('status','!=','trashed')->limit(6)->orderBy('projects.created','DESC')->get(),
                        'recent_employers'   => \DB::table('users')->select(['first_name','last_name','created'])->where('status','!=','trashed')->where('type','=','employer')->limit(6)->orderBy('users.created','DESC')->get(),
                        'recent_talents'     => \DB::table('users')->select(['first_name','last_name','created'])->where('status','!=','trashed')->where('type','=','talent')->limit(6)->orderBy('users.created','DESC')->get(),
                        'recent_contacts'    => \DB::table('messages')->select(['message_content','created'])->where(['message_status' => 'approved'])->where('message_ticket_status','open')->whereRaw(" (sender_type='talent' OR sender_type='employer' OR sender_type='guest') ")->limit(6)->orderBy('messages.created','DESC')->get(),
                        'recent_dispute'    => \DB::table('projects_dispute')->select(['projects.title','id_raised_dispute','last_updated'])->leftJoin('projects','projects.id_project','=','projects_dispute.project_id')->where(['projects_dispute.status' => 'open'])->limit(6)->orderBy('projects_dispute.created','DESC')->get(),
                        'total_projects'     => \DB::table('projects')->where('status','!=','trashed')->get()->count(),
                        'total_employers'    => \DB::table('users')->where('type','=','employer')->where('status','!=','trashed')->get()->count(),
                        'total_talents'      => \DB::table('users')->where('type','=','talent')->where('status','!=','trashed')->get()->count(),
                        'total_contacts'     => \DB::table('messages')->where('message_status','=','approved')->where('message_ticket_status','open')->whereRaw(" (sender_type='talent' OR sender_type='employer' OR sender_type='guest') ")->get()->count(),
                        'total_disputes'     => \DB::table('projects_dispute')->where('status','!=','discarded')->get()->count(),
                    ]
                ];
            }elseif($user_type == 'sub-admin'){
                $data = [
                    "{$user_type}" => [
                        'recent_abuses'     =>  \DB::table('report_abuse')->select(['message as title','created'])->limit(6)->orderBy('created','DESC')->get(),
                        'recent_dispute'    =>  \DB::table('projects_dispute')->select('projects.title','projects_dispute.created')->leftJoin('projects','projects.id_project','=','projects_dispute.project_id')->groupBy('project_id')->limit(6)->orderBy('created','DESC')->get(),
                        'total_abuses'      =>  \DB::table('report_abuse')->get()->count(),
                        'total_dispute'     =>  \DB::table('projects_dispute')->groupBy('project_id')->get()->count(),
                    ]
                ];
            }

            return $data;
        }

        /**
         * [This method is used for getting subAdmin permission] 
         * @param [Integer]$id_user[Used for user id]        
         * @return Data Response
         */
        

        public static function getSubAdminPermission($id_user){
            $menuPermission = DB::table('users_menu_visibility')
            ->select('menu_visibility')
            ->where('id_user', $id_user)
            ->first();

            return json_decode(json_encode($menuPermission), true);
        }
         
         /**
         * [This method is used to change currency] 
         * @param [Integer]$id_user[Used for user id] 
         * @param [Varchar]$permission[Used for permission] 
         * @return none
         */
        

        public static function createSubAdminPermission($id_user, $permission){
	    	DB::table('users_menu_visibility')->where('id_user', $id_user)->delete();
			DB::table('users_menu_visibility')->insert($permission);
	    }

        /**
         * [This method is used to for token] 
         * @param [Varchar]$token[Used for token]
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
                    )->whereNotIn('type',['employer','talent'])->whereNotIn('status',['trashed'])->first()
                ),
                true
            );
        }

        /**
         * [This method is used for getting message by id] 
         * @param [Integer]$id_message[Used for message id] 
         * @return Data Response
         */
        

        public static function getMessageByID($id_message){
        	$message = DB::table('messages')
        	->where('id_message', $id_message)
        	->first();

        	return json_decode(json_encode($message), true);
        }

        /**
         * [This method is used for getting reply message by id] 
         * @param [Integer]$id_message[Used for message id]
         * @return Data Response
         */
        

        public static function getMessageReplyByID($id_message){
            $message = DB::table('messages')
            ->where('message_reply_id', $id_message)
            ->where('sender_type', 'admin')
            ->get()
            ->toArray();

            return json_decode(json_encode($message), true);
        }

        /**
         * [This method is used to update message] 
         * @param [Integer]$id_message[Used for message id]
         * @param [Varchar]$data[Used for data]
         * @return none
         */
        

        public static function updateMessage($id_message, $data){
            DB::table('messages')
            ->where('id_message',$id_message)
            ->update($data);
        }

        /**
         * [This method is used for adding message] 
         * @param [Varchar]$data[Used for data]       
         * @return none
         */
        

        public static function addMessage($data){
            DB::table('messages')->insert($data);
        }

        /**
         * [This method is used for deleting message by id ] 
         * @param [Integer]$id_message[Used for message id]
         * @return none
         */
        

        public static function deleteMessageById($id_message){
            DB::table('messages')
            ->where('id_message',$id_message)
            ->update(['message_status' => 'trashed']);
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
                    )->whereNotIn('type',['employer','talent'])->whereNotIn('status',['trashed'])->first()
                ),
                true
            );
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
	}
