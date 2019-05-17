<?php namespace Modules\Version\Http\Controllers\Admin;

use Laracasts\Flash\Flash;
use Illuminate\Http\Request;
use Modules\Version\Entities\Version;
use Modules\Version\Repositories\VersionRepository;
use Modules\Core\Http\Controllers\Admin\AdminBaseController;
use Modules\Version\Services\VersionService;
use Modules\Version\Http\Requests\VersionUpdateRequest;

class VersionController extends AdminBaseController
{
    /**
     * @var VersionRepository
     */
    private $version;

    /**
     * @var VersionService
     */
    private $service;

    public function __construct(VersionRepository $version, VersionService $service)
    {
        parent::__construct();

        $this->version = $version;
        $this->service = $service;
    }

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        //$versions = $this->version->all();
        $item = $this->service->getItem();
        $configs = config('asgard.version.config.config');

        return view('version::admin.versions.index', compact('item', 'configs'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  Version $version
     * @param  Request $request
     * @return Response
     */
    public function update(VersionUpdateRequest $request)
    {
        $this->service->update($request->all());

        flash()->success(trans('core::core.messages.resource updated', ['name' => trans('version::versions.title.versions')]));

        return redirect()->route('admin.version.version.index');
    }

}
