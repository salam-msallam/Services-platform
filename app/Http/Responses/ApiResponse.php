<?php

namespace App\Http\Responses;

use Illuminate\Http\JsonResponse;

final class ApiResponse
{
    public static function success(mixed $data = [], string $message = ''): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => $data,
            'message' => $message,
        ]);
    }

    public static function error(string $message, mixed $data = [], int $status = 400): JsonResponse
    {
        return response()->json([
            'success' => false,
            'data' => $data,
            'message' => $message,
        ], $status);
    }
}
