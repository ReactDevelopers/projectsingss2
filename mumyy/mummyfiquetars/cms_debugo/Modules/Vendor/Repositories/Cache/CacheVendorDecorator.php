<?php namespace Modules\Vendor\Repositories\Cache;

use Modules\Vendor\Repositories\VendorRepository;
use Modules\Core\Repositories\Cache\BaseCacheDecorator;

class CacheVendorDecorator extends BaseCacheDecorator implements VendorRepository
{
    public function __construct(VendorRepository $vendor)
    {
        parent::__construct();
        $this->entityName = 'vendor.vendors';
        $this->repository = $vendor;
    }
}
