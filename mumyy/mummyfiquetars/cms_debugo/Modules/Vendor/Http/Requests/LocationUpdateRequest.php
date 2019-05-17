<?php namespace Modules\Vendor\Http\Requests;

use Modules\Core\Internationalisation\BaseFormRequest;

class LocationUpdateRequest extends BaseFormRequest {
	
	public function rules() {
		$id = $this->request->get('location_id');
		$vendor_id = $this->request->get('vendor_id');
		return [ 
			'country_id' => 'required',
			'city_id' => 'checkUniqueLocation:' . $vendor_id . ',' . $id,
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
