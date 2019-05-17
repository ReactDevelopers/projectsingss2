<?php namespace Modules\User\Http\Controllers\Admin;

use Modules\User\Http\Requests\RolesRequest;
use Modules\User\Permissions\PermissionManager;
use Modules\User\Repositories\RoleRepository;
use Modules\User\Services\RoleService;
use Illuminate\Http\Request;
use URL;

class RolesController extends BaseUserModuleController
{
    /**
     * @var RoleRepository
     */
    private $role;

    /**
     * @var RoleService
     */
    private $service;

    public function __construct(PermissionManager $permissions, RoleRepository $role, RoleService $service)
    {
        parent::__construct();

        $this->permissions = $permissions;
        $this->role = $role;
        $this->service = $service;
    }

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index(Request $request)
    {
        // $roles = $this->role->all();

        // return view('user::admin.roles.index', compact('roles'));

        $roles = $this->service->getItems('list', $request);
        $count = $this->service->getItems('count', $request);
        $limit = $request->get('limit');
        $keyword = $request->get('keyword');
        $page = $request->get('page');
        $array_field = array('id', 'name');
        $order_field = $request->get('order_field') && in_array($request->get('order_field'), $array_field) ? $request->get('order_field') : 'id';
        $sort = $request->get('sort') ? $request->get('sort') : 'DESC';

        $start = ($page ? $page - 1 : 0) * ($limit ? $limit : 10) + 1;
        $offset = $start + $roles->count() - 1;

        return view('user::admin.roles.index_new', compact(['roles', 'limit', 'keyword', 'page', 'order_field', 'sort', 'count', 'start', 'offset']));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {
        return view('user::admin.roles.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  RolesRequest $request
     * @return Response
     */
    public function store(RolesRequest $request)
    {
        $data = $this->mergeRequestWithPermissions($request);

        $this->role->create($data);

        flash(trans('user::messages.role created'));

        return redirect()->route('admin.user.role.index');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int      $id
     * @return Response
     */
    public function edit($id)
    {
        if (!$role = $this->role->find($id)) {
            flash()->error(trans('user::messages.role not found'));

            return redirect()->route('admin.user.role.index');
        }

        $previousUrl = URL::previous();

        return view('user::admin.roles.edit', compact('role', 'previousUrl'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  int          $id
     * @param  RolesRequest $request
     * @return Response
     */
    public function update($id, RolesRequest $request)
    {
        $data = $this->mergeRequestWithPermissions($request);

        $this->role->update($id, $data);

        flash(trans('user::messages.role updated'));

        $previousUrl = $request->get('previousUrl');
        
        if($previousUrl){
            return redirect($previousUrl);
        }
        
        return redirect()->route('admin.user.role.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int      $id
     * @return Response
     */
    public function destroy($id)
    {
        if($id < 4){
            flash()->error(trans('user::messages.role cannnot deleted'));

            return redirect()->route('admin.user.role.index');
        }

        $this->role->delete($id);

        flash(trans('user::messages.role deleted'));

        return redirect()->route('admin.user.role.index');
    }
}
