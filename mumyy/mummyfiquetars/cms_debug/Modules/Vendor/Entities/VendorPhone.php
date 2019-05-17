<?php namespace Modules\Vendor\Entities;

use Dimsav\Translatable\Translatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class VendorPhone extends Model
{
    // use Translatable;
	use SoftDeletes;
    const DELETED_AT = 'is_deleted';
    
    protected $table = 'mm__user_phones';
    public $timestamps = false;
    public $translatedAttributes = [];
    protected $fillable = [
		'phone_number',
		'country_code',
		'is_primary',
		'is_verifyed',
		'status',
		'is_deleted',
		'user_id',
		'location_id',
    ];
}
