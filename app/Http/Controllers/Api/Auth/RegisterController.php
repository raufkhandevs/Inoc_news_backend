<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Api\BaseController;
use App\Http\Requests\Api\Auth\RegisterRequest;
use App\Http\Resources\Api\Auth\UserResource;
use App\Services\Auth\AuthService;
use Illuminate\Http\JsonResponse;

class RegisterController extends BaseController
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
     * @param RegisterRequest $request
     * @return JsonResponse
     */
    public function __invoke(RegisterRequest $request): JsonResponse
    {
        // Register the user
        $user = $this->authService->register($request->validated());
        
        // Generate token for the user
        $deviceName = $request->input('device_name', 'auth_token');
        $authData = $this->authService->login([
            'email' => $request->email,
            'password' => $request->password,
        ], $deviceName);
        
        return $this->successResponse([
            'user' => new UserResource($user),
            'token' => $authData['token'],
            'token_type' => $authData['token_type'],
        ], 'User registered successfully', 201);
    }
}