<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Symfony\Component\HttpFoundation\Response;

class RateLimitMiddleware
{
    public function handle(Request $request, Closure $next, int $maxAttempts = 60, int $decayMinutes = 1): Response
    {
        $key = $this->resolveRequestSignature($request);

        if (Cache::has($key)) {
            $attempts = Cache::get($key);
            if ($attempts >= $maxAttempts) {
                return response()->json([
                    'error' => 'Too Many Requests',
                    'message' => 'Vous avez dépassé la limite de requêtes autorisées. Veuillez réessayer plus tard.'
                ], 429);
            }
            Cache::increment($key);
        } else {
            Cache::put($key, 1, $decayMinutes * 60);
        }

        return $next($request);
    }

    protected function resolveRequestSignature(Request $request): string
    {
        return 'rate_limit:' . $request->ip() . ':' . $request->route()->getName();
    }
}
