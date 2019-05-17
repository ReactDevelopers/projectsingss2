<?php

	namespace Models;

	use Illuminate\Database\Eloquent\Model;

	class TalentSkills extends Model{
		protected $table = 'talent_skills';

		public function skills(){
			return $this->hasOne('Models\Skills','id_skill','skill_id');
		}
	}
