<?php namespace Modules\Vendor\Repositories\Eloquent;

use Modules\Vendor\Repositories\VendorCategoryRepository;
use Modules\Core\Repositories\Eloquent\EloquentBaseRepository;

class EloquentVendorCategoryRepository extends EloquentBaseRepository implements VendorCategoryRepository
{
	public function countAllRequest(){
		return $this->model->whereHas('category', function($query){
								$query->where('status', 1);
								$query->whereNull('is_deleted');
							})
							->whereHas('vendor', function($query){
								$query->where('status', 1);
								$query->whereNull('is_deleted');
							})
							->where(function($query){
								$query->whereNull('status');
								$query->orWhere('status', 2);
							})
							->whereNull('is_deleted')
							->count();
	}
}
