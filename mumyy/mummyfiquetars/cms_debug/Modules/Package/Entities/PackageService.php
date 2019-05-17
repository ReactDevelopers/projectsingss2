<?php namespace Modules\Package\Entities;

use Dimsav\Translatable\Translatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PackageService extends Model
{
    // use Translatable;
    use SoftDeletes;
    const DELETED_AT = 'is_deleted';

    public $timestamps = false;
    protected $table = 'mm__package_services';
    public $translatedAttributes = [];
    protected $fillable = [
    	'name',
		'description',
		'status',
		'is_deleted',
    ];
}
