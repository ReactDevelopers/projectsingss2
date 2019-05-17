<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class RaceHotelInventory extends Model
{
    use SoftDeletes;

    /**
     * The table associated with the model
     */
    protected $table = 'races_hotels_inventory';

    /**
     * Guarded attributes for this model
     *
     * @var array
     */
    protected $guarded = [];

    /**
     * Attributes that should be mutated to dates
     * @var array
     */
    protected $dates = [
        'deleted_at'
    ];

    /**
     * Get the race/hotel combo for the inventory row
     */
    public function race_hotel()
    {
        return $this->belongsTo('App\RaceHotel');
    }

    /**
     * Get the confirmation items for the inventory row
     */
    public function confirmation_items()
    {
        return $this->hasMany('App\ConfirmationItem', 'races_hotels_inventory_id');
    }

    /**
     * Get the related rooming lists
     *
     * @return Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function rooming_lists()
    {
        return $this->hasMany('App\RoomingList', 'races_hotels_inventory_id');
    }
}
