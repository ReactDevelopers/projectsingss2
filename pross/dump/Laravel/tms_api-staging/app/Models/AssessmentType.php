<?php 
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Lib\BulkDataQuery;

class AssessmentType extends Model {
	use BulkDataQuery;
	
    protected $fillable = ["assessment_type_name"];

    public static function getCached()
    {
        $self = new static;

        return \Cache::rememberForever('AssessmentType:all', function () use($self) {
            return $self->select('id','assessment_type_name')->get()->toArray();
        });
    }
}