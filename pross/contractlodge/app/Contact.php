<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Contact extends Model
{
    /**
     * Guarded attributes of the model
     *
     * @var array
     */
    protected $guarded = [];

    /**
     * Get all of the clients's contacts
     *
     * @return Illuminate\Database\Eloquent\Relations\MorphToMany
     */
    public function contacts()
    {
        return $this->morphToMany('App\Contact', 'contactable');
    }
}
