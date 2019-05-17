<?php

    namespace Models; 

    use Illuminate\Database\Eloquent\Model;
    use Illuminate\Support\Facades\DB;
    use Illuminate\Support\Facades\Crypt;
    use Illuminate\Support\Facades\Mail;

    class ProjectLogs extends Model{
        protected $table   = "project_log";
        const CREATED_AT = 'created';
        const UPDATED_AT = 'updated';

        protected $fillable = [];

        protected $hidden = [];

        public function __construct(){
    	   
        }

        /**
         * [This method is for scope for calculating total timings] 
         * @return Boolean
         */

        public function scopeTotalTiming($query){
            $prefix         = DB::getTablePrefix();
            
            $query->addSelect([
                \DB::Raw("'00:00:00' as total_working_hours"),
                \DB::Raw("'0' as working_hours"),
                //\DB::Raw("IFNULL(SEC_TO_TIME(SUM(TIME_TO_SEC({$prefix}project_log.worktime))),'00:00:00') as total_working_hours"),
                //\DB::Raw("(ABS(HOUR(IFNULL(SEC_TO_TIME(SUM(TIME_TO_SEC({$prefix}project_log.worktime))),'00:00:00')))+ABS(MINUTE(IFNULL(SEC_TO_TIME(SUM(TIME_TO_SEC({$prefix}project_log.worktime))),'00:00:00'))/60)+ABS(SECOND(IFNULL(SEC_TO_TIME(SUM(TIME_TO_SEC({$prefix}project_log.worktime))),'00:00:00'))/3600)) as working_hours"),
            ]);

            return $query;
        }  

        /**
         * [This method is used for remove] 
         * @param [String]$data [Used for data]
         * @return Boolean
         */ 

        public static function save_project_log($data){
            if(self::insert($data)){
                return true;
            }else{
                return false;
            }
        }

        /**
         * [This method is used for alredy logged] 
         * @param [Integer]$project_id[Used for project id]
         * @param [Integer]$talent_id[Used for user's id]
         * @param [Integer]$employment[Used for employement]]
         * @return Boolean
         */ 

        public static function is_alredy_logged($project_id,$talent_id,$employment){
            $prefix = DB::getTablePrefix();
            
            $table_project_log = DB::table('project_log');
            $table_project_log->whereNotNull('startdate');
            $table_project_log->whereNotNull('enddate');
            $table_project_log->where('talent_id',$talent_id);
           
            if($employment == 'daily' || $employment == 'hourly'){
                $table_project_log->where('project_id','=',$project_id);
                $table_project_log->where(\DB::Raw("DATE(created)"),'=',\DB::Raw("'".date('Y-m-d')."'"));
            }else if($employment == 'weekly'){
                $table_project_log->where('project_id','=',$project_id);
                $table_project_log->where(\DB::Raw("WEEK(created)"),'=',\DB::Raw("'".date('W')."'"));
            }else if($employment == 'monthly'){
                $table_project_log->where('project_id','=',$project_id);
                $table_project_log->where(\DB::Raw("MONTH(created)"),'=',\DB::Raw("'".date('n')."'"));
            }else if($employment == 'fixed'){
                $table_project_log->where('project_id','=',$project_id);
            }else{
                $table_project_log->where('project_log.project_id','=',$project_id);
            }

            if(empty($table_project_log->get()->count())){
                return false;
            }else{
                return true;
            }
        }

        /**
         * [This method is used for job close request] 
         * @param [Integer]$project_id[Used for project id]
         * @param [Integer]$talent_id[Used for user's id]
         * @return Data Response
         */ 

        public static function request_close_job($project_id,$talent_id){
            return self::whereRaw("
                talent_id    = '{$talent_id}'
                AND project_id     = '{$project_id}'
                AND start IS NOT NULL
            ")->update([
                'enddate'         => date('Y-m-d H:i:s'),
                'end_timestamp'   => date('Y-m-d H:i:s'),
                'updated'         => date('Y-m-d H:i:s')
            ]);
        }

        /**
         * [This method is used confirmation to start ] 
         * @param [Integer]$project_id[Used for project id]
         * @param [Integer]$employer_id[Used for employer id]]
         * @return Data Response
         */ 

        public static function confirm_start($project_id,$employer_id){
            /*DATE(startdate)    = '".date("Y-m-d")."' AND */
            return self::whereRaw("
                employer_id    = '{$employer_id}'
                AND project_id     = '{$project_id}'
                AND start          = 'pending'
            ")->update([
                'start'                     => 'confirmed',
                'start_confirm_timestamp'   => date('Y-m-d H:i:s'),
                'updated'                   => date('Y-m-d H:i:s')
            ]);
        }

        /**
         * [This method is used for confirmation to close] 
         * @param [Integer]$project_id[Used for project id]
         * @param [Integer]$employer_id[Used for employer id]
         * @return Data Response
         */ 


        public static function confirm_close($project_id,$employer_id){
            /*DATE(startdate)    = '".date("Y-m-d")."' AND */
            return self::whereRaw("
                employer_id    = '{$employer_id}'
                AND project_id     = '{$project_id}'
                AND close          = 'pending'
            ")
            ->update([
                'close'                     => 'confirmed',
                'end_confirm_timestamp'     => date('Y-m-d H:i:s'),
                'updated'                   => date('Y-m-d H:i:s')
            ]);
        }

        /**
         * [This method is used for alredy confirmed] 
         * @param [Integer]$project_id[Used for project id]
         * @param [Integer]$employer_id[Used for employer id]
         * @return Boolean
         */ 

        public static function is_alredy_confirmed($project_id,$employer_id){
            $table_project_log = DB::table('project_log');
            $is_alredy_confirmed = $table_project_log
            ->where(\DB::Raw('DATE(created)'),'=',date('Y-m-d'))
            ->where('start','confirmed')
            ->where('close','confirmed')
            ->where('project_id',$project_id)
            ->where('employer_id',$employer_id)
            ->get()
            ->count();

            if(empty($is_alredy_confirmed)){
                return false;
            }else{
                return true;
            }
        }

        /**
         * [This method is used for remove] 
         * @param [Integer]$project_id[Used for project id]
         * @return Json Response
         */ 

        public static function findById($project_id){
            $table_project_log = DB::table('project_log');
            $logs = $table_project_log->where('project_id',$project_id)->where('close','pending')->get()->first();
            
            return json_decode(json_encode($logs),true);
        }

        /**
         * [This method is used for project_log] 
         * @param [type]$where [Used for where clause]
         * @return Data Response
         */ 

        public static function project_log($where=""){
            $table_project_log = DB::table('project_log');
            $data = $table_project_log->whereRaw($where)->get();
            return $data;
        }

        /**
         * [This method is used for time log] 
         * @param [type]$project_id
         * @param [type]$talent_id
         * @return Data Response
         */ 

        public static function timelog($project_id,$talent_id){
            return self::addSelect(['workdate'])->totalTiming()
            ->where('project_id',$project_id)
            ->where('talent_id',$talent_id)
            ->groupBy(['workdate'])
            ->get();
        }
    }
