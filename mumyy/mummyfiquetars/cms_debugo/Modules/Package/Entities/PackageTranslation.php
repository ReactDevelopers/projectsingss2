<?php namespace Modules\Package\Entities;

use Illuminate\Database\Eloquent\Model;

class PackageTranslation extends Model
{
    public $timestamps = false;
    protected $fillable = [];
    protected $table = 'package__package_translations';
}
