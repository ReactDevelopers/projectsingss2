<?php namespace Modules\Vendor\Entities;

use Dimsav\Translatable\Translatable;
use Illuminate\Database\Eloquent\Model;

class State extends Model
{
    // use Translatable;
	
	public $timestamps = false;
    protected $table = 'mm__new_countries_states';
    public $translatedAttributes = [];
    protected $fillable = [
    	'name',
		'country_id',
		'active',
    ];
}
