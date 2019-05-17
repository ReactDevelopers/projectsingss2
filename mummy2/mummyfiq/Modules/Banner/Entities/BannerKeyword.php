<?php namespace Modules\Banner\Entities;

use Dimsav\Translatable\Translatable;
use Illuminate\Database\Eloquent\Model;

class BannerKeyword extends Model
{
    // use Translatable;

    protected $table = 'mm__banner_keywords';
    public $translatedAttributes = [];
    protected $fillable = ['id','banner_id','keywords','created_at','updated_at'];
}
