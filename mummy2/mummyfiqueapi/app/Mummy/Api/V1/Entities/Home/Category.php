<?php

namespace App\Mummy\Api\V1\Entities\Home;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Category extends Model
{
	use SoftDeletes;
	protected $dates = ['is_deleted'];
     const DELETED_AT = 'is_deleted';
     protected $table = 'mm__categories';
    protected $fillable = ['id','name','description','sorts','country_id','status'];

}
