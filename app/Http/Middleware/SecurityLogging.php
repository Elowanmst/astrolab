<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class SecurityLogging
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $startTime = microtime(true);
        
        // Logger les tentatives sur des routes sensibles
        if ($this->isSensitiveRoute($request)) {
            $this->logRequest($request);
        }

        $response = $next($request);

        // Logger les réponses d'erreur
        if ($response->getStatusCode() >= 400) {
            $this->logErrorResponse($request, $response, $startTime);
        }

        // Logger les actions de paiement
        if ($this->isPaymentRoute($request)) {
            $this->logPaymentActivity($request, $response, $startTime);
        }

        return $response;
    }

    /**
     * Vérifier si la route est sensible
     */
    protected function isSensitiveRoute(Request $request): bool
    {
        $sensitiveRoutes = [
            'login',
            'register',
            'password.*',
            'checkout.*',
            'admin.*',
        ];

        $routeName = $request->route()?->getName() ?? '';
        
        foreach ($sensitiveRoutes as $pattern) {
            if (fnmatch($pattern, $routeName)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Vérifier si c'est une route de paiement
     */
    protected function isPaymentRoute(Request $request): bool
    {
        $paymentRoutes = [
            'checkout.process',
            'stripe.webhook',
            'paypal.webhook',
            'lyra.webhook',
        ];

        return in_array($request->route()?->getName(), $paymentRoutes);
    }

    /**
     * Logger la requête
     */
    protected function logRequest(Request $request): void
    {
        Log::channel('security')->info('Sensitive route accessed', [
            'ip' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'url' => $request->fullUrl(),
            'method' => $request->method(),
            'route' => $request->route()?->getName(),
            'user_id' => $request->user()?->id,
            'session_id' => $request->session()->getId(),
            'timestamp' => now(),
            'headers' => $this->getSafeHeaders($request),
        ]);
    }

    /**
     * Logger les réponses d'erreur
     */
    protected function logErrorResponse(Request $request, Response $response, float $startTime): void
    {
        $duration = round((microtime(true) - $startTime) * 1000, 2);
        
        Log::channel('security')->warning('Error response', [
            'status_code' => $response->getStatusCode(),
            'ip' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'url' => $request->fullUrl(),
            'method' => $request->method(),
            'route' => $request->route()?->getName(),
            'user_id' => $request->user()?->id,
            'duration_ms' => $duration,
            'timestamp' => now(),
        ]);
    }

    /**
     * Logger l'activité de paiement
     */
    protected function logPaymentActivity(Request $request, Response $response, float $startTime): void
    {
        $duration = round((microtime(true) - $startTime) * 1000, 2);
        
        Log::channel('security')->info('Payment activity', [
            'status_code' => $response->getStatusCode(),
            'ip' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'route' => $request->route()?->getName(),
            'user_id' => $request->user()?->id,
            'duration_ms' => $duration,
            'timestamp' => now(),
            'payment_data' => $this->getSafePaymentData($request),
        ]);
    }

    /**
     * Obtenir les headers sécurisés (sans données sensibles)
     */
    protected function getSafeHeaders(Request $request): array
    {
        $headers = $request->headers->all();
        
        // Retirer les headers sensibles
        unset(
            $headers['authorization'],
            $headers['cookie'],
            $headers['x-csrf-token']
        );

        return $headers;
    }

    /**
     * Obtenir les données de paiement sécurisées
     */
    protected function getSafePaymentData(Request $request): array
    {
        $data = $request->only([
            'payment_method',
            'amount',
            'currency',
        ]);

        // Masquer les données sensibles
        if ($request->has('card_number')) {
            $cardNumber = $request->input('card_number');
            $data['card_last_4'] = substr(preg_replace('/\s+/', '', $cardNumber), -4);
        }

        return $data;
    }
}
