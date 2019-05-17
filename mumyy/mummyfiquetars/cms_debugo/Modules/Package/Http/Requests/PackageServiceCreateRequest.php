<?php namespace Modules\Package\Http\Requests;

use Modules\Core\Internationalisation\BaseFormRequest;

class PackageServiceCreateRequest extends BaseFormRequest {
	
	public function rules() {
		return [ 
			'name' => 'required|uniquePackageServiceCreate',
			'sorts' => 'integer'
		];
	}
	public function authorize() {
		return true;
	}
	public function messages() {
		return [
			'name.unique_package_service_create' => 'The name has already been taken',
		];
	}
}
