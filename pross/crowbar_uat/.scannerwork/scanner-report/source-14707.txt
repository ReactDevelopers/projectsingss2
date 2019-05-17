<?php

    namespace Models; 

    use Illuminate\Database\Eloquent\Model;
    use Illuminate\Support\Facades\DB;
    use Illuminate\Support\Facades\Crypt;
    use Illuminate\Support\Facades\Mail;

    class Notifications extends Model{
        protected $table = 'notifications';
        protected $primaryKey = 'id_notification';
        
        const CREATED_AT = 'created';
        const UPDATED_AT = 'updated';

        protected $fillable = [];

        protected $hidden = [];

        public function __construct() {}

        /**
         * [This method is used for remove] 
         * @param [Varchar]$notify [used for notify]
         * @param [Varchar]$notification_by[used for notification_by]
         * @param [Varchar]$notification[notification]
         * @param [Varchar]$notification_response_json[used for notification response json]
         * @return Boolean
         */

        public static function scopeDefaultKeys($query){
            $prefix     = DB::getTablePrefix();
            $base_url   = ___image_base_url();
            $query->addSelect([
                'notifications.id_notification',
                'notifications.notify',
                'notifications.notified_by',
                'notifications.notification',
                'notifications.notification_response_json',
                'notifications.notification_status',
                'notifications.desktop_notification_status',
                'notifications.created'
            ]);

            return $query;
        }

        /**
         * [This method is for relating chat request to receiver] 
         * @return Boolean
         */

        public function scopeSender($query){
            $prefix         = DB::getTablePrefix();
            $base_url       = ___image_base_url();

            $query->leftjoin('users as sender','sender.id_user','notifications.notified_by')
            ->leftJoin('files as files',function($leftjoin){
                $leftjoin->on('files.user_id','=','sender.id_user');
                $leftjoin->on('files.type','=',\DB::Raw('"profile"'));
            })->addSelect([
                \DB::Raw("TRIM(IF({$prefix}sender.last_name IS NULL , {$prefix}sender.first_name, CONCAT({$prefix}sender.first_name,' ',{$prefix}sender.last_name))) as sender_name"),
                \DB::Raw("
                    IF(
                         {$prefix}files.filename IS NOT NULL,
                         CONCAT('{$base_url}',{$prefix}files.folder,'thumbnail/',{$prefix}files.filename),(
                         IF({$prefix}sender.social_picture IS NOT NULL OR {$prefix}sender.social_picture != '', {$prefix}sender.social_picture  ,CONCAT('{$base_url}','images/','".DEFAULT_AVATAR_IMAGE."')))
                     ) as sender_picture
                "),
            ]);

            return $query;
                    // IF(
                    //     {$prefix}files.filename IS NOT NULL,
                    //     CONCAT('{$base_url}',{$prefix}files.folder,'thumbnail/',{$prefix}files.filename),
                    //     IF({$prefix}users.social_picture IS NOT NULL OR {$prefix}users.social_picture != '', {$prefix}users.social_picture  ,CONCAT('{$base_url}','images/','".DEFAULT_AVATAR_IMAGE."'))
                    // ) as sender_picture
        }

        public static function notify($notify, $notified_by, $notification, $notification_response_json){

            $table_notification     = \DB::table('notifications');
            if($notify != SUPPORT_CHAT_USER_ID){
                $notification_type      = \Models\Users::findById($notify,['type'])['type'];
            }else{
                $notification_type      = 'administrator';
            }
            
            $insert_data = [
                'notify'                        => $notify,
                'notified_by'                   => $notified_by,
                'notification'                  => $notification,
                'notification_response_json'    => $notification_response_json,
                'created'                       => date('Y-m-d H:i:s'),
                'updated'                       => date('Y-m-d H:i:s'),
            ];

            $isInserted = self::insertGetId($insert_data);

            /*WRITING COUNTER IN FILE */
            \File::put(public_path("uploads/notification/{$notify}.txt"), self::count($notify));

            if(!empty($isInserted)){
                /* SENDING MAIL FOR NOTIFICATION */
                if($notification == 'JOB_RAISE_DISPUTE_RECEIVED_REPLY'){
                    $notification_check = 'JOB_RAISE_DISPUTE_RECEIVED';
                }else{
                    $notification_check = $notification;
                }
                
                $email_setting = \Models\Settings::is_settings_enabled($notify,$notification_check,'email');
                if(!empty($email_setting)){
                    $receiver_information   = \Models\Users::findById($notify,['email','first_name','last_name']);
                    $sender_information     = \Models\Users::findById($notified_by,['email','first_name','last_name']);
                    
                    if(!empty($receiver_information)){
                        $payload = json_decode($notification_response_json,true);
                        
                        if($notification === 'JOB_UPDATED_BY_EMPLOYER'){
                            $subject            = trans(sprintf("notification.%s",sprintf("READABLE_%s",$notification)));
                            $notification_text  = sprintf(str_replace(".", "", trans(sprintf('notification.%s',$notification))),$payload['project_title']);
                        }else{
                            $subject            = str_replace(".", "", trans(sprintf('notification.%s',$notification)));
                            $notification_text  = trans(sprintf("notification.%s",sprintf("READABLE_%s",$notification)));
                        }

                        $emailData              = ___email_settings();
                        $emailData['email']     = $receiver_information['email'];
                        $emailData['name']      = $receiver_information['first_name'];
                        $emailData['link']      = ___get_notification_url($notification,$notification_response_json);
                        $emailData['subject']   = $subject;
                        $emailData['content']   = sprintf(
                            NOTIFICATION_TEMPLATE,
                            get_file_url(\Models\Users::get_file(sprintf(" type = 'profile' AND user_id = %s",$notified_by),'single',['filename','folder'])),
                            trim($sender_information['first_name'].' '.$sender_information['last_name']),
                            $notification_text,
                            ___d(date('Y-m-d H:i:s'))
                        );

                        ___mail_sender($receiver_information['email'],sprintf("%s %s",$receiver_information['first_name'],$receiver_information['last_name']),"notification",$emailData);
                    }
                }else{
                    //return (bool) self::notificationhistory($notify,$notification_type,$notification,json_encode(['result' => 'Notification setting is not enabled.','user_id' => $notify, 'reference_table' => 'users']));
                }

                $mobile_setting = \Models\Settings::is_settings_enabled($notify,$notification,'mobile');

                if(!empty($mobile_setting)){
                    $notification_id = $isInserted;
                    $notification_count = $table_notification->where(
                        [
                            'notify' => $notify,
                            'notification_status' => 'unread'
                        ]
                    )->count();

                    $device = \Models\Users::get_current_device($notify,['device_token','device_type']);
                    
                    if(!empty($device)){
                        if($device['device_type'] == 'android'){
                            return (bool) self::android($notify,$notification_type,$device['device_token'],$notification,$notification_id,$notification_count,$notification_response_json);
                        }else if($device['device_type'] == 'iphone'){
                            return (bool) self::ios($notify,$notification_type,$device['device_token'],$notification,$notification_id,$notification_count,$notification_response_json);
                        }
                    }else{
                        return (bool) self::notificationhistory($notify,$notification_type,$notification,json_encode(['result' => 'No device added for this user.','user_id' => $notify, 'reference_table' => 'users']));
                    }
                }else{
                    return (bool) self::notificationhistory($notify,$notification_type,$notification,json_encode(['result' => 'Notification setting is not enabled.','user_id' => $notify, 'reference_table' => 'users']));
                }
            }else{
                return (bool) false;
            }            
        } 

        /**
         * [This method is used for android] 
         * @param [Varchar]$notify [used for notify]
         * @param [Varchar]$notification_type[used for notification_type]
         * @param [Varchar]$device_token[device_token]
         * @param [Varchar]$notification[notification]
         * @param [Integer]$notification_id[Used for notification id]
         * @param [Varchar]$notification_count[used for notification count]
         * @param [Varchar]$notification_response_json[used for notification response json]
         * @return Json Response
         */ 

        public static function android($notify,$notification_type,$device_token,$notification,$notification_id,$notification_count,$notification_response_json) {/*dd(json_decode($notification_response_json,true));*/
            $users_data             = \Models\Users::findById($notify,['type']);
            $push_notification      = \PushNotification::setService('fcm');
            

            if($notification == 'JOB_CHAT_REQUEST_SENT_BY_TALENT'){
                $payload = json_decode($notification_response_json,true)["chat"][0];
                $payload["request_status"] = 'pending';
            }else if($notification == 'JOB_CHAT_REQUEST_ACCEPTED_BY_EMPLOYER'){
                $payload = json_decode($notification_response_json,true)["chat"][0];
                # $receiver = $payload["receiver_id"];
                # $payload["receiver_id"] = $payload["sender_id"];
                # $payload["sender_id"] = $receiver;
            }else{
                $payload = json_decode($notification_response_json,true);
            }

            if($notification === 'JOB_UPDATED_BY_EMPLOYER'){
                $notification_text = sprintf(trans(sprintf('notification.%s',$notification)),$payload['project_title']);
            }else if($notification === 'JOB_RAISE_DISPUTE_RECEIVED'){
                $notification_text = sprintf(trans(sprintf('notification.%s',$notification)),sprintf("#%'.0".JOBID_PREFIX."d",$payload['project_id']));
            }else if($notification === 'JOB_START_REMINDER'){
                $type = \DB::table('users')->where('id_user',$notify)->select('type')->first();
                if($type->type=='talent'){
                    $notification_text = $payload['msg'];
                    // dd($notification_text);
                }
                if($type->type=='employer'){
                    $notification_text = 'hello';
                }
            }else{
                $notification_text = trans(sprintf('notification.%s',$notification));
            }

            $push_notification->setMessage([
                'data' => [
                    'sound'                 => 'default',
                    'title'                 => 'Crowbar',
                    'body'                  => $notification_text,
                    'id'                    => $notification_id,
                    'notification_key'      => $notification,
                    'proposal'              => self::unread_notifications($notify,'proposals',$users_data['type']),
                    'count'                 => (int)self::unread_notifications($notify),
                    'payload'               => $payload,
                ]
            ]);

            $push_notification->setApiKey(config(sprintf('environment.PUSH_NOTIFICATION.%s.ANDROID_GOOGLE_API_KEY',app()->environment())));

            try{
                $response = json_encode($push_notification->setDevicesToken($device_token)->send()->getFeedback());
            }catch (\Exception $e ) {
                $response = json_encode(['status' => false, 'message' => $e->getMessage()]);
            }

            return self::notificationhistory($notify,$notification_type,$notification,$response.config(sprintf('environment.PUSH_NOTIFICATION.%s.ANDROID_GOOGLE_API_KEY',app()->environment())));
        }

        /**
         * [This method is used for remove] 
         * @param [Varchar]$notify [used for notify]
         * @param [Varchar]$notification_type[used for notification_type]
         * @param [Varchar]$device_token[device_token]
         * @param [Varchar]$notification[notification]
         * @param [Integer]$notification_id[Used for notification id]
         * @param [Varchar]$notification_count[used for notification count]
         * @param [Varchar]$notification_response_json[used for notification response json]
         * @return Json Response
         */ 

        public static function ios($notify,$notification_type,$device_token,$notification,$notification_id,$notification_count,$notification_response_json) {
            $dry_run           = true;
            $users_data        = \Models\Users::findById($notify,['type']);
            $push_notification = \PushNotification::setService('apn');

            if($notification == 'JOB_CHAT_REQUEST_SENT_BY_TALENT'){
                $payload = json_decode($notification_response_json,true)["chat"][0];
                $payload["request_status"] = 'pending';
            }else if($notification == 'JOB_CHAT_REQUEST_ACCEPTED_BY_EMPLOYER'){
                $payload = json_decode($notification_response_json,true)["chat"][0];
                # $receiver = $payload["receiver_id"];
                # $payload["receiver_id"] = $payload["sender_id"];
                # $payload["sender_id"] = $receiver;
            }else{
                $payload = json_decode($notification_response_json,true);
            }

            if($notification === 'JOB_UPDATED_BY_EMPLOYER'){
                $notification_text = sprintf(trans(sprintf('notification.%s',$notification)),$payload['project_title']);
            }else if($notification === 'JOB_RAISE_DISPUTE_RECEIVED'){
                $notification_text = sprintf(trans(sprintf('notification.%s',$notification)),sprintf("#%'.0".JOBID_PREFIX."d",$payload['project_id']));
            }else{
                $notification_text = trans(sprintf('notification.%s',$notification));
            }

            $push_notification->setMessage([
                'aps' => [
                    'alert' => $notification_text,
                    'badge' => (int) $notification_count,
                    'sound' => 'default',
                ],
                'data' => [
                    'id'                    => (string)$notification_id,
                    'notification_key'      => $notification,
                    'proposal'              => \Models\Notifications::unread_notifications($notify,'proposals',$users_data['type']),
                    'count'                 => (int)\Models\Notifications::unread_notifications($notify),
                    'payload'               => $payload,
                
                ]
            ]);

            if(app()->environment() == 'staging' || app()->environment() == 'preproduction'){
                $dry_run = false;
            }

            $push_notification->setConfig([
                'certificate'   => str_replace('app/', '', config(sprintf('environment.PUSH_NOTIFICATION.%s.IOS_APPLE_CERTIFICATE',app()->environment()))),
                'passPhrase'    => base64_decode(config(sprintf('environment.PUSH_NOTIFICATION.%s.IOS_APPLE_PASSWORD',app()->environment()))),
                'dry_run'       => $dry_run
            ]);

            try{
                $response = json_encode($push_notification->setDevicesToken($device_token)->send()->getFeedback());
            }catch (\Exception $e ) {
                $response = json_encode(['status' => false, 'message' => $e->getMessage()]);
            }

            return self::notificationhistory($notify,$notification_type,$notification,$response);
        }

        /**
         * [This method is used for notification history] 
         * @param [Integer]$user_id[Used for user id]
         * @param [Integer]$user_type[Used for user type]
         * @param [Varchar]$notification[notification]
         * @param [Varchar]$notification_response[used for notification response]
         * @return Data Response
         */ 

        public static function notificationhistory($user_id,$user_type,$notification,$notification_response){
            $table_notification      = \DB::table('notification_history');

            $isInserted = $table_notification->insert([
                'user_id' => $user_id,
                'user_type' => $user_type,
                'notification_type' => $notification,
                'notification_response' => $notification_response,
                'created' => date('Y-m-d H:i:s'),
                'updated' => date('Y-m-d H:i:s'),
            ]);

            if(!empty($isInserted)){
                return $isInserted;
            }else{
                return false;
            }
        }

        /**
         * [This method is used for user notification] 
         * @param [Integer]$user_id[Used for user id]
         * @param [Integer]$user_type[Used for user type]
         * @param [Integer]notification_id[Used for notification id]
         * @return Boolean
         */ 

        public static function is_user_notification($user_id,$user_type,$notification_id){
            $table_notification     = \DB::table('notifications');

            $is_notification = $table_notification->where([
                'id_notification' => $notification_id,
                'notify' => $user_id
            ])->count();

            if(!empty($is_notification)){
                return (bool) $is_notification;
            }else{
                return (bool) false;
            }
        }

        /**
         * [This method is used for remove] 
         * @param [Integer]$user_id[Used for user id]
         * @param [Integer]$user_type[Used for user type]
         * @param [Integer]notification_id[Used for notification id]
         * @return Data Response
         */ 

        public static function remove($user_id,$user_type,$notification_id){
            $table_notification     = \DB::table('notifications');

            return (bool) $table_notification->where(
                array(
                    'id_notification' => $notification_id,
                    'notify' => $user_id
                )
            )->update([
                'notification_status' => 'trashed',
                'notification_freshness_status' => 'no',
                'updated' => date('Y-m-d H:i:s')
            ]);
        }

        /**
         * [This method is used to getdetail] 
         * @param [Integer]$user_id[Used for user id]
         * @param [Integer]$user_type[Used for user type]
         * @param [Integer]notification_id[Used for notification id]
         * @return Data Response
         */ 

        public static function getdetail($user_id,$user_type,$notification_id){
            $table_notification     = \DB::table('notifications');

            return json_decode(
                json_encode(
                    $table_notification->where([
                        'id_notification' => $notification_id,
                        'notify' => $user_id
                    ])->get()
                ),
                true
            );
        }

        /**
         * [This method is used for unread notifications] 
         * @param [Integer]$user_id[Used for user id]
         * @param [Integer]$notification_type[Used for notification type]
         * @param [Integer]$user_type[Used for user type]
         * @return Data Response
         */ 

        public static function unread_notifications($user_id,$notification_type="", $user_type=""){
            $table_notification = DB::table('notifications');
            
            if(!empty($notification_type) && $notification_type == 'proposals'){
                if($user_type == 'employer'){
                    $table_notification->where('notification','=','JOB_PROPOSAL_SUBMITTED_BY_TALENT');  
                }else{
                    $table_notification->where('notification','=','JOB_ACCEPTED_BY_EMPLOYER');
                }
            }

            $table_notification->where('notification_status','!=','trashed')
            ->where('notify','=',$user_id)
            ->where('notification_status','unread');

            return (int) $table_notification->count();
        }

        /**
         * [This method is used for lists] 
         * @param [Integer]$user_id[Used for user id]
         * @param [Integer]$page[Used for paging]
         * @param [Integer]$limit[Used for limit]
         * @return Data Response
         */ 

        public static function lists($user_id,$page = NULL,$limit = DEFAULT_PAGING_LIMIT){
            $table_notification = DB::table('notifications as notifications');
            $prefix     = DB::getTablePrefix();
            $base_url   = ___image_base_url();

            $offset = 0;

            $notifications   = \Models\Notifications::defaultKeys()->sender()
            ->where('notification_status','!=','trashed')
            ->where('notify','=',$user_id)
            ->orderBy('notifications.created','DESC')
            ->groupBy(['notifications.id_notification']);


            $total                          = $notifications->get()->count();
            $notification_list              = $notifications->get();
            $total_unread_notifications     = \Models\Notifications::where('notification_status','!=','trashed')
            ->where('notify','=',$user_id)
            ->where('notification_status','!=','trashed')
            ->where('notification_status','unread')
            ->get()->count();

            $total_filtered_result = [];
            if(!empty($page)){
                $offset = ($page - 1)*$limit;
                $total_filtered_result  = $notifications->offset($offset)->limit($limit);
                $total_filtered_result  = $notifications->get()->count();
            }
            
            $notifications                  = json_decode(json_encode($notifications->get()),true);
            if(!empty($notifications)){
                array_walk($notifications, function(&$item){

                    $item['created']                    = ___ago($item['created']);
                    $item['notification_key']           = $item['notification'];
                    $item['notification_redirection']   = ___get_notification_url($item['notification'],$item['notification_response_json']);
                    
                    if($item['notification_key'] === 'JOB_UPDATED_BY_EMPLOYER'){
                        $payload = json_decode($item['notification_response_json'],true);
                        $item['notification']           = sprintf(trans(sprintf("notification.%s",$item['notification'])),$payload['project_title']);
                    }else if($item['notification_key'] === 'JOB_RAISE_DISPUTE_RECEIVED'){
                        $payload = json_decode($item['notification_response_json'],true);
                        $item['notification']           = sprintf(trans(sprintf("notification.%s",$item['notification'])),sprintf("#%'.0".JOBID_PREFIX."d",$payload['project_id']));
                    }else{
                        $item['notification']           = trans(sprintf("notification.%s",$item['notification']));
                    }

                    if($item['notification_key'] == 'JOB_CHAT_REQUEST_SENT_BY_TALENT' || $item['notification_key'] == 'JOB_CHAT_REQUEST_ACCEPTED_BY_EMPLOYER'){
                        $item['chat']    = json_decode($item['notification_response_json'],true)['chat'][0];
                        $item['notification_response_json'] = [];

                        $is_chat_possible_with_talent = \Models\Chats::is_chat_possible_with_talent($item['notify'],$item['notified_by']);

                        if($is_chat_possible_with_talent == 'chat_not_accepted'){
                            $item['chat']['request_status']   = 'pending';
                        }else{
                            $item['chat']['request_status']   = 'accepted';
                        }
                    }else{
                        $item['notification_response_json'] = json_decode($item['notification_response_json'],true);
                    }
                });
            }

            return [
                'total' => $total,
                'result' => $notifications,
                'result_object' => $notification_list,
                'total_filtered_result' => $total_filtered_result,
                'total_unread_notifications' => (!empty($total_unread_notifications))?$total_unread_notifications:"",
            ];
        }

        /**
         * [This method is used for notification count] 
         * @param [Integer]$user_id[Used for user id]
         * @return Data Response
         */ 

        public static function count($user_id){
            $notification_count = \Models\Notifications::where('notification_status','!=','trashed')
            ->where('notify','=',$user_id)
            ->where('notification_status','!=','trashed')
            ->where('notification_status','unread')
            ->get()->count();

            if(empty($notification_count)){
                return "";
            }else{
                return $notification_count;
            }
        }

        /**
         * [This method is used for markread] 
         * @param [Integer]notification_id[Used for notification id]
         * @param [Integer]$user_id[Used for user id]
         * @return Json Response
         */ 

        public static function markread($notification_id,$user_id){
            $users_data         = \Models\Users::findById($user_id,['type']);
            $table_notification = DB::table('notifications as notifications');

            $table_notification->where('id_notification','=',$notification_id);
            $table_notification->update(['notification_status' => 'read', 'updated' => date('Y-m-d H:i:s')]);

            $table_notification->select(['notification','notification_response_json']);
            $notification = json_decode(json_encode($table_notification->get()->first()),true);            
            
            /*WRITING COUNTER IN FILE */
            \File::put(public_path("uploads/notification/{$user_id}.txt"), self::count($user_id));

            return [
                'status' => true,
                'total_unread_proposal_notifications'   => self::unread_notifications($user_id,'proposals',$users_data['type']),
                'total_unread_notifications'            => self::unread_notifications($user_id),
                'redirect' => ___get_notification_url($notification['notification'],$notification['notification_response_json'])
            ];
        }

        /**
         * [This method is used for mark_read_desktop] 
         * @param [Integer]notification_id[Used for notification id]
         * @return Data Response
         */ 

        public static function mark_read_desktop($notification_id){
            $table_notification = DB::table('notifications');

            $table_notification->where('id_notification','=',$notification_id);
            $table_notification->update(['desktop_notification_status' => 'sent', 'updated' => date('Y-m-d H:i:s')]);

            return ['status' => true];
        }

    }