<?php

	namespace Models;

	use Illuminate\Database\Eloquent\Model;

	class TalentSubindustries extends Model{
		protected $table = 'talent_subindustries';

		public function subindustries(){
			return $this->hasOne('Models\Industries','id_industry','subindustry_id');
		}
	}
