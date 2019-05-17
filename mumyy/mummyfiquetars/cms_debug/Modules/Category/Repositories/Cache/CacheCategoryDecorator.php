<?php namespace Modules\Category\Repositories\Cache;

use Modules\Category\Repositories\CategoryRepository;
use Modules\Core\Repositories\Cache\BaseCacheDecorator;

class CacheCategoryDecorator extends BaseCacheDecorator implements CategoryRepository
{
    public function __construct(CategoryRepository $category)
    {
        parent::__construct();
        $this->entityName = 'category.categories';
        $this->repository = $category;
    }
}
