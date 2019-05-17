<?php

namespace App\Mummy\Api\V1\Repositories;

use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Criteria\RequestCriteria;
use App\Mummy\Api\V1\Repositories\CustomerRepository;
use App\Mummy\Api\V1\Entities\Vendor;
use App\Mummy\Api\V1\Validators\VendorValidator;

/**
 * Class CustomerRepositoryEloquent
 * @package namespace App\Mummy\Api\V1\Repositories;
 */
class VendorRepositoryEloquent extends BaseRepository implements VendorRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return Vendor::class;
    }

    /**
    * Specify Validator class name
    *
    * @return mixed
    */
    public function validator()
    {

        return VendorValidator::class;
    }


    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
        $this->pushCriteria(app(RequestCriteria::class));
    }
}
