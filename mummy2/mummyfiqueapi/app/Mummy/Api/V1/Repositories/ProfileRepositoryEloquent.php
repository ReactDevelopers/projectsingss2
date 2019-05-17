<?php

namespace App\Mummy\Api\V1\Repositories;

use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Criteria\RequestCriteria;
use App\Mummy\Api\V1\Repositories\CustomerRepository;
use App\Mummy\Api\V1\Entities\Customer;
use App\Mummy\Api\V1\Validators\CustomerValidator;
use DB;
use App\Helpers\Helper;
/**
 * Class CustomerRepositoryEloquent
 * @package namespace App\Mummy\Api\V1\Repositories;
 */
class ProfileRepositoryEloquent extends BaseRepository implements ProfileRepository
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

    public function updateAvatar($attributes, $customer){
        if(isset($attributes['photo']) && $attributes['photo'] ){
            $file = $attributes['photo'];
            $fileName = md5(rand()) . '_' .time() . '.' . $file->getClientOriginalExtension();
            // $abc = DB::table('users')->where('id', $customer['id'])->get();dd($abc);
            DB::transaction(function () use ($customer, $attributes, $fileName)  {
                if(!empty($fileName)){
                    $photo = Helper::uploadImage($attributes['photo'], $fileName);

                    //update photo
                    DB::table('users')->where('id', $customer['id'])->update(['photo' => $photo]);
                }
            });
            return true;
        }
        return false;
        
    }
}
