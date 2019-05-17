<?php namespace Modules\Vendor\Entities;

use Dimsav\Translatable\Translatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class VendorLocation extends Model
{
    // use Translatable;
	use SoftDeletes;
    const DELETED_AT = 'is_deleted';
    
    protected $table = 'mm__vendors_location';
    public $translatedAttributes = [];
    protected $fillable = [
    	'user_id',
		'country_id',
		'states_id',
		'city_id',
		'city_name',
		'is_primary',
		'status',
		'is_deleted',
		'created_at',
		'updated_at',
		'lat',
		'lng',
        'zip_code',
    ];

    public function country()
    {
        return $this->belongsTo('Modules\Vendor\Entities\Country', 'country_id');
    }
   	public function city()
    {
        return $this->belongsTo('Modules\Vendor\Entities\City', 'city_id');
    }

    public function getCountryNameAttribute(){
        $country = $this->country()->first();
        if ($country !== null) {
            return $country->name;
        }
        return null;
    }

    public function getCountryCodeAttribute(){
        $country = $this->country()->first();
        if ($country !== null) {
            return $country->phonecode;
        }
        return null;
    }

    public function getCityNameAttribute(){
        $city = $this->city()->first();
        if ($city !== null) {
            return $city->name;
        }
        return null;
    }

}
