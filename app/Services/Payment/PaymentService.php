<?php

namespace App\Services\Payment;

use App\Models\Order;
use App\Models\PaymentTransaction;
use App\Services\Security\SecurityValidationService;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Illuminate\Http\Request;

class PaymentService
{
    protected $processor;
    protected $securityValidator;

    public function __construct(SecurityValidationService $securityValidator)
    {
        $this->processor = config('payment.default_processor', 'stripe');
        $this->securityValidator = $securityValidator;
    }

    /**
     * Traiter un paiement avec validations de sécurité avancées
     */
    public function processPayment(array $paymentData, Order $order, Request $request = null)
    {
        // Log de début de transaction avec ID unique
        $transactionId = uniqid('PAY_', true);
        
        Log::channel('payments')->info('=== DÉBUT TRAITEMENT PAIEMENT ===', [
            'transaction_id' => $transactionId,
            'order_id' => $order->id,
            'amount' => $order->total_amount,
            'processor' => $this->processor,
            'timestamp' => now(),
        ]);

        try {
            // 1. Validations de sécurité
            if ($request) {
                $securityValidation = $this->securityValidator->validatePaymentTransaction($request, $paymentData);
                
                if (!$securityValidation['valid']) {
                    $this->handleSecurityViolations($securityValidation, $request, $order);
                    
                    return [
                        'success' => false,
                        'error' => 'Transaction refusée pour des raisons de sécurité',
                        'security_violations' => $securityValidation['violations'],
                        'risk_score' => $securityValidation['risk_score'],
                    ];
                }

                // Log du score de risque même si validation OK
                if ($securityValidation['risk_score'] > 20) {
                    Log::channel('security')->info('Transaction à risque modéré acceptée', [
                        'transaction_id' => $transactionId,
                        'risk_score' => $securityValidation['risk_score'],
                        'order_id' => $order->id,
                    ]);
                }
            }

            // 2. Traitement selon le processeur
            $result = match ($this->processor) {
                'stripe' => $this->processStripePayment($paymentData, $order, $transactionId),
                'paypal' => $this->processPayPalPayment($paymentData, $order, $transactionId),
                'lyra' => $this->processLyraPayment($paymentData, $order, $transactionId),
                default => $this->processSimulationPayment($paymentData, $order, $transactionId),
            };

            // 3. Log du résultat
            Log::channel('payments')->info('=== RÉSULTAT PAIEMENT ===', [
                'transaction_id' => $transactionId,
                'success' => $result['success'],
                'processor_transaction_id' => $result['transaction_id'] ?? null,
                'error' => $result['error'] ?? null,
            ]);

            return $result;

        } catch (\Exception $e) {
            Log::channel('payments')->error('=== ERREUR CRITIQUE PAIEMENT ===', [
                'transaction_id' => $transactionId,
                'error' => $e->getMessage(),
                'stack_trace' => $e->getTraceAsString(),
                'order_id' => $order->id,
            ]);

            return [
                'success' => false,
                'error' => 'Erreur technique lors du traitement du paiement',
                'internal_error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Traitement Stripe sécurisé
     */
    protected function processStripePayment(array $paymentData, Order $order, string $transactionId)
    {
        if (!class_exists('Stripe\Stripe')) {
            throw new \Exception('Stripe SDK non installé. Exécutez: composer require stripe/stripe-php');
        }

        // Créer la transaction en base avant traitement
        $transaction = PaymentTransaction::create([
            'order_id' => $order->id,
            'transaction_id' => $transactionId,
            'processor' => 'stripe',
            'status' => 'pending',
            'amount' => $order->total_amount,
            'fees' => $this->calculateFees($order->total_amount),
            'currency' => 'EUR',
            'payment_method' => 'card',
            'card_last_4' => substr(preg_replace('/\s+/', '', $paymentData['card_number']), -4),
            'card_brand' => $this->detectCardBrand($paymentData['card_number']),
            'processed_at' => now(),
        ]);

        try {
            // Configuration Stripe
            \Stripe\Stripe::setApiKey(config('stripe.secret_key'));
            
            // Vérification des clés API
            if (!config('stripe.secret_key') || !config('stripe.publishable_key')) {
                throw new \Exception('Clés API Stripe manquantes');
            }

            // Préparation des métadonnées sécurisées
            $metadata = [
                'order_id' => $order->id,
                'order_number' => $order->order_number,
                'transaction_id' => $transactionId,
                'environment' => app()->environment(),
                'timestamp' => now()->toISOString(),
            ];

            // Création du PaymentIntent
            $paymentIntent = \Stripe\PaymentIntent::create([
                'amount' => round($order->total_amount * 100), // En centimes
                'currency' => config('stripe.currency', 'eur'),
                'payment_method_data' => [
                    'type' => 'card',
                    'card' => [
                        'number' => preg_replace('/\s+/', '', $paymentData['card_number']),
                        'exp_month' => substr($paymentData['card_expiry'], 0, 2),
                        'exp_year' => '20' . substr($paymentData['card_expiry'], 3, 2),
                        'cvc' => $paymentData['card_cvv'],
                    ],
                ],
                'confirm' => true,
                'return_url' => route('checkout.success', $order->id),
                'description' => "Commande Astrolab #{$order->order_number}",
                'metadata' => $metadata,
                'receipt_email' => $order->user->email ?? $order->guest_email,
                'statement_descriptor' => 'ASTROLAB',
            ]);

            // Mise à jour de la transaction
            $success = $paymentIntent->status === 'succeeded';
            
            $transaction->update([
                'status' => $success ? 'completed' : 'failed',
                'processor_transaction_id' => $paymentIntent->id,
                'processor_response' => [
                    'payment_intent_id' => $paymentIntent->id,
                    'status' => $paymentIntent->status,
                    'charges' => $paymentIntent->charges->data ?? [],
                    'processed_at' => now()->toISOString(),
                ]
            ]);

            Log::channel('payments')->info('Paiement Stripe traité', [
                'transaction_id' => $transactionId,
                'stripe_payment_intent' => $paymentIntent->id,
                'status' => $paymentIntent->status,
                'amount' => $order->total_amount,
            ]);

            return [
                'success' => $success,
                'transaction_id' => $paymentIntent->id,
                'message' => $success 
                    ? 'Paiement traité avec succès par Stripe' 
                    : 'Paiement en cours de traitement',
                'processor' => 'stripe',
                'internal_transaction_id' => $transactionId,
            ];

        } catch (\Stripe\Exception\CardException $e) {
            $this->handleStripeError($transaction, $e, 'card_error');
            return [
                'success' => false,
                'error' => 'Carte refusée: ' . $e->getError()->message,
                'processor' => 'stripe',
            ];
        } catch (\Stripe\Exception\RateLimitException $e) {
            $this->handleStripeError($transaction, $e, 'rate_limit');
            return [
                'success' => false,
                'error' => 'Service temporairement indisponible',
                'processor' => 'stripe',
            ];
        } catch (\Stripe\Exception\InvalidRequestException $e) {
            $this->handleStripeError($transaction, $e, 'invalid_request');
            return [
                'success' => false,
                'error' => 'Paramètres de paiement invalides',
                'processor' => 'stripe',
            ];
        } catch (\Exception $e) {
            $this->handleStripeError($transaction, $e, 'general_error');
            return [
                'success' => false,
                'error' => 'Erreur technique du processeur de paiement',
                'processor' => 'stripe',
            ];
        }
    }

    /**
     * Simulation de paiement avec contrôles de sécurité
     */
    protected function processSimulationPayment(array $paymentData, Order $order, string $transactionId)
    {
        // Créer la transaction
        $transaction = PaymentTransaction::create([
            'order_id' => $order->id,
            'transaction_id' => $transactionId,
            'processor' => 'simulation',
            'status' => 'pending',
            'amount' => $order->total_amount,
            'fees' => 0,
            'currency' => 'EUR',
            'payment_method' => 'card',
            'card_last_4' => substr(preg_replace('/\s+/', '', $paymentData['card_number']), -4),
            'card_brand' => $this->detectCardBrand($paymentData['card_number']),
            'processed_at' => now(),
        ]);

        // Simulation d'un délai de traitement
        usleep(rand(500000, 2000000)); // 0.5 à 2 secondes

        // Cartes de test spécifiques
        $cardNumber = preg_replace('/\s+/', '', $paymentData['card_number']);
        $testResult = $this->getTestResult($cardNumber);

        // Mise à jour de la transaction
        $transaction->update([
            'status' => $testResult['success'] ? 'completed' : 'failed',
            'failure_reason' => $testResult['success'] ? null : $testResult['message'],
            'processor_response' => [
                'test_result' => $testResult,
                'card_used' => $cardNumber,
                'simulated_at' => now()->toISOString(),
            ]
        ]);

        Log::channel('payments')->info('Simulation de paiement', [
            'transaction_id' => $transactionId,
            'result' => $testResult,
            'card_last_4' => substr($cardNumber, -4),
        ]);

        return [
            'success' => $testResult['success'],
            'transaction_id' => $transactionId,
            'message' => $testResult['message'],
            'processor' => 'simulation',
        ];
    }

    /**
     * Traitement PayPal (placeholder sécurisé)
     */
    protected function processPayPalPayment(array $paymentData, Order $order, string $transactionId)
    {
        throw new \Exception('PayPal non encore implémenté dans cette version sécurisée');
    }

    /**
     * Traitement Lyra (placeholder sécurisé)
     */
    protected function processLyraPayment(array $paymentData, Order $order, string $transactionId)
    {
        throw new \Exception('Lyra/PayZen non encore implémenté dans cette version sécurisée');
    }

    /**
     * Gérer les violations de sécurité
     */
    private function handleSecurityViolations(array $securityValidation, Request $request, Order $order)
    {
        foreach ($securityValidation['violations'] as $violation) {
            $this->securityValidator->logSecurityViolation($violation, $request);
            
            // Blocage automatique pour les violations graves
            if ($violation['severity'] === 'high') {
                $this->securityValidator->blockIpTemporary($request->ip(), 60);
            }
        }

        // Log de la transaction refusée
        Log::channel('security')->alert('Transaction de paiement refusée', [
            'order_id' => $order->id,
            'risk_score' => $securityValidation['risk_score'],
            'violations_count' => count($securityValidation['violations']),
            'ip' => $request->ip(),
            'user_id' => $request->user()?->id,
        ]);
    }

    /**
     * Gérer les erreurs Stripe
     */
    private function handleStripeError(PaymentTransaction $transaction, \Exception $e, string $errorType)
    {
        $transaction->update([
            'status' => 'failed',
            'failure_reason' => $e->getMessage(),
            'processor_response' => [
                'error_type' => $errorType,
                'error_message' => $e->getMessage(),
                'error_time' => now()->toISOString(),
            ]
        ]);

        Log::channel('payments')->error('Erreur Stripe', [
            'transaction_id' => $transaction->transaction_id,
            'error_type' => $errorType,
            'error_message' => $e->getMessage(),
        ]);
    }

    /**
     * Résultats des cartes de test
     */
    private function getTestResult(string $cardNumber): array
    {
        return match ($cardNumber) {
            '4242424242424242' => ['success' => true, 'message' => '✅ Paiement test réussi - Carte Visa valide'],
            '5555555555554444' => ['success' => true, 'message' => '✅ Paiement test réussi - Carte Mastercard valide'],
            '4000000000000002' => ['success' => false, 'message' => '❌ Carte déclinée - Fonds insuffisants'],
            '4000000000000069' => ['success' => false, 'message' => '❌ Carte expirée'],
            '4000000000000127' => ['success' => false, 'message' => '❌ Code CVV incorrect'],
            '4000000000000119' => ['success' => false, 'message' => '❌ Erreur de traitement - Réessayez plus tard'],
            default => rand(1, 100) <= 95 
                ? ['success' => true, 'message' => '✅ Paiement simulé avec succès']
                : ['success' => false, 'message' => '❌ Paiement refusé (simulation aléatoire)'],
        };
    }

    /**
     * Calculer les frais de transaction
     */
    public function calculateFees(float $amount): float
    {
        $fees = config("payment.processors.{$this->processor}.fees", [
            'percentage' => 0,
            'fixed' => 0,
        ]);

        if (is_array($fees)) {
            return ($amount * ($fees['percentage'] ?? 0) / 100) + ($fees['fixed'] ?? 0);
        }

        return 0;
    }

    /**
     * Détecter la marque de carte
     */
    private function detectCardBrand(string $cardNumber): string
    {
        $cardNumber = preg_replace('/\s+/', '', $cardNumber);
        
        if (preg_match('/^4[0-9]{12}(?:[0-9]{3})?$/', $cardNumber)) {
            return 'visa';
        }
        
        if (preg_match('/^5[1-5][0-9]{14}$/', $cardNumber) || 
            preg_match('/^2[2-7][0-9]{14}$/', $cardNumber)) {
            return 'mastercard';
        }
        
        if (preg_match('/^3[47][0-9]{13}$/', $cardNumber)) {
            return 'amex';
        }
        
        if (preg_match('/^6(?:011|5[0-9]{2})[0-9]{12}$/', $cardNumber)) {
            return 'discover';
        }
        
        return 'unknown';
    }

    /**
     * Obtenir les informations du processeur
     */
    public function getProcessorInfo(): array
    {
        return config("payment.processors.{$this->processor}", []);
    }

    /**
     * Vérifier si le processeur est en mode test
     */
    public function isTestMode(): bool
    {
        return config('app.env') !== 'production' || 
               config("payment.processors.{$this->processor}.test_mode", false);
    }
}
