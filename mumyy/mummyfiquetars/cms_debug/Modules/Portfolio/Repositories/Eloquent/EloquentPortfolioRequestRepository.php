<?php namespace Modules\Portfolio\Repositories\Eloquent;

use Modules\Portfolio\Repositories\PortfolioRequestRepository;
use Modules\Core\Repositories\Eloquent\EloquentBaseRepository;

class EloquentPortfolioRequestRepository extends EloquentBaseRepository implements PortfolioRequestRepository
{
	public function countAllRequest(){
		return $this->model->whereHas('vendor', function($query){
								$query->where('status', 1);
								$query->whereNull('is_deleted');
							})
							->where(function($query){
								// $query->whereNull('status');
								$query->Where('status', 2);
							})
							->whereNull('is_deleted')
							->count();
	}
}
