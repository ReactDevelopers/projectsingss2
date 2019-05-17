<?php

namespace App;

use Fixerio;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Confirmation extends Model
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
        'signed_on',
    ];

    /**
     * Eager load relationships
     * @var array
     */
    protected $with = ['payments'];

    /**
     * Get the currency for this object
     * @return Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function currency()
    {
        return $this->belongsTo('App\Currency');
    }

    /**
     * Get the race hotel combo object for this confirmation
     * @return Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function race_hotel()
    {
        return $this->belongsTo('App\RaceHotel');
    }

     /**
     * Get the confirmation items data for the new confirmation room
     * @return Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function confirmation_items()
    {
        return $this->hasMany('App\ConfirmationItem', 'confirmation_id');
    }

    /**
     * Get the client who owns this confirmation
     * @return Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function client()
    {
        return $this->belongsTo('App\Client');
    }

    /**
     * Get all of the room's confirmation payments
     * @return Illuminate\Database\Eloquent\Relations\MorphToMany
     */
    public function payments()
    {
        return $this->morphToMany('App\Payment', 'payable')->withTimestamps();
    }

    /**
     * Method to find distinct room types and total amount for each room type
     * for the current confirmation
     * @return Illuminate\Database\Eloquent\Collection
     */
    public function room_types()
    {
        $room_types = collect([]);

        // Group all confirmation items by races_hotels_inventory_id
        // as we need a total sum of each room type
        $groups = $this->confirmation_items
            ->groupBy('races_hotels_inventory_id');

        foreach ($groups as $confirmation_items) {
            $amount = 0;

            foreach ($confirmation_items as $confirmation_item) {
                $amount += ($confirmation_item->rate * $confirmation_item->friendly_room_nights);
            }

            $room_types->push(collect([
                'id' => $confirmation_items->first()->races_hotels_inventory_id,
                'amount' => $amount,
                'name' => $confirmation_items->first()->races_hotels_inventory->room_name,
            ]));
        }

        return $room_types;
    }

    /**
     * Get the exchange rate for a confirmation
     * @return float
     */
    public function getExchangeRateAttribute()
    {
        if (empty($this->race_hotel_id) || empty($this->currency_id)) {
            return 1; // FIXME: should probably throw an error as this should be set.
        }

        if (!isset($this->currency->symbol)) {
            $this->load(['currency']);
        }

        if (!isset($this->race_hotel->id)) {
            $this->load(['race_hotel']);
        }

        return Fixerio::convert([
            'from' => $this->race_hotel->currency->name,
            'to' => $this->currency->name,
            'amount' => 1.00, // amount static added
        ])['result'];
    }

    /**
     * Get the room's confirmation friendly sent_on date
     * @return string
     */
    public function getFriendlySentOnAttribute()
    {
        if (empty($this->sent_on)) {
            return '';
        }
        return Carbon::parse($this->sent_on)->format('D, M j, Y');
    }

    /**
     * Get the room's confirmation friendly due on / expires_on date
     * @return string
     */
    public function getFriendlyExpiresOnAttribute()
    {
        if (empty($this->expires_on)) {
            return '';
        }
        return Carbon::parse($this->expires_on)->format('D, M j, Y');
    }

    /**
     * Get the friendly date
     * @return string
     */
    public function getFriendlySignedOnAttribute()
    {
        if (empty($this->signed_on)) {
            return '';
        }
        return Carbon::parse($this->signed_on)->format('D, M j, Y');
    }

    /**
     * Get all of the confirmation uploads
     *
     * @return Illuminate\Database\Eloquent\Relations\MorphToMany
     */
    public function uploads()
    {
        return $this->morphToMany('App\Upload', 'uploadable');
    }

    public function sendToHellosign()
    {
        //
    }

    /**
     * Method to get all outstanding confirmations
     *
     * @return Illuminate\Database\Eloquent\Collection
     */
    public static function getOutStandingConfirmations()
    {
        return self::orderBy('created_at', 'DESC')
            ->with(['race_hotel', 'race_hotel.race', 'confirmation_items', 'client'])
            ->whereDate('expires_on', '>=', date('Y-m-d'))
            ->whereNull('signed_on')
            ->whereHas('race_hotel', function ($query) {
                $query->whereNull('deleted_at');
            })
            ->whereHas('race_hotel.hotel', function ($query) {
                $query->whereNull('deleted_at');
            })
            ->whereHas('race_hotel.race', function ($query) {
                $query->whereNull('deleted_at');
            })
            ->get();
    }

    /**
     * Method to get all confirmations expiring today
     *
     * @return Illuminate\Database\Eloquent\Collection
     */
    public static function getExpiringConfirmations()
    {
        return self::orderBy('created_at', 'DESC')
            ->with(['race_hotel', 'race_hotel.race', 'confirmation_items', 'client'])
            ->whereDate('expires_on', '=', date('Y-m-d'))
            ->whereHas('race_hotel', function ($query) {
                $query->whereNull('deleted_at');
            })
            ->get();
    }
}
