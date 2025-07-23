<?php

namespace App\Services\Payment;

use App\Models\Order;
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
                return $this->processSimulationPayment($paymentData, $order);
        }
    }

    /**
     * Simulation de paiement (pour les tests)
     */
    protected function processSimulationPayment(array $paymentData, Order $order)
    {
        // Simuler un délai de traitement
        sleep(1);

        // Log pour le développement
        Log::info('Simulation de paiement', [
            'order_id' => $order->id,
            'amount' => $order->total_amount,
            'card_last_4' => substr($paymentData['card_number'], -4),
        ]);

        // Simuler un succès (95% de réussite)
        if (rand(1, 100) <= 95) {
            return [
                'success' => true,
                'transaction_id' => 'SIM_' . time() . '_' . $order->id,
                'message' => 'Paiement simulé avec succès',
                'processor' => 'simulation',
            ];
        } else {
            return [
                'success' => false,
                'error' => 'Paiement refusé (simulation)',
                'processor' => 'simulation',
            ];
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
            // Définir la clé API Stripe
            \Stripe\Stripe::setApiKey(config('payment.processors.stripe.secret_key'));

            // Créer un PaymentIntent
            $paymentIntent = \Stripe\PaymentIntent::create([
                'amount' => $order->total_amount * 100, // En centimes
                'currency' => 'eur',
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
                'metadata' => [
                    'order_id' => $order->id,
                    'order_number' => $order->order_number,
                ],
            ]);

            return [
                'success' => $paymentIntent->status === 'succeeded',
                'transaction_id' => $paymentIntent->id,
                'message' => 'Paiement traité par Stripe',
                'processor' => 'stripe',
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
}
