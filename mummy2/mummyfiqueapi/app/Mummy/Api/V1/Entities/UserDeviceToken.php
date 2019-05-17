<?php

namespace App\Mummy\Api\V1\Entities;

use Illuminate\Database\Eloquent\Model;

class UserDeviceToken extends Model
{
     protected $table = 'mm__user_device';
    protected $fillable = ['user_id','id','device_token','created_at'];

}
