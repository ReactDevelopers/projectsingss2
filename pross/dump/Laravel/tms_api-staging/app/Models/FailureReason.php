<?php 
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FailureReason extends Model {

    protected $fillable = ["failure_reason"];

    public static function getCached()
    {
        $self = new static;

        return \Cache::rememberForever('FailureReason:all', function () use($self) {
            return $self->select('failure_reason','id')->get()->toArray();
        });
    }
}