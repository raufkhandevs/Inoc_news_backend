<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Category;
use App\Http\Resources\Api\CategoryResource;
use App\Http\Controllers\Api\BaseController;

class CategoryController extends BaseController
{
    /**
     * Get all categories
     *
     * @return JsonResponse
     */
    public function index()
    {
        $categories = Category::all(); // TODO: Paginate
        return $this->successResponse([
            'categories' => CategoryResource::collection($categories),
        ]);
    }
}
