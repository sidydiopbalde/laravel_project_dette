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
        
        // Gérer les exceptions spécifiques ServiceException et RepositoryException
        if ($exception instanceof \App\Exceptions\ServiceException) {
            return $this->handleServiceException($exception);
        }

        if ($exception instanceof \App\Exceptions\RepositoryException) {
            return $this->handleRepositoryException($exception);
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
     * Gérer les exceptions ServiceException.
     */
    protected function handleServiceException(ServiceException $exception): JsonResponse
    {
        return response()->json([
            'error' => 'Service Error',
            'message' => $exception->getMessage(),
            'details' => $exception->getDetails(),
        ], $exception->getCode() ?: Response::HTTP_INTERNAL_SERVER_ERROR);
    }

    /**
     * Gérer les exceptions RepositoryException.
     */
    protected function handleRepositoryException(RepositoryException $exception): JsonResponse
    {
        return response()->json([
            'error' => 'Repository Error',
            'message' => $exception->getMessage(),
            'details' => $exception->getDetails(),
        ], $exception->getCode() ?: Response::HTTP_INTERNAL_SERVER_ERROR);
    }

    // Méthodes pour gérer l'authentification, autorisation, validation et modèles non trouvés
    protected function handleAuthenticationException(\Illuminate\Auth\AuthenticationException $exception): JsonResponse
    {
        return response()->json([
            'error' => 'Unauthenticated',
            'message' => 'Vous devez être authentifié pour accéder à cette ressource.'
        ], Response::HTTP_UNAUTHORIZED);
    }

    protected function handleAuthorizationException(\Illuminate\Auth\Access\AuthorizationException $exception): JsonResponse
    {
        return response()->json([
            'error' => 'Unauthorized',
            'message' => 'Vous n\'avez pas la permission d\'accéder à cette ressource.'
        ], Response::HTTP_FORBIDDEN);
    }

    protected function handleValidationException(\Illuminate\Validation\ValidationException $exception): JsonResponse
    {
        return response()->json([
            'error' => 'Validation Error',
            'message' => $exception->getMessage(),
            'errors' => $exception->errors()
        ], Response::HTTP_UNPROCESSABLE_ENTITY);
    }

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
        ], Response::HTTP_INTERNAL_SERVER_ERROR);
    }
}
