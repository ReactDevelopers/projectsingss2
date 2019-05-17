<?php namespace Modules\Report\Services;

use DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Pagination\Paginator;
use Illuminate\Http\Request;
use Modules\Report\Entities\Comment;
use Modules\Report\Repositories\CommentRepository;

class CommentService {

    /**
     *
     * @var PortfolioRepository
     */
    private $repository;  

    public function __construct( CommentRepository $repository) {
        $this->repository   = $repository;
    }

    public function all(){
        return Comment::select('mm__vendors_comment_report.*', 'reporter.first_name as user_reported', 'reporter.email as email_reported')
                         ->join('mm__vendors_comment', 'mm__vendors_comment.id', '=', 'mm__vendors_comment_report.comment_id')
                         ->join('users as reporter', 'reporter.id', '=', 'mm__vendors_comment_report.user_id')
                         ->join('users as user_reported', 'user_reported.id', '=', 'mm__vendors_comment.user_id')
                         ->join('mm__vendors_portfolios', 'mm__vendors_portfolios.id', '=', 'mm__vendors_comment.portfolios_id')
                         ->join('users as vendor', 'vendor.id', '=', 'mm__vendors_portfolios.vendor_id')
                         ->whereNull('reporter.is_deleted')
                         ->whereNull('mm__vendors_portfolios.is_deleted')
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
            $items = Comment::select('mm__vendors_comment_report.*', 'reporter.first_name as user_reported', 'reporter.email as email_reported')
                         ->join('mm__vendors_comment', 'mm__vendors_comment.id', '=', 'mm__vendors_comment_report.comment_id')
                         ->join('users as reporter', 'reporter.id', '=', 'mm__vendors_comment_report.user_id')
                         ->join('users as user_reported', 'user_reported.id', '=', 'mm__vendors_comment.user_id')
                         ->join('mm__vendors_portfolios', 'mm__vendors_portfolios.id', '=', 'mm__vendors_comment.portfolios_id')
                         ->join('users as vendor', 'vendor.id', '=', 'mm__vendors_portfolios.vendor_id')
                         ->whereNull('reporter.is_deleted')
                         ->whereNull('mm__vendors_portfolios.is_deleted')
                         ->whereNull('user_reported.is_deleted')
                         ->whereNull('vendor.is_deleted')
                         ->where(function($query) use ($keyword) {
                            $query->where('mm__vendors_comment_report.id', '=', $keyword);
                            $query->orWhere('mm__vendors_comment_report.content', 'like', '%' . $keyword . '%');
                            $query->orWhere('user_reported.email', 'like', '%' . $keyword . '%');
                            $query->orWhere('reporter.email', 'like', '%' . $keyword . '%');
                            $query->orWhere('vendor.email', 'like', '%' . $keyword . '%');
                        })
                        ->orderBy($order_field, $sort)
                        ->paginate($limit);

            return $items;
        }else{
            return Comment::select('mm__vendors_comment_report.*', 'reporter.first_name as user_reported', 'reporter.email as email_reported')
                         ->join('mm__vendors_comment', 'mm__vendors_comment.id', '=', 'mm__vendors_comment_report.comment_id')
                         ->join('users as reporter', 'reporter.id', '=', 'mm__vendors_comment_report.user_id')
                         ->join('users as user_reported', 'user_reported.id', '=', 'mm__vendors_comment.user_id')
                         ->join('mm__vendors_portfolios', 'mm__vendors_portfolios.id', '=', 'mm__vendors_comment.portfolios_id')
                         ->join('users as vendor', 'vendor.id', '=', 'mm__vendors_portfolios.vendor_id')
                         ->whereNull('reporter.is_deleted')
                         ->whereNull('mm__vendors_portfolios.is_deleted')
                         ->whereNull('user_reported.is_deleted')
                         ->whereNull('vendor.is_deleted')
                         ->where(function($query) use ($keyword) {
                            $query->where('mm__vendors_comment_report.id', '=', $keyword);
                            $query->orWhere('mm__vendors_comment_report.content', 'like', '%' . $keyword . '%');
                            $query->orWhere('user_reported.email', 'like', '%' . $keyword . '%');
                            $query->orWhere('reporter.email', 'like', '%' . $keyword . '%');
                            $query->orWhere('vendor.email', 'like', '%' . $keyword . '%');
                        })
                         ->count();
        }
        
    }
}