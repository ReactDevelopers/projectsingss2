<?php

    namespace Models; 

    use Illuminate\Database\Eloquent\Model;
    use Illuminate\Support\Facades\DB;
    use Illuminate\Support\Facades\Crypt;
    use Illuminate\Support\Facades\Mail;

    class Reviews extends Model{
        const CREATED_AT = 'created';
        const UPDATED_AT = 'updated';

        protected $fillable = [];

        protected $hidden = [];
        protected $table  = 'reviews';

        public function __construct(){
    	   
        }

        /**
         * [This method is for scope for default keys] 
         * @return Boolean
         */

        public function scopeDefaultKeys($query){
            $prefix         = DB::getTablePrefix();
            
            $query->addSelect([
                'reviews.id_review',
                'reviews.sender_id',
                'reviews.receiver_id',
                'reviews.project_id',
                'reviews.description',
                'reviews.category_two',
                'reviews.category_three',
                'reviews.category_four',
                'reviews.category_five',
                'reviews.category_six',
                'reviews.review_average',
                'reviews.created'
            ]);

            return $query;
        }    

        /**
         * [This method is for relating review to receiver] 
         * @return Boolean
         */

        public function sender(){
            return $this->hasOne('\Models\Users','id_user','sender_id');
        }  

        /**
         * [This method is for relating review to receiver] 
         * @return Boolean
         */

        public function receiver(){
            return $this->hasOne('\Models\Users','id_user','receiver_id');
        }  

        /**
         * [This method is for relating review to receiver] 
         * @return Boolean
         */

        public function project(){
            return $this->hasOne('\Models\Projects','id_project','project_id');
        }  

        /**
         * [This method is used to add review] 
         * @param [Varchar]$data [Used for data]
         * @return Boolean
         */ 

        public static function add_review($data){
            if(empty($data)){
                return (bool) false;
            }else{
                return self::insertGetId($data);
            }
        }

        /**
         * [This method is used review] 
         * @param [Integer]$sender_id[Used for sender id]
         * @param [Integer]$receiver_id[Used for receiver id]
         * @param [Integer]$project_id[Used for project id]
         * @return Data Response
         */ 

        public static function is_reviewed($sender_id, $receiver_id, $project_id){
            return DB::table('reviews as reviews')
            ->where('sender_id',$sender_id)
            ->where('receiver_id',$receiver_id)
            ->where('project_id',$project_id)
            ->get()->count();
        }

        /**
         * [This method is used for remove] 
         * @param [Integer]$talent_id,[Used for user's id ]
         * @param [Integer]$project_id[Used for project id]
         * @param [Enum]$review_type[Used for review type]
         * @param [Integer]$page[Used for paging]
         * @param [Varchar]$key[Used for Keys]
         * @param [Integer]$limit[Used for limit]
         * @return Data Response
         */ 

        public static function talent_reviews($talent_id,$project_id="",$review_type="by_employers",$page = 0,$keys = NULL,$limit = DEFAULT_PAGING_LIMIT){
            $table_reviews = DB::table('reviews as reviews');
            $prefix = DB::getTablePrefix();
            $offset = 0;
            
            if(empty($keys)){
                $keys           = [
                    'reviews.id_review',
                    'reviews.receiver_id',
                    'reviews.sender_id',
                    'reviews.description',
                    'reviews.review_average',
                    \DB::Raw("DATE({$prefix}reviews.created) as created"),
                    \DB::Raw("CONCAT({$prefix}employer.first_name,' ',{$prefix}employer.last_name) as employer_name"),
                ];
            }

            $table_reviews->select($keys);
            if($review_type == 'by_me'){
                $table_reviews->leftJoin('users as employer','employer.id_user','=','reviews.receiver_id');
                $table_reviews->where("reviews.sender_id",$talent_id);
            }else{
                $table_reviews->leftJoin('users as employer','employer.id_user','=','reviews.sender_id');
                $table_reviews->where("reviews.receiver_id",$talent_id);
            }

            if(!empty($project_id)){
                $table_reviews->where("reviews.project_id",$project_id);
            }


            if(!empty($page)){
                $offset = ($page - 1)*$limit;
            }
            
            $table_reviews->groupBy(['reviews.id_review']);
            $table_reviews->orderBy('reviews.id_review');
            
            $total = $table_reviews->get()->count();

            $table_reviews->offset($offset);
            $table_reviews->limit($limit);

            $reviews  = json_decode(json_encode($table_reviews->get()),true);
            $total_filtered_result = $table_reviews->get()->count();
                
            if(!empty($reviews)){
                array_walk($reviews, function(&$item) use($review_type){
                    $item['created'] = ___d($item['created']);
                    if($review_type == 'by_me'){
                        $item['picture'] = get_file_url(\Models\Talents::get_file(sprintf(" type = 'profile' AND user_id = %s",$item['receiver_id']),'single',['filename','folder']));
                    }else{
                        $item['picture'] = get_file_url(\Models\Talents::get_file(sprintf(" type = 'profile' AND user_id = %s",$item['sender_id']),'single',['filename','folder']));
                    }
                });
            }

            return [
                'total' => $total,
                'result' => $reviews,
                'total_filtered_result' => $total_filtered_result,
            ];
        } 

        /**
         * [This method is used for employer reviews] 
         * @param [Integer]$employer_id[Used for employer id]
         * @param [Integer]$project_id[Used for project id]
         * @param [Enum]$review_type[Used for review type]
         * @param [Integer]$page[Used for paging]
         * @param [Varchar]$key[Used for Keys]
         * @param [Integer]$limit[Used for limit]
         * @return Data Response
         */ 

        public static function employer_reviews($employer_id,$project_id="",$review_type="by_talents",$page = 0,$keys = NULL,$limit = DEFAULT_PAGING_LIMIT){
            $table_reviews = DB::table('reviews as reviews');
            $prefix = DB::getTablePrefix();
            $offset = 0;
            
            if(empty($keys)){
                $keys           = [
                    'reviews.id_review',
                    'reviews.receiver_id',
                    'reviews.sender_id',
                    'reviews.description',
                    'reviews.review_average',
                    \DB::Raw("DATE({$prefix}reviews.created) as created"),
                    \DB::Raw("CONCAT({$prefix}talent.first_name,' ',{$prefix}talent.last_name) as talent_name"),
                ];
            }

            $table_reviews->select($keys);

            if($review_type == 'by_me'){
                $table_reviews->leftJoin('users as talent','talent.id_user','=','reviews.receiver_id');
                $table_reviews->where("reviews.sender_id",$employer_id);
            }else{
                $table_reviews->leftJoin('users as talent','talent.id_user','=','reviews.sender_id');
                $table_reviews->where("reviews.receiver_id",$employer_id);
            }

            if(!empty($project_id)){
                $table_reviews->where("reviews.project_id",$project_id);
            }
            
            if(!empty($page)){
                $offset = ($page - 1)*$limit;
            }
            
            $table_reviews->groupBy(['reviews.id_review']);
            $table_reviews->orderBy('reviews.id_review');
            
            $total = $table_reviews->get()->count();

            $table_reviews->offset($offset);
            $table_reviews->limit($limit);

            $reviews  = json_decode(json_encode($table_reviews->get()),true);
            $total_filtered_result = $table_reviews->get()->count();
                
            if(!empty($reviews)){
                array_walk($reviews, function(&$item) use($review_type){
                    $item['created'] = ___d($item['created']);
                    if($review_type == 'by_me'){
                        $item['picture'] = get_file_url(\Models\Talents::get_file(sprintf(" type = 'profile' AND user_id = %s",$item['receiver_id']),'single',['filename','folder']));
                    }else{
                        $item['picture'] = get_file_url(\Models\Talents::get_file(sprintf(" type = 'profile' AND user_id = %s",$item['sender_id']),'single',['filename','folder']));
                    }
                });
            }

            return [
                'total' => $total,
                'result' => $reviews,
                'total_filtered_result' => $total_filtered_result,
            ];
        }

        /**
         * [This method is used for review in detail] 
         * @param [Integer]$review_id [USed for Review Id]
         * @param [Enum]$review_type[Used for review type]
         * @return Data Response
         */ 

        public static function review_detail($review_id){
            return \Models\Reviews::defaultKeys()->with([
                'sender' => function($q){
                    $q->select(
                        'id_user'
                    )->name()->companyLogo();
                },
                'receiver' => function($q){
                    $q->select(
                        'id_user'
                    )->name()->companyLogo();
                },
                'project' => function($q){
                    $q->defaultKeys()->companyName();
                }
            ])
            ->where('id_review',$review_id)
            ->get()
            ->first();
        }

        /**
         * [This method is used for listing] 
         * @param [Enum]$type[Used for type]
         * @param [Integer]$user_id [Used for user id]
         * @param [Integer]$project_id[Used for project id]
         * @param [Integer]$page[Used for paging]
         * @param [Varchar]$key[Used for Keys]
         * @param [Integer]$limit[Used for limit]
         * @return Data Response
         */ 

        public static function listing($type,$user_id,$project_id = NULL, $page = 0, $keys = NULL, $limit = DEFAULT_PAGING_LIMIT){
            $table_reviews  = DB::table('reviews as reviews');
            $prefix         = DB::getTablePrefix();
            $base_url       = ___image_base_url();

            if(empty($keys)){
                $keys           = [
                    'reviews.id_review',
                    'reviews.receiver_id',
                    'reviews.sender_id',
                    'reviews.category_two',
                    'reviews.category_three',
                    'reviews.category_four',
                    'reviews.category_five',
                    'reviews.category_six',
                    'reviews.description',
                    'reviews.review_average',
                    'reviews.created',
                    'sender.type as sender_type',
                    \DB::Raw("TRIM(CONCAT({$prefix}sender.first_name,' ',{$prefix}sender.last_name)) as sender_name"),
                    'receiver.type as receiver_type',
                    \DB::Raw("TRIM(CONCAT({$prefix}receiver.first_name,' ',{$prefix}receiver.last_name)) as receiver_name"),
                    \DB::Raw("
                        IF(
                            {$prefix}receiver_files.filename IS NOT NULL,
                            CONCAT('{$base_url}','/',{$prefix}receiver_files.folder,{$prefix}receiver_files.filename),
                            CONCAT('{$base_url}','/','images/','".DEFAULT_AVATAR_IMAGE."')
                        ) as receiver_picture
                    "),
                    \DB::Raw("
                        IF(
                            {$prefix}sender_files.filename IS NOT NULL,
                            CONCAT('{$base_url}','/',{$prefix}sender_files.folder,{$prefix}sender_files.filename),
                            CONCAT('{$base_url}','/','images/','".DEFAULT_AVATAR_IMAGE."')
                        ) as sender_picture
                    "),
                ];
            }

            $table_reviews->select($keys);
            $table_reviews->leftJoin("users as sender","sender.id_user","=","reviews.sender_id");
            $table_reviews->leftJoin("users as receiver","receiver.id_user","=","reviews.receiver_id");
            $table_reviews->leftJoin('files as receiver_files',function($leftjoin){
                $leftjoin->on('receiver_files.user_id','=','receiver.id_user');
                $leftjoin->on('receiver_files.type','=',\DB::Raw('"profile"'));
            });
            $table_reviews->leftJoin('files as sender_files',function($leftjoin){
                $leftjoin->on('sender_files.user_id','=','sender.id_user');
                $leftjoin->on('sender_files.type','=',\DB::Raw('"profile"'));
            });

            if(!empty($project_id)){
                $table_reviews->where("reviews.project_id",$project_id);
            }
            
            if($type == 'sender'){
                $table_reviews->where("reviews.sender_id",$user_id);
            }elseif($type == 'receiver'){
                $table_reviews->where("reviews.receiver_id",$user_id);
            }

            return $table_reviews->get();            
        }

        /**
         * [This method is used for summery] 
         * @param [Integer]$user_id [Used for user id]
         * @return Json Response
         */ 

        public static function summary($user_id){
            $table_reviews = DB::table('reviews');

            $reviews = $table_reviews->select([
                \DB::raw('IFNULL(ROUND(AVG(review_average), 1), "0.0") as rating'),
                \DB::raw('COUNT(id_review) as review')
            ])
            ->where('receiver_id',$user_id)
            ->get()
            ->first();

            return json_decode(json_encode($reviews),true);
        }
    }
