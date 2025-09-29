<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CanContributeMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!auth()->check()) {
            return redirect()->route('login')->with('error', 'Vous devez être connecté pour accéder à cette fonctionnalité.');
        }

        if (!auth()->user()->canContribute()) {
            return redirect()->back()->with('error', 'Vous n\'avez pas les permissions nécessaires pour contribuer. Contactez un administrateur.');
        }

        return $next($request);
    }
}
