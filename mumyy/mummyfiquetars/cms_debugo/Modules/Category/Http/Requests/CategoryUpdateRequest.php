<?php namespace Modules\Category\Http\Requests;

use Modules\Core\Internationalisation\BaseFormRequest;
use Modules\Category\Entities\Category;

class CategoryUpdateRequest extends BaseFormRequest {
	
	public function rules() {
		$id = $this->request->get('id');
		return [ 
			'name' => 'required|uniqueCategoryUpdate:' . $id,
			'sorts' => 'integer'
		];
	}
	public function authorize() {
		return true;
	}
	public function messages() {
		return [
			'name.unique_category_update' => 'The name has already been taken',
		];
	}
}
