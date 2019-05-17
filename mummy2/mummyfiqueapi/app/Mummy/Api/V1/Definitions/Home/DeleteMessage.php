<?php
namespace App\Mummy\V1\Definitions\Home;
/**
 * @SWG\Definition(type="object", @SWG\Xml(name="Customer"))
 */
class DeleteMessage
{
    /**
     * @SWG\Property(example="1")
     * @var integer
     */
    public $message_id;
}