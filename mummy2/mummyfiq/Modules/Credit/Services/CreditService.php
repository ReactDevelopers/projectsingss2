<?php namespace Modules\Credit\Services;

use DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Pagination\Paginator;
use Illuminate\Http\Request;
use Modules\Credit\Entities\Credit;

class CreditService {


    public function __construct() {

    }

    public function getItems($option = 'list', $request){
        $limit = $request->get('limit') ? $request->get('limit') : 10;
        $keyword = $request->get('keyword') ? $request->get('keyword') : "";

        $array_field = array('created_at', 'business_name', 'email');
        $order_field = $request->get('order_field') && in_array($request->get('order_field'), $array_field) ? $request->get('order_field') : 'created_at';
        $sort = $request->get('sort') ? $request->get('sort') : 'DESC';

        if($option == 'list'){
            $items = Credit::select('mm__vendors_credit.vendor_id', 'mm__vendors_credit.created_at', 'mm__vendors_profile.business_name')
                         ->join('users', 'users.id', '=', 'mm__vendors_credit.vendor_id')
                         ->join('mm__vendors_profile', 'mm__vendors_profile.user_id', '=', 'mm__vendors_credit.vendor_id')
                         ->whereNull('users.is_deleted')
                         ->where(function($query) use ($keyword) {
                            $query->where('mm__vendors_profile.business_name', 'like', '%'.$keyword.'%');
                        })
                        ->groupBy('mm__vendors_credit.vendor_id')
                        ->orderBy($order_field, $sort)
                        ->paginate($limit);
            return $items;
        }else{
            return Credit::select('mm__vendors_credit.vendor_id', 'mm__vendors_credit.created_at', 'mm__vendors_profile.business_name')
                         ->join('users', 'users.id', '=', 'mm__vendors_credit.vendor_id')
                         ->join('mm__vendors_profile', 'mm__vendors_profile.user_id', '=', 'mm__vendors_credit.vendor_id')
                         ->whereNull('users.is_deleted')
                         ->where(function($query) use ($keyword) {
                            $query->where('mm__vendors_profile.business_name', 'like', '%'.$keyword.'%');
                         })
                         ->groupBy('mm__vendors_credit.vendor_id')
                         ->count();
        }
        
    }

}