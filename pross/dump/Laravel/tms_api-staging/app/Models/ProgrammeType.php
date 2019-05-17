<?php 
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Lib\BulkDataQuery;

class ProgrammeType extends Model {
	
	use BulkDataQuery;

    protected $fillable = ["prog_type_code","prog_type_name"];

    public static function getCached()
    {
        $self = new static;

        return \Cache::rememberForever('ProgrammeType:all', function () use($self) {
            return $self->select('id','prog_type_code','prog_type_name')->orderBy('prog_type_code')->get()->toArray();
        });
    }
}