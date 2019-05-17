<?php namespace Modules\Package\Repositories\Cache;

use Modules\Package\Repositories\PackageServiceRepository;
use Modules\Core\Repositories\Cache\BaseCacheDecorator;

class CachePackageServiceDecorator extends BaseCacheDecorator implements PackageServiceRepository
{
    public function __construct(PackageServiceRepository $packageservice)
    {
        parent::__construct();
        $this->entityName = 'package.packageservices';
        $this->repository = $packageservice;
    }
}
