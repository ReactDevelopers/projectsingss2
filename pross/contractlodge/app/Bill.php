<?php

namespace App;

use Fixerio;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Bill extends Model
{
    use SoftDeletes;

    /**
     * Guarded attributes for this model
     *
     * @var array
     */
    protected $guarded = [];

    /**
     * Get all of the bills payments
     * @return Illuminate\Database\Eloquent\Relations\MorphToMany
     */
    public function payments()
    {
        return $this->morphToMany('App\Payment', 'payable')->withTimestamps();
    }

    /**
     * Method to get all bills
     *
     * @return Illuminate\Database\Eloquent\Collection
     */
    public static function getAll()
    {
        return self::orderBy('created_at', 'DESC')
            ->with(['payments', 'currency', 'race_hotel', 'race_hotel.race', 'race_hotel.hotel'])
            ->get();
    }

    /**
     * Method to get all bills
     *
     * @return Illuminate\Database\Eloquent\Collection
     */
    public static function getAllUnpaid()
    {
        return self::orderBy('created_at', 'DESC')
            ->with(['payments', 'currency', 'race_hotel', 'race_hotel.race', 'race_hotel.hotel'])
            ->whereNull('deleted_at')
            ->whereHas('payments', function ($query) {
                $query->whereNull('deleted_at')
                    ->where('amount_due', '>', 0)
                    ->where(function ($query) {
                        $query->whereColumn('amount_paid', '<', 'amount_due')
                            ->orWhereNull('paid_on');
                    });
            })
            ->whereHas('race_hotel', function ($query) {
                $query->whereNull('deleted_at');
            })
            ->whereHas('race_hotel.race', function ($query) {
                $query->whereNull('deleted_at');
            })
            ->whereHas('race_hotel.hotel', function ($query) {
                $query->whereNull('deleted_at');
            })
            ->get();
    }

    /**
     * Get the exchange rate for a bill
     * @return float
     */
    public function getExchangeRateAttribute()
    {
        if (empty($this->race_hotel_id)) {
            return 1;
        }

        $this->load(['currency', 'currency_exchange']);

        return Fixerio::convert([
            'from' => $this->currency->name,
            'to' => $this->currency_exchange->name,
            'amount' => 1.00, // amount static added
        ])['result'];
    }

    /**
     * Get the bill friendly contract_signed_on date
     * @return string
     */
    public function getFriendlyContractSignedOnAttribute()
    {
        if (empty($this->contract_signed_on)) {
            return '';
        }
        return Carbon::parse($this->contract_signed_on)->format('D, M j, Y');
    }

    /**
     * Get the currency for the bill
     */
    public function currency()
    {
        return $this->belongsTo('App\Currency', 'currency_id');
    }

    /**
     * Get the currency for the bill exchange_to field
     */
    public function currency_exchange()
    {
        return $this->belongsTo('App\Currency', 'exchange_currency_id');
    }

    /**
     * Get the race_hotel the bill belongs to
     */
    public function race_hotel()
    {
        return $this->belongsTo('App\RaceHotel', 'race_hotel_id');
    }
}
