<?php namespace Modules\Category\Http\Requests;

use Modules\Core\Internationalisation\BaseFormRequest;

class SubCategoryUpdateRequest extends BaseFormRequest {
	
	public function rules() {
		$id = $this->request->get('id');
		return [ 
			'name' => 'required|uniqueSubCategoryUpdate:' . $id,
			'sorts' => 'integer',
			// 'category_id' => 'required'
		];
	}
	public function authorize() {
		return true;
	}
	public function messages() {
		return [
			'name.unique_sub_category_update' => 'The name has already been taken',
		];
	}
}
