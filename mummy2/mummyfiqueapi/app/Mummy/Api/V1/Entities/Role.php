<?php

namespace App\Mummy\Api\V1\Entities;

use Illuminate\Database\Eloquent\Model;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;

class Role extends Model implements Transformable
{
    use TransformableTrait;

    protected $fillable = [];

    protected $table = 'roles';

    public function users(){
        return $this->belongsToMany('App\User','role_users','role_id','user_id');
    }

}
