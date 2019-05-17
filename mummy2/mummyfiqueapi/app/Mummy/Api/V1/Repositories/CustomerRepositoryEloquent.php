<?php

namespace App\Mummy\Api\V1\Repositories;

use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Criteria\RequestCriteria;
use App\Mummy\Api\V1\Repositories\CustomerRepository;
use App\Mummy\Api\V1\Entities\Customer;
use App\Mummy\Api\V1\Validators\CustomerValidator;

/**
 * Class CustomerRepositoryEloquent
 * @package namespace App\Mummy\Api\V1\Repositories;
 */
class CustomerRepositoryEloquent extends BaseRepository implements CustomerRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return Customer::class;
    }

    /**
    * Specify Validator class name
    *
    * @return mixed
    */
    public function validator()
    {

        return CustomerValidator::class;
    }


    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
        $this->pushCriteria(app(RequestCriteria::class));
    }
}
