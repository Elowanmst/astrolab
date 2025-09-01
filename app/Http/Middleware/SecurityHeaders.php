<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class SecurityHeaders
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        if (!config('security.headers.enabled', true)) {
            return $response;
        }

        // Protection XSS
        $response->headers->set('X-Content-Type-Options', 'nosniff');
        $response->headers->set('X-Frame-Options', 'DENY');
        $response->headers->set('X-XSS-Protection', '1; mode=block');

        // Content Security Policy
        $csp = $this->buildContentSecurityPolicy();
        $response->headers->set('Content-Security-Policy', $csp);

        // Strict Transport Security (HTTPS uniquement)
        if ($request->secure()) {
            $hsts = 'max-age=31536000; includeSubDomains; preload';
            $response->headers->set('Strict-Transport-Security', $hsts);
        }

        // Politique de référent
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');

        // Permissions Policy
        $permissionsPolicy = $this->buildPermissionsPolicy();
        $response->headers->set('Permissions-Policy', $permissionsPolicy);

        // Retirer les headers qui révèlent des informations sensibles
        $response->headers->remove('X-Powered-By');
        $response->headers->remove('Server');

        return $response;
    }

    /**
     * Construire la Content Security Policy
     */
    private function buildContentSecurityPolicy(): string
    {
        $cspConfig = config('security.headers.content_security_policy', []);
        
        $csp = [];
        foreach ($cspConfig as $directive => $value) {
            $directive = str_replace('_', '-', $directive);
            $csp[] = "{$directive} {$value}";
        }

        return implode('; ', $csp);
    }

    /**
     * Construire la Permissions Policy
     */
    private function buildPermissionsPolicy(): string
    {
        $permissionsConfig = config('security.headers.permissions_policy', []);
        
        $permissions = [];
        foreach ($permissionsConfig as $feature => $allowlist) {
            $permissions[] = "{$feature}={$allowlist}";
        }

        return implode(', ', $permissions);
    }
}
