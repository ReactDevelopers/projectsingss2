<?php namespace Modules\Package\Http\Requests;

use Modules\Core\Internationalisation\BaseFormRequest;

class PackageUpdateRequest extends BaseFormRequest {
	
	public function rules() {
		$id = $this->request->get('id');
		return [ 
			'name' => 'required|uniquePackageUpdate:' . $id,
			'sorts' => 'integer',
			'price' => 'required|min:0|numeric'
		];
	}
	public function authorize() {
		return true;
	}
	public function messages() {
		return [
			'name.unique_package_update' => 'The name has already been taken',
		];
	}
}
