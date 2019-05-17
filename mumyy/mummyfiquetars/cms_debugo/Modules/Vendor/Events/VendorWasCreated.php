<?php namespace Modules\Vendor\Events;

class VendorWasCreated
{
    public $vendor;
    public $users;

    public function __construct($vendor, $users)
    {
        $this->vendor = $vendor;
        $this->users = $users;
    }
}
