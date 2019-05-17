<?php namespace Modules\PriceRange\Http\Requests;

use Modules\Core\Internationalisation\BaseFormRequest;

class PriceRangeUpdateRequest extends BaseFormRequest {
	
	public function rules() {
		$id = $this->request->get('id');
		return [ 
			'price_name' => 'required|uniquePriceRangeUpdate:' . $id,
			'sorts' => 'integer',
		];
	}
	public function authorize() {
		return true;
	}
	public function messages() {
		return [
			'name.unique_price_range_update' => 'The name has already been taken',
		];
	}
}
