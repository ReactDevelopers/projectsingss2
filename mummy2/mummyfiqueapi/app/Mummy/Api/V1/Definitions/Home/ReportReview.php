<?php
namespace App\Mummy\V1\Definitions\Home;
/**
 * @SWG\Definition(type="object", @SWG\Xml(name="Customer"))
 */
class ReportReview
{
    /**
     * @SWG\Property(example="1")
     * @var string
     */
    public $review_id;
    /**
     * @SWG\Property(example="1")
     * @var string
     */
    public $content;
}