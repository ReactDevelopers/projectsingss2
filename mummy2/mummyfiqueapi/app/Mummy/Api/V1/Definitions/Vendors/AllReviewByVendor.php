<?php
namespace App\Mummy\V1\Definitions\Vendors;
/**
 * @SWG\Definition(type="object", @SWG\Xml(name="Customer"))
 */
class AllReviewByVendor
{
	/**
     * @SWG\Property(example="51")
     * @var string
     */
    public $vendor_id;
    /**
     * @SWG\Property(example="1")
     * @var string
     */
    public $page;
    /**
     * @SWG\Property(example="10")
     * @var string
     */
    public $take;
    /**
     * @SWG\Property(example="1")
     * @var string
     */
    public $sort_by;
}