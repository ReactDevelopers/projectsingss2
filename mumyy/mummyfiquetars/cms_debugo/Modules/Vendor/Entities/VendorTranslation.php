<?php namespace Modules\Vendor\Entities;

use Illuminate\Database\Eloquent\Model;

class VendorTranslation extends Model
{
    public $timestamps = false;
    protected $fillable = [];
    protected $table = 'vendor__vendor_translations';
}
