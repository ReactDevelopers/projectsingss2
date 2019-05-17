<?php namespace Modules\Category\Http\Controllers\Admin;

use Laracasts\Flash\Flash;
use Illuminate\Http\Request;
use Modules\Category\Entities\Category;
use Modules\Category\Repositories\CategoryRepository;
use Modules\Core\Http\Controllers\Admin\AdminBaseController;
use Modules\Category\Http\Requests\CategoryCreateRequest;
use Modules\Category\Http\Requests\CategoryUpdateRequest;
use Modules\Category\Services\CategoryService;
use Modules\Media\Repositories\FileRepository;
use URL;

class CategoryController extends AdminBaseController
{
    /**
     * @var CategoryRepository
     */
    private $category;

    /**
     * @var CategoryService
     */
    private $categoryService;

    /**
     * @var FileRepository
     */
    private $file;

    public function __construct(CategoryRepository $category, CategoryService $categoryService, FileRepository $file)
    {
        parent::__construct();

        $this->category         = $category;
        $this->categoryService  = $categoryService;
        $this->file             = $file;
    }

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index(Request $request)
    {
        // $categories = Category::all();

        // return view('category::admin.categories.index', compact('categories'));

        $categories = $this->categoryService->getItems('list', $request);
        $count = $this->categoryService->getItems('count', $request);
        $limit = $request->get('limit');
        $keyword = $request->get('keyword');
        $page = $request->get('page');
        $array_field = array('id', 'name', 'status');
        $order_field = $request->get('order_field') && in_array($request->get('order_field'), $array_field) ? $request->get('order_field') : 'id';
        $sort = $request->get('sort') ? $request->get('sort') : 'DESC';

        $start = ($page ? $page - 1 : 0) * ($limit ? $limit : 10) + 1;
        $offset = $start + $categories->count() - 1;

        return view('category::admin.categories.index', compact(['categories', 'limit', 'keyword', 'page', 'order_field', 'sort', 'count', 'start', 'offset']));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {
        return view('category::admin.categories.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  Request $request
     * @return Response
     */
    public function store(CategoryCreateRequest $request)
    {
        $this->categoryService->create($request->all());

        flash()->success(trans('core::core.messages.resource created', ['name' => trans('category::categories.title.categories')]));

        return redirect()->route('admin.category.category.index');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  Category $category
     * @return Response
     */
    public function edit(Category $category)
    {
        //inject file to media module view
        $image = $this->file->findFileByZoneForEntity('image', $category);

        $previousUrl = URL::previous();

        return view('category::admin.categories.edit', compact(['category', 'image', 'previousUrl']));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  Category $category
     * @param  Request $request
     * @return Response
     */
    public function update(Category $category, CategoryUpdateRequest $request)
    {
        $this->categoryService->update($category, $request->all());

        flash()->success(trans('core::core.messages.resource updated', ['name' => trans('category::categories.title.categories')]));

        // return redirect()->route('admin.category.category.index');
        //inject file to media module view
        $image = $this->file->findFileByZoneForEntity('image', $category);

        $previousUrl = $request->get('previousUrl');
        
        if($previousUrl){
            return redirect($previousUrl);
        }
        
        return view('category::admin.categories.edit', compact(['category', 'image']));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  Category $category
     * @return Response
     */
    public function destroy(Category $category)
    {
        $this->category->destroy($category);

        flash()->success(trans('core::core.messages.resource deleted', ['name' => trans('category::categories.title.categories')]));

        return redirect()->route('admin.category.category.index');
    }
}
