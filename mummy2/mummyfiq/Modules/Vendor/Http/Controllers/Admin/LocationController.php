<?php namespace Modules\Vendor\Http\Controllers\Admin;

use Laracasts\Flash\Flash;
use Illuminate\Http\Request;
use Modules\Vendor\Entities\Vendor;
use Modules\Vendor\Entities\VendorLocation;
use Modules\Vendor\Repositories\VendorLocationRepository;
use Modules\Core\Http\Controllers\Admin\AdminBaseController;
use Modules\Vendor\Services\LocationService;
use Modules\Vendor\Services\VendorService;
use Modules\Vendor\Http\Requests\LocationCreateRequest;
use Modules\Vendor\Http\Requests\LocationUpdateRequest;
use URL;

class LocationController extends AdminBaseController
{
    /**
     * @var VendorLocationRepository
     */
    private $location;


    /**
     * @var LocationService
     */
    private $service;

    /**
     * @var VendorService
     */
    private $vendorService;

    public function __construct(VendorLocationRepository $location, LocationService $service, VendorService $vendorService)
    {
        parent::__construct();

        $this->location = $location;
        $this->service = $service;
        $this->vendorService = $vendorService;
    }

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index(Vendor $vendor, Request $request)
    {
        $locations = $this->service->getItems('list', $request, $vendor);
        $count = $this->service->getItems('count', $request, $vendor);
        $limit = $request->get('limit');
        $keyword = $request->get('keyword');
        $page = $request->get('page');
        $array_field = array('id', 'country', 'city');
        $order_field = $request->get('order_field') && in_array($request->get('order_field'), $array_field) ? $request->get('order_field') : 'id';
        $sort = $request->get('sort') ? $request->get('sort') : 'DESC';

        $start = ($page ? $page - 1 : 0) * ($limit ? $limit : 10) + 1;
        $offset = $start + $locations->count() - 1;

        return view('vendor::admin.locations.index', compact(['locations', 'limit', 'keyword', 'page', 'order_field', 'sort', 'count', 'start', 'offset', 'vendor']));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create(Vendor $vendor)
    {   
        $countriesArr = $this->vendorService->getCountryArray();

        return view('vendor::admin.locations.create', compact(['vendor', 'countriesArr']));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  Request $request
     * @return Response
     */
    public function store(Vendor $vendor, LocationCreateRequest $request)
    {

        $this->service->create($request->all());
        
        // $this->vendor->create($request->all());

        flash()->success(trans('core::core.messages.resource created', ['name' => trans('vendor::locations.title.location')]));

        return redirect()->route('admin.vendor.location.index', $vendor->id);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  Vendor $vendor
     * @return Response
     */
    public function edit(VendorLocation $location, Vendor $vendor)
    {
        $countriesArr = $this->vendorService->getCountryArray();
        $citiesArr = $this->vendorService->getCitiesArr($location->country_id);
        $vendorPhones = $this->vendorService->getVendorPhones($location->id, $vendor->id);
        $vendorPhonecodes = $this->vendorService->getVendorPhonecodesArr($vendor);
        $previousUrl = URL::previous();

        return view('vendor::admin.locations.edit', compact(['vendor', 'location', 'countriesArr', 'citiesArr', 'vendorPhones', 'vendorPhonecodes', 'previousUrl']));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  Vendor $vendor
     * @param  Request $request
     * @return Response
     */
    public function update(VendorLocation $location, Vendor $vendor, LocationUpdateRequest $request)
    {
        $this->service->update($location, $request->all());
        
        // $this->vendor->update($vendor, $request->all());

        flash()->success(trans('core::core.messages.resource updated', ['name' => trans('vendor::locations.title.location')]));

        $previousUrl = $request->get('previousUrl');
        
        if($previousUrl){
            return redirect($previousUrl);
        }

        return redirect()->route('admin.vendor.location.index', $vendor->id);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  Vendor $vendor
     * @return Response
     */
    public function destroy(VendorLocation $location, Vendor $vendor)
    {
        if($location->is_primary){
            flash()->error(trans('vendor::locations.messages.cannot delete primary location'));

            return redirect()->route('admin.vendor.location.index', $vendor->id);
        }

        $this->service->destroy($location);

        flash()->success(trans('core::core.messages.resource deleted', ['name' => trans('vendor::locations.title.location')]));

        return redirect()->route('admin.vendor.location.index', $vendor->id);
    }
}
