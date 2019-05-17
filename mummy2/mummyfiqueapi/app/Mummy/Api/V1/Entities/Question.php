<?php

namespace App\Mummy\Api\V1\Entities;

use Illuminate\Database\Eloquent\Model;
use Prettus\Repository\Contracts\Transformable;

class Question extends Model
{

    protected $fillable = ['category_id','id','question','anwsers_type','status','question_type'];

    protected $table = 'mm__questions';

}
