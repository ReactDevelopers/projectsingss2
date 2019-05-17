<?php

namespace Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Mail;

class Group extends Model
{
    protected $table = 'group';
    protected $primaryKey = 'id';

    const CREATED_AT = 'created';
    const UPDATED_AT = 'updated';

    protected $fillable = ['id','name','status','created','updated'];

    /**
     * [This method is used to get activity based on action]
     * @param [Integer]$id_project[Used for user id]
     * @param [String]$action[Used for action]
     * @return Data Response
     */

    public static function getGroupList(){

    	$prefix         = \DB::getTablePrefix();
           				  \DB::statement(\DB::raw('set @row_number=0'));

        $table_group = \DB::table('group');
        $table_group->select([
        				\DB::raw('@row_number  := @row_number  + 1 AS row_number'),
        				'id',
        				'name',
        				'status','created','updated'
            ]);
       	$table_group->where('group.status','active');
       	$table_group->orderBy('group.id','desc');

        return $table_group->get();
    }

    /**
     * [This method is used to save currency] 
     * @param [Array] $answerArr [Used for answer]
     * @return Boolean 
     */

    public static function saveGroup($answerArr){
        $return = \DB::table('group')->insertGetId($answerArr);
        return $return;
    }

    /**
     * [This method is for updating ] 
     * @return Boolean
     */
    public static function getGroupDetails($id){

        $group_details = \DB::table('group')
                            ->select('*')
                            ->where('id','=',$id)
                            ->first();

        return json_decode(json_encode($group_details),true);

    }

    /**
     * [This method is for updating Group] 
     * @return Boolean
     */
    public static function updateGroup($updateArr,$id){

        $update_group = \DB::table('group')
                            ->where('id','=',$id)
                            ->update($updateArr);

        return (bool)$update_group;
    }

    /**
     * [This method is for deleting Group] 
     * @return Boolean
     */
    public static function updateStatusGroupById($id){

        $update_group = \DB::table('group')
                            ->where('id','=',$id)
                            ->update(['status'=>'deleted']);

        return (bool)$update_group;
    }

    /**
     * [This method is for checking group name] 
     * @return Boolean
     */
    public static function checkForGroupName($name){

        $table_group = \DB::table('group');
        $table_group->select('id');
        // $table_group->where('group.name','LIKE', '%'.$name.'%');
        $table_group->where('group.name','=',$name);
        $table_group->where('group.status','active');
        $table_group = $table_group->first();

        if(!empty($table_group)){
            return true;
        }else{
            return false;
        }
    }
    
}