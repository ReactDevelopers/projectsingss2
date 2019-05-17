<?php namespace Modules\Report\Entities;

use Dimsav\Translatable\Translatable;
use Illuminate\Database\Eloquent\Model;
use Modules\Audittrail\Traits\AuditTrailModelTrait;

class Review extends Model
{
	use AuditTrailModelTrait;
	
    protected $table = 'mm__user_report';
    public $translatedAttributes = [];
    protected $fillable = [];
}
