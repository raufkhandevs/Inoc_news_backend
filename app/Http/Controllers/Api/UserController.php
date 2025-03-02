<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Resources\Api\Auth\UserResource;
use App\Http\Controllers\Api\BaseController;

class UserController extends BaseController
{
    /**
     * Get the authenticated user.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function me(Request $request)
    {
        return $this->successResponse(new UserResource($request->user()));
    }
}
