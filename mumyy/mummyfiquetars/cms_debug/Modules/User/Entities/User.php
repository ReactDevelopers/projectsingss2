<?php namespace Modules\User\Entities;

use Dimsav\Translatable\Translatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Audittrail\Traits\AuditTrailModelTrait;

class User extends Model
{
    // use Translatable;
	use SoftDeletes, AuditTrailModelTrait;
    const DELETED_AT = 'is_deleted';

    protected $table = 'users';
    public $translatedAttributes = [];
    protected $fillable = [
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