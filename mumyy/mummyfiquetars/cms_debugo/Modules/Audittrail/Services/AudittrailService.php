<?php namespace Modules\Audittrail\Services;

use DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Pagination\Paginator;
use Illuminate\Http\Request;
use Modules\Audittrail\Entities\Log;
use Modules\Audittrail\Repositories\LogRepository;

class AudittrailService {


    public function __construct() {

    }

    public function getItems($option = 'list', $request){
        $limit = $request->get('limit') ? $request->get('limit') : 10;
        $keyword = $request->get('keyword') ? $request->get('keyword') : "";

        $array_field = array('audittrail__logs.id');
        $order_field = $request->get('order_field') && in_array($request->get('order_field'), $array_field) ? $request->get('order_field') : 'audittrail__logs.id';
        $sort = $request->get('sort') ? $request->get('sort') : 'DESC';

        if($option == 'list'){
            $items = Log::select('audittrail__logs.*')
                         ->join('users', 'users.id', '=', 'audittrail__logs.performed_user_id')
                         ->whereNull('users.is_deleted')
                         ->where(function($query) use ($keyword) {
                            $query->where(DB::raw("CONCAT(`users`.`first_name`, ' ', `users`.`last_name`)"), 'like', '%'.$keyword.'%');
                            $query->orWhere('audittrail__logs.event_name', 'like', '%'.$keyword.'%');
                            $query->orWhere('audittrail__logs.title', 'like', '%'.$keyword.'%');
                        })
                         ->orderBy($order_field, $sort)
                         ->paginate($limit);

            return $items;
        }else{
            return Log::select('audittrail__logs.*')
                         ->join('users', 'users.id', '=', 'audittrail__logs.performed_user_id')
                         ->whereNull('users.is_deleted')
                         ->where(function($query) use ($keyword) {
                            $query->where(DB::raw("CONCAT(`users`.`first_name`, ' ', `users`.`last_name`)"), 'like', '%'.$keyword.'%');
                            $query->orWhere('audittrail__logs.event_name', 'like', '%'.$keyword.'%');
                            $query->orWhere('audittrail__logs.title', 'like', '%'.$keyword.'%');
                        })
                         ->orderBy($order_field, $sort)
                         ->count();
        }
        
    }

    public function all(){
        return Log::select('audittrail__logs.*')
                    ->join('users', 'users.id', '=', 'audittrail__logs.performed_user_id')
                    ->whereNull('users.is_deleted')
                    ->orderBy('audittrail__logs.id', 'desc')
                    ->get();
    }
}