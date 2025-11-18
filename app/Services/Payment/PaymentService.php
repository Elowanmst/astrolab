<?php

namespace App\Services\Payment;

use App\Models\Order;
use Exception;
use Illuminate\Support\Facades\Log;
use Stripe\StripeClient;
use Stripe\Exception\ApiErrorException;

class PaymentService
{
    protected $stripe;

    public function __construct()
    {
        $this->stripe = new StripeClient(config('stripe.secret_key'));
    }

    /**
     * Process Stripe payment - PCI DSS Compliant Version
     * This method only creates PaymentIntents and handles confirmation
     * Card data is processed entirely on the client side via Stripe Elements
     */
    public function processStripePayment(Order $order, array $paymentData = []): array
    {
        try {
            Log::info('Processing Stripe payment', [
                'order_id' => $order->id,
                'amount' => $order->total_amount
            ]);

            // Calculate amount in cents
            $amountInCents = (int) round($order->total_amount * 100);

            // Create PaymentIntent with order details
            $paymentIntent = $this->stripe->paymentIntents->create([
                'amount' => $amountInCents,
                'currency' => $order->currency ?? 'eur',
                'payment_method_types' => ['card'],
                'metadata' => [
                    'order_id' => $order->id,
                    'order_number' => $order->order_number,
                    'customer_email' => $order->email,
                ],
                'description' => "Commande #{$order->order_number}",
                'receipt_email' => $order->email,
                'shipping' => [
                    'name' => trim("{$order->shipping_first_name} {$order->shipping_last_name}"),
                    'address' => [
                        'line1' => $order->shipping_address,
                        'line2' => $order->shipping_address_2,
                        'city' => $order->shipping_city,
                        'postal_code' => $order->shipping_postal_code,
                        'country' => $order->shipping_country,
                    ],
                ],
            ]);

            Log::info('PaymentIntent created successfully', [
                'payment_intent_id' => $paymentIntent->id,
                'order_id' => $order->id,
                'amount' => $amountInCents
            ]);

            return [
                'success' => true,
                'payment_intent_id' => $paymentIntent->id,
                'client_secret' => $paymentIntent->client_secret,
                'amount' => $amountInCents,
                'currency' => $paymentIntent->currency,
            ];

        } catch (ApiErrorException $e) {
            Log::error('Stripe API error during payment processing', [
                'error' => $e->getMessage(),
                'error_code' => $e->getStripeCode(),
                'order_id' => $order->id
            ]);

            return [
                'success' => false,
                'error' => 'Erreur de paiement: ' . $e->getMessage(),
                'error_type' => 'stripe_api_error'
            ];

        } catch (Exception $e) {
            Log::error('General error during Stripe payment processing', [
                'error' => $e->getMessage(),
                'order_id' => $order->id
            ]);

            return [
                'success' => false,
                'error' => 'Une erreur est survenue lors du traitement du paiement.',
                'error_type' => 'general_error'
            ];
        }
    }

    /**
     * Confirm Stripe payment after client-side confirmation
     * This is called after the client has confirmed the payment with Stripe Elements
     */
    public function confirmStripePayment(string $paymentIntentId): array
    {
        try {
            Log::info('Confirming Stripe payment', [
                'payment_intent_id' => $paymentIntentId
            ]);

            // Retrieve the PaymentIntent to check its status
            $paymentIntent = $this->stripe->paymentIntents->retrieve($paymentIntentId);

            Log::info('PaymentIntent retrieved', [
                'payment_intent_id' => $paymentIntentId,
                'status' => $paymentIntent->status,
                'amount' => $paymentIntent->amount
            ]);

            if ($paymentIntent->status === 'succeeded') {
                return [
                    'success' => true,
                    'payment_intent_id' => $paymentIntent->id,
                    'status' => $paymentIntent->status,
                    'amount' => $paymentIntent->amount,
                    'currency' => $paymentIntent->currency,
                    'payment_method_id' => $paymentIntent->payment_method,
                    'charges' => $paymentIntent->charges->data,
                ];
            } else {
                return [
                    'success' => false,
                    'error' => 'Le paiement n\'a pas été confirmé.',
                    'status' => $paymentIntent->status,
                    'payment_intent_id' => $paymentIntent->id,
                ];
            }

        } catch (ApiErrorException $e) {
            Log::error('Stripe API error during payment confirmation', [
                'error' => $e->getMessage(),
                'error_code' => $e->getStripeCode(),
                'payment_intent_id' => $paymentIntentId
            ]);

            return [
                'success' => false,
                'error' => 'Erreur lors de la confirmation: ' . $e->getMessage(),
                'error_type' => 'stripe_api_error'
            ];

        } catch (Exception $e) {
            Log::error('General error during payment confirmation', [
                'error' => $e->getMessage(),
                'payment_intent_id' => $paymentIntentId
            ]);

            return [
                'success' => false,
                'error' => 'Une erreur est survenue lors de la confirmation du paiement.',
                'error_type' => 'general_error'
            ];
        }
    }

    /**
     * Legacy method for backward compatibility - redirects to appropriate Stripe method
     */
    public function processPayment(string $paymentMethod, array $paymentData): array
    {
        if ($paymentMethod === 'stripe') {
            if (isset($paymentData['order'])) {
                return $this->processStripePayment($paymentData['order'], $paymentData);
            } else {
                throw new Exception('Order object required for Stripe payments');
            }
        } else {
            throw new Exception("Payment method not supported: {$paymentMethod}");
        }
    }

    /**
     * Get supported payment methods
     */
    public function getSupportedPaymentMethods(): array
    {
        return ['stripe'];
    }
}
