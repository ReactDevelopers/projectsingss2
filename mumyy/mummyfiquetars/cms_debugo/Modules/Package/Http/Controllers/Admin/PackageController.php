<?php namespace Modules\Package\Http\Controllers\Admin;

use Laracasts\Flash\Flash;
use Illuminate\Http\Request;
use Modules\Package\Entities\Package;
use Modules\Package\Entities\Plan;
use Modules\Package\Repositories\PackageRepository;
use Modules\Core\Http\Controllers\Admin\AdminBaseController;
use Modules\Package\Http\Requests\PackageCreateRequest;
use Modules\Package\Http\Requests\PackageUpdateRequest;
use Modules\Package\Services\PackageService;
use URL;

class PackageController extends AdminBaseController
{
    /**
     * @var PackageRepository
     */
    private $package;

    /**
     * @var PackageService
     */
    private $service;

    public function __construct(PackageRepository $package, PackageService $service)
    {
        parent::__construct();

        $this->package = $package;
        $this->service = $service;
    }

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index(Request $request)
    {
        // $packages = Package::all();

        // return view('package::admin.packages.index', compact('packages'));

        $packages = $this->service->getItems('list', $request);
        $count = $this->service->getItems('count', $request);
        $limit = $request->get('limit');
        $keyword = $request->get('keyword');
        $page = $request->get('page');
        $array_field = array('id', 'name', 'price', 'type');
        $order_field = $request->get('order_field') && in_array($request->get('order_field'), $array_field) ? $request->get('order_field') : 'id';
        $sort = $request->get('sort') ? $request->get('sort') : 'DESC';

        $start = ($page ? $page - 1 : 0) * ($limit ? $limit : 10) + 1;
        $offset = $start + $packages->count() - 1;

        return view('package::admin.packages.index', compact(['packages', 'limit', 'keyword', 'page', 'order_field', 'sort', 'count', 'start', 'offset']));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {
        return redirect()->route('admin.package.package.index');
        return view('package::admin.packages.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  Request $request
     * @return Response
     */
    public function store(PackageCreateRequest $request)
    {
        $this->package->create($request->all());

        flash()->success(trans('core::core.messages.resource created', ['name' => trans('package::packages.title.packages')]));

        return redirect()->route('admin.package.package.index');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  Package $package
     * @return Response
     */
    public function edit(Plan $package)
    {
        $previousUrl = URL::previous();
        $features = $this->service->getFeaturesPackage($package);

        return view('package::admin.packages.edit', compact(['package', 'previousUrl', 'features']));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  Package $package
     * @param  Request $request
     * @return Response
     */
    public function update(Plan $package, PackageUpdateRequest $request)
    {
        $this->service->update($package, $request->all());

        flash()->success(trans('core::core.messages.resource updated', ['name' => trans('package::packages.title.packages')]));

        $previousUrl = $request->get('previousUrl');
        
        if($previousUrl){
            return redirect($previousUrl);
        }

        return redirect()->route('admin.package.package.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  Package $package
     * @return Response
     */
    public function destroy(Package $package)
    {
        return redirect()->route('admin.package.package.index');
        $this->package->destroy($package);

        flash()->success(trans('core::core.messages.resource deleted', ['name' => trans('package::packages.title.packages')]));

        return redirect()->route('admin.package.package.index');
    }
}
