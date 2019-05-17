<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CustomInvoiceItem extends Model
{
    use SoftDeletes;

    /**
     * Guarded attributes for this model
     *
     * @var array
     */
    protected $guarded = [];

    /**
     * Get the invoices for the invoices items/rows
     *
     *  @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function invoice()
    {
        return $this->belongsTo('App\CustomInvoice', 'custom_invoice_id');
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

        if (!isset($this->invoice->id)) {
            $this->load('invoice');
        }

        return $this->rate * $this->invoice->exchange_rate;
    }

    /**
     * Get the friendly date
     * @return string
     */
    public function getFriendlyDateAttribute()
    {
        if (empty($this->date)) {
            return '';
        }
        return Carbon::parse($this->date)->format('D, M j, Y');
    }

    /**
     * Get the line total (quantity * rate)
     * @return integer
     */
    public function getLineTotalAttribute()
    {
        return $this->rate * $this->quantity;
    }
}
