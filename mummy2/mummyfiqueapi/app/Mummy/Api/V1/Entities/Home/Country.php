<?php

namespace App\Mummy\Api\V1\Entities\Home;

use Illuminate\Database\Eloquent\Model;

class Country extends Model
{
     protected $table = 'mm__new_countries';
    protected $fillable = ['id','name','sortname','phonecode'];

}
