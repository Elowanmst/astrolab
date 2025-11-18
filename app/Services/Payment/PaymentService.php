<?php
// app/Services/Payment/PaymentService.php

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

    public function processPayment(array $paymentData, Order $order)
    {
        Log::info('=== DÉBUT TRAITEMENT PAIEMENT ===', [
            'processor' => $this->processor,
            'order_id' => $order->id,
            'amount' => $order->total_amount
        ]);

        switch ($this->processor) {
            case 'stripe':
                Log::info('Traitement avec Stripe');
                return $this->processStripePayment($paymentData, $order);
            
            case 'simulation':
                Log::info('Traitement avec Simulation');
                return $this->processSimulationPayment($paymentData, $order);
            
            default:
                Log::error('Processeur inconnu', ['processor' => $this->processor]);
                throw new \Exception("Processeur de paiement non configuré : {$this->processor}");
        }
    }

    /**
     * NOUVELLE MÉTHODE STRIPE SÉCURISÉE
     * Crée un PaymentIntent et retourne le client_secret pour le frontend
     */
    protected function processStripePayment(array $paymentData, Order $order)
    {
        // Vérifier que Stripe est installé
        if (!class_exists('Stripe\Stripe')) {
            Log::error('Stripe SDK non installé');
            return [
                'success' => false,
                'error' => 'Stripe SDK non installé. Exécutez: composer require stripe/stripe-php',
                'processor' => 'stripe',
            ];
        }

        // Vérifier les clés
        $secretKey = config('stripe.secret_key') ?: config('services.stripe.secret');
        if (!$secretKey) {
            Log::error('Clé secrète Stripe manquante');
            return [
                'success' => false,
                'error' => 'Configuration Stripe incomplète - Clé secrète manquante',
                'processor' => 'stripe',
            ];
        }

        Log::info('Configuration Stripe', [
            'secret_key_present' => !empty($secretKey),
            'secret_key_prefix' => substr($secretKey, 0, 7),
        ]);

        try {
            // Configurer Stripe
            \Stripe\Stripe::setApiKey($secretKey);

            // Pour les tests, on peut utiliser une carte de test directe
            // ATTENTION : Ceci est UNIQUEMENT pour les tests en mode développement
            if (app()->environment('local') && $this->isTestCard($paymentData['card_number'])) {
                return $this->processTestCard($paymentData, $order);
            }

            // NOUVEAU : Créer le PaymentIntent sans données de carte
            $paymentIntent = \Stripe\PaymentIntent::create([
                'amount' => round($order->total_amount * 100), // En centimes
                'currency' => 'eur',
                'description' => "Commande Astrolab #{$order->order_number}",
                'metadata' => [
                    'order_id' => $order->id,
                    'order_number' => $order->order_number,
                ],
                'payment_method_types' => ['card'],
                // Mode manuel - sera confirmé côté frontend
                'confirmation_method' => 'manual',
                'confirm' => false,
            ]);

            // Créer une transaction en base
            $transaction = PaymentTransaction::create([
                'order_id' => $order->id,
                'transaction_id' => $paymentIntent->id,
                'processor' => 'stripe',
                'status' => 'pending',
                'amount' => $order->total_amount,
                'currency' => 'EUR',
                'payment_method' => 'card',
                'processed_at' => now(),
                'processor_response' => [
                    'payment_intent_id' => $paymentIntent->id,
                    'status' => $paymentIntent->status,
                    'client_secret' => $paymentIntent->client_secret,
                    'created' => now()->toISOString(),
                ]
            ]);

            Log::info('PaymentIntent Stripe créé (en attente confirmation)', [
                'payment_intent_id' => $paymentIntent->id,
                'status' => $paymentIntent->status,
                'amount' => $paymentIntent->amount,
                'client_secret' => $paymentIntent->client_secret ? 'PRESENT' : 'MISSING',
            ]);

            // Retourner le client_secret pour confirmation côté frontend
            return [
                'success' => true, // Création réussie, confirmation en attente
                'transaction_id' => $paymentIntent->id,
                'client_secret' => $paymentIntent->client_secret,
                'requires_action' => true,
                'message' => 'PaymentIntent créé, confirmation requise côté client',
                'processor' => 'stripe',
                'status' => $paymentIntent->status,
            ];

        } catch (\Stripe\Exception\CardException $e) {
            Log::error('Erreur carte Stripe', [
                'error' => $e->getMessage(),
                'decline_code' => $e->getDeclineCode(),
            ]);

            return [
                'success' => false,
                'error' => 'Carte refusée: ' . $e->getMessage(),
                'processor' => 'stripe',
            ];
        } catch (\Stripe\Exception\AuthenticationException $e) {
            Log::error('Erreur authentification Stripe', ['error' => $e->getMessage()]);
            
            return [
                'success' => false,
                'error' => 'Erreur de configuration Stripe (clés invalides)',
                'processor' => 'stripe',
            ];
        } catch (\Exception $e) {
            Log::error('Erreur Stripe générale', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return [
                'success' => false,
                'error' => 'Erreur de paiement: ' . $e->getMessage(),
                'processor' => 'stripe',
            ];
        }
    }

    /**
     * Vérifier si c'est une carte de test Stripe
     */
    private function isTestCard($cardNumber)
    {
        $cardNumber = preg_replace('/\s+/', '', $cardNumber);
        $testCards = [
            '4242424242424242', // Visa succès
            '4000000000000002', // Visa déclinée
            '4000000000009995', // Visa insufficient_funds
            '5555555555554444', // Mastercard succès
        ];
        
        return in_array($cardNumber, $testCards);
    }

    /**
     * Traiter une carte de test en développement
     */
    private function processTestCard(array $paymentData, Order $order)
    {
        $cardNumber = preg_replace('/\s+/', '', $paymentData['card_number']);
        
        // Créer une transaction
        $transaction = PaymentTransaction::create([
            'order_id' => $order->id,
            'transaction_id' => 'test_' . time() . '_' . $order->id,
            'processor' => 'stripe_test',
            'status' => 'completed',
            'amount' => $order->total_amount,
            'currency' => 'EUR',
            'payment_method' => 'card',
            'card_last_4' => substr($cardNumber, -4),
            'card_brand' => $this->detectCardBrand($cardNumber),
            'processed_at' => now(),
        ]);

        // Simuler selon la carte
        if ($cardNumber === '4242424242424242') {
            Log::info('Carte test Stripe - Succès simulé');
            return [
                'success' => true,
                'transaction_id' => $transaction->transaction_id,
                'message' => 'Test Stripe - Paiement simulé avec succès',
                'processor' => 'stripe_test',
                'status' => 'succeeded',
            ];
        } else {
            Log::info('Carte test Stripe - Échec simulé');
            $transaction->update(['status' => 'failed']);
            return [
                'success' => false,
                'error' => 'Carte de test refusée',
                'processor' => 'stripe_test',
            ];
        }
    }

    // Garder la simulation pour les tests
    protected function processSimulationPayment(array $paymentData, Order $order)
    {
        // ... votre code existant
    }

    private function detectCardBrand($cardNumber)
    {
        $cardNumber = preg_replace('/\s+/', '', $cardNumber);
        
        if (preg_match('/^4/', $cardNumber)) {
            return 'visa';
        } elseif (preg_match('/^5[1-5]/', $cardNumber)) {
            return 'mastercard';
        }
        
        return 'unknown';
    }
}