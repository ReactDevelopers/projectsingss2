<?php namespace Modules\Banner\Entities;

use Dimsav\Translatable\Translatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Audittrail\Traits\AuditTrailModelTrait;

class SubCategory extends Model
{
    // use Translatable;
    use SoftDeletes, AuditTrailModelTrait;
    const DELETED_AT = 'is_deleted';
    
    public $timestamps = false;
    protected $table = 'mm__sub_categories';
    public $translatedAttributes = [];
    protected $fillable = [
    	'name',
		'description',
		'sorts',
		'category_id',
		'status',
		'is_deleted',
    ];

    // public function category(){
    //     return $this->belongsTo('Modules\Category\Entities\Category', 'category_id');
    // }
}
