<?php

    namespace Models; 

    use Illuminate\Database\Eloquent\Model;
    use Illuminate\Support\Facades\DB;

    class Settings extends Model{
        protected $table = 'settings';
        protected $primaryKey = 'id_settings';
        
        const CREATED_AT = 'created';
        const UPDATED_AT = 'updated';

        protected $fillable = [];

        protected $hidden = [];

        public function __construct() {
            
        }

        /**
         * [This method is used for adding] 
         * @param [Integer]$user_id [Used for user id]
         * @param [Varchar]$data[Used for data]
         * @return Data Response
         */ 

        public static function add($user_id, $data, $settings = []){
            $email_settings = []; $mobile_settings = [];

            if(!empty($data['email'])){
                if(!empty($settings)){
                    foreach($settings['email'] as $item){
                        if(!in_array($item['setting'],$data['email'])){
                            $email_settings[] = [
                                'setting'   => $item['setting'],
                                'user_id'   => auth()->user()->id_user,
                                'type'      => 'email',
                                'updated'   => date('Y-m-d H:i:s'),
                                'created'   => date('Y-m-d H:i:s'),
                            ];
                        }
                    }
                }
            }

            if(!empty($data['mobile'])){
                if(!empty($settings)){
                    foreach($settings['mobile'] as $item){
                        if(!in_array($item['setting'],$data['mobile'])){
                            $mobile_settings[] = [
                                'setting'   => $item['setting'],
                                'user_id'   => auth()->user()->id_user,
                                'type'      => 'mobile',
                                'updated'   => date('Y-m-d H:i:s'),
                                'created'   => date('Y-m-d H:i:s'),
                            ];
                        }
                    }
                }
            }

            $table_setting     = \DB::table('settings');
            $table_setting->where('user_id',$user_id);
            $table_setting->delete();

            if(!empty($email_settings)){
                $table_setting->insert($email_settings);
            }

            if(!empty($mobile_settings)){
                $table_setting->insert($mobile_settings);
            }

            return true;
        }

        /**
         * [This method is used for fetching] 
         * @param [Integer]$user_id [Used for user id]
         * @param [Enum]$type[Used for type]
         * @return Json Response
         */ 

        public static function fetch($user_id, $type = 'talent'){
            $table_setting      = \DB::table('settings');
            $emailsettings      = array_column(
                json_decode(
                    json_encode(
                        $table_setting->select('setting')
                        ->where('type','email')
                        ->where('user_id',$user_id)
                        ->get()
                    ),
                    true
                ),
                'setting'
            );
            
            $table_setting      = \DB::table('settings');
            $mobilesettings      = array_column(
                json_decode(
                    json_encode(
                        $table_setting->select('setting')
                        ->where('type','mobile')
                        ->where('user_id',$user_id)
                        ->get()
                    ),
                    true
                ),
                'setting'
            );

            $table_users        = \DB::table('users');
            $newsletter = json_decode(
                    json_encode(
                        $table_users->select(['newsletter_subscribed','city','industry','subindustry'])
                        ->where('id_user',$user_id)
                        ->get()->first()
                    ),
                    true
                );

            $allsettings            = preg_grep('/^((?!READABLE_).)*$/', array_keys(\Lang::get('notification')));
            $type                   = strtoupper(($type == EMPLOYER_ROLE_TYPE)?TALENT_ROLE_TYPE:EMPLOYER_ROLE_TYPE);

            $result['email']        = array_values(
                array_filter(
                    array_map(function($item) use($emailsettings, $type){
                        if(!empty(strpos($item, $type))){
                            $status = in_array($item, $emailsettings)?DEFAULT_NO_VALUE:DEFAULT_YES_VALUE;
                            return [
                                'setting' => $item,
                                'status' => $status
                            ];    
                        }else{
                            unset($item);
                        }
                    }, $allsettings)
                )
            );
            
            $result['mobile']       = array_values(
                array_filter(
                    array_map(function($item) use($mobilesettings, $type){
                        if(!empty(strpos($item, $type))){
                            $status = in_array($item, $mobilesettings)?DEFAULT_NO_VALUE:DEFAULT_YES_VALUE;
                            return [
                                'setting' => $item,
                                'status' => $status
                            ];    
                        }
                    }, $allsettings)
                )
            );
            
            $newsletter['interest'] = \Models\Talents::interested_in($user_id);

            $result = array_merge($result,$newsletter);
            
            return $result;
        }

        /**
         * [This method is used for enabled setting] 
         * @param [Integer]$user_id [Used for user id]
         * @param [Varchar]$key[Used for Keys]
         * @param [Enum]$type[Used for type]
         * @return Data Response
         */ 
        
        public static function is_settings_enabled($user_id,$key,$type){
            $table_setting     = \DB::table('settings');
            $table_setting->where('user_id',$user_id);
            $table_setting->where('type',$type);
            $table_setting->where('setting',$key);

            if(!empty($table_setting->get()->count())){
                return false;
            }else{
                return true;
            }
        }
    }
