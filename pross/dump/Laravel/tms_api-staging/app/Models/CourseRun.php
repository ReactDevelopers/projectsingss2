<?php 
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Lib\BulkDataQuery;
use \Illuminate\Database\Eloquent\SoftDeletes;

class CourseRun extends Model {
    
    use BulkDataQuery, SoftDeletes;   
	protected $appends = ['delivery_method'];

    protected $fillable = [
        'current_status','should_check_deconflict','id','course_code','start_date','end_date','assessment_start_date','assessment_end_date'
    ];

    public function creator() {
    	
    	return $this->belongsTo(\App\User::class, 'creator_id','id')->withTrashed();
    }

    public function deliveryMethodData() {

        return $this->belongsTo(DeliveryMethod::class,'delivery_method_id');
    }

    public function getDeliveryMethodAttribute() {

        $delivery_method = $this->getAttribute('deliveryMethodData');
        return $delivery_method ? $delivery_method->name : null;
    }
}