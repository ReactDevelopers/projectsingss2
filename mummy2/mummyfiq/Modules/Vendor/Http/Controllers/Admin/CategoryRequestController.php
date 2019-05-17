<?php namespace Modules\Vendor\Http\Controllers\Admin;

use Laracasts\Flash\Flash;
use Illuminate\Http\Request;
use Modules\Vendor\Entities\Vendor;
use Modules\Vendor\Entities\VendorProfile;
use Modules\Vendor\Entities\VendorCategory;
use Modules\Vendor\Repositories\VendorRepository;
use Modules\Core\Http\Controllers\Admin\AdminBaseController;
use Modules\Media\Services\FileService;
use Modules\Media\Repositories\FileRepository;
use Modules\Vendor\Http\Requests\VendorCreateRequest;
use Modules\Vendor\Http\Requests\VendorUpdateRequest;
use Modules\Vendor\Services\VendorService;
use Modules\Vendor\Services\CategoryRequestService;
use Modules\Category\Services\CategoryService;

class CategoryRequestController extends AdminBaseController
{
    /**
     * @var VendorRepository
     */
    private $vendor;

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

    /**
     * @var CategoryRequestService
     */
    private $categoryRequestService;

    public function __construct(VendorRepository $vendor, FileRepository $file, VendorService $vendorService, CategoryService $categoryService, CategoryRequestService $categoryRequestService)
    {
        parent::__construct();

        $this->vendor = $vendor;
        $this->file = $file;
        $this->vendorService = $vendorService;
        $this->categoryService = $categoryService;
        $this->categoryRequestService = $categoryRequestService;
    }

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index(Request $request)
    {
        // $categoryRequest = $this->categoryRequestService->all();
        // return view('vendor::admin.vendorcategoryrequest.index', compact('categoryRequest'));

        $categoryRequest = $this->categoryRequestService->getItems('list', $request);
        $count = $this->categoryRequestService->getItems('count', $request);
        $limit = $request->get('limit');
        $keyword = $request->get('keyword');
        $page = $request->get('page');
        $array_field = array('id', 'business_name', 'category_name');
        $order_field = $request->get('order_field') && in_array($request->get('order_field'), $array_field) ? $request->get('order_field') : 'id';
        $sort = $request->get('sort') ? $request->get('sort') : 'DESC';

        $start = ($page ? $page - 1 : 0) * ($limit ? $limit : 10) + 1;
        $offset = $start + $categoryRequest->count() - 1;

        return view('vendor::admin.vendorcategoryrequest.index', compact(['categoryRequest', 'limit', 'keyword', 'page', 'order_field', 'sort', 'count', 'start', 'offset']));
    }

    /**
     * Approve item.
     *
     * @return Response
     */
    public function approve(VendorCategory $vendorCategory)
    {
        $this->categoryRequestService->approve($vendorCategory);

        flash()->success(trans('vendor::categoryrequest.messages.resource approve', ['name' => trans('vendor::categoryrequest.title.vendors category request')]));

        return redirect()->route('admin.vendor.categoryrequest.index');
    }

   
    /**
     * Reject item.
     *
     * @return Response
     */
    public function reject(VendorCategory $vendorCategory)
    {
        $this->categoryRequestService->reject($vendorCategory);

        flash()->success(trans('vendor::categoryrequest.messages.resource reject', ['name' => trans('vendor::categoryrequest.title.vendors category request')]));

        return redirect()->route('admin.vendor.categoryrequest.index');
    }

   
    /**
     * Remove the specified resource from storage.
     *
     * @param  Vendor $vendor
     * @return Response
     */
    public function destroy(VendorCategory $vendorCategory)
    {
        $this->categoryRequestService->destroy($vendorCategory);

        flash()->success(trans('core::core.messages.resource deleted', ['name' => trans('vendor::categoryrequest.title.vendors category request')]));

        return redirect()->route('admin.vendor.categoryrequest.index');
    }
}
