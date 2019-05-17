<?php

namespace App\Mummy\Api\V1\Entities\Vendors;

use Illuminate\Database\Eloquent\Model;

class VendorPricelist extends Model
{
     protected $table = 'mm__vendors_profile_pricelist';
     public $timestamps = false;
    protected $fillable = ['id','user_id','category_id','sub_category_name','price_name','price_value','description'];

}
