<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class Handler extends ExceptionHandler
{
    public function render($request, Throwable $exception): JsonResponse
    {
        // Exclure les exceptions qui sont des instances de ServiceException ou RepositoryException
        if ($exception instanceof \App\Exceptions\ServiceException ||
            $exception instanceof \App\Exceptions\RepositoryException) {
            return parent::render($request, $exception);
        }

        // Gérer les exceptions liées à l'authentification
        if ($exception instanceof \Illuminate\Auth\AuthenticationException) {
            return $this->handleAuthenticationException($exception);
        }

        // Gérer les exceptions liées aux autorisations
        if ($exception instanceof \Illuminate\Auth\Access\AuthorizationException) {
            return $this->handleAuthorizationException($exception);
        }

        // Gérer les exceptions de validation
        if ($exception instanceof \Illuminate\Validation\ValidationException) {
            return $this->handleValidationException($exception);
        }

        // Gérer les exceptions de modèle non trouvé (ModelNotFoundException)
        if ($exception instanceof \Illuminate\Database\Eloquent\ModelNotFoundException) {
            return $this->handleModelNotFoundException($exception);
        }

        // Gérer les exceptions génériques ou inconnues
        return $this->handleGenericException($exception);
    }

    /**
     * Gérer les exceptions d'authentification.
     */
    protected function handleAuthenticationException(\Illuminate\Auth\AuthenticationException $exception): JsonResponse
    {
        return response()->json([
            'error' => 'Unauthenticated',
            'message' => 'Vous devez être authentifié pour accéder à cette ressource.'
        ], Response::HTTP_UNAUTHORIZED);
    }

    /**
     * Gérer les exceptions d'autorisation.
     */
    protected function handleAuthorizationException(\Illuminate\Auth\Access\AuthorizationException $exception): JsonResponse
    {
        return response()->json([
            'error' => 'Unauthorized',
            'message' => 'Vous n\'avez pas la permission d\'accéder à cette ressource.'
        ], Response::HTTP_FORBIDDEN);
    }

    /**
     * Gérer les exceptions de validation.
     */
    protected function handleValidationException(\Illuminate\Validation\ValidationException $exception): JsonResponse
    {
        return response()->json([
            'error' => 'Validation Error',
            'message' => $exception->getMessage(),
            'errors' => $exception->errors()
        ], Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    /**
     * Gérer les exceptions de modèle non trouvé.
     */
    protected function handleModelNotFoundException(\Illuminate\Database\Eloquent\ModelNotFoundException $exception): JsonResponse
    {
        return response()->json([
            'error' => 'Not Found',
            'message' => 'Ressource non trouvée.'
        ], Response::HTTP_NOT_FOUND);
    }

    /**
     * Gérer les exceptions génériques ou inconnues.
     */
    protected function handleGenericException(Throwable $exception): JsonResponse
    {
        return response()->json([
            'error' => 'Server Error',
            'message' => 'Une erreur interne du serveur est survenue.',
            'exception' => get_class($exception),
            'details' => $exception->getMessage(),
            // Vous pouvez également inclure un backtrace ou d'autres informations en mode de développement
        ], Response::HTTP_INTERNAL_SERVER_ERROR);
    }
}
