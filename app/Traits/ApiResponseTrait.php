<?php

namespace App\Traits;

trait ApiResponseTrait
{
    public function apiResponse($data = null, $message = null, $status = null)
    {
        $array = [
            'data' => $data,
            'message' => $message,
            'status' => $status,
        ];

        return response()->json($array, $status);
    }
    public function errorApiResponse($errors = null, $message = null, $status = null)
    {
        $array = [
            'errors' => $errors,
            'message' => $message,
            'status' => $status,
        ];

        return response()->json($array, $status);
    }
}
