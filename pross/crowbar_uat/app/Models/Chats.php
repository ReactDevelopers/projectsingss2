<?php

    namespace Models; 

    use Illuminate\Database\Eloquent\Model;
    use Illuminate\Support\Facades\DB;
    use Illuminate\Support\Facades\Crypt;
    use Illuminate\Support\Facades\Mail;

    class Chats extends Model{
        protected $table = 'chat';
        protected $primaryKey = 'id_chat';
        
        const CREATED_AT = 'created';
        const UPDATED_AT = 'updated';

        protected $fillable = [];

        protected $hidden = [];

        /**
         * [This method is used for adding message] 
         * @param [Varchar] $data [Used for data]
         * @return Data Response
         */

        public static function addmessage($data){
            $prefix = DB::getTablePrefix();

            if(empty($data)){
                return (bool) false;
            }
            
            $message_type   = (!empty($data['message_type']))?$data['message_type']:'text';
            $base_url       = ___image_base_url();
            $created        = date('Y-m-d H:i:s');
            
            if($message_type == 'text'){
                if(___e($data['message']) !== $data['message']){
                    $isReported = \Models\Abuse::report($data['sender_id'],$data['receiver_id'],trim($data['message']), 'abusive-words');

                    $isNotified = \Models\Notifications::notify(
                        SUPPORT_CHAT_USER_ID,
                        $data['sender_id'],
                        'REPORT_ABUSE_CONTENT_RECEIVED',
                        json_encode([
                            "group_id"      => (string) $data['group_id'],
                            "receiver_id"   => (string) $data['receiver_id'],
                            "sender_id"     => (string) $data['sender_id']
                        ])
                    );
                }

                $message = ___e(trim(htmlentities(htmlentities($data['message']))));
            }else{
                $message = trim($data['message']);
            }

            if(empty($data['timestamp'])){
                $timestamp     = microtime();
            }else{
                $timestamp = $data['timestamp'];
            }

            if(empty($data['local_chat_id'])){
                $local_chat_id = microtime();
            }else{
                $local_chat_id = $data['local_chat_id'];
            }

            $chat_packet[] = [
                'local_chat_id' => $local_chat_id,
                'message'       => addslashes($message),
                'message_type'  => $message_type,
                'sender_id'     => $data['sender_id'],
                'receiver_id'   => $data['receiver_id'],
                'group_id'      => $data['group_id'],
                'timestamp'     => $timestamp,
                'created'       => $created,
                'updated'       => $created,
            ];

            $chat_id = DB::statement(\Models\Customs::insertIgnoreQuery($chat_packet,"{$prefix}chat"));
            
            if(!empty($chat_id)){
                $result = json_decode(
                    json_encode(
                        \DB::table((new static)->getTable())
                        ->where('group_id',$data['group_id'])
                        ->where('sender_id',$data['sender_id'])
                        ->where('timestamp',$timestamp)
                        ->get()
                        ->first()
                    ),
                    true
                );

                $sender_details             = \Models\Users::findById($result['sender_id'],[
                        \DB::raw('TRIM(CONCAT(first_name," ",IFNULL(last_name,""))) as name'),
                        'chat_status as status',
                        'id_user',
                        'status as account_status'
                    ]);
                $receiver_details           = \Models\Users::findById($result['receiver_id'],[\DB::raw('TRIM(CONCAT(first_name," ",IFNULL(last_name,""))) as name'),'id_user','status as account_status']);
                
                /* SENDER DETAILS */
                $result['sender']           = (string)$sender_details['name'];
                $result['sender_picture']   = (string)get_file_url(\Models\Talents::get_file(sprintf(" type = 'profile' AND user_id = %s",$result['sender_id']),'single',['filename','folder']));
                $result['status']           = (string)$sender_details['status'];

                /* RECEIVER DETAILS */
                $result['receiver_name']    = (string)$receiver_details['name'];
                $result['receiver_picture'] = (string)get_file_url(\Models\Talents::get_file(sprintf(" type = 'profile' AND user_id = %s",$result['receiver_id']),'single',['filename','folder']));
                
                /*OTHER DETAILS*/
                $result['request_status']   = (string)'accepted';
                $result['timelog']          = (string)strtotime($result['created']);
                $result['ago']              = (string)___agoday($result['created']);

                $result['notification']     = \Models\Users::get_current_device($result['receiver_id'],['device_token','device_type']);
                
                /*ACCOUNT STATUS*/
                $result['sender_status']    = $sender_details['account_status']; 
                $result['receiver_status']  = $receiver_details['account_status']; 

                if($result['sender_status'] == 'inactive' || $result['receiver_status'] == 'inactive'){
                    \DB::table((new static)->getTable())
                    ->where('sender_id',$data['sender_id'])
                    ->where('group_id',$data['group_id'])
                    ->where('timestamp',$timestamp)
                    ->delete();
                }

                if(!empty($data['group_id'])){
                    $chat_requests = \Models\ChatRequests::where('id_chat_request',$data['group_id'])->select(\DB::Raw("LPAD({$prefix}chat_requests.project_id, ".JOBID_PREFIX.", '0') as project_id"))->get()->first();
                    $result['project_id'] = $chat_requests->project_id;
                }
            }else{
                $result = [];
            }

            return $result;
        }

         /**
         * [This method is used to update messages] 
         * @param [Varchar] $data [Used for data]
         * @return Data Response
         */


        public static function updatemessage($data){
            if(empty($data)){
                return (bool) false;
            }
            
            $chat_table = \DB::table((new static)->getTable());
            
            $chat_table->where('id_chat',$data['chat_id'])->update(['seen_status' => $data['seen_status'], 'updated' => date('Y-m-d H:i:s')]);


            if(!empty($data['chat_id'])){
                $result = json_decode(
                    json_encode(
                        $chat_table
                        ->where('id_chat',$data['chat_id'])
                        ->get()
                        ->first()
                    ),
                    true
                );
                
                $result['sender']         = \Models\Users::findById($result['sender_id'],[\DB::raw('TRIM(CONCAT(first_name," ",IFNULL(last_name,""))) as name')])['name'];
                $result['timestamp']      = strtotime($result['created']);
                $result['ago']            = ___agoday($result['created']);
                $result['sender_picture'] = get_file_url(\Models\Talents::get_file(sprintf(" type = 'profile' AND user_id = %s",$result['sender_id']),'single',['filename','folder']));

                if(!empty($data['group_id'])){
                    $chat_requests = \Models\ChatRequests::where('id_chat_request',$data['group_id'])->select(\DB::Raw("LPAD({$prefix}chat_requests.project_id, ".JOBID_PREFIX.", '0') as project_id"))->get()->first();
                    $result['project_id'] = $chat_requests->project_id;
                }
            }else{
                $result = [];
            }

            return $result;
        }

         /**
         * [This method is used to read all] 
         * @param [Varchar] $data [Used for data]
         * @return String Response
         */

        public static function readall($data){
            if(empty($data)){
                return (bool) false;
            }
            
            $chat_table = \DB::table((new static)->getTable());
            
            return $chat_table
            ->where('receiver_id',$data['receiver_id'])
            ->where('sender_id',$data['sender_id'])
            ->where('group_id',$data['group_id'])
            ->where('seen_status','!=','read')
            ->update([
                'seen_status' => $data['seen_status'], 
                'updated' => date('Y-m-d H:i:s')
            ]);
        }

         /**
         * [This method is used to get messages] 
         * @param [Integer]$sender_id [Used for sender id]
         * @param [Integer]$receiver_id[Used for receiver id]
         * @param [Integer]$page[Used for paging]
         * @param [Integer]$chat_id[Used for chat id]
         * @param [Sort]$sort[Used for sorting]
         * @param [VArchar]$admin[Used for admin]
         * @return Data Response
         */

        
        public static function getmessages($group_id, $sender_id,$receiver_id,$page = 1,$chat_id = 0,$direction = 'up',$column = 'delete_sender_status',$sort=NULL,$admin=NULL){
            $table_chat = DB::table((new static)->getTable());
            $prefix = DB::getTablePrefix();

            $table_chat->select([
                'id_chat',
                'group_id',
                'chat_requests.project_id',
                'message',
                \DB::Raw("TRIM(CONCAT({$prefix}sender.first_name,' ',{$prefix}sender.last_name)) as sender"),
                'chat.sender_id',
                \DB::Raw("'' as sender_picture"),
                \DB::Raw("TRIM(CONCAT({$prefix}receiver.first_name,' ',{$prefix}receiver.last_name)) as receiver"),
                'chat.receiver_id',
                \DB::Raw("'' as receiver_picture"),
                'message_type',
                'seen_status',
                'delete_sender_status',
                'delete_receiver_status',
                'chat.created',
                'sender.type as sender_type',
                'receiver.type as receiver_type'
            ]);

            $table_chat->leftjoin('users as sender','sender.id_user','=','chat.sender_id');
            $table_chat->leftjoin('users as receiver','receiver.id_user','=','chat.receiver_id');
            $table_chat->leftjoin('chat_requests as chat_requests','chat_requests.id_chat_request','=','chat.group_id');
            $table_chat->where($column,'=','active');
            
            if(!empty($chat_id) && $chat_id != -1){
                if($direction === 'up'){
                    $table_chat->where('id_chat','<',$chat_id);
                    $table_chat->limit(CHAT_PAGING_LIMIT);
                    $table_chat->offset(0); 
                }else{
                    $table_chat->where('id_chat','>',$chat_id);
                }
            }else if(!empty($admin)){
            }else{
                $table_chat->limit(CHAT_PAGING_LIMIT);
                $table_chat->offset(0); 
            }

            if(!empty($sort)){
                $table_chat->orderBy('id_chat','ASC');
            }else{
                $table_chat->orderBy('id_chat','DESC'); 
            }

            $messages = $table_chat->whereRaw("
                (
                    ({$prefix}chat.sender_id = {$sender_id} AND {$prefix}chat.receiver_id = {$receiver_id})
                    OR 
                    ({$prefix}chat.sender_id = {$receiver_id} AND  {$prefix}chat.receiver_id = {$sender_id})
                ) 
            ")->where('group_id',$group_id)->get();

            $result = json_decode(json_encode($messages),true);

            if(!empty($result)){
                array_walk($result,function(&$item){
                    $item['sender_picture']     = get_file_url(\Models\Talents::get_file(sprintf(" type = 'profile' AND user_id = %s",$item['sender_id']),'single',['filename','folder']));
                    $item['receiver_picture']   = get_file_url(\Models\Employers::get_file(sprintf(" type = 'profile' AND user_id = %s",$item['receiver_id']),'single',['filename','folder']));
                    $item['timestamp']          = ___t($item['created']);
                    $item['ago']                = (string)___agoday($item['created']);
                });
            }

            return $result;
        }  

         /**
         * [This method is used to get offline message] 
         * @param [Integer]$sender_id [Used for sender id]
         * @param [Integer]$chat_id[Used for chat id]
         * @param [VArchar]$message_type[Used for message type]
         * @return Data Response
         */

        public static function getofflinemessages($sender_id,$page = 1,$chat_id = 0,$direction = 'up',$column = 'delete_sender_status',$message_type = 'live'){
            $table_chat = DB::table((new static)->getTable());
            $prefix = DB::getTablePrefix();

            $table_chat->select([
                'id_chat',
                'chat.group_id',
                'message',
                \DB::Raw("TRIM(CONCAT({$prefix}sender.first_name,' ',{$prefix}sender.last_name)) as sender"),
                'chat.sender_id',
                \DB::Raw("'' as sender_picture"),
                \DB::Raw("TRIM(CONCAT({$prefix}receiver.first_name,' ',{$prefix}receiver.last_name)) as receiver"),
                "receiver.chat_status as status",
                'chat.receiver_id',
                \DB::Raw("'' as receiver_picture"),
                \DB::Raw("'accepted' as request_status"),
                'message_type',
                'seen_status',
                'delete_sender_status',
                'delete_receiver_status',
                'chat.created',
                'chat.updated',
            ]);

            $table_chat->leftjoin('users as sender','sender.id_user','=','chat.sender_id');
            $table_chat->leftjoin('users as receiver','receiver.id_user','=','chat.receiver_id');
            $table_chat->leftjoin('chat_requests','chat_requests.id_chat_request','=','chat.group_id');
            
            if($message_type == 'offline'){
                $table_chat->whereIn('seen_status',['sent']);
            }

            $table_chat->where('chat_requests.request_status','=','accepted');
            $table_chat->where($column,'=','active');
            
            if(!empty($chat_id) && $chat_id != -1){
                if($direction === 'up'){
                    $table_chat->where('id_chat','<',$chat_id);
                    $table_chat->limit(CHAT_PAGING_LIMIT);
                    $table_chat->offset(0); 
                }else{
                    $table_chat->where('id_chat','>',$chat_id);
                }
            }else{
                $table_chat->limit(CHAT_PAGING_LIMIT);
                $table_chat->offset(0); 
            }

            $table_chat->orderBy('id_chat','DESC');

            $messages = $table_chat->where("chat.receiver_id","=",$sender_id)->get();

            $result = json_decode(json_encode($messages),true);

            if(!empty($result)){
                array_walk($result,function(&$item){
                    $item['sender_picture']     = get_file_url(\Models\Talents::get_file(sprintf(" type = 'profile' AND user_id = %s",$item['sender_id']),'single',['filename','folder']));
                    $item['receiver_picture']   = get_file_url(\Models\Employers::get_file(sprintf(" type = 'profile' AND user_id = %s",$item['receiver_id']),'single',['filename','folder']));
                    $item['timestamp']          = ___t($item['created']);
                    $item['ago']                = (string)___agoday($item['created']);
                });
            }

            return $result;
        } 

         /**
         * [This method is used for acceptance] 
         * @param [Integer]$sender_id[Used for sender id]
         * @param [Varchar]$receiver_id [Used for sender id]
         * @return Data Response
         */

        public static function accept($group_id,$sender_id,$receiver_id){
            if(empty($sender_id) && empty($receiver_id) && empty($group_id)){
                return (bool) false;
            }
            
            $isAlreadyAccepted = \DB::table('chat_requests')->where([
                'id_chat_request' => $group_id,
                'sender_id' => $receiver_id,
                'receiver_id' => $sender_id,
                'chat_initiated' => 'employer-accepted',
            ])->get()->count();

            if(empty($isAlreadyAccepted)){
                $chat_table = \DB::table('chat_requests');
                $isAccepted = $chat_table->where([
                    'id_chat_request' => $group_id,
                    'sender_id' => $receiver_id,
                    'receiver_id' => $sender_id,
                ])->update([
                    'request_status' => 'accepted',
                    'chat_initiated' => 'employer-accepted',
                    'created' => date('Y-m-d H:i:s')
                ]);

                if(!empty($isAccepted)){
                    $chat = \Models\Talents::get_my_chat_list($receiver_id,NULL,$sender_id);
                    
                    /* SENDER DETAILS */
                    $sender_details              = \Models\Users::findById($sender_id,[\DB::raw('TRIM(CONCAT(first_name," ",IFNULL(last_name,""))) as name'),'chat_status as status','id_user']);
                    $chat[0]['sender']           = (string)$sender_details['name'];
                    $chat[0]['sender_picture']   = (string)get_file_url(\Models\Talents::get_file(sprintf(" type = 'profile' AND user_id = %s",$sender_id),'single',['filename','folder']));
                    $chat[0]['status']           = (string)$sender_details['status'];
                    $chat[0]['request_status']   = (string)'accepted';

                    $isNotified = \Models\Notifications::notify(
                        $receiver_id,
                        $sender_id,
                        'JOB_CHAT_REQUEST_ACCEPTED_BY_EMPLOYER',
                        json_encode([
                            "receiver_id" => (string) $receiver_id,
                            "sender_id" => (string) $sender_id,
                            "group_id" => (string) $group_id,
                            "chat" => $chat
                        ])
                    );
                }

                return $isAccepted;
            }else{
                return true;
            }

        }   

         /**
         * [This method is used for rejection] 
         * @param [Integer]$sender_id[Used for sender id]
         * @param [Varchar]$receiver_id [Used for sender id]
         * @return Data Response
         */ 

        public static function reject($group_id,$sender_id,$receiver_id){
            if(empty($sender_id) && empty($receiver_id)){
                return (bool) false;
            }
            
            $chat_table = \DB::table('chat_requests');
            
            $isRejected = $chat_table->where([
                'id_chat_request'   => $group_id,
                'sender_id'         => $receiver_id,
                'receiver_id'       => $sender_id,
            ])->update([
                'request_status' => 'pending',
                'chat_initiated' => NULL,
                'created' => date('Y-m-d H:i:s')
            ]);
            

            if(!empty($isRejected) && 0){
                $chat = \Models\Talents::get_my_chat_list($receiver_id,NULL,$sender_id);
                /* SENDER DETAILS */
                $sender_details              = \Models\Users::findById($sender_id,[\DB::raw('TRIM(CONCAT(first_name," ",IFNULL(last_name,""))) as name'),'chat_status as status','id_user']);
                $chat[0]['sender']           = (string)$sender_details['name'];
                $chat[0]['sender_picture']   = (string)get_file_url(\Models\Talents::get_file(sprintf(" type = 'profile' AND user_id = %s",$sender_id),'single',['filename','folder']));
                $chat[0]['status']           = (string)$sender_details['status'];
                $chat[0]['request_status']   = (string)'accepted';

                $isNotified = \Models\Notifications::notify(
                    $receiver_id,
                    $sender_id,
                    'JOB_CHAT_REQUEST_REJECTED_BY_EMPLOYER',
                    json_encode([
                        "receiver_id"   => (string) $receiver_id,
                        "sender_id"     => (string) $sender_id,
                        "group_id"      => (string) $group_id,
                        "chat"          => $chat
                    ])
                );
            }
        }

        /**
         * [This method is used to initiate chat request] 
         * @param [Integer]$sender_id[Used for sender id]
         * @param [Varchar]$receiver_id [Used for sender id]
         * @return Data Response
         */

        public static function initiate_chat_request($sender_id,$receiver_id,$project_id){
            
            if(empty($sender_id) && empty($receiver_id) && empty($project_id)){
                return (bool) false;
            }

            $chat_table = \DB::table('chat_requests');
            $chat_table->where([
                'project_id' => $project_id,
                'sender_id' => $sender_id,
                'receiver_id' => $receiver_id,
                'request_status' => 'pending',
                'chat_initiated' => NULL,
            ]);
            
            if($chat_table->count() > 0){
                $isUpdated = $chat_table->update([
                    'chat_initiated' => 'talent',
                    'is_terminated' => DEFAULT_NO_VALUE,
                    'created' => date('Y-m-d H:i:s')
                ]);

                $chat   = \Models\Employers::get_my_chat_list($receiver_id,NULL,$sender_id);

                /* SENDER DETAILS */
                $sender_details              = \Models\Users::findById($sender_id,[\DB::raw('TRIM(CONCAT(first_name," ",IFNULL(last_name,""))) as name'),'chat_status as status','id_user']);
                $chat[0]['sender']           = (string)$sender_details['name'];
                $chat[0]['sender_picture']   = (string)get_file_url(\Models\Employers::get_file(sprintf(" type = 'profile' AND user_id = %s",$sender_id),'single',['filename','folder']));
                $chat[0]['status']           = (string)$sender_details['status'];
                $chat[0]['request_status']   = (string)'accepted';

                $isNotified = \Models\Notifications::notify(
                    $receiver_id,
                    $sender_id,
                    "JOB_CHAT_REQUEST_SENT_BY_TALENT",
                    json_encode([
                        "sender_id" => (string) $sender_id,
                        "receiver_id" => (string) $receiver_id,
                        "project_id" => (string) $project_id,
                        "chat" => $chat
                    ])
                );
            }
            
            $chat_table = \DB::table('chat_requests');
            $chat_table->where([
                'project_id' => $project_id,
                'sender_id' => $sender_id,
                'receiver_id' => $receiver_id,
            ]);
            
            $result = $chat_table->select(['chat_initiated'])->first();
            
            if(!empty($result->chat_initiated)){
                if($result->chat_initiated == 'talent'){
                    return [
                        'status' => true,
                        'message' => 'M0280',
                        'chat_initiated' => 'talent',
                    ];
                }else if($result->chat_initiated == 'employer'){
                    return [
                        'status' => true,
                        'message' => 'M0281',
                        'chat_initiated' => 'employer',
                    ];
                }else{
                    return [
                        'status' => true,
                        'message' => 'M0282',
                        'chat_initiated' => 'employer-accepted',
                    ];
                }
            }else{
                return [
                    'status' => false,
                    'chat_initiated' => NULL
                ];
            }
        } 

        /**
         * [This method is used for employer chat request] 
         * @param [Integer]$sender_id[Used for sender id]
         * @param [Varchar]$receiver_id [Used for sender id]
         * @return Data Response
         */ 

        public static function employer_chat_request($sender_id,$receiver_id,$project_id){
            $prefix = DB::getTablePrefix();

            if(empty($sender_id) && empty($receiver_id) && empty($project_id)){
                return (bool) false;
            }
            
            $chat_table = \DB::table('chat_requests');
            $chat_table->where([
                'project_id' => $project_id,
                'sender_id' => $receiver_id,
                'receiver_id' => $sender_id,
                'request_status' => 'pending',
                'chat_initiated' => NULL,
            ]);
            
            if($chat_table->count() > 0){
                $isUpdated = $chat_table->update([
                    'chat_initiated' => 'employer',
                    'request_status' => 'accepted',
                    'is_terminated' => DEFAULT_NO_VALUE,
                    'created' => date('Y-m-d H:i:s')
                ]);
            }else{
                $isUpdated = \Models\Employers::send_chat_request($receiver_id,$sender_id,$project_id);
            }
            
            $chat_table = \DB::table('chat_requests');
            $chat_table->where([
                'project_id' => $project_id,
                'sender_id' => $receiver_id,
                'receiver_id' => $sender_id,
            ]);
            
            $result = $chat_table->select(['chat_initiated'])->first();
            
            if(!empty($result->chat_initiated)){
                if($result->chat_initiated == 'talent'){
                    return [
                        'status' => true,
                        'message' => 'M0000',
                        'chat_initiated' => 'talent',
                    ];
                }else if($result->chat_initiated == 'employer'){
                    return [
                        'status' => true,
                        'message' => 'M0000',
                        'chat_initiated' => 'employer',
                    ];
                }else{
                    return [
                        'status' => true,
                        'message' => 'M0000',
                        'chat_initiated' => 'employer-accepted',
                    ];
                }
            }else{
                return [
                    'status' => false,
                    'message' => 'M0000',
                    'chat_initiated' => NULL
                ];
            }
        }

        /**
         * [This method is used for deletion] 
         * @param [Integer]$sender_id[Used for sender id]
         * @param [Varchar]$receiver_id [Used for sender id]
         * @return Data Response
         */
  
        public static function delete_all($sender_id,$receiver_id,$column,$group_id){
            $isConversationStarted = self::is_conversation_started($sender_id,$receiver_id,$column);

            if($isConversationStarted){
                $chat_table = \DB::table('chat');
                $isDeleted = $chat_table->whereRaw("(
                    (
                        sender_id = {$sender_id} 
                        AND 
                        receiver_id = {$receiver_id}
                        AND
                        group_id = {$group_id}
                    ) 
                    OR
                    (
                        sender_id = {$receiver_id} 
                        AND 
                        receiver_id = {$sender_id}
                        AND
                        group_id = {$group_id}
                    ) 
                )")->update([
                    $column => 'trashed', 
                    'updated' => date('Y-m-d H:i:s')
                ]);

                if(!empty($isDeleted)){
                    return [
                        'message' => 'deleted_all',
                        'sender_id' => $sender_id
                    ];
                }else{
                    return [
                        'message' => 'unable_to_delete_messages',
                        'sender_id' => $sender_id
                    ];
                }
            }else{
                return [
                    'message' => 'already_deleted_messages',
                    'sender_id' => $sender_id
                ];
            }
        }        

        /**
         * [This method is used for termination] 
         * @param [Integer]$sender_id[Used for sender id]
         * @param [Varchar]$receiver_id [Used for sender id]
         * @return Data Response
         */
  
        public static function terminate($sender_id,$receiver_id,$group_id){
            $isDeleted = 0;
            $chat_requests  = \Models\ChatRequests::select(['sender_id','receiver_id'])->where("id_chat_request",$group_id)->get()->first();
            
            if(!empty($chat_requests->sender_id)){
                $isDeleted += \Models\Notifications::where(["notify" => $chat_requests->sender_id,"notification" => 'JOB_CHAT_REQUEST_ACCEPTED_BY_EMPLOYER'])->delete();
            }
            
            if(!empty($chat_requests->receiver_id)){
                $isDeleted += \Models\Notifications::where(["notify" => $chat_requests->receiver_id,"notification" => 'JOB_CHAT_REQUEST_SENT_BY_TALENT'])->delete();
            }
            
            return \Models\ChatRequests::where("id_chat_request",$group_id)->update([
                'request_status' => 'pending', 
                'chat_initiated' => NULL, 
                'is_terminated' => DEFAULT_YES_VALUE, 
                'terminated_by' => $sender_id, 
                'updated' => date('Y-m-d H:i:s')
            ]);
        }

         /**
         * [This method is used to support chat request] 
         * @param [Integer]$sender_id[Used for sender id]
         * @param [Varchar]$receiver_id [Used for sender id]
         * @return Data Response
         */
        
        public static function support_chat_request($sender_id,$receiver_id){
            if(empty($sender_id) && empty($receiver_id)){
                return (bool) false;
            }

            $chat_table = \DB::table('chat_requests');
            $chat_table->where([
                'sender_id' => $sender_id,
                'receiver_id' => $receiver_id
            ]);
            
            if($chat_table->count() < 1){
                $isInserted = $chat_table->insert([
                    'sender_id' => $sender_id,
                    'receiver_id' => $receiver_id,
                    'request_status' => 'accepted',
                    'chat_initiated' => 'employer',
                    'created' => date('Y-m-d H:i:s')
                ]);
            }

            return true;
        } 

        /**
         * [This method is used to add raise dispute topic] 
         * @param [Integer]$sender_id$id_report [Used for sender id]
         * @param [Varchar]$closeConnection [Used for close connection]
         * @return Data Response
         */
 
        public static function add_raise_dispute_topic($sender_id,$receiver_id,$message){
            if(empty($sender_id) && empty($receiver_id)){
                return (bool) false;
            }

            self::addmessage([
                'sender_id'         => $sender_id,
                'receiver_id'       => $receiver_id,
                'message'           => $message,
                'message_type'      => 'raise-dispute'
            ]);

            self::addmessage([
                'sender_id'         => $sender_id,
                'receiver_id'       => $receiver_id,
                'message'           => trans('general.M0391'),
                'message_type'      => 'text'
            ]);
        }

         /**
         * [This method is used for possible chat with employer] 
         * @param [Integer]$talent_id [Used for user's id]
         * @param [Integer]$employer_id[Used for employer id]
         * @return Data Response
         */
        
        public static function is_chat_possible_with_employer($talent_id,$employer_id){
            if(empty($talent_id) && empty($employer_id)){
                return '';
            }

            $chat_request = \DB::table('chat_requests')
            ->where('sender_id','=',$talent_id)
            ->where('receiver_id','=',$employer_id)
            ->get()
            ->first();

            if(!empty($chat_request)){
                if($chat_request->chat_initiated == 'talent'){
                    return 'chat_not_accepted';
                }else if($chat_request->chat_initiated == 'employer'){
                    return 'yes';
                }else if($chat_request->chat_initiated == 'employer-accepted'){
                    return 'yes';
                }else{
                    return 'request_for_chat';
                }
            }else{
                return 'request_for_chat';
            }
        }

        /**
         * [This method is used for possible chat with user's] 
         * @param [Integer]$employer_id [Used for employer id]
         * @param [Integer]$talent_id[Used for user's id]
         * @return Data Response
         */

        public static function is_chat_possible_with_talent($employer_id,$talent_id){
            if(empty($talent_id) && empty($employer_id)){
                return '';
            }
            
            $chat_request = \DB::table('chat_requests')
            ->where('sender_id','=',$talent_id)
            ->where('receiver_id','=',$employer_id)
            ->get()
            ->first();

            if(!empty($chat_request)){
                if($chat_request->chat_initiated == 'talent'){
                    return 'chat_not_accepted';
                }else if($chat_request->chat_initiated == 'employer'){
                    return 'yes';
                }else if($chat_request->chat_initiated == 'employer-accepted'){
                    return 'yes';
                }else{
                    return 'request_for_chat';
                }
            }else{
                return 'request_for_chat';
            }
        }

        /**
         * [This method is used to start conversation] 
         * @param [Integer]$sender_id [Used for sender id]
         * @param [Integer]$receiver_id[Used for receiver id]
         * @return Data Response
         */

        public static function is_conversation_started($sender_id,$receiver_id, $deleted = false){
            $chat_table = \DB::table('chat');
            $chat_table->whereRaw("
                (
                    (
                        (sender_id = {$sender_id})
                        AND 
                        (receiver_id = {$receiver_id})
                    ) 
                    OR
                    (
                        (sender_id = {$receiver_id})
                        AND 
                        (receiver_id = {$sender_id})
                    )
                )
            ");

            if(!empty($deleted)){
                $chat_table->where($deleted,'!=','trashed');
            }

            return $chat_table->get()->count();
        }

        public static function getChatRoomGroupId($sender_id,$receiver_id,$group_id){

            $get_group_id = \DB::table('chat_requests')
                            ->select('id_chat_request')
                            ->where('sender_id','=',$sender_id)
                            ->where('receiver_id','=',$receiver_id)
                            ->where('project_id','=',$group_id)
                            ->get()
                            ->first();

            if(!empty($get_group_id)){
                return $get_group_id->id_chat_request; 
            }else{
                return 0; 
            }
            
        }
    }
