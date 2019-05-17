<?php

	namespace Models; 

	use Illuminate\Database\Eloquent\Model;

	class ProjectsSubindustries extends Model{
		protected $table = 'projects_subindustries';

		public function subindustries(){
            return $this->hasOne('Models\Industries','id_industry','subindustry_id');
        }

		public function projects(){
            return $this->hasOne('Models\Projects','id_project','project_id');
        }
	}