<?php

namespace App\Mummy\Api\V1\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserPhone extends Model
{
	use SoftDeletes;
     protected $table = 'mm__user_phones';
      const DELETED_AT = 'is_deleted';
     public $timestamps = false;
    protected $fillable = ['user_id','id','phone_number','country_code','is_primary','status','is_verifyed'];

}
