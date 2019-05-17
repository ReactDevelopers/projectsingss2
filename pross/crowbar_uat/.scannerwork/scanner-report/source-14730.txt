<?php

	namespace Models; 

	use Illuminate\Database\Eloquent\Model;

	class ProjectsIndustries extends Model{
		protected $table = 'projects_industries';

		public function industries(){
            return $this->hasOne('Models\Industries','id_industry','industry_id');
        }

        /**
         * [This method is used for getting industry by JobId] 
         * @param [Varchar]$project_id[Used for Job ID]
         * @return ID
         */

        public static function get_industry_by_jobID($project_id){
            $get_industry = \DB::table('projects_industries')
                                ->select('industry_id')
                                ->where('project_id',$project_id)
                                ->first();

            $get_industry = json_decode(json_encode($get_industry),true);
            return $get_industry['industry_id'];
        } 
	}