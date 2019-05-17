<?php namespace Modules\Vendor\Repositories\Cache;

use Modules\Vendor\Repositories\VendorProfileRepository;
use Modules\Core\Repositories\Cache\BaseCacheDecorator;

class CacheVendorProfileDecorator extends BaseCacheDecorator implements VendorProfileRepository
{
    public function __construct(VendorProfileRepository $vendor)
    {
        parent::__construct();
        $this->entityName = 'vendor.vendorProfiles';
        $this->repository = $vendor;
    }
}
