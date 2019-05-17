<?php

	namespace Models;

	use Illuminate\Database\Eloquent\Model;

	class Proposals extends Model{
	    protected $table = 'talent_proposals';
	    protected $primaryKey = 'id_proposal';

		const CREATED_AT = 'created';
		const UPDATED_AT = 'updated';

	    protected $fillable = [
	        'user_id', 'coupon_id', 'project_id', 'price_unit', 'submission_fee','quoted_price', 'comments','type', 'status', 'created','updated','working_hours','from_time','to_time','accept_escrow','pay_commision_percent'
	    ];

        /**
         * [This method is for relating talent] 
         * @return Boolean
         */

        public function project(){
            return $this->hasOne('\Models\Projects','id_project','project_id');
        }

        /**
         * [This method is for relating talent] 
         * @return Boolean
         */

        public function talent(){
            return $this->hasOne('\Models\Talents','id_user','user_id');
        }

        /**
         * [This method is for relating talent] 
         * @return Boolean
         */

        public function connectedCompany(){
            return $this->hasOne('\Models\companyConnectedTalent','id_user','user_id');
        }

        /**
         * [This method is for relating talent] 
         * @return Boolean
         */

        public function file(){
            return $this->hasOne('\Models\File','record_id','id_proposal');
        }

        /**
         * [This method is for scope for quoted price] 
         * @return Boolean
         */

        public function scopeQuotedPrice($query){
            $prefix         = \DB::getTablePrefix();
            
            $query->addSelect([
                'talent_proposals.quoted_price',
                'talent_proposals.price_unit'
            ]);

            return $query;
        }

        /**
         * [This method is for scope for quoted price] 
         * @return Boolean
         */

        public function scopeConvertedQuotedPrice($query){
            $prefix         = \DB::getTablePrefix();
            
            $query->addSelect([
                \DB::Raw('`CONVERT_PRICE`('.$prefix.'talent_proposals.quoted_price, '.$prefix.'talent_proposals.price_unit, "'.request()->currency.'") AS quoted_price'),
                \DB::Raw("'".___cache('currencies')[request()->currency]."' as price_unit"),
            ]);

            return $query;
        }  

        /**
         * [This method is for scoping default keys] 
         * @return Boolean
         */

        public function scopeDefaultKeys($query){
            $base_url       = ___image_base_url();
            $prefix         = \DB::getTablePrefix();
            
            $query->addSelect([
                'id_proposal',
                'project_id',
                'user_id',
                'comments',
                'status',
                'payment',
                'working_hours',
                'from_time',
                'to_time',
                'created',
                \DB::Raw("
                    (
                        ABS(HOUR(IFNULL(SEC_TO_TIME(SUM(TIME_TO_SEC({$prefix}talent_proposals.working_hours))),'00:00:00')))+
                        ABS(MINUTE(IFNULL(SEC_TO_TIME(SUM(TIME_TO_SEC({$prefix}talent_proposals.working_hours))),'00:00:00'))/60)+
                        ABS(SECOND(IFNULL(SEC_TO_TIME(SUM(TIME_TO_SEC({$prefix}talent_proposals.working_hours))),'00:00:00'))/3600)
                    ) as decimal_working_hours
                "),
            ])->groupBy('id_proposal');

            return $query;
        }

        /**
         * [This method is for scope for default keys] 
         * @return Boolean
         */

        public function scopeIsProjectClosed($query){
            $prefix         = \DB::getTablePrefix();
            $current_date   = date('Y-m-d');
            $query->where(
                \DB::Raw("
                    IF(
                        (DATE({$prefix}projects.startdate) > DATE('{$current_date}') && DATE({$prefix}projects.enddate) > DATE('{$current_date}')),
                        'pending',
                        IF(
                            (DATE({$prefix}projects.startdate) <= DATE('{$current_date}') && DATE({$prefix}projects.enddate) >= DATE('{$current_date}')),
                            'initiated',
                            'closed'
                        )
                    )
                "),
                'closed'
            );

            return $query;
        }  

        /**
         * [This method is for scope for default keys] 
         * @return Boolean
         */

        public function scopeIsProjectPending($query){
            $prefix         = \DB::getTablePrefix();
            $current_date   = date('Y-m-d');
            $query->where(
                \DB::Raw("
                    IF(
                        (DATE({$prefix}projects.startdate) < DATE('{$current_date}') && DATE({$prefix}projects.enddate) < DATE('{$current_date}')),
                        'pending',
                        IF(
                            (DATE({$prefix}projects.startdate) >= DATE('{$current_date}') && DATE({$prefix}projects.enddate) <= DATE('{$current_date}')),
                            'initiated',
                            'closed'
                        )
                    )
                "),
                'pending'
            );

            return $query;
        }  


        /**
         * [This method is used for listing] 
         * @param [Integer]$employer_id [Used for employer id]
         * @param [Integer]$page[Used for paging]
         * @param [Sort]$sort[Used for sorting]
         * @param [Search]$search [Used for searching]
         * @param [Integer]$limit[Used for limiting]
         * @return Data Response
         */ 

        public static function lists($employer_id, $page = NULL, $sort = NULL, $search = NULL, $limit = DEFAULT_PAGING_LIMIT){
            
            $table_projects = \DB::table('projects as projects');
            $prefix         = \DB::getTablePrefix();

            if(!empty($page)){
                $table_projects->limit($limit);
                $table_projects->offset(($page - 1)*$limit);  
            }

            $table_projects->select([
                'projects.id_project',
                'projects.title',
                'projects.created',
                \DB::Raw("COUNT({$prefix}proposals.id_proposal) as total_proposals"),
                \DB::Raw("
                    IF(
                        (
                            SELECT COUNT(proposal.id_proposal) FROM {$prefix}talent_proposals as proposal 
                            WHERE proposal.project_id = {$prefix}proposals.project_id
                            AND proposal.status = 'accepted'
                        ),
                        '".DEFAULT_YES_VALUE."',
                        '".DEFAULT_NO_VALUE."'
                    ) as proposal_current_status
                "),
            ]);

            $table_projects->leftJoin('talent_proposals as proposals','proposals.project_id','=','projects.id_project');
            $table_projects->where('projects.user_id','=',$employer_id);
            $table_projects->groupBy(['projects.id_project']);
            $table_projects->orderBy('projects.updated','DESC');
            return $table_projects->get();
        }

        /**
         * [This method is used for accepted proposals] 
         * @param [Integer]$project_id[Used for project id]
         * @return Json Response
         */ 

	    public static function accepted_proposal($project_id){
            $prefix = \DB::getTablePrefix();
            $table_talent_proposals = \DB::table('talent_proposals as talent_proposals');
            
            $table_talent_proposals->select([
                'talent.email as accepted_talent_email',
                'talent_proposals.user_id',
                'talent_proposals.project_id',
                'talent_proposals.id_proposal',
                'talent_proposals.quoted_price',
                'projects.employment',
                'projects.title',
                'employer.company_name',
                \DB::Raw("(SUM(TIMESTAMPDIFF(Second,{$prefix}project_log.startdate,{$prefix}project_log.enddate))/3600) as working_hours")
            ]);

            $table_talent_proposals->leftJoin('projects','projects.id_project','=','talent_proposals.project_id');
            $table_talent_proposals->leftJoin('project_log',function($leftjoin){
                $leftjoin->on('project_log.project_id','=','talent_proposals.project_id');
                $leftjoin->whereNotNull('project_log.startdate');
                $leftjoin->whereNotNull('project_log.enddate');
            });
            $table_talent_proposals->leftJoin('users as employer','employer.id_user','=','projects.user_id');
            $table_talent_proposals->leftJoin('users as talent','talent.id_user','=','talent_proposals.user_id');
            $table_talent_proposals->where('talent_proposals.project_id','=',$project_id);
            $table_talent_proposals->where('talent_proposals.status','=','accepted');
            $table_talent_proposals->groupBy(['talent_proposals.project_id']);

            return json_decode(json_encode($table_talent_proposals->get()->first()),true);
        }

        /**
         * [This method is used for accepted proposals] 
         * @param [Integer]$project_id[Used for project id]
         * @return Json Response
         */ 

        public static function get_accepted_proposal($project_id){
            $prefix = \DB::getTablePrefix();
            $table_talent_proposals = \DB::table('talent_proposals as talent_proposals');
            
            $table_talent_proposals->select([
                'talent.email as accepted_talent_email',
                'talent_proposals.user_id',
                'talent_proposals.project_id',
                'talent_proposals.id_proposal',
                'talent_proposals.quoted_price',
                'projects.employment',
                'projects.title',
                'employer.company_name',
            ]);

            $table_talent_proposals->leftJoin('projects','projects.id_project','=','talent_proposals.project_id');
            $table_talent_proposals->leftJoin('users as employer','employer.id_user','=','projects.user_id');
            $table_talent_proposals->leftJoin('users as talent','talent.id_user','=','talent_proposals.user_id');
            $table_talent_proposals->where('talent_proposals.project_id','=',$project_id);
            $table_talent_proposals->where('talent_proposals.status','=','accepted');
            $table_talent_proposals->groupBy(['talent_proposals.project_id']);

            return json_decode(json_encode($table_talent_proposals->get()->first()),true);
        }

        /**
         * [This method is used for proposals in detail] 
         * @param [Integer]$project_id[Used for project id]
         * @return Json Response
         */ 

        public static function proposals_detail($proposal_id){
            $table_talent_proposals = \DB::table('talent_proposals as talent_proposals');
            $prefix = \DB::getTablePrefix();
            $table_talent_proposals->select([
                'talent_proposals.user_id',
                'talent_proposals.project_id',
                'talent_proposals.id_proposal',
                'talent_proposals.quoted_price',
                'talent_proposals.price_unit',
                'talent_proposals.comments',
                'talent_proposals.created',
                'projects.employment',
                'talent_proposals.working_hours',
                'projects.title',
                'projects.employment',
                'employer.company_name',
                \DB::Raw('CONCAT('.$prefix.'talent.first_name, " ",'.$prefix.'talent.last_name) AS name'),
                'proposal_document.id_file as document_id',
                'proposal_document.filename as document_name',
                'proposal_document.size as document_size',
            ]);

            $table_talent_proposals->leftJoin('projects','projects.id_project','=','talent_proposals.project_id');
            $table_talent_proposals->leftJoin('users as employer','employer.id_user','=','projects.user_id');
            $table_talent_proposals->leftJoin('users as talent','talent.id_user','=','talent_proposals.user_id');
            $table_talent_proposals->leftjoin('files as proposal_document', function($leftjoin){
                $leftjoin->on('proposal_document.record_id','=','talent_proposals.id_proposal');
                $leftjoin->where('proposal_document.type','=','proposal');
            });            
            $table_talent_proposals->where('id_proposal','=',$proposal_id);

            return json_decode(json_encode($table_talent_proposals->get()->first()),true);
        }

        /**
         * [This method is used for accepted proposal] 
         * @param [type]$project_id [<description>]
         * @return Data Response
         */ 

        public static function accepted_proposal_id($project_id){
            $proposal = \DB::table('talent_proposals')
            ->select('user_id')
            ->where('project_id',$project_id)
            ->where('status','accepted')
            ->get()
            ->first();

            if(!empty($proposal->user_id)){
                return $proposal->user_id;
            }else{
                return false;
            }
        }

        /**
         * [This method is used for accepted user's proposals] 
         * @param [type]$project_id [<description>]
         * @return Data Response
         */ 

        public static function accepted_proposal_talent($project_id){
            $proposal = \DB::table('talent_proposals')
            ->select('user_id')
            ->where('project_id',$project_id)
            ->where('status','accepted')
            ->get()
            ->first();

            if(!empty($proposal->user_id)){
                return $proposal->user_id;
            }else{
                return false;
            }
        }

        /**
         * [This method is used for project proposal in detail] 
         * @param [type]$project_id [<description>]
         * @param [Integer]$talent_id[Used for talent_id]
         * @return Json Response
         */ 

        public static function project_proposal_detail($project_id,$talent_id){
            $proposal = \DB::table('talent_proposals')
            ->where('project_id',$project_id)
            ->where('user_id',$talent_id)
            ->get()
            ->first();

            if(!empty($proposal)){
                return json_decode(json_encode($proposal),true);
            }else{
                return [];
            }
        }

        /**
         * [This method is used for user's] 
         * @param [Enum]$type[Used for type]
         * @param [Integer]$talent_id[Used for talent_id]
         * @param [Integer]$page[Used for paging]
         * @param [Varchar]$key[Used for Keys]
         * @param [Integer]$limit[Used for limit]
         * @return Data Response
         */ 

        public static function talents($type, $talent_id,$page = NULL){
            $prefix = \DB::getTablePrefix();
            $offset = ($page-1)*DEFAULT_PAGING_LIMIT;

            $proposals = Proposals::defaultKeys()->quotedPrice()->with([
                'project' => function($q){
                    $q->select(
                        'id_project'
                    )
                    ->defaultKeys()
                    ->companyName()
                    ->companyLogo();
                },
                'talent' => function($q){
                    $q->select(
                        'id_user'
                    )->name();
                },
            ])
            ->where('talent_proposals.user_id','=',$talent_id);
 
            if($type == 'active'){
                $proposals->where('talent_proposals.status','=','accepted');
            }else if($type == 'submitted'){
                $proposals->where('talent_proposals.status','!=','accepted');
            }

            if(!empty($page)){
                return $proposals->groupBy(['talent_proposals.id_proposal'])->orderBy('talent_proposals.id_proposal','DESC')->limit(DEFAULT_PAGING_LIMIT)->offset($offset)->get();
            }else{
                return $proposals->groupBy(['talent_proposals.id_proposal'])->orderBy('talent_proposals.id_proposal','DESC')->get();
            }
        }

        /**
         * [This method is used for getting total count of proposals for a talent] 
         * @param [Integer]$talent_id[Used for talent_id]
         * @return Json Response
         */ 

        public static function get_talent_proposal_count($talent_id){
            $proposal_count = \DB::table('talent_proposals')
            ->where('user_id',$talent_id)
            ->count();

            return $proposal_count;
        }

        public static function getCouponDetail($id_project, $id_user){
            $couponDetail = \DB::table('coupon')
            ->select([
                'coupon.*'
            ])
            ->leftJoin('talent_proposals','talent_proposals.coupon_id','=','coupon.id')
            ->where('talent_proposals.project_id', $id_project)
            ->where('talent_proposals.user_id', $id_user)
            ->get()
            ->first();

            return json_decode(json_encode($couponDetail), true);
        }

        public static function getCouponDetailByProposalId($id_proposal){
            $couponDetail = \DB::table('coupon')
            ->select([
                'coupon.*'
            ])
            ->leftJoin('talent_proposals','talent_proposals.coupon_id','=','coupon.id')
            ->where('talent_proposals.id_proposal', $id_proposal)
            ->get()
            ->first();

            return json_decode(json_encode($couponDetail), true);
        }
    }
