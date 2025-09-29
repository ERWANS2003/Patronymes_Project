<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\RateLimiter;

class RequestValidationMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next)
    {
        // Validation des paramètres de requête
        $this->validateRequestParameters($request);
        
        // Rate limiting pour les requêtes sensibles
        $this->applyRateLimiting($request);
        
        // Sanitisation des entrées
        $this->sanitizeInputs($request);
        
        return $next($request);
    }
    
    private function validateRequestParameters(Request $request)
    {
        // Vérifier la taille de la requête
        if ($request->header('Content-Length') > 10485760) { // 10MB
            Log::warning('Large request detected', [
                'size' => $request->header('Content-Length'),
                'url' => $request->fullUrl(),
                'ip' => $request->ip()
            ]);
        }
        
        // Vérifier les paramètres suspects
        $suspiciousParams = ['<script', 'javascript:', 'onload=', 'onerror='];
        $allInput = $request->all();
        
        foreach ($allInput as $key => $value) {
            if (is_string($value)) {
                foreach ($suspiciousParams as $suspicious) {
                    if (stripos($value, $suspicious) !== false) {
                        Log::warning('Suspicious input detected', [
                            'parameter' => $key,
                            'value' => substr($value, 0, 100),
                            'ip' => $request->ip(),
                            'url' => $request->fullUrl()
                        ]);
                        break;
                    }
                }
            }
        }
    }
    
    private function applyRateLimiting(Request $request)
    {
        $key = 'request_validation:' . $request->ip();
        
        if (RateLimiter::tooManyAttempts($key, 100)) {
            Log::warning('Rate limit exceeded for request validation', [
                'ip' => $request->ip(),
                'url' => $request->fullUrl()
            ]);
        }
        
        RateLimiter::hit($key, 60); // 100 tentatives par minute
    }
    
    private function sanitizeInputs(Request $request)
    {
        $input = $request->all();
        
        foreach ($input as $key => $value) {
            if (is_string($value)) {
                // Supprimer les caractères de contrôle
                $value = preg_replace('/[\x00-\x1F\x7F]/', '', $value);
                
                // Limiter la longueur
                if (strlen($value) > 10000) {
                    $value = substr($value, 0, 10000);
                }
                
                $input[$key] = $value;
            }
        }
        
        $request->merge($input);
    }
}
