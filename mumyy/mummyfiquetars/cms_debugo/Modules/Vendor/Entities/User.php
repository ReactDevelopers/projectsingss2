<?php namespace Modules\Vendor\Entities;

use Dimsav\Translatable\Translatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

use Gerardojbaez\LaraPlans\Contracts\PlanSubscriberInterface;
use Gerardojbaez\LaraPlans\Traits\PlanSubscriber;

class User extends Model implements PlanSubscriberInterface
{
    use PlanSubscriber;
    
    // use Translatable;
	use SoftDeletes;
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

    public function vendorProfile()
    {
        return $this->hasOne('Modules\Vendor\Entities\VendorProfile', 'user_id');
    }
    public function vendorLocation()
    {
        return $this->hasMany('Modules\Vendor\Entities\VendorLocation', 'user_id');
    }
   	public function vendorCategory()
    {
        return $this->hasMany('Modules\Vendor\Entities\VendorCategory', 'user_id');
    }
   	public function vendorPhone()
    {
        return $this->hasMany('Modules\Vendor\Entities\VendorPhone', 'user_id');
    }

    public function vendorPlanSubscription()
    {
        return $this->hasOne('Modules\Vendor\Entities\VendorPlanSubscription', 'user_id');
    }

    public function getVendorBusinessName(){
    	return $this->vendorProfile ? $this->vendorProfile->business_name : '';
    }
}