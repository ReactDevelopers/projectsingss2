<?php namespace Modules\Advertisement\Entities;

use Dimsav\Translatable\Translatable;
use Illuminate\Database\Eloquent\Model;

class AdvertisementType extends Model
{
    // use Translatable;

    protected $table = 'mm__advs_type';
    public $translatedAttributes = [];
    protected $fillable = [
    	'name',
		'key',
		'photo',
		'sorts',
		'status',
		'is_deleted',
		'created_at',
		'updated_at',
    ];
}
