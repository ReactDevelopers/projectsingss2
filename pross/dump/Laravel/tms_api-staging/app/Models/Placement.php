<?php 
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Lib\BulkDataQuery;
use \Illuminate\Database\Eloquent\SoftDeletes;

class Placement extends Model {
    
    use BulkDataQuery, SoftDeletes;
       
    protected $fillable = [
        'id', 
        'course_run_id', 
        'personnel_number', 
        'result_uploaded', 
        'attendance', 
        'assessment_results', 
        'absent_reason_id', 
        'failure_reason_id', 
        'creator_id', 
        'updater_id', 
        'current_status', 
        'is_email_send', 
    ];

    public function participants() {

        return $this->belongsTo(\App\User::class, 'personnel_number','personnel_number');
    }

    public function subordinates() {

        return $this->belongsTo(\App\User::class, 'subordinate_per_id','personnel_number');
    }

    public function courseRun() {

        return $this->belongsTo(\App\Models\CourseRun::class, 'course_run_id','id');
    }

    public function conflictInCourseRun() {

        return $this->belongsTo(\App\Models\CourseRun::class, 'conflict_in_course_run_id','id');
    }
}