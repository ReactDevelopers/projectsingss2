<?php
namespace App\Mummy\V1\Definitions\Home;
/**
 * @SWG\Definition(type="object", @SWG\Xml(name="Customer"))
 */
class MessageScreen
{
    /**
     * @SWG\Property(example="1")
     * @var string
     */
    public $type;
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
    public $is_read;
    /**
     * @SWG\Property(example="1")
     * @var string
     */
    public $sort_by;
}