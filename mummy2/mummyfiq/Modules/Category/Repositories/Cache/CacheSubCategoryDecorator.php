<?php namespace Modules\Category\Repositories\Cache;

use Modules\Category\Repositories\SubCategoryRepository;
use Modules\Core\Repositories\Cache\BaseCacheDecorator;

class CacheSubCategoryDecorator extends BaseCacheDecorator implements SubCategoryRepository
{
    public function __construct(SubCategoryRepository $subcategory)
    {
        parent::__construct();
        $this->entityName = 'category.subcategories';
        $this->repository = $subcategory;
    }
}
