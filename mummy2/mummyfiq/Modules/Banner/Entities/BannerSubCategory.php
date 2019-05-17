<?php namespace Modules\Banner\Entities;

use Dimsav\Translatable\Translatable;
use Illuminate\Database\Eloquent\Model;

class BannerSubCategory extends Model
{
    // use Translatable;

    protected $table = 'mm__banner_subcategory';
    public $translatedAttributes = [];
    protected $fillable = ['id','banner_id','subcategory','created_at','updated_at'];
}
