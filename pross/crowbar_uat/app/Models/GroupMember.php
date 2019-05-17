<?php

namespace Models;

use Illuminate\Database\Eloquent\Model;

class GroupMember extends Model
{
    protected $table = 'group_member';
    protected $primaryKey = 'id';

    const CREATED_AT = 'created';
    const UPDATED_AT = 'updated';

    protected $fillable = ['id','group_id','user_id','created','updated'];

        /**
     * [This method is used to get activity based on action]
     * @param [Integer]$id_project[Used for user id]
     * @param [String]$action[Used for action]
     * @return Data Response
     */

    public static function getGroupMembersById($group_id){

    	$prefix         = \DB::getTablePrefix();
           				  \DB::statement(\DB::raw('set @row_number=0'));

        $table_group = \DB::table('group_member');
        $table_group->select([
        						'group_member.user_id',
        						\DB::raw("TRIM(CONCAT({$prefix}users.email,' (',{$prefix}users.name,')' )) as text"),
            				]);
       	$table_group->leftJoin('users','users.id_user','=','group_member.user_id');
       	$table_group->where('group_member.group_id',$group_id);
       	$table_group->orderBy('group_member.id','desc');
       	$table_group = $table_group->get();

       	$table_group = json_decode(json_encode($table_group),true);     	 

        return $table_group;
    }

    public static function getGroupMembersByIdListing($group_id){

        $prefix         = \DB::getTablePrefix();

        $table_group = \DB::table('group_member');
        $table_group->select([
                    \DB::raw('@row_number  := @row_number  + 1 AS row_number'),
                    'group_member.user_id',
                    'group_member.group_id',
                    \DB::raw("TRIM(CONCAT({$prefix}users.email,' (',{$prefix}users.name,')' )) as text"),
                    ]);
        $table_group->leftJoin('users','users.id_user','=','group_member.user_id');
        $table_group->where('group_member.group_id',$group_id);
        $table_group->orderBy('group_member.id','desc');
        $table_group = $table_group->get();       

        return $table_group;
    }

    public static function deleteMembersByGroupId($group_id){

    	$deletedMembers = \DB::table('group_member')
									->where('group_id',$group_id)
									->delete();

		return $deletedMembers;
    }

    public static function deleteGroupMembers($group_id,$user_id){

      $deletedMembers = \DB::table('group_member')
                  ->where('group_id',$group_id)
                  ->where('user_id',$user_id)
                  ->delete();

    return $deletedMembers;
    }

    public static function getGroupMemberList($user_id){

      $prefix = \DB::getTablePrefix();
                \DB::statement(\DB::raw('set @row_number=0'));

      $table_group = \DB::table('group_member');
      $table_group->select(['group.name','group.id']);
      $table_group->leftJoin('group','group.id','=','group_member.group_id');
      $table_group->where('group_member.user_id',$user_id);
      $table_group->where('group.status','active');
      $table_group = $table_group->get();

      return json_decode(json_encode($table_group),true);
    }

    public static function getGroupMemberIds($id_group){
      $table_group = \DB::table('group_member')
      ->select([
        \DB::raw('GROUP_CONCAT(user_id) AS group_member')
      ])
      ->where('group_id', $id_group)
      ->get()
      ->first();

      return json_decode(json_encode($table_group),true);
    }
}