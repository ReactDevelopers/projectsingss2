<?php namespace Modules\Banner\Entities;

use Dimsav\Translatable\Translatable;
use Illuminate\Database\Eloquent\Model;

class BannerVendor extends Model
{
    // use Translatable;

    protected $table = 'mm__banner_vendor';
    public $translatedAttributes = [];
    protected $fillable = ['id','banner_id','vendor','created_at','updated_at'];
}
