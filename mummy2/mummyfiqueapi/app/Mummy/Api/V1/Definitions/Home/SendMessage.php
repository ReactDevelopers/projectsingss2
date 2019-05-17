<?php
namespace App\Mummy\V1\Definitions\Home;
/**
 * @SWG\Definition(type="object", @SWG\Xml(name="Customer"))
 */
class SendMessage
{
    /**
     * @SWG\Property(example="4")
     * @var integer
     */
    public $receiver_id;
    /**
     * @SWG\Property(example="test title")
     * @var string
     */
    public $subject;
    /**
     * @SWG\Property(example="test content")
     * @var string
     */
    public $message;
}