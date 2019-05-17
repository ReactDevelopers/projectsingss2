<?php namespace Modules\Portfolio\Repositories\Cache;

use Modules\Portfolio\Repositories\PortfolioRepository;
use Modules\Core\Repositories\Cache\BaseCacheDecorator;

class CachePortfolioDecorator extends BaseCacheDecorator implements PortfolioRepository
{
    public function __construct(PortfolioRepository $portfolio)
    {
        parent::__construct();
        $this->entityName = 'portfolio.portfolios';
        $this->repository = $portfolio;
    }
}
