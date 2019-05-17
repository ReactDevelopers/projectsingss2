<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletes;

class Hotel extends Model
{
    use SoftDeletes;

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
     * Get the races this hotel is associated with
     *
     * @return Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function races()
    {
        return $this->belongsToMany('App\Race', 'races_hotels')
            ->whereNull('races_hotels.deleted_at')
            ->as('meta')
            ->using('App\RaceHotel')
            ->withPivot(
                'id',
                'inventory_currency_id',
                'inventory_min_check_in',
                'inventory_min_check_out',
                'inventory_notes',
                'rooming_list_sent',
                'rooming_list_confirmed'
            );
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
     * Get all of the hotel's contacts
     *
     * @return Illuminate\Database\Eloquent\Relations\MorphToMany
     */
    public function contacts()
    {
        return $this->morphToMany('App\Contact', 'contactable')->withTimestamps();
    }

    /**
     * Method to get all active hotels
     *
     * @return Illuminate\Database\Eloquent\Collection
     */
    public static function getAll()
    {
        return self::orderBy('name', 'ASC')
            ->with('country')
            ->get();
    }

    /**
     * Method to get all archived hotels i.e. the ones which were soft deleted
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
