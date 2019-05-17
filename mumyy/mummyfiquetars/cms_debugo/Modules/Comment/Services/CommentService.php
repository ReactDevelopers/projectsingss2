<?php namespace Modules\Comment\Services;

use DB;
use Illuminate\Pagination\Paginator;
use Illuminate\Http\Request;
use Modules\Comment\Entities\Comment;
use Modules\Comment\Repositories\CommentRepository;
use Modules\Vendor\Repositories\VendorRepository;

class CommentService {

    protected $repository;

    public function __construct(VendorRepository $repository) {
        $this->repository = $repository;
    }


    public function getItems($option = 'list', $request){
        $limit = $request->get('limit') ? $request->get('limit') : 10;
        $keyword = $request->get('keyword') ? $request->get('keyword') : "";

        $array_field = array('id', 'customer_name','business_name', 'title', 'created_at','status');
        $order_field = $request->get('order_field') && in_array($request->get('order_field'), $array_field) ? $request->get('order_field') : 'id';
        $sort = $request->get('sort') ? $request->get('sort') : 'DESC';

        if($option == 'list'){
            $items = Comment::select('mm__user_reviews.*', 'mm__vendors_profile.business_name', 'users.first_name as customer_name')
                         ->join('users', 'users.id', '=', 'mm__user_reviews.user_id', 'left')
                         ->join('mm__vendors_profile', 'mm__vendors_profile.user_id', '=', 'mm__user_reviews.vendor_id')
                         ->whereNull('mm__user_reviews.is_deleted')
                         ->whereNull('users.is_deleted')
                         ->where(function($query) use ($keyword) {
                            $query->where('mm__user_reviews.id', '=', $keyword);
                            $query->orWhere('mm__user_reviews.title', 'like', '%' . $keyword . '%');
                            $query->orWhere('mm__vendors_profile.business_name', 'like', '%'.$keyword.'%');
                            $query->orWhere('users.first_name', 'like', '%' . $keyword . '%');
                        })
                         ->where(function($query) {
                            $query->whereNull('users.id');
                            $query->orWhere('users.status', 1);
                        })
                        ->orderBy($order_field, $sort)
                        ->paginate($limit);

            return $items;
        }else{
            return Comment::select('mm__user_reviews.*', 'mm__vendors_profile.business_name', 'users.first_name as customer_name')
                         ->join('users', 'users.id', '=', 'mm__user_reviews.user_id', 'left')
                         ->join('mm__vendors_profile', 'mm__vendors_profile.user_id', '=', 'mm__user_reviews.vendor_id')
                         ->whereNull('mm__user_reviews.is_deleted')
                         ->whereNull('users.is_deleted')
                         ->where(function($query) use ($keyword) {
                            $query->where('mm__user_reviews.id', '=', $keyword);
                            $query->orWhere('mm__user_reviews.title', 'like', '%' . $keyword . '%');
                            $query->orWhere('mm__vendors_profile.business_name', 'like', '%'.$keyword.'%');
                            $query->orWhere('users.first_name', 'like', '%' . $keyword . '%');
                        })
                         ->where(function($query) {
                            $query->whereNull('users.id');
                            $query->orWhere('users.status', 1);
                        })
                         ->count();
        }
        
    }

    public function updateRatingPoint($vendor_id){
        $vendor = $this->repository->find($vendor_id);

        if(count($vendor)){
            $reviews = $vendor->vendorReview;
            $vendorProfile = $vendor->vendorProfile;

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
        }
    }

}