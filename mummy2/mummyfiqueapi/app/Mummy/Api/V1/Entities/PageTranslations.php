<?php

namespace App\Mummy\Api\V1\Entities;

use Illuminate\Database\Eloquent\Model;

class PageTranslations extends Model
{
     protected $table = 'page__page_translations';
    public $timestamps = false;
    protected $fillable = ['id','page_id','status','body','created_at','updated_at'];

}
