<?php namespace Modules\Portfolio\Services;

use DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Pagination\Paginator;
use Illuminate\Http\Request;
use Modules\Portfolio\Entities\Portfolio;
use Modules\Portfolio\Entities\PortfolioMedia;
use Modules\Portfolio\Repositories\PortfolioRepository;
use Modules\Portfolio\Repositories\PortfolioMediaRepository;
use Modules\Media\Services\FileService;
use Modules\Media\Services\MediaService;
use Modules\Media\Repositories\FileRepository;
use Modules\Advertisement\Services\AdvertisementService;
use Modules\Vendor\Services\VendorService;
use Carbon\Carbon;
use Modules\Media\Image\Imagy;

class PortfolioService {

    /**
     *
     * @var PortfolioRepository
     */
    private $portfolioRepository;  

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
     * @var AdvertisementService
     */
    private $advertisementService;

    /**
     * @var VendorService
     */
    private $vendorService;

    /**
     * @var MediaService
     */
    private $mediaService;

    /**
     *
     * @var Imagy
     */
    private $imagy;

    public function __construct( PortfolioRepository $portfolioRepository, PortfolioMediaRepository $portfolioMediaRepository, FileService $fileService, FileRepository $file, AdvertisementService $advertisementService, VendorService $vendorService, Imagy $imagy, MediaService $mediaService) {
        $this->portfolioRepository          = $portfolioRepository;
        $this->portfolioMediaRepository     = $portfolioMediaRepository;
        $this->fileService                  = $fileService;
        $this->file                         = $file;
        $this->advertisementService         = $advertisementService;
        $this->vendorService                = $vendorService;
        $this->imagy                        = $imagy;
        $this->mediaService                 = $mediaService;
    }

    public function all(){
        return Portfolio::whereHas('vendor', function($query){
                            $query->whereNull('is_deleted');
                        })
                         ->where('status', 1)
                         ->orderBy('id', 'DESC')->get();

    }

    public function allRequest(){
        return Portfolio::whereHas('vendor', function($query){
                            $query->whereNull('is_deleted');
                        })
                         ->where('status', 2)
                         ->orderBy('id', 'DESC')->get();

    }

    public function approve($model){
        $model->status = 1;
        $model->save();
        return true;
    }

    public function reject($model){
        $model->status = 3;
        $model->save();
        return true;
    }

    /**
     * [create description]
     * @param  [type] $data [description]
     * @return [type]       [description]
     */
    public function create($data){
        $data = array_merge($data, array('status' => 1));
        $data['sub_category_id'] = $data['sub_category_id'] ? $data['sub_category_id'] : null;
        // $data['created_at'] = \Carbon\Carbon::now('UTC');
        // $data['updated_at'] = \Carbon\Carbon::now('UTC');
        $item = $this->portfolioRepository->create($data);

        if( isset($data["medias_multi"]) && !empty($data["medias_multi"]) ) {
            $eventImgs = $data["medias_multi"]["image"];

            if( isset($eventImgs["files"]) && !empty($eventImgs["files"]) ) {
                foreach ($eventImgs["files"] as $key => $mediaId) {

                    $file = $this->file->find($mediaId);
                    $entityId = $item->id;
                    $dimension = "";

                    if (str_contains($file->mimetype, 'video')) {
                        $path = $pathThumb = getPathImage($file);
                        $pathThumb = getPathThumbImage($file);
                        $thumbnailPath = $file->path->getRelativeUrl();
                        $mediaType = 'VIDEO';
                    } else {
                        $path      = getPathImage($file);
                        $pathThumb = getPathThumbImage($file, 'largeThumb');
                        $pathResizeThumb = getPathThumbImage($file, 'resizeThumb');
                        $imageInfo = @getimagesize(convertLinkS3ToHttp($this->mediaService->getImage($pathResizeThumb)));
                        $dimension = $imageInfo ? json_encode(array('width' => $imageInfo[0], 'height' => $imageInfo[1])) : "";
                        $thumbnailPath = $this->imagy->getThumbnail($file->path, 'mediumThumb');
                        $mediaType = 'IMAGE';
                    }

                    $data = [
                        'portfolio_id' => $entityId,
                        'media_url' => $path,
                        'media_url_thumb' => $pathThumb,
                        'photo_resize' => $pathResizeThumb,
                        'media_type' => $mediaType,
                        'media_source' => 'local',
                        'dimension' => $dimension,
                        'sorts' => $this->getSortMedia($entityId) + 1,
                        'status' => 1
                    ];

                    $this->addMedia($data);

                    // $item->files()->sync([ $eventImg['image'] => ['imageable_type' => 'Modules\\Portfolio\\Entities\\Portfolio', 'zone' => 'image']]);
                    // $image = $this->file->findFileByZoneForEntity('image', $item);
                    // $portfolioMedia = new PortfolioMedia();
                    // $portfolioMedia->portfolio_id = $item->id;
                    
                    // $portfolioMedia->media_url = $this->advertisementService->getPathImage($image);
                    // $portfolioMedia->media_url_thumb = $this->advertisementService->getPathImage($image);
                    // $portfolioMedia->media_type = 'image';
                    // $portfolioMedia->save();
                }
            }
        }  

        return $item;
    }

    public function update($model, $data){
        $vendor_id = $model->vendor_id;
        $data['sub_category_id'] = $data['sub_category_id'] ? $data['sub_category_id'] : null;
        $data['updated_at'] = \Carbon\Carbon::now('UTC');
        $model->update($data);
        // if( $vendor_id != $data['vendor_id']){
        //     $this->updateVendorStatus($vendor_id);
        // }

        // $image = $this->file->findFileByZoneForEntity('image', $model);
        // if( isset($data["medias_single"]) && !empty($data["medias_single"]) ) {
        //     if($image)
        //     {
        //         $model->detachFiles([$image->id]);  
        //     }
            
        //     $eventImg = $data["medias_single"];

        //     $model->files()->sync([ $eventImg['image'] => ['imageable_type' => 'Modules\\Portfolio\\Entities\\Portfolio', 'zone' => 'image']]);
        //     $image1 = $this->file->findFileByZoneForEntity('image', $model);
        //     $portfolioMedia = PortfolioMedia::where('portfolio_id',$model->id)->first();
        //     $portfolioMedia->portfolio_id = $model->id;
            
        //     $portfolioMedia->media_url = $this->advertisementService->getPathImage($image1);
        //     $portfolioMedia->media_url_thumb = $this->advertisementService->getPathImage($image1);
        //     $portfolioMedia->media_type = 'image';
        //     $portfolioMedia->save();
        // } 

        return $model;
    }

    public function destroy($model){
        $vendor_id = $model->id;
        $model->status = 0;
        $model->save();
        $result = $model->delete();

        // set vendor to inactive if there is no portfolio
        // $this->updateVendorStatus($vendor_id);

        return $result;
    }

    public function updateVendorStatus($vendor_id){
        $vendor = $this->vendorService->findBy('id', $vendor_id);
        $portfolio = $this->vendorService->getPortfolio($vendor);
        if(!count($portfolio)){
            $vendor->status = 0;
            $vendor->save();
        }
        return $vendor;
    }

    public function getSortMedia($portfolio_id){
        $media = PortfolioMedia::where('portfolio_id', $portfolio_id)->orderBy('sorts', 'DESC')->first();

        if(count($media)){
            return $media->sorts;
        }

        return -1;
    }

    public function addMedia($data){
        return $this->portfolioMediaRepository->create($data);
    }

    public function removeMedia($id){
        $media = $this->portfolioMediaRepository->findByAttributes(array('id' => $id));
        
        if(count($media)){
            $media->delete();
            return true;
        }

        return false;
    }

       public function getItems($option = 'list', $request){
        $limit = $request->get('limit') ? $request->get('limit') : 10;
        $keyword = $request->get('keyword') ? $request->get('keyword') : "";

        $array_field = array('id', 'business_name', 'category_name', 'title', 'city', 'description');
        $order_field = $request->get('order_field') && in_array($request->get('order_field'), $array_field) ? $request->get('order_field') : 'id';
        $sort = $request->get('sort') ? $request->get('sort') : 'DESC';

        if($option == 'list'){
            $items = Portfolio::select('mm__vendors_portfolios.*', 'mm__vendors_profile.business_name', 'mm__categories.name as category_name')
                         ->join('mm__vendors_profile', 'mm__vendors_profile.user_id', '=', 'mm__vendors_portfolios.vendor_id')
                         ->join('users', 'users.id', '=', 'mm__vendors_profile.user_id')
                         ->join('mm__categories', 'mm__categories.id', '=', 'mm__vendors_portfolios.category_id')
                         ->where('mm__vendors_portfolios.status','=', 1)
                         ->whereNull('mm__vendors_portfolios.is_deleted')
                         ->whereNull('users.is_deleted')
                         ->where(function($query) use ($keyword) {
                            $query->where('mm__vendors_portfolios.id', '=', $keyword);
                            $query->orWhere('mm__vendors_portfolios.city', 'like', '%'.$keyword.'%');
                            $query->orWhere('mm__vendors_portfolios.title', 'like', '%' . $keyword . '%');
                            $query->orWhere('mm__vendors_portfolios.description', 'like', '%' . $keyword . '%');
                            $query->orWhere('mm__vendors_profile.business_name', 'like', '%' . $keyword . '%');
                            $query->orWhere('mm__categories.name', 'like', '%' . $keyword . '%');
                        })
                        ->orderBy($order_field, $sort)
                        ->paginate($limit);

            return $items;
        }else{
            return Portfolio::select('mm__vendors_portfolios.*', 'mm__vendors_profile.business_name')
                         ->join('mm__vendors_profile', 'mm__vendors_profile.user_id', '=', 'mm__vendors_portfolios.vendor_id')
                         ->join('users', 'users.id', '=', 'mm__vendors_profile.user_id')
                         ->join('mm__categories', 'mm__categories.id', '=', 'mm__vendors_portfolios.category_id')
                         ->where('mm__vendors_portfolios.status','=', 1)
                         ->whereNull('mm__vendors_portfolios.is_deleted')
                         ->whereNull('users.is_deleted')
                         ->where(function($query) use ($keyword) {
                            $query->where('mm__vendors_portfolios.id', '=', $keyword);
                            $query->orWhere('mm__vendors_portfolios.city', 'like', '%'.$keyword.'%');
                            $query->orWhere('mm__vendors_portfolios.title', 'like', '%' . $keyword . '%');
                            $query->orWhere('mm__vendors_portfolios.description', 'like', '%' . $keyword . '%');
                            $query->orWhere('mm__vendors_profile.business_name', 'like', '%' . $keyword . '%');
                            $query->orWhere('mm__categories.name', 'like', '%' . $keyword . '%');
                        })
                         ->count();
        }
        
    }

    public function getRequestItems($option = 'list', $request){
        $limit = $request->get('limit') ? $request->get('limit') : 10;
        $keyword = $request->get('keyword') ? $request->get('keyword') : "";

        $array_field = array('id', 'business_name', 'title', 'city', 'description');
        $order_field = $request->get('order_field') && in_array($request->get('order_field'), $array_field) ? $request->get('order_field') : 'id';
        $sort = $request->get('sort') ? $request->get('sort') : 'DESC';

        if($option == 'list'){
            $items = Portfolio::select('mm__vendors_portfolios.*', 'mm__vendors_profile.business_name')
                         ->join('mm__vendors_profile', 'mm__vendors_profile.user_id', '=', 'mm__vendors_portfolios.vendor_id')
                         ->join('users', 'users.id', '=', 'mm__vendors_profile.user_id')
                         ->where(function($query) use ($keyword) {
                            $query->where('mm__vendors_portfolios.status','=', 2);
                            $query->orWhere('mm__vendors_portfolios.status', '=', 3);
                         })
                         ->whereNull('mm__vendors_portfolios.is_deleted')
                         ->whereNull('users.is_deleted')
                         ->where(function($query) use ($keyword) {
                            $query->where('mm__vendors_portfolios.id', '=', $keyword);
                            $query->orWhere('mm__vendors_portfolios.city', 'like', '%'.$keyword.'%');
                            $query->orWhere('mm__vendors_portfolios.title', 'like', '%' . $keyword . '%');
                            $query->orWhere('mm__vendors_portfolios.description', 'like', '%' . $keyword . '%');
                            $query->orWhere('mm__vendors_profile.business_name', 'like', '%' . $keyword . '%');
                        })
                        ->orderBy($order_field, $sort)
                        ->paginate($limit);

            return $items;
        }else{
            return Portfolio::select('mm__vendors_portfolios.*', 'mm__vendors_profile.business_name')
                         ->join('mm__vendors_profile', 'mm__vendors_profile.user_id', '=', 'mm__vendors_portfolios.vendor_id')
                         ->join('users', 'users.id', '=', 'mm__vendors_profile.user_id')
                         ->where(function($query) {
                            $query->where('mm__vendors_portfolios.status','=', 2);
                            $query->orWhere('mm__vendors_portfolios.status', '=', 3);
                         })
                         ->whereNull('mm__vendors_portfolios.is_deleted')
                         ->whereNull('users.is_deleted')
                         ->where(function($query) use ($keyword) {
                            $query->where('mm__vendors_portfolios.id', '=', $keyword);
                            $query->orWhere('mm__vendors_portfolios.city', 'like', '%'.$keyword.'%');
                            $query->orWhere('mm__vendors_portfolios.title', 'like', '%' . $keyword . '%');
                            $query->orWhere('mm__vendors_portfolios.description', 'like', '%' . $keyword . '%');
                            $query->orWhere('mm__vendors_profile.business_name', 'like', '%' . $keyword . '%');
                        })
                         ->count();
        }
        
    }
}