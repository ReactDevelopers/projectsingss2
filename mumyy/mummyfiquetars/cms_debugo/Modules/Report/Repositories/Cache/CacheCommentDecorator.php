<?php namespace Modules\Report\Repositories\Cache;

use Modules\Report\Repositories\CommentRepository;
use Modules\Core\Repositories\Cache\BaseCacheDecorator;

class CacheCommentDecorator extends BaseCacheDecorator implements CommentRepository
{
    public function __construct(CommentRepository $comment)
    {
        parent::__construct();
        $this->entityName = 'report.comments';
        $this->repository = $comment;
    }
}
