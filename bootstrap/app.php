<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // Middlewares globaux
        $middleware->append(\App\Http\Middleware\SecurityHeadersMiddleware::class);
        $middleware->append(\App\Http\Middleware\InputSanitizationMiddleware::class);
        $middleware->append(\App\Http\Middleware\PerformanceMonitoringMiddleware::class);

        // Middlewares de rate limiting
        $middleware->alias([
            'rate.limit' => \App\Http\Middleware\RateLimitMiddleware::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
