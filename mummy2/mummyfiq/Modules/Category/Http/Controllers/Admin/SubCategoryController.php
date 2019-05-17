<?php namespace Modules\Category\Http\Controllers\Admin;

use Laracasts\Flash\Flash;
use Illuminate\Http\Request;
use Modules\Category\Entities\SubCategory;
use Modules\Category\Entities\Category;
use Modules\Category\Repositories\SubCategoryRepository;
use Modules\Core\Http\Controllers\Admin\AdminBaseController;
use Modules\Category\Http\Requests\SubCategoryCreateRequest;
use Modules\Category\Http\Requests\SubCategoryUpdateRequest;
use Modules\Category\Services\CategoryService;
use URL;

class SubCategoryController extends AdminBaseController
{
    /**
     * @var SubCategoryRepository
     */
    private $subcategory;

    /**
     * @var CategoryService
     */
    private $categoryService;

    public function __construct(SubCategoryRepository $subcategory, CategoryService $categoryService)
    {
        parent::__construct();

        $this->subcategory = $subcategory;
        $this->categoryService = $categoryService;
    }

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index(Request $request)
    {
        // $subcategories = SubCategory::whereNull('is_deleted')->get();
        // $categories = $this->subcategory->getCategoriesForSearch();
        // $categories = ['All' => "All Categories"] + $categories;

        // return view('category::admin.subcategories.index', compact(['subcategories', 'categories']));

        $subcategories = $this->categoryService->getSubItems('list', $request);
        $count = $this->categoryService->getSubItems('count', $request);
        $categories = $this->subcategory->getCategoriesForSearch();
        $categories = ['All' => "All Categories"] + $categories;
        $limit = $request->get('limit');
        $keyword = $request->get('keyword');
        $page = $request->get('page');
        $array_field = array('id', 'name');
        $order_field = $request->get('order_field') && in_array($request->get('order_field'), $array_field) ? $request->get('order_field') : 'id';
        $sort = $request->get('sort') ? $request->get('sort') : 'DESC';

        $start = ($page ? $page - 1 : 0) * ($limit ? $limit : 10) + 1;
        $offset = $start + $subcategories->count() - 1;

        return view('category::admin.subcategories.index', compact(['subcategories', 'categories', 'limit', 'keyword', 'page', 'order_field', 'sort', 'count', 'start', 'offset']));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {
        $categories = $this->subcategory->getCategories();

        return view('category::admin.subcategories.create', compact('categories'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  Request $request
     * @return Response
     */
    public function store(SubCategoryCreateRequest $request)
    {
        $this->subcategory->create($request->all());

        flash()->success(trans('core::core.messages.resource created', ['name' => trans('category::subcategories.title.subcategories')]));

        return redirect()->route('admin.category.subcategory.index');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  SubCategory $subcategory
     * @return Response
     */
    public function edit(SubCategory $subcategory)
    {
        $categories = $this->subcategory->getCategories();

        $previousUrl = URL::previous();

        return view('category::admin.subcategories.edit', compact(['subcategory', 'categories', 'previousUrl']));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  SubCategory $subcategory
     * @param  Request $request
     * @return Response
     */
    public function update(SubCategory $subcategory, SubCategoryUpdateRequest $request)
    {
        $this->subcategory->update($subcategory, $request->all());

        flash()->success(trans('core::core.messages.resource updated', ['name' => trans('category::subcategories.title.subcategories')]));

        // return redirect()->route('admin.category.subcategory.index');
        $categories = $this->subcategory->getCategories();

        $previousUrl = $request->get('previousUrl');
        
        if($previousUrl){
            return redirect($previousUrl);
        }
        
        return view('category::admin.subcategories.edit', compact(['subcategory', 'categories']));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  SubCategory $subcategory
     * @return Response
     */
    public function destroy(SubCategory $subcategory)
    {
        $this->subcategory->destroy($subcategory);

        flash()->success(trans('core::core.messages.resource deleted', ['name' => trans('category::subcategories.title.subcategories')]));

        return redirect()->route('admin.category.subcategory.index');
    }
}
