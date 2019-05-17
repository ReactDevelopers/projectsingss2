<?php namespace Modules\User\Services;

use DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Pagination\Paginator;
use Illuminate\Http\Request;
use Modules\User\Entities\User;

class UserService {

    public function __construct() {

    }

    public function getItems($option = 'list', $request){
        $limit = $request->get('limit') ? $request->get('limit') : 10;
        $keyword = $request->get('keyword') ? $request->get('keyword') : "";

        $array_field = array('id', 'first_name', 'last_name', 'email', 'created_at', 'last_login');
        $order_field = $request->get('order_field') && in_array($request->get('order_field'), $array_field) ? $request->get('order_field') : 'id';
        $sort = $request->get('sort') ? $request->get('sort') : 'DESC';

        if($option == 'list'){
            $items = User::select('users.*',DB::raw('roles.name as roleName'))
                         ->join('role_users', 'role_users.user_id', '=', 'users.id')
                         ->join('roles', 'role_users.role_id', '=', 'roles.id')
                         ->whereNotIn('role_users.role_id', [2, 3])
                         ->where(function($query) use ($keyword) {
                            $query->where('users.id', '=', $keyword);
                            $query->orWhere('users.first_name', 'like', '%'.$keyword.'%');
                            $query->orWhere('users.last_name', 'like', '%'.$keyword.'%');
                            $query->orWhere('users.email', 'like', '%' . $keyword . '%');
                        })
                        ->orderBy($order_field, $sort)
                        ->paginate($limit);

            return $items;
        }else{
            return User::select('users.*')
                         ->join('role_users', 'role_users.user_id', '=', 'users.id')
                         ->whereNotIn('role_users.role_id', [2, 3])
                         ->where(function($query) use ($keyword) {
                            $query->where('users.id', '=', $keyword);
                            $query->orWhere('users.first_name', 'like', '%'.$keyword.'%');
                            $query->orWhere('users.last_name', 'like', '%'.$keyword.'%');
                            $query->orWhere('users.email', 'like', '%' . $keyword . '%');
                        })
                         ->count();
        }
        
    }
}