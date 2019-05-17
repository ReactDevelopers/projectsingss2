<?php

    namespace Models; 

    use Illuminate\Database\Eloquent\Model;
    use Illuminate\Support\Facades\DB;

    class ChatRequests extends Model{
        protected $table = 'chat_requests';
        protected $primaryKey = 'id_chat_request';
        
        const CREATED_AT = 'created';
        const UPDATED_AT = 'updated';

        protected $fillable = [];

        protected $hidden = [];
        
        /**
         * [This method is for scope for default keys] 
         * @return Boolean
         */

        public function scopeDefaultKeys($query){
            
            $query->addSelect([
                'chat_requests.id_chat_request',
                'chat_requests.sender_id',
                'chat_requests.receiver_id',
                'chat_requests.project_id',
                'chat_requests.chat_initiated',
                'chat_requests.request_status',
            ]);

            return $query;
        }

        /**
         * [This method is for relating chat request to receiver] 
         * @return Boolean
         */

        public function ScopeReceiver($query){
            $prefix         = DB::getTablePrefix();
                        
            $query->leftjoin('users as receiver','receiver.id_user','chat_requests.receiver_id')->addSelect([
                \DB::Raw("TRIM(CONCAT({$prefix}receiver.first_name,' ',{$prefix}receiver.last_name)) as receiver_name"),
                "receiver.chat_status"
            ]);

            return $query;
        }

        /**
         * [This method is for relating chat request to receiver] 
         * @return Boolean
         */

        public function ScopeSender($query){
            $prefix         = DB::getTablePrefix();

            $query->leftjoin('users as sender','sender.id_user','chat_requests.sender_id')->addSelect([
                \DB::Raw("TRIM(CONCAT({$prefix}sender.first_name,' ',{$prefix}sender.last_name)) as receiver_name"),
                "sender.chat_status"
            ]);

            return $query;
        }
    }