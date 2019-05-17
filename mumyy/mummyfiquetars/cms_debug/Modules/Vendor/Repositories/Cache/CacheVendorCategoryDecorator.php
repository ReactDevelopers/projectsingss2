<?php namespace Modules\Vendor\Repositories\Cache;

use Modules\Vendor\Repositories\VendorCategoryRepository;
use Modules\Core\Repositories\Cache\BaseCacheDecorator;

class CacheVendorCategoryDecorator extends BaseCacheDecorator implements VendorCategoryRepository
{
    public function __construct(VendorCategoryRepository $vendor)
    {
        parent::__construct();
        $this->entityName = 'vendor.vendorCategorys';
        $this->repository = $vendor;
    }
}
