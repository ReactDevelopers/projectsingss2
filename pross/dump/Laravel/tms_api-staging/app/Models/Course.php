<?php 
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Lib\BulkDataQuery;
use \Illuminate\Database\Eloquent\SoftDeletes;

class Course extends Model {
    use BulkDataQuery, SoftDeletes;
    
    protected $appends = ['delivery_method'];

    protected $fillable = [
        'course_code',
        'title',
        'duration_in_days',
        'programme_category_id',
        'programme_type_id',
        'department_id',
        'assessment_type_id',
        'mandatory',
        'training_location_id',
        //'delivery_method',
        'course_provider',
        'cost_per_pax',
        'subsidy',
        'subsidy_value',
        'vendor_email'
    ];
    
    public function deliveryMethodData() {

        return $this->belongsTo(DeliveryMethod::class,'delivery_method_id');
    }

    public function getDeliveryMethodAttribute() {

        $delivery_method = $this->getAttribute('deliveryMethodData');
        return $delivery_method ? $delivery_method->name : null;
    }
}