<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Author;
use App\Http\Resources\Api\AuthorResource;
use App\Http\Controllers\Api\BaseController;

class AuthorController extends BaseController
{
    /**
     * Get all authors
     *
     * @return JsonResponse
     */
    public function index()
    {
        $authors = Author::all(); // TODO: Paginate
        return $this->successResponse([
            'authors' => AuthorResource::collection($authors),
        ]);
    }
}
