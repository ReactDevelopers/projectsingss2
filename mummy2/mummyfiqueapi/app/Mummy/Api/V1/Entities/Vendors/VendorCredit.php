<?php

namespace App\Mummy\Api\V1\Entities\Vendors;

use Illuminate\Database\Eloquent\Model;

class VendorCredit extends Model
{
     protected $table = 'mm__vendors_credit';
    protected $fillable = ['id','vendor_id','amount','point','created_at','updated_at'];

}
