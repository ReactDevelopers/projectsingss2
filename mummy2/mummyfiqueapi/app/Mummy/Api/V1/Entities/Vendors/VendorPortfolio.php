<?php

namespace App\Mummy\Api\V1\Entities\Vendors;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class VendorPortfolio extends Model
{
	use SoftDeletes;
     protected $table = 'mm__vendors_portfolios';
     public $timestamps = false;
    protected $fillable = ['id','category_id','sub_category_id','city','title','description','tags','vendor_id','status','photography'];
    public function vendorPortfolioMedia()
    {
        return $this->hasMany('App\Mummy\Api\V1\Entities\Vendors\VendorPortfolioMedia', 'portfolio_id');
    }
    protected $dates = ['is_deleted'];

    const DELETED_AT = 'is_deleted';

    public function category(){
        return $this->belongsTo('App\Mummy\Api\V1\Entities\Home\Category','category_id');
    }

    public function vendor(){
        return $this->belongsTo('App\Mummy\Api\V1\Entities\Home\Vendor','vendor_id');
    }
}
