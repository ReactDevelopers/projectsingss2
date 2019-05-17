<?php

	namespace Models; 

	use Illuminate\Database\Eloquent\Model;

	class InviteTalent extends Model{
	   	protected $table = 'invite_talent';	
        protected $primaryKey = 'id_invite';

        const CREATED_AT = 'created';
        const UPDATED_AT = 'updated';

        protected $fillable = ['id_invite','employer_id','talent_id','code','status','created','updated'];


        public function employerDetail(){
        	return $this->hasOne('Models\Employers','id_user','employer_id');
        }

        public function talentDetail(){
        	return $this->hasOne('Models\Talents','id_user','talent_id');
        }

	}