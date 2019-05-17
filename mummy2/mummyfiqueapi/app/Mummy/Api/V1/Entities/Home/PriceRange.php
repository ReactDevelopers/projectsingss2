<?php

namespace App\Mummy\Api\V1\Entities\Home;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PriceRange extends Model
{
	use SoftDeletes;
	const DELETED_AT = 'is_deleted';
    protected $table = 'mm__price_range';
    protected $fillable = ['id','price_name','description','sorts','status'];

}

