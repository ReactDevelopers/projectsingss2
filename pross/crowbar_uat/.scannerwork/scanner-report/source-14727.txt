<?php

    namespace Models; 

    use Illuminate\Database\Eloquent\Model;
    
    class RaiseDisputeComments extends Model{
        protected $table   = "projects_dispute_comments";
        const CREATED_AT = 'created';
        const UPDATED_AT = 'updated';

        protected $fillable = [];

        protected $hidden = [];

        public function __construct(){

        }

        /**
         * [This method is for scope for default keys] 
         * @return Boolean
         */

        public function scopeDefaultKeys($query){
            $prefix         = \DB::getTablePrefix();
            
            $query->addSelect([
                'projects_dispute_comments.id_dispute',
                'projects_dispute_comments.dispute_id',
                'projects_dispute_comments.sender_id',
                'projects_dispute_comments.comment',
                'projects_dispute_comments.type',
                'projects_dispute_comments.created'
            ]);

            return $query;
        }  

        /**
         * [This method is for relating dispute comment to sender] 
         * @return Boolean
         */

        public function sender(){
            return $this->hasOne('\Models\Users','id_user','sender_id');
        }  

        /**
         * [This method is for relating dispute files] 
         * @return Boolean
         */

        public function files(){
            return $this->hasMany('\Models\File','record_id','id_dispute');
        }  

        /**
         * [This method is used for submit] 
         * @param [Integer]$user_id [Used for user id]
         * @param [Varchar]$data[Used for data]
         * @return Data REsponse
         */ 

        public static function submit($data){
            $inserted_id = self::insertGetId($data);

            return $inserted_id;
        }
    }