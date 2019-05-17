<?php
namespace App\Mummy\V1\Definitions\Home;
/**
 * @SWG\Definition(type="object", @SWG\Xml(name="Customer"))
 */
class Search
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
     * @SWG\Property(example="vendor")
     * @var string
     */
    public $name;
    /**
     * The contact id list
     * @SWG\Property(items=@SWG\Items(type="integer"))
     * @var array
     */
    public $category_id;
    /**
     * The contact id list
     * @SWG\Property(items=@SWG\Items(type="integer"))
     * @var array
     */
    public $sub_category_id;
    /**
     * The contact id list
     * @SWG\Property(items=@SWG\Items(type="integer"))
     * @var array
     */
    public $price_range_id;
    /**
     * The contact id list
     * @SWG\Property(items=@SWG\Items(type="integer"))
     * @var array
     */
    public $country_id;
    /**
     * The contact id list
     * @SWG\Property(items=@SWG\Items(type="integer"))
     * @var array
     */
    public $city_id;
}