<?php

namespace App\Services\Security;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Illuminate\Http\Request;

class SecurityValidationService
{
    /**
     * Valider une transaction de paiement
     */
    public function validatePaymentTransaction(Request $request, array $paymentData): array
    {
        $violations = [];

        // 1. Validation de l'IP
        $ipValidation = $this->validateIpAddress($request);
        if (!$ipValidation['valid']) {
            $violations[] = $ipValidation;
        }

        // 2. Validation des données de carte
        $cardValidation = $this->validateCardData($paymentData);
        if (!$cardValidation['valid']) {
            $violations[] = $cardValidation;
        }

        // 3. Validation du comportement utilisateur
        $behaviorValidation = $this->validateUserBehavior($request);
        if (!$behaviorValidation['valid']) {
            $violations[] = $behaviorValidation;
        }

        // 4. Validation de l'intégrité de session
        $sessionValidation = $this->validateSessionIntegrity($request);
        if (!$sessionValidation['valid']) {
            $violations[] = $sessionValidation;
        }

        // 5. Détection de fraude basique
        $fraudValidation = $this->detectFraudPatterns($request, $paymentData);
        if (!$fraudValidation['valid']) {
            $violations[] = $fraudValidation;
        }

        return [
            'valid' => empty($violations),
            'violations' => $violations,
            'risk_score' => $this->calculateRiskScore($violations),
        ];
    }

    /**
     * Valider l'adresse IP
     */
    private function validateIpAddress(Request $request): array
    {
        $ip = $request->ip();
        
        // Vérifier si l'IP est dans la blacklist
        $blacklisted = Cache::get("blacklist_ip:{$ip}", false);
        if ($blacklisted) {
            return [
                'valid' => false,
                'type' => 'ip_blacklisted',
                'message' => 'Adresse IP bloquée',
                'severity' => 'high',
            ];
        }

        // Vérifier les changements d'IP suspects
        $sessionIp = session('original_ip');
        if ($sessionIp && $sessionIp !== $ip) {
            return [
                'valid' => false,
                'type' => 'ip_change',
                'message' => 'Changement d\'IP détecté',
                'severity' => 'medium',
            ];
        }

        // Vérifier les VPN/Proxy (basique)
        if ($this->isVpnOrProxy($ip)) {
            return [
                'valid' => false,
                'type' => 'vpn_proxy',
                'message' => 'VPN ou proxy détecté',
                'severity' => 'medium',
            ];
        }

        return ['valid' => true];
    }

    /**
     * Valider les données de carte
     */
    private function validateCardData(array $paymentData): array
    {
        $cardNumber = preg_replace('/\s+/', '', $paymentData['card_number'] ?? '');
        
        // Vérifier les patterns de cartes de test en production
        if (app()->environment('production')) {
            $testCards = [
                '4242424242424242',
                '4000000000000002',
                '5555555555554444',
            ];
            
            if (in_array($cardNumber, $testCards)) {
                return [
                    'valid' => false,
                    'type' => 'test_card_production',
                    'message' => 'Carte de test utilisée en production',
                    'severity' => 'high',
                ];
            }
        }

        // Vérifier la cohérence des données
        $cardName = $paymentData['card_name'] ?? '';
        if (strlen($cardName) < 2) {
            return [
                'valid' => false,
                'type' => 'invalid_cardholder',
                'message' => 'Nom du porteur invalide',
                'severity' => 'medium',
            ];
        }

        return ['valid' => true];
    }

    /**
     * Valider le comportement utilisateur
     */
    private function validateUserBehavior(Request $request): array
    {
        $sessionId = $request->session()->getId();
        
        // Vérifier la vitesse de navigation
        $pageViews = Cache::get("page_views:{$sessionId}", []);
        if (count($pageViews) > 0) {
            $lastView = end($pageViews);
            $timeDiff = time() - $lastView;
            
            if ($timeDiff < 5) { // Moins de 5 secondes
                return [
                    'valid' => false,
                    'type' => 'too_fast_navigation',
                    'message' => 'Navigation trop rapide',
                    'severity' => 'medium',
                ];
            }
        }

        // Enregistrer la vue actuelle
        $pageViews[] = time();
        Cache::put("page_views:{$sessionId}", array_slice($pageViews, -10), 3600);

        // Vérifier le temps passé sur la page de paiement
        $paymentStartTime = session('payment_start_time');
        if ($paymentStartTime && (time() - $paymentStartTime < 30)) {
            return [
                'valid' => false,
                'type' => 'too_fast_payment',
                'message' => 'Saisie de paiement trop rapide',
                'severity' => 'high',
            ];
        }

        return ['valid' => true];
    }

    /**
     * Valider l'intégrité de la session
     */
    private function validateSessionIntegrity(Request $request): array
    {
        // Vérifier que la session contient les données de checkout
        if (!session()->has('checkout_data')) {
            return [
                'valid' => false,
                'type' => 'missing_checkout_data',
                'message' => 'Données de commande manquantes',
                'severity' => 'high',
            ];
        }

        // Vérifier l'âge de la session
        $sessionStart = session('session_start_time', time());
        $sessionAge = time() - $sessionStart;
        
        if ($sessionAge > 3600) { // Plus d'1 heure
            return [
                'valid' => false,
                'type' => 'session_too_old',
                'message' => 'Session expirée',
                'severity' => 'medium',
            ];
        }

        return ['valid' => true];
    }

    /**
     * Détecter les patterns de fraude
     */
    private function detectFraudPatterns(Request $request, array $paymentData): array
    {
        $ip = $request->ip();
        $cardLast4 = substr(preg_replace('/\s+/', '', $paymentData['card_number'] ?? ''), -4);
        
        // Vérifier les tentatives multiples avec différentes cartes
        $attempts = Cache::get("payment_attempts:{$ip}", []);
        $uniqueCards = array_unique(array_column($attempts, 'card_last_4'));
        
        if (count($uniqueCards) > 3) {
            return [
                'valid' => false,
                'type' => 'multiple_cards',
                'message' => 'Tentatives avec plusieurs cartes',
                'severity' => 'high',
            ];
        }

        // Enregistrer cette tentative
        $attempts[] = [
            'card_last_4' => $cardLast4,
            'timestamp' => time(),
        ];
        
        // Garder seulement les tentatives des 24 dernières heures
        $attempts = array_filter($attempts, function($attempt) {
            return (time() - $attempt['timestamp']) < 86400;
        });
        
        Cache::put("payment_attempts:{$ip}", $attempts, 86400);

        // Vérifier la fréquence des tentatives
        $recentAttempts = array_filter($attempts, function($attempt) {
            return (time() - $attempt['timestamp']) < 1800; // 30 minutes
        });
        
        if (count($recentAttempts) > 5) {
            return [
                'valid' => false,
                'type' => 'too_many_attempts',
                'message' => 'Trop de tentatives récentes',
                'severity' => 'high',
            ];
        }

        return ['valid' => true];
    }

    /**
     * Calculer le score de risque
     */
    private function calculateRiskScore(array $violations): int
    {
        $score = 0;
        
        foreach ($violations as $violation) {
            switch ($violation['severity'] ?? 'low') {
                case 'high':
                    $score += 30;
                    break;
                case 'medium':
                    $score += 15;
                    break;
                case 'low':
                    $score += 5;
                    break;
            }
        }
        
        return min($score, 100);
    }

    /**
     * Vérifier si l'IP est un VPN/Proxy (détection basique)
     */
    private function isVpnOrProxy(string $ip): bool
    {
        // Liste basique de ranges VPN connus
        $vpnRanges = [
            '10.0.0.0/8',
            '172.16.0.0/12',
            '192.168.0.0/16',
        ];
        
        foreach ($vpnRanges as $range) {
            if ($this->ipInRange($ip, $range)) {
                return true;
            }
        }
        
        return false;
    }

    /**
     * Vérifier si une IP est dans une plage
     */
    private function ipInRange(string $ip, string $range): bool
    {
        list($subnet, $mask) = explode('/', $range);
        return (ip2long($ip) & ~((1 << (32 - $mask)) - 1)) === ip2long($subnet);
    }

    /**
     * Logger une violation de sécurité
     */
    public function logSecurityViolation(array $violation, Request $request): void
    {
        Log::channel('security')->warning('Violation de sécurité détectée', [
            'violation' => $violation,
            'ip' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'url' => $request->fullUrl(),
            'user_id' => $request->user()?->id,
            'session_id' => $request->session()->getId(),
            'timestamp' => now(),
        ]);
    }

    /**
     * Bloquer temporairement une IP
     */
    public function blockIpTemporary(string $ip, int $minutes = 60): void
    {
        Cache::put("blacklist_ip:{$ip}", true, $minutes * 60);
        
        Log::channel('security')->alert('IP bloquée temporairement', [
            'ip' => $ip,
            'duration_minutes' => $minutes,
            'timestamp' => now(),
        ]);
    }
}
