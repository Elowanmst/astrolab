<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class WebhookSecurity
{
    /**
     * Middleware de sécurité pour les webhooks Stripe
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Vérification de l'IP (si configuré)
        if (config('security.webhook.ip_whitelist_enabled')) {
            $allowedIps = config('security.webhook.allowed_ips', []);
            $clientIp = $request->ip();
            
            if (!empty($allowedIps) && !in_array($clientIp, $allowedIps)) {
                Log::warning('Webhook access denied - IP not allowed', [
                    'ip' => $clientIp,
                    'user_agent' => $request->userAgent(),
                    'url' => $request->fullUrl()
                ]);
                
                return response()->json(['error' => 'Unauthorized'], 403);
            }
        }

        // Vérification du User-Agent Stripe
        $userAgent = $request->userAgent();
        if (!str_contains($userAgent, 'Stripe')) {
            Log::warning('Webhook access denied - Invalid User-Agent', [
                'user_agent' => $userAgent,
                'ip' => $request->ip()
            ]);
        }

        // Log de la requête webhook pour audit
        Log::info('Webhook request received', [
            'ip' => $request->ip(),
            'user_agent' => $userAgent,
            'content_type' => $request->header('Content-Type'),
            'stripe_signature' => $request->header('Stripe-Signature') ? 'present' : 'missing'
        ]);

        return $next($request);
    }
}
