<?php
namespace App\Mummy\V1\Definitions\Home;
/**
 * @SWG\Definition(type="object", @SWG\Xml(name="Customer"))
 */
class SearchNearby
{
    /**
     * @SWG\Property(example="27")
     * @var string
     */
    public $lat;
    /**
     * @SWG\Property(example="27")
     * @var string
     */
    public $lng;
}