<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;

class SecurityMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        // Protection contre les attaques XSS
        $this->sanitizeInput($request);
        
        // Rate limiting pour les requêtes sensibles
        if ($request->is('patronymes/create') || $request->is('patronymes/*/edit')) {
            $key = 'patronyme_actions:' . $request->ip();
            if (RateLimiter::tooManyAttempts($key, 5)) {
                return response()->json(['error' => 'Trop de tentatives. Veuillez patienter.'], 429);
            }
            RateLimiter::hit($key, 300); // 5 tentatives par 5 minutes
        }
        
        // Headers de sécurité
        $response = $next($request);
        
        $response->headers->set('X-Content-Type-Options', 'nosniff');
        $response->headers->set('X-Frame-Options', 'DENY');
        $response->headers->set('X-XSS-Protection', '1; mode=block');
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');
        $response->headers->set('Permissions-Policy', 'geolocation=(), microphone=(), camera=()');
        
        return $response;
    }
    
    private function sanitizeInput(Request $request)
    {
        $input = $request->all();
        
        foreach ($input as $key => $value) {
            if (is_string($value)) {
                $input[$key] = strip_tags($value);
            }
        }
        
        $request->merge($input);
    }
}
