<?php namespace Modules\Package\Http\Requests;

use Modules\Core\Internationalisation\BaseFormRequest;

class PackageServiceUpdateRequest extends BaseFormRequest {
	
	public function rules() {
		$id = $this->request->get('id');
		return [ 
			'name' => 'required|uniquePackageServiceUpdate:' . $id,
			'sorts' => 'integer'
		];
	}
	public function authorize() {
		return true;
	}
	public function messages() {
		return [
			'name.unique_package_service_update' => 'The name has already been taken',
		];
	}
}
