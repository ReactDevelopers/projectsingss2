<?php
namespace App\Mummy\V1\Definitions\Home;
/**
 * @SWG\Definition(type="object", @SWG\Xml(name="Customer"))
 */
class SendReview
{
    /**
     * @SWG\Property(example="27")
     * @var integer
     */
    public $vendor_id;
    /**
     * @SWG\Property(example="3")
     * @var integer
     */
    public $rating;
    /**
     * @SWG\Property(example="test title")
     * @var string
     */
    public $title;
    /**
     * @SWG\Property(example="test content")
     * @var string
     */
    public $content;
}