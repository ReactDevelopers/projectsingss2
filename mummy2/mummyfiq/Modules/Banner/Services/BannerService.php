<?php namespace Modules\Banner\Services;

use DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Pagination\Paginator;
use Illuminate\Http\Request;
use Modules\Media\Services\FileService;
use Modules\Media\Repositories\FileRepository;
use Modules\Banner\Entities\Banner;
use Modules\Banner\Entities\Country;
use Modules\Banner\Entities\Category;
use Modules\Banner\Entities\SubCategory;
use Modules\Banner\Entities\BannerKeyword;
use Modules\Banner\Entities\BannerVendor;
use Modules\Banner\Entities\BannerCategory;
use Modules\Banner\Entities\BannerSubCategory;
use Modules\Banner\Entities\BannerCountry;
use Modules\Banner\Entities\Vendor;
use Modules\Banner\Repositories\BannerRepository;
use Modules\Advertisement\Services\AdvertisementService;
use Modules\Portfolio\Repositories\PortfolioRepository;
use Carbon\Carbon;
use Modules\Vendor\Events\VendorWasCreated;
use Modules\Media\Services\MediaService;
use Modules\Comment\Services\CommentService;

class BannerService {

    /**
     *
     * @var BannerRepository
     */
    private $BannerRepository;  

    /**
     *
     * @var FileService
     */
    private $fileService;

    /**
     * @var FileRepository
     */
    private $file;


    public function __construct( BannerRepository $bannerRepository,FileService $fileService, FileRepository $file)
    {
        $this->bannerRepository  = $bannerRepository;
        $this->fileService = $fileService;
        $this->file = $file;
    }

    public function all(){

        // return json_decode(json_encode(Banner::with(
        //     [
        //         // 'cat'=>function($q){
        //         // },
        //         // 'country'=>function($q){
        //         // },
        //         // 'vendor'=>function($q){
        //         // },
        //         // 'keyword'=>function($q){
        //         // },
        //         'media'=>function($q){
        //         }

        //     ]
        // )->get()),true);

        return Banner::select(['mm__banner.*','media__files.path'])->leftjoin('media__files','media__files.id','=','mm__banner.image')->whereNull('is_deleted')->get();

        // dd($data,'456');
        // return Banner::select(['mm__banner.*','mm__banner_country.country','mm__banner_category.category','mm__banner_vendor.vendor','mm__banner_keywords.keywords','media__files.path'])->leftjoin('mm__banner_country','mm__banner_country.banner_id','=','mm__banner.id')->leftjoin('mm__banner_category','mm__banner_category.banner_id','=','mm__banner.id')->leftjoin('mm__banner_vendor','mm__banner_vendor.banner_id','=','mm__banner.id')->leftjoin('mm__banner_keywords','mm__banner_keywords.banner_id','=','mm__banner.id')->leftjoin('media__files','media__files.id','=','mm__banner.image')->get();

        // select('users.*', 'role_users.role_id')->join('role_users', 'role_users.user_id', '=', 'users.id')->where('role_users.role_id', Config('constant.user_role.vendor'))->get();
    }

    /**
     * [create description]
     * @param  [type] $data [description]
     * @return [type]       [description]
     */
    public function create($data){
        // dd($data);
        $postdata = [
            'title' => $data['title'],
            'type' => $data['type'],
            'image' => empty($data['medias_single']['image']) ? '' : $data['medias_single']['image'],
            'status' => $data['status'],
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')  
        ];

        if($data['type']==0)
        {
            $postdata['link'] = $data['link'];
        }

        // dd($postdata,$request->all(),'1234');

        $Bannerid = $item = $this->bannerRepository->create($postdata);

        if( isset($data["medias_single"]) && !empty($data["medias_single"]) ) {
            $eventImg = $data["medias_single"];

            $item->files()->sync([ $eventImg['image'] => ['imageable_type' => 'Modules\\\\Banner\\\\Entities\\\\Banner', 'zone' => 'image']]);

            //inject file to media module view
            $image = $this->file->findFileByZoneForEntity('image', $item);
            if($image){
                $path = $image->path;
                $pathThumb = getPathThumbImage($image, 'largeThumb');
                $imageInfo = getimagesize($this->convertLinkS3ToHttp($path));
                $dimension = json_encode(array('width' => $imageInfo[0], 'height' => $imageInfo[1]));

                $item->media = $this->getPathImage($image);
                $item->media_thumb = $pathThumb;
                $item->type = 'IMAGE';
                $item->dimension = $dimension;
                $item->save();
            }
        }

        if($data['type']==1)
        {

            foreach ($data['country'] as $k => $v) {
                BannerCountry::create([
                    'banner_id' => $Bannerid->id,
                    'country' => $v,
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s')  
                ]);
            }

            foreach ($data['category'] as $k => $v) {
                BannerCategory::create([
                    'banner_id' => $Bannerid->id,
                    'category' => $v,
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s')  
                ]);
            }

            if(!empty($data['subcategory']))
            foreach ($data['subcategory'] as $k => $v) {
                BannerSubCategory::create([
                    'banner_id' => $Bannerid->id,
                    'subcategory' => $v,
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s')  
                ]);
            }


            foreach ($data['vendor'] as $k => $v) {
                BannerVendor::create([
                    'banner_id' => $Bannerid->id,
                    'vendor' => $v,
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s')  
                ]);
            }

            if(!empty($data['keywords']))
            foreach ($data['keywords'] as $k => $v) {
                BannerKeyword::create([
                    'banner_id' => $Bannerid->id,
                    'keywords' => $v,
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s')  
                ]);
            }

        }
    }


    /**
     * @param $model
     * @param  array  $data
     * @return object
     */
    public function update($model, $data)
    {    
        // $image = $this->file->findFileByZoneForEntity('image', $model);

        // if($image){
        //     $path = $this->getPathImage($image);
        //     $pathThumb = getPathThumbImage($image, 'largeThumb');
        //     $imageInfo = getimagesize($this->convertLinkS3ToHttp($image->path));
        //     $dimension = json_encode(array('width' => $imageInfo[0], 'height' => $imageInfo[1]));

        //     $data = array_merge($data, ['media' => $path, 'media_thumb' => $pathThumb, 'type' => 'IMAGE', 'dimension' => $dimension]);
        // }

        // $model->update($data);

        // return $model;

        $postdata = [
            'title' => $data['title'],
            'type' => $data['type'],
            'image' => ($data['medias_single']['image']),
            'status' => $data['status'],
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')  
        ];

        //dd($data);

        // dd($model,$data,$image,$postdata);   

        if($data['type']==0)
        {
            $postdata['link'] = $data['link'];
        }
        else
        {
            $postdata['link'] = '';   
        }

        $item = Banner::where('id','=',$model['id'])->update($postdata);
        $deleted = DB::table('media__imageables')->whereId($data['oldimageablesid'])->delete();

        if( isset($data["medias_single"]) && !empty($data["medias_single"]) ) {
            $eventImg = $data["medias_single"];

            $model->files()->sync([ $eventImg['image'] => ['imageable_type' => 'Modules\\\\Banner\\\\Entities\\\\Banner', 'zone' => 'image']]);

            //inject file to media module view
            $image = $this->file->findFileByZoneForEntity('image', $model);
            if($image){
                $path = $image->path;
                $pathThumb = getPathThumbImage($image, 'largeThumb');
                $imageInfo = getimagesize($this->convertLinkS3ToHttp($path));
                $dimension = json_encode(array('width' => $imageInfo[0], 'height' => $imageInfo[1]));

                $item->media = $this->getPathImage($image);
                $item->media_thumb = $pathThumb;
                $item->type = 'IMAGE';
                $item->dimension = $dimension;
                $item->save();
            }
        }
        // $this->bannerRepository->update($postdata);
            if(!empty($data['keywords']))
            BannerKeyword::where('banner_id','=',$model['id'])->delete();
            BannerCountry::where('banner_id','=',$model['id'])->delete();
            BannerCategory::where('banner_id','=',$model['id'])->delete();
            if(!empty($data['subcategory']))
            BannerSubCategory::where('banner_id','=',$model['id'])->delete();
            BannerVendor::where('banner_id','=',$model['id'])->delete();
        if($data['type']==1)
        {

            foreach ($data['country'] as $k => $v) {
                BannerCountry::create([
                    'banner_id' => $model['id'],
                    'country' => $v,
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s')  
                ]);
            }

            foreach ($data['category'] as $k => $v) {
                BannerCategory::create([
                    'banner_id' => $model['id'],
                    'category' => $v,
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s')  
                ]);
            }

            if(!empty($data['subcategory']))
            foreach ($data['subcategory'] as $k => $v) {
                BannerSubCategory::create([
                    'banner_id' => $Bannerid->id,
                    'subcategory' => $v,
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s')  
                ]);
            }


            foreach ($data['vendor'] as $k => $v) {
                BannerVendor::create([
                    'banner_id' => $model['id'],
                    'vendor' => $v,
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s')  
                ]);
            }

            if(!empty($data['keywords']))
            foreach ($data['keywords'] as $k => $v) {
                BannerKeyword::create([
                    'banner_id' => $model['id'],
                    'keywords' => $v,
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s')  
                ]);
            }

        }

        return $model;
    }

    
    /**
     * [getCountryArray description]
     * @return [array] [description]
     */
    public function getCountryArray(){
        $results = Country::where('active', 1)->orderBy('name', 'asc')->get();
        $data = [];
        if(!empty($results)){
            foreach ($results as $item)
            {
                $data += [
                    $item->id => $item->name,
                ];
            }
        }
        asort($data);
        return $data;
    }

    /**
     * [getCategoryArray description]
     * @return [array] [description]
     */
    public function getCategoryArray(){
        $categories = Category::whereNull('is_deleted')->orderBy('name', 'asc')->get();
        $data = [];
        if(!empty($categories)){
            foreach ($categories as $item)
            {
                $data += [
                    $item->id => $item->name,
                ];
            }
        }
        return $data;
    }

    /**
     * [getSubCategoryArray description]
     * @return [array] [description]
     */
    public function getSubCategoryArray(){
        $subcategories = SubCategory::select('mm__sub_categories.*')
                         ->whereNull('mm__sub_categories.is_deleted')->orderBy('name', 'asc')->get();
        $data = [];
        if(!empty($subcategories)){
            foreach ($subcategories as $item)
            {
                $data += [
                    $item->id => $item->name,
                ];
            }
        }
        return $data;
    }

    /**
     * [getVendorArray description]
     * @return [array] [description]
     */
    public function getVendorArray(){

        $vendor = Vendor::select('mm__vendors_profile.business_name','mm__vendors_profile.id')
                         ->join('role_users', 'role_users.user_id', '=', 'users.id')
                         ->join('mm__vendors_profile', 'mm__vendors_profile.user_id', '=', 'users.id')
                         ->where('role_users.role_id', Config('constant.user_role.vendor'))
                         ->get();

        $data = [];

        if(!empty($vendor)){
            foreach ($vendor as $item)
            {
                $data += [
                    $item->id => $item->business_name,
                ];
            }
        }

        return $data;
    }

    /**
     * @param  Model $model
     * @return bool
     */
    public function destroy($model)
    {
        return Banner::where('id',$model->id)->update(['is_deleted'=>Carbon::now()->timestamp]);

    }

    
}