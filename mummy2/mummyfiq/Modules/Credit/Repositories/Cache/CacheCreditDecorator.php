<?php namespace Modules\Credit\Repositories\Cache;

use Modules\Credit\Repositories\CreditRepository;
use Modules\Core\Repositories\Cache\BaseCacheDecorator;

class CacheCreditDecorator extends BaseCacheDecorator implements CreditRepository
{
    public function __construct(CreditRepository $credit)
    {
        parent::__construct();
        $this->entityName = 'credit.credits';
        $this->repository = $credit;
    }
}
