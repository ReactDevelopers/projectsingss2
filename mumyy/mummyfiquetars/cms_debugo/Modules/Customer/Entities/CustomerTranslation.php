<?php namespace Modules\Customer\Entities;

use Illuminate\Database\Eloquent\Model;

class CustomerTranslation extends Model
{
    public $timestamps = false;
    protected $fillable = [];
    protected $table = 'customer__customer_translations';
}
