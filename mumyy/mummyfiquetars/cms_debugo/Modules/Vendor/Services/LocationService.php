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
use Modules\Vendor\Services\VendorService;
use Carbon\Carbon;

class LocationService {

    /**
     *
     * @var VendorLocationRepository
     */
    private $repository;

    /**
     *
     * @var VendorService
     */
    private $vendorService;

    public function __construct(VendorLocationRepository $repository, VendorService $vendorService) {
        $this->repository = $repository;
        $this->vendorService = $vendorService;
    }


    public function getItems($option = 'list', $request, Vendor $vendor){
        $limit = $request->get('limit') ? $request->get('limit') : 10;
        $keyword = $request->get('keyword') ? $request->get('keyword') : "";

        $array_field = array('id', 'country', 'city');
        $order_field = $request->get('order_field') && in_array($request->get('order_field'), $array_field) ? $request->get('order_field') : 'id';
        $sort = $request->get('sort') ? $request->get('sort') : 'DESC';

        if($option == 'list'){
            $items = VendorLocation::select('mm__vendors_location.*', 'mm__new_countries.name as country', 'mm__new_countries_cities.name as city')
            						->join('mm__new_countries', 'mm__new_countries.id', '=', 'mm__vendors_location.country_id', 'left')
                         			->join('mm__new_countries_cities', 'mm__new_countries_cities.id', '=', 'mm__vendors_location.city_id', 'left')
            						->whereNull('is_deleted')
                         			->where('user_id', $vendor->id)
                         			->where(function($query) use ($keyword) {
			                            $query->where('mm__vendors_location.id', '=', $keyword);
			                            $query->orWhere('mm__new_countries.name', 'like', '%'.$keyword.'%');
			                            $query->orWhere('mm__new_countries_cities.name', 'like', '%' . $keyword . '%');
			                        })
			                        ->orderBy($order_field, $sort)
			                        ->paginate($limit);

            return $items;
        }else{
            return VendorLocation::select('mm__vendors_location.*', 'mm__new_countries.name as country', 'mm__new_countries_cities.name as city')
            						->join('mm__new_countries', 'mm__new_countries.id', '=', 'mm__vendors_location.country_id', 'left')
                         			->join('mm__new_countries_cities', 'mm__new_countries_cities.id', '=', 'mm__vendors_location.city_id', 'left')
            						->whereNull('is_deleted')
                         			->where('user_id', $vendor->id)
                         			->where(function($query) use ($keyword) {
			                            $query->where('mm__vendors_location.id', '=', $keyword);
			                            $query->orWhere('mm__new_countries.name', 'like', '%'.$keyword.'%');
			                            $query->orWhere('mm__new_countries_cities.name', 'like', '%' . $keyword . '%');
			                        })
                         			->count();
        }
        
    }

    public function create($data){
        $dataUpdate = [
            'country_id' => $data['country_id'],
            'states_id' => $data['country_id'],
            'city_id' => $data['city_id'],
            'zip_code' => $data['zip_code'],
            'user_id' => $data['vendor_id'],
            'is_primary' => 0,
            'status' => 1,
        ];

        // get location from city and country if chnage new location
        $country = Country::where('id', $data['country_id'])->first();
        $city = City::where('id', $data['city_id'])->first();
        $address = (count($city) ? $city->name . ", " : " ") . (count($country) ? $country->name : "");
        if(!empty($address)){
            $location = $this->vendorService->getLocation($address);
            if(!empty($location)){
                $dataUpdate = array_merge($dataUpdate, [
                        'lat' => $location['lat'],
                        'lng' => $location['long'],
                    ]);
            }
        }  
        // check primary
        if(isset($data['is_primary']) && !empty($data['is_primary'])){
            // remove other location from is_primary
            VendorLocation::where('user_id', $data['vendor_id'])->update([ 'is_primary' => 0 ]);
            
            $dataUpdate['is_primary'] = 1 ;
        } 
        
        $model = $this->repository->create($dataUpdate);

        // update phone number
        $business_phone = $this->vendorService->getPhoneRequest($data['business_phone']);
        $data['business_code'] = str_replace("+", "", $data['business_code']);
    
        if(isset($business_phone) && !empty($business_phone)){
            $this->vendorService->updateVendorPhone($model->user_id, $business_phone, $model, $data['business_code']);
        }else{
            VendorPhone::where('user_id', $model->user_id)
                        ->where('location_id', $model->id)
                        ->where('status', 1)
                        ->whereNull('is_deleted')
                        ->delete();
            VendorPhone::where('user_id', $model->user_id)
                        ->whereNull('location_id')
                        ->where('status', 1)
                        ->whereNull('is_deleted')
                        ->delete();
        }
    }

    public function update($model, $data){
        $dataUpdate = [
            'country_id' => $data['country_id'],
            'states_id' => $data['country_id'],
            'city_id' => $data['city_id'],
            'zip_code' => $data['zip_code'],
        ];

        // get location from city and country if chnage new location
        if($data['country_id'] != $model->country_id || $data['city_id'] != $model->city_id){
            $country = Country::where('id', $data['country_id'])->first();
            $city = City::where('id', $data['city_id'])->first();
            $address = (count($city) ? $city->name . ", " : " ") . (count($country) ? $country->name : "");
            if(!empty($address)){
                $location = $this->vendorService->getLocation($address);
                if(!empty($location)){
                    $dataUpdate = array_merge($dataUpdate, [
                            'lat' => $location['lat'],
                            'lng' => $location['long'],
                        ]);
                }
            }  
            
        }

        // check primary
        if(isset($data['is_primary']) && !empty($data['is_primary'])){
            // remove other location from is_primary
            VendorLocation::where('user_id', $model->user_id)->update([ 'is_primary' => 0 ]);                

            $dataUpdate = array_merge($dataUpdate, [ 'is_primary' => 1 ]);
        } 
        
        $this->repository->update($model, $dataUpdate);

        // update phone number
        $business_phone = $this->vendorService->getPhoneRequest($data['business_phone']);
        $data['business_code'] = str_replace("+", "", $data['business_code']);
    
        if(isset($business_phone) && !empty($business_phone)){
            $this->vendorService->updateVendorPhone($model->user_id, $business_phone, $model, $data['business_code']);
        }else{
            VendorPhone::where('user_id', $model->user_id)
                        ->where('location_id', $model->id)
                        ->where('status', 1)
                        ->whereNull('is_deleted')
                        ->delete();
            VendorPhone::where('user_id', $model->user_id)
                        ->whereNull('location_id')
                        ->where('status', 1)
                        ->whereNull('is_deleted')
                        ->delete();
        }
    }

    public function destroy($model){
        return $model->delete();
    }
}