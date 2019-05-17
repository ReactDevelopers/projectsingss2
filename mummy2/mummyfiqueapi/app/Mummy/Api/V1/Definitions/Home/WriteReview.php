<?php
namespace App\Mummy\V1\Definitions\Home;
/**
 * @SWG\Definition(type="object", @SWG\Xml(name="Customer"))
 */
class WriteReview
{
    /**
     * @SWG\Property(example="27")
     * @var string
     */
    public $vendor_id;
}