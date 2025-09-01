<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\RateLimiter;
use Symfony\Component\HttpFoundation\Response;

class SecurityRateLimiting
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next, string $type = 'global'): Response
    {
        if (!config('security.rate_limiting.enabled', true)) {
            return $next($request);
        }

        $config = config("security.rate_limiting.{$type}", []);
        
        if (empty($config)) {
            return $next($request);
        }

        $key = $this->resolveRequestSignature($request, $type);
        $maxAttempts = $config['requests'] ?? 60;
        $decayMinutes = $config['per_minute'] ?? 1;

        if (RateLimiter::tooManyAttempts($key, $maxAttempts)) {
            $this->logSuspiciousActivity($request, $type, $key);
            
            // Pour les tentatives de paiement, bloquer plus longtemps
            if ($type === 'payment') {
                $blockDuration = $config['block_duration'] ?? 30;
                RateLimiter::clear($key);
                RateLimiter::hit($key, $blockDuration * 60); // En secondes
            }
            
            return $this->buildResponse($request, $key, $maxAttempts, $decayMinutes);
        }

        RateLimiter::hit($key, $decayMinutes * 60);

        $response = $next($request);

        return $this->addRateLimitHeaders(
            $response,
            $maxAttempts,
            RateLimiter::attempts($key),
            RateLimiter::availableIn($key)
        );
    }

    /**
     * Résoudre la signature de la requête pour le rate limiting
     */
    protected function resolveRequestSignature(Request $request, string $type): string
    {
        $ip = $request->ip();
        $route = $request->route()?->getName() ?? $request->path();
        
        // Pour les actions sensibles, inclure l'utilisateur si connecté
        if (in_array($type, ['auth', 'payment', 'checkout'])) {
            $userId = $request->user()?->id ?? 'guest';
            return "rate_limit:{$type}:{$ip}:{$userId}:{$route}";
        }

        return "rate_limit:{$type}:{$ip}:{$route}";
    }

    /**
     * Logger l'activité suspecte
     */
    protected function logSuspiciousActivity(Request $request, string $type, string $key): void
    {
        Log::channel('security')->warning('Rate limit exceeded', [
            'type' => $type,
            'ip' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'url' => $request->fullUrl(),
            'method' => $request->method(),
            'user_id' => $request->user()?->id,
            'key' => $key,
            'timestamp' => now(),
        ]);
    }

    /**
     * Construire la réponse de rate limiting
     */
    protected function buildResponse(Request $request, string $key, int $maxAttempts, int $decayMinutes): Response
    {
        $retryAfter = RateLimiter::availableIn($key);
        
        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'Trop de tentatives. Réessayez dans ' . ceil($retryAfter / 60) . ' minute(s).',
                'retry_after' => $retryAfter,
            ], 429);
        }

        return response()->view('errors.429', [
            'retry_after' => ceil($retryAfter / 60),
        ], 429);
    }

    /**
     * Ajouter les headers de rate limiting
     */
    protected function addRateLimitHeaders(Response $response, int $maxAttempts, int $attempts, int $retryAfter): Response
    {
        $response->headers->add([
            'X-RateLimit-Limit' => $maxAttempts,
            'X-RateLimit-Remaining' => max(0, $maxAttempts - $attempts),
            'X-RateLimit-Reset' => now()->addSeconds($retryAfter)->timestamp,
        ]);

        return $response;
    }
}
