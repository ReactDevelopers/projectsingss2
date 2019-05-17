<?php namespace Modules\Vendor\Repositories\Cache;

use Modules\Vendor\Repositories\VendorPhoneRepository;
use Modules\Core\Repositories\Cache\BaseCacheDecorator;

class CacheVendorPhoneDecorator extends BaseCacheDecorator implements VendorPhoneRepository
{
    public function __construct(VendorPhoneRepository $vendor)
    {
        parent::__construct();
        $this->entityName = 'vendor.vendorPhones';
        $this->repository = $vendor;
    }
}
