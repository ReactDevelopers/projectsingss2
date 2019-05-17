<?php namespace Modules\Portfolio\Http\Requests;

use Modules\Core\Internationalisation\BaseFormRequest;

class CreatePortfolioRequest extends BaseFormRequest {
	
	public function rules() {
		return [ 
			'vendor_id' => 'required',
			'category_id' => 'required',
			'city' => 'required',
			'title' => 'required',
			// 'description' => 'required',
			// 'city_id' => 'required',
		];
	}
	public function authorize() {
		return true;
	}
	public function messages() {
		return [
			'category_id.required' => 'The category field is required.'
		];
	}
}
