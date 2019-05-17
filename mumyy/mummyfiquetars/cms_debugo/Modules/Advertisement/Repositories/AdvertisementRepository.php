<?php namespace Modules\Advertisement\Repositories;

use Modules\Core\Repositories\BaseRepository;

interface AdvertisementRepository extends BaseRepository
{
	/**
     * Return a collection of all elements of the resource
     * @return mixed
     */
    public function all();

    /**
     * Create a resource
     *
     * @param $data
     * @return mixed
     */
    public function create($data);

    /**
     * @param $model
     * @param  array  $data
     * @return object
     */
    public function update($model, $data);

}
