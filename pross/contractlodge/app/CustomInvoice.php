<?php

namespace App;

use Fixerio;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CustomInvoice extends Model
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
        'deleted_at'
    ];

    /**
     * Get all of the invoice payments
     * @return Illuminate\Database\Eloquent\Relations\MorphToMany
     */
    public function payments()
    {
        return $this->morphToMany('App\Payment', 'payable');
    }

    /**
     * Get the currency for this object
     * @return Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function currency()
    {
        return $this->belongsTo('App\Currency');
    }

    /**
     * Get the race hotel combo object for this custom invoice
     * @return Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function race_hotel()
    {
        return $this->belongsTo('App\RaceHotel');
    }

     /**
     * Get the invoice items data for the invoice
     *  @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function invoice_items()
    {
        return $this->hasMany('App\CustomInvoiceItem', 'custom_invoice_id');
    }

    /**
     * Get the client who owns this invoice
     *  @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function client()
    {
        return $this->belongsTo('App\Client');
    }

    /**
     * Get the exchange rate for a confirmation
     * @return float
     */
    public function getExchangeRateAttribute()
    {
        if (empty($this->race_hotel_id) || empty($this->currency_id)) {

            // FIXME: should probably throw an error as this should be set.
            //
            // However, it's quite possible an invoice can be created without
            // being attached to a race_hotel object. So, in this case,
            // we need a way to include a local rate that we can
            // convert from.
            return 1;
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
     * Get the invoice friendly due on date
     * @return string
     */
    public function getFriendlyDueOnAttribute()
    {
        if (empty($this->due_on)) {
            return '';
        }
        return Carbon::parse($this->due_on)->format('D, M j, Y');
    }

    /**
     * Get the total for all the invoice item line amounts
     * @return integer
     */
    public function getAmountAttribute()
    {
        $amount = 0;
        if (empty($this->invoice_items)) {
            return $amount;
        }

        foreach($this->invoice_items as $item) {
            $amount += $item->line_total;
        }

        return $amount;
    }

    /**
     * Get all of the custom invoice uploads
     *
     * @return Illuminate\Database\Eloquent\Relations\MorphToMany
     */
    public function uploads()
    {
        return $this->morphToMany('App\Upload', 'uploadable');
    }

}
