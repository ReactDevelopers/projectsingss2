<?php 
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TrainingLocation extends Model {

    protected $fillable = ["location"];  

    public static function getCached()
    {
        $self = new static;

        return \Cache::rememberForever('TrainingLocation:all', function () use($self) {
            return $self->select('location','id')->get()->toArray();
        });
    }
}