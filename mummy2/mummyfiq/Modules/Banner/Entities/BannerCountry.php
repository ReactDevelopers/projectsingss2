<?php namespace Modules\Banner\Entities;

use Dimsav\Translatable\Translatable;
use Illuminate\Database\Eloquent\Model;

class BannerCountry extends Model
{
    // use Translatable;

    protected $table = 'mm__banner_country';
    public $translatedAttributes = [];
    protected $fillable = ['id','banner_id','country','created_at','updated_at'];
}
