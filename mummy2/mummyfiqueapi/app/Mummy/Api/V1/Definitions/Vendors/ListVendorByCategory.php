<?php
namespace App\Mummy\V1\Definitions\Vendors;
/**
 * @SWG\Definition(type="object", @SWG\Xml(name="Customer"))
 */
class ListVendorByCategory
{
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
    public $category_id;
     /**
     * The contact id list
     * @SWG\Property(items=@SWG\Items(type="integer"))
     * @var array
     */
    public $country_id;
     /**
     * @SWG\Property(example="1")
     * @var string
     */
    public $sort_by;
    
}