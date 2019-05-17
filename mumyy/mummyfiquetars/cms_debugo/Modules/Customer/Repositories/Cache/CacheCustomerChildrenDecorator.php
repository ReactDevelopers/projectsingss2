<?php namespace Modules\Customer\Repositories\Cache;

use Modules\Customer\Repositories\CustomerChildrenRepository;
use Modules\Core\Repositories\Cache\BaseCacheDecorator;

class CacheCustomerChildrenDecorator extends BaseCacheDecorator implements CustomerChildrenRepository
{
    public function __construct(CustomerChildrenRepository $customerchildren)
    {
        parent::__construct();
        $this->entityName = 'customer.customerchildren';
        $this->repository = $Customerchildren;
    }
}
