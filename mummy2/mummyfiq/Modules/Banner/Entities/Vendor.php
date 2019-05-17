<?php namespace Modules\Banner\Entities;

use Dimsav\Translatable\Translatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Audittrail\Traits\AuditTrailModelTrait;
use Modules\Media\Support\Traits\MediaRelation;

class Vendor extends Model
{
    // use Translatable;
	use SoftDeletes, AuditTrailModelTrait;

	use MediaRelation;
    const DELETED_AT = 'is_deleted';

    protected $table = 'users';
    public $translatedAttributes = [];
    protected $fillable = [
  //   	'name',
		// 'email',
		// 'password',
		// 'login_token',
		// 'company',
		// 'phone',
		// 'image',
		// 'status',
		// 'dob',
		// 'created_at',
		// 'updated_at',
		'email',
		'password',
		'permissions',
		'last_login',
		'first_name',
		'last_name',
		'remember_token',
		'facebook_id',
		'google_id',
		'is_verified',
		'created_at',
		'updated_at',
		'status',
		'is_deleted',
    ];

}