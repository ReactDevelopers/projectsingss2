<?php
use App\RoomingListGuestNight;

return [
    'statuses_abbreviations' => [
        RoomingListGuestNight::STATUS_UNUSED         => 'N',
        RoomingListGuestNight::STATUS_USED           => 'U',
        RoomingListGuestNight::STATUS_RESELL         => 'R',
        RoomingListGuestNight::STATUS_EARLY_CHECKIN  => 'E',
        RoomingListGuestNight::STATUS_LATE_CHECKOUT  => 'L',
        RoomingListGuestNight::STATUS_RESOLD         => 'S',
    ],
];
