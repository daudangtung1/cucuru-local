<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\ApiController;
use App\Services\CategoryService;
use Illuminate\Http\Request;

;

class CategoryController extends ApiController
{
    /**
     * @var CategoryService
     */
    protected $categoryService;

    /**
     * PostController constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->categoryService = new CategoryService();
    }

    public function index(Request $request)
    {
        $limit = $this->getValidLimit($request->input('limit'), self::DEFAULT_LIMIT);
        $categories = $this->categoryService->getList($limit);
        $this->customPagination($categories['pagination']);

        return $this->responseSuccess($categories['data']);
    }
}
