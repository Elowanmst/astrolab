<?php

namespace App\Services\Payment;

use App\Models\Order;
use App\Models\PaymentTransaction;
use Illuminate\Support\Facades\Log;

class PaymentService
{
    protected $processor;

    public function __construct()
    {
        $this->processor = config('payment.default_processor', 'simulation');
    }

    /**
     * Traiter un paiement
     */
    public function processPayment(array $paymentData, Order $order)
    {
        switch ($this->processor) {
            case 'stripe':
                return $this->processStripePayment($paymentData, $order);
            
            case 'paypal':
                return $this->processPayPalPayment($paymentData, $order);
            
            case 'lyra':
                return $this->processLyraPayment($paymentData, $order);
            
            default:
                throw new \Exception("Processeur de paiement non configuré : {$this->processor}. Veuillez configurer PAYMENT_PROCESSOR=stripe dans votre .env");
        }
    }

    /**
     * Simulation de paiement (pour les tests)
     */
    protected function processSimulationPayment(array $paymentData, Order $order)
    {
        // Créer la transaction en base
        $transaction = PaymentTransaction::create([
            'order_id' => $order->id,
            'transaction_id' => 'SIM_' . time() . '_' . $order->id,
            'processor' => 'simulation',
            'status' => 'pending',
            'amount' => $order->total_amount,
            'fees' => 0,
            'currency' => 'EUR',
            'payment_method' => 'card',
            'card_last_4' => substr($paymentData['card_number'], -4),
            'card_brand' => $this->detectCardBrand($paymentData['card_number']),
            'processed_at' => now(),
        ]);

        // Simuler un délai de traitement
        sleep(1);

        // Cartes de test spécifiques pour contrôler le résultat
        $cardNumber = preg_replace('/\s+/', '', $paymentData['card_number']);
        $testResult = $this->getTestResult($cardNumber);

        // Log pour le développement
        Log::info('Simulation de paiement', [
            'order_id' => $order->id,
            'transaction_id' => $transaction->transaction_id,
            'amount' => $order->total_amount,
            'card_last_4' => substr($paymentData['card_number'], -4),
            'test_result' => $testResult,
        ]);

        if ($testResult['success']) {
            $transaction->update([
                'status' => 'completed',
                'processor_response' => [
                    'success' => true,
                    'message' => $testResult['message'],
                    'test_card' => $cardNumber,
                    'simulated_at' => now()->toISOString(),
                ]
            ]);

            return [
                'success' => true,
                'transaction_id' => $transaction->transaction_id,
                'message' => $testResult['message'],
                'processor' => 'simulation',
            ];
        } else {
            $transaction->update([
                'status' => 'failed',
                'failure_reason' => $testResult['message'],
                'processor_response' => [
                    'success' => false,
                    'error' => $testResult['message'],
                    'test_card' => $cardNumber,
                    'simulated_at' => now()->toISOString(),
                ]
            ]);

            return [
                'success' => false,
                'error' => $testResult['message'],
                'processor' => 'simulation',
            ];
        }
    }

    /**
     * Détermine le résultat du test selon la carte utilisée
     */
    private function getTestResult($cardNumber)
    {
        switch ($cardNumber) {
            case '4242424242424242':
                return ['success' => true, 'message' => '✅ Paiement test réussi - Carte Visa valide'];
                
            case '5555555555554444':
                return ['success' => true, 'message' => '✅ Paiement test réussi - Carte Mastercard valide'];
                
            case '4000000000000002':
                return ['success' => false, 'message' => '❌ Carte déclinée - Fonds insuffisants'];
                
            case '4000000000000127':
                return ['success' => false, 'message' => '❌ Carte expirée'];
                
            case '4000000000000119':
                return ['success' => false, 'message' => '❌ Erreur de traitement - Réessayez plus tard'];
                
            default:
                // Simulation aléatoire pour les autres cartes (95% de succès)
                if (rand(1, 100) <= 95) {
                    return ['success' => true, 'message' => '✅ Paiement simulé avec succès'];
                } else {
                    return ['success' => false, 'message' => '❌ Paiement refusé (simulation aléatoire)'];
                }
        }
    }

    /**
     * Traitement Stripe (nécessite stripe/stripe-php)
     */
    protected function processStripePayment(array $paymentData, Order $order)
    {
        if (!class_exists('Stripe\Stripe')) {
            throw new \Exception('Stripe SDK non installé. Exécutez: composer require stripe/stripe-php');
        }

        try {
            // Définir la clé API Stripe depuis la configuration
            \Stripe\Stripe::setApiKey(config('stripe.secret_key'));

            // Créer un PaymentIntent
            $paymentIntent = \Stripe\PaymentIntent::create([
                'amount' => round($order->total_amount * 100), // En centimes
                'currency' => config('stripe.currency', 'eur'),
                'payment_method_data' => [
                    'type' => 'card',
                    'card' => [
                        'number' => $paymentData['card_number'],
                        'exp_month' => substr($paymentData['card_expiry'], 0, 2),
                        'exp_year' => '20' . substr($paymentData['card_expiry'], 3, 2),
                        'cvc' => $paymentData['card_cvv'],
                    ],
                ],
                'confirm' => true,
                'description' => "Commande Astrolab #{$order->order_number}",
                'metadata' => [
                    'order_id' => $order->id,
                    'order_number' => $order->order_number,
                    'customer_email' => $order->user->email ?? $order->guest_email,
                    'customer_name' => $order->user->name ?? $order->guest_name,
                ],
                'receipt_email' => $order->user->email ?? $order->guest_email,
            ]);

            // Log pour le suivi
            Log::info('Paiement Stripe traité', [
                'order_id' => $order->id,
                'payment_intent_id' => $paymentIntent->id,
                'amount' => $order->total_amount,
                'status' => $paymentIntent->status,
            ]);

            return [
                'success' => $paymentIntent->status === 'succeeded',
                'transaction_id' => $paymentIntent->id,
                'message' => $paymentIntent->status === 'succeeded' 
                    ? 'Paiement traité avec succès par Stripe' 
                    : 'Paiement en cours de traitement',
                'processor' => 'stripe',
                'payment_intent' => $paymentIntent,
            ];

        } catch (\Stripe\Exception\CardException $e) {
            // Erreur de carte (carte déclinée, etc.)
            return [
                'success' => false,
                'error' => 'Carte refusée: ' . $e->getError()->message,
                'processor' => 'stripe',
            ];
        } catch (\Stripe\Exception\RateLimitException $e) {
            // Trop de requêtes à l'API
            return [
                'success' => false,
                'error' => 'Erreur de limite de taux, réessayez plus tard',
                'processor' => 'stripe',
            ];
        } catch (\Stripe\Exception\InvalidRequestException $e) {
            // Paramètres invalides
            return [
                'success' => false,
                'error' => 'Paramètres de paiement invalides',
                'processor' => 'stripe',
            ];
        } catch (\Stripe\Exception\AuthenticationException $e) {
            // Clés API invalides
            return [
                'success' => false,
                'error' => 'Erreur d\'authentification Stripe',
                'processor' => 'stripe',
            ];
        } catch (\Stripe\Exception\ApiConnectionException $e) {
            // Problème de réseau
            return [
                'success' => false,
                'error' => 'Erreur de connexion au service de paiement',
                'processor' => 'stripe',
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage(),
                'processor' => 'stripe',
            ];
        }
    }

    /**
     * Traitement PayPal (nécessite paypal/rest-api-sdk-php)
     */
    protected function processPayPalPayment(array $paymentData, Order $order)
    {
        // Implementation PayPal ici
        throw new \Exception('PayPal non encore implémenté');
    }

    /**
     * Traitement Lyra/PayZen (nécessite lyracom/rest-php-sdk)
     */
    protected function processLyraPayment(array $paymentData, Order $order)
    {
        // Implementation Lyra ici
        throw new \Exception('Lyra/PayZen non encore implémenté');
    }

    /**
     * Calculer les frais de transaction
     */
    public function calculateFees($amount)
    {
        $fees = config('payment.fees.' . $this->processor, [
            'percentage' => 0,
            'fixed' => 0,
        ]);

        return ($amount * $fees['percentage'] / 100) + $fees['fixed'];
    }

    /**
     * Obtenir les informations du processeur actuel
     */
    public function getProcessorInfo()
    {
        return config('payment.processors.' . $this->processor);
    }

    /**
     * Détecter la marque de carte depuis le numéro
     */
    private function detectCardBrand($cardNumber)
    {
        $cardNumber = preg_replace('/\s+/', '', $cardNumber);
        
        // Visa
        if (preg_match('/^4[0-9]{12}(?:[0-9]{3})?$/', $cardNumber)) {
            return 'visa';
        }
        
        // Mastercard
        if (preg_match('/^5[1-5][0-9]{14}$/', $cardNumber) || 
            preg_match('/^2[2-7][0-9]{14}$/', $cardNumber)) {
            return 'mastercard';
        }
        
        // American Express
        if (preg_match('/^3[47][0-9]{13}$/', $cardNumber)) {
            return 'amex';
        }
        
        // Discover
        if (preg_match('/^6(?:011|5[0-9]{2})[0-9]{12}$/', $cardNumber)) {
            return 'discover';
        }
        
        return 'unknown';
    }
}
