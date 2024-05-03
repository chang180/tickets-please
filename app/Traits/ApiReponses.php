<?php

namespace App\Traits;

trait ApiReponses
{
    protected function ok($message) {
        return $this->success($message);
    }

    protected function success($message, $statusCode = 200)
    {
        return response()->json([
            'message' => $message,
            'status' => $statusCode
        ], $statusCode);
    }
}
