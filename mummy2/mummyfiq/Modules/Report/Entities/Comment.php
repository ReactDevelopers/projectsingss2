<?php namespace Modules\Report\Entities;

use Dimsav\Translatable\Translatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Audittrail\Traits\AuditTrailModelTrait;

class Comment extends Model
{
	use SoftDeletes, AuditTrailModelTrait;
    const DELETED_AT = 'is_deleted';
    
    protected $table = 'mm__vendors_comment_report';
    public $translatedAttributes = [];
    protected $fillable = [
	    'user_id',
		'comment_id',
		'content',
		'status',
		'is_deleted',
		'created_at',
		'updated_at',
    ];
}
