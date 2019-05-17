<?php namespace Modules\Comment\Entities;

use Dimsav\Translatable\Translatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Audittrail\Traits\AuditTrailModelTrait;

class Vendorcomment extends Model
{
	use AuditTrailModelTrait;
	public $timestamps = true;
    protected $table = 'mm__vendors_comment';
    protected $fillable = [
    	'id',
		'user_id',
		'portfolios_id',
		'comment',
		'status',
		'created_at',
		'updated_at',
	];
}
