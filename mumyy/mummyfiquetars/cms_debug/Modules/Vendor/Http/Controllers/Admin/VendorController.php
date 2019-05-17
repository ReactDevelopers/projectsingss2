<?php namespace Modules\Vendor\Http\Controllers\Admin;

use Laracasts\Flash\Flash;
use Illuminate\Http\Request;
use Modules\Vendor\Entities\Vendor;
use Modules\Vendor\Entities\VendorProfile;
use Modules\Vendor\Repositories\VendorRepository;
use Modules\Core\Http\Controllers\Admin\AdminBaseController;
use Modules\Media\Services\FileService;
use Modules\Media\Repositories\FileRepository;
use Modules\Vendor\Http\Requests\VendorCreateRequest;
use Modules\Vendor\Http\Requests\VendorUpdateRequest;
use Modules\Vendor\Services\VendorService;
use Modules\Category\Services\CategoryService;
use URL;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\DB;
class VendorController extends AdminBaseController
{
    /**
     * @var VendorRepository
     */
    private $vendor;

    /**
     * @var TableBuilder
     */
    private $tableBuilder;

    /**
     *
     * @var FileService
     */
    private $fileService;

    /**
     * @var FileRepository
     */
    private $file;

    /**
     * @var VendorService
     */
    private $vendorService;

    /**
     * @var CategoryService
     */
    private $categoryService;

    public function __construct(VendorRepository $vendor, FileRepository $file, VendorService $vendorService, CategoryService $categoryService)
    {
        parent::__construct();

        $this->vendor = $vendor;
        $this->file = $file;
        $this->vendorService = $vendorService;
        $this->categoryService = $categoryService;
    }

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index(Request $request)
    {
        $vendors = $this->vendorService->getItems('list', $request);
        $count = $this->vendorService->getItems('count', $request);
        $limit = $request->get('limit');
        $keyword = $request->get('keyword');
        $page = $request->get('page');
        $array_field = array('id', 'business_name', 'email', 'last_login');
        $order_field = $request->get('order_field') && in_array($request->get('order_field'), $array_field) ? $request->get('order_field') : 'id';
        $sort = $request->get('sort') ? $request->get('sort') : 'DESC';

        $start = ($page ? $page - 1 : 0) * ($limit ? $limit : 10) + 1;
        $offset = $start + $vendors->count() - 1;

        return view('vendor::admin.vendors.index', compact(['vendors', 'limit', 'keyword', 'page', 'order_field', 'sort', 'count', 'start', 'offset']));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {
        $subCategories = $this->categoryService->getSubCategoryArray();
        $vendorPhonecodes = $this->vendorService->getPhonecodesArr();

        return view('vendor::admin.vendors.create', compact(['subCategories', 'vendorPhonecodes']));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  Request $request
     * @return Response
     */
    public function store(VendorCreateRequest $request)
    {

        $item = $this->vendorService->create($request->all());
        //vendor setting
        DB::table('mm__vendors_settings')->insert([
            'vendor_id' => $item->id,
            'profile_report_leads' => 1,
            'someone_left_a_review' => 1
        ]);
        
        // $this->vendor->create($request->all());

        flash()->success(trans('core::core.messages.resource created', ['name' => trans('vendor::vendors.title.vendors')]));

        return redirect()->route('admin.vendor.vendor.index');
        // return redirect()->route('admin.vendor.vendor.edit', $item->id);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  Vendor $vendor
     * @return Response
     */
    public function edit(Vendor $vendor)
    {
        $location = $this->vendorService->getVendorPrimaryLocation($vendor);
        // $states = $location ? $this->vendorService->getStateArray($location->country_id) : array();
        // $cities = $location ? $this->vendorService->getCityArray($location->states_id) : array();
        
        $countriesArr = $this->vendorService->getVendorCountriesArr($vendor);
        $citiesArr = $this->vendorService->getVendorCitiesArr($vendor, $location->country_id);

        $vendorCategory = $this->vendorService->getVendorCategory($vendor);
        // $subCategories = $vendorCategory ? $this->categoryService->getSubCategoryArray($vendorCategory->category_id) : array();
        $subCategories = $this->categoryService->getSubCategoryArray();
        
        // $vendorPhone = $vendor->vendorPhone->where('is_primary' , 1)->first();
        $vendorPhones = $this->vendorService->getVendorPhones($location->id, $vendor->id);
        $vendorPhonecodes = $this->vendorService->getVendorPhonecodesArr($vendor);

        if(!$vendor->vendorProfile){
            VendorProfile::create([
                    'user_id' => $vendor->id,
                    'created_by' => $vendor->id,
                ]);
           return redirect()->route('admin.vendor.vendor.edit', $vendor->id);
        }

        //inject file to media module view
        $image = $this->file->findFileByZoneForEntity('image', $vendor->vendorProfile);

        // $portfolio = $this->vendorService->getPortfolio($vendor);
        // if(!count($portfolio)){
        //     flash()->warning(trans('vendor::vendors.messages.create portfolio before publish', ['vendor' => $vendor->id]));
        // }
        $previousUrl = URL::previous();
        return view('vendor::admin.vendors.edit', compact(['vendor', 'location', 'image', 'countriesArr', 'citiesArr', 'vendorCategory', 'subCategories', 'vendorPhones', 'vendorPhonecodes', 'previousUrl']));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  Vendor $vendor
     * @param  Request $request
     * @return Response
     */
    public function update(Vendor $vendor, VendorUpdateRequest $request)
    {
        $this->vendorService->update($vendor, $request->all());
        
        // $this->vendor->update($vendor, $request->all());

        flash()->success(trans('core::core.messages.resource updated', ['name' => trans('vendor::vendors.title.vendors')]));

        $previousUrl = $request->get('previousUrl');
        
        if($previousUrl){
            return redirect($previousUrl);
        }
        
        return redirect()->route('admin.vendor.vendor.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  Vendor $vendor
     * @return Response
     */
    public function destroy(Vendor $vendor)
    {
        $this->vendorService->destroy($vendor);

        flash()->success(trans('core::core.messages.resource deleted', ['name' => trans('vendor::vendors.title.vendors')]));

        return redirect()->route('admin.vendor.vendor.index');
    }

    /**
     * Export Vendors Data List
     *
     * @param  Vendors $vendor
     * @return Response
     */

    public function getExportVendors(Request $request){
        $vendors = $this->vendorService->all();
        Excel::create('Vendors', function($excel) use ($vendors){
            $excel->sheet('Sheet 1', function($sheet) use ($vendors){
                $sheet->loadView('vendor::admin.vendors.export.vendor',compact('vendors'));
            });
        })->export('csv');
    }
}
