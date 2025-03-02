<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Api\BaseController;
use App\Http\Requests\Api\Auth\LoginRequest;
use App\Http\Resources\Api\Auth\UserResource;
use App\Services\Auth\AuthService;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;

class LoginController extends BaseController
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
     * @param LoginRequest $request
     * @return JsonResponse
     */
    public function __invoke(LoginRequest $request): JsonResponse
    {
        try {
            $deviceName = $request->input('device_name', 'auth_token');
            
            $authData = $this->authService->login([
                'email' => $request->email,
                'password' => $request->password,
            ], $deviceName);
            
            return $this->successResponse([
                'user' => new UserResource($authData['user']),
                'token' => $authData['token'],
                'token_type' => $authData['token_type'],
            ], 'User logged in successfully');
            
        } catch (ValidationException $e) {
            return $this->errorResponse('Invalid credentials', 401, [
                'email' => $e->errors()['email'] ?? ['The provided credentials are incorrect.'],
            ]);
        }
    }
}