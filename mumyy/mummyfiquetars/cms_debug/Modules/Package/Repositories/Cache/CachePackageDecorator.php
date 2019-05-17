<?php namespace Modules\Package\Repositories\Cache;

use Modules\Package\Repositories\PackageRepository;
use Modules\Core\Repositories\Cache\BaseCacheDecorator;

class CachePackageDecorator extends BaseCacheDecorator implements PackageRepository
{
    public function __construct(PackageRepository $package)
    {
        parent::__construct();
        $this->entityName = 'package.packages';
        $this->repository = $package;
    }
}
