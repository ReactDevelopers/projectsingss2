<?php 
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DeliveryMethod extends Model {

    protected $fillable = ["name"];  

    public static function getCached()
    {
        $self = new static;

        return \Cache::rememberForever('DeliveryMethod:all', function () use($self) {
            return $self->select('name','id')->orderBy('name')->get()->toArray();
        });
    }
}