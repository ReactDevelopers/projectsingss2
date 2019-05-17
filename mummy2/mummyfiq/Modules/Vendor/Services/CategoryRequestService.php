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
use Modules\Vendor\Entities\Country;
use Modules\Vendor\Entities\State;
use Modules\Vendor\Entities\City;
use Modules\Vendor\Entities\User;
use Modules\Vendor\Repositories\VendorRepository;
use Modules\Vendor\Repositories\VendorProfileRepository;
use Modules\Vendor\Repositories\VendorLocationRepository;
use Modules\Vendor\Repositories\VendorCategoryRepository;
use Modules\Vendor\Repositories\VendorPhoneRepository;
use Modules\Advertisement\Services\AdvertisementService;
use Modules\Portfolio\Repositories\PortfolioRepository;
use Carbon\Carbon;

class CategoryRequestService {

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
     * @var FileService
     */
    private $fileService;

    /**
     * @var FileRepository
     */
    private $file;

    public function __construct( VendorRepository $vendorRepository, VendorProfileRepository $vendorProfileRepository, VendorLocationRepository $vendorLocationRepository, VendorCategoryRepository $vendorCategoryRepository, VendorPhoneRepository $vendorPhoneRepository, FileService $fileService, FileRepository $file, PortfolioRepository $portfolioRepository) {
        $this->vendorRepository             = $vendorRepository;
        $this->vendorProfileRepository      = $vendorProfileRepository;
        $this->vendorLocationRepository     = $vendorLocationRepository;
        $this->vendorCategoryRepository     = $vendorCategoryRepository;
        $this->vendorPhoneRepository        = $vendorPhoneRepository;
        $this->portfolioRepository          = $portfolioRepository;
        $this->fileService                  = $fileService;

    }

    public function all(){
        return VendorCategory::whereHas('category', function($query){
                                    $query->where('status', 1);
                                    $query->whereNull('is_deleted');
                                })
                                ->whereHas('vendor', function($query){
                                    $query->where('status', 1);
                                    $query->whereNull('is_deleted');
                                })
                                ->where(function($query){
                                        $query->whereNull('status');
                                        $query->orWhere('status', 2);
                                        $query->orWhere('status', 3);
                                    })
                                ->whereNull('is_deleted')
                                ->get();
    }

    public function count(){
        return VendorCategory::whereHas('category', function($query){
                                    $query->where('status', 1);
                                    $query->whereNull('is_deleted');
                                })
                                ->whereHas('vendor', function($query){
                                    $query->where('status', 1);
                                    $query->whereNull('is_deleted');
                                })
                                ->where(function($query){
                                        $query->whereNull('status');
                                        $query->orWhere('status', 2);
                                        $query->orWhere('status', 3);
                                    })
                                ->whereNull('is_deleted')
                                ->count();
    }

    public function approve($model){
        $model->status = 1;
        $model->save();
        return true;
    }

    public function reject($model){
        $model->status = 3;
        $model->save();
        return true;
    }

        /**
     * @param  Model $model
     * @return bool
     */
    public function destroy($model)
    {
        return $model->delete();
    }

    public function getItems($option = 'list', $request){
        $limit = $request->get('limit') ? $request->get('limit') : 10;
        $keyword = $request->get('keyword') ? $request->get('keyword') : "";

        $array_field = array('id', 'business_name', 'category_name');
        $order_field = $request->get('order_field') && in_array($request->get('order_field'), $array_field) ? $request->get('order_field') : 'id';
        $sort = $request->get('sort') ? $request->get('sort') : 'DESC';

        if($option == 'list'){
            $items = VendorCategory::select('mm__vendors_category.*', 'mm__vendors_profile.business_name', 'mm__categories.name as category_name')
                         ->join('mm__vendors_profile', 'mm__vendors_profile.user_id', '=', 'mm__vendors_category.user_id')
                         ->join('users', 'users.id', '=', 'mm__vendors_category.user_id')
                         ->join('mm__categories', 'mm__categories.id', '=', 'mm__vendors_category.category_id')
                         ->where(function($query) use ($keyword) {
                            $query->where('mm__vendors_category.id', '=', $keyword);
                            $query->orWhere('mm__vendors_profile.business_name', 'like', '%'.$keyword.'%');
                            $query->orWhere('mm__categories.name', 'like', '%' . $keyword . '%');
                        })
                         ->where(function($query) use ($keyword) {
                            $query->whereNull('mm__vendors_category.status');
                            $query->orWhere('mm__vendors_category.status', 2);
                            $query->orWhere('mm__vendors_category.status', 3);
                        })
                         ->where(function($query) use ($keyword) {
                            $query->whereNull('mm__categories.is_deleted');
                            $query->orWhere('mm__categories.status', 1);
                        })
                         ->where(function($query) use ($keyword) {
                            $query->whereNull('users.is_deleted');
                            $query->orWhere('users.status', 1);
                        })
                        ->orderBy($order_field, $sort)
                        ->paginate($limit);

            return $items;
        }else{
            return VendorCategory::select('mm__vendors_category.*', 'mm__vendors_profile.business_name', 'mm__categories.name as category_name')
                         ->join('mm__vendors_profile', 'mm__vendors_profile.user_id', '=', 'mm__vendors_category.user_id')
                         ->join('users', 'users.id', '=', 'mm__vendors_category.user_id')
                         ->join('mm__categories', 'mm__categories.id', '=', 'mm__vendors_category.category_id')
                         ->where(function($query) use ($keyword) {
                            $query->where('mm__vendors_category.id', '=', $keyword);
                            $query->orWhere('mm__vendors_profile.business_name', 'like', '%'.$keyword.'%');
                            $query->orWhere('mm__categories.name', 'like', '%' . $keyword . '%');
                        })
                         ->where(function($query) use ($keyword) {
                            $query->whereNull('mm__vendors_category.status');
                            $query->orWhere('mm__vendors_category.status', 2);
                            $query->orWhere('mm__vendors_category.status', 3);
                        })
                         ->where(function($query) use ($keyword) {
                            $query->whereNull('mm__categories.is_deleted');
                            $query->orWhere('mm__categories.status', 1);
                        })
                         ->where(function($query) use ($keyword) {
                            $query->whereNull('users.is_deleted');
                            $query->orWhere('users.status', 1);
                        })
                         ->count();
        }
        
    }
}