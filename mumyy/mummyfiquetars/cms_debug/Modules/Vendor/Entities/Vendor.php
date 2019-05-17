<?php namespace Modules\Vendor\Entities;

use Dimsav\Translatable\Translatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Audittrail\Traits\AuditTrailModelTrait;

class Vendor extends Model
{
    // use Translatable;
	use SoftDeletes, AuditTrailModelTrait;
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

    public function role()
    {
        return $this->hasOne('Modules\Vendor\Entities\UserRole', 'user_id');
    }

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
        return $this->hasMany('Modules\Vendor\Entities\VendorPlanSubscription', 'user_id');
    }

    public function vendorCredit()
    {
        return $this->hasOne('Modules\Credit\Entities\Credit', 'vendor_id');
    }

    public function vendorReview(){
        return $this->hasMany('Modules\Comment\Entities\Comment','vendor_id')->where('status','=',1)->whereNull('is_deleted');
    }

    public function getVendorBusinessName(){
    	return $this->vendorProfile ? $this->vendorProfile->business_name : '';
    }

    public function getVendorPlanName(){
        $vendorPlanSubscriptions = $this->vendorPlanSubscription->where('canceled_at', null);
        $name = "Free";
        $plan_id = 0;
        if(count($vendorPlanSubscriptions)){
            foreach ($vendorPlanSubscriptions as $item) {
                if($item->plan_id > $plan_id){
                    $name = $item->getPlanName();
                    $plan_id = $item->plan_id;
                }
            }
        }
        return $name;
    }

    public function getCreditPoint(){
        return $this->vendorCredit ? $this->vendorCredit->getPoint() : 0;
    }

    public function credit(){
        return $this->hasMany('Modules\Credit\Entities\Credit', 'vendor_id');
    }
    
    public function getSumPoint(){
        return $this->credit->sum('point');
    }

    /**
     *
     * @return status user Vendors
     *
     */
    
    public function getStatus(){
        $status = config('asgard.vendor.config.status');
        return ($this->status == 1)?$status[1]:$status[0];
    }

    public function getFullNameAttribute(){
        return ucfirst($this->first_name) .' '. ucfirst($this->last_name);
    }

    public function getLocationAttribute(){
        $location = $this->vendorLocation()->where('is_primary',1)->first();
        if ($location) {
            return $location;
        }
        return false;
    }

    public function getCountryNameAttribute(){
        $location = $this->vendorLocation()->where('is_primary',1)->first();
        if ($location) {
            return $location->country_name;
        }
        return false;
    }

    public function getCountryCodeAttribute(){
        $location = $this->vendorLocation()->where('is_primary',1)->first();
        if ($location) {
            return $location->country_code;
        }
        return false;
    }

    public function getCityNameAttribute(){
        $location = $this->vendorLocation()->where('is_primary',1)->first();
        if ($location) {
            return $location->city_name;
        }
        return false;
    }

    public function getCategoryNameAttribute(){
        $category = $this->vendorCategory()->where('is_primary',1)->first();
        if ($category !== null) {
            return $category->getCategoryName();
        }
        return null;
    }

    public function getCategorySubNameAttribute(){
        $category = $this->vendorCategory()->where('is_primary',1)->first();
        if ($category !== null) {
            return $category->getCategorySubName();
        }
        return null;
    }
}