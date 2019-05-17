<?php namespace Modules\Banner\Entities;

use Dimsav\Translatable\Translatable;
use Illuminate\Database\Eloquent\Model;

class BannerCategory extends Model
{
    // use Translatable;

    protected $table = 'mm__banner_category';
    public $translatedAttributes = [];
    protected $fillable = ['id','banner_id','category','created_at','updated_at'];
}
