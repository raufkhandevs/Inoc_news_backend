<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Api\BaseController;
use App\Services\Auth\AuthService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class LogoutController extends BaseController
{
    /**
     * Auth service instance.
     *
     * @var AuthService
     */
    protected AuthService $authService;

    /**
     * Create a new controller instance.
     *
     * @param AuthService $authService
     */
    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    /**
     * Handle the incoming request.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function __invoke(Request $request): JsonResponse
    {
        $user = $request->user();
        $deviceName = $request->input('device_name');
        
        $this->authService->logout($user, $deviceName);
        
        return $this->successResponse(null, 'Successfully logged out');
    }
}