<?php namespace Modules\Vendor\Http\Requests;

use Modules\Core\Internationalisation\BaseFormRequest;

class VendorCreateRequest extends BaseFormRequest {
	
	public function rules() {
		return [ 
			'email' => 'required|uniqueEmailVendorCreate',
			'first_name' => 'required',
			// 'last_name' => 'required',
			'password' => 'required|min:8|alphanumeric',

			'business_name' => 'required',
			'business_address' => 'required',
			// 'country_id' => 'required',
			// 'states_id' => 'required',
			// 'zip_code' => 'numeric|min:0|digits_between:5,6',
			// 'city_id' => 'required',
			'medias_single' =>'required',
			'category_id' => 'required',
			// 'business_phone[]' => 'numeric',
			'social_media_link_facebook' =>'url',
			'social_media_link_twitter' =>'url',
			'social_media_link_instagram' =>'url',
			'social_media_link_pinterest' =>'url',
		];
	}
	public function authorize() {
		return true;
	}
	public function messages() {
		return [
			'first_name.required' => 'The name field is required.',
			'email.unique_email_vendor_create' => 'The email has already been taken.',
			'country_id.required' => 'The country field is required.',
			'states_id.required' => 'The state field is required.',
			'city_id.required' => 'The city field is required.',
			'business_address.check_location' => 'Please type valid address.',
			'medias_single.required' => 'The image field is required.',
			'category_id.required' => 'The business category field is required.',
			'password.min' => 'The password must be at least 8 alphanumeric characters.',
			'password.alphanumeric' => 'The password must be at least 8 alphanumeric characters.',
		];
	}
}
