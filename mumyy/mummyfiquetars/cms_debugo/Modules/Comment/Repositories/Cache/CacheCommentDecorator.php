<?php namespace Modules\Comment\Repositories\Cache;

use Modules\Comment\Repositories\CommentRepository;
use Modules\Core\Repositories\Cache\BaseCacheDecorator;

class CacheCommentDecorator extends BaseCacheDecorator implements CommentRepository
{
    public function __construct(CommentRepository $comment)
    {
        parent::__construct();
        $this->entityName = 'comment.comments';
        $this->repository = $comment;
    }
}
