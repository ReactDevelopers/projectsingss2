<?php namespace Modules\User\Http\Controllers\Admin;

use Modules\Core\Contracts\Authentication;
use Modules\User\Events\UserHasBegunResetProcess;
use Modules\User\Http\Requests\CreateUserRequest;
use Modules\User\Http\Requests\UpdateUserRequest;
use Modules\User\Permissions\PermissionManager;
use Modules\User\Repositories\RoleRepository;
use Modules\User\Repositories\UserRepository;
use Modules\User\Services\UserService;
use Illuminate\Http\Request;
use URL;
use Excel;
class UserController extends BaseUserModuleController
{
    /**
     * @var UserRepository
     */
    private $user;
    /**
     * @var RoleRepository
     */
    private $role;
    /**
     * @var Authentication
     */
    private $auth;

    /**
     * @var UserService
     */
    private $service;

    /**
     * @param PermissionManager $permissions
     * @param UserRepository    $user
     * @param RoleRepository    $role
     * @param Authentication    $auth
     */
    public function __construct(
        PermissionManager $permissions,
        UserRepository $user,
        RoleRepository $role,
        Authentication $auth,
        UserService $service
    ) {
        parent::__construct();

        $this->permissions = $permissions;
        $this->user = $user;
        $this->role = $role;
        $this->auth = $auth;
        $this->service = $service;
    }

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index(Request $request)
    {
        // $users = $this->user->allUser();

        // $currentUser = $this->auth->check();

        // return view('user::admin.users.index', compact('users', 'currentUser'));

        $users = $this->service->getItems('list', $request);
        $count = $this->service->getItems('count', $request);
        $limit = $request->get('limit');
        $keyword = $request->get('keyword');
        $page = $request->get('page');
        $array_field = array('id', 'first_name', 'last_name', 'email', 'created_at', 'last_login');
        $order_field = $request->get('order_field') && in_array($request->get('order_field'), $array_field) ? $request->get('order_field') : 'id';
        $sort = $request->get('sort') ? $request->get('sort') : 'DESC';

        $start = ($page ? $page - 1 : 0) * ($limit ? $limit : 10) + 1;
        $offset = $start + $users->count() - 1;

        $currentUser = $this->auth->check();

        return view('user::admin.users.index', compact(['users', 'currentUser', 'limit', 'keyword', 'page', 'order_field', 'sort', 'count', 'start', 'offset']));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {
        $roles = $this->role->all();

        return view('user::admin.users.create', compact('roles'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  CreateUserRequest $request
     * @return Response
     */
    public function store(CreateUserRequest $request)
    {
        $data = $this->mergeRequestWithPermissions($request);

        $this->user->createWithRoles($data, $request->roles, true);

        flash(trans('user::messages.user created'));

        return redirect()->route('admin.user.user.index');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int      $id
     * @return Response
     */
    public function edit($id)
    {
        if (!$user = $this->user->find($id)) {
            flash()->error(trans('user::messages.user not found'));

            return redirect()->route('admin.user.user.index');
        }
        $roles = $this->role->all();

        $currentUser = $this->auth->check();

        $previousUrl = URL::previous();

        return view('user::admin.users.edit', compact('user', 'roles', 'currentUser', 'previousUrl'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  int               $id
     * @param  UpdateUserRequest $request
     * @return Response
     */
    public function update($id, UpdateUserRequest $request)
    {
        $data = $this->mergeRequestWithPermissions($request);

        $this->user->updateAndSyncRoles($id, $data, $request->roles);

        flash(trans('user::messages.user updated'));
        
        $previousUrl = $request->get('previousUrl');
        
        if($previousUrl){
            return redirect($previousUrl);
        }

        return redirect()->route('admin.user.user.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int      $id
     * @return Response
     */
    public function destroy($id)
    {
        $this->user->delete($id);

        flash(trans('user::messages.user deleted'));

        return redirect()->route('admin.user.user.index');
    }

    public function sendResetPassword($user, Authentication $auth)
    {
        $user = $this->user->find($user);
        $code = $auth->createReminderCode($user);

        event(new UserHasBegunResetProcess($user, $code));

        flash(trans('user::auth.reset password email was sent'));

        return redirect()->route('admin.user.user.edit', $user->id);
    }

    /**
     * Export User Data List
     *
     * @param  User $user
     * @return Response
     */

    public function getExportUsers(Request $request){
        $users = $this->service->getItems('list',$request);
        Excel::create('Users', function($excel) use ($users){
            $excel->sheet('Sheet 1', function($sheet) use ($users){
                $sheet->loadView('user::admin.users.export.user',compact('users'));
            });
        })->export('csv');
    }
}
