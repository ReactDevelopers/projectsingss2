<?php namespace Modules\Vendor\Entities;

use Dimsav\Translatable\Translatable;
use Illuminate\Database\Eloquent\Model;

class City extends Model
{
    // use Translatable;
	
	public $timestamps = false;
    protected $table = 'mm__new_countries_cities';
    public $translatedAttributes = [];
    protected $fillable = [
    	'name',
		'state_id',
		'active',
    ];
}
