<?php

namespace App\Mummy\Api\V1\Entities\Vendors;

use Illuminate\Database\Eloquent\Model;

class VendorPackage extends Model
{
     protected $table = 'mm__vendors_package';
    protected $fillable = ['id','user_id','package_id','start_date','end_date','created_at','updated_at','city_id','status'];

}
