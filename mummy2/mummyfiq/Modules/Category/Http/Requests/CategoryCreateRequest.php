<?php namespace Modules\Category\Http\Requests;

use Modules\Core\Internationalisation\BaseFormRequest;

class CategoryCreateRequest extends BaseFormRequest {
	
	public function rules() {
		return [ 
			'name' => 'required|uniqueCategoryInsert',
			'sorts' => 'integer'
		];
	}
	public function authorize() {
		return true;
	}
	public function messages() {
		return [
			'name.unique_category_insert' => 'The name has already been taken',
		];
	}
}
