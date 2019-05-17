<?php namespace Modules\Portfolio\Http\Controllers\Admin;

use Laracasts\Flash\Flash;
use Illuminate\Http\Request;
use Modules\Portfolio\Entities\Portfolio;
use Modules\Portfolio\Entities\PortfolioMedia;
use Modules\Category\Entities\Category;
use Modules\Category\Entities\SubCategory;
use Modules\Vendor\Entities\UserRole;
use Modules\Vendor\Entities\Vendor;
use Modules\Vendor\Services\VendorService;
use Modules\Portfolio\Http\Requests\CreatePortfolioRequest;
use Modules\Portfolio\Repositories\PortfolioRepository;
use Modules\Portfolio\Services\PortfolioService;
use Modules\Core\Http\Controllers\Admin\AdminBaseController;
use Modules\Media\Repositories\FileRepository;
use Modules\Category\Services\CategoryService;
use Modules\Advertisement\Services\AdvertisementService;
use URL;
use Excel;
class PortfolioController extends AdminBaseController
{
    /**
     * @var PortfolioRepository
     */
    private $portfolio;

    /**
     * @var FileRepository
     */
    private $file;

    /**
     * @var PortfolioService
     */
    private $portfolioService;

    /**
     * @var VendorService
     */
    private $vendorService;

    /**
     * @var categoryService
     */
    private $categoryService;

    /**
     * @var AdvertisementService
     */
    private $advertisementService;

    public function __construct(PortfolioRepository $portfolio, FileRepository $file, PortfolioService $portfolioService, VendorService $vendorService, CategoryService $categoryService, AdvertisementService $advertisementService)
    {
        parent::__construct();

        $this->portfolio                = $portfolio;
        $this->file                     = $file;
        $this->portfolioService         = $portfolioService;
        $this->vendorService            = $vendorService;
        $this->categoryService          = $categoryService;
        $this->advertisementService     = $advertisementService;
    }

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index(Request $request)
    {
        // $portfolios = $this->portfolioService->all();

        // return view('portfolio::admin.portfolios.index', compact('portfolios'));

        $portfolios = $this->portfolioService->getItems('list', $request);
        $count = $this->portfolioService->getItems('count', $request);
        $limit = $request->get('limit');
        $keyword = $request->get('keyword');
        $page = $request->get('page');
        $array_field = array('id', 'business_name', 'category_name', 'title', 'city', 'description');
        $order_field = $request->get('order_field') && in_array($request->get('order_field'), $array_field) ? $request->get('order_field') : 'id';
        $sort = $request->get('sort') ? $request->get('sort') : 'DESC';

        $start = ($page ? $page - 1 : 0) * ($limit ? $limit : 10) + 1;
        $offset = $start + $portfolios->count() - 1;

        return view('portfolio::admin.portfolios.index', compact(['portfolios', 'limit', 'keyword', 'page', 'order_field', 'sort', 'count', 'start', 'offset']));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create(Request $request)
    {      
        $data = $request->all();
        $subCategories = $this->categoryService->getSubCategoryArray();
        
        if(!empty($data) && isset($data['vendor'])){
            $vendors = $this->vendorService->findBy('id', $data['vendor']);
            if($vendors){
                $isRedirectBack = true;    
            }else{
                $vendors = $this->vendorService->allArray();
                $isRedirectBack = false;
            }            
        }else{
            $vendors = $this->vendorService->allArray();
            $isRedirectBack = false;
        }

        return view('portfolio::admin.portfolios.create', compact('subCategories','vendors', 'isRedirectBack'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  Request $request
     * @return Response
     */
    public function store(CreatePortfolioRequest $request)
    {
        $item = $this->portfolioService->create($request->all());      
        if($item && $request->has('isRedirectBack')){
            flash()->success(trans('portfolio::portfolios.messages.resource created, can publish vendor', ['name' => trans('portfolio::portfolios.title.portfolios')]));

            return redirect()->route('admin.vendor.vendor.edit', $item->vendor_id);
        }else{
            flash()->success(trans('core::core.messages.resource created'));

            return redirect()->route('admin.portfolio.portfolio.index');
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  Portfolio $portfolio
     * @return Response
     */
    public function edit(Portfolio $portfolio)
    {
        $subCategories = $this->categoryService->getSubCategoryArray();
        $categoryVendor = $this->vendorService->getVendorCategoryArray($portfolio->vendor_id);
        $vendors = $this->vendorService->allArray();
        $imageFiles = PortfolioMedia::where('portfolio_id',$portfolio->id)->get();

        //inject file to media module view
        // $imageFiles = $this->file->findMultipleFilesByZoneForEntity('image', $portfolio);

        $previousUrl = URL::previous();

        return view('portfolio::admin.portfolios.edit', compact('portfolio','vendors', 'categoryVendor', 'subCategories','portfolioMedia', 'imageFiles', 'previousUrl'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  Portfolio $portfolio
     * @param  Request $request
     * @return Response
     */
    public function update(Portfolio $portfolio, CreatePortfolioRequest $request)
    {
        $allRequest = $request->all();
       
        $this->portfolioService->update($portfolio, $request->all());       

        flash()->success(trans('core::core.messages.resource updated', ['name' => trans('portfolio::portfolios.title.portfolios')]));

        $previousUrl = $request->get('previousUrl');
        
        if($previousUrl){
            return redirect($previousUrl);
        }

        return redirect()->route('admin.portfolio.portfolio.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  Portfolio $portfolio
     * @return Response
     */
    public function destroy(Portfolio $portfolio)
    {
        $this->portfolioService->destroy($portfolio);

        flash()->success(trans('core::core.messages.resource deleted', ['name' => trans('portfolio::portfolios.title.portfolios')]));

        return redirect()->route('admin.portfolio.portfolio.index');
    }

    public function fetchdataIndex(Request $request)
    {
        return 1;
        // $data = $request->get('get_category');
        // return $data;
        // if($data)
        // {
        //     $categories = SubCategory::where('category_id',$data)->get();

        //     return $categories;
        // }
        // else
        // {
        //     $categories = SubCategory::where('category_id','!=',0)->get();

        //     return $categories;
        // }


    }

    /**
     * Export Review Data List
     *
     * @param  Review $customer
     * @return Response
     */

    public function getExportPortfolio(Request $request){
        $portfolios = $this->portfolioService->all();
        Excel::create('Portfolios', function($excel) use ($portfolios){
            $excel->sheet('Sheet 1', function($sheet) use ($portfolios){
                $sheet->loadView('portfolio::admin.portfolios.export.portfolio',compact('portfolios'));
            });
        })->export('csv');
    }
}
