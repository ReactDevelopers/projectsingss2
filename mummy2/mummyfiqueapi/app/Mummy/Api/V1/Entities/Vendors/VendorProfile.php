<?php

namespace App\Mummy\Api\V1\Entities\Vendors;

use Illuminate\Database\Eloquent\Model;

class VendorProfile extends Model
{
     protected $table = 'mm__vendors_profile';
     public $timestamps = false;
    protected $fillable = ['id','user_id','business_name','photo','dimension'];

}
