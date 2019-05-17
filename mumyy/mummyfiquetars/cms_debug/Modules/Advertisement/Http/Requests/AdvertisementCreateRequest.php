<?php namespace Modules\Advertisement\Http\Requests;

use Modules\Core\Internationalisation\BaseFormRequest;

class AdvertisementCreateRequest extends BaseFormRequest {
	
	public function rules() {
		$adv_id = $this->request->get('adv_id');
		if($adv_id == 7){
			return [ 
				'title' => 'required',
				'link' => 'required',
				'by' => 'required',
				'medias_single' => 'required',
			];
		}else{
			return [ 
				// 'title' => 'required|uniqueAdvertisementUpdate:' . $id,
				'medias_single' => 'required',
			];
		}
	}
	public function authorize() {
		return true;
	}
	public function messages() {
		return [
			// 'title.unique_advertisement_create' => 'The title has already been taken',
			'medias_single.required' => 'The image field is required.',
		];
	}
}
