<?php namespace Modules\Category\Http\Requests;

use Modules\Core\Internationalisation\BaseFormRequest;

class SubCategoryCreateRequest extends BaseFormRequest {
	
	public function rules() {
		return [ 
			'name' => 'required|uniqueSubCategoryInsert',
			'sorts' => 'integer',
			// 'category_id' => 'required'
		];
	}
	public function authorize() {
		return true;
	}
	public function messages() {
		return [
			'name.unique_sub_category_insert' => 'The name has already been taken',
		];
	}
}
