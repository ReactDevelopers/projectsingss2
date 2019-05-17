<?php namespace Modules\Comment\Entities;

use Dimsav\Translatable\Translatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Audittrail\Traits\AuditTrailModelTrait;

class Comment extends Model
{
	 public $timestamps = false;
	 use SoftDeletes, AuditTrailModelTrait;
    const DELETED_AT = 'is_deleted';
     protected $table = 'mm__user_reviews';
    protected $fillable = ['id','user_id','vendor_id','title','content','rating','status'];
}
