<?php namespace Modules\Banner\Repositories\Cache;

use Modules\Banner\Repositories\BannerRepository;
use Modules\Core\Repositories\Cache\BaseCacheDecorator;

class CacheBannerDecorator extends BaseCacheDecorator implements BannerRepository
{
    public function __construct(BannerRepository $banner)
    {
        parent::__construct();
        $this->entityName = 'banner.banners';
        $this->repository = $banner;
    }
}
