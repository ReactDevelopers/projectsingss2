<?php
namespace App\Mummy\V1\Definitions\Vendors;
/**
 * @SWG\Definition(type="object", @SWG\Xml(name="Customer"))
 */
class SubmitComment
{
    /**
     * @SWG\Property(example="1")
     * @var string
     */
    public $portfolios_id;
    
    /**
     * @SWG\Property(format="int64",example="test comment")
     * @var string
     */
    public $comment;
}