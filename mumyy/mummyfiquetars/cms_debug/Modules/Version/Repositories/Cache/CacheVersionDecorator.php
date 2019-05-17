<?php namespace Modules\Version\Repositories\Cache;

use Modules\Version\Repositories\VersionRepository;
use Modules\Core\Repositories\Cache\BaseCacheDecorator;

class CacheVersionDecorator extends BaseCacheDecorator implements VersionRepository
{
    public function __construct(VersionRepository $version)
    {
        parent::__construct();
        $this->entityName = 'version.versions';
        $this->repository = $version;
    }
}
