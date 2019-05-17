<?php 
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Lib\BulkDataQuery;

class Department extends Model {
    
    use BulkDataQuery;

    protected $fillable = ["dept_code", "dept_name"];
    public $timestamps = false;

    public static function getCached()
    {
        $self = new static;

        return \Cache::rememberForever('Department:course', function () use($self) {
            return $self->select('dept_code','dept_name','id')->where('is_user_dept','No')->orderBy('dept_code')->get()->toArray();
        });
    }

    public static function getStafDeptCached()
    {
        $self = new static;

        return \Cache::rememberForever('Department:staff', function () use($self) {
            return $self->select('dept_code','dept_name','id')->where('is_user_dept','Yes')->orderBy('dept_code')->get()->toArray();
        });
    }
}