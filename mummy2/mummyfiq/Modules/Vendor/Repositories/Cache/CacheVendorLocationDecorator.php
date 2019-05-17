<?php namespace Modules\Vendor\Repositories\Cache;

use Modules\Vendor\Repositories\VendorLocationRepository;
use Modules\Core\Repositories\Cache\BaseCacheDecorator;

class CacheVendorLocationDecorator extends BaseCacheDecorator implements VendorLocationRepository
{
    public function __construct(VendorLocationRepository $vendor)
    {
        parent::__construct();
        $this->entityName = 'vendor.vendorLocations';
        $this->repository = $vendor;
    }
}
