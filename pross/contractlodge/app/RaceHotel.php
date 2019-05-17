<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\Pivot;

class RaceHotel extends Pivot
{
    use SoftDeletes;
    /**
     * The table associated with the model
     */
    protected $table = 'races_hotels';

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
        'inventory_min_check_in',
        'inventory_min_check_out'
    ];

    /**
     * Get the race for the race/hotel combo
     */
    public function race()
    {
        return $this->belongsTo('App\Race');
    }

    /**
     * Get the hotel for the race/hotel combo
     */
    public function hotel()
    {
        return $this->belongsTo('App\Hotel');
    }

    /**
     * Get the room type and inventory data for the race/hotel combo
     */
    public function room_type_inventories()
    {
        return $this->hasMany('App\RaceHotelInventory', 'race_hotel_id');
    }

    /**
     * Get the currency for the race/hotel combo
     */
    public function currency()
    {
        return $this->belongsTo('App\Currency', 'inventory_currency_id');
    }

    /**
     * Get the confirmations related to the race/hotel combo
     */
    public function confirmations()
    {
        return $this->hasMany('App\Confirmation', 'race_hotel_id');
    }

    /**
     * Get the confirmations related to the race/hotel combo
     */
    public function on_offer_confirmations()
    {
        return $this->hasMany('App\Confirmation', 'race_hotel_id')
            ->whereNull('signed_on')
            ->where(function ($query) {
                $query->whereDate('expires_on', '>=', date('Y-m-d'))
                    ->orWhereNull('expires_on');
            });
    }

    /**
     * Get the confirmations related to the race/hotel combo
     */
    public function signed_confirmations()
    {
        return $this->hasMany('App\Confirmation', 'race_hotel_id')
            ->whereNotNull('signed_on');
    }

    /**
     * Get the friendly inventory_min_check_in date
     * @return string
     */
    public function getFriendlyMinCheckInAttribute()
    {
        if (empty($this->inventory_min_check_in)) {
            return '';
        }
        return Carbon::parse($this->inventory_min_check_in)->format('D, M j, Y');
    }

    /**
     * Get the race's friendly inventory_min_check_out date
     * @return string
     */
    public function getFriendlyMinCheckOutAttribute()
    {
        if (empty($this->inventory_min_check_out)) {
            return '';
        }
        return Carbon::parse($this->inventory_min_check_out)->format('D, M j, Y');
    }

    /**
     * Get the race's min nights as an integer
     * @return int
     */
    public function getNumMinNightsAttribute()
    {
        if (empty($this->inventory_min_check_in) || empty($this->inventory_min_check_out)) {
            return '';
        }
        return $this->inventory_min_check_in->diffInDays($this->inventory_min_check_out);
    }

    /**
     * Get the custom invoices related to the race/hotel combo
     */
    public function custom_invoices()
    {
        return $this->hasMany('App\CustomInvoice', 'race_hotel_id');
    }

     /**
     * Get the confirmations related to the race/hotel combo order by client id
     */
    public function signed_confirmations_order_by_client_id()
    {
        return $this->hasMany('App\Confirmation', 'race_hotel_id')
            ->whereNotNull('signed_on')
            ->orderBy('client_id');
    }

    /**
     * Get the rooming list friendly rooming_list_sent date
     * @return string
     */
    public function getFriendlyRoomingListSentAttribute()
    {
        if (empty($this->rooming_list_sent)) {
            return '';
        }
        return Carbon::parse($this->rooming_list_sent)->format('D, M j, Y');
    }

    /**
     * Get the rooming list friendly rooming_list_confirmed date
     * @return string
     */
    public function getFriendlyRoomingListConfirmedAttribute()
    {
        if (empty($this->rooming_list_confirmed)) {
            return '';
        }
        return Carbon::parse($this->rooming_list_confirmed)->format('D, M j, Y');
    }

    /**
     * Get all of the custom inventory uploads
     *
     * @return Illuminate\Database\Eloquent\Relations\MorphToMany
     */
    public function uploads()
    {
        return $this->morphToMany('App\Upload', 'uploadable');
    }
}
