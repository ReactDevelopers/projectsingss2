<?php

namespace App\Mummy\Api\V1\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AdvertisementItem extends Model
{
	use SoftDeletes;
     protected $table = 'mm__advs_items';
    public $timestamps = false;
    protected $dates = ['is_deleted'];
     const DELETED_AT = 'is_deleted';
    protected $fillable = ['id','adv_id','media','media_thumb','type','sorts','total_click','status','is_deleted'];

}

