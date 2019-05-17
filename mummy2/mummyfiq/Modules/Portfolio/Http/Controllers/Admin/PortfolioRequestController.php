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

class PortfolioRequestController extends AdminBaseController
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
        // $portfolios = $this->portfolioService->allRequest();

        // return view('portfolio::admin.portfoliorequest.index', compact('portfolios'));

        $portfolios = $this->portfolioService->getRequestItems('list', $request);
        $count = $this->portfolioService->getRequestItems('count', $request);
        $limit = $request->get('limit');
        $keyword = $request->get('keyword');
        $page = $request->get('page');
        $array_field = array('id', 'business_name', 'email');
        $order_field = $request->get('order_field') && in_array($request->get('order_field'), $array_field) ? $request->get('order_field') : 'id';
        $sort = $request->get('sort') ? $request->get('sort') : 'DESC';

        $start = ($page ? $page - 1 : 0) * ($limit ? $limit : 10) + 1;
        $offset = $start + $portfolios->count() - 1;

        return view('portfolio::admin.portfoliorequest.index', compact(['portfolios', 'limit', 'keyword', 'page', 'order_field', 'sort', 'count', 'start', 'offset']));
    }

    /**
     * Approve item
     *
     * @param  Request $request
     * @return Response
     */
    public function approve(Portfolio $portfolio)
    {
        $this->portfolioService->approve($portfolio);

        flash()->success(trans('portfolio::portfoliorequest.messages.resource approve', ['name' => trans('portfolio::portfoliorequest.title.portfolio request')]));

        return redirect()->route('admin.portfolio.portfoliorequest.index');
    }

    /**
     * Reject item
     *
     * @param  Request $request
     * @return Response
     */
    public function reject(Portfolio $portfolio)
    {
        $this->portfolioService->reject($portfolio);

        flash()->success(trans('portfolio::portfoliorequest.messages.resource reject', ['name' => trans('portfolio::portfoliorequest.title.portfolio request')]));

        return redirect()->route('admin.portfolio.portfoliorequest.index');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  Portfolio $portfolio
     * @return Response
     */
    public function edit(Portfolio $portfolio)
    {
        if($portfolio->status != 2){
            flash()->error(trans('portfolio::portfoliorequest.messages.invalid item'));
        }
        $subCategories = $this->categoryService->getSubCategoryArray();
        $categoryVendor = $this->vendorService->getVendorCategoryArray($portfolio->vendor_id);
        $vendors = $this->vendorService->allArray();
        $imageFiles = PortfolioMedia::where('portfolio_id',$portfolio->id)->get();

        //inject file to media module view
        // $imageFiles = $this->file->findMultipleFilesByZoneForEntity('image', $portfolio);

        $previousUrl = URL::previous();

        return view('portfolio::admin.portfoliorequest.edit', compact('portfolio','vendors', 'categoryVendor', 'subCategories','portfolioMedia', 'imageFiles', 'previousUrl'));
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

        return redirect()->route('admin.portfolio.portfoliorequest.index');
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

        return redirect()->route('admin.portfolio.portfoliorequest.index');
    }

}
