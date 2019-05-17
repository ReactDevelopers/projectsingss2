<?php namespace Modules\Report\Entities;

use Dimsav\Translatable\Translatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserReview extends Model
{
	 public $timestamps = false;
	 use SoftDeletes;
    const DELETED_AT = 'is_deleted';
     protected $table = 'mm__user_reviews';
    protected $fillable = ['id','user_id','vendor_id','title','content','rating','status'];
}
