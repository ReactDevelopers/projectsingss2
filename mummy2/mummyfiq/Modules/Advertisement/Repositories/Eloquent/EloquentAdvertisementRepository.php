<?php namespace Modules\Advertisement\Repositories\Eloquent;

use Modules\Advertisement\Repositories\AdvertisementRepository;
use Modules\Core\Repositories\Eloquent\EloquentBaseRepository;
use Modules\Advertisement\Entities\Advertisement;
use Modules\Media\Repositories\FileRepository;

class EloquentAdvertisementRepository extends EloquentBaseRepository implements AdvertisementRepository
{
	public $timestamps = false;

	/**
     * Return a collection of all elements of the resource
     * @return mixed
     */
	public function all()
    {
        return $this->model->orderBy('id', 'DESC')->get();
    }

}
