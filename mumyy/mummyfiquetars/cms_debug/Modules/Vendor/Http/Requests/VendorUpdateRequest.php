<?php namespace Modules\Vendor\Http\Requests;

use Modules\Core\Internationalisation\BaseFormRequest;

class VendorUpdateRequest extends BaseFormRequest {
	
	public function rules() {
		$id = $this->request->get('id');
		$return = [ 
			'email' => 'required|uniqueEmailVendorUpdate:' . $id,
			'first_name' => 'required',
			// 'last_name' => 'required',

			'business_name' => 'required',
			'business_address' => 'required',
			// 'country_id' => 'required',
			// 'states_id' => 'required',
			// 'zip_code' => 'numeric|min:0|digits_between:5,6',
			// 'status' => 'checkVendorPortfolio:' . $id,
			'category_id' => 'required',
			// 'business_phone' => 'numeric',
			'social_media_link_facebook' =>'url',
			'social_media_link_twitter' =>'url',
			'social_media_link_instagram' =>'url',
			'social_media_link_pinterest' =>'url',
		];
		if($this->request->get('password')){
			$return += ['password' => 'min:8|alphanumeric'];
		}
		return $return;
	}
	public function authorize() {
		return true;
	}
	public function messages() {
		return [
			'first_name.required' => 'The name field is required.',
			'email.unique_email_vendor_update' => 'The email has already been taken.',
			'country_id.required' => 'The country field is required.',
			'states_id.required' => 'The state field is required.',
			'city_id.required' => 'The city field is required.',
			'business_address.check_location' => 'Please type valid address.',
			// 'status.check_vendor_portfolio' => 'You have to add a portfolio in order to publish this vendor.',
			'category_id.required' => 'The business category field is required.',
			'password.min' => 'The password must be at least 8 alphanumeric characters.',
			'password.alphanumeric' => 'The password must be at least 8 alphanumeric characters.',
		];
	}
}
