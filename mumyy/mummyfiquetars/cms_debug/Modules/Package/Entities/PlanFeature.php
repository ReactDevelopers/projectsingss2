<?php namespace Modules\Package\Entities;

use Dimsav\Translatable\Translatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PlanFeature extends Model
{
    // use Translatable;
    
    public $timestamps = true;
    protected $table = 'mm__plan_features';
    public $translatedAttributes = [];
    protected $fillable = [
		'plan_id',
        'code',
        'value',
        'sort_order',
        'created_at',
        'updated_at',
    ];
}
