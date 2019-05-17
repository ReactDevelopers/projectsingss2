<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class Country extends Model
{

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
     * Get the hotels associated with this country
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function hotels()
    {
        return $this->hasMany('App\Hotel');
    }
}
