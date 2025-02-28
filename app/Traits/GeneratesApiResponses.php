<?php

namespace App\Traits;

use Illuminate\Http\JsonResponse;

trait GeneratesApiResponses
{
    protected function success(string $message, array $data = [], int $statusCode = 200): JsonResponse
    {
        return $this->response($message, $data, $statusCode);
    }

    protected function failure(string $message, array $data = [], int $statusCode = 400): JsonResponse
    {
        return $this->response($message, $data, $statusCode);
    }

    protected function response(string $message, array $data = [], int $statusCode = 200): JsonResponse
    {
        return response()->json([
            'message' => $message,
            'data' => $data,
            'status' => $statusCode,
        ], $statusCode);
    }
}
