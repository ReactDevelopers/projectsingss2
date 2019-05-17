<?php 
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProgrammeCategory extends Model {

    protected $fillable = ["prog_category_code","prog_category_name"];  

    public static function getCached()
    {
        $self = new static;

        return \Cache::rememberForever('ProgrammeCategory:all', function () use($self) {
            return $self->select('prog_category_code','prog_category_name','id')->get()->toArray();
        });
    }
}