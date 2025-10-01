<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\Request;
use App\Services\MonitoringService;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * The list of the inputs that are never flashed to the session on validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     */
    public function register(): void
    {
        $this->reportable(function (Throwable $e) {
            // Log automatique des erreurs avec contexte
            MonitoringService::logError($e, [
                'request_data' => request()->all(),
                'headers' => request()->headers->all(),
                'session_data' => session()->all()
            ]);
        });
    }

    /**
     * Render an exception into an HTTP response.
     */
    public function render($request, Throwable $e)
    {
        // Log des erreurs de sécurité
        if ($e instanceof \Illuminate\Auth\AuthenticationException) {
            MonitoringService::logSecurityEvent('authentication_failed', [
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'url' => $request->fullUrl()
            ]);
        }

        if ($e instanceof \Illuminate\Auth\Access\AuthorizationException) {
            MonitoringService::logSecurityEvent('authorization_failed', [
                'user_id' => auth()->id(),
                'ip' => $request->ip(),
                'url' => $request->fullUrl(),
                'ability' => $e->getMessage()
            ]);
        }

        if ($e instanceof \Illuminate\Validation\ValidationException) {
            MonitoringService::logSecurityEvent('validation_failed', [
                'user_id' => auth()->id(),
                'ip' => $request->ip(),
                'url' => $request->fullUrl(),
                'errors' => $e->errors()
            ]);
        }

        return parent::render($request, $e);
    }
}
