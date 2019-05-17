<?php namespace Modules\Category\Services;

use DB;
use Illuminate\Pagination\Paginator;
use Illuminate\Http\Request;
use Modules\Category\Entities\Category;
use Modules\Category\Entities\SubCategory;
use Modules\Category\Repositories\CategoryRepository;
use Modules\Media\Services\FileService;
use Modules\Media\Repositories\FileRepository;

class CategoryService {

    /**
     *
     * @var categoryRepository
     */
    private $categoryRepository;  

    /**
     *
     * @var FileService
     */
    private $fileService;

    /**
     * @var FileRepository
     */
    private $file;

    public function __construct( CategoryRepository $categoryRepository, FileService $fileService, FileRepository $file) {
        $this->categoryRepository   = $categoryRepository;
        $this->fileService          = $fileService;
        $this->file                 = $file;
    }

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

    public function getSubCategoryArray($category_id = false){
        if(!$category_id){
            $subCategories = SubCategory::orderBy('name', 'asc')->get();
        }
        else{
            $subCategories = SubCategory::where('category_id', $category_id)->orderBy('name', 'asc')->get();
        }
        
        $data = [];
        if(!empty($subCategories)){
            foreach ($subCategories as $item)
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
     * [create description]
     * @param  [type] $data [description]
     * @return [type]       [description]
     */
    public function create($data){
        // create item
        $item = $this->categoryRepository->create($data);

        if( isset($data["medias_single"]) && !empty($data["medias_single"]) ) {
            $eventImg = $data["medias_single"];

            $item->files()->sync([ $eventImg['image'] => ['imageable_type' => 'Modules\\Category\\Entities\\Category', 'zone' => 'image']]);

            //inject file to media module view
            $image = $this->file->findFileByZoneForEntity('image', $item);
            if($image){
                $path = $image->path;
                $item->photo = getPathImage($image);
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
            $path = getPathImage($image);
            $data = array_merge($data, ['photo' => $path]);
        }

        // update item        
        $model->update($data);

        return $model;
    }

    public function getItems($option = 'list', $request){
        $limit = $request->get('limit') ? $request->get('limit') : 10;
        $keyword = $request->get('keyword') ? $request->get('keyword') : "";

        $array_field = array('id', 'name', 'status');
        $order_field = $request->get('order_field') && in_array($request->get('order_field'), $array_field) ? $request->get('order_field') : 'id';
        $sort = $request->get('sort') ? $request->get('sort') : 'DESC';

        if($option == 'list'){
            $items = Category::select('mm__categories.*')
                         ->whereNull('mm__categories.is_deleted')
                         ->where(function($query) use ($keyword) {
                            $query->where('mm__categories.id', '=', $keyword);
                            $query->orWhere('mm__categories.name', 'like', '%'.$keyword.'%');
                        })
                        ->orderBy($order_field, $sort)
                        ->paginate($limit);

            return $items;
        }else{
            return Category::select('mm__categories.*')
                         ->whereNull('mm__categories.is_deleted')
                         ->where(function($query) use ($keyword) {
                            $query->where('mm__categories.id', '=', $keyword);
                            $query->orWhere('mm__categories.name', 'like', '%'.$keyword.'%');
                        })
                         ->count();
        }
        
    }

    public function getSubItems($option = 'list', $request){
        $limit = $request->get('limit') ? $request->get('limit') : 10;
        $keyword = $request->get('keyword') ? $request->get('keyword') : "";

        $array_field = array('id', 'name');
        $order_field = $request->get('order_field') && in_array($request->get('order_field'), $array_field) ? $request->get('order_field') : 'id';
        $sort = $request->get('sort') ? $request->get('sort') : 'DESC';

        if($option == 'list'){
            $items = SubCategory::select('mm__sub_categories.*')
                         ->whereNull('mm__sub_categories.is_deleted')
                         ->where(function($query) use ($keyword) {
                            $query->where('mm__sub_categories.id', '=', $keyword);
                            $query->orWhere('mm__sub_categories.name', 'like', '%'.$keyword.'%');
                        })
                         ->orderBy($order_field, $sort)
                         ->paginate($limit);

            return $items;
        }else{
            return SubCategory::select('mm__sub_categories.*')
                         ->whereNull('mm__sub_categories.is_deleted')
                         ->where(function($query) use ($keyword) {
                            $query->where('mm__sub_categories.id', '=', $keyword);
                            $query->orWhere('mm__sub_categories.name', 'like', '%'.$keyword.'%');
                        })
                         ->count();
        }
        
    }
}