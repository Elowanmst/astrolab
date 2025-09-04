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
        $this->processor = config('payment.default_processor', 'stripe');
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
                throw new \Exception('Processeur de paiement non configuré pour la production');
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
            // Définir la clé API Stripe depuis la configuration des services
            \Stripe\Stripe::setApiKey(config('services.stripe.secret'));

            // Créer un PaymentIntent
            $paymentIntent = \Stripe\PaymentIntent::create([
                'amount' => round($order->total_amount * 100), // En centimes
                'currency' => config('payment.processors.stripe.currency', 'eur'),
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
