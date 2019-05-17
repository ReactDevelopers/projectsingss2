<?php namespace Modules\Portfolio\Repositories;

use Modules\Core\Repositories\BaseRepository;

interface PortfolioRequestRepository extends BaseRepository
{
	public function countAllRequest();
}
