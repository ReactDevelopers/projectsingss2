<?php

namespace App\Mummy\Api\V1\Repositories;

use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Criteria\RequestCriteria;
use App\Mummy\Api\V1\Repositories\UserRepository;
use App\Mummy\Api\V1\Entities\User;
use App\Mummy\Api\V1\Validators\UserValidator;

/**
 * Class UserRepositoryEloquent
 * @package namespace App\Mummy\Api\V1\Repositories;
 */
class UserRepositoryEloquent extends BaseRepository implements UserRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return User::class;
    }

    /**
    * Specify Validator class name
    *
    * @return mixed
    */
    public function validator()
    {

        return UserValidator::class;
    }


    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
        $this->pushCriteria(app(RequestCriteria::class));
    }
}
