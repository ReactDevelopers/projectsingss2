<?php namespace Modules\Review\Repositories\Cache;

use Modules\Review\Repositories\ReviewRepository;
use Modules\Core\Repositories\Cache\BaseCacheDecorator;

class CacheReviewDecorator extends BaseCacheDecorator implements ReviewRepository
{
    public function __construct(ReviewRepository $review)
    {
        parent::__construct();
        $this->entityName = 'review.reviews';
        $this->repository = $review;
    }
}
