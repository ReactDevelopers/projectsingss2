<?php namespace Modules\Version\Entities;

use Dimsav\Translatable\Translatable;
use Illuminate\Database\Eloquent\Model;
use Modules\Audittrail\Traits\AuditTrailModelTrait;

class Version extends Model
{
    use AuditTrailModelTrait;

    protected $table = 'version__versions';
    public $translatedAttributes = [];
    protected $fillable = [];
}
