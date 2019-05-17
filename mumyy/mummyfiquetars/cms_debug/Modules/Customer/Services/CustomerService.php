<?php namespace Modules\Customer\Services;

use DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Pagination\Paginator;
use Illuminate\Http\Request;
use Modules\Customer\Entities\Customer;
use Modules\Customer\Repositories\CustomerRepository;
use Modules\Customer\Repositories\CustomerSettingRepository;
use Modules\Customer\Repositories\CustomerChildrenRepository;
use Modules\Media\Services\FileService;
use Modules\Media\Repositories\FileRepository;
use Carbon\Carbon;
use Modules\Vendor\Repositories\VendorPhoneRepository;
use Modules\Comment\Repositories\CommentRepository;
use Modules\Comment\Repositories\VendorcommentRepository;
use Modules\Comment\Services\CommentService;

class CustomerService {

	/**
     *
     * @var CustomerRepository
     */
    private $customerRepository;  

    /**
     *
     * @var CustomerSettingRepository
     */
    private $customerSettingRepository;  

    /**
     *
     * @var CustomerChildrenRepository
     */
    private $customerChildrenRepository;  

    /**
     *
     * @var FileService
     */
    private $fileService;

    /**
     * @var FileRepository
     */
    private $file;

    /**
     * @var VendorPhoneRepository
     */
    private $vendorPhoneRepository;

    /**
     * @var CommentRepository
     */
    private $reviewRepository;

    /**
     * @var VendorcommentRepository
     */
    private $commentRepository;

    /**
     * @var CommentService
     */
    private $commentService;

    public function __construct( CustomerRepository $customerRepository, CustomerSettingRepository $customerSettingRepository, CustomerChildrenRepository $customerChildrenRepository, FileService $fileService, FileRepository $file, VendorPhoneRepository $vendorPhoneRepository, CommentRepository $reviewRepository, VendorcommentRepository $commentRepository, CommentService $commentService) {
        $this->customerRepository           = $customerRepository;
        $this->customerSettingRepository    = $customerSettingRepository;
        $this->customerChildrenRepository   = $customerChildrenRepository;
        $this->fileService                  = $fileService;
        $this->file                         = $file;
        $this->vendorPhoneRepository        = $vendorPhoneRepository;
        $this->reviewRepository             = $reviewRepository;
        $this->commentRepository            = $commentRepository;
        $this->commentService               = $commentService;
    }

    public function all(){
        return Customer::select('users.*', 'role_users.role_id')->join('role_users', 'role_users.user_id', '=', 'users.id')->where('role_users.role_id', Config('constant.user_role.customer'))->get();
    }

   	/**
     * Create a resource
     *
     * @param $data
     * @return mixed
     */
    public function create($data)
    {
        $data = $this->hashPassword($data);
        $data = array_merge($data, array('name' => $data['first_name']));
        $item = $this->customerRepository->create($data);

    	\DB::table('role_users')->insert(
		    array(
		    	'user_id' => $item->id,
				'role_id' => Config('constant.user_role.customer'),
				'created_at' => gmdate("Y-m-d H:i:s"),
				'updated_at' => gmdate("Y-m-d H:i:s"),
		    )
		);

        // add user phone
        $dataPhone = [
            'phone_number'  => $data['phone'],
            'country_code'  => 65,
            'is_primary'    => 1,
            'is_verifyed'   => 1,
            'status'        => 1,
            'user_id'       => $item->id,
        ];
        $this->vendorPhoneRepository->create($dataPhone);

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
        $data = array_merge($data, array('name' => $data['first_name']));
        $model->update($data);

        // update user phone getByAttributes
        if(isset($data['phone']) && !empty($data['phone'])){
            $phone = $this->vendorPhoneRepository->findByAttributes(['user_id' => $model->id]);
            $phone->update(['phone_number' => $data['phone']]);
        }else{
            $phones = $this->vendorPhoneRepository->getByAttributes(['user_id' => $model->id]);
            if(count($phones)){
                foreach ($phones as $key => $item) {
                   $this->vendorPhoneRepository->destroy($item);
                }
            }
        }
        

        return $model;
    }

    /**
     * @param  Model $model
     * @return bool
     */
    public function destroy($model)
    {
    	$now = Carbon::now()->timestamp;

    	// if(!empty($model->facebook_id)) $model->facebook_id = $model->facebook_id . '_' . $now;
    	// if(!empty($model->google_id)) $model->google_id = $model->google_id . '_' . $now;
    	// $model->email = $model->email . '_' . $now;
    	// $model->status = 0;
    	// $model->save();
        // update data before delete
        $dataDeleted = [
            'facebook_id' => !empty($model->facebook_id) ? $model->facebook_id . '_' . $now : "",
            'google_id' => !empty($model->google_id) ? $model->google_id . '_' . $now : "",
            'email' => $model->email . '_' . $now,
            'status' => 0,
        ];
        $query = "UPDATE users
                    SET facebook_id = '".$dataDeleted['facebook_id']."',
                        google_id = '".$dataDeleted['google_id']."',
                        email = '".$dataDeleted['email']."',
                        status = '".$dataDeleted['status']."'
                    WHERE users.id = '".$model->id."'
                    ";
        \DB::select($query);

        // delete customer setting
        $setting = $this->customerSettingRepository->findByAttributes(array('user_id' => $model->id));
        if(count($setting)) $setting->delete();

        // delete children detail
        $children = $this->customerChildrenRepository->findByAttributes(array('user_id' => $model->id));
        if(count($children)) $children->delete();

        // delete review
        $reviews = $this->reviewRepository->getByAttributes(array('user_id' => $model->id));
        if(count($reviews)) {
            $vendorIdArr = [];
            foreach($reviews as $item){
                if(!in_array($item->vendor_id, $vendorIdArr)){
                    $vendorIdArr[] = $item->vendor_id;
                }
                $item->delete();
            }
            
            foreach ($vendorIdArr as $key => $item) {
                $this->commentService->updateRatingPoint($item);
            }
        }

        // delete comment
        $comments = $this->commentRepository->getByAttributes(array('user_id' => $model->id));
        if(count($comments)) {
            foreach($comments as $item){
                $item->delete();
            }
        }

        return $model->delete();
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

    public function getItems($option = 'list', $request){
        $limit = $request->get('limit') ? $request->get('limit') : 10;
        $keyword = $request->get('keyword') ? $request->get('keyword') : "";

        $array_field = array('id', 'first_name', 'email', 'status', 'created_at','last_login');
        $order_field = $request->get('order_field') && in_array($request->get('order_field'), $array_field) ? $request->get('order_field') : 'id';
        $sort = $request->get('sort') ? $request->get('sort') : 'DESC';

        if($option == 'list'){
            $items = Customer::select('users.*')
                         ->join('role_users', 'role_users.user_id', '=', 'users.id')
                         ->where('role_users.role_id', Config('constant.user_role.customer'))
                         ->where(function($query) use ($keyword) {
                            // $query->where(DB::raw("CONCAT(`users`.`first_name`, ' ', `users`.`last_name`)"), 'like', '%'.$keyword.'%');
                            $query->where('users.first_name', 'like', '%'.$keyword.'%');
                            $query->orWhere('users.name', 'like', '%'.$keyword.'%');
                            $query->orWhere('users.email', 'like', '%' . $keyword . '%');
                            $query->orWhere('users.id', '=', $keyword);
                        })
                        ->orderBy($order_field, $sort)
                        ->paginate($limit);

            return $items;
        }else{
            return Customer::select('users.*')
                         ->join('role_users', 'role_users.user_id', '=', 'users.id')
                         ->where('role_users.role_id', Config('constant.user_role.customer'))
                         ->where(function($query) use ($keyword) {
                            $query->where('users.first_name', 'like', '%'.$keyword.'%');
                            $query->orWhere('users.name', 'like', '%'.$keyword.'%');
                            $query->orWhere('users.email', 'like', '%' . $keyword . '%');
                            $query->orWhere('users.id', '=', $keyword);
                        })
                         ->count();
        }
        
    }
}