<?php namespace Modules\Package\Entities;

use Illuminate\Database\Eloquent\Model;

class PackageServiceTranslation extends Model
{
    public $timestamps = false;
    protected $fillable = [];
    protected $table = 'package__packageservice_translations';
}
