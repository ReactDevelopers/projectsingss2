<?php
namespace App\Mummy\V1\Definitions;
/**
 * @SWG\Definition(type="object", @SWG\Xml(name="Customer"))
 */
class SubCategory
{
    /**
     * The contact id list
     * @SWG\Property(items=@SWG\Items(type="integer"))
     * @var array
     */
    public $category_id;
    
}