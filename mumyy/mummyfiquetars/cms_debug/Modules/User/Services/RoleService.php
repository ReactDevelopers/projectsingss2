<?php namespace Modules\User\Services;

use DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Pagination\Paginator;
use Illuminate\Http\Request;
use Modules\User\Entities\Role;

class RoleService {

    public function __construct() {

    }

    public function getItems($option = 'list', $request){
        $limit = $request->get('limit') ? $request->get('limit') : 10;
        $keyword = $request->get('keyword') ? $request->get('keyword') : "";

        $array_field = array('id', 'name');
        $order_field = $request->get('order_field') && in_array($request->get('order_field'), $array_field) ? $request->get('order_field') : 'id';
        $sort = $request->get('sort') ? $request->get('sort') : 'DESC';

        if($option == 'list'){
            $items = Role::where(function($query) use ($keyword) {
                            $query->where('id', '=', $keyword);
                            $query->orWhere('name', 'like', '%'.$keyword.'%');
                        })
                        ->orderBy($order_field, $sort)
                        ->paginate($limit);

            return $items;
        }else{
            return Role::where(function($query) use ($keyword) {
                            $query->where('id', '=', $keyword);
                            $query->orWhere('name', 'like', '%'.$keyword.'%');
                        })
                         ->count();
        }
        
    }
}