<?php
namespace App\Mummy\V1\Definitions\Vendors;
/**
 * @SWG\Definition(type="object", @SWG\Xml(name="Customer"))
 */
class SaveVendor
{
    /**
     * @SWG\Property(example="27")
     * @var string
     */
    public $vendor_id;
}