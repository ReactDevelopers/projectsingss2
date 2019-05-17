<?php namespace Modules\Customer\Http\Requests;

use Modules\Core\Internationalisation\BaseFormRequest;

class CustomerCreateRequest extends BaseFormRequest {
	
	public function rules() {
		return [ 
			'email' => 'required|email|uniqueEmailCustomerCreate',
			'first_name' => 'required',
			// 'last_name' => 'required',
			'password' => 'required|min:8|alphanumeric',
		];
	}
	public function authorize() {
		return true;
	}
	public function messages() {
		return [
			'email.unique_email_customer_create' => 'The email has already been taken',
			'first_name.required' => 'The name field is required.',
			'password.min' => 'The password must be at least 8 alphanumeric characters.',
			'password.alphanumeric' => 'The password must be at least 8 alphanumeric characters.',
		];
	}
}
