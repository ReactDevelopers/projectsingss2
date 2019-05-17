<?php

namespace App\Mummy\Api\V1\Entities;

use Illuminate\Database\Eloquent\Model;
use Laravel\Passport\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;

class Vendor extends Model
{
	use HasApiTokens;
	use Notifiable;
	use SoftDeletes;
     protected $table = 'users';
    protected $fillable = ['id','email','password','address','name','created_at','updated_at'];
	protected $dates = ['is_deleted','trial_ends_at'];

    const DELETED_AT = 'is_deleted';
    public function portfolios()
    {
        return $this->hasMany('App\Mummy\Api\V1\Entities\Vendors\VendorPortfolio', 'vendor_id');
    }
     public function location(){
        return $this->hasMany('App\Mummy\Api\V1\Entities\Vendors\VendorLocation','user_id');
    }
    public function categories(){
        return $this->belongsToMany('App\Mummy\Api\V1\Entities\Home\Category','mm__vendors_category','user_id','category_id')->withPivot('status', 'is_primary','price_range_id','sub_category_id','sorts','id');
    }
     public function profile(){
        return $this->hasOne('App\Mummy\Api\V1\Entities\Vendors\VendorProfile','user_id');
    }
   public function roles(){
        return $this->belongsToMany('App\Mummy\Api\V1\Entities\Role','role_users','user_id','role_id');
    }

    public function vendorSetting(){
        return $this->hasOne('App\Mummy\Api\V1\Entities\Vendors\VendorSetting', 'vendor_id', 'id');
    }
}
