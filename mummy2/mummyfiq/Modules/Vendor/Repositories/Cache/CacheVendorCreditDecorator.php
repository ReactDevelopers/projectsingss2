<?php namespace Modules\Vendor\Repositories\Cache;

use Modules\Vendor\Repositories\VendorCreditRepository;
use Modules\Core\Repositories\Cache\BaseCacheDecorator;

class CacheVendorCreditDecorator extends BaseCacheDecorator implements VendorCreditRepository
{
    public function __construct(VendorCreditRepository $vendorcredit)
    {
        parent::__construct();
        $this->entityName = 'vendor.vendorCredit';
        $this->repository = $vendor;
    }
}
