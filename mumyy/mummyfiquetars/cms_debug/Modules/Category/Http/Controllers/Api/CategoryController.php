<?php

namespace Modules\Category\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Modules\Category\Services\CategoryService;

class CategoryController extends Controller
{
    /**
     * @var CategoryService
     */
    private $categoryService;

    public function __construct(CategoryService $categoryService)
    {
        $this->categoryService = $categoryService;
    }

    public function fetch(Request $request){
        $data = $request->all();
        $category_id = $data['category_id'];

        $subCategories = $this->categoryService->getSubCategoryArray($category_id);
        $response = [
                'category_id' => $category_id,
                'data' => $subCategories
        ];
        // return Response::json($response);
        return response()->json($response);
    }

    public function get(Request $request){
        $data = $request->all();
        $category_id = $data['category_id'];

        $subCategories = $this->categoryService->getSubCategoryArray($category_id);
        $response = [
                'category_id' => $category_id,
                'data' => $subCategories
        ];
        // return Response::json($response);
        return response()->json($response);
    }
}
