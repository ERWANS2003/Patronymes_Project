<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class InputSanitizationMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Sanitiser les entrées GET et POST
        $queryData = $request->query->all();
        $requestData = $request->request->all();

        $this->sanitizeInput($queryData);
        $this->sanitizeInput($requestData);

        // Remettre les données sanitizées dans la requête
        $request->query->replace($queryData);
        $request->request->replace($requestData);

        return $next($request);
    }

    /**
     * Sanitise les données d'entrée
     */
    private function sanitizeInput(array &$data): void
    {
        foreach ($data as $key => &$value) {
            if (is_array($value)) {
                $this->sanitizeInput($value);
            } elseif (is_string($value)) {
                // Supprimer les caractères de contrôle
                $value = preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]/', '', $value);

                // Normaliser les espaces
                $value = preg_replace('/\s+/', ' ', trim($value));

                // Limiter la longueur (protection contre les attaques par déni de service)
                if (strlen($value) > 10000) {
                    $value = substr($value, 0, 10000);
                }

                // Échapper les caractères HTML dangereux
                $value = htmlspecialchars($value, ENT_QUOTES | ENT_HTML5, 'UTF-8', false);
            }
        }
    }
}
