<?php

namespace App\Mummy\Api\V1\Controllers;

use Illuminate\Http\Request;

use Carbon\Carbon;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;
use Illuminate\Pagination\Paginator;
use Illuminate\Pagination\LengthAwarePaginator;
use App\Mummy\Api\V1\Entities\UserRole;
use App\Mummy\Api\V1\Entities\Customer;
use App\Mummy\Api\V1\Entities\Vendors\VendorComment;
use App\Mummy\Api\V1\Entities\Vendors\VendorCategory;
use App\Mummy\Api\V1\Entities\Vendors\VendorPortfolio;
use App\Mummy\Api\V1\Entities\Vendors\VendorPortfolioMedia;
use App\Mummy\Api\V1\Entities\Vendors\VendorProfile;
use App\Mummy\Api\V1\Entities\Home\Category;
use App\Mummy\Api\V1\Entities\CustomerActivitive;
use App\Mummy\Api\V1\Entities\UserReview;
use App\Mummy\Api\V1\Entities\Vendor;
use App\Mummy\Api\V1\Requests\Favourite\FavouriteRequest;
use App\Mummy\Api\V1\Requests\Portfolio\PortfolioRequest;
use App\Mummy\Api\V1\Requests\Vendor\VendorByCategoryRequest;
use App\Mummy\Api\V1\Requests\Vendor\AllReviewByVendorRequest;
use App\Mummy\Api\V1\Requests\Vendor\GetInstagramFeedRequest;
use App\Mummy\Api\V1\Requests\Vendor\GetInstagramFeedDetailRequest;
use Helper;
use DB;


class VendorsController extends ApiController
{
    /**
     * @SWG\Get(
     *   path="/v1/vendors/getListVendor",
     *   description="",
     *   summary="",
     *   operationId="api.v1.vendors.getListVendor",
     *   produces={"application/json"},
     *   tags={"VENDORS"},
     *   @SWG\Response(response=401, description="unauthorized"),
     *   @SWG\Response(response=200, description="Success"),
     * )
     */
    public function getListVendor(Request $request)
    {
        $vendors = [] ;
        $vendorsID = DB::table('users')->Join('role_users', 'users.id', '=', 'role_users.user_id')->where('role_id', 3)->where('status', 1)->get();
        if($vendorsID)
        {
            foreach ($vendorsID as $key => $value) {
            $vendors += [$key => Customer::find($value->user_id)];
            
            }
        }
        return response([
                    'data' => [
                        'vendors'   =>  Helper::formatListVendors($vendors)
                        ],
                ],Response::HTTP_OK);
        
    }

    /**
     * @SWG\Post(
     *   path="/v1/vendors/submitComment",
     *   description="<ul>
     *     <li>currentPassword : string (required)</li>
     *     <li>newPassword : string (required)</li></ul>",
     *   summary="View",
     *   operationId="api.v1.vendors.submitComment",
     *   produces={"application/json"},
     *   tags={"VENDORS"},
     *   @SWG\Parameter(
     *     name="body",
     *     in="body",
     *     description="Target customer.",
     *     required=true,
     *    @SWG\Schema(ref="#/definitions/SubmitComment")
     *   ),
     *   @SWG\Response(response=500, description="internal server error"),
     *   security={
     *       {"api_key": {}}
     *   }
     * )
     *
     */
    public function submitComment(Request $request)
    {
        $portfolios_id             = !empty($request->portfolios_id) ? $request->portfolios_id : false;
        $comment             = !empty($request->comment) ? $request->comment : false;
        if(!$portfolios_id || !$comment ){
            return response([
                    'error' => [
                    'message'   =>  'Portfolios_id, Comment is required'],
                ],Response::HTTP_OK);
        }
        $customer = $request->user();
        $vendorsComment = new VendorComment();
        $vendorsComment->user_id = $customer->id;
        $vendorsComment->portfolios_id = $portfolios_id;
        $vendorsComment->comment = $comment;
        $vendorsComment->save();
        return response([
                    'data' => [
                        'message'   =>  'Submit Comment Success ',
                        'comment'   =>  Helper::formatGetComment($vendorsComment,$customer)
                        ],
                ],Response::HTTP_OK);
    }

    /**
     * @SWG\Post(
     *   path="/v1/vendors/saveVendor",
     *   description="<ul>
     *     <li>currentPassword : string (required)</li>
     *     <li>newPassword : string (required)</li></ul>",
     *   summary="View",
     *   operationId="api.v1.vendors.saveVendor",
     *   produces={"application/json"},
     *   tags={"VENDORS"},
     *   @SWG\Parameter(
     *     name="body",
     *     in="body",
     *     description="Target customer.",
     *     required=true,
     *    @SWG\Schema(ref="#/definitions/SaveVendor")
     *   ),
     *   @SWG\Response(response=500, description="internal server error"),
     *   security={
     *       {"api_key": {}}
     *   }
     * )
     *
     */
    public function saveVendor(FavouriteRequest $request)
    {
        $vendor_id             = !empty($request->vendor_id) ? $request->vendor_id : false;
        if(!$vendor_id){
            return response([
                    'error' => [
                    'message'   =>  'Vendor_id is required'],
                ],Response::HTTP_OK);
        }
        $customer = $request->user();
        $checkExistFavourite = CustomerActivitive::where('vendor_id',$vendor_id)->where('user_id',$customer->id)->where('activity',1)->first();
        if($checkExistFavourite)
        {
            $checkExistFavourite->delete();
            return response([
                    'data' => [
                    'message'   =>  'Delete Vendor Success'],
                ],Response::HTTP_OK);
        }
        $isCheck = 0;
        $userRole = UserRole::where('user_id',$vendor_id)->get();
        if(count($userRole) > 0)
        {
            foreach ($userRole as $key => $value) {
                if($value->role_id == 3)
                {
                    $isCheck = 1;
                }
            }
            
        }
        if($isCheck == 1)
        {
            $vendor = Vendor::where('id',$vendor_id)->first();
            $userActivitive = new CustomerActivitive();
            $userActivitive->user_id = $customer->id;
            $userActivitive->vendor_id = $vendor_id;
            $userActivitive->activity = 1;
            $userActivitive->status = 1;
            $userActivitive->save();
            return response([
                'data' => [
                'message'   =>  'Save Vendor Success'],
            ],Response::HTTP_OK);
        }
        return response([
                    'error' => [
                    'message'   =>  'Vendor is Not Found'],
                ],Response::HTTP_OK);
    }

    /**
     * @SWG\Post(
     *   path="/v1/vendors/deleteSaveVendor",
     *   description="<ul>
     *     <li>currentPassword : string (required)</li>
     *     <li>newPassword : string (required)</li></ul>",
     *   summary="View",
     *   operationId="api.v1.vendors.deleteSaveVendor",
     *   produces={"application/json"},
     *   tags={"VENDORS"},
     *   @SWG\Parameter(
     *     name="body",
     *     in="body",
     *     description="Target customer.",
     *     required=true,
     *    @SWG\Schema(ref="#/definitions/SaveVendor")
     *   ),
     *   @SWG\Response(response=500, description="internal server error"),
     *   security={
     *       {"api_key": {}}
     *   }
     * )
     *
     */
    public function deleteSaveVendor(FavouriteRequest $request)
    {
        $vendor_id             = !empty($request->vendor_id) ? $request->vendor_id : false;
        if(!$vendor_id){
            return response([
                    'error' => [
                    'message'   =>  'Vendor_id is required'],
                ],Response::HTTP_OK);
        }

        $customer = $request->user();
        
        $customerActivitive = CustomerActivitive::where('vendor_id',$vendor_id)->where('user_id',$customer->id)->where('activity',1)->first();
        if($customerActivitive)
        {
            $customerActivitive->delete();
            return response([
                    'data' => [
                    'message' =>  'Delete Save Vendor Success'
                    ],
                ],Response::HTTP_OK);
        }
        return response([
                    'error' => [
                    'message' =>  'Can not Delete Save Vendor'
                    ],
                ],Response::HTTP_OK);
    }

    /**
     * @SWG\Post(
     *   path="/v1/vendors/addFavourite",
     *   description="<ul>
     *     <li>currentPassword : string (required)</li>
     *     <li>newPassword : string (required)</li></ul>",
     *   summary="View",
     *   operationId="api.v1.vendors.addFavourite",
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
    public function addFavourite(PortfolioRequest $request)
    {
        $portfolio_id             = !empty($request->portfolio_id) ? $request->portfolio_id : false;

        $vendorPortfolio = VendorPortfolio::find($portfolio_id);
        if(isset($vendorPortfolio) && !empty($vendorPortfolio))
        {
            $customer = $request->user();
            $checkExistFavourite = CustomerActivitive::where('portfolio_id',$portfolio_id)->where('user_id',$customer->id)->where('activity',2)->first();
            if($checkExistFavourite)
            {
                $checkExistFavourite->delete();
                return response([
                        'data' => [
                        'message'   =>  'Delete Favourite Success'],
                    ],Response::HTTP_OK);
            }
            $userActivitive = new CustomerActivitive();
            $userActivitive->user_id = $customer->id;
            $userActivitive->portfolio_id = $portfolio_id;
            $userActivitive->activity = 2;
            $userActivitive->save();
            return response([
                'data' => [
                'message'   =>  'Add Favourite Success'],
            ],Response::HTTP_OK);
           
        }
         return response([
                    'error' => [
                    'message'   =>  'Can not Add Favourite'],
                ],Response::HTTP_OK);
        
    }


    /**
     * @SWG\Post(
     *   path="/v1/vendors/likeVendor",
     *   description="<ul>
     *     <li>currentPassword : string (required)</li>
     *     <li>newPassword : string (required)</li></ul>",
     *   summary="View",
     *   operationId="api.v1.vendors.likeVendor",
     *   produces={"application/json"},
     *   tags={"VENDORS"},
     *   @SWG\Parameter(
     *     name="body",
     *     in="body",
     *     description="Target customer.",
     *     required=true,
     *    @SWG\Schema(ref="#/definitions/SaveVendor")
     *   ),
     *   @SWG\Response(response=500, description="internal server error"),
     *   security={
     *       {"api_key": {}}
     *   }
     * )
     *
     */
    public function likeVendor(FavouriteRequest $request)
    {
        $vendor_id             = !empty($request->vendor_id) ? $request->vendor_id : false;
        if(!$vendor_id){
            return response([
                    'error' => [
                    'message'   =>  'Vendor_id is required'],
                ],Response::HTTP_OK);
        }
        $customer = $request->user();
        $checkExistLikeVendor = CustomerActivitive::where('vendor_id',$vendor_id)->where('user_id',$customer->id)->where('activity',3)->first();
        if($checkExistLikeVendor)
        {
            $checkExistLikeVendor->delete();
            return response([
                    'data' => [
                    'message'   =>  'Delete Like Vendor Success'],
                ],Response::HTTP_OK);
        }
        $userRole = UserRole::where('user_id',$vendor_id)->first();
        if($userRole)
        {
            if($userRole->role_id == 3)
            {
                $userActivitive = new CustomerActivitive();
                $userActivitive->user_id = $customer->id;
                $userActivitive->vendor_id = $vendor_id;
                $userActivitive->activity = 3;
                $userActivitive->save();
                return response([
                    'data' => [
                    'message'   =>  'Save Like Vendor Success'],
                ],Response::HTTP_OK);
            }
        }
        return response([
                    'error' => [
                    'message'   =>  'Vendor is Not Found'],
                ],Response::HTTP_OK);
    }

    /**
     * @SWG\Get(
     *   path="/v1/vendors/getVendorScreen",
     *   description="",
     *   summary="",
     *   operationId="api.v1.vendors.getVendorScreen",
     *   produces={"application/json"},
     *   tags={"VENDORS"},
     *   @SWG\Response(response=401, description="unauthorized"),
     *   @SWG\Response(response=200, description="Success"),
     *   security={
     *       {"api_key": {}}
     *   }
     * )
     */
    public function getVendorScreen(Request $request)
    {
        $categories = [];
        $listCategory = DB::select('SELECT p.category_id FROM mm__vendors_category AS p Join mm__categories as p1 ON p.category_id = p1.id Where p1.status = 1 AND p1.is_deleted IS NULL GROUP BY p.category_id');
        if($listCategory)
        {
            foreach ($listCategory as $key => $value) {
                 $categories += [$key => Category::find($value->category_id)];
            }
        }
        return response([
                    'data' => [
                    'categories'   =>  Helper::formatListCategories($categories)],
                ],Response::HTTP_OK);
    }

    /**
     * @SWG\Post(
     *   path="/v1/vendors/postListVendorByCategory",
     *   description="",
     *   summary="",
     *   operationId="api.v1.vendors.postListVendorByCategory",
     *   produces={"application/json"},
     *   tags={"VENDORS"},
     *   @SWG\Parameter(
     *     name="body",
     *     in="body",
     *     description="Target customer.",
     *     required=true,
     *    @SWG\Schema(ref="#/definitions/ListVendorByCategory")
     *   ),
     *   @SWG\Response(response=401, description="unauthorized"),
     *   @SWG\Response(response=200, description="Success"),
     *   security={
     *       {"api_key": {}}
     *   }
     * )
     */
    public function postListVendorByCategory(VendorByCategoryRequest $request)
    {
         $category_id             = !empty($request->category_id) ? $request->category_id : false;
          $country_id             = $request->country_id ? $request->country_id : false;
           $page             = is_numeric($request->page) ? $request->page : 1;
            $take             = is_numeric($request->take) ? $request->take : config('constant.default.take');
            $sort_by             = is_numeric($request->sort_by) ? $request->sort_by : false;
            if(!$page)
                $page =1;
            if(!$take)
                $take = config('constant.default.take');
          // $query = "select tb5.views as view,tb.user_id, COALESCE(sum(tb3.activity = 5), 0) as favourite from mm__vendors_category AS tb Left JOIN mm__vendors_location AS tb1 ON tb.user_id = tb1.user_id JOIN users AS tb2 ON tb2.id = tb.user_id AND tb2.status = 1 and tb2.is_deleted IS NULL Left JOIN mm__user_activities AS tb3 On tb3.vendor_id = tb.user_id JOIN mm__vendors_portfolios AS tb4 On tb4.vendor_id = tb.user_id AND tb4.status = 1 AND tb4.is_deleted IS NULL JOIN mm__vendors_profile AS tb5 On tb5.user_id = tb.user_id WHERE tb.category_id = $category_id";
 
          $query = "select tb5.views as view,tb.user_id, (select count(*) FROM mm__user_activities ua JOIN mm__vendors_portfolios vp ON ua.portfolio_id = vp.id AND vp.status = 1 ANd vp.is_deleted IS NULL WHERE ua.portfolio_id IS NOT NULL AND ua.portfolio_id = tb4.id AND ua.activity = 5 AND ua.vendor_id = tb.user_id) as favourite from mm__vendors_category AS tb Left JOIN mm__vendors_location AS tb1 ON tb.user_id = tb1.user_id JOIN users AS tb2 ON tb2.id = tb.user_id AND tb2.status = 1 and tb2.is_deleted IS NULL Left JOIN mm__vendors_portfolios AS tb4 On tb4.vendor_id = tb.user_id AND tb4.status = 1 AND tb4.is_deleted IS NULL JOIN mm__vendors_profile AS tb5 On tb5.user_id = tb.user_id WHERE tb.category_id = $category_id AND tb.status = 1 and tb.is_deleted IS NULL";
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
             $query .= " AND tb1.country_id IN $countries";
          }
          $query .= " GROUP BY tb.user_id";

          switch ($sort_by) {
                case config('constant.default.all'):
                    $query .= " ORDER BY user_id DESC";
                    break;
                case config('constant.default.view'):
                    $query .= " ORDER BY tb5.views DESC";
                    break;
                default:
                    $query .= " ORDER BY favourite DESC";
                    break;
          }
          // if($sort_by)
          // {
          //   if($sort_by == config('constant.default.all'))
          //   {
          //       $query .= " ORDER BY user_id DESC";
          //   }
          //   if($sort_by == config('constant.default.favourite'))
          //   {
          //       $query .= " ORDER BY favourite DESC";
          //   }
          //    if($sort_by == config('constant.default.view'))
          //   {
          //       $query .= " ORDER BY tb5.views DESC";
          //   }
          // }
          // 

          $result = DB::select($query);
          $vendors = [] ;
          $vendorCollection =[] ;
          $maxPage = 1;
          if($result)
          {

            $maxVendor = count($result);
            $maxPage = CEIL($maxVendor/$take);
            $offset = ($page - 1) * $take;
            $query .=  " LIMIT $take OFFSET $offset";
            
            $resultVendors = DB::select($query);

            foreach ($resultVendors as $key => $value) {
                $vendors += [$key => Customer::find($value->user_id)];
            }
            
            // $vendorCollection = $this->paginateFilter($vendors,$take);
          }
          return response([
                    'data' => [
                        'vendors'   =>  Helper::formatListVendorsByCategory($vendors),
                        'maxPage'   =>  $maxPage
                        ],
                ],Response::HTTP_OK);
    }
    /**
     * @SWG\Get(
     *   path="/v1/vendors/getVendorProfile?vendor_id={vendor_id}",
     *   description="",
     *   summary="",
     *   operationId="api.v1.vendors.getVendorProfile",
     *   produces={"application/json"},
     *   tags={"VENDORS"},
     *   @SWG\Parameter(
     *     description="",
     *     in="path",
     *     name="vendor_id",
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
    public function getVendorProfile(Request $request)
    {
        $vendor_id             = !empty($request->vendor_id) ? $request->vendor_id : false;
        $customer = $request->user();
        $arrImage = [];
        $vendors = [];
        $arr = [];
        $vendorlimit = [];
        $questions = [];

        $checkVendorReduce = Vendor::where('id',$vendor_id)->first();
        $primaryCategory =  $checkVendorReduce->categories()->where('mm__vendors_category.is_primary',1)->where('mm__vendors_category.status',1)->first();
        if(isset($primaryCategory))
        {
            $questions = \App\Mummy\Api\V1\Entities\Question::where('category_id',$primaryCategory->id)->where('status',1)->get();
        }
        $vendor = DB::table('users')->Join('role_users', 'users.id', '=', 'role_users.user_id')->where('id', $vendor_id)->where('role_id', 3)->where('status', 1)->get();
        $vendorActiveView = CustomerActivitive::where('vendor_id',$vendor_id)->where('user_id',$customer->id)->where('activity',4)->first();
        if(!$vendorActiveView)
        {
            $newCustomerActiveView = new CustomerActivitive();
            $newCustomerActiveView->vendor_id = $vendor_id;
            $newCustomerActiveView->user_id = $customer->id;
            $newCustomerActiveView->activity = 4;
            $newCustomerActiveView->save();
            $viewVendorProfile = VendorProfile::where('user_id',$vendor_id)->first();
            $viewVendorProfile->views = $viewVendorProfile->views + 1;
            $viewVendorProfile->save();
        }
        if(isset($vendor) && !empty($vendor) && count($vendor) > 0)
        {
            $firstVendorCategory = VendorCategory::where('user_id',$vendor_id)->first();
            // $vendorCategory = VendorCategory::where('user_id','!=',$vendor_id)->where('category_id',$firstVendorCategory->category_id)->with('vendor')->get();
            $vendorCategory = VendorCategory::where('category_id',$firstVendorCategory->category_id)
                                            ->whereHas('vendors', function($query) use ($vendor_id){
                                                $query->where('id','!=',$vendor_id);
                                                $query->whereNull('is_deleted');
                                                $query->where('status', 1);
                                                $query->whereHas('portfolios', function($query){
                                                    $query->whereNull('is_deleted');
                                                    $query->where('status', 1);
                                                });
                                            })->get();
            $random = $vendorCategory->shuffle();
            if($random)
            {
                foreach ($random as $key1 => $item) {
                    if($key1 < 2)
                    {
                        $arr += [$key1 => Customer::find($item->user_id)];
                    }
                
                }
                shuffle($arr);
            }
            $vendorPortfolio = VendorPortfolio::where('vendor_id',$vendor_id)
                                                ->where('status', 1)
                                                ->whereHas('category', function($query){
                                                    $query->whereNull('is_deleted');
                                                    $query->where('status', 1);
                                                })
                                                ->get();
            $userReview = UserReview::where('vendor_id',$vendor_id)->where('status',1)->get();;
            $userReviewLimit = UserReview::where('vendor_id',$vendor_id)->where('status',1)->orderBy('created_at', 'desc')->limit(3)->get();

            // show instagam feed
            $vendorProfile = VendorProfile::where('user_id',$vendor_id)->first();
            $instagramFeed = $items = [];
            $isShowInstagramFeed = false;
            $temp = 0;
            if($vendorProfile->instagram_showfeed && !empty($vendorProfile->instagram_token)){
                $isShowInstagramFeed = true;
                try{
                    $client = new \GuzzleHttp\Client();
                    $res = $client->request('GET', 'https://api.instagram.com/v1/users/self/media/recent/?access_token=' . $vendorProfile->instagram_token . "&count=5");

                    $items = json_decode($res->getBody(),true);
                    
                    $instagramFeed = Helper::formatInstagramFeed($items);
                    
                }catch(\Exception $e) {
                    \Log::debug("==============================DEBUG===========================");
                    \Log::debug("Error: " . $e->getMessage());
                    \Log::debug("==============================END DEBUG===========================");
                }
            }

            // dd($items['data']);

            return response([
                        'data' => [
                        'vendor'   => Helper::formatGetVendors($vendor,$customer),
                        'portfolios'   => Helper::formatListPortfolio($vendorPortfolio,$customer),
                        'countPortfolios'   => count($vendorPortfolio),
                        'info'   => Helper::getInfoVendor($questions, $vendor_id),
                        'reviews'   =>  Helper::formatListReviews($userReviewLimit,$customer),
                        'countReviews'   => count($userReview),
                        'vendorYouMayLike'   => Helper::formatListVendors($arr),
                        'isShowInstagramFeed' => $isShowInstagramFeed,
                        'instagramFeed' => $instagramFeed,
                        ],
                    ],Response::HTTP_OK);
        }
         return response([
                    'error' => [
                    'message'   =>  'Results not found'],
                ],Response::HTTP_OK);
        
        
    }

    /**
     * @SWG\Get(
     *   path="/v1/vendors/getInstagramFeed?vendor_id={vendor_id}&page={page}&take={take}",
     *   description="",
     *   summary="",
     *   operationId="api.v1.vendors.getInstagramFeed",
     *   produces={"application/json"},
     *   tags={"VENDORS"},
     *   @SWG\Parameter(
     *     description="",
     *     in="path",
     *     name="vendor_id",
     *     required=true,
     *     type="integer",
     *     default="" 
     *   ),
     *   @SWG\Parameter(
     *     description="",
     *     in="path",
     *     name="page",
     *     required=false,
     *     type="integer",
     *     default="1" 
     *   ),
     *   @SWG\Parameter(
     *     description="",
     *     in="path",
     *     name="take",
     *     required=false,
     *     type="integer",
     *     default="15" 
     *   ),
     *   @SWG\Parameter(
     *     description="",
     *     in="path",
     *     name="next_page",
     *     required=false,
     *     type="string",
     *     default="15" 
     *   ),
     *   @SWG\Response(response=401, description="unauthorized"),
     *   @SWG\Response(response=200, description="Success"),
     *   security={
     *       {"api_key": {}}
     *   }
     * )
     */
    public function getInstagramFeed(GetInstagramFeedRequest $request){
        $vendor_id = isset($request->vendor_id) && !empty($request->vendor_id) ? $request->vendor_id : false;
        $page = isset($request->page) && !empty($request->page) ? $request->page : 1;
        $take = isset($request->take) && !empty($request->take) ? $request->take : 15;
        $max_id = isset($request->next_page) && !empty($request->next_page) ? $request->next_page : "";

        $vendorProfile = VendorProfile::where('user_id',$vendor_id)->first();
        if(count($vendorProfile)){
            $instagramFeed = [];
            $temp = 0;
            $next_url = "";
            $maxPage = 1;

            if($vendorProfile->instagram_showfeed && !empty($vendorProfile->instagram_token)){
                try{
                    $client = new \GuzzleHttp\Client();

                    // get max page
                    $res = $client->request('GET', 'https://api.instagram.com/v1/users/self/media/recent/?access_token=' . $vendorProfile->instagram_token);
                    $items = json_decode($res->getBody(),true);
                    $maxPage = ceil(count($items['data']) / $take);
                    if($page > 1){
                        if(!$max_id){
                            // get max id
                            $n = $take * ( $page - 1 );
                            $maxNumber = 30;
                            $temp = 0;
                            while($n > $temp){
                                  
                                $takeNumber = $n - $temp > $maxNumber ? $maxNumber :  $n - $temp;
                                if(!$max_id){
                                    $res = $client->request('GET', 'https://api.instagram.com/v1/users/self/media/recent/?access_token=' . $vendorProfile->instagram_token .  "&count=" . $takeNumber);
                                }else{
                                    $res = $client->request('GET', 'https://api.instagram.com/v1/users/self/media/recent/?access_token=' . $vendorProfile->instagram_token . "&max_id=" . $max_id .  "&count=" . $takeNumber);
                                }
                                
                                $items = json_decode($res->getBody(),true);
                                $max_id = $items['pagination']['next_max_id'];

                                $temp = $temp + $maxNumber;
                            }
                        }else{
                            $res = $client->request('GET', 'https://api.instagram.com/v1/users/self/media/recent/?access_token=' . $vendorProfile->instagram_token . "&max_id=" . $max_id . "&count=" . $take);
                            $items = json_decode($res->getBody(),true);
                            $max_id = $items['pagination']['next_max_id'];
                        }
                        
                        $res = $client->request('GET', 'https://api.instagram.com/v1/users/self/media/recent/?access_token=' . $vendorProfile->instagram_token . "&max_id=" . $max_id . "&count=" . $take);
                    }else{
                        $res = $client->request('GET', 'https://api.instagram.com/v1/users/self/media/recent/?access_token=' . $vendorProfile->instagram_token . "&count=" . $take);
                    }
                    
                    $items = json_decode($res->getBody(),true);

                    // get pagination next page
                    if(!($items['pagination'] && !empty($items['pagination']))){
                        $maxPage = $page;
                        $max_id = "";
                    }else{
                        $maxPage = $maxPage > $page || $maxPage == 1 ? $maxPage : ++$page; 
                        $max_id = $items['pagination']['next_max_id'];
                    }

                    // get next url
                    // $next_url = $items['pagination']['next_url'];
                    $instagramFeed = Helper::formatInstagramFeed($items);
                    
                }catch(\Exception $e) {
                	\Log::debug("==============================DEBUG===========================");
                    \Log::debug("Error: " . $e->getMessage());
                    \Log::debug("==============================END DEBUG===========================");
                }

                return response([
                            'data' => [
                                'instagramFeed' => $instagramFeed,
                                "maxPage" => $maxPage,
                                "nextPage" => $max_id,
                            ],
                        ],Response::HTTP_OK);

            }
        }

        return response([
                'error' => [
                'message'   =>  'Results not found'],
            ],Response::HTTP_OK);
    }

    /**
     * @SWG\Get(
     *   path="/v1/vendors/getInstagramFeedDetail?vendor_id={vendor_id}&media_id={media_id}",
     *   description="",
     *   summary="",
     *   operationId="api.v1.vendors.getInstagramFeedDetail",
     *   produces={"application/json"},
     *   tags={"VENDORS"},
     *   @SWG\Parameter(
     *     description="",
     *     in="path",
     *     name="vendor_id",
     *     required=true,
     *     type="integer",
     *     default="" 
     *   ),
     *   @SWG\Parameter(
     *     description="",
     *     in="path",
     *     name="media_id",
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
    public function getInstagramFeedDetail(GetInstagramFeedDetailRequest $request){
        $vendor_id = isset($request->vendor_id) && !empty($request->vendor_id) ? $request->vendor_id : false;
        $media_id = isset($request->media_id) && !empty($request->media_id) ? $request->media_id : false;
        $page = isset($request->page) && !empty($request->page) ? $request->page : 1;
        $take = isset($request->take) && !empty($request->take) ? $request->take : 15;

        $vendorProfile = VendorProfile::where('user_id',$vendor_id)->first();
        if(count($vendorProfile)){
            $instagramFeed = $info = [];
            $temp = 0;
            $next_url = "";
            $maxPage = 1;

            if($vendorProfile->instagram_showfeed && !empty($vendorProfile->instagram_token)){
                try{
                    $client = new \GuzzleHttp\Client();

                    // get max page
                    $res = $client->request('GET', "https://api.instagram.com/v1/media/$media_id?access_token=$vendorProfile->instagram_token");
                    $items = json_decode($res->getBody(),true);

                    // get next url
                    // $next_url = $items['pagination']['next_url'];
                    $info = [
                        'username' => $items['data']['user']['username'],
                        'full_name' => $items['data']['user']['full_name'],
                        'caption' => $items['data']['caption']['text'] ? $items['data']['caption']['text'] : "",
                    ];

                    $instagramFeed = Helper::formatInstagramFeed($items, "detail");
                    
                }catch(\Exception $e) {
                
                }

                return response([
                            'data' => [
                                'info' => $info,
                                'instagramFeed' => $instagramFeed,
                                // "maxPage" => $maxPage,
                                // "next_url" => $next_url
                            ],
                        ],Response::HTTP_OK);

            }
        }

        return response([
                'error' => [
                'message'   =>  'Results not found'],
            ],Response::HTTP_OK);
    }

    /**
     * @SWG\Post(
     *   path="/v1/vendors/getAllReviewByVendor",
     *   description="",
     *   summary="",
     *   operationId="api.v1.vendors.getAllReviewByVendor",
     *   produces={"application/json"},
     *   tags={"VENDORS"},
     *   @SWG\Parameter(
     *     name="body",
     *     in="body",
     *     description="Target customer.",
     *     required=true,
     *    @SWG\Schema(ref="#/definitions/AllReviewByVendor")
     *   ),
     *   @SWG\Response(response=401, description="unauthorized"),
     *   @SWG\Response(response=200, description="Success"),
     *   security={
     *       {"api_key": {}}
     *   }
     * )
     */
    public function getAllReviewByVendor(AllReviewByVendorRequest $request)
    {
        $vendor_id             = !empty($request->vendor_id) ? $request->vendor_id : false;
        $sort_by             = is_numeric($request->sort_by) ? $request->sort_by : false;
        $page             = is_numeric($request->page) ? $request->page : false;
        $take             = is_numeric($request->take) ? $request->take : false;
        if(!$page)
            $page =1;
        if(!$take)
            $take = config('constant.default.take');

        $customer = $request->user();
         $userReviewCollection = [];
        $maxPage = 1;
        $query = "select tb.* from mm__user_reviews AS tb Join users AS tb2 ON tb2.id = tb.vendor_id WHERE tb.vendor_id = $vendor_id AND tb2.status = 1 AND tb.status = 1 AND tb.is_deleted IS NULL ";
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
                    'reviews'   =>  Helper::formatListReviews($userReviewCollection,$customer),
                    'maxPage' =>  $maxPage
                    
                    ],
                ],Response::HTTP_OK);
    }
    /**
     * @SWG\Post(
     *   path="/v1/vendors/viewAction",
     *   description="",
     *   summary="",
     *   operationId="api.v1.vendors.viewAction",
     *   produces={"application/json"},
     *   tags={"VENDORS"},
     *   @SWG\Parameter(
     *     name="body",
     *     in="body",
     *     description="Target customer.",
     *     required=true,
     *    @SWG\Schema(ref="#/definitions/ViewAction")
     *   ),
     *   @SWG\Response(response=401, description="unauthorized"),
     *   @SWG\Response(response=200, description="Success"),
     *   security={
     *       {"api_key": {}}
     *   }
     * )
     */
    public function viewAction(Request $request)
    {
        $vendor_id             = !empty($request->vendor_id) ? $request->vendor_id : false;
        $type             = is_numeric($request->type) ? $request->type : false;

        $customer = $request->user();
        if($vendor_id && $type)
        {
            $check = Helper::reducePoint($vendor_id,$customer->id,$type);
            Helper::checkActivity($vendor_id,$customer->id,$type,$check);
            return response([
                    'data' => [
                        'message' =>  'View Action Success'
                    
                    ],
                ],Response::HTTP_OK);
        }
        else
        {
            return response([
                    'error' => [
                        'message' =>  'View Action Failed'
                    
                    ],
                ],Response::HTTP_OK);
        }
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
    
    public function getUpdateLikeActivity(){
        $activities = CustomerActivitive::where('activity' , 5)->whereNull('vendor_id')->get();
        if(count($activities)){
            foreach ($activities as $key => $item) {
                $portfolio = VendorPortfolio::where('id', $item->portfolio_id)->first();
                if(count($portfolio)){
                    $item->vendor_id = $portfolio->vendor_id;
                    $item->save();
                }else{
                    $item->delete();
                }
            }
        }
    }
}
