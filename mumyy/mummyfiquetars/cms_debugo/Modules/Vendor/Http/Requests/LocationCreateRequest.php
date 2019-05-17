<?php namespace Modules\Vendor\Http\Requests;

use Modules\Core\Internationalisation\BaseFormRequest;

class LocationCreateRequest extends BaseFormRequest {
	
	public function rules() {
		$vendor_id = $this->request->get('vendor_id');
		return [ 
			'country_id' => 'required',
			'city_id' => 'checkUniqueLocation:' . $vendor_id,
			'zip_code' => 'numeric|min:0|digits_between:5,6',
		];
	}
	public function authorize() {
		return true;
	}
	public function messages() {
		return [
			'city_id.check_unique_location' => 'Location already exist!',
		];
	}
}
