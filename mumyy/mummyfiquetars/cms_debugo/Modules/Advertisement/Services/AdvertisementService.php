<?php namespace Modules\Advertisement\Services;

use DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Pagination\Paginator;
use Illuminate\Http\Request;
use Modules\Media\Services\FileService;
use Modules\Media\Repositories\FileRepository;
use Modules\Advertisement\Entities\Advertisement;
use Modules\Advertisement\Entities\AdvertisementType;
use Modules\Advertisement\Repositories\AdvertisementRepository;
use Modules\Advertisement\Repositories\AdvertisementTypeRepository;
use Modules\Media\UrlResolvers\BaseUrlResolver;

class AdvertisementService {

    /**
     *
     * @var AdvertisementRepository
     */
    private $advertisementRepository;  

    /**
     *
     * @var AdvertisementTypeRepository
     */
    private $advertisementTypeRepository;  

    /**
     *
     * @var FileService
     */
    private $fileService;

    /**
     * @var FileRepository
     */
    private $file;

    public function __construct( AdvertisementRepository $advertisementRepository, AdvertisementTypeRepository $advertisementTypeRepository, FileService $fileService, FileRepository $file) {
        $this->advertisementRepository          = $advertisementRepository;
        $this->advertisementTypeRepository      = $advertisementTypeRepository;
        $this->fileService                      = $fileService;
        $this->file                             = $file;
    }

    /**
     * [getAdvertisementTypeArray description]
     * @return [type] [description]
     */
    public function getAdvertisementTypeArray(){
        $types = AdvertisementType::get();
        $dataTypes = [];
        if(!empty($types)){
            foreach ($types as $item)
            {
                $dataTypes += [
                    $item->id => Config('asgard.advertisement.config.type') [$item->key],
                ];
            }
        }
        return $dataTypes;
    }

    public function getTypesForSearching(){
        $types = $this->advertisementTypeRepository->all();
        $dataTypes = [];
        if(!empty($types)){
            foreach ($types as $item)
            {
                $dataTypes += [
                    $item->id => Config('asgard.advertisement.config.type') [$item->key],
                ];
            }
        }
        return $dataTypes;
    }


    /**
     * [create description]
     * @param  [type] $data [description]
     * @return [type]       [description]
     */
    public function create($data){
        // create item
        $data = $data + ['sorts' => 0];

        if($data['status'] && $data['adv_id'] != 3){
            $itemsWithType = Advertisement::where('adv_id', $data['adv_id'])->update(array('status' => 0));
        }
        $item = $this->advertisementRepository->create($data);

        if( isset($data["medias_single"]) && !empty($data["medias_single"]) ) {
            $eventImg = $data["medias_single"];

            $item->files()->sync([ $eventImg['image'] => ['imageable_type' => 'Modules\\Advertisement\\Entities\\Advertisement', 'zone' => 'image']]);

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

        return $item;
    }

    /**
     * @param $model
     * @param  array  $data
     * @return object
     */
    public function update($model, $data)
    {    
        $image = $this->file->findFileByZoneForEntity('image', $model);

        if($image){
            $path = $this->getPathImage($image);
            $pathThumb = getPathThumbImage($image, 'largeThumb');
            $imageInfo = getimagesize($this->convertLinkS3ToHttp($image->path));
            $dimension = json_encode(array('width' => $imageInfo[0], 'height' => $imageInfo[1]));

            $data = array_merge($data, ['media' => $path, 'media_thumb' => $pathThumb, 'type' => 'IMAGE', 'dimension' => $dimension]);
        }

        // update item    
        if($data['status'] && $model->adv_id != 3){
            $itemsWithType = Advertisement::where('adv_id', $model->adv_id)->where('id', '!=', $model->id)->update(array('status' => 0));
        }

        $model->update($data);

        return $model;
    }

    public function convertLinkS3ToHttp($url){
        if(substr($url, 0,5) == 'https'){
            return str_replace('https', 'http', substr($url,0,5)) . substr($url,5);
        }
        return $url;
    }

    public function getPathImage($file){
        return Config('asgard.media.config.files-path') . $file->filename;
    }

    public function getImage($path){
        $resolver = new BaseUrlResolver();
        $resolvedPath = $resolver->resolve($path);

        return $resolvedPath;
    }

    public function getItems($option = 'list', $request){
        $limit = $request->get('limit') ? $request->get('limit') : 10;
        $keyword = $request->get('keyword') ? $request->get('keyword') : "";
        $type = $request->get('type') ? $request->get('type') : "";

        $array_field = array('id', 'title', 'type_name', 'status');
        $order_field = $request->get('order_field') && in_array($request->get('order_field'), $array_field) ? $request->get('order_field') : 'id';
        $sort = $request->get('sort') ? $request->get('sort') : 'DESC';

        if($option == 'list'){
            if(!$type){
                $items = Advertisement::select('mm__advs_items.*', 'mm__advs_type.name as type_name')
                         ->join('mm__advs_type', 'mm__advs_type.id', '=', 'mm__advs_items.adv_id')
                         ->whereNull('mm__advs_items.is_deleted')
                         ->where(function($query) use ($keyword) {
                            $query->where('mm__advs_items.id', '=', $keyword);
                            $query->orWhere('mm__advs_items.title', 'like', '%'.$keyword.'%');
                            $query->orWhere('mm__advs_type.name', 'like', '%' . $keyword . '%');
                        })
                        ->orderBy($order_field, $sort)
                        ->paginate($limit);
            }else{
                $items = Advertisement::select('mm__advs_items.*', 'mm__advs_type.name as type_name')
                         ->join('mm__advs_type', 'mm__advs_type.id', '=', 'mm__advs_items.adv_id')
                         ->whereNull('mm__advs_items.is_deleted')
                         ->where(function($query) use ($keyword) {
                            $query->where('mm__advs_items.id', '=', $keyword);
                            $query->orWhere('mm__advs_items.title', 'like', '%'.$keyword.'%');
                            $query->orWhere('mm__advs_type.name', 'like', '%' . $keyword . '%');
                        })
                         ->where('mm__advs_type.id', $type)
                        ->orderBy($order_field, $sort)
                        ->paginate($limit);
            }
            
            return $items;
        }else{
            if(!$type){
                return Advertisement::select('mm__advs_items.*', 'mm__advs_type.name as type_name')
                         ->join('mm__advs_type', 'mm__advs_type.id', '=', 'mm__advs_items.adv_id')
                         ->whereNull('mm__advs_items.is_deleted')
                         ->where(function($query) use ($keyword) {
                            $query->where('mm__advs_items.id', '=', $keyword);
                            $query->orWhere('mm__advs_items.title', 'like', '%'.$keyword.'%');
                            $query->orWhere('mm__advs_type.name', 'like', '%' . $keyword . '%');
                        })
                        ->count();
            }else{
                return Advertisement::select('mm__advs_items.*', 'mm__advs_type.name as type_name')
                         ->join('mm__advs_type', 'mm__advs_type.id', '=', 'mm__advs_items.adv_id')
                         ->whereNull('mm__advs_items.is_deleted')
                         ->where(function($query) use ($keyword) {
                            $query->where('mm__advs_items.id', '=', $keyword);
                            $query->orWhere('mm__advs_items.title', 'like', '%'.$keyword.'%');
                            $query->orWhere('mm__advs_type.name', 'like', '%' . $keyword . '%');
                        })
                         ->where('mm__advs_type.id', $type)
                        ->count();
            }
        }
        
    }
}