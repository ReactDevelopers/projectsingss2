<?php namespace Modules\Package\Entities;

use Dimsav\Translatable\Translatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Audittrail\Traits\AuditTrailModelTrait;

class Package extends Model
{
    // use Translatable;
    use SoftDeletes, AuditTrailModelTrait;
    const DELETED_AT = 'is_deleted';
    
    public $timestamps = false;
    protected $table = 'mm__packages';
    public $translatedAttributes = [];
    protected $fillable = [
		'name',
		'price',
		'type',
		'services',
		'country_id',
		'status',
		'is_deleted',
    ];
}
