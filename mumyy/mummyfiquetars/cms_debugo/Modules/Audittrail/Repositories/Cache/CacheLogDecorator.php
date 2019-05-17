<?php namespace Modules\Audittrail\Repositories\Cache;

use Modules\Audittrail\Repositories\LogRepository;
use Modules\Core\Repositories\Cache\BaseCacheDecorator;

class CacheLogDecorator extends BaseCacheDecorator implements LogRepository
{
    public function __construct(LogRepository $log)
    {
        parent::__construct();
        $this->entityName = 'audittrail.logs';
        $this->repository = $log;
    }
}
