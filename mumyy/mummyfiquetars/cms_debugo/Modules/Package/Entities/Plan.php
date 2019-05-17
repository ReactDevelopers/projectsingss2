<?php namespace Modules\Package\Entities;

use Dimsav\Translatable\Translatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Audittrail\Traits\AuditTrailModelTrait;

class Plan extends Model
{
    // use Translatable;
    use AuditTrailModelTrait;
    
    public $timestamps = true;
    protected $table = 'mm__plans';
    public $translatedAttributes = [];
    protected $fillable = [
		'name',
        'description',
        'price',
        'interval',
        'interval_count',
        'trial_period_days',
        'sort_order',
        'created_at',
        'updated_at',
    ];
}
