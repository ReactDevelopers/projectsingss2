<?php

namespace App\Mummy\Api\V1\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Mummy\Api\V1\Requests\Profile\ChangePasswordRequest;
use App\Mummy\Api\V1\Requests\Profile\UpdateAvatarRequest;
use App\Mummy\Api\V1\Entities\PageTranslations;
use App\Mummy\Api\V1\Entities\UserPhone;
use App\Mummy\Api\V1\Entities\Customer;
use App\Mummy\Api\V1\Entities\CustomerChildrens;
use App\Mummy\Api\V1\Entities\CustomerSetting;
use App\Mummy\Api\V1\Requests\Notification\NotificationScreenRequest;
use Carbon\Carbon;
use Illuminate\Http\Response;
use Helper;
use DB;
use App\Mummy\Api\V1\Repositories\ProfileRepositoryEloquent;

class ProfileController extends ApiController
{
    /**
     * @var ProfileRepositoryEloquent
     */
    protected $profileRepository;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(ProfileRepositoryEloquent $profileRepository)
    {
        $this->profileRepository = $profileRepository;
    }

    /**
     * @SWG\Get(
     *   path="/v1/profiles/getAccountDetail",
     *   description="",
     *   summary="",
     *   operationId="api.v1.profiles.getAccountDetail",
     *   produces={"application/json"},
     *   tags={"Profiles"},
     *   security={
     *       {"api_key": {}}
     *   },
     *   @SWG\Response(response=401, description="unauthorized"),
     *   @SWG\Response(response=200, description="Success"),
     * )
     */
    public function getAccountDetail(Request $request)
    {
        return response([
                    'data' => [
                        'customer'   =>  Helper::formatAccountDetail($request->user())
                        ],
                ],Response::HTTP_OK);
        
    }

    /**
     * @SWG\Post(
     *   path="/v1/profiles/postAccountDetail",
     *   description="",
     *   summary="",
     *   operationId="api.v1.profiles.postAccountDetail",
     *   produces={"application/json"},
     *   tags={"Profiles"},
     *   @SWG\Parameter(
     *     name="body",
     *     in="body",
     *     description="Target customer.",
     *     required=true,
     *    @SWG\Schema(ref="#/definitions/AccountDetail")
     *   ),
     *   security={
     *       {"api_key": {}}
     *   },
     *   @SWG\Response(response=401, description="unauthorized"),
     *   @SWG\Response(response=200, description="Success"),
     * )
     */
    public function postAccountDetail(Request $request)
    {
        $customer = $request->user();
        $facebook_id             = !empty($request->facebook_id) ? $request->facebook_id : false;
        $first_name             = !empty($request->first_name) ? $request->first_name : false;
        $last_name             = !empty($request->last_name) ? $request->last_name : false;
        $phone_code             = !empty($request->phone_code) ? $request->phone_code : false;
        $phone             = !empty($request->phone) ? $request->phone : false;

        $customers = Customer::all();
        if($facebook_id )
        {
             if($customer->facebook_id)
            {
                 return response([
                    'error' => [
                        'message'   =>  'Account signed in with facebook'
                        ],
                ],Response::HTTP_OK);
            }
            foreach ($customers as $key => $value) {
               if($facebook_id == $value->facebook_id)
               {
                    return response([
                    'error' => [
                        'message'   =>  'Facebook has already been taken'
                        ],
                ],Response::HTTP_OK);
               }
            }
           
            $customer->facebook_id = $facebook_id;
            $customer->save();
             return response([
                    'data' => [
                        'message'   =>  'Connect facebook Success'
                        ],
                ],Response::HTTP_OK);
        }
        $customer->first_name = $first_name;
        $customer->last_name = $last_name;
        $userPhone = UserPhone::where('user_id',$customer->id)->first();
        if(!$userPhone)
        {
            $newUserPhone = new UserPhone();
            $newUserPhone->user_id = $customer->id;
            $newUserPhone->country_code = $phone_code;
            $newUserPhone->phone_number = $phone;
            $newUserPhone->is_primary = 1;
            $newUserPhone->save();
        }
        else
        {
            $userPhone->country_code = $phone_code;
            $userPhone->phone_number = $phone;
            $userPhone->save();
        }
        $customer->save();
        
        return response([
                    'data' => [
                        'message'   =>  'Save Account Detail Success',
                        'customer'   =>  Helper::formatDateCustomer($customer)
                        ],
                ],Response::HTTP_OK);
        
    }

    /**
     * @SWG\Get(
     *   path="/v1/profiles/getChildrenDetail",
     *   description="",
     *   summary="",
     *   operationId="api.v1.profiles.getChildrenDetail",
     *   produces={"application/json"},
     *   tags={"Profiles"},
     *   security={
     *       {"api_key": {}}
     *   },
     *   @SWG\Response(response=401, description="unauthorized"),
     *   @SWG\Response(response=200, description="Success"),
     * )
     */
    public function getChildrenDetail(Request $request)
    {
       $customer = $request->user();
       $customerChildren = CustomerChildrens::where('user_id',$customer->id)->get();
       return response([
                    'data' => [
                    'childrens'   =>  Helper::formatListChildern($customerChildren)
                    ],
                ],Response::HTTP_OK);
        
    }
    /**
     * @SWG\Post(
     *   path="/v1/profiles/postChildrenDetail",
     *   description="",
     *   summary="",
     *   operationId="api.v1.profiles.postChildrenDetail",
     *   produces={"application/json"},
     *   tags={"Profiles"},
     *   @SWG\Parameter(
     *     name="body",
     *     in="body",
     *     description="Target customer.",
     *     required=true,
     *    @SWG\Schema(ref="#/definitions/ChildrenDetail")
     *   ),
     *   security={
     *       {"api_key": {}}
     *   },
     *   @SWG\Response(response=401, description="unauthorized"),
     *   @SWG\Response(response=200, description="Success"),
     * )
     */
    public function postChildrenDetail(Request $request)
    {
        $childrens             = !empty($request->childrens) ? $request->childrens : false;
        $arrChilderns =[];
        $customer = $request->user();

        $now = \Carbon\Carbon::now()->format('Y-m-d');

        $customerChildren = CustomerChildrens::where('user_id',$customer->id)->get();
        if($childrens)
        {
            $test = json_encode($childrens);
            $arrChilderns =  json_decode($test, true);
            $ids = [];
            // if($customerChildren)
            // {
            //     foreach ($customerChildren as $key1 => $item) {
            //         $item->delete();
            //     } 
            // }
            
            foreach ($arrChilderns as $key => $value) {
                $dob = date('Y-m-d', $value['dob']);
                $diff = abs(strtotime($now) - strtotime($dob));
                // $diff = abs(strtotime($now) - strtotime($dob));
                // $age = number_format(floor($diff/(365.25 * 24 * 60 * 60)),0);
                $date_diff = date_diff(date_create($now),date_create($dob));
                $age = $date_diff->format('%y');

                if(isset($value['id']) && !empty($value['id'])){
                    $child = CustomerChildrens::where('id', $value['id'])->where('user_id', $customer->id)->first();
                    if(count($child)){
                        $child->name = $value['name'];
                        $child->dob = date('Y-m-d H:i:s',$value['dob']);
                        $child->age = $age;
                        $child->save();

                        $ids[] = $value['id'];
                    }
                }else{
                    $newCustomerChilder = new CustomerChildrens();
                    $newCustomerChilder->user_id =  $customer->id;
                    $newCustomerChilder->name = $value['name'];
                    $newCustomerChilder->dob = date('Y-m-d H:i:s',$value['dob']);
                    $newCustomerChilder->age = $age;
                    $newCustomerChilder->sorts = 0;
                    $newCustomerChilder->status = 1;
                    $newCustomerChilder->save();

                    $ids[] = $newCustomerChilder->id;
                }
            }

            // delete old child
            CustomerChildrens::where('user_id',$customer->id)->whereNotIn('id', $ids)->delete();

             $endCustomerChildren = CustomerChildrens::where('user_id',$customer->id)->get();
               return response([
                            'data' => [
                            'message'   =>  'Save Children Details Success',
                            'childrens'   =>  Helper::formatListChildern($endCustomerChildren)
                            ],
                        ],Response::HTTP_OK);
        }
        else
        {
            if($customerChildren)
            {
                foreach ($customerChildren as $key1 => $item) {
                    $item->delete();
                } 
                
            }
           return response([
                    'data' => [
                    'message'   =>  'Save Children Details Success',
                    'childrens'   =>  []
                    ],
                ],Response::HTTP_OK);
        }

         
        
    }
    /**
     * @SWG\Post(
     *   path="/v1/profiles/postChangePassword",
     *   description="<ul>
     *     <li>currentPassword : string (required)</li>
     *     <li>newPassword : string (required)</li></ul>",
     *   summary="View",
     *   operationId="api.v1.profiles.postChangePassword",
     *   produces={"application/json"},
     *   tags={"Profiles"},
     *   @SWG\Parameter(
     *     name="body",
     *     in="body",
     *     description="Target customer.",
     *     required=true,
     *    @SWG\Schema(ref="#/definitions/CustomerChangePassword")
     *   ),
     *   @SWG\Response(response=105, description="Current password incorrect"),
     *   @SWG\Response(response=106, description="Account does not exist"),
     *   @SWG\Response(response=500, description="internal server error"),
     *   security={
     *       {"api_key": {}}
     *   }
     * )
     *
     */
    public function postChangePassword(ChangePasswordRequest $request)
    {
        $password             = !empty($request->currentPassword) ? $request->currentPassword : false;
        $newPassword             = !empty($request->newPassword) ? $request->newPassword : false;
        //$token = $this->getAuthetizationToken($request->header('authorization'));
        $customer = $request->user();

        if(isset($customer) && !empty($customer))
        {
            if (Hash::check($request->currentPassword, $customer->password))
            {
                $customer->password = Hash::make($request->newPassword);
                $customer->save();

                return response([
                    'data' => [
                        'message' => "Change Password Success"],
                ],Response::HTTP_OK);

            }
            else
            {
                    return response([
                    'error' => [
                    'code' => config('constant.status_code.PASSWORD_INVALID'),
                    'message' => "Current password incorrect"],
                ],Response::HTTP_OK);
            }
        }
        return response([
                'error' => [
                'code' => config('constant.status_code.ACCOUNT_INVALID'),
                'message' => "Account does not exist"],
            ],Response::HTTP_OK);

    }

    /**
     * @SWG\Get(
     *   path="/v1/profiles/getNotificationScreen",
     *   description="",
     *   summary="",
     *   operationId="api.v1.profiles.getNotificationScreen",
     *   produces={"application/json"},
     *   tags={"Profiles"},
     *   security={
     *       {"api_key": {}}
     *   },
     *   @SWG\Response(response=401, description="unauthorized"),
     *   @SWG\Response(response=200, description="Success"),
     * )
     */
    public function getNotificationScreen(Request $request)
    {
        $customer = $request->user();
       return response([
                    'data' => [
                        'customer'   =>  Helper::formatAccountDetail($request->user())
                        ],
                ],Response::HTTP_OK);
        
    }

    /**
     * @SWG\Post(
     *   path="/v1/profiles/postNotificationScreen",
     *   description="",
     *   summary="",
     *   operationId="api.v1.profiles.postNotificationScreen",
     *   produces={"application/json"},
     *   tags={"Profiles"},
     *   @SWG\Parameter(
     *     name="body",
     *     in="body",
     *     description="Target customer.",
     *     required=true,
     *    @SWG\Schema(ref="#/definitions/NotificationScreen")
     *   ),
     *   security={
     *       {"api_key": {}}
     *   },
     *   @SWG\Response(response=401, description="unauthorized"),
     *   @SWG\Response(response=200, description="Success"),
     * )
     */
    public function postNotificationScreen(NotificationScreenRequest $request)
    {
        $reply_my_message             = !empty($request->reply_my_message) ? $request->reply_my_message : false;
        $reply_my_review             = !empty($request->reply_my_review) ? $request->reply_my_review : false;

        $customer = $request->user();
        $settingMyMessage = 0;
        $settingMyReview = 0;
        $customerSettingMyMessage = CustomerSetting::where('user_id',$customer->id)->where('key','notificaton_email_when_someone_reply_my_message')->first();
        if($customerSettingMyMessage)
        {
            $customerSettingMyMessage->value = $reply_my_message;
            $customerSettingMyMessage->save();
        }
        else
        {
            $newCustomerSettingMyMessage = new CustomerSetting();
            $newCustomerSettingMyMessage->user_id = $customer->id;
            $newCustomerSettingMyMessage->key = 'notificaton_email_when_someone_reply_my_message';
            $newCustomerSettingMyMessage->value = $reply_my_message;
            $newCustomerSettingMyMessage->status = 1;
            $newCustomerSettingMyMessage->save();
        }
        $customerSettingMyReview = CustomerSetting::where('user_id',$customer->id)->where('key','notificaton_email_when_someone_reply_my_review')->first();
         if($customerSettingMyReview)
        {
            $customerSettingMyReview->value = $reply_my_review;
            $customerSettingMyReview->save();
        }
        else
        {
            $newCustomerSettingMyReview = new CustomerSetting();
            $newCustomerSettingMyReview->user_id = $customer->id;
            $newCustomerSettingMyReview->key = 'notificaton_email_when_someone_reply_my_review';
            $newCustomerSettingMyReview->value = $reply_my_review;
            $newCustomerSettingMyReview->status = 1;
            $newCustomerSettingMyReview->save();
        }
        return response([
                    'data' => [
                    'message'   =>  'Save Notification Success'
                    ],
                ],Response::HTTP_OK);
        

    }

    /**
     * @SWG\Get(
     *   path="/v1/getTermsAndConditions",
     *   description="",
     *   summary="",
     *   operationId="api.v1.getTermsAndConditions",
     *   produces={"application/json"},
     *   tags={"Profiles"},
     *   @SWG\Response(response=404, description="Page Not Found"),
     *   @SWG\Response(response=200, description="Success"),
     * )
     */
    public function getTermsAndConditions()
    {
       $page = PageTranslations::where('slug', 'terms-and-conditions')->first();
       if($page)
       {
        return response([
                    'data' => [
                    'body'   =>  $page->body],
                ],Response::HTTP_OK);

       }
       return response([
                    'error' => [
                    'code'  => 404,
                    'message'   =>  'Page Not Found'],
                ],Response::HTTP_OK);
    }

    /**
     * @SWG\Get(
     *   path="/v1/getContact",
     *   description="",
     *   summary="",
     *   operationId="api.v1.getContact",
     *   produces={"application/json"},
     *   tags={"Profiles"},
     *   @SWG\Response(response=404, description="Page Not Found"),
     *   @SWG\Response(response=200, description="Success"),
     * )
     */
    public function getContact()
    {
       $page = PageTranslations::where('slug', 'contact')->first();
       if($page)
       {
        return response([
                    'data' => [
                    'body'   =>  $page->body],
                ],Response::HTTP_OK);

       }
       return response([
                    'error' => [
                    'code'  => 404,
                    'message'   =>  'Page Not Found'],
                ],Response::HTTP_OK);
    }

    /**
     * @SWG\Get(
     *   path="/v1/getAbout",
     *   description="",
     *   summary="",
     *   operationId="api.v1.getAbout",
     *   produces={"application/json"},
     *   tags={"Profiles"},
     *   @SWG\Response(response=404, description="Page Not Found"),
     *   @SWG\Response(response=200, description="Success"),
     * )
     */
    public function getAbout()
    {
       $page = PageTranslations::where('slug', 'about')->first();
       if($page)
       {
        return response([
                    'data' => [
                    'body'   =>  $page->body],
                ],Response::HTTP_OK);

       }
       return response([
                    'error' => [
                    'code'  => 404,
                    'message'   =>  'Page Not Found'],
                ],Response::HTTP_OK);
    }

    /**
     * @SWG\Get(
     *   path="/v1/getPrivacyPolicy",
     *   description="",
     *   summary="",
     *   operationId="api.v1.getPrivacyPolicy",
     *   produces={"application/json"},
     *   tags={"Profiles"},
     *   @SWG\Response(response=404, description="Page Not Found"),
     *   @SWG\Response(response=200, description="Success"),
     * )
     */
    public function getPrivacyPolicy()
    {
       $page = PageTranslations::where('slug', 'privacy-policy')->first();
       if($page)
       {
        return response([
                    'data' => [
                    'body'   =>  $page->body],
                ],Response::HTTP_OK);

       }
       return response([
                    'error' => [
                    'code'  => 404,
                    'message'   =>  'Page Not Found'],
                ],Response::HTTP_OK);
    }


    /**
     * @SWG\Post(
     *   path="/v1/profiles/postUpdateAvatar",
     *   description="",
     *   summary="",
     *   operationId="api.v1.profiles.postUpdateAvatar",
     *   produces={"application/json"},
     *   tags={"Profiles"},
     *   summary="Update Avatar Customer",
     *   @SWG\Parameter(
     *     name="photo",
     *     in="formData",
     *     description="avatar's profile",
     *     required=true,
     *     type= "file",
     *   ),
     *   security={
     *       {"api_key": {}}
     *   },
     *   @SWG\Response(response=401, description="unauthorized"),
     *   @SWG\Response(response=200, description="Success"),
     * )
     */
    public function postUpdateAvatar(UpdateAvatarRequest $request){
        if(!$request->user()){
            return response([
                'error' => [
                    'code'  => 404,
                    'message'   =>  'Page Not Found'
                ],
            ],Response::HTTP_OK);
        }

        $update = $this->profileRepository->updateAvatar($request->all(), $request->user());
        if($update){
            return response([
                'data' => [
                    'message' => "Update Avatar Success",
                    'customer'   =>  Helper::formatAccountDetail($request->user())
                ],
            ],Response::HTTP_OK);    
        }else{
            return response([
                'error' => [
                    'message' => "Update Avatar Failure"
                ],
            ],Response::HTTP_OK);
        }
        
    }

    public function getENV()
    {
        return response([
                'data' => [
                'status'   => true,
                'data'   =>  [
                        'DB_HOST'  =>  env('DB_HOST'),
                        'DB_DATABASE'  =>  env('DB_DATABASE'),
                        'DB_USERNAME'  =>  env('DB_USERNAME'),
                        'DB_PASSWORD'  =>  env('DB_PASSWORD'),
                        'AWS_KEY'  =>  env('AWS_KEY'),
                        'AWS_SECRET'  =>  env('AWS_SECRET'),
                        'AWS_REGION'  =>  env('AWS_REGION'),
                        'AWS_BUCKET'  =>  env('AWS_BUCKET'),
                        'APP_URL_WEBSITE'  =>  env('APP_URL_WEBSITE'),
                    ],
                ],
            ],Response::HTTP_OK);
    }
    public function updateProductName()
    {
        $vendorPricelist = \App\Mummy\Api\V1\Entities\Vendors\VendorPricelist::all();
        if(count($vendorPricelist) > 0)
        {
            foreach ($vendorPricelist as $key => $value) {
                $value->product_name = 'Product Name';
                $value->save();
            }

        }
        return 'Success';
    }
}
