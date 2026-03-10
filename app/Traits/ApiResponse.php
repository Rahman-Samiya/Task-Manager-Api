<?php

namespace App\Traits;

use Illuminate\Http\JsonResponse;

trait ApiResponse
{
    /**
     * Return a success response.
     *
     * @param mixed $data
     * @param string $message
     * @param int $statusCode
     * @return JsonResponse
     */
    protected function successResponse($data = null, string $message = 'Success', int $statusCode = 200): JsonResponse
    {
        return response()->json([
            'status' => true,
            'message' => $message,
            'data' => $data,
        ], $statusCode);
    }

    /**
     * Return an error response.
     *
     * @param string $message
     * @param int $statusCode
     * @param mixed $data
     * @return JsonResponse
     */
    protected function errorResponse(string $message = 'Error', int $statusCode = 400, $data = null): JsonResponse
    {
        return response()->json([
            'status' => false,
            'message' => $message,
            'data' => $data,
        ], $statusCode);
    }

    /**
     * Return validation error response.
     *
     * @param array $errors
     * @param int $statusCode
     * @return JsonResponse
     */
    protected function validationErrorResponse(array $errors, int $statusCode = 422): JsonResponse
    {
        return response()->json([
            'status' => false,
            'message' => 'Validation failed',
            'data' => $errors,
        ], $statusCode);
    }
}
