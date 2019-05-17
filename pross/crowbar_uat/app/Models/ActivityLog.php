<?php

namespace Models;

use Illuminate\Database\Eloquent\Model;

class ActivityLog extends Model
{
   	protected $table = 'activity';
    protected $primaryKey = 'id_activity';

    const CREATED_AT = 'created';
    const UPDATED_AT = 'updated';

    protected $fillable = [
        'user_id',
        'user_type',
        'action',
        'reference_id',
        'reference_type',
        'reference',
        'activity_status',
    ];

    /**
     * [This method is used to get activity based on action]
     * @param [Integer]$id_project[Used for user id]
     * @param [String]$action[Used for action]
     * @return Data Response
     */

    public static function  getActivityByProjectId($id_project,$action, $first = false){

    	$prefix = \DB::getTablePrefix();
        $activityDetails = \DB::table('activity');
        					$activityDetails->select('activity.*',\DB::Raw("CONCAT({$prefix}users.first_name, ' ',{$prefix}users.last_name) AS fullname"));
        					$activityDetails->leftJoin('users','users.id_user','=','activity.user_id');
        					$activityDetails->where('activity.reference_id','=',$id_project);
        					$activityDetails->where('activity.action','=',$action);
        					$activityDetails = $activityDetails->get();
        					if($first == true){
        						$activityDetails = $activityDetails->first();
        					}

        return json_decode(json_encode($activityDetails),true);
    }
}
