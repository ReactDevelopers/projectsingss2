<?php namespace Modules\Customer\Entities;

use Dimsav\Translatable\Translatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Audittrail\Traits\AuditTrailModelTrait;

class Customer extends Model
{
    // use Translatable;
	use SoftDeletes;
	use AuditTrailModelTrait;
	
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

    /**
     *
     * @return status user customer
     *
     */
    
    public function getStatus(){
    	$status = config('asgard.customer.config.status');
    	return ($this->status == 1)?$status[1]:$status[0];
    }

    public function getFullNameAttribute(){
    	return ucfirst($this->first_name) .' '. ucfirst($this->last_name);
    }
}
