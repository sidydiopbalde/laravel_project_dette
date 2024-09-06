<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Contracts\Support\Arrayable;

class CentralizeResponse
{
    public function handle(Request $request, Closure $next)
    {
        // Proceed with the request and get the response from the controller
        $response = $next($request);
        
        // Check if the response is a valid array or a JsonResponse object
        if ($response instanceof JsonResponse) {
            $data = $response->getData(true); // Convert JsonResponse to array
        } else {
            $data = $response instanceof Arrayable ? $response->toArray() : (array) $response;
        }

        // Set default status and message
        $status = 200;
        $message = 'Success';
        
        // Process the response content to check for specific keys
        if (isset($data['statut']) && $data['statut'] === 'KO') {
            $status = $data['code'] ?? 400;
            $message = $data['message'] ?? 'Error';
        }

        // If there's no data, it's a success with no content
        if (empty($data)) {
            $status = 200;
            $message = 'Success';
        }

        // Return the formatted JSON response with status, message, and data
        return response()->json([
            'success' => $status === 200,
            'message' => $message,
            'data'    => $data
        ], $status);
    }
}
