<?php

namespace App\Mummy\Api\V1\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserReview extends Model
{
	use SoftDeletes;
     protected $table = 'mm__user_reviews';
    protected $fillable = ['id','user_id','vendor_id','title','content','rating','created_at','updated_at'];
    const DELETED_AT = 'is_deleted';

    public function user(){
    	return $this->belongsTo('App\Mummy\Api\V1\Entities\Customer', 'user_id')->whereNull('is_deleted')->where('status', 1);
    }

    public function vendor(){
    	return $this->belongsTo('App\Mummy\Api\V1\Entities\Vendor', 'vendor_id')->whereNull('is_deleted')->where('status', 1);
    }

}

