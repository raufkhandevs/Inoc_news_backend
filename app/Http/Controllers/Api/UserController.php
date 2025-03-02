<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Resources\Api\Auth\UserResource;
use App\Http\Controllers\Api\BaseController;
use App\Http\Requests\Api\UserPreferenceRequest;
use App\Models\User;

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
        return $this->successResponse([
            'user' => new UserResource($request->user()),
        ]);
    }

    /**
     * Update user preferences.
     *
     * @param UserPreferenceRequest $request
     * @return JsonResponse
     */
    public function preferences(UserPreferenceRequest $request)
    {
        $user = $request->user();
    
        // Update category preferences if provided
        if ($request->has('category_ids')) {
            $user->categories()->sync($request->category_ids);
        }
        
        // Update author preferences if provided
        if ($request->has('author_ids')) {
            $user->authors()->sync($request->author_ids);
        }

        return $this->successResponse([
            'user' => new UserResource($user),
        ]);
    }
}
