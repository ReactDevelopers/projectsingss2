<?php namespace Modules\Package\Http\Requests;

use Modules\Core\Internationalisation\BaseFormRequest;

class PackageCreateRequest extends BaseFormRequest {
	
	public function rules() {
		return [ 
			'name' => 'required|uniquePackageCreate',
			'sorts' => 'integer',
			'price' => 'required|min:0|integer'
		];
	}
	public function authorize() {
		return true;
	}
	public function messages() {
		return [
			'name.unique_package_create' => 'The name has already been taken',
		];
	}
}
