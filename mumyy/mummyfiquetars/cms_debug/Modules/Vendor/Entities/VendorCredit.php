<?php namespace Modules\Vendor\Entities;

use Dimsav\Translatable\Translatable;
use Illuminate\Database\Eloquent\Model;

class VendorCredit extends Model
{
    // use Translatable;
    protected $table = 'mm__vendors_credit';
    public $translatedAttributes = [];
    protected $fillable = [
    	'vendor_id',
        'amount',
        'point',
        'created_at',
        'updated_at',
    ];

}
