<?php namespace Modules\Customer\Entities;

use Dimsav\Translatable\Translatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CustomerSetting extends Model
{
    // use Translatable;
	use SoftDeletes;
    const DELETED_AT = 'is_deleted';

    protected $table = 'mm__customer_settings';
    public $translatedAttributes = [];
    protected $fillable = [
		'user_id',
		'key',
		'value',
		'status',
		'is_deleted',
		'created_at',
		'updated_at',
    ];
}
