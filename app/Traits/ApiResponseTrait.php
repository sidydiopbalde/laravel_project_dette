<?php

namespace App\Traits;

trait ApiResponseTrait
{
    /**
     * Formater la réponse API
     *
     * @param int $status
     * @param mixed $data
     * @param string $message
     * @return \Illuminate\Http\JsonResponse
     */
    public function sendResponse($status, $data = null, $message = '')
    {
        return response()->json([
            'status' => $status,
            'data' => $data,
            'message' => $message
        ], $status);
    }
}
