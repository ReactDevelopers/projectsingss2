<?php

namespace App\Mummy\Api\V1\Entities;

use Illuminate\Database\Eloquent\Model;

class UserReport extends Model
{
     protected $table = 'mm__user_report';
    protected $fillable = ['id','user_id','review_id','content','status','created_at','updated_at'];

}

