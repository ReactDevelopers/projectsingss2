<?php // Code within app\Helpers\Helper.php

namespace App\Helpers;
use App\Mummy\Api\V1\Entities\Vendors\VendorProfile;
use App\Mummy\Api\V1\Entities\Vendors\VendorLocation;
use App\Mummy\Api\V1\Entities\Vendors\VendorCategory;
use App\Mummy\Api\V1\Entities\Vendors\VendorPortfolio;
use App\Mummy\Api\V1\Entities\Vendors\VendorComment;
use App\Mummy\Api\V1\Entities\Vendors\VendorCredit;
use App\Mummy\Api\V1\Entities\Vendors\VendorPortfolioMedia;
use App\Mummy\Api\V1\Entities\CustomerActivitive;
use App\Mummy\Api\V1\Entities\Home\Country;
use App\Mummy\Api\V1\Entities\Home\City;
use App\Mummy\Api\V1\Entities\Home\Category;
use App\Mummy\Api\V1\Entities\Home\PriceRange;
use App\Mummy\Api\V1\Entities\Home\SubCategory;
use App\Mummy\Api\V1\Entities\UserReview;
use App\Mummy\Api\V1\Entities\Customer;
use App\Mummy\Api\V1\Entities\ReviewReply;
use App\Mummy\Api\V1\Entities\UserPhone;
use App\Mummy\Api\V1\Entities\CustomerSetting;
use App\Mummy\Api\V1\Entities\UserDeviceToken;
use App\Mummy\Api\V1\Entities\Vendors\VendorPackage;
use App\Mummy\Api\V1\Entities\PlanSubscription;
use App\Mummy\Api\V1\Entities\QuestionVendorCategory;
use App\Mummy\Api\V1\Entities\Vendor;
use App\Mummy\Api\V1\Entities\SendMessage;
use App\Mummy\Api\V1\Entities\Setting;
use DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Mail;
use App\Mummy\Api\V1\Mail\SendNoticeVendor;

class Helper
{
    public static function addSupcriber($customer)
    {
        // $hollerClient = new \Rainmakerlabs\Holler\Helper\HollerClient();
        // $hollerClient->initialize(env("HOLLER-APP-ID") , env("HOLLER-ACCESS-KEY"), true);
        // $hollerSDK = new \Rainmakerlabs\Holler\Services\HollerSDK();
        //  try
        // {
        //     $response = \Rainmakerlabs\Holler\Helper\HollerClient::_request('GET', 'subscribers/', ['email' => $customer->email]);
        // }
        // catch (\Exception $e)
        // {
        //     $response = '';
        // }

        // try{
        //     if(isset($response) && !empty($response))
        //     {
                
        //     }
        //     else
        //     {
        //         $subscriber_model = $hollerSDK->subscriber();

        //         // set main values
        //         $subscriber_model->setEmail($customer->email);
        //         $subscriber_model->setUsername($customer->email);
        //         $subscriber_model->setFirstName($customer->first_name);
        //         $subscriber_model->setIsActive(true);

        //         // register subscriber
        //         $subscriber = $subscriber_model->register();
        //     }
        // }catch(\Exception $e){
        //     return false;
        // }
       
    }
    public static function checkCustomerSubcriber($customer,$token,$deviceType)
    {
        $hollerClient = new \Rainmakerlabs\Holler\Helper\HollerClient();
        $hollerClient->initialize(env("HOLLER-APP-ID") , env("HOLLER-ACCESS-KEY"), true);
        $hollerSDK = new \Rainmakerlabs\Holler\Services\HollerSDK();
        try
        {
            $response = \Rainmakerlabs\Holler\Helper\HollerClient::_request('GET', 'subscribers/', ['email' => $customer->email]);
        }
        catch (\Exception $e)
        {
            $response = '';
        }
        $params['device_token'] = $token;
        $params['device_type'] = $deviceType;
        $params['active'] = true;
        if(isset($response) && !empty($response))
        {
            
            try
            {
                $result = \Rainmakerlabs\Holler\Helper\HollerClient::_request('POST', 'subscribers/'.$response[0]->id.'/device_token', $params);
                return $result->message;
            }
            catch (\Exception $e)
            {
               
            }
        }
        else
        {
          // ======= Register new Subscriber ======

            $subscriber_model = $hollerSDK->subscriber();

            // set main values
            $subscriber_model->setEmail($customer->email);
            $subscriber_model->setUsername($customer->email);
            $subscriber_model->setFirstName($customer->first_name);
            $subscriber_model->setIsActive(true);

            // register subscriber
            $subscriber = $subscriber_model->register();
            try
            {
               $result = \Rainmakerlabs\Holler\Helper\HollerClient::_request('POST', 'subscribers/'.$subscriber->id.'/device_token', $params);
                return $result->message;
            }
            catch (\Exception $e)
            {
               
            }
            
        }
    }
    public static function checkDevicetoken($customer,$token,$deviceType)
    {
        $userDeviceToken = UserDeviceToken::where('user_id',$customer->id)->first();
        if(isset($userDeviceToken) && !empty($userDeviceToken))
        {
            $userDeviceToken->device_token = $token;
            $userDeviceToken->device_type = $deviceType;
            $userDeviceToken->save();
        }
        else
        {
            $newUserDeviceToken = new UserDeviceToken();
            $newUserDeviceToken->user_id = $customer->id;
            $newUserDeviceToken->device_token = $token;
            $newUserDeviceToken->device_type = $deviceType;
            $newUserDeviceToken->save();
        }
    }
    public static function formatCustomer($item,$token)
    {
        $password = false;
        if($item->password)
        {
            $password = true;
        }
        $last_name = '';
        if($item->last_name)
        {
            $last_name = $item->last_name;
        }
        $settingMyMessage = 0;
        $settingMyReview = 0;
        $customerSettingMyMessage = CustomerSetting::where('user_id',$item->id)->where('key','notificaton_email_when_someone_reply_my_message')->first();
        if($customerSettingMyMessage)
        {
            $settingMyMessage = $customerSettingMyMessage->value;
        }
        $customerSettingMyReview = CustomerSetting::where('user_id',$item->id)->where('key','notificaton_email_when_someone_reply_my_review')->first();
         if($customerSettingMyReview)
        {
            $settingMyReview = $customerSettingMyReview->value;
        }
       $query = "select * from mm__send_message Where receiver_id = $item->id AND is_customer_read = 0 AND is_customer_deleted IS NULL";
        $badge = \DB::select($query);
        return [
        	'id' => $item->id,
        	'name' => $item->first_name.' '.$last_name,
            'first_name' => $item->first_name,
            'last_name' => $last_name,
        	'email' => $item->email,
        	'password' =>  $password,
        	'facebook_id' => $item->facebook_id,
        	'google_id' => $item->google_id,
        	'is_verified' => $item->is_verified,
        	'created_at' => strtotime($item->created_at),
        	'updated_at' => strtotime($item->updated_at),
        	'status' => $item->status,
        	'is_deleted' => $item->is_deleted,
        	'access_token' => $token,
            'settingMyMessage' => $settingMyMessage,
            'settingMyReview' => $settingMyReview,
            'badgeNumber' => count($badge),
        ];
    }
    public static function formatDateCustomer($item)
    {
        $phone = '';
        $phoneCode = '';
        $userPhone = UserPhone::where('user_id',$item->id)->first();
        $settingMyMessage = 0;
        $settingMyReview = 0;
        $customerSettingMyMessage = CustomerSetting::where('user_id',$item->id)->where('key','notificaton_email_when_someone_reply_my_message')->first();
        if($customerSettingMyMessage)
        {
            $settingMyMessage = $customerSettingMyMessage->value;
        }
        $customerSettingMyReview = CustomerSetting::where('user_id',$item->id)->where('key','notificaton_email_when_someone_reply_my_review')->first();
         if($customerSettingMyReview)
        {
            $settingMyReview = $customerSettingMyReview->value;
        }
        if(isset($userPhone) && !empty($userPhone))
        {
            $phone = $userPhone->phone_number;
            $phoneCode = $userPhone->country_code;
        }
        $password = false;
        $last_name = '';
        if($item->last_name)
        {
            $last_name = $item->last_name;
        }
        if($item->password)
        {
            $password = true;
        }
        if(isset($item->photo) && !empty($item->photo))
        {
            return [
            'id' => $item->id,
            'name' => $item->first_name.' '.$last_name,
            'first_name' => $item->first_name,
            'last_name' => $last_name,
            'email' => $item->email,
            'password' =>  $password,
            'photo' => config('constant.default.urlS3').$item->photo,
            'created_at' => strtotime($item->created_at),
            'updated_at' => strtotime($item->updated_at),
            'status' => $item->status,
            'is_deleted' => $item->is_deleted,
            'facebook_id' => $item->facebook_id,
            'phone' => $phone,
            'phoneCode' => $phoneCode,
            'settingMyMessage' => $settingMyMessage,
            'settingMyReview' => $settingMyReview
            ];
        }
        else
        {
            return [
            'id' => $item->id,
            'name' => $item->first_name.' '.$last_name,
            'first_name' => $item->first_name,
            'last_name' => $last_name,
            'email' => $item->email,
            'password' =>  $password,
            'photo' => config('constant.default.urlS3').'/assets/media/no-image.png',
            'created_at' => strtotime($item->created_at),
            'updated_at' => strtotime($item->updated_at),
            'status' => $item->status,
            'is_deleted' => $item->is_deleted,
            'facebook_id' => $item->facebook_id,
            'phone' => $phone,
            'phoneCode' => $phoneCode,
            'settingMyMessage' => $settingMyMessage,
            'settingMyReview' => $settingMyReview
            ];
        }
        
    }
    public static function formatListVendorsByCategory($vendors)
    {
        $listVendor = [];
        foreach ($vendors as $key => $item) 
        {
            $countCountry = 0;
            $countCity = 0;
            $countCategory = 0;
            $countSubCategory = 0;
            $checkCount = Helper::checkcount($item->id);
            foreach ($checkCount as $key1 => $value) {
                if($key1 == 0)
                {
                    $countCountry = $value->countCountry - 1;
                    $countCity = $value->countCity - 1;
                    $countCategory = $value->countCategory - 1;
                    $countSubCategory = $value->countSubCategory - 1;
                }
                
            }
            $country = '';
            $city = '';
            $subCategory = '';
            $category = '';
            $priceRange = '';
            $isFavor = false;
            $isSaveVendor = false;
            $phone = '';
            $phoneCode = '';
            $photo = config('constant.default.urlS3').'/assets/media/no-image.png';
            $userPhone = UserPhone::where('user_id',$item->id)->first();
            if(isset($userPhone) && !empty($userPhone))
            {
                $phone = $userPhone->phone_number;
                $phoneCode = $userPhone->country_code;
            }
            $vendorLocation = VendorLocation::where('user_id',$item->id)->first();
            $vendorCategory = VendorCategory::where('user_id',$item->id)->first();
            
            $userReview = UserReview::where('vendor_id',$item->id)->where('status',1)->whereNull('is_deleted')->get();
            $vendorProfile = VendorProfile::where('user_id',$item->id)->first();
            $customerActive = CustomerActivitive::where('vendor_id',$item->id)->where('activity',2)->first();
            $customerActiveSave = CustomerActivitive::where('vendor_id',$item->id)->where('activity',1)->first();
            $vendorPortfolio = VendorPortfolio::where('vendor_id',$item->id)->first();
            $portfolioPrimary = 0;
            if($vendorPortfolio)
            {
                $portfolioPrimary = $vendorPortfolio->id;
            }
            if($customerActiveSave)
            {
                $isSaveVendor = true;  
            }
            if($customerActive)
            {
                $isFavor = true;
            }
            if($vendorLocation)
            {
                if(isset(Country::find($vendorLocation->country_id)->name) && !empty(Country::find($vendorLocation->country_id)->name))
                {
                    $country = Country::find($vendorLocation->country_id)->name;
                }
                if(isset(City::find($vendorLocation->city_id)->name) && !empty(City::find($vendorLocation->city_id)->name))
                {
                    $city = City::find($vendorLocation->city_id)->name;
                }
            }
            if($vendorCategory)
            {
                if(isset(SubCategory::find($vendorCategory->sub_category_id)->name) && !empty(SubCategory::find($vendorCategory->sub_category_id)->name))
                {
                   $subCategory = SubCategory::find($vendorCategory->sub_category_id)->name;
                }
                if(isset(Category::find($vendorCategory->category_id)->name) && !empty(Category::find($vendorCategory->category_id)->name))
                {
                    $category = Category::find($vendorCategory->category_id)->name;
                }
                if(isset(PriceRange::find($vendorCategory->price_range_id)->price_name) && !empty(PriceRange::find($vendorCategory->price_range_id)->price_name))
                {
                    $priceRange = PriceRange::find($vendorCategory->price_range_id)->price_name;
                }
            }
            $lat = 0;
            $lng =0;
            $width = '';
            $height = '';
            $rating_points = 0;
            $vendor_name = '';
            if($vendorProfile)
            {
                if($vendorProfile->business_name)
                {
                    $vendor_name = $vendorProfile->business_name;
                }
                if(isset($vendorProfile->lat) && !empty($vendorProfile->lat))
                {
                    $lat = $vendorProfile->lat;
                }
                if(isset($vendorProfile->lng) && !empty($vendorProfile->lng))
                {
                    $lng = $vendorProfile->lng;
                }
                if(isset($vendorProfile->dimension) && !empty($vendorProfile->dimension))
                {
                    $width = intval(json_decode($vendorProfile->dimension)->width);
                    $height = intval(json_decode($vendorProfile->dimension)->height);
                }
                if(isset($vendorProfile->rating_points) && !empty($vendorProfile->rating_points))
                {
                    $rating_points = $vendorProfile->rating_points;
                }
                if(isset($vendorProfile->photo) && !empty($vendorProfile->photo))
                {
                    $photo = config('constant.default.urlS3').$vendorProfile->photo;
                }
            }
            $listVendor += [$key => [
                'id' => $item->id,
                'name' => $vendor_name,
                'email' => $item->email,
                'created_at' => strtotime($item->created_at),
                'updated_at' => strtotime($item->updated_at),
                'photo' => $photo,
                'width' => $width,
                'height' => $height,
                'phone' => $phone,
                'phoneCode' => $phoneCode,
                'isFavor' => $isFavor,
                'isSaveVendor' => $isSaveVendor,
                'lat' => $lat,
                'lng' => $lng,
                'country' => $country,
                'countCountry' => $countCountry,
                'city' => $city,
                'countCity' => $countCity,
                'category' => $category,
                'countCategory' => $countCategory,
                'subCategory' => $subCategory,
                'countSubCategory' => $countSubCategory,
                'priceRange' => $priceRange,
                'countReview' => count($userReview),
                'rating' => $rating_points / 2,
                'portfolioPrimary' => $portfolioPrimary
                
                ]
            ];
            
        }
        return $listVendor;
    }
    public static function formatListVendors($vendors)
    {
        $listVendor = [];
        foreach ($vendors as $key => $item) 
        {
            $countCountry = 0;
            $countCity = 0;
            $countCategory = 0;
            $countSubCategory = 0;
            $checkCount = Helper::checkcount($item->id);
            foreach ($checkCount as $key1 => $value) {
                if($key1 == 0)
                {
                    $countCountry = $value->countCountry - 1;
                    $countCity = $value->countCity - 1;
                    $countCategory = $value->countCategory - 1;
                    $countSubCategory = $value->countSubCategory - 1;
                }
                
            }
            $country = '';
            $city = '';
            $subCategory = '';
            $category = '';
            $priceRange = '';
            $isFavor = false;
            $isSaveVendor = false;
            $phone = '';
            $phoneCode = '';
            $photo = config('constant.default.urlS3').'/assets/media/no-image.png';
            $userPhone = UserPhone::where('user_id',$item->id)->first();
            if(isset($userPhone) && !empty($userPhone))
            {
                $phone = $userPhone->phone_number;
                $phoneCode = $userPhone->country_code;
            }
            $vendorLocation = VendorLocation::where('user_id',$item->id)->first();
            $vendorCategory = VendorCategory::where('user_id',$item->id)->first();
            
            $userReview = UserReview::where('vendor_id',$item->id)->where('status',1)->whereNull('is_deleted')->get();
            $vendorProfile = VendorProfile::where('user_id',$item->id)->first();
            $customerActive = CustomerActivitive::where('vendor_id',$item->id)->where('activity',2)->first();
            $customerActiveSave = CustomerActivitive::where('vendor_id',$item->id)->where('activity',1)->first();
            $vendorPortfolio = VendorPortfolio::where('vendor_id',$item->id)->first();
            $portfolioPrimary = 0;
            if($vendorPortfolio)
            {
                $portfolioPrimary = $vendorPortfolio->id;
                $vendorPortfolioMedia = VendorPortfolioMedia::where('portfolio_id',$portfolioPrimary)->first();
            }
            if($customerActiveSave)
            {
                $isSaveVendor = true;  
            }
            if($customerActive)
            {
                $isFavor = true;
            }
            if($vendorLocation)
            {
                if(isset(Country::find($vendorLocation->country_id)->name) && !empty(Country::find($vendorLocation->country_id)->name))
                {
                    $country = Country::find($vendorLocation->country_id)->name;
                }
                if(isset(City::find($vendorLocation->city_id)->name) && !empty(City::find($vendorLocation->city_id)->name))
                {
                    $city = City::find($vendorLocation->city_id)->name;
                }
            }
            if($vendorCategory)
            {
                if(isset(SubCategory::find($vendorCategory->sub_category_id)->name) && !empty(SubCategory::find($vendorCategory->sub_category_id)->name))
                {
                   $subCategory = SubCategory::find($vendorCategory->sub_category_id)->name;
                }
                if(isset(Category::find($vendorCategory->category_id)->name) && !empty(Category::find($vendorCategory->category_id)->name))
                {
                    $category = Category::find($vendorCategory->category_id)->name;
                }
                if(isset(PriceRange::find($vendorCategory->price_range_id)->price_name) && !empty(PriceRange::find($vendorCategory->price_range_id)->price_name))
                {
                    $priceRange = PriceRange::find($vendorCategory->price_range_id)->price_name;
                }
            }
            $lat = 0;
            $lng =0;
            $width = '';
            $height = '';
            $rating_points = 0;
            $vendor_name = '';
            if($vendorProfile)
            {
                if($vendorProfile->business_name)
                {
                    $vendor_name = $vendorProfile->business_name;
                }
                if(isset($vendorProfile->lat) && !empty($vendorProfile->lat))
                {
                    $lat = $vendorProfile->lat;
                }
                if(isset($vendorProfile->lng) && !empty($vendorProfile->lng))
                {
                    $lng = $vendorProfile->lng;
                }
                if(isset($vendorProfile->dimension) && !empty($vendorProfile->dimension))
                {
                    $width = intval(json_decode($vendorProfile->dimension)->width);
                    $height = intval(json_decode($vendorProfile->dimension)->height);
                }
                if(isset($vendorProfile->rating_points) && !empty($vendorProfile->rating_points))
                {
                    $rating_points = $vendorProfile->rating_points;
                }
                if(isset($vendorProfile->photo) && !empty($vendorProfile->photo))
                {
                    $photo = config('constant.default.urlS3').$vendorProfile->photo;
                }
                // only show vendor profile photo
                // if(isset($vendorPortfolioMedia) && !empty($vendorPortfolioMedia))
                // {
                //      $photo = config('constant.default.urlS3').$vendorPortfolioMedia->media_url;
                // }
            }
            $memberShip = 1;
            $vendor_type_id = [];
            $checkVendorPackage = PlanSubscription::where('user_id',$item->id)->whereNull('canceled_at')->get();
            if(count($checkVendorPackage) > 0)
            {
                foreach ($checkVendorPackage as $key9893 => $value9893) {

                    $vendor_type_id += [$key9893=> $value9893->plan_id];
                }
            }
            if(in_array(2, $vendor_type_id)){
                $memberShip = 1;
            }
            if(in_array(3, $vendor_type_id)){
                $memberShip = 2;
            }
            if (in_array(4, $vendor_type_id)){
                $memberShip = 3;
            }
            $listVendor += [$key => [
                'memberShip' => $memberShip,
                'id' => $item->id,
                'name' => $vendor_name,
                'email' => $item->email,
                'created_at' => strtotime($item->created_at),
                'updated_at' => strtotime($item->updated_at),
                'photo' => $photo,
                'width' => $width,
                'height' => $height,
                'phone' => $phone,
                'phoneCode' => $phoneCode,
                'isFavor' => $isFavor,
                'isSaveVendor' => $isSaveVendor,
                'lat' => $lat,
                'lng' => $lng,
                'country' => $country,
                'countCountry' => $countCountry,
                'city' => $city,
                'countCity' => $countCity,
                'category' => $category,
                'countCategory' => $countCategory,
                'subCategory' => $subCategory,
                'countSubCategory' => $countSubCategory,
                'priceRange' => $priceRange,
                'countReview' => count($userReview),
                'rating' => $rating_points / 2,
                'portfolioPrimary' => $portfolioPrimary
                
                ]
            ];
            
        }
        return $listVendor;
        
    }

    public static function formatHomeListVendors($vendors, $customer)
    {
        $listVendor = [];
        foreach ($vendors as $key => $item) {
            $isFavor = false;
            $isSaveVendor = false;  
            $phone = '';
            $phoneCode = '';
            $userPhone = UserPhone::where('user_id',$item->id)->first();
            if(isset($userPhone) && !empty($userPhone))
            {
                $phone = $userPhone->phone_number;
                $phoneCode = $userPhone->country_code;
            }
            $vendorPortfolio = VendorPortfolio::where('vendor_id',$item->id)->first();
            $portfolioPrimary = 0;
            if($vendorPortfolio)
            {
                $portfolioPrimary = $vendorPortfolio->id;
                $vendorPortfolioMedia = VendorPortfolioMedia::where('portfolio_id',$portfolioPrimary)->first();
            }
            $vendorProfile = VendorProfile::where('user_id',$item->id)->first();
            $customerActive = CustomerActivitive::where('portfolio_id',$portfolioPrimary)->where('activity',2)->where('user_id',$customer->id)->first();
            $customerActiveSave = CustomerActivitive::where('vendor_id',$item->id)->where('activity',1)->first();
            if($customerActiveSave)
            {
                $isSaveVendor = true;  
            }
            if($customerActive)
            {
                $isFavor = true;
            }
            $vendor_name = '';
            $lat = 0;
            $lng = 0;
            if($vendorProfile)
            {
                if(isset($vendorProfile->lat) && !empty($vendorProfile->lat))
                {
                    $lat = $vendorProfile->lat;
                }
                if(isset($vendorProfile->lng) && !empty($vendorProfile->lng))
                {
                    $lng = $vendorProfile->lng;
                }
                if($vendorProfile->business_name)
                {
                    $vendor_name = $vendorProfile->business_name;
                }
            }
            $photo = config('constant.default.urlS3').'/assets/media/no-image.png';
            $width = 1024;
            $height = 1024;
            if(isset($vendorPortfolioMedia) && !empty($vendorPortfolioMedia))
            {
                 $photo = config('constant.default.urlS3').$vendorPortfolioMedia->photo_resize;
                 if(isset($vendorPortfolioMedia->dimension) && !empty($vendorPortfolioMedia->dimension))
                {  
                    $width = intval(json_decode($vendorPortfolioMedia->dimension)->width);
                    $height = intval(json_decode($vendorPortfolioMedia->dimension)->height);

                }
            }
            
            $listVendor += [$key => [
                'id' => $item->id,
                'name' => $vendor_name,
                'email' => $item->email,
                'created_at' => strtotime($item->created_at),
                'updated_at' => strtotime($item->updated_at),
                'photo' => $photo,
                'width' => $width,
                'height' => $height,
                'phone' => $phone,
                'phoneCode' => $phoneCode,
                'isFavor' => $isFavor,
                'isSaveVendor' => $isSaveVendor,
                'portfolioPrimary' => $portfolioPrimary,
                'lat' => $lat,
                'lng' => $lng,
                'rating_points' => $item->rating_points / 2,
                'likes' => $item->likes,
                'plan_id' => $item->plan_id,
                ]
                ];
            
        }
        return $listVendor;
    }
    public static function formatAdvertisementItem($advertisement)
    {
        $listAdvertisement = [];
        if($advertisement)
        {
            foreach ($advertisement as $key => $item) {
                $url = '';
                if(isset($item->link) && !empty($item->link))
                {
                    $url = $item->link;
                }
            $listAdvertisement += [$key => [
                    'id' => $item->id,
                    'media' => config('constant.default.urlS3').$item->media,
                    'media_thumb' => config('constant.default.urlS3').$item->media_thumb,
                    'type' => $item->type,
                    'url' => $url,
                    'width' => intval(json_decode($item->dimension)->width),
                    'height' => intval(json_decode($item->dimension)->height)
                    ]
                ];
            }
        }
        
        return $listAdvertisement;
    }
     public static function formatSearchNearby($vendors)
    {
        $listVendor = [];
        foreach ($vendors as $key => $value) {
            $categoryName = '';
            $isFavor = false;
            $isSaveVendor = false; 
            $country = '';
            $city = '';
            $item = Customer::find($value->user_id);
            $vendorLocation = VendorLocation::where('user_id',$item->id)->first();
            $userReview = UserReview::where('vendor_id',$item->id)->where('status',1)->get();
            $vendorProfile = VendorProfile::where('user_id',$item->id)->first();
            $customerActive = CustomerActivitive::where('vendor_id',$item->id)->where('activity',2)->first();
             $customerActiveSave = CustomerActivitive::where('vendor_id',$item->id)->where('activity',1)->first();
            $checkVendorReduce = Vendor::where('id',$value->user_id)->first();
            $primaryCategory =  $checkVendorReduce->categories()->where('mm__vendors_category.is_primary',1)->where('mm__vendors_category.status',1)->first();

            if(isset($primaryCategory) && !empty($primaryCategory))
            {
                $category = Category::find($primaryCategory->id);
            }
            
            if(isset($category) && !empty($category))
            {
                $categoryName = $category->name;
            }
            if($customerActiveSave)
            {
                $isSaveVendor = true;  
            }
            if($customerActive)
            {
                $isFavor = true;
            }
            $vendor_name = '';
            $width = 500;
            $height = 500;
            if($vendorProfile)
            {
                if($vendorProfile->business_name)
                {
                    $vendor_name = $vendorProfile->business_name;
                }
                if(isset($vendorProfile->dimension) && !empty($vendorProfile->dimension))
                {
                    $width = intval(json_decode($vendorProfile->dimension)->width);
                    $height = intval(json_decode($vendorProfile->dimension)->height);
                }
            }
            if($vendorLocation)
            {
                if(isset(Country::find($vendorLocation->country_id)->name) && !empty(Country::find($vendorLocation->country_id)->name))
                {
                    $country = Country::find($vendorLocation->country_id)->name;
                }
                if(isset(City::find($vendorLocation->city_id)->name) && !empty(City::find($vendorLocation->city_id)->name))
                {
                    $city = City::find($vendorLocation->city_id)->name;
                }
            }
                $listVendor += [$key => [
                'id' => $item->id,
                'name' => $vendor_name,
                'email' => $item->email,
                'created_at' => strtotime($item->created_at),
                'updated_at' => strtotime($item->updated_at),
                'photo' => config('constant.default.urlS3').$vendorProfile->photo,
                'width' => $width,
                'height' => $height,
                'isFavor' => $isFavor,
                'isSaveVendor' => $isSaveVendor,
                'lat' => $vendorProfile->lat,
                'lng' => $vendorProfile->lng,
                'country' => $country,
                'city' => $city,
                'countReview' => count($userReview),
                'rating' => $vendorProfile->rating_points / 2 ,
                'distance' => $value->distance,
                'categoryName' => $categoryName,
                ]
            ];
        }
        return $listVendor;
    }
    public static function formatListPortfolio($portfolios,$customer)
    {
        $listPortfolio = [];
        if(isset($portfolios) && !empty($portfolios))
        {
            foreach ($portfolios as $key => $portfolioTemp) {
                $isFavor = false;
                $customerActive = CustomerActivitive::where('portfolio_id',$portfolioTemp->id)->where('activity',5)->get();
                $customerActiveFavor = CustomerActivitive::where('portfolio_id',$portfolioTemp->id)->where('user_id',$customer->id)->where('activity',2)->first();
                $customerActiveView = CustomerActivitive::where('portfolio_id',$portfolioTemp->id)->where('activity',4)->get();
                $portfolio = VendorPortfolio::find($portfolioTemp->id);
                $width = 1024;
                $height = 1024;
                $arrImage = [];
                $photo = config('constant.default.urlS3').'/assets/media/no-image.png';
                if(count($portfolio->vendorPortfolioMedia) > 0)
                {
                    foreach ($portfolio->vendorPortfolioMedia as $key1 => $value1)
                    {
                        if($key1 == 0)
                        {
                            $photo = config('constant.default.urlS3').$value1->media_url;
                        }
                        if($value1->dimension)
                        {
                            $width = intval(json_decode($value1->dimension)->width);
                            $height = intval(json_decode($value1->dimension)->height);
                        }
                        $arrImage += [$key1 => [
                                    'image' => config('constant.default.urlS3').$value1->media_url,
                                    'width' =>  $width,
                                    'height' =>     $height,
                            ]
                        ];
                    }
                }
                
                $subCategory = '';
                $categoryName = '';
                if(isset(SubCategory::find($portfolio->sub_category_id)->name) && !empty(SubCategory::find($portfolio->sub_category_id)->name))
                {
                    $subCategory = SubCategory::find($portfolio->sub_category_id)->name;
                }
                if(isset(Category::find($portfolio->category_id)->name) && !empty(Category::find($portfolio->category_id)->name))
                {
                    $categoryName =Category::find($portfolio->category_id)->name;
                }
                if(isset($customerActiveFavor) && !empty($customerActiveFavor))
                {
                    $isFavor = true;
                }
                $listPortfolio += [$key => [
                    'id'   => $portfolio->id,
                    'title'   => $portfolio->title,
                    'photo'   => $photo,
                    'category'   => $categoryName,
                    'subCategory'   => $subCategory,
                    'description'   => $portfolio->description ? $portfolio->description : "",
                    'photography' => $portfolio->photography ? $portfolio->photography : "",
                    'imgPortfolio'   => $arrImage,
                    'isFavor'   => $isFavor,
                    'countLove'   => count($customerActive),
                    'countView'   => count($customerActiveView)
                ]
            ];
            }
        }
        return $listPortfolio;
        
    }
    public static function formatPortfolio($portfolio,$user)
    {
        $listPortfolio = [];
        $isLove = false;
        $isFavor = false;
        if($portfolio)
        {
            $lovePortfolio = CustomerActivitive::where('portfolio_id',$portfolio->id)->where('activity',5)->get();
            $portfolioActiveLove = CustomerActivitive::where('portfolio_id',$portfolio->id)->where('activity',5)->where('user_id',$user->id)->first();

             $portfolioActiveFavor = CustomerActivitive::where('portfolio_id',$portfolio->id)->where('activity',2)->where('user_id',$user->id)->first();
            if($portfolioActiveLove)
            {
                $isLove = true;
            }
            if($portfolioActiveFavor)
            {
                $isFavor = true;
            }
            $width = 1024;
            $height = 1024;
            $arrImage = [];
            foreach ($portfolio->vendorPortfolioMedia as $key1 => $value1) {
                if($value1->dimension)
                {
                    $width = intval(json_decode($value1->dimension)->width);
                    $height = intval(json_decode($value1->dimension)->height);
                }
                $arrImage += [$key1 => [
                            'image' => config('constant.default.urlS3').$value1->media_url,
                            'width' =>  $width,
                            'height' =>     $height,
                    ]
                ];
            }
            $subCategory = '';
            $categoryName = '';
                if(isset(SubCategory::find($portfolio->sub_category_id)->name) && !empty(SubCategory::find($portfolio->sub_category_id)->name))
                {
                    $subCategory = SubCategory::find($portfolio->sub_category_id)->name;
                }
            if(isset(Category::find($portfolio->category_id)->name) && !empty(Category::find($portfolio->category_id)->name))
                {
                    $categoryName =Category::find($portfolio->category_id)->name;
                }
                $urlDeepLink = env('APP_URL_WEBSITE','mummy.acc-svrs.com').'/view-vendor/portfolios/'.$portfolio->vendor_id.'?v='.strtotime(\Carbon\Carbon::now());
                $listPortfolio = [
                    'id'   => $portfolio->id,
                    'title'   => $portfolio->title,
                    'category'   => $categoryName,
                    'subCategory'   => $subCategory,
                    'description'   => $portfolio->description ? $portfolio->description : "",
                    'photography' => $portfolio->photography ? $portfolio->photography : "",
                    'isLove'   => $isLove,
                    'isFavor'   => $isFavor,
                    'imgPortfolio'   => $arrImage,
                    'urlDeepLink'   => $urlDeepLink,
                    'countLovePortfolio'   => count($lovePortfolio),
            ];  
        }
                
        return $listPortfolio;
        
    }
    public static function GetListPriceRange($priceRanges)
    {
        $arr =[];
        if(isset($priceRanges) && !empty($priceRanges))
        { 
            foreach ($priceRanges as $key => $value) {
                    $arr += [$key => [
                    'id' =>$value->id,
                    'name' => $value->price_name,
                    'description' => $value->description,
                    'sort' => $value->sort,
                    'status' => $value->status
                    ]
                ];
            }
        }
        return $arr;

    }
    public static function formatGetPriceList($priceLists)
    {
        $arr =[];
        if(isset($priceLists) && !empty($priceLists))
        { 
            foreach ($priceLists as $key => $value) {
                    $productName = '';
                    if(isset($value->product_name) && !empty($value->product_name))
                    {
                        $productName = $value->product_name;
                    }
                    $arr += [$key => [
                    'id' =>$value->id,
                    'sub_category_name' => $productName,
                    'description' => $value->description,
                    'price_name' => 'Price '.$value->price_name.':',
                    'price_value' => $value->price_value
                    ]
                ];
            }
        }
        return $arr;
    }
    public static function formatListReviews($userReview,$user)
    {
        $arr =[];
        if(isset($userReview) && !empty($userReview))
        { 
            foreach ($userReview as $key => $value) {
                $reviewReply = ReviewReply::where('review_id',$value->id)->first();
                $reply =  Helper::formatReviewReply($reviewReply);
                $checkCount = Helper::checkcount($value->vendor_id);
                $photo = config('constant.default.urlS3').'/assets/media/no-image.png';
                $vendor_photo = config('constant.default.urlS3').'/assets/media/no-image.png';
                $vendor_name = '';
                $customer_name = '';
                $country = '';
                $city = '';
                $subCategory = '';
                $category = '';
                $priceRange = '';
                $vendor_id = '';
                if($value->vendor_id)
                {
                    $vendor_id = $value->vendor_id;
                }
                $vendorProfile = VendorProfile::where('user_id',$value->vendor_id)->first();
                $customer = Customer::find($value->user_id);

                $isReview = false;
                $customerReview = UserReview::where('user_id',$user->id)->where('vendor_id',$value->vendor_id)->where('id',$value->id)->first();

                if(isset($customerReview) && !empty($customerReview) && count($customerReview) > 0)
                {
                    $isReview = true;
                }
                $vendor_name = '';
                if($vendorProfile)
                {
                    if($vendorProfile->photo)
                    {
                        $vendor_photo = config('constant.default.urlS3').$vendorProfile->photo;
                    }
                    if($vendorProfile->business_name)
                    {
                        $vendor_name = $vendorProfile->business_name;
                    }
                }
                if($customer)
                {
                    $customer_name = $customer->first_name;
                    if($customer->last_name)
                    {
                        $customer_name = $customer->first_name.' '.$customer->last_name;
                    }
                    if($customer->photo)
                    {
                         $photo = config('constant.default.urlS3').$customer->photo;
                    }
                }
                $vendorLocation = VendorLocation::where('user_id',$value->vendor_id)->first();
                $vendorCategory = VendorCategory::where('user_id',$value->vendor_id)->first();
                if($vendorLocation)
                {
                    if(isset(Country::find($vendorLocation->country_id)->name) && !empty(Country::find($vendorLocation->country_id)->name))
                    {
                        $country = Country::find($vendorLocation->country_id)->name;
                    }
                    if(isset(City::find($vendorLocation->city_id)->name) && !empty(City::find($vendorLocation->city_id)->name))
                    {
                        $city = City::find($vendorLocation->city_id)->name;
                    }
                }
                if($vendorCategory)
                {
                    if(isset(SubCategory::find($vendorCategory->sub_category_id)->name) && !empty(SubCategory::find($vendorCategory->sub_category_id)->name))
                    {
                       $subCategory = SubCategory::find($vendorCategory->sub_category_id)->name;
                    }
                    if(isset(Category::find($vendorCategory->category_id)->name) && !empty(Category::find($vendorCategory->category_id)->name))
                    {
                        $category = Category::find($vendorCategory->category_id)->name;
                    }
                    if(isset(PriceRange::find($vendorCategory->price_range_id)->price_name) && !empty(PriceRange::find($vendorCategory->price_range_id)->price_name))
                    {
                        $priceRange = PriceRange::find($vendorCategory->price_range_id)->price_name;
                    }
                }
                $memberShip = 1;
                $vendor_type_id = [];
                $checkVendorPackage = PlanSubscription::where('user_id',$vendor_id)->whereNull('canceled_at')->get();
                if(count($checkVendorPackage) > 0)
                {
                    foreach ($checkVendorPackage as $key9893 => $value9893) {

                        $vendor_type_id += [$key9893=> $value9893->plan_id];
                    }
                }
                if(in_array(2, $vendor_type_id)){
                    $memberShip = 1;
                }
                if(in_array(3, $vendor_type_id)){
                    $memberShip = 2;
                }
                if (in_array(4, $vendor_type_id)){
                    $memberShip = 3;
                }
                $vendor = Vendor::find($vendor_id);

                $nameCountryCity = Helper::getProfileVendor($vendor,1);
                $nameCategorySubCategory = Helper::getProfileVendor($vendor,2);
                if(count($reply) > 0)
                {
                    $arr += [$key => [
                    'memberShip' =>$memberShip,
                    'id' =>$value->id,
                    'vendor_id' => $vendor_id,
                    'vendor_name' => $vendor_name,
                    'vendor_photo' => $vendor_photo,
                    'country' => $country,
                    'countCountry' => $checkCount['0']->countCountry - 1,
                    'city' => $city,
                    'countCity' => $checkCount['0']->countCity - 1,
                    'category' => $category,
                    'countCategory' => $checkCount['0']->countCategory - 1,
                    'subCategory' => $subCategory,
                    'countSubCategory' => $checkCount['0']->countSubCategory - 1,
                    'priceRange' => $priceRange,
                    'customer_name' => $customer_name,
                    'content' => $value->content,
                    'title' => $value->title,
                    'rating' => $value->rating,
                    'photo' => $photo,
                    'isReview' => $isReview,
                    'created_at' => strtotime($value->created_at),
                    'reviewReply'   =>   $reply,
                    'nameCountryCity' => $nameCountryCity,
                    'nameCategorySubCategory' => $nameCategorySubCategory,
                    ]
                ];
                }
                else
                {
                    $arr += [$key => [
                    'memberShip' =>$memberShip,
                    'id' =>$value->id,
                    'vendor_id' => $vendor_id,
                    'vendor_name' => $vendor_name,
                    'vendor_photo' => $vendor_photo,
                    'country' => $country,
                    'countCountry' => $checkCount['0']->countCountry,
                    'city' => $city,
                    'countCity' => $checkCount['0']->countCity,
                    'category' => $category,
                    'countCategory' => $checkCount['0']->countCategory,
                    'subCategory' => $subCategory,
                    'countSubCategory' => $checkCount['0']->countSubCategory,
                    'priceRange' => $priceRange,
                    'customer_name' => $customer_name,
                    'content' => $value->content,
                    'title' => $value->title,
                    'rating' => $value->rating,
                    'photo' => $photo,
                    'isReview' => $isReview,
                    'created_at' => strtotime($value->created_at),
                    'nameCountryCity' => $nameCountryCity,
                    'nameCategorySubCategory' => $nameCategorySubCategory,
                    ]
                ]; 
                }
                    
            }
        }
        return $arr;
    }
    public static function formatReviewDetail($userReview,$userCustomer)
    {
        $photo = config('constant.default.urlS3').'/assets/media/no-image.png';
        $vendorName = '';
        $customerName = '';
        $vendorProfile = VendorProfile::where('user_id',$userReview->vendor_id)->first();
        $vendor = Customer::find($userReview->vendor_id);
        $customer = Customer::find($userReview->user_id);
        $vendor_name = '';
        if($vendorProfile)
        {
            if($vendorProfile->business_name)
            {
                $vendor_name = $vendorProfile->business_name;
            }
        }
        if($customer)
        {
            $customer_name = $customer->first_name;
            if($customer->last_name)
            {
                $customer_name = $customer->first_name.' '.$customer->last_name;
            }
            if($customer->photo)
            {
                 $photo = config('constant.default.urlS3').$customer->photo;
            }
        }
        $isReview = false;
        $customerReview = UserReview::where('user_id',$userCustomer->id)->where('vendor_id',$userReview->vendor_id)->where('id',$userReview->id)->first();
        if(isset($customerReview) && !empty($customerReview) && count($customerReview) > 0)
        {
            $isReview = true;
        }
        return [
                'id' =>$userReview->id,
                'vendor_id' => $userReview->vendor_id,
                'vendor_name' => $vendor_name,
                'customer_name' => $customer_name,
                'content' => $userReview->content,
                'title' => $userReview->title,
                'rating' => $userReview->rating,
                'photo' => $photo,
                'isReview' => $isReview,
                'created_at' => strtotime($userReview->created_at)
                ];
    }
    public static function formatListChildern($customerChildren)
    {
        $arr =[];
        if(isset($customerChildren) && !empty($customerChildren))
        { 
            foreach ($customerChildren as $key => $value) {
                $arr += [$key => [
                'id' =>$value->id,
                'name' => $value->name,
                'dob' => strtotime($value->dob),
                'age' => $value->age
                ]
            ];
            }
        }
        return $arr;
    }

    public static function formatGetVendors($vendors,$customer)
    {
        foreach ($vendors as $key => $vendor) {
            if($key == 0)
            {
                $social_facebook_link = '';
                $social_twitter_link = '';
                $social_instagram_link = '';
                $social_pinterest_link = '';
                $checkCount = Helper::checkcount($vendor->id);
            $isSaveVendor = false; 
            $isFavor = false;  
            $is_Credit = false;
            $country = '';
            $city = '';
            $category = '';
            $subCategory = '';
            $priceRange = '';
            $photo = config('constant.default.urlS3').'/assets/media/no-image.png';
            $phone = '';
            $phoneCode = '';
            $other_contact = [];
            $userPhone = UserPhone::where('user_id',$vendor->id)->first();
            if(isset($userPhone) && !empty($userPhone))
            {
                $phone = $userPhone->phone_number;
                $phoneCode = $userPhone->country_code;
            }
           $vendorLocation = VendorLocation::where('user_id',$vendor->id)->first();
           $userReview = UserReview::where('vendor_id',$vendor->id)->where('status',1)->whereNull('is_deleted')->get();
           $vendorCategory = VendorCategory::where('user_id',$vendor->id)->first();
           $vendorProfile = VendorProfile::where('user_id',$vendor->id)->first();
            $customerActive = CustomerActivitive::where('vendor_id',$vendor->id)->where('user_id',$customer->id)->where('activity',2)->first();
             $customerActiveSave = CustomerActivitive::where('vendor_id',$vendor->id)->where('activity',1)->where('user_id',$customer->id)->first();
            $customerCredit = VendorCredit::where('vendor_id',$vendor->id)->orderBy('point', 'desc')->first();
            if(count($customerCredit) > 0 && $customerCredit->point > 0)
            {
                $is_Credit = true;
            }
            if($customerActiveSave)
            {
                $isSaveVendor = true;  
            }
            if($customerActive)
            {
                $isFavor = true;
            }
            $vendor_name = '';
            $ratingPoints = 0;
            $about = '';
            $websiteURL = '';
            if($vendorProfile)
            {
                if($vendorProfile->social_media_link)
                {
                    $social_facebook_link = json_decode($vendorProfile->social_media_link) && isset(json_decode($vendorProfile->social_media_link)->facebook) ? json_decode($vendorProfile->social_media_link)->facebook : "";
                    $social_twitter_link = json_decode($vendorProfile->social_media_link) && isset(json_decode($vendorProfile->social_media_link)->twitter) ? json_decode($vendorProfile->social_media_link)->twitter : "";
                    $social_instagram_link = json_decode($vendorProfile->social_media_link) && isset(json_decode($vendorProfile->social_media_link)->instagram) ? json_decode($vendorProfile->social_media_link)->instagram : "";
                    $social_pinterest_link = json_decode($vendorProfile->social_media_link) && isset(json_decode($vendorProfile->social_media_link)->pinterest) ? json_decode($vendorProfile->social_media_link)->pinterest : "";
                }

                if($vendorProfile->others_social_data)
                {
                    $other_contacts = json_decode($vendorProfile->others_social_data);
                    if(is_array($other_contacts) && !empty($other_contacts)){
                        foreach ($other_contacts as $key => $item) {
                            if($item->id){
                                // if(isset$other_contact['type'] == [$item->name]){
                                //     $other_contact['value'] = $item->id;
                                // }else{
                                    $other_contact[] = [
                                        'type' => $item->name,
                                        'name' => config('constant.other_contact')[$item->name],
                                        'value' => $item->id
                                    ];
                                // }
                            }
                        }
                    }
                }
                if($vendorProfile->rating_points)
                {
                    $ratingPoints = $vendorProfile->rating_points;
                }
                if($vendorProfile->about)
                {
                    $about = $vendorProfile->about;
                }
                
                if($vendorProfile->business_name)
                {
                    $vendor_name = $vendorProfile->business_name;
                }
                if($vendorProfile->website)
                {
                    $websiteURL = $vendorProfile->website;
                }
                $photo = $vendorProfile->photo;
            }
           if($vendorLocation)
            {
                if(isset(Country::find($vendorLocation->country_id)->name) && !empty(Country::find($vendorLocation->country_id)->name))
                {
                    $country = Country::find($vendorLocation->country_id)->name;
                }
                if(isset(City::find($vendorLocation->city_id)->name) && !empty(City::find($vendorLocation->city_id)->name))
                {
                    $city = City::find($vendorLocation->city_id)->name;
                }
            }
            if($vendorCategory)
            {
                if(isset(SubCategory::find($vendorCategory->sub_category_id)->name) && !empty(SubCategory::find($vendorCategory->sub_category_id)->name))
                {
                   $subCategory = SubCategory::find($vendorCategory->sub_category_id)->name;
                }
                if(isset(Category::find($vendorCategory->category_id)->name) && !empty(Category::find($vendorCategory->category_id)->name))
                {
                    $category = Category::find($vendorCategory->category_id)->name;
                }
                if(isset(PriceRange::find($vendorCategory->price_range_id)->price_name) && !empty(PriceRange::find($vendorCategory->price_range_id)->price_name))
                {
                    $priceRange = PriceRange::find($vendorCategory->price_range_id)->price_name;
                }
            }
            $memberShip = 1;
            $vendor_type_id = [];
            $checkVendorPackage = PlanSubscription::where('user_id',$vendor->id)->whereNull('canceled_at')->get();
            if(count($checkVendorPackage) > 0)
            {
                foreach ($checkVendorPackage as $key9893 => $value9893) {

                    $vendor_type_id += [$key9893=> $value9893->plan_id];
                }
            }
            if(in_array(2, $vendor_type_id)){
                $memberShip = 1;
            }
            if(in_array(3, $vendor_type_id)){
                $memberShip = 2;
            }
            if (in_array(4, $vendor_type_id)){
                $memberShip = 3;
            }
            $social_media_link = [
                    'facebook' => $social_facebook_link,
                    'twitter' => $social_twitter_link,
                    'instagram' => $social_instagram_link,
                    'pinterest' => $social_pinterest_link,
            ];
            $nameCountryCity = Helper::getProfileVendor($vendor,1);
            $nameCategorySubCategory = Helper::getProfileVendor($vendor,2);

            $urlDeepLink = env('APP_URL_WEBSITE','mummy.acc-svrs.com').'/view-vendor/portfolios/'.$vendor->id.'?v='.strtotime(\Carbon\Carbon::now());
            return [
                    'memberShip' => $memberShip,
                    'urlDeepLink' => $urlDeepLink,
                    'url' => $websiteURL,
                    'id' =>$vendor->id,
                    'photo' =>config('constant.default.urlS3').$photo,
                    'name' => $vendor_name,
                    'email' => $vendor->email,
                    'country' => $country,
                    'countCountry' => $checkCount['0']->countCountry - 1,
                    'city' => $city,
                    'countCity' => $checkCount['0']->countCity - 1,
                    'category' => $category,
                    'countCategory' => $checkCount['0']->countCategory - 1,
                    'subCategory' => $subCategory,
                    'countSubCategory' => $checkCount['0']->countSubCategory - 1,
                    'priceRange' => $priceRange,
                    'isSaveVendor' => $isSaveVendor,
                    'isFavor' => $isFavor,
                    'is_Credit' => $is_Credit,
                    'phone' => $phone,
                    'phoneCode' => $phoneCode,
                    'countReview' => count($userReview),
                    'rating' => $ratingPoints / 2,
                    'description' => $about,
                    'social_media_link' => $social_media_link,
                    'other_contact' => $other_contact,
                    'about_business' => $about,
                    'nameCountryCity' => $nameCountryCity,
                    'nameCategorySubCategory' => $nameCategorySubCategory,
                    ];
            }
        }
        
    }

    public static function formatAccountDetail($item)
    {
        $last_name = '';
        if($item->last_name)
        {
            $last_name = $item->last_name;
        }
        $phone = '';
        $phoneCode = '';
        $userPhone = UserPhone::where('user_id',$item->id)->first();
        $settingMyMessage = 0;
        $settingMyReview = 0;
        $customerSettingMyMessage = CustomerSetting::where('user_id',$item->id)->where('key','notificaton_email_when_someone_reply_my_message')->first();
        if($customerSettingMyMessage)
        {
            $settingMyMessage = $customerSettingMyMessage->value;
        }
        $customerSettingMyReview = CustomerSetting::where('user_id',$item->id)->where('key','notificaton_email_when_someone_reply_my_review')->first();
         if($customerSettingMyReview)
        {
            $settingMyReview = $customerSettingMyReview->value;
        }
        $password = false;
        if($item->password)
        {
            $password = true;
        }
        if(isset($userPhone) && !empty($userPhone))
        {
            $phone = $userPhone->phone_number;
            $phoneCode = $userPhone->country_code;
        }
        if(isset($item->photo) && !empty($item->photo))
        {
            return [
            'id' => $item->id,
            'name'  =>  $item->first_name.' '.$last_name,
            'first_name' => $item->first_name,
            'last_name' => $last_name,
            'email' => $item->email,
            'password' => $password,
            'photo' => config('constant.default.urlS3').$item->photo,
            'created_at' => strtotime($item->created_at),
            'updated_at' => strtotime($item->updated_at),
            'status' => $item->status,
            'is_deleted' => $item->is_deleted,
            'facebook_id' => $item->facebook_id,
            'phone' => $phone,
            'phoneCode' => $phoneCode,
            'settingMyMessage' => $settingMyMessage,
            'settingMyReview' => $settingMyReview
            ];
        }
        else
        {
            return [
            'id' => $item->id,
            'name'  =>  $item->first_name.' '.$last_name,
            'first_name' => $item->first_name,
            'last_name' => $last_name,
            'email' => $item->email,
            'password' => $password,
            'photo' => config('constant.default.urlS3').'/assets/media/no-image.png',
            'created_at' => strtotime($item->created_at),
            'updated_at' => strtotime($item->updated_at),
            'status' => $item->status,
            'is_deleted' => $item->is_deleted,
            'facebook_id' => $item->facebook_id,
            'phone' => $phone,
            'phoneCode' => $phoneCode,
            'settingMyMessage' => $settingMyMessage,
            'settingMyReview' => $settingMyReview
            ];
        }
        
    }
    public static function formatGetComment($value,$user)
    {
        $customer_name ='';
             $photo = config('constant.default.urlS3').'/assets/media/no-image.png';
                $customer = Customer::find($value->user_id);
                $isComment = false;
                $customerComment = VendorComment::where('user_id',$user->id)->where('portfolios_id',$value->portfolios_id)->get();

                if(isset($customerComment) && !empty($customerComment) && count($customerComment) > 0)
                {
                    $isComment = true;
                }
                if($customer)
                {
                    if($customer->first_name)
                    {
                         $customer_name = $customer->first_name;
                         if($customer->last_name)
                         {
                            $customer_name = $customer->first_name.' '.$customer->last_name;
                         }
                    }
                    if($customer->photo)
                    {
                         $photo = config('constant.default.urlS3').$customer->photo;
                    } 
                }
                
                return [
                    'customer_name'  => $customer_name,
                    'id'   => $value->id,
                    'comment'   => $value->comment,
                    'photo'   => $photo,
                    'isRemove'   => $isComment,
                    'created_at'   => strtotime($value->created_at)
                ];
    }
    public static function formatAllComment($allComment,$user)
    {
        $listAllComment = [];
        if($allComment)
        {
           foreach ($allComment as $key => $value) {
            $customer_name ='';
             $photo = config('constant.default.urlS3').'/assets/media/no-image.png';
                $customer = Customer::find($value->user_id);
                $isComment = false;
                $customerComment = VendorComment::where('user_id',$user->id)->where('portfolios_id',$value->portfolios_id)->where('id',$value->id)->get();

                if(isset($customerComment) && !empty($customerComment) && count($customerComment) > 0)
                {
                    $isComment = true;
                }
                if($customer)
                {
                    if($customer->first_name)
                    {
                         $customer_name = $customer->first_name;
                         if($customer->last_name)
                         {
                            $customer_name = $customer->first_name.' '.$customer->last_name;
                         }
                    }
                    if($customer->photo)
                    {
                         $photo = config('constant.default.urlS3').$customer->photo;
                    } 
                }
                
                $listAllComment += [$key => [
                    'customer_name'  => $customer_name,
                    'id'   => $value->id,
                    'comment'   => $value->comment,
                    'photo'   => $photo,
                    'isRemove'   => $isComment,
                    'created_at'   => strtotime($value->created_at)
                ]
                ];
            } 
        }
        return $listAllComment;
        
    }
    public static function formatListCategories($categories)
    {
         $listAllCategory = [];
        if($categories)
        {
           foreach ($categories as $key => $value) {
               $photo = config('constant.default.urlS3').'/assets/media/no-image.png';
                if($value->photo)
                {
                     $photo = config('constant.default.urlS3').$value->photo;
                }
                $listAllCategory += [$key => [
                    'id'  => $value->id,
                    'name'   => $value->name,
                    'photo'   => $photo,
                    'description'   => $value->description,
                    'sorts'   => $value->sorts,
                     'status'   => $value->status,
                ]
                ]; 
             
            } 
        }
        return $listAllCategory;
    } 

     public static function formatListMessage($messages)
    {
         $listAllMessage = [];
        if($messages)
        {
           foreach ($messages as $key => $value) {
            $photoSender = config('constant.default.urlS3').'/assets/media/no-image.png';
            $vendorProfile = VendorProfile::where('user_id',$value->sender_id)->first();
            $sender = Customer::find($value->sender_id);
            $sender_name = '';
            $city = '';
            $country = '';
            $vendorLocation = VendorLocation::where('user_id',$value->sender_id)->first();
            if($vendorLocation)
            {
                if(isset(Country::find($vendorLocation->country_id)->name) && !empty(Country::find($vendorLocation->country_id)->name))
                {
                    $country = Country::find($vendorLocation->country_id)->name;
                }
                if(isset(City::find($vendorLocation->city_id)->name) && !empty(City::find($vendorLocation->city_id)->name))
                {
                    $city = City::find($vendorLocation->city_id)->name;
                }
            }
            if($vendorProfile)
            {
                $photoSender = config('constant.default.urlS3').$vendorProfile->photo;
                if($vendorProfile->business_name)
                {
                    $sender_name = $vendorProfile->business_name;
                }
            }
             
            $listAllMessage += [$key => [
                'id'  => $value->id,
                'replyID'  => $value->sender_id,
                'sender_id'   => $value->sender_id,
                'senderName'   => $sender_name,
                'country'   => $country,
                'city'   => $city,
                'photoSender'   => $photoSender,
                'receiver_id'   => $value->receiver_id,
                'subject'   => $value->subject,
                'message'   => $value->message,
                'status'   => $value->status,
                'is_read'   => $value->is_customer_read,
                'created_at'   => strtotime($value->created_at),
                 
            ]
            ];
            } 
        }
        return $listAllMessage;
    }
    public static function formatListMessageSend($messages)
    {
         $listAllMessage = [];
        if($messages)
        {
           foreach ($messages as $key => $value) {
            $photoSender = config('constant.default.urlS3').'/assets/media/no-image.png';
            $vendorProfile = VendorProfile::where('user_id',$value->receiver_id)->first();
            $customer = Customer::find($value->sender_id);
            $receriver_name = '';
            $city = '';
            $country = '';
             $vendorLocation = VendorLocation::where('user_id',$value->receiver_id)->first();
            if($vendorLocation)
            {
                if(isset(Country::find($vendorLocation->country_id)->name) && !empty(Country::find($vendorLocation->country_id)->name))
                {
                    $country = Country::find($vendorLocation->country_id)->name;
                }
                if(isset(City::find($vendorLocation->city_id)->name) && !empty(City::find($vendorLocation->city_id)->name))
                {
                    $city = City::find($vendorLocation->city_id)->name;
                }
            }
            if($vendorProfile)
            {
                $photoSender = config('constant.default.urlS3').$vendorProfile->photo;
                if($vendorProfile->business_name)
                {
                    $receriver_name = $vendorProfile->business_name;
                }
            }
            $listAllMessage += [$key => [
                'id'  => $value->id,
                'replyID'  => $value->receiver_id,
                'sender_id'   => $value->sender_id,
                'senderName'   => $receriver_name,
                'country'   => $country,
                'city'   => $city,
                'photoSender'   => $photoSender,
                'receiver_id'   => $value->receiver_id,
                'subject'   => $value->subject,
                'message'   => $value->message,
                'status'   => $value->status,
                'is_read'   => 1,
                'isRead'   => true,
                'created_at'   => strtotime($value->created_at),
                 
            ]
            ];
            } 
        }
        return $listAllMessage;
    }
    public static function formatListMessageTrash($messages,$user)
    {
         $listAllMessage = [];
        if($messages)
        {
           foreach ($messages as $key => $value) {
            
            $replyID = $user->id == $value->receiver_id ? $value->sender_id : $value->receiver_id;
            $photoSender = config('constant.default.urlS3').'/assets/media/no-image.png';
            $vendorProfile = VendorProfile::where('user_id',$replyID)->first();
            $customer = Customer::find($value->sender_id);
            $sender_name = '';
            $city = '';
            $country = '';
             $vendorLocation = VendorLocation::where('user_id',$replyID)->first();
            if($vendorLocation)
            {
                if(isset(Country::find($vendorLocation->country_id)->name) && !empty(Country::find($vendorLocation->country_id)->name))
                {
                    $country = Country::find($vendorLocation->country_id)->name;
                }
                if(isset(City::find($vendorLocation->city_id)->name) && !empty(City::find($vendorLocation->city_id)->name))
                {
                    $city = City::find($vendorLocation->city_id)->name;
                }
            }
            if($vendorProfile)
            {
                $photoSender = config('constant.default.urlS3').$vendorProfile->photo;
                if($vendorProfile->business_name)
                {
                    $sender_name = $vendorProfile->business_name;
                }
            }
            else
            {
                if($customer)
                {
                    $sender_name = $customer->first_name;
                    if($customer->last_name)
                    {
                        $sender_name = $customer->first_name.' '.$customer->last_name;
                    }
                    if($customer->photo)
                    {
                         $photoSender = config('constant.default.urlS3').$customer->photo;
                    }
                }
            }
            $listAllMessage += [$key => [
                'id'  => $value->id,
                'replyID'  => $replyID,
                'sender_id'   => $value->sender_id,
                'senderName'   => $sender_name,
                'country'   => $country,
                'city'   => $city,
                'photoSender'   => $photoSender,
                'receiver_id'   => $value->receiver_id,
                'subject'   => $value->subject,
                'message'   => $value->message,
                'status'   => $value->status,
                'is_read'   => $value->is_customer_read,
                'created_at'   => strtotime($value->created_at),
                'deleted_at'   => strtotime($value->is_deleted),
                 
            ]
            ];
            } 
        }
        return $listAllMessage;
    }
    public static function formatGetMessage($value)
    {
        $photoSender = config('constant.default.urlS3').'/assets/media/no-image.png';
            $vendorProfile = VendorProfile::where('user_id',$value->sender_id)->first();
            $sender_name = '';
            if($vendorProfile)
            {
                $photoSender = config('constant.default.urlS3').$vendorProfile->photo;
                if($vendorProfile->business_name)
                {
                    $sender_name = $vendorProfile->business_name;
                }
            }
            return [
                'id'  => $value->id,
                'sender_id'   => $value->sender_id,
                'senderName'   => $sender_name,
                'photoSender'   => $photoSender,
                'receiver_id'   => $value->receiver_id,
                'subject'   => $value->subject,
                'message'   => $value->message,
                'status'   => $value->status,
                'is_read'   => $value->is_read,
                'created_at'   => strtotime($value->created_at),
            ];
    }
    public static function formatGetVendor($vendor)
    {
        $isFavor = false;  
        $country = '';
        $city = '';
        $category = '';
        $subCategory = '';
        $priceRange = '';
        $photo = '/assets/media/no-image.png';
        $phone = '';
        $phoneCode = '';
        $checkCount = Helper::checkcount($vendor->id);
        $userPhone = UserPhone::where('user_id',$vendor->id)->first();
        if(isset($userPhone) && !empty($userPhone))
        {
            $phone = $userPhone->phone_number;
            $phoneCode = $userPhone->country_code;
        }
       $vendorLocation = VendorLocation::where('user_id',$vendor->id)->first();
       $userReview = UserReview::where('vendor_id',$vendor->id)->where('status',1)->get();
       $vendorCategory = VendorCategory::where('user_id',$vendor->id)->first();
       $vendorProfile = VendorProfile::where('user_id',$vendor->id)->first();
        $customerActive = CustomerActivitive::where('vendor_id',$vendor->id)->where('activity',2)->first();
        if($customerActive)
        {
            $isFavor = true;
        }
       if($vendorProfile)
       {
         $photo = $vendorProfile->photo;
       }
       if($vendorLocation)
        {
            if(isset(Country::find($vendorLocation->country_id)->name) && !empty(Country::find($vendorLocation->country_id)->name))
            {
                $country = Country::find($vendorLocation->country_id)->name;
            }
            if(isset(City::find($vendorLocation->city_id)->name) && !empty(City::find($vendorLocation->city_id)->name))
            {
                $city = City::find($vendorLocation->city_id)->name;
            }
        }
        if($vendorCategory)
        {
            if(isset(SubCategory::find($vendorCategory->sub_category_id)->name) && !empty(SubCategory::find($vendorCategory->sub_category_id)->name))
            {
               $subCategory = SubCategory::find($vendorCategory->sub_category_id)->name;
            }
            if(isset(Category::find($vendorCategory->category_id)->name) && !empty(Category::find($vendorCategory->category_id)->name))
            {
                $category = Category::find($vendorCategory->category_id)->name;
            }
            if(isset(PriceRange::find($vendorCategory->price_range_id)->price_name) && !empty(PriceRange::find($vendorCategory->price_range_id)->price_name))
            {
                $priceRange = PriceRange::find($vendorCategory->price_range_id)->price_name;
            }
        }
        $vendor_name= '';
        if($vendorProfile)
        {
            if($vendorProfile->business_name)
            {
                $vendor_name = $vendorProfile->business_name;
            }
        }
        $countCountry = 0;
        $countCity = 0;
        $countCategory = 0;
        $countSubCategory = 0;
        if(isset($checkCount['0']) && !empty($checkCount['0']))
        {
            $countCountry = $checkCount['0']->countCountry - 1;
            $countCity = $checkCount['0']->countCity - 1;
            $countCategory = $checkCount['0']->countCategory - 1;
            $countSubCategory = $checkCount['0']->countSubCategory - 1;
        }
        $nameCountryCity = Helper::getProfileVendor($vendor,1);
        $nameCategorySubCategory = Helper::getProfileVendor($vendor,2);
        return [
                'id' =>$vendor->id,
                'photo' =>config('constant.default.urlS3').$photo,
                'name' => $vendor_name,
                'country' => $country,
                'countCountry' => $countCountry,
                'city' => $city,
                'countCity' => $countCity,
                'category' => $category,
                'countCategory' => $countCategory,
                'subCategory' => $subCategory,
                'countSubCategory' => $countSubCategory,
                'nameCountryCity' => $nameCountryCity,
                'nameCategorySubCategory' => $nameCategorySubCategory,
                ];
    } 
    public static function checkcount($vendorID)
    {
        $query = "select tb.user_id, COUNT(DISTINCT(tb.category_id)) AS countCategory, COUNT(DISTINCT(tb.sub_category_id)) AS countSubCategory, COUNT(DISTINCT(tb1.country_id)) AS countCountry, COUNT(DISTINCT(tb1.city_id)) AS countCity from mm__vendors_category AS tb Left JOIN mm__vendors_location AS tb1 ON tb.user_id = tb1.user_id JOIN users AS tb2 ON tb2.id = tb.user_id WHERE tb2.status = 1 AND tb.user_id = $vendorID AND tb.status = 1 AND tb.is_deleted IS NULL  GROUP BY tb.user_id";
        $result = DB::select($query);
        return $result;
    }
    
    public static function getImage($path , $server = 's3'){
        if($path == null || !$path || empty($path))
        {
            return 'img/avatar.png';
        }
        return \Storage::disk($server)->url(ltrim($path,'/'));
    }

    public static function removeImage($path , $server = 's3'){
        return \Storage::disk($server)->delete($path);
    }
    
    public static function uploadImage($file, $filename = false , $resizeWidth = false, $resizeHeight = false){
        if(!$filename)
            $filename  = time() . '.' . $file->getClientOriginalExtension();
        // new File('/path/to/photo');
        $img = \Image::make($file);

        if($resizeWidth && $resizeHeight){
            $img->resize($resizeWidth, $resizeHeight, function ($constraint) {
                $constraint->aspectRatio();
            });
        }

        //detach method is the key! Hours to find it... :/
        $resource = $img->stream()->detach();

        $status = \Storage::disk('s3')->put( config('upload.files-path') . $filename, $resource,'public');
        if($status){
            $path = config('upload.files-path') . $filename;
        }
        else{
            $path = null;
        }

        return $path;
    }
    // public static function getMlink($links)
    // {
    //     return $links;
    //     if($links)
    //     {
    //         foreach ($links as $key => $link) {
    //             return $link;
    //             return  [
    //             "Id" => $link->Id,
    //             "title" => $link->title,
    //             "url" => $link->url,
    //             "thumbnailUrl" => $link->thumbnailUrl,
    //             "authorName" => $link->authorName,
    //             "categoryName" => $link->categoryName,
    //         ];
    //         }  
    //     }
    //     return [];  
    // }
    public static function getAdvertisement($item)
    {
        $url = '';
        if(isset($item) && !empty($item))
        {
            if(isset($item->link) && !empty($item->link))
            {
                $url = $item->link;
            }
            return [
                'title' => $item->title,
                'description' => $item->description,
                'url' => $url,
                'thumbnailUrl' => config('constant.default.urlS3').$item->media,
                'authorName' => "Mummyfique Contributor",
                'categoryName' => "Minis"
              ];
        }
        return [];
        

    }
    public static function checkActivity($vendorID,$userID,$type,$check){
        $today = Carbon::today();
        $checkActivity = CustomerActivitive::where('vendor_id',$vendorID)->where('user_id',$userID)->where('activity',$type)->whereDate('created_at',date('Y-m-d', strtotime($today)))->first();
        if(isset($checkActivity) && !empty($checkActivity))
        {
            return true;
        }
        else
        {
            
            $addViewWebsite = new CustomerActivitive();
            $addViewWebsite->vendor_id = $vendorID;
            $addViewWebsite->user_id = $userID;
            if($check == true)
            {
                $addViewWebsite->status = 1;
            }
            $addViewWebsite->activity = $type;
            $addViewWebsite->save(); 
            return false;
        }
    }
    public static function reducePoint($vendorID,$user_id,$type){
        $vendor = Vendor::find($vendorID);
        $checkVendorPackage = PlanSubscription::where('user_id',$vendor->id)->get();
        $vendorSetting = \App\Mummy\Api\V1\Entities\Vendors\VendorSetting::where('vendor_id',$vendorID)->first();
        $isCheck = false;
        $vendor_type_id = [];
        $checkActivity = CustomerActivitive::where('vendor_id',$vendor->id)->where('status',1)->where('user_id',$user_id)->whereIn('activity', [6, 7, 8, 9, 11, 12, 13, 14, 15, 22])->first();
        $checkActivityNoStatus = CustomerActivitive::where('vendor_id',$vendor->id)->where('user_id',$user_id)->whereIn('activity', [6, 7, 8, 9, 11, 12, 13, 14, 15, 22])->first();
         if(count($checkVendorPackage) > 0)
        {
            foreach ($checkVendorPackage as $key9893 => $value9893) {
                 if(empty($value9893->canceled_at))
                {
                    $vendor_type_id += [$key9893=> $value9893->plan_id];
                }
                
            }
        }
        if(in_array(3, $vendor_type_id)){
            $isCheck = true;
        }
        if (in_array(4, $vendor_type_id)){
            $isCheck = true;
        }
        if(isset($checkActivity) && !empty($checkActivity)){
             
        }
        else
        {
            if($isCheck == false)
            {
                $vendorCredit = \App\Mummy\Api\V1\Entities\Vendors\VendorCredit::where('vendor_id',$vendor->id)->first();
                if(isset($vendorCredit))
                {
                    if($vendorCredit->point > 0)
                    {
                        $vendorCredit->point = $vendorCredit->point - 1;
                        $vendorCredit->save();
                        return true;
                    }
                }
            }
        }
        if(isset($checkActivityNoStatus) && !empty($checkActivityNoStatus)){

        }
        else{
            $profile = VendorProfile::where('user_id',$vendorID)->first();
            if(isset($profile) && !empty($profile))
            {
                $profile->send_mail_weekly = 2;
                $profile->send_mail_monthly = 2;
                $profile->save();
            }
            
        }

            
        
        return false;
    }
    public static function checkReduceCredit($vendor,$user_id,$type)
    {
        $isCheck = false;
        $vendor_type_id = [];
        $checkVendorPackage = PlanSubscription::where('user_id',$vendor->id)->get();
        if(count($checkVendorPackage) > 0)
        {
            foreach ($checkVendorPackage as $key9893 => $value9893) {

                $vendor_type_id += [$key9893=> $value9893->plan_id];
            }
        }
        if(in_array(3, $vendor_type_id)){
            $isCheck = true;
        }
        if (in_array(4, $vendor_type_id)){
            $isCheck = true;
        }
        if($isCheck === false)
        {
            $checkActivity = CustomerActivitive::where('vendor_id',$vendor->id)->where('user_id',$user_id)->where('activity',21)->first();
            if(isset($checkActivity) && !empty($checkActivity)){

            }
            else
            {
                $vendorCredit = \App\Mummy\Api\V1\Entities\Vendors\VendorCredit::where('vendor_id',$vendor->id)->first();
                if(isset($vendorCredit))
                {
                    if($vendorCredit->point > 0)
                    {
                        $vendorCredit->point = $vendorCredit->point - 1;
                        $vendorCredit->save();
                        $addReducePointActive = new CustomerActivitive();
                        $addReducePointActive->vendor_id = $vendor->id;
                        $addReducePointActive->user_id = $user_id;
                        $addReducePointActive->status = 1;
                        $addReducePointActive->activity = 21;
                        $addReducePointActive->save(); 
                    }
                }
            }
            
        }
    }
    public static function getInfoVendor($questions, $vendor_id)
    {
        $arr = [];

        if(count($questions) > 0)
        {
            $i = 0;
            foreach ($questions as $key => $question) {
                $questionVendorCategory = QuestionVendorCategory::where('question_id',$question->id)->where('vendor_id', $vendor_id)->first();
                if(isset($questionVendorCategory) && !empty($questionVendorCategory))
                {
                    if($questionVendorCategory->answer)
                    {
                        $arr += [$i => [
                                'question'  =>  $question->question,
                                'anwser'  =>  $questionVendorCategory->answer,
                            ]
                        ];
                        $i++;
                    }
                    
                }
            }
        }
        return $arr;
    }
    public static function promptReviewMessage($user)
    {
        $isCheck = false;
        if($user->is_show_popup == 0)
        {
            $today = Carbon::today();
            $todaydiff10day = $today->copy()->subDays(10);
            if($user->first_app_login)
            {
                if(strtotime($todaydiff10day) >= strtotime($user->first_app_login))
                {
                    $isCheck = true;
                }
            }
            $customerActivitiveFavourite = CustomerActivitive::where('user_id',$user->id)->where('activity',2)->get();
            $customerActivitiveLike = CustomerActivitive::where('user_id',$user->id)->where('activity',5)->get();
            if(count($customerActivitiveFavourite) >= 10 )
            {
                $isCheck = true;
            } 
            if(count($customerActivitiveLike) >= 10 )
            {
                $isCheck = true;
            }   
        }
        
        return $isCheck;
    }
    public static function formatReviewReply($reviewReply)
    {
        $arr = [];
        if(isset($reviewReply) && !empty($reviewReply))
        {
            $arr = [
                'replyContent' =>  $reviewReply->reply_content,
                'createdAt' =>  strtotime($reviewReply->created_at)

            ];
        }
        return $arr;
    }
    
    public static function getSystemSetting($key){
        $item = Setting::where('name', $key)->first();
        if(count($item)){
            return $item->plainValue;
        }
        return "";
    }

    public static function formatInstagramFeed($items, $type = "list"){
        $feed = [];

        if($type == "list"){
            if(isset($items['data']) && !empty(isset($items['data']))){
                foreach ($items['data'] as $key => $item) {
                    if(isset($item['images']) && !empty($item['images'])){
                        $type = "single";
                        if(isset($item['carousel_media']) && !empty($item['carousel_media'])){
                            $type = "group";
                        }
                        $info = [
                            'username' => $item['user']['username'],
                            'full_name' => $item['user']['full_name'],
                            'caption' => $item['caption']['text'] ? $item['caption']['text'] : "",
                        ];
                        $feed[] = [
                            'id' => $item['id'],
                            'image' => $item['images']['standard_resolution']['url'],
                            'width' => $item['images']['standard_resolution']['width'],
                            'height' => $item['images']['standard_resolution']['height'],
                            'link' => $item['link'],
                            'type' => $type,
                            'info' => $info,
                        ];
                    }
                }
            }
        }else{
            if(isset($items['data']['carousel_media']) && !empty($items['data']['carousel_media'])){

                foreach ($items['data']['carousel_media'] as $key => $item) {
                    // dd($item['images']);
                    if(isset($item['images']) && !empty($item['images'])){
                        $feed[] = [
                            // 'id' => $item['id'],
                            'image' => $item['images']['standard_resolution']['url'],
                            'width' => $item['images']['standard_resolution']['width'],
                            'height' => $item['images']['standard_resolution']['height'],
                            'link' => $items['data']['link'],
                        ];
                    }
                }

            }else{
                 $feed[] = [
                    // 'id' => $item['id'],
                    'image' => $items['data']['images']['standard_resolution']['url'],
                    'width' => $items['data']['images']['standard_resolution']['width'],
                    'height' => $items['data']['images']['standard_resolution']['height'],
                    'link' => $items['data']['link'],
                ];
            }
        }

        return $feed;
    }
    public static function getProfileVendor($vendor,$type){
        $result = [];
        $vendorLocation = VendorLocation::where('user_id',$vendor->id)->whereNull('is_deleted')->get();
        $vendorCategory = VendorCategory::where('user_id',$vendor->id)->whereNull('is_deleted')->get();
        // $i = 0;
        if($type == 1){
            if(count($vendorLocation) > 0){
                foreach ($vendorLocation as $key => $value) {
                       
                            $countryName = '';
                            $cityName = '';
                            if(isset($value->country->name) && !empty($value->country->name)){
                                $countryName = $value->country->name;
                            }
                            if(isset($value->city->name) && !empty($value->city->name)){
                                $cityName = $value->city->name;
                            }
                            $result += [$key => 
                                [
                                    'name'    =>  $countryName .' - '. $cityName,

                                ]]; 
                    }
                }
        }
        if($type == 2){
            if(count($vendorCategory) > 0){
                foreach ($vendorCategory as $key => $value) {
                            $categoryName = '';
                            $subCategoryName = '';
                            $name = '';
                            if(isset($value->category->name) && !empty($value->category->name)){
                                $name = $categoryName = $value->category->name;
                            }
                            if(isset($value->subCategory->name) && !empty($value->subCategory->name)){
                                $subCategoryName = $value->subCategory->name;
                                $name .= ' - '. $subCategoryName;
                            }
                            $result += [$key => 
                                [
                                    'name'    =>  $name,

                                ]]; 
                    }
                }
        }
        return $result;

    }
}