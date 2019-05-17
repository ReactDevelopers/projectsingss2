<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletes;

class Client extends Model
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
        'deleted_at'
    ];

    /**
     * Model boot function
     *
     * @return void
     */
    protected static function boot()
    {
        parent::boot();

        static::addGlobalScope('order', function (Builder $builder) {
            $builder->orderBy('name', 'asc');
        });
    }

    /**
     * Get the country associated with the hotel
     *
     * @return Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function country()
    {
        return $this->belongsTo('App\Country');
    }

    /**
     * Get all of the clients's contacts
     *
     * @return Illuminate\Database\Eloquent\Relations\MorphToMany
     */
    public function contacts()
    {
        return $this->morphToMany('App\Contact', 'contactable')
        ->withTimestamps()
        ->withPivot(
            'confirmation_contact_id',
            'invoice_contact_id'
        );
    }

    /**
     * Get the client's confirmations
     *
     * @return Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function confirmations()
    {
        return $this->hasMany('App\Confirmation');
    }

    /**
     * Get the client's invoices
     *
     * @return Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function invoices()
    {
        return $this->hasMany('App\CustomInvoice');
    }

    /**
     * Get the client's on offer confirmations.
     * This generally needs a scope for race_hotel_id when called
     * Example: $client->on_offer_confirmations()->where('race_hotel_id', $meta->id)
     *
     * @return Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function on_offer_confirmations()
    {
        return $this->hasMany('App\Confirmation')
            ->whereNull('signed_on')
            ->where(function ($query) {
                $query->whereDate('expires_on', '>=', date('Y-m-d'))
                    ->orWhereNull('expires_on');
            });
    }

    /**
     * Get the client's signed confirmations.
     * This generally needs a scope for race_hotel_id when called
     * Example: $client->signed_confirmations()->where('race_hotel_id', $meta->id)
     *
     * @return Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function signed_confirmations()
    {
        return $this->hasMany('App\Confirmation')
            ->whereNotNull('signed_on');
    }

    /**
     * Method to get all active clients
     *
     * @return Illuminate\Database\Eloquent\Collection
     */
    public static function getAll()
    {
        return self::orderBy('name', 'ASC')
            ->get();
    }

    /**
     * Method to get all archived clients i.e. the ones which were soft deleted
     *
     * @return Illuminate\Database\Eloquent\Collection
     */
    public static function getArchived()
    {
        return self::onlyTrashed()
            ->orderBy('name', 'ASC')
            ->get();
    }
}
