<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class CacheMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next, $ttl = 300)
    {
        // Cache pour les pages statiques
        if ($request->isMethod('GET') && !$request->has('no-cache')) {
            $cacheKey = 'page_' . md5($request->fullUrl());
            
            if (Cache::has($cacheKey)) {
                return response(Cache::get($cacheKey));
            }
            
            $response = $next($request);
            
            if ($response->getStatusCode() === 200) {
                Cache::put($cacheKey, $response->getContent(), $ttl);
            }
            
            return $response;
        }
        
        return $next($request);
    }
}
