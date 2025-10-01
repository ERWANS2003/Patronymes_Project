<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class ApiResponseMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);

        // Standardiser les réponses API
        if ($request->is('api/*') && $response instanceof JsonResponse) {
            $data = $response->getData(true);

            // Ajouter des métadonnées standard
            $standardizedData = [
                'success' => $response->getStatusCode() < 400,
                'status_code' => $response->getStatusCode(),
                'message' => $this->getStatusMessage($response->getStatusCode()),
                'data' => $data,
                'timestamp' => now()->toISOString(),
                'version' => '1.0'
            ];

            $response->setData($standardizedData);
        }

        return $response;
    }

    private function getStatusMessage($statusCode)
    {
        $messages = [
            200 => 'Success',
            201 => 'Created',
            400 => 'Bad Request',
            401 => 'Unauthorized',
            403 => 'Forbidden',
            404 => 'Not Found',
            422 => 'Validation Error',
            500 => 'Internal Server Error'
        ];

        return $messages[$statusCode] ?? 'Unknown';
    }
}
