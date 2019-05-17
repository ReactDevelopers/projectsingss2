<?php namespace Modules\Portfolio\Entities;

use Dimsav\Translatable\Translatable;
use Illuminate\Database\Eloquent\Model;
use Modules\Media\Support\Traits\MediaRelation;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Audittrail\Traits\AuditTrailModelTrait;

class Portfolio extends Model
{
    use MediaRelation;
    use SoftDeletes, AuditTrailModelTrait;
    const DELETED_AT = 'is_deleted';
    protected $table = 'mm__vendors_portfolios';
    public $translatedAttributes = [];
    public $timestamps = true;
    
    protected $fillable = ['id','category_id','sub_category_id','city','title','description','tags','status','vendor_id','photography','created_at','updated_at'];
    public function detachFiles($ids = [])
    {
    	return $this->files()->detach($ids);
    }

    public function vendor(){
        return $this->belongsTo('Modules\Vendor\Entities\Vendor', 'vendor_id');
    }

    public function category(){
        return $this->belongsTo('Modules\Category\Entities\Category', 'category_id');
    }

    public function subCategory(){
        return $this->belongsTo('Modules\Category\Entities\SubCategory', 'sub_category_id');
    }
}
