<?php namespace Modules\Review\Entities;

use Dimsav\Translatable\Translatable;
use Illuminate\Database\Eloquent\Model;
use Modules\Audittrail\Traits\AuditTrailModelTrait;

class Review extends Model
{
	use AuditTrailModelTrait;

    protected $table = 'mm__vendors_requests_reviews';
    protected $fillable = ['id','sent_to_customers','vendor_id','message','email_content','status'];
}
