<?php

	namespace Models;

	use Illuminate\Database\Eloquent\Model;

	class TalentIndustries extends Model{
	   	protected $table = 'talent_industries';

	   	public function industries(){
            return $this->hasOne('Models\Industries','id_industry','industry_id');
        }

        /**
         * [This method is used for getting industry by UserID] 
         * @param [Varchar]$user_id[Used for User ID]
         * @return ID
         */

        public static function get_talent_industry_by_userID($user_id){
            $get_industry = \DB::table('talent_industries')
                                ->select('industry_id')
                                ->where('user_id',$user_id)
                                ->first();

            $get_industry = json_decode(json_encode($get_industry),true);
            return $get_industry['industry_id'];
        } 
	}
