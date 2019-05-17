<?php namespace Modules\Comment\Repositories\Cache;

use Modules\Comment\Repositories\VendorcommentRepository;
use Modules\Core\Repositories\Cache\BaseCacheDecorator;

class CacheVendorcommentDecorator extends BaseCacheDecorator implements VendorcommentRepository
{
    public function __construct(VendorcommentRepository $vendorcomment)
    {
        parent::__construct();
        $this->entityName = 'comment.vendorcomments';
        $this->repository = $vendorcomment;
    }
}
