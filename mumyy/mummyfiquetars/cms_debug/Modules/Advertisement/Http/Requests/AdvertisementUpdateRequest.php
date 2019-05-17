<?php namespace Modules\Advertisement\Http\Requests;

use Modules\Core\Internationalisation\BaseFormRequest;
use Modules\Media\Repositories\FileRepository;
use Modules\Advertisement\Repositories\AdvertisementRepository;

class AdvertisementUpdateRequest extends BaseFormRequest {
	
	public function rules() {		
		$repository = app(AdvertisementRepository::class);
		$file = app(FileRepository::class);

		$id = $this->request->get('id');
		$advertisement = $repository->find($id);
		$image = $file->findFileByZoneForEntity('image', $advertisement);
		$required = $image ? '' : 'required';

		$adv_id = $this->request->get('adv_id');
		if($adv_id == 7){
			return [ 
				'title' => 'required',
				'link' => 'required',
				'by' => 'required',
				'medias_single' => $required,
			];
		}else{
			return [ 
				// 'title' => 'required|uniqueAdvertisementUpdate:' . $id,
				'medias_single' => $required,
			];
		}
		
	}
	public function authorize() {
		return true;
	}
	public function messages() {
		return [
			// 'title.unique_advertisement_update' => 'The title has already been taken',
			'medias_single.required' => 'The image field is required.',
		];
	}
}
