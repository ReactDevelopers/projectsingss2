<?php namespace Modules\Vendor\Services;

use DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Pagination\Paginator;
use Illuminate\Http\Request;
use Modules\Media\Services\FileService;
use Modules\Media\Repositories\FileRepository;
use Modules\Vendor\Entities\Vendor;
use Modules\Vendor\Entities\VendorCategory;
use Modules\Vendor\Entities\VendorPhone;
use Modules\Vendor\Entities\VendorPlan;
use Modules\Vendor\Entities\VendorLocation;
use Modules\Vendor\Entities\VendorCredit;
use Modules\Vendor\Entities\Country;
use Modules\Vendor\Entities\State;
use Modules\Vendor\Entities\City;
use Modules\Vendor\Entities\User;
use Modules\Vendor\Repositories\VendorRepository;
use Modules\Vendor\Repositories\VendorProfileRepository;
use Modules\Vendor\Repositories\VendorLocationRepository;
use Modules\Vendor\Repositories\VendorCategoryRepository;
use Modules\Vendor\Repositories\VendorPhoneRepository;
use Modules\Vendor\Repositories\VendorCreditRepository;
use Modules\Comment\Repositories\CommentRepository;
use Modules\Comment\Repositories\VendorcommentRepository;
use Modules\Advertisement\Services\AdvertisementService;
use Modules\Portfolio\Repositories\PortfolioRepository;
use Carbon\Carbon;
use Modules\Vendor\Events\VendorWasCreated;
use Modules\Media\Services\MediaService;
use Modules\Comment\Services\CommentService;

class VendorService {

    /**
     *
     * @var VendorRepository
     */
    private $vendorRepository;  
    
     /**
     *
     * @var VendorProfileRepository
     */
    private $vendorProfileRepository;  

    /**
     *
     * @var VendorLocationRepository
     */
    private $vendorLocationRepository;  

    /**
     *
     * @var VendorCategoryRepository
     */
    private $vendorCategoryRepository; 

    /**
     *
     * @var VendorPhoneRepository
     */
    private $vendorPhoneRepository;  

    /**
     *
     * @var PortfolioRepository
     */
    private $portfolioRepository;  

    /**
     *
     * @var VendorCreditRepository
     */
    private $vendorCreditRepository; 

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
     * @var MediaService
     */
    private $mediaService;

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

    public function __construct( VendorRepository $vendorRepository, VendorProfileRepository $vendorProfileRepository, VendorLocationRepository $vendorLocationRepository, VendorCategoryRepository $vendorCategoryRepository, VendorPhoneRepository $vendorPhoneRepository, FileService $fileService, FileRepository $file, PortfolioRepository $portfolioRepository, MediaService $mediaService, VendorCreditRepository $vendorCreditRepository, CommentRepository $reviewRepository, VendorcommentRepository $commentRepository, CommentService $commentService) {
        $this->vendorRepository             = $vendorRepository;
        $this->vendorProfileRepository      = $vendorProfileRepository;
        $this->vendorLocationRepository     = $vendorLocationRepository;
        $this->vendorCategoryRepository     = $vendorCategoryRepository;
        $this->vendorPhoneRepository        = $vendorPhoneRepository;
        $this->portfolioRepository          = $portfolioRepository;
        $this->fileService                  = $fileService;
        $this->file                         = $file;
        $this->mediaService                 = $mediaService;
        $this->vendorCreditRepository       = $vendorCreditRepository;
        $this->reviewRepository             = $reviewRepository;
        $this->commentRepository            = $commentRepository;
        $this->commentService               = $commentService;
    }

    public function all(){
        return Vendor::select('users.*', 'role_users.role_id')->join('role_users', 'role_users.user_id', '=', 'users.id')->where('role_users.role_id', Config('constant.user_role.vendor'))->get();
    }

    public function count(){
        return Vendor::join('role_users', 'role_users.user_id', '=', 'users.id')->where('role_users.role_id', Config('constant.user_role.vendor'))->count();
    }

    public function allArray(){
        $vendors = Vendor::select('users.*', 'mm__vendors_profile.business_name')
                        ->join('role_users', 'role_users.user_id', '=', 'users.id')
                        ->join('mm__vendors_profile', 'mm__vendors_profile.user_id', '=', 'users.id')
                        ->where('role_users.role_id', Config('constant.user_role.vendor'))
                        ->orderBy('mm__vendors_profile.business_name', 'asc')
                        ->get();
        $data = [];
        if(count($vendors)){
            foreach ($vendors as $key => $item) {
                $data += [
                    $item->id => $item->business_name,
                ];
            }
            // asort($data);
        }

        return $data;
    }

    public function findBy($field, $value){
        if(!$field || !$value){
            return false;
        }

        return $this->vendorRepository->findByAttributes(array($field => $value));
    }

    public function getList($option = 'list', $keyword = '', $order_field = 'id', $sort = 'ASC', $limit = 10, $offset = 0){

        $where = "";
        if($keyword){
            $where = " AND p.business_name LIKE '%$keyword%'";
        }
        $role = Config('constant.user_role.vendor');

        $query = "SELECT  p.business_name AS full_name, u.first_name, u.last_name, u.email, p.photo, u.id
                    FROM users u 
                    LEFT JOIN role_users r ON r.user_id = u.id
                    LEFT JOIN mm__vendors_profile  p ON p.user_id = u.id
                    WHERE r.role_id = $role AND u.is_deleted IS NULL $where 
                    ORDER BY $order_field $sort";
        
        if($option == 'total_count'){
            return count(\DB::select($query));
        }

        $query.= " LIMIT $offset, $limit";
        return \DB::select($query);
    }

    public function getItems($option = 'list', $request){
        $limit = $request->get('limit') ? $request->get('limit') : 10;
        $keyword = $request->get('keyword') ? $request->get('keyword') : "";

        $array_field = array('id', 'business_name', 'email', 'last_login');
        $order_field = $request->get('order_field') && in_array($request->get('order_field'), $array_field) ? $request->get('order_field') : 'id';
        $sort = $request->get('sort') ? $request->get('sort') : 'DESC';

        if($option == 'list'){
            $items = Vendor::select('users.*', 'mm__vendors_profile.business_name')
                         ->join('role_users', 'role_users.user_id', '=', 'users.id')
                         ->join('mm__vendors_profile', 'mm__vendors_profile.user_id', '=', 'users.id')
                         ->where('role_users.role_id', Config('constant.user_role.vendor'))
                         ->where(function($query) use ($keyword) {
                            $query->where('users.id', '=', $keyword);
                            $query->orWhere('mm__vendors_profile.business_name', 'like', '%'.$keyword.'%');
                            $query->orWhere('users.email', 'like', '%' . $keyword . '%');
                        })
                        ->orderBy($order_field, $sort)
                        ->paginate($limit);

            return $items;
        }else{
            return Vendor::select('users.*', 'mm__vendors_profile.business_name')
                         ->join('role_users', 'role_users.user_id', '=', 'users.id')
                         ->join('mm__vendors_profile', 'mm__vendors_profile.user_id', '=', 'users.id')
                         ->where('role_users.role_id', Config('constant.user_role.vendor'))
                         ->where(function($query) use ($keyword) {
                            $query->where('users.id', '=', $keyword);
                            $query->orWhere('mm__vendors_profile.business_name', 'like', '%'.$keyword.'%');
                            $query->orWhere('users.email', 'like', '%' . $keyword . '%');
                        })
                         ->count();
        }
        
    }

    public function create($data){
        $address = $data['business_address'];
        // get location from business address only
        // if(isset($data['city_id'])) $address.= ', ' . $this->getCityById($data['city_id'])->name;
        // if(isset($data['state_id'])) $address.= ', ' . $this->getStateById($data['state_id'])->name;
        // if(isset($data['country_id'])) $address.= ', ' . $this->getCountryById($data['country_id'])->name;

        $location = $this->getLocation($address);
        if(!empty($location)){
            $data = array_merge($data, [
                    'lat' => $location['lat'],
                    'lng' => $location['long'],
                ]);
        }

        $business_phone = $this->getPhoneRequest($data['business_phone']);
        $data['business_phone'] = $business_phone ? $business_phone[0] : "";

        // create vendor item
        // $data = array_merge($data, ['status' => '0']);
        $item = $this->vendorRepository->create($data);

        unset($data['status']);
        // jso encode social media data
        $social_media_link = json_encode([
            'facebook' => strtolower($data['social_media_link_facebook']),
            'twitter' => strtolower($data['social_media_link_twitter']),
            'instagram' => strtolower($data['social_media_link_instagram']),
            'pinterest' => strtolower($data['social_media_link_pinterest']),
        ]);
        $data = array_merge($data, ['is_primary' => 1, 'user_id' => $item->id, 'status' => '1', 'rating_points' => '0', 'social_media_link' => $social_media_link]);
        $data['sub_category_id'] = $data['sub_category_id'] ? $data['sub_category_id'] : null;

        // profile
        $profile = $this->vendorProfileRepository->create($data);

        if( isset($data["medias_single"]) && !empty($data["medias_single"]) ) {
            $eventImg = $data["medias_single"];

            $profile->files()->sync([ $eventImg['image'] => ['imageable_type' => 'Modules\\Vendor\\Entities\\VendorProfile', 'zone' => 'image']]);

            //inject file to media module view
            $image = $this->file->findFileByZoneForEntity('image', $profile);
            if($image){
                $advService = app(AdvertisementService::class);
                $path = $advService->getPathImage($image);
                $pathResizeThumb = getPathThumbImage($image, 'resizeThumb');
                $pathThumb = getPathThumbImage($image, 'smallThumb');
                // $imageInfo = @getimagesize($advService->convertLinkS3ToHttp($image->path));
                $imageInfo = @getimagesize(convertLinkS3ToHttp($this->mediaService->getImage($pathResizeThumb)));
                $dimension = $imageInfo ? json_encode(array('width' => $imageInfo[0], 'height' => $imageInfo[1])) : "";

                $profile->photo = $path;
                $profile->photo_resize = $pathResizeThumb;
                $profile->photo_thumb = $pathThumb;
                $profile->dimension = $dimension;
                $profile->save();
            }
        }

        // category
        $this->vendorCategoryRepository->create($data);
        
        // location
        $location = $this->vendorLocationRepository->create($data);

        // phone
        // $data = array_merge($data, ['phone_number' => $data['business_phone'], 'country_code' => $data['business_code'], 'is_verifyed' => '1']);
        // $this->vendorPhoneRepository->create($data);
        if(isset($business_phone) && !empty($business_phone)){
            foreach ($business_phone as $itemPhone) {
                $this->vendorPhoneRepository->create($data + ['phone_number' => $itemPhone, 'country_code' => $data['business_code'], 'is_verifyed' => '1', 'location_id' => $location->id]);
            }
        }

        // creating subscriptions
        $user = User::where('email', $item->email)->first();
        $plan = VendorPlan::where('name', 'Free')->first();
        $user->newSubscription('main', $plan)->create();

        // add credit point
        $this->vendorCreditRepository->create(['vendor_id' => $item->id, 'amount' => '0', 'point' => setting('vendor::credit_point') ? setting('vendor::credit_point') : 10]);

        // send email to admin
        $query = "SELECT  u.*
                    FROM users u 
                    LEFT JOIN role_users r ON r.user_id = u.id
                    WHERE r.role_id = 1 AND u.is_deleted IS NULL
                    ORDER BY u.id ASC";
        $users = \DB::select($query);

        if(count($users)){
            $usersArr = [];
            foreach ($users as $key => $user) {
                $usersArr[] = $user->email;
            }
            event(new VendorWasCreated($item, $usersArr));
        }        
        return $item;
    }

    /**
     * @param $model
     * @param  array  $data
     * @return object
     */
    public function update($model, $data)
    {
        $data['sub_category_id'] = $data['sub_category_id'] ? $data['sub_category_id'] : null;
        
        $address = $data['business_address'];
        // get location from business address only
        // if(isset($data['city_id'])) $address.= ', ' . $this->getCityById($data['city_id'])->name;
        // if(isset($data['state_id'])) $address.= ', ' . $this->getStateById($data['state_id'])->name;
        // if(isset($data['country_id'])) $address.= ', ' . $this->getCountryById($data['country_id'])->name;

        $location = $this->getLocation($address);
        if(!empty($location)){
            $data = array_merge($data, [
                    'lat' => $location['lat'],
                    'lng' => $location['long'],
                ]);
        }

        // update vendor item        
        $this->checkForNewPassword($data);
        $model->update($data);

        $image = $this->file->findFileByZoneForEntity('image', $model->vendorProfile);
        $path = null;
        $pathResizeThumb = null;
        $pathThumb = null;
        $dimension = null;
        if($image){
            $advService = app(AdvertisementService::class);
            $path = $advService->getPathImage($image);
            $pathResizeThumb = getPathThumbImage($image, 'resizeThumb');
            $pathThumb = getPathThumbImage($image, 'smallThumb');
            $imageInfo = @getimagesize(convertLinkS3ToHttp($this->mediaService->getImage($pathResizeThumb)));
            if($imageInfo){
                $dimension = json_encode(array('width' => $imageInfo[0], 'height' => $imageInfo[1]));
            }
        }

        // $zip_code = $data['zip_code'];
        // $business_phone = $this->getPhoneRequest($data['business_phone']);
        // unset($data['business_phone']);

        $primaryLocation = $this->vendorLocationRepository->findByAttributes(array('user_id' => $model->id, 'is_primary' => 1));

        // jso encode social media data
        $social_media_link = json_encode([
            'facebook' => strtolower($data['social_media_link_facebook']),
            'twitter' => strtolower($data['social_media_link_twitter']),
            'instagram' => strtolower($data['social_media_link_instagram']),
            'pinterest' => strtolower($data['social_media_link_pinterest']),
        ]);
        $data = array_merge($data, ['user_id' => $model->id, 'status' => '1','is_primary' => '1', 'photo' => $path, 'photo_resize' => $pathResizeThumb, 'photo_thumb' => $pathThumb, 'dimension' => $dimension, 'social_media_link' => $social_media_link]);

        // profile
        $profile = $this->vendorProfileRepository->findByAttributes(array('user_id' => $model->id));
        if($profile){
            // if($primaryLocation->city_id != $data['city_id']){
            //     unset($data['zip_code']);
            // }else{
            //     $data['business_phone'] = $business_phone ? $business_phone[0] : "";
            // }
            $profile->update($data);
            // unset($data['business_phone']);
        }else{
            // $data['business_phone'] = $business_phone ? $business_phone[0] : "";
            $data = array_merge($data, ['rating_points' => '0']);
            $this->vendorProfileRepository->create($data);
            unset($data['rating_points']);
            // unset($data['business_phone']);
        }
        unset($data['lat']);
        unset($data['lng']);

        // category
        $category = $this->vendorCategoryRepository->findByAttributes(array('user_id' => $model->id, 'is_primary' => 1));
        if($category){
            $category->update($data);
        }else{
            $newCateogry = $this->vendorCategoryRepository->create($data);
        }
        
        // location
        // $location = $this->vendorLocationRepository->findByAttributes(array('user_id' => $model->id, 'is_primary' => 1));
        // if(!$location){
        //     $this->vendorLocationRepository->create($data + ['zip_code' => $zip_code, 'business_phone' => $business_phone]);
        // }
        // $model->vendorLocation()->where('city_id', $data['city_id'])->where('user_id', $model->id)->update(['zip_code' => $zip_code]);

        // phone
        // $phone = $this->vendorPhoneRepository->findByAttributes(array('user_id' => $model->id, 'is_primary' => 1));
        // if($phone){
        //     if($primaryLocation->city_id == $data['city_id']){
        //         $data = array_merge($data, ['phone_number' => $business_phone , 'country_code' => $data['business_code']]);
        //         $phone->update($data);
        //     }
        // }else{
        //     $data = array_merge($data, ['phone_number' => $business_phone , 'country_code' => $data['business_code'], 'is_verifyed' => '1']);
        //     $this->vendorPhoneRepository->create($data);
        // }
        
        // $location = VendorLocation::where('country_id', $data['country_id'])
        //                             ->where('city_id', $data['city_id'])
        //                             ->where('user_id', $model->id)
        //                             ->first();
        // if(isset($business_phone) && !empty($business_phone)){
        //     $this->updateVendorPhone($model->id, $business_phone, $location, $data['business_code']);
        // }else{
        //     VendorPhone::where('user_id', $model->id)
        //                 ->where('location_id', $location->id)
        //                 ->where('status', 1)
        //                 ->whereNull('is_deleted')
        //                 ->delete();
        //     VendorPhone::where('user_id', $model->id)
        //                 ->whereNull('location_id')
        //                 ->where('status', 1)
        //                 ->whereNull('is_deleted')
        //                 ->delete();
        // }

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

        // delete vendor profile
        $profile = $this->vendorProfileRepository->findByAttributes(array('user_id' => $model->id));
        if(count($profile)) $profile->delete();

        // delete vendor category
        $category = $this->vendorCategoryRepository->findByAttributes(array('user_id' => $model->id));
        if(count($category)) $category->delete();

        // delete vendor location
        $location = $this->vendorLocationRepository->findByAttributes(array('user_id' => $model->id));
        if(count($location)) $location->delete();

        // delete vendor portfolio
        // $portfolio = $this->portfolioRepository->findByAttributes(array('vendor_id' => $model->id));
        // if(count($portfolio)) $portfolio->delete();
        $query = "UPDATE mm__vendors_portfolios
                    SET is_deleted = '".$now."',
                        status = '0'
                    WHERE id = '".$model->id."'
                    ";
        \DB::select($query);

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
     * [getLocation description]
     * @param  [string] $address [address]
     * @return [array]          [array location]
     */
    public function getLocation($address) {
        $result = array();
        //  $address = '201 S. Division St., Ann Arbor, MI 48104'; // Google HQ
        if (empty($address)) {
            return $result;
        }
        $prepAddr = str_replace(' ', '+', $address);
        $prepAddr = urlencode($address);
        
        $arrContextOptions=array(
            "ssl"=>array(
                "verify_peer"=>false,
                "verify_peer_name"=>false,
            ),
        );  

        $url = 'https://maps.googleapis.com/maps/api/geocode/json?address='.$prepAddr.'&sensor=false';
        
        $geocode = file_get_contents($url, false, stream_context_create($arrContextOptions));
        $output = json_decode($geocode);
        if (!empty($output->results)) {
            $result = array();
            $i = 0;
        
            $lat = $output->results[0]->geometry->location->lat;
            $long = $output->results[0]->geometry->location->lng;
        
            $result = array(
                    'lat' => $lat,
                    'long' => $long
            );
            return $result;
        } else {
            return $result;
        }
    }

    public function getVendorPrimaryLocation(Vendor $vendor){
        return $this->vendorLocationRepository->findByAttributes(array('user_id' => $vendor->id, 'is_primary' => 1));
    } 

    public function getVendorLocation($vendor_id, $city_id){
        return VendorLocation::where('user_id', $vendor_id)
                            ->whereNull('is_deleted')
                            ->where('status', 1)
                            ->where('city_id', $city_id)
                            ->first();
    }

    public function getVendorCountriesArr(Vendor $vendor){
        $location = VendorLocation::where('user_id', $vendor->id)
                                    ->whereNull('is_deleted')
                                    ->where('status', 1)
                                    ->get();

        if(count($location)){
            $temp = $data = [];
            foreach ($location as $item) {
                if(!in_array($item->country_id, $temp)){
                    $temp[] = $item->country_id;
                    $data += [
                        $item->country_id => $item->country->name
                    ];
                }
            }
            return $data;
        }

        return array();
    }

    public function getVendorCitiesArr(Vendor $vendor, $country_id){
        $location = VendorLocation::where('user_id', $vendor->id)
                                    ->whereNull('is_deleted')
                                    ->where('status', 1)
                                    ->where('country_id', $country_id)
                                    ->get();
        if(count($location)){
            $temp = $data = [];
            foreach ($location as $item) {
                if(!in_array($item->city_id, $temp) && $item->city){
                    $temp[] = $item->city_id;
                    $data += [
                        $item->city_id => $item->city->name,
                    ];
                }
            }
            return $data;
        }

        return array();
    }

    public function getCitiesArr($country_id){
        $data= [];
        $states = State::where('active', 1)
                     ->where('country_id', $country_id)
                     ->get();
        if(count($states)){
            foreach ($states as $sItem) {
                $cities = City::where('active', 1)
                                ->where('state_id', $sItem->id)
                                ->get();
                if(count($cities)){
                    foreach ($cities as $item) {
                        $data += [
                            $item->id => $item->name,
                        ];
                    }
                    return $data;
                }
            }
        }
        
        return array();
    }

    public function getVendorPhonecodesArr(Vendor $vendor){
        $data = [];
        $location = VendorLocation::where('user_id', $vendor->id)
                                ->whereNull('is_deleted')
                                ->where('status', 1)
                                ->get();
        if(count($location)){
            $temp = [];
            foreach ($location as $item) {
                if(!in_array($item->country_id, $temp)){
                    $temp[] = $item->country_id;
                    $data  += [
                        $item->country->phonecode => '+'.$item->country->phonecode
                    ];
                }
            }
            return $data;
        }

        return array();
    }

    public function getVendorPhonecodesAjax(Vendor $vendor){
        $phonecodesArr = [];
        $location = VendorLocation::where('user_id', $vendor->id)
                                ->whereNull('is_deleted')
                                ->where('status', 1)
                                ->get();

        if(count($location)){
            $temp = [];
            foreach ($location as $item) {
                if(!in_array($item->country_id, $temp)){
                    $temp[] = $item->country_id;
                    $phonecodesArr[] = [
                        'id' => $item->country->id,
                        'name' => $item->country->name,
                        'phonecode' => $item->country->phonecode,
                    ];  
                }
            } 
        }
        return $phonecodesArr;
    }

    public function getPhonecodesAjax(){
        $data = [];

        $location = Country::where('active', 1)
                                ->get();
        foreach ($location as $item) {
            $data[] = [
                'id' => $item->id,
                'name' => $item->name,
                'phonecode' => $item->phonecode,
            ];
        }

        return $data;
    }

    public function getPhonecodesArr(){
        $data = [];

        $location = Country::where('active', 1)
                                ->get();
        foreach ($location as $item) {
            $data[] = [
                $item->phonecode => '+'.$item->phonecode
            ];
        }

        return $data;
    }

    public function getVendorPhonecodes(Vendor $vendor){
        $location = VendorLocation::where('user_id', $vendor->id)
                                    ->whereNull('is_deleted')
                                    ->where('status', 1)
                                    ->get();

        if(count($location)){
            $temp = $data = [];
            foreach ($location as $item) {
                if(!in_array($item->country_id, $temp)){
                    $temp[] = $item->country_id;
                    $data  += [
                        'id' => $item->country->phonecode,
                        'phonecode' => '+'.$item->country->phonecode,
                    ];
                }
            }
            return $data;
        }

        return array();
    }

    public function getVendorPhones($location_id, $vendor_id){
        $phones = VendorPhone::where('user_id', $vendor_id)
                                ->where('location_id', $location_id)
                                ->whereNull('is_deleted')
                                ->where('status', 1)
                                ->get();

        if(count($phones)){
            return $phones;
        }else{
            $phones = VendorPhone::where('user_id', $vendor_id)
                                ->whereNull('is_deleted')
                                ->whereNull('location_id')
                                ->where('status', 1)
                                ->get();
        }
        return count($phones) ? $phones : array();
    }

    public function getVendorPhonesArr($location_id, $vendor_id){
        $phones = VendorPhone::where('user_id', $vendor_id)
                                ->where('location_id', $location_id)
                                ->whereNull('is_deleted')
                                ->where('status', 1)
                                ->get();
        $data = [];
        
        if(count($phones)){
            foreach ($phones as $item) {
                $data[] = [
                    'id' => $item->id,
                    'country_code' => $item->country_code,
                    'phone_number' => $item->phone_number,
                ];
            }
        }   

        return $data;                        
    }

    public function getPhonecodeByCountry($country_id){
        $item = Country::where('id', $country_id)->first();
        if(count($item)){
            return $item->phonecode;
        }
        return false;
    }

    public function getVendorCategory(Vendor $vendor){
        return $this->vendorCategoryRepository->findByAttributes(array('user_id' => $vendor->id, 'is_primary' => 1));
    }

    public function getPortfolio(Vendor $vendor){
        return $this->portfolioRepository->findByAttributes(array('vendor_id' => $vendor->id));
    }
    /**
     * [getCountryArray description]
     * @return [array] [description]
     */
    public function getCountryArray(){
        $results = Country::where('active', 1)->orderBy('name', 'asc')->get();
        $data = [];
        if(!empty($results)){
            foreach ($results as $item)
            {
                $data += [
                    $item->id => $item->name,
                ];
            }
        }
        asort($data);
        return $data;
    }

    /**
     * [getStateArray description]
     * @param  [type] $country_id [description]
     * @return [type]             [description]
     */
    public function getStateArray($country_id){
        $results = State::where('country_id', $country_id)->orderBy('name', 'asc')->get();
        $data = [];
        if(!empty($results)){
            foreach ($results as $item)
            {
                $data += [
                    $item->id => $item->name,
                ];
            }
        }
        asort($data);
        return $data;
    }

    /**
     * [getCityArray description]
     * @param  [type] $state_id [description]
     * @return [type]           [description]
     */
    public function getCityArray($state_id){
        $results = City::where('state_id', $state_id)->orderBy('name', 'asc')->get();
        $data = [];
        if(!empty($results)){
            foreach ($results as $item)
            {
                $data += [
                    $item->id => $item->name,
                ];
            }
        }
        asort($data);
        return $data;
    }

    /**
     * [getCityArray description]
     * @param  [type] $state_id [description]
     * @return [type]           [description]
     */
    public function getVendorCategoryArray($user_id){
        $results = VendorCategory::whereHas('category', function($query){
                                    $query->where('status', 1);
                                })
                                ->where('user_id', $user_id)
                                ->where('status', 1)->get();
        $data = [];
        if(count($results)){
            foreach ($results as $item)
            {
                $data += [
                    $item->category->id => $item->category->name,
                ];
            }
        }
        asort($data);
        return $data;
    }

        /**
     * [getCountryById description]
     * @return [array] [description]
     */
    public function getCountryById($id){
        return Country::where('id', $id)->first();
    }

    /**
     * [getStateById description]
     * @param  [type] $country_id [description]
     * @return [type]             [description]
     */
    public function getStateById($id){
        return State::where('id', $id)->first();
    }

    /**
     * [getCityById description]
     * @param  [type] $state_id [description]
     * @return [type]           [description]
     */
    public function getCityById($id){
        return City::where('id', $id)->first();
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

    /**
     * [updateVendorCredit update vendor credit]
     * @return [type] [description]
     */
    public function updateVendorCredit(){
        $vendors = Vendor::whereHas('role', function($query){
                                $query->where('role_id', 3);
                            })
                            ->whereNull('is_deleted')->get();
        if(count($vendors)){
            foreach($vendors as $item){
                $credit = VendorCredit::where('vendor_id', $item->id)->first();
                if(!count($credit)){
                    $this->vendorCreditRepository->create(['vendor_id' => $item->id, 'amount' => 0, 'point' => 10]);
                }
            }

        }
    }

    public function getPhoneRequest($phone_number){
        if(count($phone_number) == 1 && !$phone_number[0]){
            return array();
        }

        $arr = [];
        foreach ($phone_number as $item) {
            if($item){
                $arr[] = $item;
            }
        }
        return $arr;
    }

    public function updateVendorPhone($vendor_id, $phones, $location, $country_code){
        $vendorPhones = VendorPhone::where('user_id', $vendor_id)
                                    ->where('location_id', $location->id)
                                    ->where('status', 1)
                                    ->whereNull('is_deleted')
                                    ->get();
        if(count($vendorPhones)){
            $this->syncVendorPhone($phones, $vendorPhones, $location, $country_code);
        }else{
            if($location->is_primary == 1){
                $vendorPhones = VendorPhone::where('user_id', $vendor_id)
                                    ->where('location_id', $location->id)
                                    ->where('status', 1)
                                    ->whereNull('is_deleted')
                                    ->get();
                if(count($vendorPhones)){
                    $this->syncVendorPhone($phones, $vendorPhones, $location, $country_code);
                }else{
                    foreach ($phones as $k=>$item) {
                        $is_primary = $k==0 ? 1 : 0;
                        $newItem = new VendorPhone;
                        $newItem->phone_number = $item;
                        $newItem->country_code = $country_code;
                        $newItem->is_primary = $is_primary;
                        $newItem->is_verifyed = 1;
                        $newItem->status = 1;
                        $newItem->is_deleted = null;
                        $newItem->user_id = $vendor_id;
                        $newItem->location_id = $location->id;
                        $newItem->save();
                    }
                }
            }else{
                foreach ($phones as $k=>$item) {
                    $newItem = new VendorPhone;
                    $newItem->phone_number = $item;
                    $newItem->country_code = $country_code;
                    $newItem->is_primary = 0;
                    $newItem->is_verifyed = 1;
                    $newItem->status = 1;
                    $newItem->is_deleted = null;
                    $newItem->user_id = $vendor_id;
                    $newItem->location_id = $location->id;
                    $newItem->save();
                }
            }
        }
    }

    public function syncVendorPhone($phones, $vendorPhones, $location, $country_code){
        if( count($phones) == count($vendorPhones) ){
            foreach ($vendorPhones as $k=>$item) {
                $is_primary = ($k==0 && $location->is_primary == 1) ? 1 : 0;
                $item->phone_number = $phones[$k];
                $item->country_code = $country_code;
                $item->is_primary = $is_primary;
                $item->save();
            }
        }elseif( count($phones) < count($vendorPhones) ){
            foreach ($vendorPhones as $k=>$item) {
                if(isset($phones[$k]) && !empty($phones[$k])){
                    $is_primary = ($k==0 && $location->is_primary == 1) ? 1 : 0;
                    $item->phone_number = $phones[$k];
                    $item->country_code = $country_code;
                    $item->is_primary = $is_primary;
                    $item->save();
                }else{
                    $item->delete();
                }
            }
        }else{
            foreach ($phones as $k=>$item) {
                if($k + 1 <= count($vendorPhones) ){
                    $userphone = VendorPhone::where('id', $vendorPhones[$k]->id)->first();
                    $is_primary = ($k==0 && $location->is_primary == 1) ? 1 : 0;
                    $userphone->phone_number = $item;
                    $userphone->country_code = $country_code;
                    $userphone->is_primary = $is_primary;
                    $userphone->save();
                }else{
                    $is_primary = ($k==0 && $location->is_primary == 1) ? 1 : 0;
                    $newItem = new VendorPhone;
                    $newItem->phone_number = $item;
                    $newItem->country_code = $country_code;
                    $newItem->is_primary = $is_primary;
                    $newItem->is_verifyed = 1;
                    $newItem->status = 1;
                    $newItem->is_deleted = null;
                    $newItem->user_id = $location->user_id;
                    $newItem->location_id = $location->id;
                    $newItem->save();
                }
            }
        }
    }
}