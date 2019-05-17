<?php

	namespace Models; 

	use Illuminate\Database\Eloquent\Model;

	class ProjectRequiredSkills extends Model{
		protected $table = 'project_required_skills';	

		public function skills(){
            return $this->hasOne('Models\Skills','id_skill','skill_id');
        }
	}