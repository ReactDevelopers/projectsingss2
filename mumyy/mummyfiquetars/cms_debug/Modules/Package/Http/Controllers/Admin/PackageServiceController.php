<?php namespace Modules\Package\Http\Controllers\Admin;

use Laracasts\Flash\Flash;
use Illuminate\Http\Request;
use Modules\Package\Entities\PackageService;
use Modules\Package\Repositories\PackageServiceRepository;
use Modules\Core\Http\Controllers\Admin\AdminBaseController;
use Modules\Package\Http\Requests\PackageServiceCreateRequest;
use Modules\Package\Http\Requests\PackageServiceUpdateRequest;

class PackageServiceController extends AdminBaseController
{
    /**
     * @var PackageServiceRepository
     */
    private $packageservice;

    public function __construct(PackageServiceRepository $packageservice)
    {
        parent::__construct();

        $this->packageservice = $packageservice;
    }

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index(Request $request)
    {
        $packageservices = PackageService::all();

        return view('package::admin.packageservices.index', compact('packageservices'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {
        return view('package::admin.packageservices.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  Request $request
     * @return Response
     */
    public function store(PackageServiceCreateRequest $request)
    {
        $this->packageservice->create($request->all());

        flash()->success(trans('core::core.messages.resource created', ['name' => trans('package::packageservices.title.packageservices')]));

        return redirect()->route('admin.package.packageservice.index');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  PackageService $packageservice
     * @return Response
     */
    public function edit(PackageService $packageservice)
    {
        return view('package::admin.packageservices.edit', compact('packageservice'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  PackageService $packageservice
     * @param  Request $request
     * @return Response
     */
    public function update(PackageService $packageservice, PackageServiceUpdateRequest $request)
    {
        $this->packageservice->update($packageservice, $request->all());

        flash()->success(trans('core::core.messages.resource updated', ['name' => trans('package::packageservices.title.packageservices')]));

        return redirect()->route('admin.package.packageservice.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  PackageService $packageservice
     * @return Response
     */
    public function destroy(PackageService $packageservice)
    {
        $this->packageservice->destroy($packageservice);

        flash()->success(trans('core::core.messages.resource deleted', ['name' => trans('package::packageservices.title.packageservices')]));

        return redirect()->route('admin.package.packageservice.index');
    }
}
