<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Currency extends Model
{
    /**
     * Get the races associated to the currency
     */
    public function races()
    {
        return $this->hasMany('App\Race');
    }
}
