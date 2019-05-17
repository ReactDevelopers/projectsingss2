<?php

namespace App\Mummy\Api\V1\Entities;

use Illuminate\Database\Eloquent\Model;
use Prettus\Repository\Contracts\Transformable;

class QuestionVendorCategory extends Model
{

    protected $fillable = ['question_id','id','anwsers'];

    protected $table = 'mm__vendor_questions_category';

}
