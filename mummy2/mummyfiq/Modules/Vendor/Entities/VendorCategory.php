<?php namespace Modules\Vendor\Entities;

use Dimsav\Translatable\Translatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Audittrail\Traits\AuditTrailModelTrait;

class VendorCategory extends Model
{
    // use Translatable;
	use SoftDeletes;
    const DELETED_AT = 'is_deleted';

    public $timestamps = false;
    protected $table = 'mm__vendors_category';
    public $translatedAttributes = [];
    protected $fillable = [
    	'user_id',
		'category_id',
		'sub_category_id',
		'sub_category_custname',
		'is_primary',
		'price_range_id',
		'sorts',
		'status',
		'is_deleted',
    ];

    public function category(){
    	return $this->belongsTo('Modules\Category\Entities\Category', 'category_id', 'id');
    }

    public function subCategory(){
        return $this->hasOne(self::class, 'sub_category_id');
    }

    public function vendor(){
        return $this->belongsTo('Modules\Vendor\Entities\Vendor', 'user_id', 'id');
    }

    public function vendorProfile(){
        return $this->belongsTo('Modules\Vendor\Entities\VendorProfile', 'user_id', 'user_id');
    }

    public function getCategoryName(){
        return $this->category ? $this->category->name : '';
    }

    public function getBusinessName(){
        return $this->vendorProfile ? $this->vendorProfile->business_name : '';
    }

    public function getCategorySubName(){
        $category = $this->subCategory()->first();

        return $category ? $category->getCategoryName() : null;
    }
}
