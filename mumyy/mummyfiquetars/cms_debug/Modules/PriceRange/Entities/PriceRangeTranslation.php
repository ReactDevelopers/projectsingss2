<?php namespace Modules\PriceRange\Entities;

use Illuminate\Database\Eloquent\Model;

class PriceRangeTranslation extends Model
{
    public $timestamps = false;
    protected $fillable = [];
    protected $table = 'pricerange__pricerange_translations';
}
