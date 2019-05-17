<?php namespace Modules\Category\Repositories\Eloquent;

use Modules\Category\Repositories\SubCategoryRepository;
use Modules\Core\Repositories\Eloquent\EloquentBaseRepository;
use Modules\Category\Entities\Category;

class EloquentSubCategoryRepository extends EloquentBaseRepository implements SubCategoryRepository
{

	public function getCategories(){
		$categories = Category::orderBy('name', 'ASC')->get();
		$data =[];
		if(count($categories)){			
			foreach ($categories as $key => $item) {
				$data = $data + [$item->id => $item->name];
			}
		}
		return $data;
	}

	public function getCategoriesForSearch(){
		$categories = Category::orderBy('name', 'ASC')->get();
		$data =[];
		if(count($categories)){			
			foreach ($categories as $key => $item) {
				$data = $data + [$item->name => $item->name];
			}
		}
		return $data;
	}
}
