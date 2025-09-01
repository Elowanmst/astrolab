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
    ->withMiddleware(function (Middleware $middleware) {
        // Middleware de sécurité globaux
        $middleware->web(append: [
            \App\Http\Middleware\SecurityHeaders::class,
            \App\Http\Middleware\SecurityLogging::class,
        ]);

        // Middleware spécifiques avec alias
        $middleware->alias([
            'security.rate' => \App\Http\Middleware\SecurityRateLimiting::class,
            'security.headers' => \App\Http\Middleware\SecurityHeaders::class,
            'security.logging' => \App\Http\Middleware\SecurityLogging::class,
            'webhook.security' => \App\Http\Middleware\WebhookSecurity::class,
        ]);

        // Groupes de middleware pour routes sensibles
        $middleware->group('secure', [
            'security.headers',
            'security.rate',
            'security.logging',
        ]);

        // Rate limiting pour les routes de paiement
        $middleware->group('payment', [
            'security.headers',
            'security.rate:payment',
            'security.logging',
            'throttle:payment',
        ]);

        // Middleware pour l'authentification
        $middleware->group('auth_routes', [
            'security.headers',
            'security.rate:auth',
            'security.logging',
        ]);

        // Middleware pour le checkout
        $middleware->group('checkout', [
            'security.headers',
            'security.rate:checkout',
            'security.logging',
        ]);

        // Middleware pour les webhooks
        $middleware->group('webhook', [
            'webhook.security',
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
