<?php 
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Role extends Model {

    protected $fillable = ["name", "title", "description"];

    protected $dates = [];

    public static $rules = [
        // Validation rules
    ];

    public static function getCached()
    {
        $self = new static;

        return \Cache::rememberForever('Role:all', function () use($self) {
            return $self->select('id','name','title')->get()->toArray();
        });
    }
}