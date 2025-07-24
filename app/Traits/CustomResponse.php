<?php

namespace App\Traits;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

trait CustomResponse
{
    /**
     * Returns a success json response
     */
    public function success(string $message = 'Operation successful', object|array $data = [], int $code = Response::HTTP_OK): JsonResponse
    {
        $response = [
            'status' => true,
            'message' => $message,
        ];

        if (! empty($data)) {
            $response['data'] = $data;
        }

        return response()->json($response, $code);
    }

    public function error($message = 'Operation failed', $data = null, $code = Response::HTTP_BAD_REQUEST): JsonResponse
    {
        $response = [
            'status' => true,
            'message' => $message,
        ];

        if (! empty($data)) {
            $response['data'] = $data;
        }

        return response()->json($response, $code);
    }

    public function serverError($title, $exception): JsonResponse
    {
        Log::critical("{$title} server error: ", [
            'exception' => $exception,
        ]);

        return response()->json([
            'status' => false,
            'message' => 'Internal server error',
        ], Response::HTTP_INTERNAL_SERVER_ERROR);
    }
}
