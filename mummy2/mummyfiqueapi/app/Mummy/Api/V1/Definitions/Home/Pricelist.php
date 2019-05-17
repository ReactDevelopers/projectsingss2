<?php
namespace App\Mummy\V1\Definitions\Home;
/**
 * @SWG\Definition(type="object", @SWG\Xml(name="Customer"))
 */
class Pricelist
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
     * @SWG\Property(example="50")
     * @var string
     */
    public $vendor_id;
    /**
     * The contact id list
     * @SWG\Property(items=@SWG\Items(type="integer"))
     * @var array
     */
    public $category_id;
}