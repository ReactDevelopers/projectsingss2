<?php 
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AbsentReason extends Model {

    protected $fillable = ["absent_reason"];

    public static function getCached()
    {
        $self = new static;

        return \Cache::rememberForever('AbsentReason:all', function () use($self) {
            return $self->select('absent_reason','id')->get()->toArray();
        });
    }
}