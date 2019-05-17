<?php namespace Modules\PriceRange\Entities;

use Dimsav\Translatable\Translatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Audittrail\Traits\AuditTrailModelTrait;

class PriceRange extends Model
{
    // use Translatable;
	use SoftDeletes, AuditTrailModelTrait;
    const DELETED_AT = 'is_deleted';
    
    public $timestamps = false;
    protected $table = 'mm__price_range';
    public $translatedAttributes = [];
    protected $fillable = [
    	'price_name',
		'description',
		'sorts',
		'status',
		'is_deleted',
    ];
}
