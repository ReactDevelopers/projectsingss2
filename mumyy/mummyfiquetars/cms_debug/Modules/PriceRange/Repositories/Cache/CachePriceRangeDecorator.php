<?php namespace Modules\PriceRange\Repositories\Cache;

use Modules\PriceRange\Repositories\PriceRangeRepository;
use Modules\Core\Repositories\Cache\BaseCacheDecorator;

class CachePriceRangeDecorator extends BaseCacheDecorator implements PriceRangeRepository
{
    public function __construct(PriceRangeRepository $pricerange)
    {
        parent::__construct();
        $this->entityName = 'pricerange.priceranges';
        $this->repository = $pricerange;
    }
}
