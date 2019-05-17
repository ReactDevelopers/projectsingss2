<?php

namespace Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Members extends Model
{
    protected $table = 'members';
    protected $primaryKey = 'id';

    public static function add_member($data){

		$projects = DB::table((new static)->getTable());
		$insertId = $projects->insertGetId($data);

		return $insertId;
	}

	public static function getMemberRequest($user_id){

		$prefix   = DB::getTablePrefix();
        $base_url = ___image_base_url();
        $language = \App::getLocale();

		$request_list = DB::table((new static)->getTable())
				   		->select('members.*','users.created','users.id_user',
				   			DB::Raw("
			                IF(
			                    {$prefix}files.filename IS NOT NULL,
			                    CONCAT('{$base_url}',{$prefix}files.folder,{$prefix}files.filename),
			                    CONCAT('{$base_url}','images/','".DEFAULT_AVATAR_IMAGE."')
			                ) as picture
			                "),
			                DB::Raw("IF((`{$prefix}user_industry`.`{$language}` != ''),`{$prefix}user_industry`.`{$language}`, `{$prefix}user_industry`.`en`) as industry_name"),
			                DB::Raw("IF((`{$prefix}countries`.`{$language}` != ''),`{$prefix}countries`.`{$language}`, `{$prefix}countries`.`en`) as country"),
			                DB::Raw("TRIM(IF({$prefix}users.last_name IS NULL, {$prefix}users.first_name, CONCAT({$prefix}users.first_name,' ',{$prefix}users.last_name))) as name")
				   			)
				   		->leftjoin('users','users.id_user','=','members.user_id')
				   		->leftjoin('countries','countries.id_country','=','users.country')
            			->leftJoin('talent_industries as talent_industry','talent_industry.user_id','=','users.id_user')
            			->leftJoin('industries as user_industry','user_industry.id_industry','=','talent_industry.industry_id')
            			->leftjoin('files',function($leftjoin){
				            $leftjoin->on('files.user_id','=','users.id_user');
				            $leftjoin->where('files.type','=',\DB::Raw("'profile'"));
				        })
				   		->where('member_id','=',$user_id)
				   		->where('request_status','=','pending')
				   		->get();

		return json_decode($request_list,true);
	}

	public static function request_status($member_id,$user_id,$req_status){

		$update_rsvp = DB::table((new static)->getTable())
				 		->where('member_id','=',$member_id)
						->where('user_id','=',$user_id)
				 		->update(['request_status'=>$req_status]);

		return $update_rsvp;
	}
}
