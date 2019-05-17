<?php

namespace App\Mummy\Api\V1\Entities\Vendors;

use Illuminate\Database\Eloquent\Model;

class VendorComment extends Model
{
     protected $table = 'mm__vendors_comment';
    protected $fillable = ['id','user_id','portfolios_id','comment','status','created_at','updated_at'];

}
