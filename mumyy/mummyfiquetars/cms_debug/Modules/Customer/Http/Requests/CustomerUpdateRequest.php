<?php namespace Modules\Customer\Http\Requests;

use Modules\Core\Internationalisation\BaseFormRequest;
use Modules\Customer\Entities\Customer;

class CustomerUpdateRequest extends BaseFormRequest {
	
	public function rules() {
		if($this->request->get('password')){
			return [ 
				'email' => 'required',
				'first_name' => 'required',
				'password' => 'min:8|alphanumeric',
				// 'last_name' => 'required',
			];
		}else{
			return [ 
				'email' => 'required',
				'first_name' => 'required',
				// 'last_name' => 'required',
			];
		}
		
	}
	public function authorize() {
		return true;
	}
	public function messages() {
		return [
			'email.unique_email_customer_update' => 'The email has already been taken',
			'first_name.required' => 'The name field is required.',
			'password.min' => 'The password must be at least 8 alphanumeric characters.',
			'password.alphanumeric' => 'The password must be at least 8 alphanumeric characters.',
		];
	}
}
