<?php namespace Modules\Version\ValueObjects;

use Doctrine\ORM\Mapping as ORM;

/** @ORM\Embeddable */
class Version
{
    public function __construct()
    {
        // Implement stub method
    }

    public function __toString()
    {
        return ''; // Implement stub method
    }
}
