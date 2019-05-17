<?php namespace Modules\Advertisement\Repositories\Cache;

use Modules\Advertisement\Repositories\AdvertisementRepository;
use Modules\Core\Repositories\Cache\BaseCacheDecorator;

class CacheAdvertisementDecorator extends BaseCacheDecorator implements AdvertisementRepository
{
    public function __construct(AdvertisementRepository $advertisement)
    {
        parent::__construct();
        $this->entityName = 'advertisement.advertisements';
        $this->repository = $advertisement;
    }
}
