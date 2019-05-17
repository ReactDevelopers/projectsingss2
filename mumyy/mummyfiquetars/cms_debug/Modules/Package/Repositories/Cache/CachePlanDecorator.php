<?php namespace Modules\Package\Repositories\Cache;

use Modules\Package\Repositories\PlanRepository;
use Modules\Core\Repositories\Cache\BaseCacheDecorator;

class CachePlanDecorator extends BaseCacheDecorator implements PlanRepository
{
    public function __construct(PlanRepository $plan)
    {
        parent::__construct();
        $this->entityName = 'package.plans';
        $this->repository = $plan;
    }
}
