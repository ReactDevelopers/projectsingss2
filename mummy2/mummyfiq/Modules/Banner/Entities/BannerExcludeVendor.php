<?php namespace Modules\Banner\Entities;

use Dimsav\Translatable\Translatable;
use Illuminate\Database\Eloquent\Model;

class BannerExcludeVendor extends Model
{
    // use Translatable;

    protected $table = 'mm__banner_exlude_vendor';
    public $translatedAttributes = [];
    protected $fillable = ['id','banner_id','excludevendor','created_at','updated_at'];
}
