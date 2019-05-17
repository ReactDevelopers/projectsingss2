<?php

namespace App\Mummy\Api\V1\Entities\Vendors;

use Illuminate\Database\Eloquent\Model;

class VendorCategory extends Model
{
     protected $table = 'mm__vendors_category';
     public $timestamps = false;
    protected $fillable = ['id','user_id','category_id','sub_category_id','sub_category_custname','price_range_id','sorts','status'];

    public function vendors()
    {
        return $this->hasMany('App\Mummy\Api\V1\Entities\Vendor', 'id', 'user_id');
    }

    public function category(){
        return $this->belongsTo('App\Mummy\Api\V1\Entities\Home\Category','category_id');
    }
    public function subCategory(){
        return $this->belongsTo('App\Mummy\Api\V1\Entities\Home\SubCategory','sub_category_id');
    }

}
