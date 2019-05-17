<?php namespace Modules\User\Entities;

use Dimsav\Translatable\Translatable;
use Illuminate\Database\Eloquent\Model;
use Modules\Audittrail\Traits\AuditTrailModelTrait;

class Role extends Model
{
    // use Translatable;
    use AuditTrailModelTrait;

    protected $table = 'roles';
    public $translatedAttributes = [];
    protected $fillable = [
		'slug',
        'name',
        'permissions',
        'created_at',
        'updated_at',
    ];
}