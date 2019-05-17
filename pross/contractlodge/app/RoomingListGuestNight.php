<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class RoomingListGuestNight extends Model
{
    /**
     * Constants for various statuses for the room night
     */
    const STATUS_UNUSED = 0;
    const STATUS_USED = 1;
    const STATUS_RESELL = 2;
    const STATUS_EARLY_CHECKIN = 3;
    const STATUS_LATE_CHECKOUT = 4;
    const STATUS_RESOLD = 5;

    /**
     * Attributes that should be mutated to dates
     *
     * @var array
     */
    public $dates = [
        'status_updated_at',
    ];
}
