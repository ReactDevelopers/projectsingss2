<?php

namespace App;

use Fixerio;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Payment extends Model
{
    use SoftDeletes;

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
        'deleted_at',
    ];

    /**
     * Get all of the invoice payments
     *
     * @return Illuminate\Database\Eloquent\Relations\MorphToMany
     */
    // COMMENTED BY NATE ON 2/19/19. WILL BE REMOVED BY 3/4/2019.
    // 1. I don't think this model needs to be self-referrential.
    // 2. There is no table or field called "paymentable".
    // Therefore, I theorize this method is never actually run.
    // If that's incorrect, please talk to Nate. If no use case
    // is found by 3/4/2019, this method will be removed permanently.
    //
    // TODO: Remove this on/after 3/4/2019 if no use case is found for this method.
    //
    // public function payments()
    // {
    //     return $this->morphToMany('App\Payment', 'paymentable');
    // }

    /**
     * Get all the owning payable models.
     */
    public function payable()
    {
        return $this->morphTo();
    }

    /**
     * Get friendly date
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
     * Get friendly date
     * @return string
     */
    public function getFriendlyPaidOnAttribute()
    {
        if (empty($this->paid_on)) {
            return '';
        }
        return Carbon::parse($this->paid_on)->format('D, M j, Y');
    }

    /**
     * Get friendly date for to_accounts_on field
     *
     * @return string
     */
    public function getFriendlyToAccountsOnAttribute()
    {
        if (empty($this->to_accounts_on)) {
            return '';
        }
        return Carbon::parse($this->to_accounts_on)->format('D, M j, Y');
    }

    /**
     * Get friendly date for invoice_date field
     *
     * @return string
     */
    public function getFriendlyInvoiceDateAttribute()
    {
        if (empty($this->invoice_date)) {
            return '';
        }
        return Carbon::parse($this->invoice_date)->format('D, M j, Y');
    }

}
