<?php

namespace App\Mummy\Api\V1\Entities\Vendors;

use Illuminate\Database\Eloquent\Model;

class VendorLocation extends Model
{
     protected $table = 'mm__vendors_location';
    protected $fillable = ['id','user_id','country_id','states_id','city_id','city_name','created_at','updated_at'];

    /**
     * Get the active record associated with the user.
     */
    public function country()
    {
        return $this->belongsTo('App\Mummy\Api\V1\Entities\Home\Country');
    }

    /**
     * Get the active record associated with the user.
     */
    public function city()
    {
        return $this->belongsTo('App\Mummy\Api\V1\Entities\Home\City');
    }

}

