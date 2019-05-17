<?php namespace Modules\Customer\Entities;

use Dimsav\Translatable\Translatable;
use Illuminate\Database\Eloquent\Model;

class Country extends Model
{
    // use Translatable;

	public $timestamps = false;
    protected $table = 'mm__new_countries';
    public $translatedAttributes = [];
    protected $fillable = [
		'sortname',
		'name',
		'phonecode',
    ];
}
