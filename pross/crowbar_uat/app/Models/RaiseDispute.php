<?php

    namespace Models; 

    use Illuminate\Database\Eloquent\Model;
    use Illuminate\Support\Facades\DB;
    use Illuminate\Support\Facades\Crypt;
    use Illuminate\Support\Facades\Mail;

    class RaiseDispute extends Model{
        protected $table   = "projects_dispute";
        const CREATED_AT = 'created';
        const UPDATED_AT = 'updated';

        protected $fillable = [];

        protected $hidden = [];

        public function __construct(){
    	   
        }

        /**
         * [This method is for relating dispute to sender] 
         * @return Boolean
         */

        public function sender(){
            return $this->hasOne('\Models\Users','id_user','last_commented_by');
        }

        /**
         * [This method is for relating dispute to sender] 
         * @return Boolean
         */

        public function disputedBy(){
            return $this->hasOne('\Models\Users','id_user','disputed_by');
        }  

        /**
         * [This method is for relating dispute to comments] 
         * @return Boolean
         */

        public function comments(){
            return $this->hasMany('\Models\RaiseDisputeComments','dispute_id','id_raised_dispute');
        }   

        /**
         * [This method is for relating dispute to concern] 
         * @return Boolean
         */

        public function concern(){
            return $this->hasOne('\Models\DisputeConcern','id_concern','reason');
        }   

        /**
         * [This method is for relating dispute to project] 
         * @return Boolean
         */

        public function project(){
            return $this->hasOne('\Models\Projects','id_project','project_id');
        }

        /**
         * [This method is for relating dispute to amount_agreed] 
         * @return Boolean
         */

        public function amount_agreed(){
            return $this->hasOne('\Models\Transactions','transaction_project_id','project_id');
        }

        /**
         * [This method is for relating dispute to amount_paid] 
         * @return Boolean
         */

        public function amount_paid(){
            return $this->hasOne('\Models\Transactions','transaction_project_id','project_id');
        }

        /**
         * [This method is for scope for default keys] 
         * @return Boolean
         */

        public function scopeDefaultKeys($query){
            $prefix         = DB::getTablePrefix();
            
            $query->addSelect([
                'projects_dispute.id_raised_dispute',
                'projects_dispute.project_id',
                'projects_dispute.disputed_by',
                'projects_dispute.reason',
                'projects_dispute.type',
                'projects_dispute.last_commented_by',
                'projects_dispute.last_updated',
                'projects_dispute.status',
                'projects_dispute.updated',
                'projects_dispute.created'
            ]);

            return $query;
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

        /**
         * [This method is used for detail] 
         * @param [Integer]$user_id [Used for user id]
         * @param [Integer]$project_id [Used for project id]
         * @return Json Response
         */ 

        public static function detail($project_id,$user_id){
            $dispute_detail =  self::defaultKeys()->where('projects_dispute.status','=','open')
            ->where('project_id',$project_id)
            ->get()
            ->first();

            if(!empty($dispute_detail)){
                $raise_dispute_types        = \Models\Listings::raise_dispute_type_column();
                $raise_dispute_index        = array_search($dispute_detail->type, $raise_dispute_types);

                $dispute_detail->next_type  = $raise_dispute_types[($raise_dispute_index+1)];

                if($user_id == $dispute_detail->last_commented_by){
                    $dispute_detail->duration   = date("Y-m-d",strtotime($dispute_detail->updated." +".constant("RAISE_DISPUTE_STEP_".($raise_dispute_index+1)."_HOURS_LIMIT")." hour"));
                }else{
                    $dispute_detail->duration   = ""; 
                }
            }else{
                $dispute_detail = (object)[
                    'next_type' => 'sender-comment',
                    'duration' => ""
                ];
            }

            return $dispute_detail;
        }

        /**
         * [This method is used for raise dispute] 
         * @param [Varchar]$data[Used for data]
         * @param [Varchar]$disputed_by[Used for disputed by]
         * @return Data Response
         */ 

        public static function raise_dispute($data,$disputed_by){

            $isAlreadyDisputed = self::where([
                'project_id'    => $data['project_id'], 
                'status'        => 'open'
            ]);

            if(!empty($isAlreadyDisputed->get()->count())){
                $isAlreadyDisputed = $isAlreadyDisputed->select('sender_id')->get()->first();

                if($isAlreadyDisputed->sender_id == $data['sender_id']){
                    return [
                        'status'   => false,
                        'message'  => 'M0382'
                    ];
                }else{
                    $user = \Models\Users::findById($isAlreadyDisputed->sender_id,['type']);

                    if($user['type'] == 'employer'){
                        return [
                            'status'   => false,
                            'message'  => 'M0388'
                        ];
                    }else{
                        return [
                            'status'   => false,
                            'message'  => 'M0389'
                        ];
                    }
                }
            }else{
                $isInserted = self::insert($data);
                if(!empty($isInserted)){
                    $isNotified = \Models\Notifications::notify(
                        $data['receiver_id'],
                        $data['sender_id'],
                        sprintf('JOB_DISPUTED_BY_%s',strtoupper($disputed_by)),
                        json_encode([
                            "project_id" => (string) $data['project_id'],
                            "receiver_id" => (string) $data['receiver_id'],
                            "sender_id" => (string) $data['sender_id'],
                        ])
                    );

                    if(!empty($isNotified)){
                        
                        $project    = \Models\Projects::findById($data['project_id'],['title']);

                        if($disputed_by == 'talent'){
                            /* ADD SENDER TO CHAT LIST */
                            $isSenderChatRequestSent = \Models\Chats::support_chat_request($data['sender_id'],SUPPORT_CHAT_USER_ID);
                            if(!empty($isSenderChatRequestSent)){
                                $topic_name = sprintf(trans("general.M0390"), url(sprintf("%s/find-jobs/job-details?job_id=%s",TALENT_ROLE_TYPE,___encrypt($data['project_id']))), $project['title']);
                                $sender_message = \Models\Chats::add_raise_dispute_topic(SUPPORT_CHAT_USER_ID,$data['sender_id'],$topic_name);
                            }

                            /* ADD RECEIVED TO CHAT LIST */
                            $isReceiverChatRequestSent = \Models\Chats::support_chat_request(SUPPORT_CHAT_USER_ID,$data['receiver_id']);
                            if(!empty($isReceiverChatRequestSent)){
                                $topic_name = sprintf(trans("general.M0390"), url(sprintf("%s/my-jobs/job_details?job_id=%s",EMPLOYER_ROLE_TYPE,___encrypt($data['project_id']))), $project['title']);
                                $receiver_message = \Models\Chats::add_raise_dispute_topic(SUPPORT_CHAT_USER_ID,$data['receiver_id'],$topic_name);   
                            }
                        }else{
                            $isSenderChatRequestSent = \Models\Chats::support_chat_request(SUPPORT_CHAT_USER_ID,$data['sender_id']);
                            if(!empty($isSenderChatRequestSent)){
                                $topic_name = sprintf(trans("general.M0390"), url(sprintf("%s/find-jobs/job-details?job_id=%s",TALENT_ROLE_TYPE,___encrypt($data['project_id']))), $project['title']);
                                $sender_message = \Models\Chats::add_raise_dispute_topic(SUPPORT_CHAT_USER_ID,$data['sender_id'],$topic_name);
                            }

                            $isReceiverChatRequestSent = \Models\Chats::support_chat_request($data['receiver_id'],SUPPORT_CHAT_USER_ID);
                            if(!empty($isReceiverChatRequestSent)){
                                $topic_name = sprintf(trans("general.M0390"), url(sprintf("%s/my-jobs/job_details?job_id=%s",EMPLOYER_ROLE_TYPE,___encrypt($data['project_id']))), $project['title']);
                                $receiver_message = \Models\Chats::add_raise_dispute_topic(SUPPORT_CHAT_USER_ID,$data['receiver_id'],$topic_name);   
                            }
                        }

                    }

                    return [
                        'status'   => true,
                        'message'  => 'M0383'
                    ];
                }else{
                    return [
                        'status'   => false,
                        'message'  => 'M0356'
                    ];
                }
            }
        }

        /**
         * [This method is used to get all raise dispute]
         * @param null
         * @return Data Response
         */ 

        public static function getAllRaiseDispute(){
            $prefix = DB::getTablePrefix();

            \DB::statement(\DB::raw('set @row_number=0'));
            $raise_dispute =  DB::table('projects_dispute')
            ->select([
                \DB::raw('@row_number  := @row_number  + 1 AS row_number'),
                'projects_dispute.id_raised_dispute',
                'projects_dispute.project_id',
                'projects_dispute.sender_id',
                'projects_dispute.receiver_id',
                'projects_dispute.comment as reason',
                'projects_dispute.status',
                'projects_dispute.created',
                'projects.title AS project_title',
                \DB::Raw("TRIM(CONCAT({$prefix}sender.first_name,' ',{$prefix}sender.last_name)) as sender_name"),
                \DB::Raw("TRIM(CONCAT({$prefix}receiver.first_name,' ',{$prefix}receiver.last_name)) as receiver_name"),
            ])
            ->leftJoin('users as sender','sender.id_user','=','projects_dispute.sender_id')
            ->leftJoin('users as receiver','receiver.id_user','=','projects_dispute.receiver_id')
            ->leftJoin('projects','projects.id_project','=','projects_dispute.project_id')
            ->orderBy('projects_dispute.id_raised_dispute','desc')
            ->groupBy(['projects_dispute.project_id'])
            ->get();

            return $raise_dispute;
        }

        /**
         * [This method is used to findById] 
         * @param [Integer]$dispute_id [Used for Dispute id]
         * @return Json Response
         */ 

        public static function findById($dispute_id){
            $prefix                 = DB::getTablePrefix();
            $base_url               = ___image_base_url();
            $language               = \App::getLocale();
            $table_raise_dispute    = DB::table('projects_dispute');
            $table_raise_dispute->select([
                'projects_dispute.id_raised_dispute',
                'projects_dispute.project_id',
                'projects_dispute.sender_id',
                'projects_dispute.receiver_id',
                'projects_dispute.comment as reason',
                'projects_dispute.status',
                'projects_dispute.created',
                'projects_dispute.dispute_closed_date',
                'projects.id_project',
                'projects.title',
                'projects.description',
                'projects.employment',
                'projects.price',
                'projects.price_max',
                'projects.budget_type',
                'projects.bonus',
                'projects.price_unit',
                'projects.startdate',
                'projects.enddate',
                'projects.expertise',
                'projects.created as project_created',
                \DB::Raw("IF(({$prefix}industry.{$language} != ''),{$prefix}industry.`{$language}`, {$prefix}industry.`en`) as industry_name"),
                \DB::Raw("IF(({$prefix}sub_industry.{$language} != ''),{$prefix}sub_industry.`{$language}`, {$prefix}sub_industry.`en`) as sub_industry_name"),
                'talent_proposals.id_proposal',
                'talent_proposals.quoted_price',
                \DB::Raw("(
                        SELECT 
                        {$prefix}transactions.transaction_subtotal
                        FROM {$prefix}transactions 
                        WHERE {$prefix}transactions.transaction_project_id = {$prefix}projects.id_project 
                        AND {$prefix}transactions.transaction_type = 'debit'
                        AND {$prefix}transactions.transaction_status = 'confirmed'
                    ) as amount_agreed
                "),
                'talent_proposals.created as proposal_submitted',
                'talent_proposals.comments as proposal_comments',
                \DB::Raw("SUM({$prefix}transactions.transaction_subtotal) as amount_paid"),
                'employer.company_name',
                'proposal_document.id_file as document_id',
                'proposal_document.filename as document_name',
                'proposal_document.size as document_size',
                \DB::Raw("TRIM(CONCAT({$prefix}employer.first_name,' ',{$prefix}employer.last_name)) as employer_name"),
                \DB::Raw("TRIM(CONCAT({$prefix}talent.first_name,' ',{$prefix}talent.last_name)) as talent_name"),
                \DB::Raw("TRIM(CONCAT({$prefix}admin.first_name,' ',{$prefix}admin.last_name)) as admin_name"),
                \DB::Raw("IFNULL(IF(({$prefix}city.`{$language}` != ''), {$prefix}city.`{$language}`, {$prefix}city.`en` ), '') as location_name"),
                \DB::Raw("TRIM(CONCAT({$prefix}sender.first_name,' ',{$prefix}sender.last_name)) as sender_name"),
                \DB::Raw("TRIM(CONCAT({$prefix}receiver.first_name,' ',{$prefix}receiver.last_name)) as receiver_name"),
                \DB::Raw("
                    IF(
                        {$prefix}sender_image.filename IS NOT NULL,
                        CONCAT('{$base_url}','/',{$prefix}sender_image.folder,{$prefix}sender_image.filename),
                        CONCAT('{$base_url}','/','images/','".DEFAULT_AVATAR_IMAGE."')
                    ) as sender_picture
                "),
                \DB::Raw("
                    IF(
                        {$prefix}receiver_image.filename IS NOT NULL,
                        CONCAT('{$base_url}','/',{$prefix}receiver_image.folder,{$prefix}receiver_image.filename),
                        CONCAT('{$base_url}','/','images/','".DEFAULT_AVATAR_IMAGE."')
                    ) as receiver_picture
                "),
                \DB::Raw("
                    IF(
                        {$prefix}employer_image.filename IS NOT NULL,
                        CONCAT('{$base_url}','/',{$prefix}employer_image.folder,{$prefix}employer_image.filename),
                        CONCAT('{$base_url}','/','images/','".DEFAULT_AVATAR_IMAGE."')
                    ) as employer_picture
                "),
                \DB::Raw("GROUP_CONCAT({$prefix}qualifications.qualification) as required_qualifications"),
                \DB::Raw("GROUP_CONCAT({$prefix}skills.skill) as skills"),
                \DB::Raw("(
                        SELECT 
                        (SUM(TIMESTAMPDIFF(Second,{$prefix}project_log.startdate,{$prefix}project_log.enddate))/3600)
                        FROM {$prefix}project_log 
                        WHERE {$prefix}project_log.project_id = {$prefix}projects.id_project 
                        AND {$prefix}project_log.enddate IS NOT NULL 
                        AND {$prefix}project_log.close = 'pending'
                    ) as working_hours
                "),
            ]);

            $table_raise_dispute->leftJoin('users as sender','sender.id_user','=','projects_dispute.sender_id');
            $table_raise_dispute->leftJoin('users as receiver','receiver.id_user','=','projects_dispute.receiver_id');
            $table_raise_dispute->leftJoin('projects','projects.id_project','=','projects_dispute.project_id');
            $table_raise_dispute->leftJoin('users as employer','employer.id_user','=','projects.user_id');
            $table_raise_dispute->leftJoin('industries as industry','industry.id_industry','=','projects.industry');
            $table_raise_dispute->leftJoin('industries as sub_industry','sub_industry.id_industry','=','projects.subindustry');
            $table_raise_dispute->leftJoin('city as city','city.id_city','=','projects.location');
            $table_raise_dispute->leftJoin('project_required_qualifications as qualifications','qualifications.project_id','=','projects.id_project');
            $table_raise_dispute->leftJoin('project_required_skills as skills','skills.project_id','=','projects.id_project');
            $table_raise_dispute->leftJoin('talent_proposals',function($leftjoin){
               $leftjoin->on('talent_proposals.project_id','=','projects.id_project'); 
               $leftjoin->on('talent_proposals.status','=',\DB::Raw('"accepted"')); 
            });
            $table_raise_dispute->leftJoin('transactions',function($leftjoin){
               $leftjoin->on('transactions.transaction_project_id','=','projects.id_project'); 
               $leftjoin->on('transactions.transaction_user_type','=',\DB::Raw('"talent"')); 
               $leftjoin->on('transactions.transaction_proposal_id','=','talent_proposals.id_proposal'); 
               $leftjoin->on('transactions.transaction_proposal_id','=','talent_proposals.id_proposal'); 
            });
            $table_raise_dispute->leftJoin('files as sender_image',function($leftjoin){
                $leftjoin->on('sender_image.user_id','=','sender.id_user');
                $leftjoin->on('sender_image.type','=',\DB::Raw('"profile"'));
            });
            $table_raise_dispute->leftJoin('files as receiver_image',function($leftjoin){
                $leftjoin->on('receiver_image.user_id','=','receiver.id_user');
                $leftjoin->on('receiver_image.type','=',\DB::Raw('"profile"'));
            });
            $table_raise_dispute->leftJoin('files as employer_image',function($leftjoin){
                $leftjoin->on('employer_image.user_id','=','employer.id_user');
                $leftjoin->on('employer_image.type','=',\DB::Raw('"profile"'));
            });
            $table_raise_dispute->leftJoin('users as talent','talent.id_user','=','talent_proposals.user_id');
            $table_raise_dispute->leftJoin('users as admin','admin.id_user','=','projects_dispute.dispute_closed_by');
            $table_raise_dispute->leftjoin('files as proposal_document', function($leftjoin){
                $leftjoin->on('proposal_document.record_id','=','talent_proposals.id_proposal');
                $leftjoin->where('proposal_document.type','=','proposal');
            });
            $table_raise_dispute->groupBy(['projects_dispute.project_id']);

            return json_decode(
                json_encode(
                    $table_raise_dispute->where(
                        array(
                            'id_raised_dispute' => $dispute_id,
                        )
                    )->first()
                ),
                true
            );
        }

        /**
         * [This method is used to resolve raise dispute] 
         * @param [Integer]$project_id [Used for project id]
         * @return Data Response
         */ 

        public static function resolve_raise_dispute($project_id){
            $raise_dispute = \DB::table('projects_dispute')
            ->where('project_id', $project_id)
            ->where('status', 'open')
            ->first();
            
            $isStatusUpdated = \DB::table('projects_dispute')
            ->where('project_id', $project_id)
            ->where('status', 'open')
            ->update([
                'status' => 'closed',
                'type'  => 'closed',
                'dispute_closed_by' => \Auth::guard('admin')->user()->id_user,
                'dispute_closed_date' => date('Y-m-d H:i:s'),
                'updated' => date('Y-m-d H:i:s')
            ]);
            
            $commentArray = [
                'dispute_id'    => $raise_dispute->id_raised_dispute,
                'sender_id'     => SUPPORT_CHAT_USER_ID,
                'comment'       => trans('website.crowbar_talent_response'),
                'type'          => 'closed',
                'updated'       => date('Y-m-d H:i:s'),
                'created'       => date('Y-m-d H:i:s'),
            ];

            $isCommentCreated = \Models\RaiseDisputeComments::submit($commentArray);
            
            if(!empty($isStatusUpdated)){
                $message = trans('admin.A0060');
                return true;
                
                /*$isSaved = \Models\Chats::addmessage([
                    'message'       => $message,
                    'sender_id'     => SUPPORT_CHAT_USER_ID,
                    'receiver_id'   => $raise_dispute->sender_id,
                    'message_type'  => 'raise-dispute-resolved',
                ]);
                
                $isSaved = \Models\Chats::addmessage([
                    'message'       => $message,
                    'sender_id'     => SUPPORT_CHAT_USER_ID,
                    'receiver_id'   => $raise_dispute->receiver_id,
                    'message_type'  => 'raise-dispute-resolved',
                ]);

                if(!empty($isSaved)){
                    return true;
                }*/
            }
        }
    }
