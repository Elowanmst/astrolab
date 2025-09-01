<?php

use Illuminate\Support\Facades\Route;
use Stripe\Stripe;
use Stripe\PaymentIntent;

Route::get('/test-stripe', function () {
    try {
        // Configuration Stripe avec la clé secrète
        Stripe::setApiKey(config('services.stripe.secret'));
        
        // Test 1: Vérification de la connexion Stripe
        $account = \Stripe\Account::retrieve();
        
        // Test 2: Création d'un PaymentIntent de test (1 euro)
        $paymentIntent = PaymentIntent::create([
            'amount' => 100, // 1 euro en centimes
            'currency' => 'eur',
            'metadata' => [
                'test' => 'Configuration Astrolab',
                'timestamp' => now()->toISOString()
            ]
        ]);
        
        return response()->json([
            'status' => '✅ SUCCESS',
            'message' => 'Configuration Stripe parfaitement fonctionnelle !',
            'data' => [
                'account_id' => $account->id,
                'account_type' => $account->type,
                'country' => $account->country,
                'payment_intent_id' => $paymentIntent->id,
                'payment_intent_status' => $paymentIntent->status,
                'stripe_keys' => [
                    'public_key_valid' => str_starts_with(config('services.stripe.key'), 'pk_live_'),
                    'secret_key_valid' => str_starts_with(config('services.stripe.secret'), 'sk_live_'),
                    'webhook_secret_set' => !empty(config('services.stripe.webhook_secret')),
                ],
                'currency' => $paymentIntent->currency,
                'amount' => $paymentIntent->amount,
                'environment' => 'PRODUCTION (Live Keys)',
                'timestamp' => now()->format('Y-m-d H:i:s')
            ]
        ], 200);
        
    } catch (\Stripe\Exception\InvalidRequestException $e) {
        return response()->json([
            'status' => '❌ ERROR - Invalid Request',
            'error' => $e->getMessage(),
            'suggestion' => 'Vérifiez vos clés Stripe'
        ], 400);
        
    } catch (\Stripe\Exception\AuthenticationException $e) {
        return response()->json([
            'status' => '❌ ERROR - Authentication',
            'error' => 'Clés Stripe invalides',
            'details' => $e->getMessage()
        ], 401);
        
    } catch (\Exception $e) {
        return response()->json([
            'status' => '❌ ERROR - General',
            'error' => $e->getMessage()
        ], 500);
    }
});
