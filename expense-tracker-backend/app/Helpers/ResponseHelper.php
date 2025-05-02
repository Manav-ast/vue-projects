<?php

namespace App\Helpers;

use Illuminate\Http\JsonResponse;

class ResponseHelper
{
    public static function success($data = [], $message = '', $statusCode = 200): JsonResponse
    {
        $response = $data;

        if (is_array($response)) {
            $response['success'] = true;
            if (!empty($message)) {
                $response['message'] = $message;
            }
        } else {
            $response = [
                'success' => true,
                'data' => $data
            ];
            if (!empty($message)) {
                $response['message'] = $message;
            }
        }

        return response()->json($response, $statusCode);
    }

    public static function error($message = 'An error occurred.', $statusCode = 500, $errors = []): JsonResponse
    {
        $response = [
            'success' => false,
            'error' => $message
        ];

        if (!empty($errors)) {
            $response['errors'] = $errors;
        }

        return response()->json($response, $statusCode);
    }
}
