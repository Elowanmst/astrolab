<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Models\Order;
use Stripe\Webhook;
use Stripe\Exception\SignatureVerificationException;

class StripeWebhookController extends Controller
{
    /**
     * Gérer les webhooks Stripe
     */
    public function handle(Request $request)
    {
        $payload = $request->getContent();
        $sig_header = $request->header('Stripe-Signature');
        $endpoint_secret = config('stripe.webhook_secret');

        try {
            $event = Webhook::constructEvent($payload, $sig_header, $endpoint_secret);
        } catch (\UnexpectedValueException $e) {
            // Payload invalide
            Log::error('Webhook Stripe: Payload invalide', ['error' => $e->getMessage()]);
            return response('Invalid payload', 400);
        } catch (SignatureVerificationException $e) {
            // Signature invalide
            Log::error('Webhook Stripe: Signature invalide', ['error' => $e->getMessage()]);
            return response('Invalid signature', 400);
        }

        // Traiter l'événement
        switch ($event['type']) {
            case 'payment_intent.succeeded':
                $this->handlePaymentSucceeded($event['data']['object']);
                break;
                
            case 'payment_intent.payment_failed':
                $this->handlePaymentFailed($event['data']['object']);
                break;
                
            case 'payout.paid':
                $this->handlePayoutPaid($event['data']['object']);
                break;
                
            case 'payout.failed':
                $this->handlePayoutFailed($event['data']['object']);
                break;
                
            default:
                Log::info('Webhook Stripe: Événement non géré', ['type' => $event['type']]);
        }

        return response('Success', 200);
    }

    /**
     * Paiement réussi
     */
    private function handlePaymentSucceeded($paymentIntent)
    {
        $orderId = $paymentIntent['metadata']['order_id'] ?? null;
        
        if ($orderId) {
            $order = Order::find($orderId);
            if ($order) {
                $order->update([
                    'payment_status' => 'completed',
                    'status' => 'processing',
                    'transaction_id' => $paymentIntent['id'],
                ]);
                
                Log::info('Paiement confirmé par webhook', [
                    'order_id' => $orderId,
                    'payment_intent_id' => $paymentIntent['id']
                ]);
            }
        }
    }

    /**
     * Paiement échoué
     */
    private function handlePaymentFailed($paymentIntent)
    {
        $orderId = $paymentIntent['metadata']['order_id'] ?? null;
        
        if ($orderId) {
            $order = Order::find($orderId);
            if ($order) {
                $order->update([
                    'payment_status' => 'failed',
                    'status' => 'cancelled',
                ]);
                
                Log::warning('Paiement échoué confirmé par webhook', [
                    'order_id' => $orderId,
                    'payment_intent_id' => $paymentIntent['id']
                ]);
            }
        }
    }

    /**
     * Virement vers votre compte bancaire réussi
     */
    private function handlePayoutPaid($payout)
    {
        Log::info('Virement Stripe vers compte bancaire réussi', [
            'payout_id' => $payout['id'],
            'amount' => $payout['amount'] / 100,
            'currency' => $payout['currency'],
            'arrival_date' => $payout['arrival_date']
        ]);
        
        // Ici vous pourriez mettre à jour un modèle de comptabilité
        // ou envoyer une notification à l'admin
    }

    /**
     * Virement vers votre compte bancaire échoué
     */
    private function handlePayoutFailed($payout)
    {
        Log::error('Virement Stripe vers compte bancaire échoué', [
            'payout_id' => $payout['id'],
            'amount' => $payout['amount'] / 100,
            'currency' => $payout['currency'],
            'failure_code' => $payout['failure_code'],
            'failure_message' => $payout['failure_message']
        ]);
        
        // Envoyer une alerte à l'admin
    }
}
