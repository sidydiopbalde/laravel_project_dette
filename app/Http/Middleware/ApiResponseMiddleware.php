<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ApiResponseMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        // Exécuter l'action suivante et obtenir la réponse
        $response = $next($request);

        // Vérifier si la réponse est déjà au format JSON
        if ($response instanceof \Illuminate\Http\JsonResponse) {
            return $response;
        }

        // Formater la réponse
        return response()->json([
            'status' => $response->status(),
            'data' => $response->getContent(),
            'message' => $response->status() == 200 ? 'Succès' : 'Erreur'
        ], $response->status());
    }
}
