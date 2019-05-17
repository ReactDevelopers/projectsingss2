<?php
namespace App\Mummy\V1\Definitions\Home;
/**
 * @SWG\Definition(type="object", @SWG\Xml(name="Customer"))
 */
class DeleteComment
{
    /**
     * @SWG\Property(example="1")
     * @var integer
     */
    public $comment_id;
}