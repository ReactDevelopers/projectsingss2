<?php

namespace App;

use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ConfirmationItem extends Model
{
    use SoftDeletes;

    /**
     * Guarded attributes for this model
     * @var array
     */
    protected $guarded = [];

    /**
     * Attributes that should be mutated to dates
     * @var array
     */
    protected $dates = [
        'deleted_at',
        'check_in',
        'check_out'
    ];

    /**
     * Get the confirmation for the confirmation items/rows
     * @return Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function confirmation()
    {
        return $this->belongsTo('App\Confirmation');
    }

    /**
     * Get the confirmation room for the confirmation items/rows
     * @return Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function races_hotels_inventory()
    {
        return $this->belongsTo('App\RaceHotelInventory');
    }

    /**
     * Get the race's friendly check_in date
     * @return string
     */
    public function getFriendlyCheckInAttribute()
    {
        if (empty($this->check_in)) {
            return '';
        }
        return Carbon::parse($this->check_in)->format('M j, Y');
    }

    /**
     * Get the race's friendly check_out date
     * @return string
     */
    public function getFriendlyCheckOutAttribute()
    {
        if (empty($this->check_out)) {
            return '';
        }
        return Carbon::parse($this->check_out)->format('M j, Y');
    }

    /**
     * Get the amount for the current item
     * Amount = Quantity * Rate
     * @return decimal
     */
    public function getAmountAttribute()
    {
        return $this->quantity * $this->rate;
    }

    /**
     * Get the rate in the exchanged currency
     * @return decimal
     */
    public function getRateExchangedAttribute()
    {
        if (empty($this->rate)) {
            return 0;
        }

        if (!isset($this->confirmation->id)) {
            $this->load(['confirmation']);
        }

        return $this->rate * $this->confirmation->exchange_rate;
    }

    /**
     * Get the room's confirmation item's friendly date diff for check_in and check_out
     * @return string
     */
    public function getFriendlyDiffAttribute()
    {
        $day_check_in = Carbon::parse($this->check_in)->format('D');
        $day_check_out = Carbon::parse($this->check_out)->format('D');
        $date = Carbon::parse($this->check_in);

        return $diff = $day_check_in." - ".$day_check_out." "." (".$date->diffInDays($this->check_out).")";
    }

    /**
     * Get the room's confirmation item's friendly room nights
     * @return string
     */
    public function getFriendlyRoomNightsAttribute()
    {
        $date = Carbon::parse($this->check_in);
        $diff = $date->diffInDays($this->check_out);

        return $this->quantity * $diff;
    }

    /**
     * Return whether the check_in/out range is fully within the contract's specified
     * date range to be considered a "min" room night (inclusive).
     * @return boolean
     */
    public function getIsWithinMinNightRangeAttribute()
    {
        if (isset($this->races_hotels_inventory)) {
            $race_hotel = $this->races_hotels_inventory->race_hotel;
        }

        if (isset($race_hotel)) {

            $first = Carbon::parse($race_hotel->inventory_min_check_in);
            $second = Carbon::parse($race_hotel->inventory_min_check_out);

            // "within" inclusive of min/max.
            $is_within_min = Carbon::parse($this->check_in)->between($first, $second);
            $is_within_max = Carbon::parse($this->check_out)->between($first, $second);

            return ($is_within_min && $is_within_max);
        }
    }
}
