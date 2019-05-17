<?php namespace Modules\PriceRange\Http\Requests;

use Modules\Core\Internationalisation\BaseFormRequest;

class PriceRangeCreateRequest extends BaseFormRequest {
	
	public function rules() {
		return [ 
			'price_name' => 'required|uniquePriceRangeCreate',
			'sorts' => 'integer',
		];
	}
	public function authorize() {
		return true;
	}
	public function messages() {
		return [
			'name.unique_price_range_create' => 'The name has already been taken',
		];
	}
}
