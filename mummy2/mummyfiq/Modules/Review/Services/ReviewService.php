<?php namespace Modules\Review\Services;

use DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Pagination\Paginator;
use Illuminate\Http\Request;
use Modules\Review\Entities\Review;

class ReviewService {

    public function __construct() {

    }

    public function getItems($option = 'list', $request){
        $limit = $request->get('limit') ? $request->get('limit') : 10;
        $keyword = $request->get('keyword') ? $request->get('keyword') : "";

        $array_field = array('id', 'business_name', 'customer_name', 'message', 'email_content');
        $order_field = $request->get('order_field') && in_array($request->get('order_field'), $array_field) ? $request->get('order_field') : 'id';
        $sort = $request->get('sort') ? $request->get('sort') : 'DESC';

        if($option == 'list'){
            $items = Review::select('mm__vendors_requests_reviews.*', 'uvp.business_name', 'uc.first_name as customer_name')
                         ->join('users as uv', 'uv.id', '=', 'mm__vendors_requests_reviews.vendor_id')
                         ->join('users as uc', 'uc.email', '=', 'mm__vendors_requests_reviews.sent_to_customers')
                         ->join('mm__vendors_profile as uvp', 'uvp.user_id', '=', 'uv.id')
                         ->whereNull('uv.is_deleted')
                         ->whereNull('uc.is_deleted')
                         ->where(function($query) use ($keyword) {
                            $query->where('mm__vendors_requests_reviews.id', '=', $keyword);
                            $query->orWhere('mm__vendors_requests_reviews.message', 'like', '%'.$keyword.'%');
                            $query->orWhere('mm__vendors_requests_reviews.email_content', 'like', '%'.$keyword.'%');
                            $query->orWhere('uvp.business_name', 'like', '%' . $keyword . '%');
                            $query->orWhere('uc.email', 'like', '%' . $keyword . '%');
                        })
                        ->orderBy($order_field, $sort)
                        ->paginate($limit);

            return $items;
        }else{
            return Review::select('mm__vendors_requests_reviews.*', 'uvp.business_name', 'uc.first_name as customer_name')
                         ->join('users as uv', 'uv.id', '=', 'mm__vendors_requests_reviews.vendor_id')
                         ->join('users as uc', 'uc.email', '=', 'mm__vendors_requests_reviews.sent_to_customers')
                         ->join('mm__vendors_profile as uvp', 'uvp.user_id', '=', 'uv.id')
                         ->whereNull('uv.is_deleted')
                         ->whereNull('uc.is_deleted')
                         ->where(function($query) use ($keyword) {
                            $query->where('mm__vendors_requests_reviews.id', '=', $keyword);
                            $query->orWhere('mm__vendors_requests_reviews.message', 'like', '%'.$keyword.'%');
                            $query->orWhere('mm__vendors_requests_reviews.email_content', 'like', '%'.$keyword.'%');
                            $query->orWhere('uvp.business_name', 'like', '%' . $keyword . '%');
                            $query->orWhere('uc.email', 'like', '%' . $keyword . '%');
                        })
                         ->count();
        }
        
    }

}