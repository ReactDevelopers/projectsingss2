<?php namespace Modules\Advertisement\Repositories\Cache;

use Modules\Advertisement\Repositories\AdvertisementRepository;
use Modules\Core\Repositories\Cache\BaseCacheDecorator;

class CacheAdvertisementTypeDecorator extends BaseCacheDecorator implements AdvertisementTypeRepository
{
    public function __construct(AdvertisementTypeRepository $advertisementType)
    {
        parent::__construct();
        $this->entityName = 'advertisement.advertisementTypes';
        $this->repository = $advertisementType;
    }
}
