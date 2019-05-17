<?php namespace Modules\Credit\Http\Requests;

use Modules\Core\Internationalisation\BaseFormRequest;

class CreateCreditRequest extends BaseFormRequest {
	
	public function rules() {
		return [ 
			'amount' => 'required',
			'point' => 'required',
			// 'city_id' => 'required',
		];
	}
	public function authorize() {
		return true;
	}
	public function messages() {
		return [
			
		];
	}
}
