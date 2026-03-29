<?php

use App\Http\Middleware\CheckRole;
use App\Http\Middleware\EnsureTwoFactorVerified;
use App\Http\Middleware\SecurityHeaders;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Cache\RateLimiting\Limit;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // Headers de sécurité sur toutes les requêtes
        $middleware->append(SecurityHeaders::class);

        // Alias middleware
        $middleware->alias([
            'role' => CheckRole::class,
            '2fa'  => EnsureTwoFactorVerified::class,
        ]);

        // Exclure le webhook PayTech du CSRF
        $middleware->validateCsrfTokens(except: [
            'api/v1/webhooks/paytech',
        ]);

        // Rate limiters
        $middleware->throttleWithRedis();
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->render(function (\Throwable $e, Request $request) {
            if ($request->is('api/*') && app()->isProduction()) {
                return response()->json(['message' => 'Une erreur est survenue.'], 500);
            }
        });
    })
    ->booted(function () {
        RateLimiter::for('api', fn(Request $r) =>
            Limit::perMinute(60)->by($r->user()?->id ?: $r->ip())
        );

        RateLimiter::for('auth', fn(Request $r) =>
            Limit::perMinute(10)->by($r->ip())
        );

        RateLimiter::for('admin', fn(Request $r) =>
            Limit::perMinute(30)->by($r->user()?->id ?: $r->ip())
        );
    })
    ->create();
