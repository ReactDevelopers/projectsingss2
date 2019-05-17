<?php namespace Modules\Vendor\Repositories\Eloquent;

use Modules\Vendor\Repositories\VendorRepository;
use Modules\Core\Repositories\Eloquent\EloquentBaseRepository;
use Illuminate\Support\Facades\Hash;

class EloquentVendorRepository extends EloquentBaseRepository implements VendorRepository
{
	/**
     * @var \Illuminate\Database\Eloquent\Model An instance of the Eloquent Model
     */
    protected $model;

    /**
     * @param Model $model
     */
    public function __construct($model)
    {
        $this->model = $model;
    }

	/**
     * Create a resource
     *
     * @param $data
     * @return mixed
     */
    public function create($data)
    {
        // dd($data);
        $data = $this->hashPassword($data);
        $item = $this->model->create($data);

    	\DB::table('role_users')->insert(
		    array(
		    	'user_id' => $item->id,
				'role_id' => Config('constant.user_role.vendor'),
				'created_at' => gmdate("Y-m-d H:i:s"),
				'updated_at' => gmdate("Y-m-d H:i:s"),
		    )
		);

		return $item ;
    }

    /**
     * @param $model
     * @param  array  $data
     * @return object
     */
    public function update($model, $data)
    {
        $this->checkForNewPassword($data);

        $model->update($data);

        return $model;
    }

    /**
     * Hash the password key
     * @param array $data
     */
    private function hashPassword(array &$data)
    {
        $data['password'] = Hash::make(hash('sha1', $data['password']));

        return $data;
    }

    /**
     * Check if there is a new password given
     * If not, unset the password field
     * @param array $data
     */
    private function checkForNewPassword(array &$data)
    {
        if (! $data['password']) {
            unset($data['password']);

            return;
        }

        $data['password'] = Hash::make(hash('sha1', $data['password']));

        return $data;
    }
}
