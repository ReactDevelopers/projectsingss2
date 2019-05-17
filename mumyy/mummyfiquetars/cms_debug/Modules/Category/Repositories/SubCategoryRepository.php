<?php namespace Modules\Category\Repositories;

use Modules\Core\Repositories\BaseRepository;

interface SubCategoryRepository extends BaseRepository
{
	
	public function getCategories();
}
