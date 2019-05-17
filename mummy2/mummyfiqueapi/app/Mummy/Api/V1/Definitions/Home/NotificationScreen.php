<?php
namespace App\Mummy\V1\Definitions\Home;
/**
 * @SWG\Definition(type="object", @SWG\Xml(name="Customer"))
 */
class NotificationScreen
{
	 /**
     * @SWG\Property(example="1")
     * @var string
     */
    public $reply_my_message;
    /**
     * @SWG\Property(example="1")
     * @var string
     */
    public $reply_my_review;
}