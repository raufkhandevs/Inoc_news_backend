<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Responses\ApiResponse;
use Illuminate\Http\JsonResponse;

class BaseController extends Controller
{
    /**
     * Success response method.
     *
     * @param mixed $data
     * @param string $message
     * @param int $statusCode
     * @return JsonResponse
     */
    public function successResponse($data = null, string $message = 'Success', int $statusCode = 200): JsonResponse
    {
        return ApiResponse::success($data, $message, $statusCode);
    }

    /**
     * Error response method.
     *
     * @param string $message
     * @param int $statusCode
     * @param mixed $errors
     * @return JsonResponse
     */
    public function errorResponse(string $message = 'Error', int $statusCode = 400, $errors = null): JsonResponse
    {
        return ApiResponse::error($message, $statusCode, $errors);
    }
}