<?php namespace Modules\Advertisement\Entities;

use Illuminate\Database\Eloquent\Model;

class AdvertisementTranslation extends Model
{
    public $timestamps = false;
    protected $fillable = [];
    protected $table = 'advertisement__advertisement_translations';
}
