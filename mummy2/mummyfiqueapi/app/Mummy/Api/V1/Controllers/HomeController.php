<?php

namespace App\Mummy\Api\V1\Controllers;

use Illuminate\Http\Request;

use Carbon\Carbon;
use Illuminate\Pagination\Paginator;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;
use App\Mummy\Api\V1\Entities\AdvertisementItem;
use App\Mummy\Api\V1\Entities\UserRole;
use App\Mummy\Api\V1\Entities\Customer;
use App\Mummy\Api\V1\Entities\CustomerChildrens;
use App\Mummy\Api\V1\Entities\CustomerActivitive;
use App\Mummy\Api\V1\Entities\UserReview;
use App\Mummy\Api\V1\Entities\Home\Category;
use App\Mummy\Api\V1\Entities\Home\City;
use App\Mummy\Api\V1\Entities\Home\Country;
use App\Mummy\Api\V1\Entities\Home\PriceRange;
use App\Mummy\Api\V1\Entities\Home\SubCategory;
use App\Mummy\Api\V1\Entities\SendMessage;
use App\Mummy\Api\V1\Entities\UserReport;
use App\Mummy\Api\V1\Entities\ReviewReply;
use App\Mummy\Api\V1\Entities\Vendors\VendorLocation;
use App\Mummy\Api\V1\Entities\Vendors\VendorPortfolio;
use App\Mummy\Api\V1\Entities\Vendors\VendorPortfolioMedia;
use App\Mummy\Api\V1\Entities\Vendors\VendorPricelist;
use App\Mummy\Api\V1\Entities\PlanSubscription;
use App\Mummy\Api\V1\Entities\Vendors\VendorComment;
use App\Mummy\Api\V1\Entities\Vendor;
use App\Mummy\Api\V1\Requests\Favourite\FavouriteRequest;
use App\Mummy\Api\V1\Requests\Search\SearchNearbyRequest;
use App\Mummy\Api\V1\Requests\Search\SearchByNameRequest;
use App\Mummy\Api\V1\Requests\Portfolio\PortfolioRequest;
use App\Mummy\Api\V1\Requests\Message\SendMessageRequest;
use App\Mummy\Api\V1\Requests\Review\ReviewRequest;
use App\Mummy\Api\V1\Requests\Review\WriteReviewRequest;
use App\Mummy\Api\V1\Requests\Review\SendReviewRequest;
use App\Mummy\Api\V1\Requests\Review\EditReviewRequest;
use App\Mummy\Api\V1\Requests\Review\ReportReviewRequest;
use App\Mummy\Api\V1\Requests\Comment\DeleteCommentRequest;
use App\Mummy\Api\V1\Repositories\VendorRepository;

use App\Mummy\Api\V1\Entities\Vendors\VendorProfile;
use Helper;
use DB;
use Illuminate\Mail\Mailer;
use Illuminate\Mail\Message;
use App\Mummy\Api\V1\Service\HomeService;
use App\Mummy\Api\V1\Events\SendMessageVendor;
use App\Mummy\Api\V1\Events\SendReviewVendor;

class HomeController extends ApiController
{
    protected $mailer;
    protected $service;
    protected $vendorRepository;
    
    public function __construct(Mailer $mailer, HomeService $service, VendorRepository $vendorRepository)
    {
        $this->mailer = $mailer;
        $this->service = $service;
        $this->vendorRepository = $vendorRepository;
    }
    /**
     * @SWG\Get(
     *   path="/v1/homes/userBadgeNumber",
     *   description="",
     *   summary="",
     *   operationId="api.v1.homes.userBadgeNumber",
     *   produces={"application/json"},
     *   tags={"Home"},
     *   @SWG\Response(response=401, description="unauthorized"),
     *   @SWG\Response(response=200, description="Success"),
     *   security={
     *       {"api_key": {}}
     *   }
     * )
     */
    public function userBadgeNumber(Request $request)
    {
        $customer = $request->user();
        $isCheck = Helper::promptReviewMessage($customer);
        $query = "select * from mm__send_message Where receiver_id = $customer->id AND is_customer_read = 0 AND is_customer_deleted IS NULL";
        $badge = \DB::select($query);
        return response([
                    'data' => [
                    'badgeNumber'   =>  count($badge),
                    'isCheck'   =>  $isCheck,
                    ],
                ],Response::HTTP_OK);
    }
    /**
     * @SWG\Get(
     *   path="/v1/homes/showPopup",
     *   description="",
     *   summary="",
     *   operationId="api.v1.homes.showPopup",
     *   produces={"application/json"},
     *   tags={"Home"},
     *   @SWG\Response(response=401, description="unauthorized"),
     *   @SWG\Response(response=200, description="Success"),
     *   security={
     *       {"api_key": {}}
     *   }
     * )
     */
    public function showPopup(Request $request)
    {
        $customer = $request->user();
        $customer->is_show_popup = 1;
        $customer->save();
        return response([
                    'data' => [
                    'message'   =>  'Change status Sucess'
                    ],
                ],Response::HTTP_OK);
    }
    /**
     * @SWG\Get(
     *   path="/v1/homes/getHome?take={take}&page={page}",
     *   description="",
     *   summary="",
     *   operationId="api.v1.homes.getHome",
     *   produces={"application/json"},
     *   tags={"Home"},
     *   @SWG\Parameter(
     *     description="",
     *     in="path",
     *     name="take",
     *     required=true,
     *     type="integer",
     *     default="" 
     *   ),
     *   @SWG\Parameter(
     *     description="",
     *     in="path",
     *     name="page",
     *     required=true,
     *     type="integer",
     *     default="" 
     *   ),
     *   @SWG\Response(response=401, description="unauthorized"),
     *   @SWG\Response(response=200, description="Success"),
     *   security={
     *       {"api_key": {}}
     *   }
     * )
     */
    public function getHome_(Request $request)
    {
        $page             = is_numeric($request->page) ? $request->page : false;
        $take             = is_numeric($request->take) ? $request->take : false;
        $arr = [];
        $advertisement = AdvertisementItem::where('adv_id',3)->where('status',1)->get();
        $random = $advertisement->shuffle();
        if($random)
        {
            foreach ($random as $key1 => $item) {
                if($key1 < 3)
                {
                    $arr += [$key1 => AdvertisementItem::find($item->id)];
                }
            
            }
            shuffle($arr);
        }
        $vendors = [] ;
        $maxPage = 0;
        $listVendor = Vendor::with('profile')->with('categories')->with('location')->whereHas('portfolios',function($query){
                $query->where('status',1);
            })->whereHas('roles',function($query){
                $query->where('role_id','=',3);
            })->whereHas('categories',function($query) {
                $query->where('mm__vendors_category.status','=',1);
            });
        $model = $listVendor->get();
        //Start MC 51
        $arrGoldFree = [];
        $arrSilver = [];
        $arrVendors = [];
        $arrAllVendorId = [];
        foreach ($model as $key => $vendor) {
            $arrAllVendorId += [$key => $vendor->id];
            $vendorPlanID = $this->getVendorPlan($vendor->id);
            if($vendorPlanID == 2 || $vendorPlanID == 4)
            {
                $arrGoldFree += [$vendor->id => $vendor->profile];
                
            }
            if($vendorPlanID == 3)
            {
                $arrSilver += [$vendor->id => $vendor->profile];
            }
        }
        //Array Gold Free
        $collectionGoldFree = collect($arrGoldFree);
        $sortedGoldFree = $collectionGoldFree->sortByDesc('rating_points');
        $vendorGoldFree = $sortedGoldFree->take(8)->values()->all();
        //Array Silver
        $collectionSilver = collect($arrSilver);
        $sortedSilver = $collectionSilver->sortByDesc('rating_points');
        $vendorSilver = $sortedSilver->take(8)->values()->all();
        if(count($vendorGoldFree))
        {
            foreach ($vendorGoldFree as $key => $goleFree) {
                $arrVendors += [$goleFree->user_id    => Customer::find($goleFree->user_id) ];
            }
        }
        if(count($vendorSilver))
        {
            foreach ($vendorSilver as $key => $silver) {
                $arrVendors += [$silver->user_id    => Customer::find($silver->user_id) ];
            }
        }
        $allVendor = $this->getAllFreeVendor($arrVendors,$arrAllVendorId);
        $maxPage = 1;
        $maxVendor = count($allVendor);
        if($take)
        {
           $maxPage = CEIL($maxVendor/$take);
        }
        else
        {
            $maxPage = CEIL($maxVendor/config('constant.default.take'));
        }
        
        $vendors = $this->paginateFilter($allVendor ,config('constant.default.take'));
        $customer = $request->user();
        if($take)
        {
            $vendors = $this->paginateFilter($allVendor ,$take);
        }
        // dd($vendors);
        //End MC 51
        if($page > 1)
        {
            return response([
                    'data' => [
                        'vendors'   =>  Helper::formatHomeListVendors($vendors,$customer),
                        'maxPage' => $maxPage
                        ],
                ],Response::HTTP_OK);
        }
        return response([
                    'data' => [
                        'advertisement'   =>  Helper::formatAdvertisementItem($arr),
                        'vendors'   =>  Helper::formatHomeListVendors($vendors,$customer),
                        'maxPage' => $maxPage
                        ],
                ],Response::HTTP_OK);
        
        
    }
    public function getHome(Request $request)
    {
        $page             = is_numeric($request->page) && $request->page ? $request->page : 1;
        $take             = is_numeric($request->take) ? $request->take : config('constant.default.take');
        $arr = [];
        $advertisement = AdvertisementItem::where('adv_id',3)->where('status',1)->get();
        $random = $advertisement->shuffle();
        if($random)
        {
            foreach ($random as $key1 => $item) {
                if($key1 < 3)
                {
                    $arr += [$key1 => AdvertisementItem::find($item->id)];
                }
            
            }
            shuffle($arr);
        }


        $vendors = $this->service->getItems('list', $page, $take);
        $countAllVendors = $this->service->getItems('count');
        $maxPage = CEIL($countAllVendors/$take);
        
        $customer = $request->user();

        //End MC 51
        if($page > 1)
        {
            return response([
                    'data' => [
                        'vendors'   =>  Helper::formatHomeListVendors($vendors,$customer),
                        'maxPage' => $maxPage
                        ],
                ],Response::HTTP_OK);
        }
        return response([
                    'data' => [
                        'advertisement'   =>  Helper::formatAdvertisementItem($arr),
                        'vendors'   =>  Helper::formatHomeListVendors($vendors,$customer),
                        'maxPage' => $maxPage
                        ],
                ],Response::HTTP_OK);
        
        
    }

    /**
     * @SWG\Get(
     *   path="/v1/homes/getListFavourite?take={take}&page={page}",
     *   description="",
     *   summary="",
     *   operationId="api.v1.homes.getListFavourite",
     *   produces={"application/json"},
     *   tags={"Home"},
     *   @SWG\Parameter(
     *     description="",
     *     in="path",
     *     name="take",
     *     required=true,
     *     type="integer",
     *     default="" 
     *   ),
     *   @SWG\Parameter(
     *     description="",
     *     in="path",
     *     name="page",
     *     required=true,
     *     type="integer",
     *     default="" 
     *   ),
     *   @SWG\Response(response=401, description="unauthorized"),
     *   @SWG\Response(response=200, description="Success"),
     *   security={
     *       {"api_key": {}}
     *   }
     * )
     */
    public function getListFavourite(Request $request)
    {
        $page             = is_numeric($request->page) ? $request->page : false;
        $take             = is_numeric($request->take) ? $request->take : false;
        if(!$page)
            $page =1;
        if(!$take)
            $take = config('constant.default.take');
        $portfolioCollection = [];
        $porfolios = [];
        $customer = $request->user();
        $customerID = $customer->id;
        $query = "Select tb.portfolio_id From mm__user_activities as tb Join users as tb2 On tb2.id = tb.user_id JOIN mm__vendors_portfolios AS tb1 ON tb.portfolio_id = tb1.id AND tb1.is_deleted IS NULL Where tb.user_id = $customerID AND tb.portfolio_id IS NOT NULL AND tb.activity = 2 AND tb2.status = 1 AND tb1.status = 1 Group By tb.portfolio_id";
        $customerActivitive = DB::select($query);
        $maxPage = 1;
        if($customerActivitive)
        {
            foreach ($customerActivitive as $key => $value) {
            $porfolios += [$key => VendorPortfolio::find($value->portfolio_id)];
            
            }
            $maxVendor = count($porfolios);
            $maxPage = CEIL($maxVendor/2);
            if($take)
            {
               $maxPage = CEIL($maxVendor/$take);
            }
            $portfolioCollection = $this->paginateFilter($porfolios,$take);
        }
        return response([
                    'data' => [
                    'customer' =>  Helper::formatDateCustomer($customer),
                    'count' =>  count($customerActivitive),
                    'vendors'   =>  Helper::formatListPortfolio($portfolioCollection,$customer),
                    'maxPage' =>  $maxPage,
                    ],
                ],Response::HTTP_OK);
        
    }

    /**
     * @SWG\Post(
     *   path="/v1/homes/deleteFavourite",
     *   description="<ul>
     *     <li>currentPassword : string (required)</li>
     *     <li>newPassword : string (required)</li></ul>",
     *   summary="View",
     *   operationId="api.v1.homes.deleteFavourite",
     *   produces={"application/json"},
     *   tags={"Home"},
     *   @SWG\Parameter(
     *     name="body",
     *     in="body",
     *     description="Target customer.",
     *     required=true,
     *    @SWG\Schema(ref="#/definitions/Favourite")
     *   ),
     *   @SWG\Response(response=500, description="internal server error"),
     *   security={
     *       {"api_key": {}}
     *   }
     * )
     *
     */
    public function deleteFavourite(PortfolioRequest $request)
    {
        $portfolio_id             = !empty($request->portfolio_id) ? $request->portfolio_id : false;
        if(!$portfolio_id){
            return response([
                    'error' => [
                    'message'   =>  'Portfolio_id is required'],
                ],Response::HTTP_OK);
        }

        $customer = $request->user();

        $customerActivitive = CustomerActivitive::where('portfolio_id',$portfolio_id)->where('user_id',$customer->id)->where('activity',2)->first();
        if($customerActivitive)
        {
            $customerActivitive->delete();
            return response([
                    'data' => [
                    'message' =>  'Delete Favourite Success'
                    ],
                ],Response::HTTP_OK);
        }
        return response([
                    'error' => [
                    'message' =>  'Can not Delete Favourite'
                    ],
                ],Response::HTTP_OK);
    }

    /**
     * @SWG\Get(
     *   path="/v1/homes/getSearchScreen?country_id={country_id}",
     *   description="",
     *   summary="",
     *   operationId="api.v1.homes.getSearchScreen",
     *   produces={"application/json"},
     *   tags={"Home"},
     *   @SWG\Parameter(
     *     description="",
     *     in="path",
     *     name="country_id",
     *     required=false,
     *     type="integer",
     *     default="" 
     *   ),
     *   @SWG\Response(response=401, description="unauthorized"),
     *   @SWG\Response(response=200, description="Success"),
     *   security={
     *       {"api_key": {}}
     *   }
     * )
     */
    public function getSearchScreen(Request $request)
    {
        $country_id             = !empty($request->country_id) ? $request->country_id : false;
        $countries = [];
        $cities = [];
        $arrCity = [];
        $arrCountry = [];
        $i = 0;
        $j = 0;
        $queryCountry = "select distinct(country_id) from mm__vendors_location where is_deleted IS NULL";
        $queryCity = "select distinct(city_id) from mm__vendors_location where is_deleted IS NULL";
        if($country_id && $country_id != '{country_id}')
        {
            $queryCity = "select distinct(city_id) from mm__vendors_location where is_deleted IS NULL AND country_id = $country_id";
        }
        $resultCountry = \DB::select($queryCountry);
        $resultCity = \DB::select($queryCity);
        if(count($resultCountry) > 0)
        {
            foreach ($resultCountry as $key => $value) {
                $country = Country::find($value->country_id);
                if(isset($country) && !empty($country))
                {
                    $arrCountry += [$key => $country];
                }
            }
        }
        if(count($resultCity) > 0)
        {
            foreach ($resultCity as $keyCity => $valueCity) {
                $city = City::find($valueCity->city_id);
                if(isset($city) && !empty($city))
                {
                    $arrCity += [$keyCity => $city];
                }
            }
        }

        if(count($arrCity) > 0)
        {
            foreach ($arrCity as $keyArrCity => $valueArrCity) {
                $cities += [$i => $valueArrCity];
                $i++;
            }
        }
        if(count($arrCountry) > 0)
        {
            foreach ($arrCountry as $keyArrCountry => $valueArrCountry) {
                $countries += [$j => $valueArrCountry];
                $j++;
            }
        }
        return response([
                    'data' => [
                    'countries' =>  $countries,
                    'cities' =>  $cities,
                    'categories' =>  Category::all(),
                    'subcategories' =>  SubCategory::all(),
                    'pricerange' =>  PriceRange::all(),
                    ],
                ],Response::HTTP_OK);
    }

    /**
     * @SWG\Post(
     *   path="/v1/homes/postSearch",
     *   description="<ul>
     *     <li>currentPassword : string (required)</li>
     *     <li>newPassword : string (required)</li></ul>",
     *   summary="View",
     *   operationId="api.v1.homes.postSearch",
     *   produces={"application/json"},
     *   tags={"Home"},
     *   @SWG\Parameter(
     *     name="body",
     *     in="body",
     *     description="Target customer.",
     *     required=true,
     *    @SWG\Schema(ref="#/definitions/Search")
     *   ),
     *   @SWG\Response(response=500, description="internal server error"),
     *   security={
     *       {"api_key": {}}
     *   }
     * )
     *
     */
    public function postSearch(Request $request)
    {
        $country_id             = !empty($request->country_id) ? $request->country_id : false;
        $city_id             = !empty($request->city_id) ? $request->city_id : false;
        $category_id             = !empty($request->category_id) ? $request->category_id : false;
        $sub_category_id             = !empty($request->sub_category_id) ? $request->sub_category_id : false;
        $price_range_id             = !empty($request->price_range_id) ? $request->price_range_id : false;
        $name             = !empty($request->name) ? $request->name : false;
        $page             = is_numeric($request->page) ? $request->page : false;
        $take             = is_numeric($request->take) ? $request->take : false;
        if(!$page)
            $page =1;
        if(!$take)
            $take = config('constant.default.take');
        $query = $this->queryBuild($country_id,$city_id, $category_id,$sub_category_id,$price_range_id,$name);
        $result = DB::select($query);
        if($result)
        {
            $arr =[];
            foreach ($result as $key => $value) 
            {
                $arr += [$key => Customer::find($value->user_id)];
            
            }
            $maxPage = 1;
            $maxVendor = count($arr);
            $maxPage = CEIL($maxVendor/2);
            if($take)
            {
               $maxPage = CEIL($maxVendor/$take);
            }
            $vendorCollection = $this->paginateFilter($arr,$take);
            return response([
                    'data' => [
                    'vendors'   =>  Helper::formatListVendorsByCategory($vendorCollection),
                    'maxPage'   =>  $maxPage
                    ],
                ],Response::HTTP_OK);
        }
         return response([
                    'error' => [
                    'message'   =>  'Try searching for different keywords.'],
                ],Response::HTTP_OK);
       
    }

    /**
     * @SWG\Post(
     *   path="/v1/homes/postSearchByName",
     *   description="<ul>
     *     <li>currentPassword : string (required)</li>
     *     <li>newPassword : string (required)</li></ul>",
     *   summary="View",
     *   operationId="api.v1.homes.postSearchByName",
     *   produces={"application/json"},
     *   tags={"Home"},
     *   @SWG\Parameter(
     *     name="body",
     *     in="body",
     *     description="Target customer.",
     *     required=true,
     *    @SWG\Schema(ref="#/definitions/SearchByName")
     *   ),
     *   @SWG\Response(response=500, description="internal server error"),
     *   security={
     *       {"api_key": {}}
     *   }
     * )
     *
     */
    public function postSearchByName(SearchByNameRequest $request)
    {
        $name             = !empty($request->name) ? $request->name : false;
        $query = "select tb.*,tb1.title,tb2.role_id,tb3.business_name from users AS tb Left JOIN mm__vendors_portfolios AS tb1 ON tb.id = tb1.vendor_id LEFT JOIN role_users AS tb2 ON tb.id = tb2.user_id LEFT JOIN mm__vendors_profile AS tb3 ON tb.id = tb3.user_id WHERE tb2.role_id = 3 AND tb.status = 1 AND tb1.is_deleted IS NULL AND tb1.status = 1 AND tb.is_deleted IS NULL";
        $page             = is_numeric($request->page) ? $request->page : false;
        $take             = is_numeric($request->take) ? $request->take : false;
        if(!$page)
            $page =1;
        if(!$take)
            $take = config('constant.default.take');
        if($name)
        {
            // $query .=" AND (tb3.business_name like '%$name%' OR tb1.title like '%$name%') ";
            $nameArr = $this->service->getSearchNameArray($name);

            $whereBusinessName = $whereTitle = $whereDescription = [];
            foreach($nameArr as $item){
                $whereBusinessName[] =  " tb3.business_name like '%$item%' ";
                $whereTitle[] =  " tb1.title like '%$item%' ";
                $whereDescription[] =  " tb1.description like '%$item%' ";
            }

            $query .= " AND ( " . implode("AND", $whereBusinessName) . " OR " . implode("AND", $whereTitle) . " OR " . implode("AND", $whereDescription) . ")";            
        }

        $query.= " GROUP BY tb3.user_id ORDER BY tb3.business_name ASC";

        $vendors = DB::select($query);
        if(isset($vendors) && !empty($vendors))
        {
            $maxPage = 1;
            $maxVendor = count($vendors);
            $maxPage = CEIL($maxVendor/2);
            if($take)
            {
               $maxPage = CEIL($maxVendor/$take);
            }
            $vendorCollection = $this->paginateFilter($vendors,$take);
            return response([
                    'data' => [
                    'vendors'   =>  Helper::formatListVendors($vendorCollection),
                    'maxPage'   =>  $maxPage
                    ],
                ],Response::HTTP_OK);
        }
        return response([
                    'error' => [
                    'message'   =>  'Try searching for different keywords.'],
                ],Response::HTTP_OK);
        

    }

    /**
     * @SWG\Post(
     *   path="/v1/homes/postSearchNearBy",
     *   description="<ul>
     *     <li>currentPassword : string (required)</li>
     *     <li>newPassword : string (required)</li></ul>",
     *   summary="View",
     *   operationId="api.v1.homes.postSearchNearBy",
     *   produces={"application/json"},
     *   tags={"Home"},
     *   @SWG\Parameter(
     *     name="body",
     *     in="body",
     *     description="Target customer.",
     *     required=true,
     *    @SWG\Schema(ref="#/definitions/SearchNearby")
     *   ),
     *   @SWG\Response(response=500, description="internal server error"),
     *   security={
     *       {"api_key": {}}
     *   }
     * )
     *
     */
    public function postSearchNearBy(SearchNearbyRequest $request)
    {
        
        $vendors = [];
        $lat             = !empty($request->lat) ? $request->lat : false;
        $lng             = !empty($request->lng) ? $request->lng : false;
        if(!$lng || !$lat){
            return response([
                    'error' => [
                    'message'   =>  'lat , lng is required'],
                ],Response::HTTP_OK);
        }

        $arr =[];
        $vendors = DB::select('SELECT p.user_id,p.lat,p.lng,( 6371 * acos( cos( radians(?) ) * cos( radians( p.lat ) ) * cos( radians( p.lng ) - radians(?) ) + sin( radians(?) ) * sin( radians( p.lat ) ) ) ) AS distance FROM mm__vendors_profile AS p JOIN users AS tb2 ON tb2.id = p.user_id JOIN mm__vendors_category AS tb5 ON p.user_id =tb5.user_id Where tb2.status = 1 AND tb5.is_deleted IS NULL AND tb5.status = 1 AND tb2.status = 1 AND tb2.is_deleted IS NULL GROUP BY  p.user_id,p.lat,p.lng Having ( 6371 * acos( cos( radians(?) ) * cos( radians( p.lat ) ) * cos( radians( p.lng ) - radians(?) ) + sin( radians(?) ) * sin( radians( p.lat ) ) ) ) < 5',[$lat,$lng,$lat,$lat,$lng,$lat]);
        // if($vendors)
        // {
        //     foreach ($vendors as $key => $value) {
        //     $arr += [$key => Customer::find($value->user_id)];
            
        //     }
        // }
        return response([
                    'data' => [
                    'vendors'   =>  Helper::formatSearchNearby($vendors)
                    ],
                ],Response::HTTP_OK);
        
    }

    //Filter
    /**
     * @SWG\Post(
     *   path="/v1/homes/postFilter",
     *   description="<ul>
     *     <li>currentPassword : string (required)</li>
     *     <li>newPassword : string (required)</li></ul>",
     *   summary="View",
     *   operationId="api.v1.homes.postFilter",
     *   produces={"application/json"},
     *   tags={"Home"},
     *   @SWG\Parameter(
     *     name="body",
     *     in="body",
     *     description="Target customer.",
     *     required=true,
     *    @SWG\Schema(ref="#/definitions/Filter")
     *   ),
     *   @SWG\Response(response=500, description="internal server error"),
     *   security={
     *       {"api_key": {}}
     *   }
     * )
     *
     */
    public function postFilter(Request $request)
    {
        $country_id             = !empty($request->country_id) ? $request->country_id : false;
        $category_id             = !empty($request->category_id) ? $request->category_id : false;
        $sub_category_id             = !empty($request->sub_category_id) ? $request->sub_category_id : false;
        $price_range_id             = !empty($request->price_range_id) ? $request->price_range_id : false;
        $page             = is_numeric($request->page) ? $request->page : false;
        $take             = is_numeric($request->take) ? $request->take : false;
        if(!$page)
            $page =1;
        if(!$take)
            $take = config('constant.default.take');
        $arr = [];
        $temp = [];
        $advertisement = AdvertisementItem::all();
        $random = $advertisement->shuffle();
        if($random)
        {
            foreach ($random as $key1 => $item) {
                if($key1 < 3)
                {
                    $arr += [$key1 => AdvertisementItem::find($item->id)];
                }
            }
            shuffle($arr);
        }
        $vendors = [] ;
        $maxPage = 1;
        $query = $this->queryBuild($country_id,'', $category_id,$sub_category_id,$price_range_id,'');
        $vendorsID = DB::select($query);
        $collection = collect($vendorsID)->forPage($page, $take);
       
        $customer = $request->user();
        //$maxPage = $vendorsID->lastPage();
        
        if($vendorsID)
        {
            $maxVendor = count($vendorsID);
            $maxPage = CEIL($maxVendor/2);
            if($take)
            {
               $maxPage = CEIL($maxVendor/$take);
            }
            foreach ($vendorsID as $key => $value) {
                $vendors += [$key => Customer::find($value->user_id)];
            }
        }
        $vendorCollection = $this->paginateFilter($vendors,$take);
         return response([
                    'data' => [
                        'advertisement'   =>  [],
                        'vendors'   =>  Helper::formatHomeListVendors($vendorCollection,$customer),
                        'maxPage' => $maxPage
                        ],
                ],Response::HTTP_OK);
    }

    /**
     * @SWG\Post(
     *   path="/v1/homes/postAllPortfolioByVendor",
     *   description="",
     *   summary="",
     *   operationId="api.v1.homes.postAllPortfolioByVendor",
     *   produces={"application/json"},
     *   tags={"Home"},
     *   @SWG\Parameter(
     *     name="body",
     *     in="body",
     *     description="Target customer.",
     *     required=true,
     *    @SWG\Schema(ref="#/definitions/AllPortFolioByVendor")
     *   ),
     *   @SWG\Response(response=401, description="unauthorized"),
     *   @SWG\Response(response=200, description="Success"),
     *   security={
     *       {"api_key": {}}
     *   }
     * )
     */
    public function postAllPortfolioByVendor(Request $request)
    {
        $vendor_id        = !empty($request->vendor_id) ? $request->vendor_id : false;
        $category_id      = !empty($request->category_id) ? $request->category_id : false;
        $page             = is_numeric($request->page) ? $request->page : false;
        $take             = is_numeric($request->take) ? $request->take : false;
        // sort by
        $sort_by          = is_numeric($request->sort_by) ? $request->sort_by : false;
        if(!$page)
            $page =1;
        if(!$take)
            $take = config('constant.default.take');
        if(!$sort_by)
            $sort_by = config('constant.sort.portfolio.popularity');
        $customer = $request->user();
        $vendorPortfolios = [];
        $allPortfolioByVendor = [] ;
        $maxPage = 1;
        $query = "select tb.*, (select count(*) FROM mm__user_activities ua JOIN mm__vendors_portfolios vp ON ua.portfolio_id = vp.id AND vp.status = 1 ANd vp.is_deleted IS NULL WHERE ua.portfolio_id IS NOT NULL AND ua.portfolio_id = tb.id AND ua.activity = 5) as count_love, (select count(*) FROM mm__user_activities ua2 WHERE ua2.portfolio_id IS NOT NULL AND ua2.portfolio_id = tb.id AND ua2.activity = 4) as count_view from mm__vendors_portfolios AS tb JOIN users AS tb2 ON tb2.id = tb.vendor_id JOIN mm__categories tb3 ON tb.category_id = tb3.id AND tb3.is_deleted IS NULL AND tb3.status = 1 WHERE tb.vendor_id = $vendor_id AND tb2.status = 1 AND tb.is_deleted IS NULL AND tb.status = 1";
        if($category_id)
        {
            if(isset($category_id) && !empty($category_id))
            {
                $categories = "(";
                foreach ($category_id as $key1 => $value1) {
                    if($key1 == 0)
                    {
                        $categories .= "$value1";
                    }
                    else
                    {
                        $categories .= ",$value1";
                    }
                }
                $categories .= ")";
                $query .= " AND category_id IN $categories";
            }
        }
        if($sort_by == config('constant.sort.portfolio.lastest')){
            $query .= " ORDER BY tb.id DESC ";
        }elseif($sort_by == config('constant.sort.portfolio.most_viewed')){
            $query .= " ORDER BY count_view DESC ";
        }else{
            $query .= " ORDER BY count_love DESC ";
        }

        $vendorPortfolio = DB::select($query);
        if(isset($vendorPortfolio) && !empty($vendorPortfolio))
        {
            $maxVendor = count($vendorPortfolio);
            $maxPage = CEIL($maxVendor/2);
            if($take)
            {
               $maxPage = CEIL($maxVendor/$take);
            }
            foreach ($vendorPortfolio as $key => $value) {
                $vendorPortfolios += [$key => $value];
            }
            $allPortfolioByVendor = $this->paginateFilter($vendorPortfolios,$take);
        }
        return response([
                    'data' => [
                        'portfolios'   =>  Helper::formatListPortfolio($allPortfolioByVendor,$customer),
                        'maxPage' => $maxPage
                        ],
                ],Response::HTTP_OK);
        
    }

    /**
     * @SWG\Post(
     *   path="/v1/homes/postLovePortfolio",
     *   description="",
     *   summary="",
     *   operationId="api.v1.homes.postLovePortfolio",
     *   produces={"application/json"},
     *   tags={"Home"},
     *   @SWG\Parameter(
     *     name="body",
     *     in="body",
     *     description="Target customer.",
     *     required=true,
     *    @SWG\Schema(ref="#/definitions/LovePortfolio")
     *   ),
     *   @SWG\Response(response=401, description="unauthorized"),
     *   @SWG\Response(response=200, description="Success"),
     *   security={
     *       {"api_key": {}}
     *   }
     * )
     */
    public function postLovePortfolio(PortfolioRequest $request)
    {
        $portfolio_id             = !empty($request->portfolio_id) ? $request->portfolio_id : false;
        $customer = $request->user();
        $vendorID = '';
        $checkExistLovePortfolio = CustomerActivitive::where('portfolio_id',$portfolio_id)->where('user_id',$customer->id)->where('activity',5)->first();
        if($checkExistLovePortfolio)
        {
            $checkExistLovePortfolio->delete();
            return response([
                    'data' => [
                    'message'   =>  'Delete Love Portfolio Success'],
                ],Response::HTTP_OK);
        }
        if($portfolio_id)
        {
            $portfolio = VendorPortfolio::find($portfolio_id);
            if(isset($portfolio) && !empty($portfolio))
            {
                $vendorID = $portfolio->vendor_id;
            }
            
        }
        $vendor = Vendor::where('id',$vendorID)->first();
        $userActivitive = new CustomerActivitive();
        $userActivitive->user_id = $customer->id;
        $userActivitive->portfolio_id = $portfolio_id;
        $userActivitive->vendor_id = $vendorID;
        
        $userActivitive->activity = 5;
        $userActivitive->save();
        return response([
            'data' => [
            'message'   =>  'Love Portfolio Success'],
        ],Response::HTTP_OK);
    }


    /**
     * @SWG\Post(
     *   path="/v1/homes/postPortfolioAllComment",
     *   description="",
     *   summary="",
     *   operationId="api.v1.homes.postPortfolioAllComment",
     *   produces={"application/json"},
     *   tags={"Home"},
     *   @SWG\Parameter(
     *     name="body",
     *     in="body",
     *     description="Target customer.",
     *     required=true,
     *    @SWG\Schema(ref="#/definitions/Portfolio")
     *   ),
     *   @SWG\Response(response=401, description="unauthorized"),
     *   @SWG\Response(response=200, description="Success"),
     *   security={
     *       {"api_key": {}}
     *   }
     * )
     */
    public function postPortfolioAllComment(PortfolioRequest $request)
    {
        $portfolio_id             = !empty($request->portfolio_id) ? $request->portfolio_id : false;
        $page             = is_numeric($request->page) ? $request->page : false;
        $take             = is_numeric($request->take) ? $request->take : false;
        if(!$page)
            $page =1;
        if(!$take)
            $take = config('constant.default.take');

        $customer = $request->user();
        $customerActiveView = CustomerActivitive::where('portfolio_id',$portfolio_id)->where('user_id',$customer->id)->where('activity',4)->first();
            
        if(!$customerActiveView)
        {
            $newCustomerActiveView = new CustomerActivitive();
            $newCustomerActiveView->portfolio_id = $portfolio_id;
            $newCustomerActiveView->user_id = $customer->id;
            $newCustomerActiveView->activity = 4;
            $newCustomerActiveView->save();
            $viewPortfolio = VendorPortfolio::find($portfolio_id);
            if($viewPortfolio)
            {
                $viewPortfolio->views = $viewPortfolio->views + 1;
                $viewPortfolio->save();
            }
            
        }
        $vendor = [];
        $vendorAllComment = [];
        $countAllComment = 0;
         $maxPage = 1;

        $vendorComment = VendorComment::where('portfolios_id',$portfolio_id)->where('status',1)->orderBy('id', 'desc')->get();
        if(isset($vendorComment) && !empty($vendorComment) && count($vendorComment) >0)
        {
            $countAllComment = count($vendorComment);
            $maxVendor = count($vendorComment);
            $maxPage = CEIL($maxVendor/2);
            if($take)
            {
               $maxPage = CEIL($maxVendor/$take);
            }
            foreach ($vendorComment as $key => $value) {
                $vendorAllComment += [$key => $value];
            }
        }
        $vendorCollectionAllComment = $this->paginateFilter($vendorAllComment,$take);
        $vendorPortfolio = VendorPortfolio::find($portfolio_id);

        
        if(isset($vendorPortfolio) && !empty($vendorPortfolio))
        {
            $vendor = DB::table('users')->Join('role_users', 'users.id', '=', 'role_users.user_id')->where('id', $vendorPortfolio->vendor_id)->where('role_id', 3)->where('status', 1)->get();
            if($page > 1)
            {
                 return response([
                        'data' => [
                        'allComment'   => Helper::formatAllComment($vendorCollectionAllComment,$customer),
                        'countAllComment'   => $countAllComment,
                        'maxPage'   => $maxPage
                        
                        ],
                    ],Response::HTTP_OK);
            }
            return response([
                        'data' => [
                        'vendor'   => Helper::formatGetVendors($vendor,$customer),
                        'portfolios'   => Helper::formatPortfolio($vendorPortfolio,$customer),
                        'allComment'   => Helper::formatAllComment($vendorCollectionAllComment,$customer),
                        'countAllComment'   => $countAllComment,
                        'maxPage'   => $maxPage
                        
                        ],
                    ],Response::HTTP_OK);
        }
         return response([
                    'error' => [
                    'message'   =>  'No Result Found.'],
                ],Response::HTTP_OK);
        
    }



    /**
     * @SWG\Post(
     *   path="/v1/homes/deleteComment",
     *   description="",
     *   summary="",
     *   operationId="api.v1.homes.deleteComment",
     *   produces={"application/json"},
     *   tags={"Home"},
     *   @SWG\Parameter(
     *     name="body",
     *     in="body",
     *     description="Target customer.",
     *     required=true,
     *    @SWG\Schema(ref="#/definitions/DeleteComment")
     *   ),
     *   @SWG\Response(response=401, description="unauthorized"),
     *   @SWG\Response(response=200, description="Success"),
     *   security={
     *       {"api_key": {}}
     *   }
     * )
     */
    public function deleteComment(DeleteCommentRequest $request)
    {
        $comment_id             = is_numeric($request->comment_id) ? $request->comment_id : false;
        $vendorComment = VendorComment::find($comment_id);
        if(isset($vendorComment) && !empty($vendorComment))
        {
            $vendorComment->delete();
            return response([
                    'data' => [
                    'message'   =>  'Delete Comment Success'],
                ],Response::HTTP_OK);
        }
        return response([
                    'error' => [
                    'message'   =>  'No Result Found.'],
                ],Response::HTTP_OK);
    }

    /**
     * @SWG\Post(
     *   path="/v1/homes/postPricelist",
     *   description="",
     *   summary="",
     *   operationId="api.v1.homes.postPricelist",
     *   produces={"application/json"},
     *   tags={"Home"},
     *   @SWG\Parameter(
     *     name="body",
     *     in="body",
     *     description="Target customer.",
     *     required=true,
     *    @SWG\Schema(ref="#/definitions/Pricelist")
     *   ),
     *   @SWG\Response(response=401, description="unauthorized"),
     *   @SWG\Response(response=200, description="Success"),
     *   security={
     *       {"api_key": {}}
     *   }
     * )
     */
    public function postPricelist(FavouriteRequest $request)
    {
        $vendor_id             = !empty($request->vendor_id) ? $request->vendor_id : false;
        $category_id             = !empty($request->category_id) ? $request->category_id : false;
        $page             = is_numeric($request->page) ? $request->page : false;
        $take             = is_numeric($request->take) ? $request->take : false;
        if(!$page)
            $page =1;
        if(!$take)
            $take = config('constant.default.take');

        $customer = $request->user();
        $query = "select tb.* from mm__vendors_profile_pricelist AS tb JOIN users AS tb2 ON tb2.id = tb.user_id WHERE user_id = $vendor_id AND tb2.status = 1";

        $vendor = Vendor::where('id',$vendor_id)->first();
        $check = Helper::reducePoint($vendor_id,$customer->id,6);
        Helper::checkActivity($vendor_id,$customer->id,6,$check);
        if(isset($category_id) && !empty($category_id))
        {
            $categories = "(";
            foreach ($category_id as $key => $value) {
                if($key == 0)
                {
                    $categories .= "$value";
                }
                else
                {
                    $categories .= ",$value";
                }
            }
            $categories .= ")";
            $query .= " AND category_id IN $categories";
        }
        $vendorPricelist = DB::select($query);
        if(isset($vendorPricelist) && !empty($vendorPricelist))
        {
            $maxVendor = count($vendorPricelist);
            $maxPage = CEIL($maxVendor/2);
            if($take)
            {
               $maxPage = CEIL($maxVendor/$take);
            }
            $vendorCollection = $this->paginateFilter($vendorPricelist,$take);
            return response([
                    'data' => [
                    'pricelist'   =>  Helper::formatGetPriceList($vendorCollection),
                    'maxPage'   =>  $maxPage],
                ],Response::HTTP_OK);
        }
        return response([
                    'data' => [
                    'pricelist'   =>  []],
                ],Response::HTTP_OK);
        
        
    }

    /**
     * @SWG\Post(
     *   path="/v1/homes/postProfileMyVendor",
     *   description="",
     *   summary="",
     *   operationId="api.v1.homes.postProfileMyVendor",
     *   produces={"application/json"},
     *   tags={"Home"},
     *   @SWG\Parameter(
     *     name="body",
     *     in="body",
     *     description="Target customer.",
     *     required=true,
     *    @SWG\Schema(ref="#/definitions/ProfileMyVendor")
     *   ),
     *   @SWG\Response(response=401, description="unauthorized"),
     *   @SWG\Response(response=200, description="Success"),
     *   security={
     *       {"api_key": {}}
     *   }
     * )
     */
    public function postProfileMyVendor(Request $request)
    {
        $sort_by             = is_numeric($request->sort_by) ? $request->sort_by : false;
        $page             = is_numeric($request->page) ? $request->page : false;
        $take             = is_numeric($request->take) ? $request->take : false;
        if(!$page)
            $page =1;
        if(!$take)
            $take = config('constant.default.take');
        $vendors = [];
        $vendorCollection = [];
        $customer = $request->user();
        $query = "select tb1.*,tb3.rating_points, COUNT(tb4.vendor_id) as SumReview from mm__user_activities as tb1 Join users as tb2 On tb2.id = tb1.vendor_id Join mm__vendors_profile as tb3 On tb3.user_id = tb1.vendor_id Left Join mm__user_reviews as tb4 On tb4.vendor_id = tb1.vendor_id where tb2.status = 1 AND tb1.user_id = $customer->id AND tb1.activity = 1 Group By tb1.vendor_id";
        $customerActivitive = \DB::select($query);
        $maxPage = 1;
        if($customerActivitive)
        {
            if($sort_by == 3)
            {
                $customerActivitive = collect($customerActivitive)->sortBy('SumReview')->reverse();
            }
            if($sort_by == 2)
            {
                $customerActivitive = collect($customerActivitive)->sortBy('rating_points')->reverse();
            }
            foreach ($customerActivitive as $key => $value) {
            $vendors += [$key => Customer::find($value->vendor_id)];
            
            }
            $maxVendor = count($vendors);
            $maxPage = CEIL($maxVendor/2);
            if($take)
            {
               $maxPage = CEIL($maxVendor/$take);
            }
            
            $vendorCollection = $this->paginateFilter($vendors,$take);
            
        }
        //$userReview = UserReview::where('user_id',$customer->id)->get();
        return response([
                    'data' => [
                    'customer' =>  Helper::formatDateCustomer($customer),
                    'countMyVendor' =>  count($customerActivitive),
                    'vendors'   =>  Helper::formatListVendors($vendorCollection),
                    'maxPage' =>  $maxPage,
                    //'reviews'   =>  Helper::formatListReviews($userReview)
                    ],
                ],Response::HTTP_OK);
    }

    /**
     * @SWG\Post(
     *   path="/v1/homes/postProfileMyReview",
     *   description="",
     *   summary="",
     *   operationId="api.v1.homes.postProfileMyReview",
     *   produces={"application/json"},
     *   tags={"Home"},
     *   @SWG\Parameter(
     *     name="body",
     *     in="body",
     *     description="Target customer.",
     *     required=true,
     *    @SWG\Schema(ref="#/definitions/ProfileMyReview")
     *   ),
     *   @SWG\Response(response=401, description="unauthorized"),
     *   @SWG\Response(response=200, description="Success"),
     *   security={
     *       {"api_key": {}}
     *   }
     * )
     */
    public function postProfileMyReview(Request $request)
    {
        $sort_by             = is_numeric($request->sort_by) ? $request->sort_by : false;
        $page             = is_numeric($request->page) ? $request->page : false;
        $take             = is_numeric($request->take) ? $request->take : false;
        if(!$page)
            $page =1;
        if(!$take)
            $take = config('constant.default.take');
         $userReviewCollection = [];
        $customer = $request->user();
        $maxPage = 1;
        $query = "select tb.* from mm__user_reviews AS tb JOIN users AS tb2 ON tb2.id = tb.vendor_id WHERE user_id = $customer->id AND tb2.status = 1 AND tb.status = 1 AND tb.is_deleted is NULL";
        if($sort_by == config('constant.default.LatestReview'))
        {
            $query .= " ORDER BY created_at DESC";
        }
        if($sort_by == config('constant.default.OldestReview'))
        {
            $query .= " ORDER BY created_at ASC";
        }
        if($sort_by == config('constant.default.HighestRating'))
        {
             $query .= " ORDER BY rating DESC";
        }
        if($sort_by == config('constant.default.LowestRating'))
        {
             $query .= " ORDER BY rating ASC";
        }
        $userReview = DB::select($query);
        if($userReview)
        {
            $maxVendor = count($userReview);
            $maxPage = CEIL($maxVendor/2);
            if($take)
            {
               $maxPage = CEIL($maxVendor/$take);
            }
            $userReviewCollection = $this->paginateFilter($userReview,$take);
           
        }
         return response([
                    'data' => [
                    'customer' =>  Helper::formatDateCustomer($customer),
                    'reviews'   =>  Helper::formatListReviews($userReviewCollection,$customer),
                    'maxPage' =>  $maxPage
                    
                    ],
                ],Response::HTTP_OK);
    }
    /**
     * @SWG\Get(
     *   path="/v1/homes/getProfileMyAccount",
     *   description="",
     *   summary="",
     *   operationId="api.v1.homes.getProfileMyAccount",
     *   produces={"application/json"},
     *   tags={"Home"},
     *   @SWG\Response(response=401, description="unauthorized"),
     *   @SWG\Response(response=200, description="Success"),
     *   security={
     *       {"api_key": {}}
     *   }
     * )
     */
    public function getProfileMyAccount(Request $request)
    {
        $customer = $request->user();
        $customerChildren = CustomerChildrens::where('user_id',$customer->id)->get();
        return response([
                    'data' => [
                    'customer'   =>  Helper::formatDateCustomer($customer),
                    'childrens'   =>  Helper::formatListChildern($customerChildren)
                    ],
                ],Response::HTTP_OK);
    }

    /**
     * @SWG\Post(
     *   path="/v1/homes/editReview",
     *   description="",
     *   summary="",
     *   operationId="api.v1.homes.editReview",
     *   produces={"application/json"},
     *   tags={"Home"},
     *   @SWG\Parameter(
     *     name="body",
     *     in="body",
     *     description="Target customer.",
     *     required=true,
     *    @SWG\Schema(ref="#/definitions/EditReview")
     *   ),
     *   @SWG\Response(response=401, description="unauthorized"),
     *   @SWG\Response(response=200, description="Success"),
     *   security={
     *       {"api_key": {}}
     *   }
     * )
     */
    public function editReview(EditReviewRequest $request)
    {
        $review_id             = $request->review_id ? $request->review_id : false;
        $content             = $request->content ? $request->content : false;
        $title             = $request->title ? $request->title : false;
        $rating             = is_numeric($request->rating) ? $request->rating : false;
        $userReview = UserReview::find($review_id);
        $customer = $request->user();
        if(isset($userReview) && !empty($userReview) && count($userReview) > 0)
        {
            $userReview->user_id = $customer->id;
            $userReview->vendor_id = $userReview->vendor_id;
            $userReview->title = $title;
            $userReview->content = $content;
            $userReview->rating = $rating;
            $userReview->save();
            $vendorProfile = VendorProfile::where('user_id',$userReview->vendor_id)->first();
            $reviews = UserReview::where('vendor_id',$userReview->vendor_id)->where('status',1)->whereNull('is_deleted')->get();
            if(isset($vendorProfile) && !empty($vendorProfile))
            {
                 $this->updateRatingPoint($vendorProfile,$reviews);
            }
           
            return response([
                    'data' => [
                    'message'   =>   'Edit Review Success'],
                ],Response::HTTP_OK);
        } 
        return response([
                    'error' => [
                    'message'   =>   'Edit Review Failed'],
                ],Response::HTTP_OK);
    }

    /**
     * @SWG\Post(
     *   path="/v1/homes/deleteReview",
     *   description="",
     *   summary="",
     *   operationId="api.v1.homes.deleteReview",
     *   produces={"application/json"},
     *   tags={"Home"},
     *   @SWG\Parameter(
     *     name="body",
     *     in="body",
     *     description="Target customer.",
     *     required=true,
     *    @SWG\Schema(ref="#/definitions/Review")
     *   ),
     *   @SWG\Response(response=401, description="unauthorized"),
     *   @SWG\Response(response=200, description="Success"),
     *   security={
     *       {"api_key": {}}
     *   }
     * )
     */
    public function deleteReview(ReviewRequest $request)
    {
        $userReview = UserReview::find($request->review_id);
        $ownerReviewId = $userReview->vendor_id;
        if(isset($userReview) && !empty($userReview))
        {
            $reviewReply = ReviewReply::where('review_id',$request->review_id)->first();
            if(isset($reviewReply) && !empty($reviewReply))
            {
                $reviewReply->delete();
            }
            $userReview->delete();

            // update rating point after delete review
            $reviews = UserReview::where('vendor_id',$ownerReviewId)->where('status',1)->whereNull('is_deleted')->get();
            $vendorProfile = VendorProfile::where('user_id',$ownerReviewId)->first();
            if(isset($vendorProfile) && !empty($vendorProfile))
            {
                 $this->updateRatingPoint($vendorProfile,$reviews);
            }

            return response([
                    'data' => [
                    'message'   =>  'Detele Review Success'],
                ],Response::HTTP_OK);
        }
        return response([
                    'error' => [
                    'message'   =>  'No Result Found.'],
                ],Response::HTTP_OK);
    }
    /**
     * @SWG\Post(
     *   path="/v1/homes/postReviewDetail",
     *   description="",
     *   summary="",
     *   operationId="api.v1.homes.postReviewDetail",
     *   produces={"application/json"},
     *   tags={"Home"},
     *   @SWG\Parameter(
     *     name="body",
     *     in="body",
     *     description="Target customer.",
     *     required=true,
     *    @SWG\Schema(ref="#/definitions/Review")
     *   ),
     *   @SWG\Response(response=401, description="unauthorized"),
     *   @SWG\Response(response=200, description="Success"),
     *   security={
     *       {"api_key": {}}
     *   }
     * )
     */
    public function postReviewDetail(ReviewRequest $request)
    {
        $userReview = UserReview::find($request->review_id);
        $reviewReply = ReviewReply::where('review_id',$request->review_id)->first();
        $customer = $request->user();
        if(isset($userReview) && !empty($userReview))
        {
            $reply =  Helper::formatReviewReply($reviewReply);
            if(count($reply) > 0)
            {
                 return response([
                    'data' => [
                        'review'   =>   Helper::formatReviewDetail($userReview,$customer),
                        'reviewReply'   =>   $reply
                    ],
                ],Response::HTTP_OK);
            }
            else
            {
                 return response([
                    'data' => [
                        'review'   =>   Helper::formatReviewDetail($userReview,$customer)
                    ],
                ],Response::HTTP_OK); 
            }
           
        }
        return response([
                    'error' => [
                    'message'   =>  'No Result Found.'],
                ],Response::HTTP_OK);
    }

    /**
     * @SWG\Post(
     *   path="/v1/homes/sendReview",
     *   description="",
     *   summary="",
     *   operationId="api.v1.homes.sendReview",
     *   produces={"application/json"},
     *   tags={"Home"},
     *   @SWG\Parameter(
     *     name="body",
     *     in="body",
     *     description="Target customer.",
     *     required=true,
     *    @SWG\Schema(ref="#/definitions/SendReview")
     *   ),
     *   @SWG\Response(response=401, description="unauthorized"),
     *   @SWG\Response(response=200, description="Success"),
     *   security={
     *       {"api_key": {}}
     *   }
     * )
     */
    public function sendReview(SendReviewRequest $request)
    {
        $content             = $request->content ? $request->content : false;
        $title             = $request->title ? $request->title : false;
        $rating             = is_numeric($request->rating) ? $request->rating : false;
    
        $vendorName = '';
        $vendor = DB::table('users')->Join('role_users', 'users.id', '=', 'role_users.user_id')->where('id', $request->vendor_id)->where('status', 1)->where('role_id', 3)->first();
        $customer = $request->user();
        if(isset($vendor) && !empty($vendor) && count($vendor) > 0)
        {
            $userReview = new UserReview();
            $userReview->user_id = $customer->id;
            $userReview->vendor_id = $request->vendor_id;
            $userReview->title = $title;
            $userReview->content = $content;
            $userReview->rating = $rating;
            $userReview->status = 1;
            $userReview->save();
            $vendorProfile = VendorProfile::where('user_id',$userReview->vendor_id)->first();
            $reviews = UserReview::where('vendor_id',$userReview->vendor_id)->where('status',1)->whereNull('is_deleted')->get();
            if(isset($vendorProfile) && !empty($vendorProfile))
            {
                 $this->updateRatingPoint($vendorProfile,$reviews);
                 $vendorName = $vendorProfile->business_name; 
            }
            // $this->mailer->send('mail.vendor_review', ['userName' => $vendorName], function (Message $m) use ($vendor) {
            // $m->to($vendor->email)->subject('MummyFique Apps: Send Mail Confirm');
            // });
            
            //event send email notification to vendor
            $vendor = $this->vendorRepository->find($request->vendor_id);
            event(new SendReviewVendor($vendor));

            return response([
                    'data' => [
                    'message'   =>   'Send Review Success'],
                ],Response::HTTP_OK);
        } 
        return response([
                    'error' => [
                    'message'   =>   'Send Review Failed'],
                ],Response::HTTP_OK);
    }
    /**
     * @SWG\Get(
     *   path="/v1/homes/getMLink",
     *   description="",
     *   summary="",
     *   operationId="api.v1.homes.getMLink",
     *   produces={"application/json"},
     *   tags={"Home"},
     *   @SWG\Response(response=401, description="unauthorized"),
     *   @SWG\Response(response=200, description="Success"),
     *   security={
     *       {"api_key": {}}
     *   }
     * )
     */
    public function getMLink(Request $request)
    {
        $articles =[];
        $image_1 = AdvertisementItem::where('adv_id',7)->where('status',1)->first();
        $advertisement = Helper::getAdvertisement($image_1);
        if(count($advertisement) > 0)
        {
            $articles += [0 => $advertisement];
        }
        try
        {

            $client = new \GuzzleHttp\Client();
            if(count($advertisement) > 0)
            {
                $res = $client->request('GET', 'http://mummyfique.com/api/index.php?token=9M02sk2WzW&limit=4');
            }
            else
            {
                $res = $client->request('GET', 'http://mummyfique.com/api/index.php?token=9M02sk2WzW&limit=5');
            }
            

             $items = json_decode($res->getBody(),true);
            if(isset($items['data']) && !empty($items['data']))
            {
                $collection = collect($items['data']);; 
                // $sorted = $collection->sortByDesc('Id');
                $sortedResult = $collection->values()->all();
                foreach ($sortedResult as $key => $value) {
                    if(isset($value['thumbnailUrl']) && !empty($value['thumbnailUrl']))
                    {
                        $arr = explode('/', $value['thumbnailUrl']);
                        $imageName = $arr[count($arr) - 1];
                        $result = str_replace($imageName, urlencode($imageName), $value['thumbnailUrl']);
                        $value['thumbnailUrl'] = $result;
                    }
                    if(count($advertisement) > 0)
                    {
                       $articles += [$key+1 => $value];
                    }
                    else
                    {
                        $articles += [$key => $value];
                    }
                    
                }
            }
        }
        catch (\Exception $e)
        {
            
        }
        return response([
                    'data' => [
                     'image' => 
                        $articles
                        
                    ],
                ],Response::HTTP_OK);
    }

    /**
     * @SWG\Get(
     *   path="/v1/homes/getSendMessage?receiver_id={receiver_id}",
     *   description="",
     *   summary="",
     *   operationId="api.v1.homes.getSendMessage",
     *   produces={"application/json"},
     *   tags={"Home"},
     *   @SWG\Parameter(
     *     description="",
     *     in="path",
     *     name="receiver_id",
     *     required=true,
     *     type="integer",
     *     default="" 
     *   ),
     *   @SWG\Response(response=401, description="unauthorized"),
     *   @SWG\Response(response=200, description="Success"),
     *   security={
     *       {"api_key": {}}
     *   }
     * )
     */
    public function getSendMessage(Request $request)
    {
        $receiver_id             = is_numeric($request->receiver_id) ? $request->receiver_id : false;

        $receiver = Customer::find($receiver_id);

         $vendor = DB::table('users')->Join('role_users', 'users.id', '=', 'role_users.user_id')->where('id', $receiver_id)->where('status', 1)->where('role_id', 3)->first();
         if(isset($vendor) && !empty($vendor))
         {
            return response([
                    'data' => [
                    'receiver'   =>   Helper::formatGetVendor($vendor)],
                ],Response::HTTP_OK);
         }
         return response([
                    'error' => [
                    'message'   =>  'No Result Found.'],
                ],Response::HTTP_OK);

    }

    /**
     * @SWG\Post(
     *   path="/v1/homes/postSendMessage",
     *   description="",
     *   summary="",
     *   operationId="api.v1.homes.postSendMessage",
     *   produces={"application/json"},
     *   tags={"Home"},
     *   @SWG\Parameter(
     *     name="body",
     *     in="body",
     *     description="Target customer.",
     *     required=true,
     *    @SWG\Schema(ref="#/definitions/SendMessage")
     *   ),
     *   @SWG\Response(response=401, description="unauthorized"),
     *   @SWG\Response(response=200, description="Success"),
     *   security={
     *       {"api_key": {}}
     *   }
     * )
     */
    public function postSendMessage(SendMessageRequest $request)
    {
        $message             = $request->message ? $request->message : false;
        $subject             = $request->subject ? $request->subject : false;
        $receiver_id         = is_numeric($request->receiver_id) ? $request->receiver_id : false;

        $sender = $request->user();
        $newSendMessage = new SendMessage();
        $newSendMessage->sender_id = $sender->id;
        $newSendMessage->receiver_id = $receiver_id;
        $newSendMessage->subject = $subject;
        $newSendMessage->message = $message;
        $newSendMessage->status = 1;
        $newSendMessage->is_customer_read = 1;
        $newSendMessage->is_vendor_read = 0;
        $newSendMessage->save();
        $check = Helper::reducePoint($receiver_id,$sender->id,12);
        Helper::checkActivity($receiver_id,$sender->id,12,$check);

        //event send email to vendor
        $vendor = $this->vendorRepository->find($receiver_id);
        event(new SendMessageVendor($vendor));       
        
        // debug
        // $emails = [];
        // $emails[] = $vendor->email;
        // $vendorSetting = $vendor->vendorSetting;
        // $vendorAdditionEmails = $vendorSetting->addition_emails;
        // if(!empty($vendorAdditionEmails)){
        //     $arr = explode(',', $vendorAdditionEmails);
        //     if(!empty($arr)){
        //         foreach ($arr as $key => $item) {
        //             if(!in_array(trim($item), $emails)){
        //                 $emails[] = trim($item);
        //             }
        //         }
        //     }
        // }
        // if(sizeof($emails)){
        //     $mail = app(\Illuminate\Mail\Mailer::class);
        //     $vendor_name = $vendor->profile ? $vendor->profile->business_name : "Business name";
        //     $url = env('APP_URL_WEBSITE') . "/vendor/messages";
        //     $message = 'Dear '.$vendor_name.',<br/>You have new message(s) from potential customer(s). Kindly login to <a href="'. $url .'">Mummyfique for Business</a> site to view the details.';
        //     foreach ($emails as $key => $item) {
        //         $mail->send('mail.template.template_business_mail', ['content' => $message], function (\Illuminate\Mail\Message $m) use($item){
        //             $m->to($item)->subject("[Mummyfique] You have a new message!!");
        //         });
        //         // \Illuminate\Support\Facades\Mail::to($item)->queue(new \App\Mummy\Api\V1\Mail\SendMessageToVendor($vendor));
        //     } 
        // }

        return response([
                    'data' => [
                    'message'   =>   'Send Message Success'],
                ],Response::HTTP_OK);
    }

    /**
     * @SWG\Post(
     *   path="/v1/homes/MessageScreen",
     *   description="",
     *   summary="",
     *   operationId="api.v1.homes.MessageScreen",
     *   produces={"application/json"},
     *   tags={"Home"},
     *   @SWG\Parameter(
     *     name="body",
     *     in="body",
     *     description="Target customer.",
     *     required=true,
     *    @SWG\Schema(ref="#/definitions/MessageScreen")
     *   ),
     *   @SWG\Response(response=401, description="unauthorized"),
     *   @SWG\Response(response=200, description="Success"),
     *   security={
     *       {"api_key": {}}
     *   }
     * )
     */
    public function MessageScreen(Request $request)
    {
        $type             = is_numeric($request->type) ? $request->type : false;
        $sort_by             = is_numeric($request->sort_by) ? $request->sort_by : false;
        $is_read             = is_numeric($request->is_read) ? $request->is_read : false;
        $page             = is_numeric($request->page) ? $request->page : false;
        $take             = is_numeric($request->take) ? $request->take : false;
        if(!$page)
                $page =1;
            if(!$take)
                $take = config('constant.default.take');

        if($type == config('constant.inbox.inbox'))
            return $this->messageInbox($type,$sort_by,$is_read,$page,$take,$request);
        if($type == config('constant.inbox.send'))
            return $this->messageSend($type,$sort_by,$page,$take,$request);
        if($type == config('constant.inbox.trash'))
            return $this->messageTrash($type,$sort_by,$page,$take,$request);

        return response([
                    'error' => [
                    'message'   =>  'No Result Found.'],
                ],Response::HTTP_OK);

    }

    /**
     * @SWG\Get(
     *   path="/v1/homes/getReadMessage?message_id={message_id}",
     *   description="",
     *   summary="",
     *   operationId="api.v1.homes.getReadMessage",
     *   produces={"application/json"},
     *   tags={"Home"},
     *   @SWG\Parameter(
     *     description="",
     *     in="path",
     *     name="message_id",
     *     required=true,
     *     type="integer",
     *     default="" 
     *   ),
     *   @SWG\Response(response=401, description="unauthorized"),
     *   @SWG\Response(response=200, description="Success"),
     *   security={
     *       {"api_key": {}}
     *   }
     * )
     */
    public function getReadMessage(Request $request)
    {
        $message_id             = is_numeric($request->message_id) ? $request->message_id : false;

        $user = $request->user();
        $message = SendMessage::where('id',$message_id)->first();
         if(isset($message) && !empty($message))
         {
            $message->is_customer_read = 1;
            $message->save();
            Helper::checkActivity($message->sender_id,$user->id,10,false);
            return response([
                    'data' => [
                    'message'   =>   'Message is Read'],
                ],Response::HTTP_OK);
         }
         return response([
                    'error' => [
                    'message'   =>  'No Result Found.'],
                ],Response::HTTP_OK);

    }

    /**
     * @SWG\Post(
     *   path="/v1/homes/deleteMessage",
     *   description="",
     *   summary="",
     *   operationId="api.v1.homes.deleteMessage",
     *   produces={"application/json"},
     *   tags={"Home"},
     *   @SWG\Parameter(
     *     name="body",
     *     in="body",
     *     description="Target customer.",
     *     required=true,
     *    @SWG\Schema(ref="#/definitions/DeleteMessage")
     *   ),
     *   @SWG\Response(response=401, description="unauthorized"),
     *   @SWG\Response(response=200, description="Success"),
     *   security={
     *       {"api_key": {}}
     *   }
     * )
     */
    public function deleteMessage(Request $request)
    {
        $dateNow = Carbon::now();
        $message_id             = is_numeric($request->message_id) ? $request->message_id : false;

        $message = SendMessage::find($message_id);
         if(isset($message) && !empty($message))
         {
            $message->is_customer_deleted = $dateNow;
           $message->save();
            return response([
                        'data' => [
                        'message'   =>  'Delete Message Success'],
                    ],Response::HTTP_OK);
         }
         return response([
                    'error' => [
                    'message'   =>  'No Result Found.'],
                ],Response::HTTP_OK);

    }

    /**
     * @SWG\Post(
     *   path="/v1/homes/postReportReview",
     *   description="",
     *   summary="",
     *   operationId="api.v1.homes.postReportReview",
     *   produces={"application/json"},
     *   tags={"Home"},
     *   @SWG\Parameter(
     *     name="body",
     *     in="body",
     *     description="Target customer.",
     *     required=true,
     *    @SWG\Schema(ref="#/definitions/ReportReview")
     *   ),
     *   @SWG\Response(response=401, description="unauthorized"),
     *   @SWG\Response(response=200, description="Success"),
     *   security={
     *       {"api_key": {}}
     *   }
     * )
     */
    public function postReportReview(ReportReviewRequest $request)
    {
        $review_id             = is_numeric($request->review_id) ? $request->review_id : false;
        $content             = $request->content ? $request->content : false;

        $customer = $request->user();
        $userReview = UserReview::find($review_id);
        if($userReview)
        {
            $userReport = new UserReport();
            $userReport->review_id = $review_id;
            $userReport->user_id = $customer->id;
            $userReport->content = $content;
            $userReport->status = 1;
            $userReport->save();
            return response([
                            'data' => [
                            'message'   =>  'Send Report Success'],
                        ],Response::HTTP_OK);

        }
        return response([
                    'error' => [
                    'message'   =>  'No Result Found.'],
                ],Response::HTTP_OK);
        
    }


    /**
     * @SWG\Get(
     *   path="/v1/homes/getCategory",
     *   description="",
     *   summary="",
     *   operationId="api.v1.homes.getCategory",
     *   produces={"application/json"},
     *   tags={"Home"},
     *   @SWG\Response(response=401, description="unauthorized"),
     *   @SWG\Response(response=200, description="Success"),
     *   security={
     *       {"api_key": {}}
     *   }
     * )
     */
    public function getCategory(Request $request)
    {
        $categories = Category::all();
        return response([
                    'data' => [
                    'categories'   => $categories 
                    ],
                ],Response::HTTP_OK);
    }
    /**
     * @SWG\Post(
     *   path="/v1/homes/postSubCategory",
     *   description="",
     *   summary="",
     *   operationId="api.v1.homes.postSubCategory",
     *   produces={"application/json"},
     *   tags={"Home"},
     *   @SWG\Parameter(
     *     name="body",
     *     in="body",
     *     description="Target customer.",
     *     required=true,
     *    @SWG\Schema(ref="#/definitions/SubCategory")
     *   ),
     *   @SWG\Response(response=401, description="unauthorized"),
     *   @SWG\Response(response=200, description="Success"),
     *   security={
     *       {"api_key": {}}
     *   }
     * )
     */
    public function postSubCategory(Request $request)
    {
       $subCategories = SubCategory::all();
        return response([
                    'data' => [
                    'subCategories'   => $subCategories 
                    ],
                ],Response::HTTP_OK);
        
    }
    /**
     * @SWG\Get(
     *   path="/v1/homes/getPriceRange",
     *   description="",
     *   summary="",
     *   operationId="api.v1.homes.getPriceRange",
     *   produces={"application/json"},
     *   tags={"Home"},
     *   @SWG\Response(response=401, description="unauthorized"),
     *   @SWG\Response(response=200, description="Success"),
     *   security={
     *       {"api_key": {}}
     *   }
     * )
     */
    public function getPriceRange(Request $request)
    {
        $priceRange = PriceRange::all();
        return response([
                    'data' => [
                    'priceRange'   => Helper::GetListPriceRange($priceRange)
                    ],
                ],Response::HTTP_OK);
    }
    /**
     * @SWG\Get(
     *   path="/v1/homes/getCountries",
     *   description="",
     *   summary="",
     *   operationId="api.v1.homes.getCountries",
     *   produces={"application/json"},
     *   tags={"Home"},
     *   @SWG\Response(response=401, description="unauthorized"),
     *   @SWG\Response(response=200, description="Success"),
     *   security={
     *       {"api_key": {}}
     *   }
     * )
     */
    public function getCountries(Request $request)
    {
        $countries = [];
        $arrCountry = [];
        $j = 0;
        $queryCountry = "select distinct(vl.country_id) from mm__vendors_location vl join users u on u.id = vl.user_id and u.is_deleted is null and u.status = 1 where vl.is_deleted IS NULL";
        $resultCountry = \DB::select($queryCountry);
        if(count($resultCountry) > 0)
        {
            foreach ($resultCountry as $key => $value) {
                $country = Country::find($value->country_id);
                if(isset($country) && !empty($country))
                {
                    $arrCountry += [$key => $country];
                }
            }
        }
        if(count($arrCountry) > 0)
        {
            foreach ($arrCountry as $keyArrCountry => $valueArrCountry) {
                $countries += [$j => $valueArrCountry];
                $j++;
            }
        }
         return response([
                    'data' => [
                    'countries'   => $countries
                    ],
                ],Response::HTTP_OK);
    }
    /**
     * @SWG\Post(
     *   path="/v1/homes/postCities",
     *   description="",
     *   summary="",
     *   operationId="api.v1.homes.postCities",
     *   produces={"application/json"},
     *   tags={"Home"},
     *   @SWG\Parameter(
     *     name="body",
     *     in="body",
     *     description="Target customer.",
     *     required=true,
     *    @SWG\Schema(ref="#/definitions/Cities")
     *   ),
     *   @SWG\Response(response=401, description="unauthorized"),
     *   @SWG\Response(response=200, description="Success"),
     *   security={
     *       {"api_key": {}}
     *   }
     * )
     */
    public function postCities(Request $request)
    {
        $country_id             = !empty($request->country_id) ? $request->country_id : false;
        $cities = [];
        $arrCity = [];
        $i = 0;
        $queryCity = "select distinct(city_id) from mm__vendors_location where is_deleted IS NULL";
        if($country_id && $country_id != '{country_id}')
        {
            $countries = "(";
            foreach ($country_id as $key => $value) {
                if($key == 0)
                {
                    $countries .= "$value";
                }
                else
                {
                    $countries .= ",$value";
                }
            }
            $countries .= ")";
            $queryCity = "select distinct(city_id) from mm__vendors_location where is_deleted IS NULL AND country_id IN $countries";
        }
        $resultCity = \DB::select($queryCity);
        if(count($resultCity) > 0)
        {
            foreach ($resultCity as $keyCity => $valueCity) {
                $city = City::find($valueCity->city_id);
                if(isset($city) && !empty($city))
                {
                    $arrCity += [$keyCity => $city];
                }
            }
        }

        if(count($arrCity) > 0)
        {
            foreach ($arrCity as $keyArrCity => $valueArrCity) {
                $cities += [$i => $valueArrCity];
                $i++;
            }
        }
         return response([
                    'data' => [
                    'cities'   => $cities
                    ],
                ],Response::HTTP_OK);
    }

    /**
     * @SWG\Get(
     *   path="/v1/getVersion",
     *   description="",
     *   summary="",
     *   operationId="api.v1.homes.getVersion",
     *   produces={"application/json"},
     *   tags={"Profiles"},
     *   @SWG\Response(response=401, description="unauthorized"),
     *   @SWG\Response(response=200, description="Success"),
     *   security={
     *   }
     * )
     */
    public function getVersion()
    {
        $data = [];
        if(count(config('constant.version'))){
            foreach (config('constant.version') as $key => $item) {
                $data[$key] = Helper::getSystemSetting($item);
            }
        }
        return response([
                    'data' => $data,
                ],Response::HTTP_OK);
    }

    public function queryBuild($country_id,$city_id,$category_id,$sub_category_id,$price_range_id,$name)
    {
        $query = "select tb.id as user_id from users AS tb JOIN mm__vendors_portfolios AS tb1 ON tb.id = tb1.vendor_id JOIN role_users AS tb2 ON tb.id = tb2.user_id JOIN mm__vendors_profile AS tb3 ON tb.id = tb3.user_id JOIN mm__vendors_location AS tb4 ON tb.id =tb4.user_id JOIN mm__vendors_category AS tb5 ON tb.id =tb5.user_id WHERE tb2.role_id = 3 AND tb.status = 1 AND tb1.is_deleted IS NULL AND tb1.status = 1 AND tb.is_deleted IS NULL AND tb4.is_deleted IS NULL";
        if($name)
        {
            // $query .=" AND (tb3.business_name LIKE '%$name%' OR tb1.title LIKE '%$name%' OR tb1.description LIKE '%$name%')";
            $nameArr = $this->service->getSearchNameArray($name);

            $whereBusinessName = $whereTitle = $whereDescription = [];
            foreach($nameArr as $item){
                $whereBusinessName[] =  " tb3.business_name like '%$item%' ";
                $whereTitle[] =  " tb1.title like '%$item%' ";
                $whereDescription[] =  " tb1.description like '%$item%' ";
            }

            $query .= " AND ( " . implode("AND", $whereBusinessName) . " OR " . implode("AND", $whereTitle) .  " OR " . implode("AND", $whereDescription) . ")";
        }
        if(isset($country_id) && !empty($country_id))
        {
            $countries = "(";
            foreach ($country_id as $key => $value) {
                if($key == 0)
                {
                    $countries .= "$value";
                }
                else
                {
                    $countries .= ",$value";
                }
            }
            $countries .= ")";
             $query .= " AND tb4.country_id IN $countries";
        }
        if(isset($city_id) && !empty($city_id))
        {
            $cities = "(";
            foreach ($city_id as $key => $value) {
                if($key == 0)
                {
                    $cities .= "$value";
                }
                else
                {
                    $cities .= ",$value";
                }
            }
            $cities .= ")";
             $query .= " AND tb4.city_id IN $cities";
        }
        if(isset($category_id) && !empty($category_id))
        {
            $categories = "(";
            foreach ($category_id as $key => $value) {
                if($key == 0)
                {
                    $categories .= "$value";
                }
                else
                {
                    $categories .= ",$value";
                }
            }
            $categories .= ")";
            $query .= " AND tb5.category_id IN $categories";
        }
        if(isset($sub_category_id) && !empty($sub_category_id))
        {
            $subCategories = "(";
            foreach ($sub_category_id as $key => $value) {
                if($key == 0)
                {
                    $subCategories .= "$value";
                }
                else
                {
                    $subCategories .= ",$value";
                }
            }
            $subCategories .= ")";
            $query .= " AND tb5.sub_category_id IN $subCategories";
        }
        if(isset($price_range_id) && !empty($price_range_id))
        {
             $priceRange = "(";
            foreach ($price_range_id as $key => $value) {
                if($key == 0)
                {
                    $priceRange .= "$value";
                }
                else
                {
                    $priceRange .= ",$value";
                }
            }
            $priceRange .= ")";
            $query .= " AND tb5.price_range_id IN $priceRange";
        }
        $query .= " Group By user_id";
        
        return $query;
    }
    public function paginateFilter($items,$perPage)
    {
        $pageStart = \Request::get('page', 1);
        // Start displaying items from this number;
        $offSet = ($pageStart * $perPage) - $perPage; 

        // Get only the items you need using array_slice
        $itemsForCurrentPage = array_slice($items, $offSet,$perPage);
        return new LengthAwarePaginator($itemsForCurrentPage, count($items), $perPage,Paginator::resolveCurrentPage(), array('path' => Paginator::resolveCurrentPath()));
    }
    public function messageInbox($type,$sort_by,$is_read,$page,$take,$request)
    {
        $arrayMessage = [];
        $maxPage = 1;
            $customer = $request->user();

            $query = "select tb.* from mm__send_message AS tb WHERE receiver_id = $customer->id AND is_customer_deleted IS NULL";
            $querySend = "select tb.* from mm__send_message AS tb WHERE sender_id = $customer->id AND is_customer_deleted IS NULL";
            $queryTrash = "select tb.* from mm__send_message AS tb WHERE (sender_id = $customer->id AND is_customer_deleted IS NOT NULL) OR (receiver_id = $customer->id AND is_customer_deleted IS NOT NULL)";
            $countAllMessages = count(DB::select($query));
            $countInbox = count(DB::select($query));
            $countSend = count(DB::select($querySend));
            $countTrash = count(DB::select($queryTrash));
            $countReadMessage = count(DB::select("select tb.* from mm__send_message AS tb WHERE receiver_id = $customer->id AND tb.is_customer_read = 1 AND is_customer_deleted IS NULL"));
            $unReadMessages = SendMessage::where('receiver_id',$customer->id)->where('is_customer_read',0)->whereNull('is_customer_deleted')->get();
            $countUnReadMessage = count($unReadMessages);
            if(isset($is_read))
            {
                if($is_read != 2)
                {
                    $query .= " AND tb.is_customer_read = $is_read";
                }
                
            }
            if($sort_by == config('constant.inbox.LatestMessage'))
            {
                $query .= " ORDER BY id DESC";
            }
            if($sort_by == config('constant.inbox.OldestMessage'))
            {
                $query .= " ORDER BY id ASC";
            }
            if($sort_by == config('constant.inbox.MakeRead'))
            {
                $query .= " ORDER BY id DESC";
                if(isset($unReadMessages) && !empty($unReadMessages) && count($unReadMessages) >0)
                {
                    foreach ($unReadMessages as $key => $value) {
                        $findSendMessage = SendMessage::find($value->id);
                        $findSendMessage->is_customer_read = 1;
                        $findSendMessage->save();
                    }
                    $unReadMessages = SendMessage::where('receiver_id',$customer->id)->where('is_customer_read',0)->whereNull('is_customer_deleted')->get();
                    $countUnReadMessage = count($unReadMessages);
                    $countReadMessage = count(DB::select("select tb.* from mm__send_message AS tb WHERE receiver_id = $customer->id AND tb.is_customer_read = 1 AND is_customer_deleted IS NULL"));
                    
                }
            }
            $messages = DB::select($query);
            if($messages)
            {
                $maxVendor = count($messages);
                $maxPage = CEIL($maxVendor/2);
                if($take)
                {
                   $maxPage = CEIL($maxVendor/$take);
                }
                $arrayMessage = $this->paginateFilter($messages,$take);
                
            }
            return response([
                        'data' => [
                        'messages'   =>   Helper::formatListMessage($arrayMessage),
                        'maxPage'   =>   $maxPage,
                        'countInbox'   =>   $countInbox,
                        'countSend'   =>   $countSend,
                        'countTrash'   =>   $countTrash,
                        'countReadMessage'   =>   $countReadMessage,
                        'countUnReadMessage'   =>   $countUnReadMessage
                        ],
                    ],Response::HTTP_OK);
    }
    public function messageSend($type,$sort_by,$page,$take,$request)
    {
        $arrayMessage = [];
        $maxPage = 1;
            $customer = $request->user();

            $query = "select tb.* from mm__send_message AS tb WHERE sender_id = $customer->id AND is_customer_deleted IS NULL";
             $queryInbox = "select tb.* from mm__send_message AS tb WHERE receiver_id = $customer->id AND is_customer_deleted IS NULL";
            $queryTrash = "select tb.* from mm__send_message AS tb WHERE (sender_id = $customer->id AND is_customer_deleted IS NOT NULL) OR (receiver_id = $customer->id AND is_customer_deleted IS NOT NULL)";
            $countAllMessages = count(DB::select($query));
            $countInbox = count(DB::select($queryInbox));
            $countSend = count(DB::select($query));
            $countTrash = count(DB::select($queryTrash));
            if($sort_by == config('constant.inbox.LatestMessage'))
            {
                $query .= " ORDER BY id DESC";
            }
            if($sort_by == config('constant.inbox.OldestMessage'))
            {
                $query .= " ORDER BY id ASC";
            }
            
            $messages = DB::select($query);
            if($messages)
            {
                $maxVendor = count($messages);
                $maxPage = CEIL($maxVendor/2);
                if($take)
                {
                   $maxPage = CEIL($maxVendor/$take);
                }
                $arrayMessage = $this->paginateFilter($messages,$take);
                
            }
            return response([
                        'data' => [
                        'messages'   =>   Helper::formatListMessageSend($arrayMessage),
                        'maxPage'   =>   $maxPage,
                        'countInbox'   =>   $countInbox,
                        'countSend'   =>   $countSend,
                        'countTrash'   =>   $countTrash
                        ],
                    ],Response::HTTP_OK);
    }
    public function messageTrash($type,$sort_by,$page,$take,$request)
    {
        $arrayMessage = [];
        $maxPage = 1;
            $customer = $request->user();

            $query = "select tb.* from mm__send_message AS tb WHERE (sender_id = $customer->id AND is_customer_deleted IS NOT NULL) OR (receiver_id = $customer->id AND is_customer_deleted IS NOT NULL)";
            $queryInbox = "select tb.* from mm__send_message AS tb WHERE receiver_id = $customer->id AND is_customer_deleted IS NULL";
            $querySend = "select tb.* from mm__send_message AS tb WHERE sender_id = $customer->id AND is_customer_deleted IS NULL";
            $countAllMessages = count(DB::select($query));
            $countInbox = count(DB::select($queryInbox));
            $countSend = count(DB::select($querySend));
            $countTrash = count(DB::select($query));
            if($sort_by == config('constant.inbox.LatestMessage'))
            {
                $query .= " ORDER BY id DESC";
            }
            if($sort_by == config('constant.inbox.OldestMessage'))
            {
                $query .= " ORDER BY id ASC";
            }
            
            $messages = DB::select($query);
            if($messages)
            {
                $maxVendor = count($messages);
                $maxPage = CEIL($maxVendor/2);
                if($take)
                {
                   $maxPage = CEIL($maxVendor/$take);
                }
                $arrayMessage = $this->paginateFilter($messages,$take);
                
            }
            return response([
                        'data' => [
                        'messages'   =>   Helper::formatListMessageTrash($arrayMessage,$customer),
                        'maxPage'   =>   $maxPage,
                        'countInbox'   =>   $countInbox,
                        'countSend'   =>   $countSend,
                        'countTrash'   =>   $countTrash
                        ],
                    ],Response::HTTP_OK);
    }
    protected function getVendorPlan($vendor_id)
    {
        $vendor_type_id = [];
        $checkVendorPackage = PlanSubscription::where('user_id',$vendor_id)->get();
        if(count($checkVendorPackage) > 0)
        {
            foreach ($checkVendorPackage as $key9893 => $value9893) {

                $vendor_type_id += [$key9893=> $value9893->plan_id];
            }
        }
         $plan_id = 2;
        if(in_array(2, $vendor_type_id)){
            $plan_id = 2;
        }
        if(in_array(3, $vendor_type_id)){
            $plan_id = 3;
        }
        if (in_array(4, $vendor_type_id)){
            $plan_id = 4;
        }
        return $plan_id;
    }
    protected function getAllFreeVendor($arrVendors,$arrAllVendorId)
    {
        $listFreeMember = [];
        $id = [];
        if(count($arrVendors) > 0)
        {
            foreach ($arrVendors as $key => $value) {
                $id += [$key => $value->id ];
            }
        }
        if(count($arrAllVendorId) > 0)
        {
            foreach ($arrAllVendorId as  $item) {
               if(!in_array($item,$id)) {
                    array_push($listFreeMember, $item);
                }
            }
        }
        if(count($listFreeMember) > 0)
        {
            foreach ($listFreeMember as $freeMember) {
                $arrVendors +=[$freeMember  =>  Customer::find($freeMember)];
            }
        }
        return $arrVendors;
        
    }
    protected function updateRatingPoint($vendorProfile,$reviews)
    {
        $total = 0;
        $point = 0;
        $countReview = count($reviews);
        if($countReview > 0)
        {
            foreach ($reviews as $key => $review) {
                if($review->rating)
                {
                     $total += $review->rating;
                }
            }
            $point = ROUND(($total / $countReview) * 2);
        }

        if(isset($vendorProfile) && !empty($vendorProfile))
        {
            $vendorProfile->rating_points = $point;
            $vendorProfile->save();
        }
        
        return $vendorProfile;
    }
    
    public function showConfig(){
        dd($_ENV);
    }
}
