<?php namespace Modules\Comment\Services;

use DB;
use Illuminate\Pagination\Paginator;
use Illuminate\Http\Request;
use Modules\Comment\Entities\Vendorcomment;
use Modules\Comment\Repositories\VendorcommentRepository;
use Modules\Vendor\Repositories\VendorRepository;

class VendorcommentService {

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
            $items = Vendorcomment::select('mm__vendors_comment.*', 'users.first_name as customer_name', 'mm__vendors_portfolios.title as portfolio_title')
                         ->join('users', 'users.id', '=', 'mm__vendors_comment.user_id', 'left')
                         ->join('mm__vendors_portfolios', 'mm__vendors_portfolios.id', '=', 'mm__vendors_comment.portfolios_id')
                         ->whereNull('users.is_deleted')
                         ->whereNull('mm__vendors_portfolios.is_deleted')
                         ->where(function($query) use ($keyword) {
                            $query->where('mm__vendors_comment.id', '=', $keyword);
                            $query->orWhere('mm__vendors_comment.comment', 'like', '%' . $keyword . '%');
                            $query->orWhere('users.first_name', 'like', '%' . $keyword . '%');
                            $query->orWhere('mm__vendors_portfolios.title', 'like', '%' . $keyword . '%');
                        })
                        ->orderBy($order_field, $sort)
                        ->paginate($limit);

            return $items;
        }else{
            return Vendorcomment::select('mm__vendors_comment.*', 'users.first_name as customer_name', 'mm__vendors_portfolios.title as portfolio_title')
                         ->join('users', 'users.id', '=', 'mm__vendors_comment.user_id', 'left')
                         ->join('mm__vendors_portfolios', 'mm__vendors_portfolios.id', '=', 'mm__vendors_comment.portfolios_id')
                         ->whereNull('users.is_deleted')
                         ->whereNull('mm__vendors_portfolios.is_deleted')
                         ->where(function($query) use ($keyword) {
                            $query->where('mm__vendors_comment.id', '=', $keyword);
                            $query->orWhere('mm__vendors_comment.comment', 'like', '%' . $keyword . '%');
                            $query->orWhere('users.first_name', 'like', '%' . $keyword . '%');
                            $query->orWhere('mm__vendors_portfolios.title', 'like', '%' . $keyword . '%');
                        })
                         ->count();
        }
        
    }

}