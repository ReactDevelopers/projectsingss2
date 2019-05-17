<?php namespace Modules\Vendor\Entities;

use Dimsav\Translatable\Translatable;
use Illuminate\Database\Eloquent\Model;

class UserRole extends Model
{
    // use Translatable;
	
	protected $table = 'role_users';
    protected $fillable = ['user_id','role_id','created_at','updated_at'];
}
