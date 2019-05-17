<?php

namespace App\Mummy\Api\V1\Entities\Home;

use Illuminate\Database\Eloquent\Model;

class City extends Model
{
     protected $table = 'mm__new_countries_cities';
    protected $fillable = ['id','name','state_id'];

}
