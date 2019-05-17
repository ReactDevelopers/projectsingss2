<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletes;

class Race extends Model
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
        'start_on',
        'end_on',
        'deleted_at'
    ];

    /**
     * Attribute that appends required extra attributes to race.
     * @var array
     */
    protected $appends = [
        'full_name',
        'friendly_start_on',
        'friendly_end_on'
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
            $builder->orderBy('year', 'desc')
                ->orderBy('name', 'asc');
        });
    }

    /**
     * Get the currency associated to the race
     */
    public function currency()
    {
        return $this->belongsTo('App\Currency');
    }

    /**
     * Get all the hotels associated to the race
     */
    public function hotels()
    {
        return $this->belongsToMany('App\Hotel', 'races_hotels')
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
            )->whereNull('races_hotels.deleted_at');
    }

    /**
     * Make the start_on a simple date format
     * @param  [type] $value [description]
     * @return [type]        [description]
     */
    public function getStartOnAttribute($value)
    {
        return $value;
    }

    /**
     * Make the end_on a simple date format
     * @param  [type] $value [description]
     * @return [type]        [description]
     */
    public function getEndOnAttribute($value)
    {
        return $value;
    }

    /**
     * Get the race's full name, year included.
     * @return string
     */
    public function getFullNameAttribute()
    {
        return "{$this->year} {$this->name}";
    }

    /**
     * Get the race's friendly start_on date
     * @return string
     */
    public function getFriendlyStartOnAttribute()
    {
        if (empty($this->start_on)) {
            return '';
        }
        return Carbon::parse($this->start_on)->format('D, M j, Y');
    }

    /**
     * Get the race's friendly end_on date
     * @return string
     */
    public function getFriendlyEndOnAttribute()
    {
        if (empty($this->end_on)) {
            return '';
        }
        return Carbon::parse($this->end_on)->format('D, M j, Y');
    }

    /**
     * Method to get all active races
     *
     * @return Collection
     */
    public static function getAll()
    {
        return self::orderBy('start_on', 'DESC')
            ->with('currency')
            ->get();
    }

    /**
     * Method to get all archived races i.e. the ones which were soft deleted
     *
     * @return Collection
     */
    public static function getArchived()
    {
        return self::onlyTrashed()
            ->orderBy('start_on', 'DESC')
            ->get();
    }

     /**
     * Get the room type and inventory data for the race
     */
    public function room_type_inventories()
    {
        return $this->hasManyThrough('App\RaceHotelInventory', 'App\RaceHotel', 'race_id' , 'race_hotel_id');
    }

    /**
     * Function to get total inventory stats.
     *
     * @param Object $race Race object
     *
     * @return Array $stats Total stats array
     */
    public function get_total_inventory_stats(Race $race)
    {
        $stats = [
            'sum_row_total_min_stays_contracted' => 0,
            'sum_row_total_min_stays_sold'       => 0,
            'sum_available_total'                => 0,
            'max_room_types'                     => 0,
            'inventories'                        => [],
        ];

        foreach($race->hotels as $hotel) {
            $meta = 'App\RaceHotel'::where('race_id', $race->id)
                ->where('hotel_id', $hotel->id)
                ->first()
                ->load('currency');

            if (isset($meta->id)) {
                $inventories = $meta->load([
                    'room_type_inventories.confirmation_items',
                    'room_type_inventories',
                    'room_type_inventories.race_hotel.on_offer_confirmations',
                    'room_type_inventories.race_hotel.on_offer_confirmations.confirmation_items',
                    'room_type_inventories.race_hotel.signed_confirmations',
                    'room_type_inventories.race_hotel.signed_confirmations.confirmation_items',
                    ])->room_type_inventories;
            } else {
                $inventories = [];
            }

            set_inventory_stats($inventories);

            if (isset($inventories) && count($inventories) > 0 ) {
                foreach ($inventories as $inventory) {
                    $stats['sum_row_total_min_stays_contracted'] += $inventory->min_stays_contracted;
                    $stats['sum_row_total_min_stays_sold']       += $inventory->min_stays_sold;
                    $stats['sum_available_total']                += ($inventory->min_stays_contracted - $inventory->min_stays_sold);
                    $stats['max_room_types']                     = ($inventories->count() > $stats['max_room_types']) ? $inventories->count() : $stats['max_room_types'];
                }
            }

            if (isset($meta->id)) {
                $stats['inventories'][$meta->id] = $inventories;
            }
        }

        return $stats;
    }

    /**
     * Returns the number of "room nights" (quantity x num nights).
     *
     * @param  Collection $confirmations
     * @param  string     $night_type    "min", "pre_post", or null
     * @param  integer    $room_type_id  or null
     * @return integer
     */
    private function get_room_nights(Collection $confirmations, $night_type = null, $room_type_id = null)
    {
        return $this->get_total($confirmations, false, $night_type, $room_type_id);
    }

    /**
     * Returns the number of "stays" (number of rooms, irrespective of the date range).
     * This only applies to min_nights, not pre_post_nights
     *
     * @param  Collection $confirmations
     * @param  integer    $room_type_id  or null
     * @return integer
     */
    private function get_stays(Collection $confirmations, $room_type_id = null)
    {
        return $this->get_total($confirmations, true, 'min', $room_type_id);
    }

    /**
     * Gets the number of room nights (or stays) from a collection of confirmations. i.e.,
     * you can send in a collection of signed, unsigned, or on offer confirmations
     * and the response will be the product of # rooms x # nights in stay.
     *
     * @param  Collection $confirmations
     * @param  boolean    $count_as_stay If true, returns quantity rooms as a "stay". If false, returns total room nights.
     * @param  string     $night_type    "min", "pre_post", or null
     * @return integer
     */
    private function get_total(Collection $confirmations, $count_as_stay = false, $night_type = null, $room_type_id = null)
    {
        if (empty($confirmations)) {
            return 0;
        }
        $count = 0;
        // FIXME: I'm not liking nested foreach loops again. Ugh.
        foreach ($confirmations as $confirmation) {
            if (empty($confirmation->confirmation_items)) {
                continue;
            }
            foreach ($confirmation->confirmation_items as $item) {
                if ($item->races_hotels_inventory_id == $room_type_id || $room_type_id == null) {
                    if (($night_type == 'min' && $item->is_within_min_night_range)
                        || ($night_type == 'pre_post' && ! $item->is_within_min_night_range)
                        || (empty($night_type))) {
                        if ($count_as_stay) {
                            $count += $item->quantity;
                        } else {
                            $count += $item->friendly_room_nights;
                        }
                    }
                }
            }
        }
        return $count;
    }

}
