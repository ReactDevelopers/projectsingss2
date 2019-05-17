<?php namespace Modules\Customer\Entities;

use Dimsav\Translatable\Translatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CustomerChildren extends Model
{
    // use Translatable;
	use SoftDeletes;
    const DELETED_AT = 'is_deleted';
    public $timestamps = false;
    protected $table = 'mm__customer_childrens';
    public $translatedAttributes = [];
    protected $fillable = [
		'user_id',
		'name',
		'dob',
		'age',
		'sorts',
		'status',
		'is_deleted',
    ];
}
