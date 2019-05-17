<?php namespace Modules\Report\Services;

use DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Pagination\Paginator;
use Illuminate\Http\Request;
use Modules\Report\Entities\Review;
use Modules\Report\Repositories\ReviewRepository;

class ReviewService {

    /**
     *
     * @var PortfolioRepository
     */
    private $repository;  

    public function __construct( ReviewRepository $repository) {
        $this->repository   = $repository;
    }

    public function all(){
        return Review::select('mm__user_report.*', 'reporter.first_name as user_reported', 'reporter.email as email_reported')
                         ->join('mm__user_reviews', 'mm__user_reviews.id', '=', 'mm__user_report.review_id')
                         ->join('users as reporter', 'reporter.id', '=', 'mm__user_report.user_id')
                         ->join('users as user_reported', 'user_reported.id', '=', 'mm__user_reviews.user_id')
                         ->join('users as vendor', 'vendor.id', '=', 'mm__user_reviews.vendor_id')
                         ->whereNull('mm__user_reviews.is_deleted')
                         ->whereNull('reporter.is_deleted')
                         ->whereNull('user_reported.is_deleted')
                         ->whereNull('vendor.is_deleted')
                         ->orderBy('id', 'DESC')
                         ->get();
    }

    public function getItems($option = 'list', $request){
        $limit = $request->get('limit') ? $request->get('limit') : 10;
        $keyword = $request->get('keyword') ? $request->get('keyword') : "";

        $array_field = array('id');
        $order_field = $request->get('order_field') && in_array($request->get('order_field'), $array_field) ? $request->get('order_field') : 'id';
        $sort = $request->get('sort') ? $request->get('sort') : 'DESC';

        if($option == 'list'){
            $items = Review::select('mm__user_report.*', 'reporter.first_name as user_reported', 'reporter.email as email_reported')
                         ->join('mm__user_reviews', 'mm__user_reviews.id', '=', 'mm__user_report.review_id')
                         ->join('users as reporter', 'reporter.id', '=', 'mm__user_report.user_id')
                         ->join('users as user_reported', 'user_reported.id', '=', 'mm__user_reviews.user_id')
                         ->join('users as vendor', 'vendor.id', '=', 'mm__user_reviews.vendor_id')
                         ->whereNull('mm__user_reviews.is_deleted')
                         ->whereNull('reporter.is_deleted')
                         ->whereNull('user_reported.is_deleted')
                         ->whereNull('vendor.is_deleted')
                         ->where(function($query) use ($keyword) {
                            $query->where('mm__user_report.id', '=', $keyword);
                            $query->orWhere('mm__user_report.content', 'like', '%' . $keyword . '%');
                            $query->orWhere('reporter.email', 'like', '%' . $keyword . '%');
                            $query->orWhere('user_reported.email', 'like', '%' . $keyword . '%');
                            $query->orWhere('vendor.email', 'like', '%' . $keyword . '%');
                        })
                        ->orderBy($order_field, $sort)
                        ->paginate($limit);

            return $items;
        }else{
            return Review::select('mm__user_report.*', 'reporter.first_name as user_reported', 'reporter.email as email_reported')
                         ->join('mm__user_reviews', 'mm__user_reviews.id', '=', 'mm__user_report.review_id')
                         ->join('users as reporter', 'reporter.id', '=', 'mm__user_report.user_id')
                         ->join('users as user_reported', 'user_reported.id', '=', 'mm__user_reviews.user_id')
                         ->join('users as vendor', 'vendor.id', '=', 'mm__user_reviews.vendor_id')
                         ->whereNull('mm__user_reviews.is_deleted')
                         ->whereNull('reporter.is_deleted')
                         ->whereNull('user_reported.is_deleted')
                         ->whereNull('vendor.is_deleted')
                         ->where(function($query) use ($keyword) {
                            $query->where('mm__user_report.id', '=', $keyword);
                            $query->orWhere('mm__user_report.content', 'like', '%' . $keyword . '%');
                            $query->orWhere('reporter.email', 'like', '%' . $keyword . '%');
                            $query->orWhere('user_reported.email', 'like', '%' . $keyword . '%');
                            $query->orWhere('vendor.email', 'like', '%' . $keyword . '%');
                        })
                         ->count();
        }
        
    }
}