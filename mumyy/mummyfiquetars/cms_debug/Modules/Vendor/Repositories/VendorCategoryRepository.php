<?php namespace Modules\Vendor\Repositories;

use Modules\Core\Repositories\BaseRepository;

interface VendorCategoryRepository extends BaseRepository
{
	public function countAllRequest();
}
