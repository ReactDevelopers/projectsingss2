<?php
namespace App\Mummy\V1\Definitions\Vendors;
/**
 * @SWG\Definition(type="object", @SWG\Xml(name="Customer"))
 */
class ViewAction
{
    /**
     * @SWG\Property(example="1")
     * @var string
     */
    public $vendor_id;
    
    /**
     * @SWG\Property(format="int64",example="7")
     * @var string
     */
    public $type;
}