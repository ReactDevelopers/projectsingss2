<?php namespace Modules\Page\Services;

use DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Pagination\Paginator;
use Illuminate\Http\Request;
use Modules\Media\Services\FileService;
use Modules\Media\Repositories\FileRepository;
use Modules\Page\Entities\Page;

class PageService {

    public function __construct() {

    }

    public function getItems($option = 'list', $request){
        $limit = $request->get('limit') ? $request->get('limit') : 10;
        $keyword = $request->get('keyword') ? $request->get('keyword') : "";

        $array_field = array('id', 'title');
        $order_field = $request->get('order_field') && in_array($request->get('order_field'), $array_field) ? $request->get('order_field') : 'id';
        $sort = $request->get('sort') ? $request->get('sort') : 'DESC';

        if($option == 'list'){
            $items = Page::select('page__pages.*', 'page__page_translations.title')
                         ->join('page__page_translations', 'page__page_translations.page_id', '=', 'page__pages.id')
                         ->where(function($query) use ($keyword) {
                            $query->where('page__pages.id', '=', $keyword);
                            $query->orWhere('page__page_translations.title', 'like', '%'.$keyword.'%');
                        })
                        ->orderBy($order_field, $sort)
                        ->paginate($limit);

            return $items;
        }else{
            return Page::select('page__pages.*', 'page__page_translations.title')
                         ->join('page__page_translations', 'page__page_translations.page_id', '=', 'page__pages.id')
                         ->where(function($query) use ($keyword) {
                            $query->where('page__pages.id', '=', $keyword);
                            $query->orWhere('page__page_translations.title', 'like', '%'.$keyword.'%');
                        })
                         ->count();
        }
        
    }

}