<?php namespace Modules\Credit\Entities;

use Dimsav\Translatable\Translatable;
use Illuminate\Database\Eloquent\Model;
use Modules\Audittrail\Traits\AuditTrailModelTrait;

class Credit extends Model
{
    use AuditTrailModelTrait;

    protected $table = 'mm__vendors_credit';
    public $translatedAttributes = [];
    protected $fillable = ['id', 'vendor_id', 'amount', 'point', 'created_at', 'updated_at'];

    public function getPoint(){
    	return $this->point;
    }

    public function getSumPoint(){
    	return $this->vendor->sum('point');
    }

    public function vendor(){
    	return $this->belongsTo('Modules\Vendor\Entities\Vendor', 'vendor_id');
    }
}
