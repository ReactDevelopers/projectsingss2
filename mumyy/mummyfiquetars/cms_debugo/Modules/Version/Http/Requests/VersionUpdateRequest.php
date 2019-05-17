<?php namespace Modules\Version\Http\Requests;

use Modules\Core\Internationalisation\BaseFormRequest;

class VersionUpdateRequest extends BaseFormRequest {
	
	public function rules() {
		$configs = config('asgard.version.config.config');
		$data = [];
		if(!$configs) return [];

		foreach ($configs as $key => $config) {
			if($config['required']){
				$data[$config['title']] = 'required';
			}
		}
		return $data;
	}
	public function authorize() {
		return true;
	}
	public function messages() {
		return [
			'android-version.required' => 'The Android Version field is required.',
			'ios-version.required' => 'The iOS Version field is required.',
		];
	}
}
