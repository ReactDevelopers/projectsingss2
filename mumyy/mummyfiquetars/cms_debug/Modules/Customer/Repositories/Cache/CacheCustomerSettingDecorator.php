<?php namespace Modules\Customer\Repositories\Cache;

use Modules\Customer\Repositories\CustomerSettingRepository;
use Modules\Core\Repositories\Cache\BaseCacheDecorator;

class CacheCustomerSettingDecorator extends BaseCacheDecorator implements CustomerSettingRepository
{
    public function __construct(CustomerSettingRepository $customersetting)
    {
        parent::__construct();
        $this->entityName = 'customer.customersettings';
        $this->repository = $customersetting;
    }
}
