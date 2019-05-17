<?php namespace Modules\Banner\Http\Controllers\Admin;

use Laracasts\Flash\Flash;
use Illuminate\Http\Request;
use Modules\Banner\Entities\Banner;
use Modules\Banner\Entities\BannerCountry;
use Modules\Banner\Entities\BannerCategory;
use Modules\Banner\Entities\BannerSubCategory;
use Modules\Banner\Entities\BannerVendor;
use Modules\Banner\Entities\BannerKeyword;
use Modules\Banner\Repositories\BannerRepository;
use Modules\Media\Services\FileService;
use Modules\Media\Repositories\FileRepository;
use Modules\Banner\Services\BannerService;
use Modules\Core\Http\Controllers\Admin\AdminBaseController;
use Modules\Banner\Http\Requests\BannerCreateRequest;

class BannerController extends AdminBaseController
{
    /**
     * @var BannerRepository
     */
    private $banner;

    /**
     *
     * @var FileService
     */
    private $fileService;

    /**
     * @var FileRepository
     */
    private $file;

    public function __construct(BannerRepository $banner,BannerService $bannerService,FileService $fileService, FileRepository $file)
    {
        parent::__construct();

        $this->banner = $banner;
        $this->bannerService = $bannerService;
        $this->fileService = $fileService;
        $this->file = $file;
    }

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        $banners = $this->bannerService->all();

        return view('banner::admin.banners.index', compact('banners'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {
        $countriesArr = $this->bannerService->getCountryArray();
        $categoryArr = $this->bannerService->getCategoryArray();
        $vendorArr = $this->bannerService->getVendorArray();
        $subcategoryArr = $this->bannerService->getSubCategoryArray();
       
        return view('banner::admin.banners.create',compact('countriesArr','categoryArr','vendorArr','subcategoryArr'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  Request $request
     * @return Response
     */
    // BannerCreateRequest $request
    public function store(BannerCreateRequest $request)
    {
        $this->bannerService->create($request->all());
        
        flash()->success(trans('core::core.messages.resource created', ['name' => trans('banner::banners.title.banners')]));

        return redirect()->route('admin.banner.banner.index');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  Banner $banner
     * @return Response
     */
    public function edit(Banner $banner)
    {
        $data['picture'] = Banner::select(['media__files.path','media__imageables.id as imageablesid','mm__banner.image'])->leftjoin('media__files','media__files.id','=','mm__banner.image')->leftjoin('media__imageables','media__imageables.file_id','=','mm__banner.image')->whereNull('is_deleted')->where('media__files.id',$banner->image)->first();

        // dd($data['image']);
        $countriesArr = $this->bannerService->getCountryArray();
        $categoryArr = $this->bannerService->getCategoryArray();
        $vendorArr = $this->bannerService->getVendorArray();
        $subcategoryArr = $this->bannerService->getSubCategoryArray();

        $bk = BannerKeyword::where('banner_id','=',$banner->id)->select(\DB::raw("GROUP_CONCAT(keywords) as keywords "),\DB::raw("COUNT(DISTINCT(keywords)) as count "))->get();;
        $bco = BannerCountry::where('banner_id','=',$banner->id)->select(\DB::raw("GROUP_CONCAT(country) as country "))->get();
        $bv = BannerVendor::where('banner_id','=',$banner->id)->select(\DB::raw("GROUP_CONCAT(vendor) as vendor "))->get();
        $cat = BannerCategory::where('banner_id','=',$banner->id)->select(\DB::raw("GROUP_CONCAT(category) as category "))->get();
        $subcat = BannerSubCategory::where('banner_id','=',$banner->id)->select(\DB::raw("GROUP_CONCAT(subcategory) as subcategory "))->get();
        // dd($bk);
        $data['bk'] = explode(",",$bk[0]['keywords']);
        $data['count'] = explode(",",$bk[0]['count']);

        $data['bco'] = explode(",",$bco[0]['country']);
        $data['bv'] = explode(",",$bv[0]['vendor']);
        $data['cat'] = explode(",",$cat[0]['category']);
        $data['subcat'] = explode(",",$subcat[0]['subcategory']);
        
// dd($data);
        // ->pluck('country')->all();
        // dd($bk,$bco,$bc,$cat,$banner->id,$banner);
        // dd($banner,'123');
        return view('banner::admin.banners.edit', compact('banner','countriesArr','categoryArr','vendorArr','subcategoryArr'))->with($data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  Banner $banner
     * @param  Request $request
     * @return Response
     */
    public function update(BannerCreateRequest $request,Banner $banner)
    {
        // dd($request->all(),'65656',$banner);
        $this->bannerService->update($banner, $request->all());

        flash()->success(trans('core::core.messages.resource updated', ['name' => trans('banner::banners.title.banners')]));

        return redirect()->route('admin.banner.banner.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  Banner $banner
     * @return Response
     */
    public function destroy(Banner $banner)
    {
        $this->bannerService->destroy($banner);

        flash()->success(trans('core::core.messages.resource deleted', ['name' => trans('banner::banners.title.banners')]));

        return redirect()->route('admin.banner.banner.index');
    }
}
