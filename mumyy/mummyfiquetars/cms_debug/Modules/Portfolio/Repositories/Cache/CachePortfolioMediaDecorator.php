<?php namespace Modules\Portfolio\Repositories\Cache;

use Modules\Portfolio\Repositories\PortfolioMediaRepository;
use Modules\Core\Repositories\Cache\BaseCacheDecorator;

class CachePortfolioMediaDecorator extends BaseCacheDecorator implements PortfolioMediaRepository
{
    public function __construct(PortfolioMediaRepository $portfoliomedia)
    {
        parent::__construct();
        $this->entityName = 'portfolio.portfoliomedias';
        $this->repository = $portfoliomedia;
    }
}
