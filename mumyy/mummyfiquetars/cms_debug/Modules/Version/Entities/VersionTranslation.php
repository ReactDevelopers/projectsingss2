<?php namespace Modules\Version\Entities;

use Illuminate\Database\Eloquent\Model;

class VersionTranslation extends Model
{
    public $timestamps = false;
    protected $fillable = [];
    protected $table = 'version__version_translations';
}
