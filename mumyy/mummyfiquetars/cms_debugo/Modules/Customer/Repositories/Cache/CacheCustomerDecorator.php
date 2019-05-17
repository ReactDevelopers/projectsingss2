<?php namespace Modules\Customer\Repositories\Cache;

use Modules\Customer\Repositories\CustomerRepository;
use Modules\Core\Repositories\Cache\BaseCacheDecorator;

class CacheCustomerDecorator extends BaseCacheDecorator implements CustomerRepository
{
    public function __construct(CustomerRepository $customer)
    {
        parent::__construct();
        $this->entityName = 'customer.customers';
        $this->repository = $customer;
    }
}
