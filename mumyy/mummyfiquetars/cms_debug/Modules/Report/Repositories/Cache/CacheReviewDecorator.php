<?php namespace Modules\Report\Repositories\Cache;

use Modules\Report\Repositories\ReviewRepository;
use Modules\Core\Repositories\Cache\BaseCacheDecorator;

class CacheReviewDecorator extends BaseCacheDecorator implements ReviewRepository
{
    public function __construct(ReviewRepository $review)
    {
        parent::__construct();
        $this->entityName = 'report.reviews';
        $this->repository = $review;
    }
}
